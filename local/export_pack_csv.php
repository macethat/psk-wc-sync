<?php
global $wpdb;
$packs = array(20464, 20442, 19081, 19071, 11808);
$headers = array("parent_id", "parent_nombre", "variation_id", "sku", "precio", "stock", "presentacion");
$rows = array();
foreach ($packs as $pid) {
    $pname = $wpdb->get_var("SELECT post_title FROM cid_posts WHERE ID={$pid}");
    $presentacion = "";
    if (stripos($pname, 'pack de 12') !== false || stripos($pname, '12 pack') !== false) {
        $presentacion = "Pack 12";
    } elseif (stripos($pname, '500 ml') !== false) {
        $presentacion = "500ml unitario";
    } elseif (stripos($pname, '325 ml') !== false) {
        $presentacion = "325ml Pack 12";
    }
    $vars = $wpdb->get_results("SELECT ID FROM cid_posts WHERE post_type='product_variation' AND post_parent={$pid}");
    foreach ($vars as $v) {
        $sku = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id={$v->ID} AND meta_key='_sku'");
        $price = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id={$v->ID} AND meta_key='_regular_price'");
        $stock = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id={$v->ID} AND meta_key='_stock'");
        $rows[] = array($pid, $pname, $v->ID, $sku, $price, $stock, $presentacion);
    }
}
$csv = fopen("php://output", "w");
fprintf($csv, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
fputcsv($csv, $headers);
foreach ($rows as $r) fputcsv($csv, $r);
fclose($csv);
