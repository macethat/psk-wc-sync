<?php
/**
 * Grouped product add to cart - handles variable children with variation filtering
 */
defined('ABSPATH') || exit;
global $product, $post;

$combo_id = $product->get_id();

// Check availability of all children first
$combo_available = true;
$unavailable_names = array();
$unavailable_ids = array();

foreach ($grouped_products as $check_child) {
    if ($check_child->is_type('variable')) {
        $has_stock = false;
        $allowed_ids_raw = get_post_meta($combo_id, '_combo_variations_' . $check_child->get_id(), true);
        if (!empty($allowed_ids_raw)) {
            $allowed_ids = array_map('intval', explode(',', $allowed_ids_raw));
            foreach ($allowed_ids as $vid) {
                $v = wc_get_product($vid);
                if ($v && combo_child_is_available($v)) { $has_stock = true; break; }
            }
        } else {
            $available = $check_child->get_available_variations();
            $has_stock = !empty($available);
        }
        if (!$has_stock) {
            $combo_available = false;
            $unavailable_names[] = $check_child->get_name();
            $unavailable_ids[] = $check_child->get_id();
        }
    } else {
        if (!combo_child_is_available($check_child)) {
            $combo_available = false;
            $unavailable_names[] = $check_child->get_name();
            $unavailable_ids[] = $check_child->get_id();
        }
    }
}

do_action('woocommerce_before_add_to_cart_form');
?>
<style>
.woocommerce-grouped-product-list-item__price { white-space: nowrap; }

/* Mobile: <= 768px */
@media (max-width: 768px) {
    .woocommerce-grouped-product-list.group_table,
    .woocommerce-grouped-product-list.group_table tbody,
    .woocommerce-grouped-product-list.group_table tr,
    .woocommerce-grouped-product-list.group_table td {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    .woocommerce-grouped-product-list.group_table tr {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .woocommerce-grouped-product-list-item__label {
        padding: 0 !important;
    }

    .woocommerce-grouped-product-list-item__price {
        display: none !important;
    }

    .combo-item-stock-info {
        margin-top: 4px;
        margin-bottom: 8px;
    }

    .combo-mobile-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-top: 8px;
    }

    .combo-mobile-thumb {
        flex: 0 0 70px;
    }

    .combo-mobile-thumb img {
        width: 70px;
        height: auto;
        border-radius: 4px;
    }

    .combo-mobile-price {
        flex: 1;
        text-align: left;
    }
}

/* Desktop: hide mobile-only elements */
@media (min-width: 769px) {
    .combo-mobile-row {
        display: none;
    }
}
</style>
<form class="cart grouped_form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>

    <table cellspacing="0" class="woocommerce-grouped-product-list group_table">
        <tbody>
        <?php
        $previous_post = $post;
        $grouped_product_columns = apply_filters(
            'woocommerce_grouped_product_columns',
            array('label', 'price'),
            $product
        );

        do_action('woocommerce_grouped_product_list_before', $grouped_product_columns, false, $product);

        foreach ($grouped_products as $grouped_product_child) {
            $post_object = get_post($grouped_product_child->get_id());
            $post = $post_object;
            setup_postdata($post);

            $is_available = !in_array($grouped_product_child->get_id(), $unavailable_ids);

            echo '<tr id="product-' . esc_attr($grouped_product_child->get_id()) . '" class="woocommerce-grouped-product-list-item' . (!$is_available ? ' combo-child-unavailable' : '') . '">';

            $allowed_variations = null;
            if ($grouped_product_child->is_type('variable')) {
                $allowed_ids_raw = get_post_meta($combo_id, '_combo_variations_' . $grouped_product_child->get_id(), true);

                if (!empty($allowed_ids_raw)) {
                    $allowed_ids = array_map('intval', explode(',', $allowed_ids_raw));
                    $allowed_variations = array();
                    foreach ($allowed_ids as $vid) {
                        $v = wc_get_product($vid);
                        if ($v && $v->is_purchasable() && combo_child_is_available($v)) {
                            $allowed_variations[] = $v;
                        }
                    }
                } else {
                    $available = $grouped_product_child->get_available_variations();
                    $allowed_variations = array();
                    foreach ($available as $v_data) {
                        $v = wc_get_product($v_data['variation_id']);
                        if ($v && $v->is_purchasable() && combo_child_is_available($v)) {
                            $allowed_variations[] = $v;
                        }
                    }
                }
            }

            foreach ($grouped_product_columns as $column_id) {
                do_action('woocommerce_grouped_product_list_before_' . $column_id, $grouped_product_child);

                switch ($column_id) {
                    case 'label':
                        $thumbnail = $grouped_product_child->get_image(array(70, 70));

                        $value = '<div class="combo-item">';

                        // --- Header: name + variations ---
                        $value .= '<div class="combo-item-header">';
                        $value .= '<label class="combo-product-label">';
                        $value .= $grouped_product_child->is_visible() ? '<a href="' . esc_url(apply_filters('woocommerce_grouped_product_list_link', $grouped_product_child->get_permalink(), $grouped_product_child->get_id())) . '">' . $grouped_product_child->get_name() . '</a>' : $grouped_product_child->get_name();
                        $value .= '</label>';

                        if (!$is_available) {
                            $value .= '<div class="combo-item-stock-info" style="color:#c00;font-size:12px;font-weight:600">Producto no disponible</div>';
                        } elseif ($grouped_product_child->is_type('variable')) {
                            if (!empty($allowed_variations)) {
                                $value .= '<div class="combo-variation-selector" style="margin-top:6px">';
                                if (count($allowed_variations) === 1) {
                                    $single = $allowed_variations[0];
                                    $single_labels = combo_get_variation_labels($single);
                                    $value .= '<span class="combo-variation-fixed" style="font-size:13px;color:#666">' . esc_html($single_labels) . '</span>';
                                    $value .= '<input type="hidden" name="combo_variation_id[' . esc_attr($grouped_product_child->get_id()) . ']" value="' . esc_attr($single->get_id()) . '">';
                                } else {
                                    $value .= '<select name="combo_variation_id[' . esc_attr($grouped_product_child->get_id()) . ']" class="combo-variation-select" data-child_id="' . esc_attr($grouped_product_child->get_id()) . '" style="width:100%;max-width:260px;padding:3px 6px;font-size:13px">';
                                    $value .= '<option value="">Seleccionar</option>';
                                    foreach ($allowed_variations as $v) {
                                        $v_labels = combo_get_variation_labels($v);
                                        $value .= '<option value="' . esc_attr($v->get_id()) . '">' . esc_html($v_labels) . '</option>';
                                    }
                                    $value .= '</select>';
                                }
                                $value .= '</div>';
                            }
                        }
                        $value .= '</div>'; // .combo-item-header

                        // --- Mobile: image + price row ---
                        $mobile_price = '';
                        if (!$is_available) {
                            $mobile_price = '<span style="color:#c00;font-size:13px;white-space:nowrap">Agotado</span>';
                        } elseif ($grouped_product_child->is_type('variable') && !empty($allowed_variations) && count($allowed_variations) === 1) {
                            $mobile_price = $allowed_variations[0]->get_price_html() . wc_get_stock_html($allowed_variations[0]);
                        } else {
                            $mobile_price = $grouped_product_child->get_price_html() . wc_get_stock_html($grouped_product_child);
                        }

                        $value .= '<div class="combo-mobile-row">';
                        $value .= '<div class="combo-mobile-thumb">';
                        if ($grouped_product_child->is_visible()) {
                            $value .= '<a href="' . esc_url(apply_filters('woocommerce_grouped_product_list_link', $grouped_product_child->get_permalink(), $grouped_product_child->get_id())) . '">' . $thumbnail . '</a>';
                        } else {
                            $value .= $thumbnail;
                        }
                        $value .= '</div>'; // .combo-mobile-thumb
                        $value .= '<div class="combo-mobile-price">' . $mobile_price . '</div>';
                        $value .= '</div>'; // .combo-mobile-row

                        $value .= '</div>'; // .combo-item
                        break;

                    case 'price':
                        if (!$is_available) {
                            $value = '<span style="color:#c00;font-size:13px;white-space:nowrap">Agotado</span>';
                        } elseif ($grouped_product_child->is_type('variable') && !empty($allowed_variations) && count($allowed_variations) === 1) {
                            $value = $allowed_variations[0]->get_price_html() . wc_get_stock_html($allowed_variations[0]);
                        } else {
                            $value = $grouped_product_child->get_price_html() . wc_get_stock_html($grouped_product_child);
                        }
                        break;

                    default:
                        $value = '';
                        break;
                }

                echo '<td class="woocommerce-grouped-product-list-item__' . esc_attr($column_id) . '">' . apply_filters('woocommerce_grouped_product_list_column_' . $column_id, $value, $grouped_product_child) . '</td>';

                do_action('woocommerce_grouped_product_list_after_' . $column_id, $grouped_product_child);
            }

            echo '</tr>';
        }
        $post = $previous_post;
        setup_postdata($post);
        do_action('woocommerce_grouped_product_list_after', $grouped_product_columns, $combo_available, $product);
        ?>
        </tbody>
    </table>

    <?php
    if ($combo_available) {
        foreach ($grouped_products as $child) {
            echo '<input type="hidden" name="quantity[' . esc_attr($child->get_id()) . ']" class="combo-qty-child" value="1">';
        }
    }
    ?>

    <div class="woocommerce-grouped-add-to-cart">
        <?php if ($combo_available) : ?>
            <div style="margin-bottom:10px">
                <label for="combo_qty" style="font-weight:600;margin-right:8px">Cantidad de combos:</label>
                <input type="number" name="combo_qty" id="combo_qty" value="1" min="1" step="1" style="width:70px;padding:4px 8px">
            </div>
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>"/>
            <?php do_action('woocommerce_before_add_to_cart_button'); ?>
            <button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
            <?php do_action('woocommerce_after_add_to_cart_button'); ?>
        <?php else : ?>
            <p style="color:#c00;font-weight:600;margin:0">Este combo no está disponible actualmente.</p>
        <?php endif; ?>
    </div>
</form>

<script>
jQuery(document).ready(function($) {
    function checkDisabled() {
        var disabled = false;
        $('.combo-variation-select').each(function() {
            if ($(this).val() === '') disabled = true;
        });
        $('.single_add_to_cart_button').prop('disabled', disabled);
    }

    $('.combo-variation-select').on('change', checkDisabled);
    checkDisabled();
});
</script>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>
