<?php
/**
 * Plugin Name: Precio para productos agrupados
 * Description: Agrega campo de precio personalizado y selección de variaciones a productos agrupados
 */

function combo_child_is_available($product) {
    if (!$product) return false;
    if ($product->managing_stock() && $product->get_stock_quantity() !== null && $product->get_stock_quantity() <= 6) return false;
    return $product->is_in_stock();
}

function combo_price_metabox() {
    add_meta_box('combo_price_box', 'Configuración del Combo', 'combo_price_metabox_cb', 'product', 'side', 'default');
}

function combo_price_metabox_cb($post) {
    $product = wc_get_product($post->ID);
    $children = $product ? $product->get_children() : array();
    $combo_price = get_post_meta($post->ID, '_combo_price', true);

    wp_nonce_field('combo_price_save', 'combo_price_nonce');

    echo '<p><label>Precio fijo del combo (B/.): </label>';
    echo '<input type="number" step="0.01" min="0" name="_combo_price" value="' . esc_attr($combo_price) . '" style="width:150px"></p>';

    echo '<p><strong>Variaciones permitidas:</strong></p>';
    echo '<p style="font-size:11px;color:#666;margin-top:-8px">Selecciona las variaciones que aplican al combo. Vacío = todas disponibles.</p>';

    // Up to 3 products
    for ($i = 0; $i < 3; $i++) {
        if (!isset($children[$i])) {
            echo '<div style="padding:6px;margin-bottom:6px;background:#f9f9f9;border:1px solid #ddd;border-radius:3px">';
            echo '<strong style="font-size:12px">Producto ' . ($i + 1) . ':</strong> <span style="color:#999;font-size:11px">(sin producto asociado)</span>';
            echo '</div>';
            continue;
        }

        $child_id = $children[$i];
        $child = wc_get_product($child_id);

        if (!$child) continue;

        echo '<div style="padding:6px;margin-bottom:6px;background:#f9f9f9;border:1px solid #ddd;border-radius:3px">';
        echo '<strong style="font-size:12px;display:block;margin-bottom:3px">Producto ' . ($i + 1) . ': ' . esc_html(wp_trim_words($child->get_name(), 8)) . '</strong>';

        if ($child->is_type('variable')) {
            $saved = get_post_meta($post->ID, '_combo_variations_' . $child_id, true);
            $saved_ids = !empty($saved) ? array_map('intval', explode(',', $saved)) : array();

            $variations = $child->get_available_variations();

            echo '<select name="_combo_variations_' . esc_attr($child_id) . '[]" multiple style="width:100%;min-height:50px;font-size:12px">';
            foreach ($variations as $v) {
                $var_obj = wc_get_product($v['variation_id']);
                if (!$var_obj) continue;
                $selected = in_array($v['variation_id'], $saved_ids) ? 'selected' : '';
                echo '<option value="' . esc_attr($v['variation_id']) . '" ' . $selected . '>' . esc_html($var_obj->get_attribute_summary()) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<span style="color:#999;font-size:11px">Producto simple (sin variaciones)</span>';
        }

        echo '</div>';
    }
}

function combo_price_save($post_id) {
    if (!isset($_POST['combo_price_nonce']) || !wp_verify_nonce($_POST['combo_price_nonce'], 'combo_price_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_product', $post_id)) return;

    // Save price
    if (isset($_POST['_combo_price']) && $_POST['_combo_price'] !== '') {
        update_post_meta($post_id, '_combo_price', wc_clean($_POST['_combo_price']));
    } else {
        delete_post_meta($post_id, '_combo_price');
    }

    // Remove old meta
    delete_post_meta($post_id, '_combo_allowed_attrs');

    // Save variations per child
    $product = wc_get_product($post_id);
    $children = $product ? $product->get_children() : array();

    foreach ($children as $child_id) {
        $child = wc_get_product($child_id);
        if ($child && $child->is_type('variable')) {
            $meta_key = '_combo_variations_' . $child_id;
            $field_name = '_combo_variations_' . $child_id;

            if (isset($_POST[$field_name]) && is_array($_POST[$field_name])) {
                $ids = array_map('intval', $_POST[$field_name]);
                $ids = array_filter($ids);
                if (!empty($ids)) {
                    update_post_meta($post_id, $meta_key, implode(',', $ids));
                } else {
                    delete_post_meta($post_id, $meta_key);
                }
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }
}

function combo_get_children_total($product) {
    $total = 0;
    foreach ($product->get_children() as $cid) {
        $child = wc_get_product($cid);
        if (!$child) continue;
        if ($child->is_type('variable')) {
            $prices = $child->get_variation_prices();
            $min = !empty($prices['regular_price']) ? min($prices['regular_price']) : 0;
            $total += (float)$min;
        } else {
            $total += (float)$child->get_regular_price();
        }
    }
    return $total;
}

function combo_price_frontend($price, $product) {
    $combo_price = get_post_meta($product->get_id(), '_combo_price', true);
    if ($combo_price !== '' && $combo_price !== false) {
        $cp = (float)$combo_price;
        $retail = combo_get_children_total($product);
        $html = wc_price($cp);
        if ($retail > $cp) {
            $savings = $retail - $cp;
            $html .= ' <span style="color:#c00;font-size:0.8em;font-weight:600;margin-left:12px">Ahorra ' . wc_price($savings) . '</span>';
        }
        return $html;
    }
    return $price;
}

add_action('add_meta_boxes', 'combo_price_metabox');
add_action('save_post_product', 'combo_price_save');
add_filter('woocommerce_grouped_price_html', 'combo_price_frontend', 10, 2);

// --- AJAX: obtener variación por atributos ---
add_action('wp_ajax_get_variation_by_attributes', 'ajax_get_variation_by_attributes');
add_action('wp_ajax_nopriv_get_variation_by_attributes', 'ajax_get_variation_by_attributes');
function ajax_get_variation_by_attributes() {
    $product_id = intval($_POST['product_id']);
    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
    $product = wc_get_product($product_id);
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('No es un producto variable');
    }
    $variation_id = 0;
    foreach ($product->get_children() as $cid) {
        $v = wc_get_product($cid);
        if (!$v) continue;
        $match = true;
        foreach ($attributes as $attr_name => $attr_value) {
            $v_attrs = $v->get_attributes();
            if (!isset($v_attrs[$attr_name]) || (string)$v_attrs[$attr_name] !== (string)$attr_value) {
                $match = false;
                break;
            }
        }
        if ($match) { $variation_id = $cid; break; }
    }
    if ($variation_id) {
        $variation = wc_get_product($variation_id);
        wp_send_json_success(array(
            'variation_id' => $variation_id,
            'price_html' => $variation->get_price_html(),
            'is_in_stock' => $variation->is_in_stock(),
            'max_qty' => $variation->get_max_purchase_quantity(),
        ));
    } else {
        wp_send_json_error('No se encontró variación');
    }
}

// --- Forzar grouped sin stock management propio ---
add_filter('woocommerce_is_purchasable', function($purchasable, $product) {
    if ($product->is_type('grouped')) {
        $combo_price = get_post_meta($product->get_id(), '_combo_price', true);
        if ($combo_price !== '' && $combo_price !== false) {
            return true;
        }
    }
    return $purchasable;
}, 10, 2);
add_filter('woocommerce_product_get_manage_stock', function($manage, $product) {
    if ($product->is_type('grouped')) return 'no';
    return $manage;
}, 10, 2);
add_filter('woocommerce_product_get_stock_status', function($status, $product) {
    if (!$product->is_type('grouped')) return $status;
    $children = $product->get_children();
    if (empty($children)) return 'outofstock';
    foreach ($children as $cid) {
        $child = wc_get_product($cid);
        if (!$child) continue;
        if ($child->is_type('variable')) {
            $vars = $child->get_children();
            $has_stock = false;
            foreach ($vars as $vid) {
                $v = wc_get_product($vid);
                if ($v && combo_child_is_available($v)) { $has_stock = true; break; }
            }
            if (!$has_stock) return 'outofstock';
        } else {
            if (!combo_child_is_available($child)) return 'outofstock';
        }
    }
    return 'instock';
}, 10, 2);
add_filter('woocommerce_product_get_stock_quantity', function($qty, $product) {
    if ($product->is_type('grouped')) {
        $children = $product->get_children();
        foreach ($children as $cid) {
            $child = wc_get_product($cid);
            if (!$child) continue;
            if ($child->is_type('variable')) {
                $vars = $child->get_children();
                $has_stock = false;
                foreach ($vars as $vid) {
                    $v = wc_get_product($vid);
                    if ($v && combo_child_is_available($v)) { $has_stock = true; break; }
                }
                if (!$has_stock) return 0;
            } else {
                if (!combo_child_is_available($child)) return 0;
            }
        }
        return 9999;
    }
    return $qty;
}, 10, 2);

// --- Agregar combo al carrito ---
function combo_add_to_cart_handler($product_id, $qty) {
    $product = wc_get_product($product_id);
    if (!$product) return false;

    $combo_qty = max(1, intval($_POST['combo_qty'] ?? $qty));
    // Read from combo_variation_id (new template) or variation_id (backward compat)
    $variation_ids = isset($_POST['combo_variation_id']) && is_array($_POST['combo_variation_id'])
        ? $_POST['combo_variation_id']
        : (isset($_POST['variation_id']) ? $_POST['variation_id'] : array());

    // First pass: verify ALL children are available
    $all_available = true;
    foreach ($product->get_children() as $child_id) {
        $child = wc_get_product($child_id);
        if (!$child) { $all_available = false; break; }
        if ($child->is_type('variable')) {
            $allowed_ids_raw = get_post_meta($product_id, '_combo_variations_' . $child_id, true);
            if (!empty($allowed_ids_raw)) {
                $allowed_ids = array_map('intval', explode(',', $allowed_ids_raw));
                $has_variation_stock = false;
                foreach ($allowed_ids as $vid) {
                    $v = wc_get_product($vid);
                    if ($v && combo_child_is_available($v)) { $has_variation_stock = true; break; }
                }
                if (!$has_variation_stock) { $all_available = false; break; }
            } else {
                $available = $child->get_available_variations();
                if (empty($available)) { $all_available = false; break; }
            }
        } else {
            if (!combo_child_is_available($child)) { $all_available = false; break; }
        }
    }

    if (!$all_available) {
        wc_add_notice('Uno o m&aacute;s productos del combo no est&aacute;n disponibles.', 'error');
        return false;
    }

    $combo_items = array();
    foreach ($product->get_children() as $child_id) {
        $child = wc_get_product($child_id);
        if (!$child) continue;
        if ($child->is_type('variable')) {
            $vid = isset($variation_ids[$child_id]) ? intval($variation_ids[$child_id]) : 0;
            if ($vid) {
                $v = wc_get_product($vid);
                if ($v && combo_child_is_available($v)) {
                    $combo_items[] = array(
                        'product_id' => $child_id,
                        'variation_id' => $vid,
                        'name' => $v->get_name(),
                        'sku' => $v->get_sku(),
                    );
                }
            }
        } else {
            $combo_items[] = array(
                'product_id' => $child_id,
                'variation_id' => 0,
                'name' => $child->get_name(),
                'sku' => $child->get_sku(),
            );
        }
    }
    if (empty($combo_items)) return false;

    $cart_item_data = array('combo_children' => $combo_items);
    $cart_id = WC()->cart->generate_cart_id($product_id, 0, array(), $cart_item_data);
    $cart_item_key = WC()->cart->find_product_in_cart($cart_id);

    if ($cart_item_key) {
        WC()->cart->cart_contents[$cart_item_key]['quantity'] += $combo_qty;
    } else {
        $p = wc_get_product($product_id);
        WC()->cart->cart_contents[$cart_id] = apply_filters('woocommerce_add_cart_item', array(
            'key'            => $cart_id,
            'product_id'     => $product_id,
            'variation_id'   => 0,
            'variation'      => array(),
            'quantity'       => $combo_qty,
            'data'           => $p,
            'data_hash'      => wc_get_cart_item_data_hash($p),
            'combo_children' => $combo_items,
        ), $cart_id);
    }
    WC()->session->set('cart', WC()->cart->get_cart_for_session());
    return true;
}

add_filter('woocommerce_add_to_cart_handler', function($handler, $product) {
    if ($product->is_type('grouped')) {
        $combo_price = get_post_meta($product->get_id(), '_combo_price', true);
        if ($combo_price !== '' && $combo_price !== false) return 'combo';
    }
    return $handler;
}, 10, 2);

add_action('woocommerce_add_to_cart_handler_combo', function($url) {
    $product_id = absint($_REQUEST['add-to-cart'] ?? 0);
    if (combo_add_to_cart_handler($product_id, 1)) {
        $qty = max(1, intval($_POST['combo_qty'] ?? 1));
        wc_add_to_cart_message(array($product_id => $qty), true);
    }
    wp_safe_redirect(wc_get_cart_url());
    exit;
});

// Interceptar add-to-cart vía AJAX
add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $qty) {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_type('grouped')) {
            $combo_price = get_post_meta($product_id, '_combo_price', true);
            if ($combo_price !== '' && $combo_price !== false && isset($_POST['combo_qty'])) {
                if (combo_add_to_cart_handler($product_id, $qty)) {
                    do_action('woocommerce_ajax_added_to_cart', $product_id);
                    do_action('internal_woocommerce_cart_item_added_from_user_request', $product_id, max(1, intval($_POST['combo_qty'])));
                    WC_AJAX::get_refreshed_fragments();
                    exit;
                }
            }
        }
    }
    return $passed;
}, 5, 3);

// Fijar precio del combo en el carrito
add_action('woocommerce_before_calculate_totals', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    foreach ($cart->get_cart() as $item) {
        if (!empty($item['combo_children'])) {
            $price = get_post_meta($item['product_id'], '_combo_price', true);
            if ($price !== '' && $price !== false) {
                $item['data']->set_price((float)$price);
            }
        }
    }
});

// Guardar componentes en el item del pedido
add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_key, $values) {
    if (!empty($values['combo_children'])) {
        $item->add_meta_data('_combo_children', $values['combo_children']);
        $item->add_meta_data('_combo_parent', $values['product_id']);
    }
}, 10, 3);

// Descontar stock de los componentes al confirmar el pedido
add_action('woocommerce_checkout_order_processed', function($order_id, $posted_data, $order) {
    foreach ($order->get_items() as $item) {
        $children = $item->get_meta('_combo_children');
        if (!$children) continue;
        $qty = $item->get_quantity();
        foreach ($children as $child) {
            $pid = !empty($child['variation_id']) ? $child['variation_id'] : $child['product_id'];
            $p = wc_get_product($pid);
            if ($p && $p->managing_stock()) {
                wc_update_product_stock($p, $qty, 'decrease');
            }
        }
    }
}, 10, 3);

// Ocultar boton Compare en productos agrupados con combo
add_action('wp_head', function() {
    if (is_product()) {
        global $product;
        if ($product && $product->is_type('grouped') && get_post_meta($product->get_id(), '_combo_price', true) !== '') {
            echo '<style>.woosc-btn{display:none!important}</style>';
        }
    }
});
