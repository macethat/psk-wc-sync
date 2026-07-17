<?php
/**
 * Theme functions and definitions.
 */

if (!function_exists('nutritix_form_login')) {
    function nutritix_form_login() {
        if (nutritix_is_woocommerce_activated() && 'yes' === get_option('woocommerce_enable_myaccount_registration')) {
            $register_link = get_permalink(get_option('woocommerce_myaccount_page_id'));
        } else {
            $register_link = wp_registration_url();
        }
        ?>
        <div class="login-form-head">
            <span class="login-form-title"><?php esc_attr_e('Sign in', 'nutritix') ?></span>
            <span class="pull-right">
                <a class="register-link" href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
                   title="<?php esc_attr_e('Register', 'nutritix'); ?>"><?php esc_attr_e('Create an Account', 'nutritix'); ?></a>
            </span>
        </div>
        <form class="nutritix-login-form-ajax" data-toggle="validator">
            <p>
                <label><?php esc_attr_e('Username or email', 'nutritix'); ?> <span class="required">*</span></label>
                <input name="username" type="text" required placeholder="<?php esc_attr_e('Username', 'nutritix') ?>">
            </p>
            <p>
                <label><?php esc_attr_e('Password', 'nutritix'); ?> <span class="required">*</span></label>
                <input name="password" type="password" required
                       placeholder="<?php esc_attr_e('Password', 'nutritix') ?>">
            </p>
            <button type="submit" data-button-action
                    class="btn btn-primary btn-block w-100 mt-1"><?php esc_html_e('Login', 'nutritix') ?></button>
            <input type="hidden" name="action" value="nutritix_login">
            <?php wp_nonce_field('ajax-nutritix-login-nonce', 'security-login'); ?>
        </form>
        <div class="login-form-bottom">
            <a href="<?php echo wp_lostpassword_url(get_permalink()); ?>" class="lostpass-link"
               title="<?php esc_attr_e('Lost your password?', 'nutritix'); ?>"><?php esc_attr_e('Lost your password?', 'nutritix'); ?></a>
        </div>
        <?php
    }
}

// Fix product search dropdown + mobile responsive fixes for grouped products
add_action('wp_head', function() {
    ?>
    <style>
    .product-item-search .product-link{color:var(--text)!important}.product-item-search .product-title{color:var(--text)!important;display:block!important;font-size:14px!important}
    @media (max-width: 767px) {
        .single-product-type-horizontal .woocommerce-grouped-add-to-cart {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .single-product-type-horizontal .woocommerce-grouped-add-to-cart .woosw-btn {
            order: 3;
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .single-product-type-horizontal .woocommerce-grouped-add-to-cart .single_add_to_cart_button {
            order: 2;
            width: 100%;
        }
        .single-product-type-horizontal .woocommerce-grouped-add-to-cart > div:first-child {
            order: 1;
        }
        .related.products {
            padding-left: 15px;
            padding-right: 15px;
        }
        .related.products ul.products {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .related.products ul.products li.product {
            width: calc(50% - 8px) !important;
            margin: 0 !important;
            clear: none !important;
        }
        .related.products ul.products li.product .product-block {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: stretch;
            overflow: visible !important;
            height: auto !important;
        }
        .related.products ul.products li.product .product-caption {
            display: contents;
        }
        .related.products ul.products li.product .woocommerce-loop-product__title {
            flex: 0 0 100%;
            order: 1;
            margin: 0 0 2px !important;
            font-size: 13px !important;
            line-height: 1.3 !important;
            padding: 0 !important;
        }
        .related.products ul.products li.product .woocommerce-loop-product__title a {
            color: #333 !important;
        }
        .related.products ul.products li.product .product-transition {
            order: 2;
            width: 35% !important;
            max-width: 35%;
            flex: none;
            position: relative;
        }
        .related.products ul.products li.product .product-transition .product-image img,
        .related.products ul.products li.product .product-transition img {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            height: auto !important;
        }
        .related.products ul.products li.product .price {
            order: 3;
            width: 65% !important;
            max-width: 65%;
            flex: none;
            text-align: right !important;
            align-self: center;
            margin: 0 !important;
            padding: 0 5px 0 6px !important;
        }
        .related.products ul.products li.product .button.product_type_variable,
        .related.products ul.products li.product .button.product_type_simple {
            order: 4;
            flex: 0 0 100%;
            display: block !important;
            font-size: 12px !important;
            padding: 5px 10px !important;
            text-align: center !important;
            margin: 0 0 2px !important;
            position: relative !important;
            bottom: auto !important;
        }
        .related.products ul.products li.product .button.product_type_grouped {
            display: none !important;
        }
        .related.products ul.products li.product .onsale {
            position: absolute !important;
            top: 5px;
            left: 5px;
            z-index: 2;
            font-size: 10px !important;
            padding: 2px 6px !important;
        }
        .related.products ul.products li.product .posted-in,
        .related.products ul.products li.product .count-review,
        .related.products ul.products li.product .group-action,
        .related.products ul.products li.product .woocommerce-loop-product__link {
            display: none !important;
        }
        .related.products ul.products li.product .product-caption {
            padding: 0 !important;
        }
        @media (max-width: 500px) {
            .related.products ul.products li.product {
                width: 100% !important;
            }
            .related.products ul.products li.product .product-block {
                flex-direction: column !important;
            }
            .related.products ul.products li.product .product-transition {
                width: 100% !important;
                max-width: 100%;
            }
            .related.products ul.products li.product .price {
                width: 100% !important;
                max-width: 100%;
                text-align: left !important;
            }
        }
    }
    </style>
    <?php
});

/**
 * Enhanced Structured Data for Grouped Products (Combos)
 * Adds hasVariant data linking child products to the combo
 */
add_action('wp_head', 'sp_output_combo_structured_data', 99);

function sp_output_combo_structured_data() {
    if (!is_product()) return;
    
    $product_id = get_queried_object_id();
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('grouped')) return;
    
    $combo_price = get_post_meta($product_id, '_combo_price', true);
    if ($combo_price === '' || $combo_price === false) return;
    
    $children = $product->get_children();
    if (empty($children)) return;
    
    $site_url = home_url('/');
    $currency = get_woocommerce_currency();
    $combo_price_val = wc_format_decimal((float) $combo_price, 2);
    
    $has_variants = [];
    $total_retail = 0;
    
    foreach ($children as $child_id) {
        $child = wc_get_product($child_id);
        if (!$child) continue;
        
        $child_price = (float) $child->get_price();
        $total_retail += $child_price;
        
        $child_cats = wp_strip_all_tags(wc_get_product_category_list($child_id));
        $child_sku = $child->get_sku();
        $child_image_id = $child->get_image_id();
        $child_image = $child_image_id 
            ? wp_get_attachment_url($child_image_id) 
            : wc_placeholder_img_src();
        
        $variant = array(
            '@type' => 'Product',
            'name' => $child->get_name(),
            'description' => wp_strip_all_tags(
                $child->get_short_description() 
                    ?: $child->get_description() 
                    ?: get_the_excerpt($child_id)
            ),
            'url' => get_permalink($child_id),
            'image' => $child_image,
            '@id' => get_permalink($child_id) . '#product',
            'category' => $child_cats,
            'offers' => array(
                '@type' => 'Offer',
                'price' => wc_format_decimal($child_price, 2),
                'priceCurrency' => $currency,
                'availability' => $child->is_in_stock() 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
                'url' => get_permalink($child_id),
            ),
        );
        
        if (!empty($child_sku)) {
            $variant['sku'] = $child_sku;
        }
        
        $has_variants[] = $variant;
    }
    
    $product_image_id = $product->get_image_id();
    $product_image = $product_image_id 
        ? wp_get_attachment_url($product_image_id) 
        : wc_placeholder_img_src();
    
    $product_sku = $product->get_sku();
    $product_cats = wp_strip_all_tags(wc_get_product_category_list($product_id));
    $created_date = get_the_date('c', $product_id);
    $gtin14 = '';
    if (!empty($product_sku)) {
        $gtin_raw = preg_replace('/[^0-9]/', '', $product_sku);
        $len = strlen($gtin_raw);
        if (in_array($len, [8, 12, 13, 14])) {
            $gtin14 = $gtin_raw;
        }
    }
    if (empty($gtin14) && method_exists($product, 'get_global_unique_id')) {
        $gtin14 = $product->get_global_unique_id();
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        '@id' => get_permalink($product_id) . '#product',
        'name' => $product->get_name(),
        'description' => wp_strip_all_tags(
            $product->get_short_description() 
                ?: $product->get_description() 
                ?: get_the_excerpt()
        ),
        'image' => $product_image,
        'category' => $product_cats,
        'offers' => array(
            '@type' => 'Offer',
            'url' => get_permalink($product_id),
            'price' => $combo_price_val,
            'priceCurrency' => $currency,
            'priceValidUntil' => date('Y-m-d', strtotime('+1 year')),
            'availability' => $product->is_in_stock() 
                ? 'https://schema.org/InStock' 
                : 'https://schema.org/OutOfStock',
            'validFrom' => $created_date,
            'itemCondition' => 'https://schema.org/NewCondition',
            'hasMerchantReturnPolicy' => array(
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'PA',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays' => 30,
                'returnMethod' => 'https://schema.org/ReturnInStore',
                'returnFees' => 'https://schema.org/FreeReturn',
                'refundType' => 'https://schema.org/FullRefund',
            ),
            'shippingDetails' => array(
                '@type' => 'OfferShippingDetails',
                'shippingRate' => array(
                    '@type' => 'MonetaryAmount',
                    'value' => 0,
                    'currency' => 'USD',
                ),
                'shippingDestination' => array(
                    '@type' => 'DefinedRegion',
                    'addressCountry' => 'PA',
                ),
                'deliveryTime' => array(
                    '@type' => 'ShippingDeliveryTime',
                    'handlingTime' => array(
                        '@type' => 'QuantitativeValue',
                        'minValue' => 0,
                        'maxValue' => 1,
                        'unitCode' => 'DAY',
                    ),
                    'transitTime' => array(
                        '@type' => 'QuantitativeValue',
                        'minValue' => 1,
                        'maxValue' => 5,
                        'unitCode' => 'DAY',
                    ),
                ),
            ),
            'seller' => array(
                '@type' => 'Organization',
                '@id' => $site_url . '#organization',
                'name' => get_bloginfo('name'),
                'url' => $site_url,
            ),
        ),
        'hasVariant' => $has_variants,
    );
    
    $schema['sku'] = !empty($product_sku) 
        ? $product_sku 
        : 'COMBO-' . $product_id;
    
    $schema['mpn'] = 'SP-COMBO-' . $product_id;
    
    $brand_name = get_bloginfo('name');
    if (taxonomy_exists('product_brand')) {
        $brands = wp_get_post_terms($product_id, 'product_brand');
        if (!empty($brands) && !is_wp_error($brands)) {
            $brand_name = $brands[0]->name;
        }
    } elseif (taxonomy_exists('pa_brand')) {
        $brands = wp_get_post_terms($product_id, 'pa_brand');
        if (!empty($brands) && !is_wp_error($brands)) {
            $brand_name = $brands[0]->name;
        }
    }
    if ($gtin14) {
        $schema['gtin14'] = $gtin14;
    }
    
    $schema['brand'] = array(
        '@type' => 'Brand',
        'name' => $brand_name,
    );
    
    $rating_count = $product->get_rating_count();
    $average_rating = $product->get_average_rating();
    if ($rating_count > 0) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => (string) round($average_rating, 1),
            'reviewCount' => $rating_count,
            'bestRating' => '5',
            'worstRating' => '1',
        );
    }
    
    $savings = $total_retail - (float) $combo_price;
    if ($savings > 0) {
        $schema['description'] .= sprintf(
            ' Ahorras $%s al comprar este combo vs. comprar los productos por separado.',
            wc_format_decimal($savings, 2)
        );
    }
    
    echo '<script type="application/ld+json" class="sp-combo-schema">' . "\n";
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}


add_filter('rank_math/json_ld', function($data, $jsonld) {
    $product_id = get_queried_object_id();
    if ($product_id) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_type('grouped')) {
            $combo_price = get_post_meta($product_id, '_combo_price', true);
            if ($combo_price !== '' && $combo_price !== false) {
                unset($data['richSnippet']);
            }
        }
    }
    foreach ($data as $key => $node) {
        if (!isset($node['@type'])) continue;
        $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
        if (!in_array('Store', $types) && !in_array('Organization', $types)) continue;
        if (isset($data[$key]['address'])) {
            $data[$key]['address']['addressCountry'] = 'PA';
        }
    }
    return $data;
}, 98, 2);

// === REDIRECT /blog/ → /power-rack/ ===
add_action('template_redirect', function () {
    if (trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === 'blog') {
        wp_redirect(home_url('/power-rack/'), 301);
        exit;
    }
});

// === CSS para Power Rack posts ===
add_action('wp_head', function () {
    if (!is_single() && !is_home()) return;
    echo '<style>.content-area h2, .content-area h3, .content-area h4, #primary h2, #primary h3, #primary h4 { color: #000000 !important; } .single-post .entry-title { font-size: 48px !important; } .content-area p, #primary p { font-size: 17px !important; line-height: 1.8 !important; } .content-area li, #primary li { font-size: 17px !important; line-height: 1.8 !important; } .sp-toc-wrap { text-align: left; } .sp-toc { display: inline-block; padding: 20px 25px; border-radius: 8px; margin-bottom: 30px; text-align: left; } .sp-toc-title { font-size: 18px; display: block; margin-bottom: 10px; color: #333; } .sp-toc > ul { list-style: none; margin: 0; padding: 0; } .sp-toc li { list-style: none; margin-bottom: 6px; } .sp-toc li:last-child { margin-bottom: 0; } .sp-toc-hr { border: none; border-top: 1px solid #e0d6d0; margin: 6px 0; } .sp-section-hr { border: none; border-top: 1px solid #e0d6d0; margin: 30px 0; } .sp-toc-h2-line { display: flex; align-items: flex-start; gap: 8px; position: relative; padding-right: 35px; } .sp-toc-h2-line a { color: #333; text-decoration: none; font-size: 15px; line-height: 1.4; } .sp-toc-h2-line a:hover { text-decoration: underline; } .sp-toc-bullet { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #d8bfe8; flex-shrink: 0; margin-top: 6px; } .sp-toc-toggle { cursor: pointer; font-size: 40px; line-height: 1; position: absolute; right: 0; top: -8px; user-select: none; color: #9b59b6; transition: transform .2s; } .sp-toc-toggle.open { transform: rotate(90deg); } .sp-toc-sub { display: none; margin: 4px 0 0 16px !important; padding: 0; } .sp-toc-sub.open { display: block; } .sp-toc-sub li { margin: 4px 0; font-size: inherit !important; } .sp-toc-sub li a { color: #555; text-decoration: none; font-size: 14px; line-height: 1.4; } .sp-toc-sub li a:hover { text-decoration: underline; } @media (max-width: 767px) { .single-post .entry-title { font-size: 27px !important; } .content-area h2, #primary h2 { font-size: 22px !important; } .content-area h3, #primary h3 { font-size: 19px !important; } }</style>' . "\n";
    echo '<script>document.addEventListener("click",function(e){var t=e.target.closest(".sp-toc-toggle");if(t){t.classList.toggle("open");var s=t.parentElement.nextElementSibling;if(s&&s.classList.contains("sp-toc-sub"))s.classList.toggle("open")}});</script>' . "\n";
    $accent = get_post_meta(get_the_ID(), 'highlight_accent', true);
    if ($accent) {
        echo '<style>.sp-toc-bullet { background: ' . esc_attr($accent) . ' !important; } .sp-toc-toggle { color: ' . esc_attr($accent) . ' !important; }</style>' . "\n";
    }
});

// === TABLE OF CONTENTS auto-generado para blog posts ===
add_filter('the_content', function ($content) {
    if (!is_single() || !is_main_query()) return $content;
    $pattern = '/<h([23])([^>]*)>(.*?)<\/h\1>/i';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    if (empty($matches)) return $content;
    $ids_used = [];
    $h2_items = [];
    $current = null;
    foreach ($matches as $m) {
        $level = $m[1];
        $attrs = $m[2];
        $title = wp_strip_all_tags($m[3]);
        $id = sanitize_title($title);
        if (isset($ids_used[$id])) { $ids_used[$id]++; $id .= '-' . $ids_used[$id]; }
        else { $ids_used[$id] = 1; }
        if (strpos($attrs, 'id=') === false) {
            $content = str_replace($m[0], '<h' . $level . ' id="' . $id . '"' . $attrs . '>' . $m[3] . '</h' . $level . '>', $content);
        }
        if ($level === '2') {
            $current = ['t' => $title, 'id' => $id, 'c' => []];
            $h2_items[] = $current;
        } elseif ($level === '3' && $current !== null) {
            $h2_items[count($h2_items)-1]['c'][] = ['t' => $title, 'id' => $id];
        }
    }
    if (empty($h2_items)) return $content;
    $bg = get_post_meta(get_the_ID(), 'highlight_bg', true);
    if (!$bg) $bg = '#f0ebe6';
    $post_title = get_the_title();
    $toc = '<div class="sp-toc-wrap" title="Tabla de contenido" data-description="' . esc_attr($post_title) . '"><div class="sp-toc" style="background:' . esc_attr($bg) . '"><strong class="sp-toc-title">Highlights</strong><ul>';
    $first = true;
    foreach ($h2_items as $item) {
        if (!$first) $toc .= '<hr class="sp-toc-hr">';
        $first = false;
        $has_sub = !empty($item['c']);
        $toc .= '<li>';
        $toc .= '<div class="sp-toc-h2-line"><span class="sp-toc-bullet"></span><a href="#' . $item['id'] . '">' . $item['t'] . '</a>';
        if ($has_sub) $toc .= '<span class="sp-toc-toggle">▸</span>';
        $toc .= '</div>';
        if ($has_sub) {
            $toc .= '<ul class="sp-toc-sub">';
            foreach ($item['c'] as $ch) {
                $toc .= '<li><a href="#' . $ch['id'] . '">' . $ch['t'] . '</a></li>';
            }
            $toc .= '</ul>';
        }
        $toc .= '</li>';
    }
    $i = 0;
    $content = preg_replace_callback('/<h2/i', function($m) use (&$i) {
        $i++;
        return $i === 1 ? $m[0] : '<hr class="sp-section-hr">' . $m[0];
    }, $content);
    // Botón CTA dinámico antes del reel de productos
    $reel_cat = get_post_meta(get_the_ID(), 'reel_category', true);
    if ($reel_cat) {
        $cat_term = get_term_by('slug', $reel_cat, 'product_cat');
        if ($cat_term) {
            $btn_text = get_post_meta(get_the_ID(), 'reel_btn_text', true);
            if (!$btn_text) {
                $btn_text = 'Ver todas nuestras ' . strtolower($cat_term->name);
            }
            $cat_url = get_term_link($cat_term);
            $btn = '<div style="text-align:center;margin:30px 0"><a href="' . esc_url($cat_url) . '" style="display:inline-block;background:#c0392b;color:#fff;padding:12px 30px;text-decoration:none;font-size:16px;font-weight:600">' . esc_html($btn_text) . '</a></div>';
            $content = preg_replace('/\[products/', $btn . "\n\n" . '[products', $content, 1);
        }
    }
    return $toc . '</ul></div></div>' . $content;
}, 10);

// === ARTICLE SCHEMA para Power Rack posts ===
add_action('wp_head', function () {
    if (!is_single()) return;
    $schema = get_post_meta(get_the_ID(), 'power_rack_schema', true);
    if ($schema) {
        echo '<script type="application/ld+json" class="power-rack-schema">' . "\n" . $schema . "\n" . '</script>' . "\n";
    }
});

// === META KEYWORDS para homepage ===
add_action('wp_head', function () {
    if (!is_front_page()) return;
    echo '<meta name="keywords" content="suplementos panamá, creatina panamá, proteína panamá, aminoácidos panamá, pre-entreno panamá, whey protein panamá, tienda de suplementos panamá, suplementos deportivos panamá, donde comprar creatina en panamá, quemadores de grasa panamá, vitaminas panamá, nutrición deportiva panamá" />' . "\n";
});


