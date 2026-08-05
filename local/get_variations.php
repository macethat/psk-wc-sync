<?php
global $wpdb;
$parent = 18869;
$vars = $wpdb->get_results("SELECT ID, post_title FROM cid_posts WHERE post_type='product_variation' AND post_parent={$parent}");
foreach ($vars as $v) {
    $sku = get_post_meta($v->ID, '_sku', true);
    $price = get_post_meta($v->ID, '_price', true);
    $regular = get_post_meta($v->ID, '_regular_price', true);
    $sale = get_post_meta($v->ID, '_sale_price', true);
    $stock = get_post_meta($v->ID, '_stock', true);
    $status = get_post_meta($v->ID, '_stock_status', true);
    $mgmt = get_post_meta($v->ID, '_manage_stock', true);
    echo "{$v->ID} | {$v->post_title}\n";
    echo "  SKU: {$sku} | Price: {$price} | Regular: {$regular} | Sale: {$sale}\n";
    echo "  Stock: {$stock} | Status: {$status} | Manage: {$mgmt}\n";
}
