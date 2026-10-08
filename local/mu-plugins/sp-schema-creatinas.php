<?php
/**
 * Plugin Name: SP Schema — Categoría Creatina
 * Description: Emite CollectionPage + ItemList (Product/Offer) + Organization/Store con 6 sucursales + WebSite/SearchAction en /creatina/. Retira el ItemList roto de Rank Math. No duplica el BreadcrumbList.
 * Version:     1.0.0
 */

defined('ABSPATH') || exit;

/* FAQ compartido (visible + schema) */
function sp_creatinas_faqs() {
    return array(
        array(
            'q' => '¿Dónde comprar creatina en Panamá?',
            'a' => 'Puedes comprar creatina en Panamá online en suplementospanama.net y recibirla en todo el país, o retirarla gratis en cualquiera de nuestras 6 sucursales: El Cangrejo, Megapolis, Atrio Mall, San Francisco, Altos de Panamá y Metromall.',
        ),
        array(
            'q' => '¿Qué diferencia hay entre la creatina monohidratada y la micronizada?',
            'a' => 'La diferencia está en el formato: la creatina monohidratada es la presentación más conocida y comercializada, mientras que la micronizada se muele más fina para facilitar su mezcla. Ambas están disponibles en nuestra categoría de creatina en Panamá.',
        ),
        array(
            'q' => '¿Cuánto cuesta la creatina en Panamá?',
            'a' => 'El precio depende de la marca, el tamaño y la cantidad de porciones. En cada ficha de producto ves el precio actualizado y las ofertas vigentes.',
        ),
        array(
            'q' => '¿Hacen envíos de creatina a todo Panamá y puedo retirar en sucursal?',
            'a' => 'Sí. Enviamos a todo Panamá y el envío es gratis en compras desde $150. También puedes comprar online y retirar gratis en la sucursal que elijas al finalizar la compra, en cualquiera de nuestras 6 ubicaciones.',
        ),
    );
}

/* H1 real (el tema no emite ninguno) */
add_action('woocommerce_shop_loop_header', 'sp_creatinas_h1', 5);
function sp_creatinas_h1() {
    if (!function_exists('is_product_category') || !is_product_category('creatina') || is_paged()) {
        return;
    }
    echo '<h1 class="sp-cat-h1" style="font-size:34px;text-transform:uppercase;margin:0 0 10px;">Creatina en Panamá: monohidratada, micronizada y más</h1>';
}

/* FAQ visible (respalda el FAQPage) */
add_action('woocommerce_after_shop_loop', 'sp_creatinas_faq_block', 30);
function sp_creatinas_faq_block() {
    if (!function_exists('is_product_category') || !is_product_category('creatina') || is_paged()) {
        return;
    }
    echo '<section class="sp-cat-faq" style="max-width:900px;margin:44px auto 0;padding:0 16px;">';
    echo '<h2 style="font-size:22px;margin:0 0 16px;">Preguntas frecuentes sobre la creatina</h2>';
    foreach (sp_creatinas_faqs() as $f) {
        echo '<details style="border:1px solid #e8e8e8;padding:12px 16px;margin-bottom:10px;">'
           . '<summary style="font-weight:700;cursor:pointer;font-size:16px;">' . esc_html($f['q']) . '</summary>'
           . '<div style="padding-top:10px;color:#464646;line-height:1.6;">' . esc_html($f['a']) . '</div>'
           . '</details>';
    }
    echo '</section>';
}

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

    /* 2.4b FAQPage (coincide con el FAQ visible que se inyecta en el loop) */
    $graph[] = array(
        '@type'      => 'FAQPage',
        '@id'        => $page_url . '#faq',
        'mainEntity' => array_map(function ($f) {
            return array(
                '@type'          => 'Question',
                'name'           => $f['q'],
                'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f['a']),
            );
        }, sp_creatinas_faqs()),
    );

    /* 2.5 Nodos de sucursal */
    foreach ($branch_nodes as $node) {
        $graph[] = $node;
    }

    echo PHP_EOL . '<script type="application/ld+json" class="sp-schema-creatinas">' . PHP_EOL;
    echo wp_json_encode(array('@context' => 'https://schema.org', '@graph' => $graph), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo PHP_EOL . '</script>' . PHP_EOL;
}
