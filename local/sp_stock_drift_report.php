<?php
/**
 * Reporte de desfase de stock: PSKloud (AHORA) vs WooCommerce (AHORA), por SKU y sucursal.
 * Solo lee y reporta (no modifica nada). Uso: wp eval-file sp_stock_drift_report.php
 */

$PSK_HOST = 'https://adm.premium-soft.com';
$PSK_KEY  = 'BQxQrt5/FwARtlVUwT0GFw==';
$PSK_PIN  = '46558';
$SUCS     = array(1, 5, 6, 7, 8, 10);

function sp_drift_fetch($url, $key) {
    $r = wp_remote_get($url, array('timeout' => 60, 'headers' => array('clave-api-business' => $key)));
    if (is_wp_error($r)) {
        return null;
    }
    $code = wp_remote_retrieve_response_code($r);
    if ($code >= 400) {
        return null;
    }
    return json_decode(wp_remote_retrieve_body($r), true);
}

$arts = sp_drift_fetch($PSK_HOST . '/Api/Articulos?pin=' . $PSK_PIN . '&pagina=0&cant_pagina=99999&precios=1', $PSK_KEY);
$ex   = sp_drift_fetch($PSK_HOST . '/Api/Existencias?pin=' . $PSK_PIN, $PSK_KEY);
if (!is_array($arts) || !is_array($ex)) {
    echo "ERROR: no se pudo consultar PSKloud\n";
    return;
}

// codigo por id_articulo
$code_by_id = array();
foreach ($arts as $a) {
    if (!empty($a['id_articulo']) && isset($a['codigo'])) {
        $code_by_id[(string) $a['id_articulo']] = trim((string) $a['codigo']);
    }
}

// PSKloud: codigo => almacen => qty
$psk = array();
foreach ($ex as $e) {
    $id = (string) ($e['id_articulo'] ?? '');
    if (!isset($code_by_id[$id])) {
        continue;
    }
    $code = $code_by_id[$id];
    $alm  = (string) ($e['id_almacen'] ?? '');
    if (!in_array((int) $alm, $SUCS, true)) {
        continue;
    }
    $q = $e['existencia'] ?? ($e['existencias'] ?? 0);
    $psk[$code][(int) $alm] = (int) floatval($q);
}

// Woo: recorrer productos publicados + variaciones con SKU
$rows = array();
$seen = array();
$products = wc_get_products(array('limit' => -1, 'status' => 'publish', 'return' => 'objects'));
foreach ($products as $p) {
    // Excluir combos (grouped): su stock se deriva de los hijos
    if ($p->is_type('grouped')) {
        continue;
    }
    $ids = array($p->get_id());
    foreach ($p->get_children() as $cid) {
        $ids[] = $cid;
    }
    foreach ($ids as $iid) {
        $sku = (string) get_post_meta($iid, '_sku', true);
        if ($sku === '' || !isset($psk[$sku])) {
            continue;
        }
        // Excluir packs/bebidas (no se manejan como pack en PSKloud)
        if (preg_match('/^(842595|891597|748927)/', $sku)) {
            continue;
        }
        foreach ($SUCS as $s) {
            $dedupe = $iid . '-' . $s;
            if (isset($seen[$dedupe])) {
                continue;
            }
            $seen[$dedupe] = 1;
            $woo_raw = get_post_meta($iid, '_sucursal_' . $s . '_stock', true);
            $woo = ($woo_raw === '') ? 0 : (int) $woo_raw;
            $inv = isset($psk[$sku][$s]) ? (int) $psk[$sku][$s] : 0;
            if ($woo !== $inv) {
                $rows[] = array(
                    'sku'  => $sku,
                    'id'   => $iid,
                    'suc'  => $s,
                    'woo'  => $woo,
                    'psk'  => $inv,
                    'diff' => $inv - $woo,
                    'name' => $p->get_name(),
                );
            }
        }
    }
}

// Escribir CSV en psk-sync/drift/
$psk_dir = dirname(dirname(WP_CONTENT_DIR)) . '/psk-sync';
$dir = $psk_dir . '/drift';
if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
}
$file = $dir . '/stock_drift_' . date('Y-m-d_Hi') . '.csv';
$fh = @fopen($file, 'w');
if ($fh) {
    fputcsv($fh, array('sku', 'id', 'sucursal', 'woo', 'pskloud', 'diff', 'nombre'));
    foreach ($rows as $r) {
        fputcsv($fh, array($r['sku'], $r['id'], $r['suc'], $r['woo'], $r['psk'], $r['diff'], $r['name']));
    }
    fclose($fh);
}

// Resumen en log
$log = $psk_dir . '/drift.log';
$line = '[' . date('Y-m-d H:i:s') . '] desfases=' . count($rows) . ' archivo=' . $file . "\n";
@file_put_contents($log, $line, FILE_APPEND);

echo "Desfases detectados: " . count($rows) . "\n";
echo "Archivo: $file\n";
foreach (array_slice($rows, 0, 20) as $r) {
    echo "  sku={$r['sku']} id={$r['id']} suc={$r['suc']} woo={$r['woo']} psk={$r['psk']} diff={$r['diff']}\n";
}
