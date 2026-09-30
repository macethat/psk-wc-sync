<?php
$cat = get_term_by('slug', 'descuento-online', 'product_cat');
$cat_id = $cat ? (int) $cat->term_id : 0;
echo "cat_id=$cat_id\n";

// (id, regular, sale)  -- para variables solo se usa 'sale' (cada variacion conserva su regular)
$list = array(
    array(22139, 39.99, 27.99), // Glycerol RAW
    array(22136, 52.99, 47.99), // ISO Surge Mutant
    array(21625, 56.99, 49.99), // ISO 100 Fruity Pebbles
    array(21545, 29.99, 26.99), // BCAA 12:1:1 VMS
    array(21391, 49.99, 34.99), // MASS EXTREME 2500
    array(19871, 59.99, 39.99), // ON Amino Energy 585g
    array(19843, 32.99, 29.99), // ON Amino Energy 270g
    array(19800, 52.99, 45.99), // Nutrex Whey 2 lb
    array(19778, 62.99, 59.99), // ISOFIT Nutrex 30 Serv
    array(19734, 49.99, 39.99), // Lipocide IR Evogen
    array(19430, 54.99, 37.99), // Cell K.E.M. PR v2
    array(19364, 39.99, 27.99), // EVP AQ
    array(19342, 49.99, 35.99), // EVP Xtreme Victory
    array(19304, 49.99, 35.99), // EVP-3D
    array(19253, 49.99, 34.99), // Amino K.E.M.
    array(18887, 32.99, 29.99), // Creatina Stim Pro Impulse
    array(19029, 64.99, 59.99), // RAW Isolatada 2 lb
);

$bak = array(); $ok = 0;
foreach ($list as $it) {
    list($pid, $reg, $sale) = $it;
    $p = wc_get_product($pid);
    if (!$p) { echo "NO EXISTE $pid\n"; continue; }
    $bak[$pid] = array('name' => $p->get_name(), 'type' => $p->get_type(), 'vars' => array());
    if ($p->is_type('variable')) {
        foreach ($p->get_children() as $vid) {
            $v = wc_get_product($vid);
            if (!$v) continue;
            $bak[$pid]['vars'][$vid] = array('reg' => $v->get_regular_price(), 'sale' => $v->get_sale_price());
            $v->set_sale_price($sale);
            $v->save();
        }
        echo "OK var $pid (" . count($p->get_children()) . " variaciones) sale=$sale | " . mb_substr($p->get_name(), 0, 40) . "\n";
    } else {
        $bak[$pid]['regular'] = $p->get_regular_price();
        $bak[$pid]['sale'] = $p->get_sale_price();
        $p->set_regular_price($reg);
        $p->set_sale_price($sale);
        $p->save();
        echo "OK simple $pid reg=$reg sale=$sale\n";
    }
    wp_set_object_terms($pid, array($cat_id), 'product_cat', true);
    $ok++;
}
$f = '/tmp/precios_variables_backup_' . date('Ymd-His') . '.json';
file_put_contents($f, json_encode($bak, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\nAplicados: $ok\nBackup: $f\n";
