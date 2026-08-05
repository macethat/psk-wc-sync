<?php
global $wpdb;

// Pack products to restore: parent_id => correct_pack_price
// All had price wrongly changed from pack price to unit price
$packs = array(
    20464 => 45.60,   // C4 Performance Energy - 16oz - Pack de 12 (14 vars)
    20442 => 45.60,   // C4 Ultimate Energy - 16oz - Pack de 12 (5 vars)
    19081 => 43.20,   // Amino Energy RTD - Pack (4 vars)
    19071 => 57.60,   // Protein Shake ON - Pack (2 vars)
    11808 => 57.60,   // RTD Carnivor - Pack (4 vars)
);

$total = 0;
foreach ($packs as $parent => $price) {
    $title = $wpdb->get_var("SELECT post_title FROM cid_posts WHERE ID={$parent}");
    $vars = $wpdb->get_col("SELECT ID FROM cid_posts WHERE post_type='product_variation' AND post_parent={$parent}");
    foreach ($vars as $vid) {
        update_post_meta($vid, '_regular_price', $price);
        $total++;
    }
    echo "  Parent {$parent} ({$title}): restored {$price} to {$total} vars\n";
}
echo "Done. Total variations restored: {$total}\n";
