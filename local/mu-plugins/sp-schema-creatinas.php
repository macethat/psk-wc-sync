<?php
/**
 * Plugin Name: SP Schema — Categoría Creatina
 * Description: Emite CollectionPage + ItemList (Product/Offer) + Organization/Store con 6 sucursales + WebSite/SearchAction en /creatina/. Retira el ItemList roto de Rank Math. No duplica el BreadcrumbList.
 * Version:     1.0.0
 */

defined('ABSPATH') || exit;

/* 1) Limpieza: quita cualquier nodo ItemList inyectado por otros mu-plugins */
add_filter('rank_math/json_ld', 'sp_creatinas_strip_itemlist', 999, 1);
function sp_creatinas_strip_itemlist($data) {
    if (!function_exists('is_product_category') || !is_product_category('creatina')) {
        return $data;
    }
    $clean = sp_creatinas_drop_itemlist($data);
    if (!is_array($clean)) {
        return $data;
    }
    if (isset($clean['@graph']) && is_array($clean['@graph'])) {
        $clean['@graph'] = array_values($clean['@graph']);
        if (empty($clean['@graph'])) {
            unset($clean['@graph']);
        }
    }
    return $clean;
}

function sp_creatinas_drop_itemlist($node) {
    if (!is_array($node)) {
        return $node;
    }
    if (isset($node['@type'])) {
        $types = (array) $node['@type'];
        if (in_array('ItemList', $types, true)) {
            return null;
        }
    }
    foreach ($node as $k => $v) {
        $node[$k] = sp_creatinas_drop_itemlist($v);
        if ($node[$k] === null) {
            unset($node[$k]);
        }
    }
    return $node;
}

/* 2) Emision: un unico bloque JSON-LD limpio en <head> */
add_action('wp_head', 'sp_creatinas_emit_schema', 99);
function sp_creatinas_emit_schema() {
    if (!function_exists('is_product_category') || !is_product_category('creatina')) {
        return;
    }
    $term = get_queried_object();
    if (!$term || empty($term->term_id)) {
        return;
    }

    $home      = home_url('/');
    $org_id    = $home . '#organization';
    $site_id   = $home . '#website';
    $site_name = get_bloginfo('name');
    $currency  = function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'USD';
    $logo      = 'https://suplementospanama.net/wp-content/uploads/2022/05/sp_con-valor-negro.png';

    $term_url = get_term_link($term);
    if (is_wp_error($term_url)) {
        $term_url = $home . 'creatina/';
    }
    $term_base = trailingslashit($term_url);
    $paged     = max(1, (int) get_query_var('paged'));
    $page_url  = $paged > 1 ? $term_base . 'page/' . $paged . '/' : $term_base;

    $description = $term->description
        ? wp_strip_all_tags($term->description)
        : 'Compra creatina en Panamá 100% original: monohidratada, micronizada, sin sabor o saborizada. Envío nacional y retiro gratis en 6 sucursales.';

    $graph = array();

    /* 2.1 Organization / Store + 6 sucursales */
    $branches     = function_exists('sp_branch_all') ? sp_branch_all() : array();
    $dias_map     = array('lunes' => 'Monday', 'martes' => 'Tuesday', 'miercoles' => 'Wednesday', 'jueves' => 'Thursday', 'viernes' => 'Friday', 'sabado' => 'Saturday', 'domingo' => 'Sunday');
    $sub_orgs     = array();
    $branch_nodes = array();

    foreach ($branches as $b) {
        $burl = $home . 'sucursales/' . $b['id'] . '/';
        $bid  = $burl . '#branch';
        $sub_orgs[] = array('@id' => $bid);

        $hours = array();
        if (!empty($b['horarios']) && is_array($b['horarios'])) {
            foreach ($b['horarios'] as $dk => $dh) {
                if (!isset($dias_map[$dk]) || !isset($dh[0], $dh[1])) {
                    continue;
                }
                $hours[] = array(
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => $dias_map[$dk],
                    'opens'     => $dh[0],
                    'closes'    => $dh[1],
                );
            }
        }

        $branch_nodes[] = array(
            '@type'     => 'SportingGoodsStore',
            '@id'       => $bid,
            'name'      => isset($b['nombre_completo']) ? $b['nombre_completo'] : $b['nombre'],
            'url'       => $burl,
            'telephone' => $b['telefono'],
            'image'     => get_stylesheet_directory_uri() . '/sucursales/' . $b['id'] . '/images/hero-' . $b['id'] . '.jpg',
            'address'   => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => $b['direccion'],
                'addressLocality' => 'Ciudad de Panamá',
                'addressRegion'   => 'Panamá',
                'addressCountry'  => 'PA',
            ),
            'geo'       => array(
                '@type'     => 'GeoCoordinates',
                'latitude'  => (string) $b['lat'],
                'longitude' => (string) $b['lng'],
            ),
            'openingHoursSpecification' => $hours,
            'parentOrganization'        => array('@id' => $org_id),
            'sameAs'                    => $b['google_maps_url'],
        );
    }

    $graph[] = array(
        '@type'              => array('Store', 'SportingGoodsStore', 'Organization'),
        '@id'                => $org_id,
        'name'               => 'Suplementos Panamá',
        'url'                => $home,
        'logo'               => $logo,
        'telephone'          => '6382-0672',
        'areaServed'         => array(
            array('@type' => 'City', 'name' => 'Ciudad de Panamá'),
            array('@type' => 'Country', 'name' => 'Panamá'),
        ),
        'currenciesAccepted' => 'USD',
        'paymentAccepted'    => array('Cash', 'Credit Card', 'Debit Card', 'Yappy'),
        'knowsAbout'         => array('Creatina', 'Creatina monohidratada', 'Creatina micronizada', 'Creapure', 'Suplementos deportivos'),
        'subOrganization'    => $sub_orgs,
    );

    /* 2.2 WebSite + SearchAction */
    $graph[] = array(
        '@type'      => 'WebSite',
        '@id'        => $site_id,
        'url'        => $home,
        'name'       => $site_name,
        'inLanguage' => 'es-PA',
        'publisher'  => array('@id' => $org_id),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => $home . '?s={search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    /* 2.3 CollectionPage */
    $graph[] = array(
        '@type'       => 'CollectionPage',
        '@id'         => $page_url . '#webpage',
        'url'         => $page_url,
        'name'        => $term->name . ' en Panamá',
        'description' => $description,
        'inLanguage'  => 'es-PA',
        'isPartOf'    => array('@id' => $site_id),
        'about'       => array('@type' => 'Thing', 'name' => $term->name),
        'primaryImageOfPage' => $logo,
        'mainEntity'  => array('@id' => $page_url . '#itemlist'),
    );

    /* 2.4 ItemList con Product + Offer */
    $items    = array();
    $position = 0;
    $posts    = isset($GLOBALS['wp_query']->posts) ? $GLOBALS['wp_query']->posts : array();

    foreach ($posts as $post_obj) {
        if (!isset($post_obj->post_type) || 'product' !== $post_obj->post_type) {
            continue;
        }
        $product = function_exists('wc_get_product') ? wc_get_product($post_obj->ID) : null;
        if (!$product) {
            continue;
        }
        $position++;

        $img_id = $product->get_image_id();
        $img    = $img_id ? (wp_get_attachment_image_url($img_id, 'full') ?: wc_placeholder_img_src('full')) : wc_placeholder_img_src('full');
        $perm   = get_permalink($post_obj->ID);

        $price = '';
        if ($product->is_type('grouped')) {
            $combo = get_post_meta($post_obj->ID, '_combo_price', true);
            if ($combo !== '' && $combo !== false) {
                $price = (float) $combo;
            }
        }
        if ($price === '') {
            $raw = $product->get_price();
            if ($raw !== '' && $raw !== null) {
                $price = (float) $raw;
            }
        }

        $availability = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

        $item = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'item'     => array(
                '@type'    => 'Product',
                '@id'      => $perm . '#product',
                'name'     => $product->get_name(),
                'url'      => $perm,
                'image'    => $img,
                'category' => $term->name,
            ),
        );

        $brand = '';
        foreach (array('product_brand', 'pa_brand', 'pa_marca') as $tax) {
            if (taxonomy_exists($tax)) {
                $terms = wp_get_post_terms($post_obj->ID, $tax);
                if (!is_wp_error($terms) && !empty($terms)) {
                    $brand = $terms[0]->name;
                    break;
                }
            }
        }
        if ($brand !== '') {
            $item['item']['brand'] = array('@type' => 'Brand', 'name' => $brand);
        }

        $sku = $product->get_sku();
        if ($sku !== '') {
            $item['item']['sku'] = $sku;
            $digits = preg_replace('/[^0-9]/', '', $sku);
            $len = is_string($digits) ? strlen($digits) : 0;
            if (in_array($len, array(8, 12, 13, 14), true)) {
                $item['item']['gtin' . $len] = $digits;
            }
        }

        $offers = array(
            '@type'         => 'Offer',
            'url'           => $perm,
            'priceCurrency' => $currency,
            'availability'  => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller'        => array('@id' => $org_id),
        );
        if ($price !== '') {
            $offers['price'] = function_exists('wc_format_decimal') ? wc_format_decimal($price, 2) : number_format($price, 2, '.', '');
        }
        $item['item']['offers'] = $offers;

        $items[] = $item;
    }

    $graph[] = array(
        '@type'           => 'ItemList',
        '@id'             => $page_url . '#itemlist',
        'name'            => $term->name,
        'numberOfItems'   => count($items),
        'itemListOrder'   => 'https://schema.org/ItemListUnordered',
        'itemListElement' => $items,
    );

    /* 2.5 Nodos de sucursal */
    foreach ($branch_nodes as $node) {
        $graph[] = $node;
    }

    echo PHP_EOL . '<script type="application/ld+json" class="sp-schema-creatinas">' . PHP_EOL;
    echo wp_json_encode(array('@context' => 'https://schema.org', '@graph' => $graph), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo PHP_EOL . '</script>' . PHP_EOL;
}
