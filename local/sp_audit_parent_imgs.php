<?php
$products = wc_get_products(array('type' => 'variable', 'limit' => -1, 'status' => 'publish'));
foreach ($products as $p) {
    $pid = $p->get_id();
    $thumb = get_post_thumbnail_id($pid);
    $gal = array_filter(array_map('intval', explode(',', (string) get_post_meta($pid, '_product_image_gallery', true))));
    $parent_ids = array_filter(array_merge(array($thumb), $gal));
    $var_ids = array();
    foreach ($p->get_children() as $vid) {
        $v = wc_get_product($vid);
        if ($v && $v->get_image_id()) { $var_ids[] = (int) $v->get_image_id(); }
    }
    if (!$var_ids) { continue; }

    $parent_dates = array();
    foreach ($parent_ids as $att) {
        $f = get_attached_file($att);
        if ($f && file_exists($f)) { $parent_dates[] = date('Y-m', filemtime($f)); }
    }
    $var_dates = array();
    foreach ($var_ids as $att) {
        $f = get_attached_file($att);
        if ($f && file_exists($f)) { $var_dates[] = date('Y-m', filemtime($f)); }
    }
    if (!$parent_dates || !$var_dates) { continue; }

    $oldest_var = min($var_dates);
    $stale = array_filter($parent_dates, function ($d) use ($oldest_var) { return $d < $oldest_var; });
    if ($stale) {
        echo "ALERTA $pid | " . $p->get_name() . " | padre: " . implode(',', array_unique($parent_dates))
            . " | vars: " . implode(',', array_unique($var_dates)) . "\n";
        echo "        parent_ids=" . implode(',', $parent_ids) . " | variaciones=" . implode(',', $var_ids) . "\n";
    }
}
echo "fin\n";
