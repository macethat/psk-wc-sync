<?php
/**
 * Theme functions and definitions.
 */

// dataLayer pushes for GA4 events
add_action('wp_footer', function() {
    if (!function_exists('is_checkout')) return;
    ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // remove_from_cart
    document.body.addEventListener('removed_from_cart', function(e) {
        if (typeof e.detail === 'object' && e.detail) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: 'remove_from_cart',
                ecommerce: {
                    items: [{
                        item_id: e.detail.product_id,
                        quantity: e.detail.quantity
                    }]
                }
            });
        }
    });

    // add_shipping_info + add_payment_info for checkout
    if (document.body.classList.contains('woocommerce-checkout')) {
        // add_shipping_info when checkout page loads (shipping fields present)
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({
            event: 'add_shipping_info',
            ecommerce: {
                shipping_tier: 'local_pickup'
            }
        });
        // add_payment_info when payment method changes
        document.addEventListener('change', function(e) {
            if (e.target.name === 'payment_method') {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event: 'add_payment_info',
                    ecommerce: {
                        payment_type: e.target.value
                    }
                });
            }
        });
    }
});
</script>
    <?php
}, 999);

// Internal traffic detection for GA4 (filter out office/sucursal IPs)
add_action('wp_head', function() {
    $internal_ips = array(
        '200.46.16.73',    // Oficina
        '190.34.132.114',  // SP El Cangrejo
        '200.124.21.55',   // Sucursal
        '181.197.127.90',  // Sucursal
        '181.197.52.165',  // Sucursal
        '200.124.21.60',   // Sucursal
        '200.108.54.209',  // Sucursal
    );
    if (in_array($_SERVER['REMOTE_ADDR'], $internal_ips, true)) {
        echo '<script>window.dataLayer = window.dataLayer || []; window.dataLayer.push({"traffic_type":"internal"});</script>' . "\n";
    }
}, 0);

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

// 301 Redirects for Instagram Ads 404 URLs
add_action('template_redirect', function() {
    if (!is_404()) return;
    $request = trailingslashit(strtok($_SERVER['REQUEST_URI'], '?'));
    $redirects = array(
        '/product/proteinas-iso-nutrex-2-lb-creatina-nutrex-60-serv/' => '/product/prolive-bio6-creatina-nutrex/',
        '/product/proteina-forzagen-5-lb-creatina-nutrex-60-serv/' => '/product/proteina-whey-forzagen-5-lb/',
        '/product/l-carnitina-3500-lipocide-evogen/' => '/product/l-carnitina-impulse-3500/',
        '/product/primeval-labs-proteinas-creatina-nutrex/' => '/product/proteina-primeval-labs-4-8-lb/',
        '/product/proteina-vms-5-lb-creatina-nutrex-200-serv/' => '/product/proteina-blend-vms-nutrition-5-lb/',
        '/product/cla-landerfit-l-carnitina-3500/' => '/product/cla-2000-landerfit-120-capsulas/',
        '/product/nitro-up-5-lb-creatina-nutrex-60-serv/' => '/product/proteina-nitro-up-muscleology-5-lbs/',
        '/product/proteina-vms-5-lb-creatina-forzagen-72-serv/' => '/product/proteina-blend-vms-nutrition-5-lb/',
        '/product/proteina-vms-5-lb-creatina-evogen-60-serv/' => '/product/proteina-blend-vms-nutrition-5-lb/',
        '/product/proteina-gold-standard-1-47-lb-creatina-categoria-5/' => '/product/proteina-100-whey-gold-standard/',
        '/product/lean-gainer-8-lb-creatina-forzagen-72-serv/' => '/product/ganador-de-masa-magra-lean-gainer/',
        '/product/prolive-bio5-bum-pre-blue-razz/' => '/product/prolive-bio5-bum-pre-blue-raspberry/',
        '/product/creatina-evogen-60-serv-beta-alanina-raw/' => '/product/creatina-micronizada-monohidratada/',
        '/product/evp-xtreme-n-o-polar-cherry-frost/' => '/product/suplemento-pre-entreno/',
        '/producto/creatina-micronizada-mohidratada/' => '/product/creatina-micronizada-monohidratada/',
        '/product/creatina-micronizada-monohidratada-nutricost/' => '/product/iso-100-fruity-pebbles-20-serv-1-6-lbs-24-serving-dymatize/',
        '/product/creatina-micronizada-mohidratada/' => '/product/creatina-micronizada-monohidratada/',
        '/product/proteina-iso100-dymatize-1-34-lbs/' => '/product/iso-100-fruity-pebbles-20-serv-1-6-lbs-24-serving-dymatize/',
        '/product-brand/marcas/' => '/shop/',
        '/wp-content/uploads/2025/06/6.jpg' => '/wp-content/uploads/2025/09/6.webp',
        '/wp-content/uploads/2025/06/banner-1920x400-copia-scaled.jpg' => '/wp-content/uploads/2025/09/banner-1920x400-copia-scaled-1.webp',
    );
    if (isset($redirects[$request])) {
        wp_redirect($redirects[$request], 301);
        exit;
    }
});

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

// === SUCURSALES / LOCAL PICKUP ===
define('SP_SUCURSALES', serialize(array(
    '1'  => array('name' => 'SP El Cangrejo',     'address' => 'Plaza El Cangrejo, Av. Manuel Espinosa Batista, Panamá'),
    '5'  => array('name' => 'SP Megapolis',       'address' => 'Megapolis, Piso 3 frente al Smartfit, Av. Vasco Nuñez de Balboa, Panamá'),
    '6'  => array('name' => 'SP Atrio Mall',      'address' => 'Av Marina del Norte, Costa del Este, Panamá'),
    '7'  => array('name' => 'SP San Francisco',   'address' => 'Plaza Arval, C. 72 Este, dentro de PowerCLUB San Francisco, Panamá'),
    '8'  => array('name' => 'SP Altos de Panamá', 'address' => 'Plaza Caminos del Centennial piso 1, dentro de PowerCLUB, Panamá'),
    '10' => array('name' => 'SP Metromall',       'address' => 'Av. Domingo Díaz, Estación Metro Cerro Viento, Panamá'),
)));

function sp_get_sucursales() {
    return unserialize(SP_SUCURSALES);
}

function sp_get_meta_id($item) {
    return !empty($item['variation_id']) ? $item['variation_id'] : $item['product_id'];
}

// Para combos el cart item es el grouped (combo) con combo_children[]; extrae los ids reales (variación o producto hijo)
function sp_get_cart_item_ids($item) {
    if (!empty($item['combo_children']) && is_array($item['combo_children'])) {
        $ids = array();
        foreach ($item['combo_children'] as $child) {
            $ids[] = !empty($child['variation_id']) ? $child['variation_id'] : $child['product_id'];
        }
        return $ids;
    }
    return array(sp_get_meta_id($item));
}

function sp_get_valid_sucursales_for_cart() {
    $sucursales = sp_get_sucursales();
    $cart_items = WC()->cart->get_cart();
    if (empty($cart_items)) return array();
    $valid = array();
    foreach ($sucursales as $aid => $s) {
        $all_available = true;
        foreach ($cart_items as $item) {
            $item_ok = true;
            foreach (sp_get_cart_item_ids($item) as $meta_id) {
                $prod_sucs = get_post_meta($meta_id, '_sucursales_disponibles', true);
                if (empty($prod_sucs) || !in_array((string)$aid, explode(',', $prod_sucs))) {
                    $item_ok = false;
                    break;
                }
            }
            if (!$item_ok) { $all_available = false; break; }
        }
        if ($all_available) $valid[$aid] = $s;
    }
    return $valid;
}

// Hide local_pickup when no cart item has sucursal stock
add_filter('woocommerce_package_rates', 'sp_filter_shipping_methods', 10, 2);
function sp_filter_shipping_methods($rates, $package) {
    if (!function_exists('WC') || !WC()->cart) return $rates;
    $any_sucursal = false;
    foreach (WC()->cart->get_cart() as $item) {
        foreach (sp_get_cart_item_ids($item) as $meta_id) {
            $prod_sucs = get_post_meta($meta_id, '_sucursales_disponibles', true);
            if (!empty($prod_sucs)) { $any_sucursal = true; break 2; }
        }
    }
    if (!$any_sucursal) {
        foreach ($rates as $rate_id => $rate) {
            if (strpos($rate_id, 'local_pickup') !== false) {
                unset($rates[$rate_id]);
            }
        }
    }
    return $rates;
}

function sp_save_sucursal_session() {
    if (!empty($_POST['sp_sucursal_retiro']) && function_exists('WC') && WC()->session) {
        WC()->session->set('sp_sucursal_retiro', sanitize_text_field($_POST['sp_sucursal_retiro']));
    }
}
add_action('template_redirect', 'sp_save_sucursal_session', 10);
add_action('woocommerce_cart_updated', 'sp_save_sucursal_session');
add_action('woocommerce_checkout_update_order_review', 'sp_save_sucursal_session');
add_action('wp_ajax_sp_save_sucursal', 'sp_save_sucursal_ajax');
add_action('wp_ajax_nopriv_sp_save_sucursal', 'sp_save_sucursal_ajax');
function sp_save_sucursal_ajax() {
    if (!empty($_POST['sucursal']) && function_exists('WC') && WC()->session) {
        WC()->session->set('sp_sucursal_retiro', sanitize_text_field($_POST['sucursal']));
        wp_send_json_success();
    }
    wp_send_json_error();
}

// Ensure local pickup is available (creates default zone if none exists)
add_action('init', function () {
    $zones = WC_Shipping_Zones::get_zones();
    if (!empty($zones)) return;
    $zone = new WC_Shipping_Zone(0);
    $zone->add_shipping_method('local_pickup');
});

// Cart page: show sucursal selection after shipping totals
add_action('woocommerce_cart_totals_after_shipping', 'sp_cart_sucursal_field');
function sp_cart_sucursal_field() {
    $valid = sp_get_valid_sucursales_for_cart();
    if (empty($valid)) return;
    $current = WC()->session->get('sp_sucursal_retiro', '');
    echo '<tr class="sp-sucursal-row" style="display:none">';
    echo '<td colspan="2"><label for="sp_sucursal_retiro_cart">Sucursal para retiro <abbr class="required" title="obligatorio">*</abbr></label>';
    echo '<select name="sp_sucursal_retiro" id="sp_sucursal_retiro_cart" style="width:100%">';
    echo '<option value="">Selecciona una sucursal</option>';
    foreach ($valid as $aid => $s) {
        echo '<option value="' . esc_attr($aid) . '" ' . selected($aid, $current, false) . '>' . esc_html($s['name']) . '</option>';
    }
    echo '</select></td></tr>';
}

// Product page: show availability per sucursal
add_action('woocommerce_single_product_summary', 'sp_show_sucursal_stock', 31);
function sp_show_sucursal_stock() {
    global $product;
    if (!$product) return;
    $sucursales = sp_get_sucursales();
    $is_variable = $product->is_type('variable');
    $is_grouped = $product->is_type('grouped');
    $product_ids = $is_variable ? $product->get_visible_children() : ($is_grouped ? $product->get_children() : array($product->get_id()));
    $sucursal_stock = array();
    $variation_data = array();
    $variation_status = array();
    $has_variable_child_grp = false;
    $grouped_var_data = array();

    foreach ($product_ids as $pid) {
        $child = wc_get_product($pid);
        if ($is_grouped && $child && $child->is_type('variable')) {
            $has_variable_child_grp = true;
            $var_ids = $child->get_visible_children();
            $grouped_var_data[$pid] = array();
            foreach ($var_ids as $vid) {
                $disp = get_post_meta($vid, '_sucursales_disponibles', true);
                $grp_stock = array();
                if (!empty($disp)) {
                    foreach (explode(',', $disp) as $aid) {
                        $aid = trim($aid);
                        $grp_stock[$aid] = (int) get_post_meta($vid, '_sucursal_' . $aid . '_stock', true);
                    }
                }
                $grouped_var_data[$pid][$vid] = $grp_stock;
            }
            continue;
        }
        $disponibles = get_post_meta($pid, '_sucursales_disponibles', true);
        $var_sucs = array();
        if (!empty($disponibles)) {
            foreach (explode(',', $disponibles) as $aid) {
                $aid = trim($aid);
                if (!isset($sucursales[$aid])) continue;
                $stock = (int) get_post_meta($pid, '_sucursal_' . $aid . '_stock', true);
                $var_sucs[$aid] = $stock;
                if ($is_grouped) {
                    if (!isset($sucursal_stock[$aid])) $sucursal_stock[$aid] = array();
                    $sucursal_stock[$aid][] = $stock;
                } else {
                    $sucursal_stock[$aid] = ($sucursal_stock[$aid] ?? 0) + $stock;
                }
            }
        } else {
            foreach ($sucursales as $aid => $s) {
                $var_sucs[$aid] = 0;
            }
        }
        if ($is_variable) $variation_data[$pid] = $var_sucs;
        if ($is_variable) $variation_status[$pid] = get_post_meta($pid, '_stock_status', true);
    }

    if ($has_variable_child_grp) {
        $uid = 'sp-suc-' . $product->get_id();
        echo '<div class="sp-sucursal-stock" style="margin-top:15px;padding:12px;background:#f8f8f8;border-radius:6px"';
        echo ' data-sp-grouped="1"';
        echo ' data-sp-simple=\'' . wp_json_encode($sucursal_stock, JSON_HEX_APOS) . '\'';
        echo ' data-sp-grpvar=\'' . wp_json_encode($grouped_var_data, JSON_HEX_APOS) . '\'';
        echo ' data-sp-sucs=\'' . wp_json_encode($sucursales, JSON_HEX_APOS) . '\'';
        echo '>';
        echo '<h4 style="margin:0 0 8px;font-size:14px">Disponible para retiro en:</h4>';
        echo '<ul id="' . $uid . '" style="margin:0;padding:0;list-style:none">';
        foreach ($sucursales as $aid => $s) {
            echo '<li data-sucursal="' . $aid . '" style="padding:3px 0;font-size:13px;color:#999">';
            echo '✓ ' . esc_html($s['name']);
            echo ' <span class="sp-stock-qty" style="color:#666;font-size:12px">(0 unid.)</span>';
            echo '</li>';
        }
        echo '</ul></div>';
        return;
    }

    if ($is_grouped) {
        $combo_stock = array();
        foreach ($sucursal_stock as $aid => $stocks) {
            $combo_stock[$aid] = min($stocks);
        }
        $sucursal_stock = $combo_stock;
    }
    if (empty($sucursal_stock)) {
        echo '<div class="sp-sucursal-stock" style="margin-top:15px;padding:12px;background:#f8f8f8;border-radius:6px;color:#999;font-size:13px">Solo disponible para <strong>Delivery</strong> (no hay stock en sucursales)</div>';
        return;
    }
    $uid = 'sp-suc-' . $product->get_id();
    $container_extra = '';
    if ($is_variable && !empty($variation_data)) {
        $container_extra = ' data-sp-agg="' . esc_attr(json_encode($sucursal_stock)) . '" data-sp-var="' . esc_attr(json_encode($variation_data)) . '"';
        $container_extra .= ' data-sp-status="' . esc_attr(json_encode($variation_status)) . '"';
        $container_extra .= ' data-sp-names="' . esc_attr(json_encode($sucursales)) . '"';
    }
    // Simple/grouped: si el producto está outofstock (regla stock min) pero tiene stock en sucursal, mostrar aviso de compra presencial
    if (!$is_variable && !$is_grouped) {
        $prod_status = $product->get_stock_status();
        $store_avail = array();
        foreach ($sucursal_stock as $aid => $stock) {
            if ($stock > 0 && isset($sucursales[$aid])) $store_avail[] = $sucursales[$aid]['name'];
        }
        if ($prod_status === 'outofstock' && !empty($store_avail)) {
            echo '<div class="sp-sucursal-stock sp-store-msg" style="margin-top:15px;padding:12px;background:#fff3e0;border-radius:6px;color:#e65100;font-size:13px">Disponible solo para compra por sucursal en: ' . esc_html(implode(', ', $store_avail)) . '.</div>';
            return;
        }
    }
    echo '<div class="sp-sucursal-stock" style="margin-top:15px;padding:12px;background:#f8f8f8;border-radius:6px"' . $container_extra . '>';
    echo '<h4 style="margin:0 0 8px;font-size:14px">Disponible para retiro en:</h4>';
    echo '<ul id="' . $uid . '" style="margin:0;padding:0;list-style:none">';
    foreach ($sucursal_stock as $aid => $stock) {
        $color = $stock > 0 ? '#2e7d32' : '#999';
        echo '<li data-sucursal="' . $aid . '" style="padding:3px 0;font-size:13px;color:' . $color . '">';
        echo '✓ ' . esc_html($sucursales[$aid]['name']);
        echo ' <span class="sp-stock-qty" style="color:#666;font-size:12px">(' . $stock . ' unid.)</span>';
        echo '</li>';
    }
    echo '</ul></div>';
}

// Footer JS for sucursal UI (cart/checkout toggle + variable product variation switching)
add_action('wp_footer', 'sp_sucursal_js');
function sp_sucursal_js() {
    if (!is_cart() && !is_checkout() && !is_product()) return;
    ?>
<script>
var sp_ajax = {ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'};
document.addEventListener('DOMContentLoaded', function() {
    // Variation switching for variable products
    var spContainer = document.querySelector('.sp-sucursal-stock[data-sp-var]');
    if (spContainer) {
        var spAgg = JSON.parse(spContainer.getAttribute('data-sp-agg'));
        var spVar = JSON.parse(spContainer.getAttribute('data-sp-var'));
        var spStatus = {};
        var spNames = {};
        try { spStatus = JSON.parse(spContainer.getAttribute('data-sp-status')); } catch(e) {}
        try { spNames = JSON.parse(spContainer.getAttribute('data-sp-names')); } catch(e) {}
        function spUpdateStock(variationId) {
            var data = spVar[variationId] || {};
            var allZero = true;
            var storeOnly = (spStatus[variationId] === 'outofstock');
            var storeList = [];
            spContainer.querySelectorAll('li').forEach(function(li) {
                var a = li.getAttribute('data-sucursal');
                var q = data[a] !== void 0 ? data[a] : (spAgg[a] || 0);
                li.style.color = q > 0 ? '#2e7d32' : '#999';
                li.querySelector('.sp-stock-qty').textContent = '(' + q + ' unid.)';
                if (q > 0) allZero = false;
                if (q > 0 && spNames[a]) storeList.push(spNames[a].name);
            });
            var spMsg = spContainer.querySelector('.sp-delivery-msg');
            var spStoreMsg = spContainer.querySelector('.sp-store-msg');
            if (storeOnly && storeList.length) {
                if (!spStoreMsg) {
                    spStoreMsg = document.createElement('div');
                    spStoreMsg.className = 'sp-store-msg';
                    spStoreMsg.style.cssText = 'margin-top:8px;padding:8px;background:#fff3e0;border-radius:4px;color:#e65100;font-size:12px';
                    spContainer.querySelector('h4').after(spStoreMsg);
                }
                spStoreMsg.textContent = 'Disponible solo para compra por sucursal en: ' + storeList.join(', ') + '.';
                spContainer.querySelector('h4').style.display = 'none';
                spContainer.querySelector('ul').style.display = 'none';
                if (spMsg) spMsg.style.display = 'none';
                spStoreMsg.style.display = '';
            } else {
                if (spStoreMsg) spStoreMsg.style.display = 'none';
                if (allZero) {
                    if (!spMsg) {
                        spMsg = document.createElement('div');
                        spMsg.className = 'sp-delivery-msg';
                        spMsg.style.cssText = 'margin-top:8px;padding:8px;background:#fff3e0;border-radius:4px;color:#e65100;font-size:12px';
                        spMsg.textContent = 'Solo disponible para Delivery (sin stock en sucursales)';
                        spContainer.querySelector('h4').after(spMsg);
                    }
                    spContainer.querySelector('h4').style.display = 'none';
                    spContainer.querySelector('ul').style.display = 'none';
                    spMsg.style.display = '';
                } else {
                    if (spMsg) spMsg.style.display = 'none';
                    spContainer.querySelector('h4').style.display = '';
                    spContainer.querySelector('ul').style.display = '';
                }
            }
        }
        var spLastVarId = '';
        (function spPollVar() {
            var spVarInput = document.querySelector('input.variation_id');
            if (spVarInput && spVarInput.value !== spLastVarId) {
                spLastVarId = spVarInput.value;
                var vid = parseInt(spVarInput.value);
                spUpdateStock(vid > 0 ? vid : null);
            }
            setTimeout(spPollVar, 400);
        })();
    }

    // Grouped product (combo) with variable children: poll variation selections
    var spGrpContainer = document.querySelector('.sp-sucursal-stock[data-sp-grouped]');
    if (spGrpContainer) {
        var spSimpleStock = JSON.parse(spGrpContainer.getAttribute('data-sp-simple'));
        var spGrpVar = JSON.parse(spGrpContainer.getAttribute('data-sp-grpvar'));
        var spSucs = JSON.parse(spGrpContainer.getAttribute('data-sp-sucs'));
        var spGrpLast = {};
        (function spPollGrp() {
            var changed = false;
            Object.keys(spGrpVar).forEach(function(pid) {
                var sel = document.querySelector('select.combo-variation-select[data-child_id="' + pid + '"]');
                var vid;
                if (sel) {
                    vid = sel.value;
                } else {
                    var hidden = document.querySelector('input[name="combo_variation_id[' + pid + ']"]');
                    if (hidden) vid = hidden.value;
                }
                if (vid !== (spGrpLast[pid] || '')) {
                    spGrpLast[pid] = vid || '';
                    changed = true;
                }
            });
            if (changed) {
                var combined = JSON.parse(JSON.stringify(spSimpleStock));
                Object.keys(spGrpVar).forEach(function(pid) {
                    var selVarId = spGrpLast[pid] || '';
                    if (selVarId && spGrpVar[pid][selVarId]) {
                        Object.keys(spGrpVar[pid][selVarId]).forEach(function(aid) {
                            if (!combined[aid]) combined[aid] = [];
                            combined[aid].push(spGrpVar[pid][selVarId][aid]);
                        });
                    } else {
                        Object.keys(spSucs).forEach(function(aid) {
                            if (!combined[aid]) combined[aid] = [];
                            combined[aid].push(0);
                        });
                    }
                });
                var finalStock = {};
                Object.keys(combined).forEach(function(aid) {
                    finalStock[aid] = Math.min.apply(null, combined[aid]);
                });
                var allZero = true;
                spGrpContainer.querySelectorAll('li').forEach(function(li) {
                    var a = li.getAttribute('data-sucursal');
                    var q = finalStock[a] !== void 0 ? finalStock[a] : 0;
                    li.style.color = q > 0 ? '#2e7d32' : '#999';
                    li.querySelector('.sp-stock-qty').textContent = '(' + q + ' unid.)';
                    if (q > 0) allZero = false;
                });
                var spMsg = spGrpContainer.querySelector('.sp-delivery-msg');
                if (allZero) {
                    if (!spMsg) {
                        spMsg = document.createElement('div');
                        spMsg.className = 'sp-delivery-msg';
                        spMsg.style.cssText = 'margin-top:8px;padding:8px;background:#fff3e0;border-radius:4px;color:#e65100;font-size:12px';
                        spMsg.textContent = 'Solo disponible para Delivery (sin stock en sucursales)';
                        spGrpContainer.querySelector('h4').after(spMsg);
                    }
                    spGrpContainer.querySelector('h4').style.display = 'none';
                    spGrpContainer.querySelector('ul').style.display = 'none';
                    spMsg.style.display = '';
                } else {
                    if (spMsg) spMsg.style.display = 'none';
                    spGrpContainer.querySelector('h4').style.display = '';
                    spGrpContainer.querySelector('ul').style.display = '';
                }
            }
            setTimeout(spPollGrp, 600);
        })();
    }

    // Cart / Checkout: show/hide sucursal field based on shipping method
    var spFields = [];

    function spBindSucursalToggle() {
        spFields = document.querySelectorAll('.sp-sucursal-wrap, .sp-sucursal-row');
        if (!spFields.length) return;
        var methods = document.querySelectorAll('input[name^="shipping_method"]');
        var isPickup = false;
        methods.forEach(function(m) {
            if (m.checked && m.value.indexOf('local_pickup') !== -1) isPickup = true;
        });
        spFields.forEach(function(f) { f.style.display = isPickup ? '' : 'none'; });
    }

    spBindSucursalToggle();
    document.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.indexOf('shipping_method') !== -1) {
            spBindSucursalToggle();
        }
    });
    // Delegated change listener on the sucursal selects (cart + checkout).
    // Delegation survives AJAX re-renders by WooCommerce (which replace the DOM nodes).
    document.addEventListener('change', function(e) {
        var t = e.target;
        if (!t || !t.id) return;
        if (t.id !== 'sp_sucursal_retiro' && t.id !== 'sp_sucursal_retiro_cart') return;
        spUpdateSucursalInfo();
        var val = t.value;
        if (!val) return;
        // Save to session via AJAX (independent of WooCommerce)
        var xhr = new XMLHttpRequest();
        xhr.open('POST', sp_ajax.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=sp_save_sucursal&sucursal=' + encodeURIComponent(val));
        // Then trigger WooCommerce refresh
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).trigger('update_checkout');
        }
    });
    function spUpdateSucursalInfo() {
        var methods = document.querySelectorAll('input[name^="shipping_method"]');
        var isPickup = false;
        methods.forEach(function(m) {
            if (m.checked && m.value.indexOf('local_pickup') !== -1) isPickup = true;
        });
        var sel = document.getElementById('sp_sucursal_retiro');
        var field = document.querySelector('.sp-sucursal-wrap');
        if (!sel || !sel.value || !field) {
            var stale = document.querySelector('tr.sp-sucursal-review');
            if (stale) stale.remove();
            var info = document.querySelector('.sp-sucursal-wrap .sp-sucursal-selected-info');
            if (info) info.remove();
            return;
        }
        if (!isPickup) return;
        var selOpt = sel.options[sel.selectedIndex];
        if (!selOpt || !selOpt.text || selOpt.text === 'Selecciona una sucursal') {
            var stale = document.querySelector('tr.sp-sucursal-review');
            if (stale) stale.remove();
            var info = document.querySelector('.sp-sucursal-wrap .sp-sucursal-selected-info');
            if (info) info.remove();
            return;
        }
        // Look up address from data-sp-valid
        var valid = {};
        try { valid = JSON.parse(field.getAttribute('data-sp-valid')); } catch(e) {}
        var addr = (valid[sel.value] && valid[sel.value].address) || '';

        // Update inline info
        var info = document.querySelector('.sp-sucursal-wrap .sp-sucursal-selected-info');
        if (!info) {
            info = document.createElement('div');
            info.className = 'sp-sucursal-selected-info';
            info.style.cssText = 'margin-top:4px;padding:8px;background:#e8f5e9;border-radius:4px;font-size:13px';
            if (field) field.appendChild(info);
        }
        info.innerHTML = '<strong>' + selOpt.text + '</strong><br>' + addr;

        // Update order review row
        var shipRow = document.querySelector('tr.woocommerce-shipping-totals');
        if (shipRow) {
            var existing = document.querySelector('tr.sp-sucursal-review');
            if (existing) {
                existing.querySelector('td').innerHTML = '<span style="word-break:keep-all;overflow-wrap:break-word">' + selOpt.text + (addr ? '<br><small>' + addr + '</small>' : '') + '</span>';
            } else {
                var tr = document.createElement('tr');
                tr.className = 'sp-sucursal-review';
                tr.innerHTML = '<th>Sucursal</th><td style="word-break:keep-all;overflow-wrap:break-word">' + selOpt.text + (addr ? '<br><small>' + addr + '</small>' : '') + '</td>';
                shipRow.parentNode.insertBefore(tr, shipRow.nextSibling);
            }
        }
    }
    // Poll for AJAX cart/checkout refreshes that replace the DOM
    setInterval(spBindSucursalToggle, 800);
    setInterval(spUpdateSucursalInfo, 800);
});
</script>
    <?php
}

// Detect if local_pickup is the effective shipping method (from POST, session, or package rates fallback)
function sp_get_cart_shipping_rate_ids() {
    $ids = array();
    if (!function_exists('WC') || !WC()->cart) return $ids;
    if (!WC()->shipping) { WC()->shipping = new WC_Shipping(); WC()->shipping->init(); }
    foreach (WC()->cart->get_shipping_packages() as $pkg) {
        $rates = WC()->shipping->calculate_shipping_for_package($pkg);
        if (is_array($rates)) {
            $pkg_rates = !empty($rates['rates']) && is_array($rates['rates']) ? $rates['rates'] : $rates;
            foreach (array_keys($pkg_rates) as $rid) $ids[] = (string) $rid;
        }
    }
    return $ids;
}

function sp_is_local_pickup_selected() {
    if (!empty($_POST['shipping_method'])) {
        $m = is_array($_POST['shipping_method']) ? $_POST['shipping_method'][0] : $_POST['shipping_method'];
        if (strpos($m, 'local_pickup') !== false) return true;
        return false;
    }
    if (function_exists('WC') && WC()->session) {
        $chosen = WC()->session->get('chosen_shipping_methods');
        if (is_array($chosen) && !empty($chosen) && strpos($chosen[0], 'local_pickup') !== false) return true;
        // Si el usuario ya eligió sucursal en el carrito, su intención es retiro
        if (!empty(WC()->session->get('sp_sucursal_retiro')) && sp_get_valid_sucursales_for_cart()) {
            foreach (sp_get_cart_shipping_rate_ids() as $rid) {
                if (strpos($rid, 'local_pickup') !== false) return true;
            }
        }
    }
    // Fallback: if no method chosen yet, check first available package rate
    $ids = sp_get_cart_shipping_rate_ids();
    if (!empty($ids) && strpos($ids[0], 'local_pickup') !== false) return true;
    return false;
}

// Checkout: add sucursal selection field
add_action('woocommerce_after_checkout_shipping_form', 'sp_checkout_sucursal_field');
function sp_checkout_sucursal_field($checkout) {
    $valid = sp_get_valid_sucursales_for_cart();
    if (empty($valid)) return;
    $options = array('' => 'Selecciona una sucursal');
    foreach ($valid as $aid => $s) {
        $options[$aid] = $s['name'];
    }
    $default = $checkout->get_value('sp_sucursal_retiro');
    if (empty($default)) {
        $default = WC()->session->get('sp_sucursal_retiro', '');
    }
    $is_pickup = sp_is_local_pickup_selected();
    $field_style = $is_pickup ? '' : 'display:none';
    $debug_sel = !empty($_POST['sp_sucursal_retiro']) ? $_POST['sp_sucursal_retiro'] : (WC()->session->get('sp_sucursal_retiro', ''));
    echo '<!-- SP_DEBUG selected=' . esc_attr($debug_sel) . ' method=' . esc_attr($is_pickup ? 'local_pickup' : '') . ' -->';
    echo '<div class="sp-sucursal-wrap" style="' . $field_style . '" data-sp-valid=\'' . wp_json_encode($valid, JSON_HEX_APOS) . '\'>';
    woocommerce_form_field('sp_sucursal_retiro', array(
        'type'     => 'select',
        'class'    => array('form-row-wide', 'sp-sucursal-field'),
        'label'    => 'Sucursal para retiro',
        'required' => true,
        'options'  => $options,
    ), $default);
    $display_name = '';
    $display_addr = '';
    if (!empty($default) && isset($valid[$default])) {
        $display_name = $valid[$default]['name'];
        $display_addr = $valid[$default]['address'];
    }
    echo '<div class="sp-sucursal-selected-info" style="' . ($display_name ? 'margin-top:4px;padding:8px;background:#e8f5e9;border-radius:4px;font-size:13px' : 'display:none') . '">';
    if ($display_name) {
        echo '<strong>' . esc_html($display_name) . '</strong><br>' . esc_html($display_addr);
    }
    echo '</div>';
    echo '</div>';
}

// Order review: show selected sucursal after shipping row
// Show selected sucursal in order review + AJAX fragment
add_action('woocommerce_review_order_after_shipping', 'sp_review_order_sucursal');
add_filter('woocommerce_update_order_review_fragments', 'sp_sucursal_fragment');
function sp_review_order_sucursal() {
    echo sp_get_sucursal_review_html();
}
function sp_get_sucursal_review_html() {
    $selected = '';
    if (!empty($_POST['sp_sucursal_retiro'])) {
        $selected = sanitize_text_field($_POST['sp_sucursal_retiro']);
    } elseif (function_exists('WC') && WC()->session) {
        $selected = WC()->session->get('sp_sucursal_retiro', '');
    }
    if (empty($selected)) return '';
    $sucursales = sp_get_sucursales();
    if (!sp_is_local_pickup_selected()) return '';
    if (!isset($sucursales[$selected])) return '';
    return '<tr class="sp-sucursal-review"><th>Sucursal</th><td style="word-break:keep-all;overflow-wrap:break-word">' . esc_html($sucursales[$selected]['name']) . '<br><small>' . esc_html($sucursales[$selected]['address']) . '</small></td></tr>';
}
function sp_sucursal_fragment($fragments) {
    $fragments['tr.sp-sucursal-review'] = sp_get_sucursal_review_html();
    return $fragments;
}

// Validate
add_action('woocommerce_checkout_process', 'sp_validate_sucursal_field');
function sp_validate_sucursal_field() {
    if (!sp_is_local_pickup_selected()) return;
    if (empty($_POST['sp_sucursal_retiro'])) {
        wc_add_notice('Por favor selecciona la sucursal donde retirarás tu pedido.', 'error');
    }
}

// Save order meta
add_action('woocommerce_checkout_update_order_meta', 'sp_save_sucursal_order_meta');
function sp_save_sucursal_order_meta($order_id) {
    if (!empty($_POST['sp_sucursal_retiro'])) {
        $sucursales = sp_get_sucursales();
        $aid = sanitize_text_field($_POST['sp_sucursal_retiro']);
        if (isset($sucursales[$aid])) {
            update_post_meta($order_id, '_sp_sucursal_retiro_id', $aid);
            update_post_meta($order_id, '_sp_sucursal_retiro_name', $sucursales[$aid]['name']);
            update_post_meta($order_id, '_sp_sucursal_retiro_address', $sucursales[$aid]['address']);
        }
    }
}

// Show in admin order details
add_action('woocommerce_admin_order_data_after_shipping_address', 'sp_admin_order_sucursal');
function sp_admin_order_sucursal($order) {
    $sucursal_name = $order->get_meta('_sp_sucursal_retiro_name');
    $sucursal_address = $order->get_meta('_sp_sucursal_retiro_address');
    if ($sucursal_name) {
        echo '<p><strong>Sucursal de retiro:</strong><br>' . esc_html($sucursal_name) . '<br>' . esc_html($sucursal_address) . '</p>';
    }
}

// Show in email
add_action('woocommerce_email_customer_details', 'sp_email_sucursal', 25, 4);
function sp_email_sucursal($order, $sent_to_admin, $plain_text, $email) {
    $sucursal_name = $order->get_meta('_sp_sucursal_retiro_name');
    $sucursal_address = $order->get_meta('_sp_sucursal_retiro_address');
    if ($sucursal_name) {
        if ($plain_text) {
            echo "Sucursal de retiro: $sucursal_name ($sucursal_address)\n";
        } else {
            echo '<p><strong>Sucursal de retiro:</strong><br>' . esc_html($sucursal_name) . '<br>' . esc_html($sucursal_address) . '</p>';
        }
    }
}


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
    if (is_product_category() || is_shop() || is_product_tag()) {
        $walk = function(&$item) use (&$walk) {
            if (is_array($item)) {
                if (isset($item['itemListElement']) && is_array($item['itemListElement'])) {
                    foreach ($item['itemListElement'] as &$li) {
                        if (isset($li['item']['@type']) && $li['item']['@type'] === 'Product') {
                            unset($li['item']['@type']);
                        }
                    }
                }
                foreach ($item as &$v) {
                    $walk($v);
                }
            }
        };
        $walk($data);
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
    if (!is_single() && !is_home() && !is_category()) return;
    echo '<style>.content-area h2, .content-area h3, .content-area h4, #primary h2, #primary h3, #primary h4 { color: #000000 !important; } .single-post .entry-title { font-size: 48px !important; } .content-area p, #primary p { font-size: 17px !important; line-height: 1.8 !important; } .content-area li, #primary li { font-size: 17px !important; line-height: 1.8 !important; } @media (max-width: 767px) { .blog-style-grid .entry-title { font-size: 22px !important; margin-bottom: 4px !important; } .blog-style-grid .entry-title a { font-size: 22px !important; } .blog-style-grid .elementor-grid-item { display: flex; flex-direction: column; } .blog-style-grid .entry-header { order: 1; } .blog-style-grid .entry-meta { order: 2; } .blog-style-grid .post-thumbnail { order: 3; } .blog-style-grid { row-gap: 0px !important; } } .blog-style-grid .entry-title { white-space: normal !important; overflow: visible !important; text-overflow: clip !important; display: block !important; -webkit-line-clamp: unset !important; -webkit-box-orient: unset !important; } .blog-style-grid { row-gap: 35px !important; } .sp-toc-wrap { text-align: left; } .sp-toc { display: inline-block; padding: 20px 25px; border-radius: 8px; margin-bottom: 30px; text-align: left; } .sp-toc-title { font-size: 18px; display: block; margin-bottom: 10px; color: #333; } .sp-toc > ul { list-style: none; margin: 0; padding: 0; } .sp-toc li { list-style: none; margin-bottom: 6px; } .sp-toc li:last-child { margin-bottom: 0; } .sp-toc-hr { border: none; border-top: 1px solid #e0d6d0; margin: 6px 0; } .sp-section-hr { border: none; border-top: 1px solid #e0d6d0; margin: 30px 0; } .sp-toc-h2-line { display: flex; align-items: flex-start; gap: 8px; position: relative; padding-right: 35px; } .sp-toc-h2-line a { color: #333; text-decoration: none; font-size: 15px; line-height: 1.4; } .sp-toc-h2-line a:hover { text-decoration: underline; } .sp-toc-bullet { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #d8bfe8; flex-shrink: 0; margin-top: 6px; } .sp-toc-toggle { cursor: pointer; font-size: 40px; line-height: 1; position: absolute; right: 0; top: -8px; user-select: none; color: #9b59b6; transition: transform .2s; } .sp-toc-toggle.open { transform: rotate(90deg); } .sp-toc-sub { display: none; margin: 4px 0 0 16px !important; padding: 0; } .sp-toc-sub.open { display: block; } .sp-toc-sub li { margin: 4px 0; font-size: inherit !important; } .sp-toc-sub li a { color: #555; text-decoration: none; font-size: 14px; line-height: 1.4; } .sp-toc-sub li a:hover { text-decoration: underline; } @media (max-width: 767px) { .single-post .entry-title { font-size: 27px !important; } .content-area h2, #primary h2 { font-size: 22px !important; } .content-area h3, #primary h3 { font-size: 19px !important; } }</style>' . "\n";
    echo '<script>document.addEventListener("click",function(e){var t=e.target.closest(".sp-toc-toggle");if(t){t.classList.toggle("open");var s=t.parentElement.nextElementSibling;if(s&&s.classList.contains("sp-toc-sub"))s.classList.toggle("open")}});</script>' . "\n";
    $accent = get_post_meta(get_the_ID(), 'highlight_accent', true);
    $bullet = get_post_meta(get_the_ID(), 'highlight_bullet_color', true);
    $hr_color = get_post_meta(get_the_ID(), 'highlight_hr_color', true);
    $extra = '';
    if ($accent) {
        $extra .= '.sp-toc-toggle { color: ' . esc_attr($accent) . ' !important; }';
    }
    if ($bullet) {
        $extra .= '.sp-toc-bullet { background: ' . esc_attr($bullet) . ' !important; }';
    } elseif ($accent) {
        $extra .= '.sp-toc-bullet { background: ' . esc_attr($accent) . ' !important; }';
    }
    if ($hr_color) {
        $extra .= '.sp-toc-hr { border-top-color: ' . esc_attr($hr_color) . ' !important; } .sp-section-hr { border-top-color: ' . esc_attr($hr_color) . ' !important; }';
    }
    if ($extra) {
        echo '<style>' . $extra . '</style>' . "\n";
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

// === META PIXEL + CAPI para Combos (campaña Meta Ads) ===
define('SP_META_PIXEL_ID', '1366708898928630');
define('SP_META_CAPI_TOKEN', 'EAAbqUui0F1YBR9R51WQFmLYFXozFUDWjoUcOVrX4IgXBuZCAQnaj98VDUqXfZAcSi5os7vDZBAkiUhZBbF0xfs3rqzeV3dK7DRXO2eKg83M3xtxElDS87NqZAXfohHM7JlDUHwnuyRRP7sUCmmpsLYKra6cJhaZBdMZAUMlAmEhRKzNPT9qEbZBy3h2PD4HYKwZDZD');

// Base pixel code on all pages
add_action('wp_head', function () {
    $pid = SP_META_PIXEL_ID;
    ?>
<!-- Meta Pixel Code (Combos) -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo esc_js($pid); ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo esc_attr($pid); ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
    <?php
});

// === CAPI: send server-side event to Meta ===
function sp_capi_send($event_name, $custom_data, $event_id = null, $extra_user_data = array()) {
    $user_data = array(
        'client_ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'fbp' => $_COOKIE['_fbp'] ?? '',
        'fbc' => $_COOKIE['_fbc'] ?? '',
    );
    $user_data = array_merge($user_data, $extra_user_data);
    $payload = array(
        'data' => array(array(
            'event_name' => $event_name,
            'event_time' => time(),
            'action_source' => 'website',
            'event_source_url' => home_url(add_query_arg(null, null)),
            'user_data' => $user_data,
            'custom_data' => $custom_data,
        )),
        'access_token' => SP_META_CAPI_TOKEN,
    );
    if ($event_id) $payload['data'][0]['event_id'] = $event_id;
    wp_remote_post('https://graph.facebook.com/v21.0/' . SP_META_PIXEL_ID . '/events', array(
        'headers' => array('Content-Type' => 'application/json'),
        'body' => json_encode($payload),
        'timeout' => 5,
        'blocking' => false,
        'sslverify' => true,
    ));
}

// ViewContent on single combo product pages
add_action('wp_head', function () {
    if (!is_product()) return;
    $product_id = get_queried_object_id();
    $product = wc_get_product($product_id);
    if (!$product || !$product->is_type('grouped')) return;
    $combo_price = get_post_meta($product_id, '_combo_price', true);
    if ($combo_price === '' || $combo_price === false) return;
    $category = wp_strip_all_tags(wc_get_product_category_list($product_id));
    $event_id = 'vc_' . $product_id . '_' . time();
    sp_capi_send('ViewContent', array(
        'content_type' => 'product_group',
        'content_ids' => array((string) $product_id),
        'content_name' => $product->get_name(),
        'content_category' => $category,
        'value' => (float) $combo_price,
        'currency' => get_woocommerce_currency(),
    ), $event_id);
    ?>
<script>
fbq('track', 'ViewContent', {
    content_type: 'product_group',
    content_ids: ['<?php echo esc_js($product_id); ?>'],
    content_name: '<?php echo esc_js($product->get_name()); ?>',
    content_category: '<?php echo esc_js($category); ?>',
    value: <?php echo (float) $combo_price; ?>,
    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
});
</script>
    <?php
});

// Helper: get all combo parent IDs (cached)
function sp_get_combo_ids() {
    $ids = wp_cache_get('sp_combo_ids', 'sp_meta');
    if ($ids === false) {
        $ids = get_posts(array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array('key' => '_combo_price', 'value' => '', 'compare' => '!='),
            ),
        ));
        wp_cache_set('sp_combo_ids', $ids, 'sp_meta', HOUR_IN_SECONDS);
    }
    return $ids;
}

// Helper: find parent combo ID for a child product
function sp_find_combo_parent($child_id) {
    $combo_ids = sp_get_combo_ids();
    foreach ($combo_ids as $parent_id) {
        $parent = wc_get_product($parent_id);
        if ($parent && $parent->is_type('grouped')) {
            $children = $parent->get_children();
            if (in_array($child_id, $children)) return $parent_id;
        }
    }
    return null;
}

// AddToCart when a combo product is added
add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    $parent_id = sp_find_combo_parent($product_id);
    if (!$parent_id) return;
    $parent = wc_get_product($parent_id);
    if (!$parent) return;
    $combo_price = get_post_meta($parent_id, '_combo_price', true);
    $event_id = 'atc_' . $parent_id . '_' . time();
    sp_capi_send('AddToCart', array(
        'content_type' => 'product_group',
        'content_ids' => array((string) $parent_id),
        'content_name' => $parent->get_name(),
        'value' => (float) $combo_price,
        'currency' => get_woocommerce_currency(),
    ), $event_id);
    ?>
<script>
fbq('track', 'AddToCart', {
    content_type: 'product_group',
    content_ids: ['<?php echo esc_js($parent_id); ?>'],
    content_name: '<?php echo esc_js($parent->get_name()); ?>',
    value: <?php echo (float) $combo_price; ?>,
    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
});
</script>
    <?php
}, 10, 6);

// InitiateCheckout if cart contains combos
add_action('woocommerce_before_checkout_form', function () {
    $found = [];
    foreach (WC()->cart->get_cart() as $item) {
        $pid = sp_find_combo_parent($item['product_id']);
        if ($pid) $found[] = $pid;
    }
    if (empty($found)) return;
    $unique = array_unique($found);
    $event_id = 'ic_' . md5(implode(',', $unique) . time());
    sp_capi_send('InitiateCheckout', array(
        'content_type' => 'product_group',
        'content_ids' => array_map('strval', $unique),
        'num_items' => WC()->cart->get_cart_contents_count(),
        'value' => (float) WC()->cart->get_total('numeric'),
        'currency' => get_woocommerce_currency(),
    ), $event_id);
    ?>
<script>
fbq('track', 'InitiateCheckout', {
    content_type: 'product_group',
    content_ids: <?php echo json_encode(array_map('strval', $unique)); ?>,
    num_items: <?php echo (int) WC()->cart->get_cart_contents_count(); ?>,
    value: <?php echo (float) WC()->cart->get_total('numeric'); ?>,
    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
});
</script>
    <?php
});

// Purchase on thank you page for orders with combos
add_action('woocommerce_thankyou', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    $combo_ids = [];
    $combo_total = 0;
    $extra = array();
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product) continue;
        $pid = sp_find_combo_parent($product->get_id());
        if ($pid) {
            $combo_ids[] = (string) $pid;
            $combo_total += (float) get_post_meta($pid, '_combo_price', true);
        }
    }
    if (empty($combo_ids)) return;
    $billing_email = $order->get_billing_email();
    $billing_phone = $order->get_billing_phone();
    if ($billing_email) $extra['em'] = hash('sha256', strtolower(trim($billing_email)));
    if ($billing_phone) $extra['ph'] = hash('sha256', preg_replace('/[^0-9]/', '', $billing_phone));
    $unique = array_unique($combo_ids);
    $event_id = 'pur_' . $order_id . '_' . time();
    sp_capi_send('Purchase', array(
        'content_type' => 'product_group',
        'content_ids' => $unique,
        'value' => (float) $combo_total,
        'currency' => get_woocommerce_currency(),
        'num_items' => $order->get_item_count(),
    ), $event_id, $extra);
    ?>
<script>
fbq('track', 'Purchase', {
    content_type: 'product_group',
    content_ids: <?php echo json_encode($unique); ?>,
    value: <?php echo (float) $combo_total; ?>,
    currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
    num_items: <?php echo (int) $order->get_item_count(); ?>
});
</script>
    <?php
});

// GA4 ecommerce events (manual)
add_action('wp_footer', function () {
    if (!is_product()) { return; }
    $p = wc_get_product(get_the_ID());
    if (!$p) { return; }
    $pid = $p->get_id();
    ?>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ ecommerce: null });
window.dataLayer.push({
    event: 'view_item',
    ecommerce: {
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
        value: <?php echo (float) $p->get_price(); ?>,
        items: [{
            item_id: '<?php echo esc_js($p->get_sku() ?: $pid); ?>',
            item_name: '<?php echo esc_js($p->get_name()); ?>',
            price: <?php echo (float) $p->get_price(); ?>,
            quantity: 1
        }]
    }
});
</script>
    <?php
});

add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    $pid = $variation_id ?: $product_id;
    $p = wc_get_product($pid);
    if (!$p) { return; }
    ?>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ ecommerce: null });
window.dataLayer.push({
    event: 'add_to_cart',
    ecommerce: {
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
        value: <?php echo (float) ($p->get_price() * $quantity); ?>,
        items: [{
            item_id: '<?php echo esc_js($p->get_sku() ?: $pid); ?>',
            item_name: '<?php echo esc_js($p->get_name()); ?>',
            price: <?php echo (float) $p->get_price(); ?>,
            quantity: <?php echo (int) $quantity; ?>
        }]
    }
});
</script>
    <?php
}, 5, 6);

add_action('woocommerce_before_checkout_form', function () {
    $cart = WC()->cart;
    if (!$cart) { return; }
    $items = array();
    foreach ($cart->get_cart() as $cart_item) {
        $p = $cart_item['data'];
        if (!$p) { continue; }
        $items[] = array(
            'item_id' => $p->get_sku() ?: $cart_item['product_id'],
            'item_name' => $p->get_name(),
            'price' => (float) $p->get_price(),
            'quantity' => $cart_item['quantity'],
        );
    }
    if (empty($items)) { return; }
    ?>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ ecommerce: null });
window.dataLayer.push({
    event: 'begin_checkout',
    ecommerce: {
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>',
        value: <?php echo (float) $cart->get_total('numeric'); ?>,
        items: <?php echo json_encode($items, JSON_UNESCAPED_UNICODE); ?>
    }
});
</script>
    <?php
});

add_action('woocommerce_thankyou', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) { return; }
    $items = array();
    foreach ($order->get_items() as $item) {
        $p = $item->get_product();
        if (!$p) { continue; }
        $items[] = array(
            'item_id' => $p->get_sku() ?: $item->get_product_id(),
            'item_name' => $p->get_name(),
            'price' => (float) $item->get_total(),
            'quantity' => $item->get_quantity(),
        );
    }
    if (empty($items)) { return; }
    ?>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({ ecommerce: null });
window.dataLayer.push({
    event: 'purchase',
    ecommerce: {
        transaction_id: '<?php echo esc_js($order->get_order_number()); ?>',
        value: <?php echo (float) $order->get_total(); ?>,
        currency: '<?php echo esc_js($order->get_currency()); ?>',
        items: <?php echo json_encode($items, JSON_UNESCAPED_UNICODE); ?>
    }
});
</script>
    <?php
});


