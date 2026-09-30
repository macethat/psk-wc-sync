<?php
// 1) Crear categoría "Descuento Online"
$term = term_exists('Descuento Online', 'product_cat');
if (!$term) {
    $term = wp_insert_term('Descuento Online', 'product_cat', array('slug' => 'descuento-online'));
    echo "categoria CREADA\n";
} else {
    echo "categoria ya existia\n";
}
$cat_id = is_array($term) ? (int) $term['term_id'] : (int) $term;
echo "cat_id=$cat_id\n\n";

// 2) Productos simples correctos (id, precio_normal, precio_descuento)
$list = array(
    array(22135, 33.99, 24.99), // Fiber Orange LanderFit
    array(21457, 49.99, 24.99), // BUM Esencial
    array(21456, 24.99, 14.99), // ANGRY Creatina 300mg
    array(21455, 29.99, 26.99), // Creatina 80 Serv VMS
    array(21460, 29.99, 26.99), // Glutamine 80 Serv VMS
    array(14414, 32.99, 29.99), // Colágeno Hidrolizado LanderFit Naranja
    array(11875, 35.00, 20.99), // TESTOLANDER
    array(11853, 29.99, 20.99), // Creatina Micronizada Evogen
    array(11830, 39.99, 24.99), // Creatina Micronizada Optimum
    array(9602,  47.99, 24.99), // RAW-ESSENTIAL Orange
    array(9214,  21.99, 12.99), // Vitamina C LanderFit
    array(9200,  25.99, 20.99), // Omega 3 Landerfit
    array(9175,  20.50, 17.99), // LIPO REVOLUTION STIM FREE
    array(9186,  19.99, 17.99), // LIPO REVOLUTION
    array(9190,  21.99, 15.99), // CLA 2000 Landerfit
    array(128,   52.99, 47.99), // Colágeno Doctor Joint Impulse
    array(108,   29.99, 26.99), // Creatina Super ATP Impulse
);

$backup = array();
$ok = 0; $skip = 0;
foreach ($list as $it) {
    list($id, $reg, $sale) = $it;
    $p = wc_get_product($id);
    if (!$p) { echo "NO EXISTE $id\n"; continue; }
    $backup[$id] = array('name' => $p->get_name(), 'type' => $p->get_type(), 'regular' => $p->get_regular_price(), 'sale' => $p->get_sale_price());
    if (!$p->is_type('simple')) {
        echo "SKIP $id (" . $p->get_type() . ")\n"; $skip++;
        continue;
    }
    $p->set_regular_price($reg);
    $p->set_sale_price($sale);
    $p->save();
    wp_set_object_terms($id, array($cat_id), 'product_cat', true); // append (mantiene categorías)
    echo "OK $id: reg=$reg sale=$sale | {$p->get_name()}\n";
    $ok++;
}
$f = '/tmp/precios_backup_' . date('Ymd-His') . '.json';
file_put_contents($f, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\nAplicados: $ok | Omitidos(no simple): $skip\nBackup: $f\n";
