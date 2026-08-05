<?php
add_action('wp_footer', function () {
    echo '<!-- GA4_VIEW_ITEM_DEBUG_START -->';
    if (!is_product()) { echo '<!-- NOT_PRODUCT -->'; return; }
    echo '<!-- IS_PRODUCT -->';
    $p = wc_get_product(get_the_ID());
    if (!$p) { echo '<!-- NO_PRODUCT -->'; return; }
    echo '<!-- HAS_PRODUCT:' . $p->get_id() . ' -->';
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
    echo '<!-- GA4_VIEW_ITEM_DEBUG_END -->';
}, 999);
