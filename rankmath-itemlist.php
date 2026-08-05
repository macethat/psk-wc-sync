<?php
/**
 * Plugin Name: Rank Math Schema Fixes — ItemList + Homepage Article
 * Description: Adds ItemList schema to product categories/brands via rank_math/json_ld.
 *              Removes fake Article schema from the homepage.
 * Version:     1.0.0
 * Author:      Suplementos Panama
 */

defined('ABSPATH') || exit;

// ---------------------------------------------------------------------------
// Fix 1: Add ItemList schema to product category / brand pages
// ---------------------------------------------------------------------------
add_filter('rank_math/json_ld', function (array $data, $jsonld) {

	// Only run on product category or product brand archive pages
	if (!is_product_category() && !is_tax('product_brand')) {
		return $data;
	}

	// Get the current queried object (term)
	$term = get_queried_object();
	if (!$term || empty($term->name)) {
		return $data;
	}

	// Build the category/brand page URL
	$page_url = get_term_link($term);
	if (is_wp_error($page_url)) {
		$page_url = home_url(add_query_arg(array()));
	}

	// Grab the products displayed on the current page (WP_Query already done)
	global $wp_query;
	$posts = $wp_query->posts ?? array();

	$list_items = array();
	$position   = 0;

	foreach ($posts as $p) {
		// Only WooCommerce products
		if ('product' !== ($p->post_type ?? '')) {
			continue;
		}
		if (++$position > 10) {
			break; // performance cap
		}

		$product = wc_get_product($p);
		if (!$product) {
			continue;
		}

		$image_url = '';
		$image_id  = $product->get_image_id();
		if ($image_id) {
			$image_url = wp_get_attachment_image_url($image_id, 'full');
		}
		if (!$image_url) {
			$image_url = wc_placeholder_img_src('full');
		}

		$list_items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'item'     => array(
				'@type' => 'Product',
				'name'  => $product->get_name(),
				'url'   => $product->get_permalink(),
				'image' => $image_url,
			),
		);
	}

	if (empty($list_items)) {
		return $data;
	}

	// Prepend the ItemList to the graph
	$item_list = array(
		'@type'          => 'ItemList',
		'name'           => $term->name,
		'url'            => $page_url,
		'numberOfItems'  => count($list_items),
		'itemListElement' => $list_items,
	);

	// Add to the @graph if it exists, otherwise wrap it
	if (isset($data['@graph']) && is_array($data['@graph'])) {
		array_unshift($data['@graph'], $item_list);
	} else {
		// If the schema doesn't have @graph, we create an @graph wrapper
		$graph   = isset($data['@type']) ? array($data, $item_list) : array($item_list);
		$context = isset($data['@context']) ? $data['@context'] : 'https://schema.org';
		$data    = array(
			'@context' => $context,
			'@graph'   => $graph,
		);
	}

	return $data;
}, 20, 2);

// ---------------------------------------------------------------------------
// Fix 2: Remove fake Article / richSnippet schema from the homepage
// ---------------------------------------------------------------------------
// The rank_math/json_ld filter receives a FLAT array of entries keyed by type
// (e.g. $data['richSnippet'], $data['WebPage'], etc.), NOT a @graph structure.
// The @graph wrapper is added AFTER this filter returns.
// The 'richSnippet' key is where 'Article' schema lives (added by Singular class).
add_filter('rank_math/json_ld', function (array $data, $jsonld) {

	// Only target the front page
	if (is_front_page()) {
		// Remove the richSnippet entry entirely (this is the fake Article)
		if (isset($data['richSnippet'])) {
			unset($data['richSnippet']);
		}
		// Also iterate through the flat array as a safety net
		foreach ($data as $key => $entry) {
			if (is_array($entry) && isset($entry['@type'])) {
				$types = (array) $entry['@type'];
				if (in_array('Article', $types, true)) {
					unset($data[$key]);
				}
			}
		}
	}

	return $data;
}, 999, 2);

// ---------------------------------------------------------------------------
// Fix 2b (backup): Also use validated_data filter for homepage Article removal
// At this stage $data may already have @graph structure
// ---------------------------------------------------------------------------
add_filter('rank_math/schema/validated_data', function (array $data) {

	if (!is_front_page()) {
		return $data;
	}

	// Data may be a flat array (keyed) or already have @graph
	if (isset($data['@graph']) && is_array($data['@graph'])) {
		$data['@graph'] = array_values(
			array_filter($data['@graph'], function ($node) {
				if (!is_array($node)) {
					return true;
				}
				$types = isset($node['@type']) ? (array) $node['@type'] : array();
				if (in_array('Article', $types, true)) {
					return false;
				}
				return true;
			})
		);
	} elseif (isset($data['richSnippet'])) {
		unset($data['richSnippet']);
	}

	return $data;
}, 999);
