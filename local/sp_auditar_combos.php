<?php
/**
 * sp_auditar_combos.php
 * Audita los combos grouped: verifica cache _price (variaciones y padres) y
 * que las fichas no tengan cifras desactualizadas de retail/ahorro.
 * NO modifica _combo_price (regla de oro).
 *
 * Uso:
 *   wp eval-file /tmp/sp_auditar_combos.php                     -> solo auditoria
 *   wp eval-file /tmp/sp_auditar_combos.php -- fix              -> auditoria + fix cache _price
 */
$FIX = isset($GLOBALS['argv']) && is_array($GLOBALS['argv']) && in_array('fix', $GLOBALS['argv'], true);

if ($FIX) {
    global $wpdb;
    $n = $wpdb->query(
        "UPDATE {$wpdb->postmeta} pc
         JOIN {$wpdb->postmeta} pm ON pc.post_id = pm.post_id AND pc.meta_key = '_price'
         LEFT JOIN {$wpdb->postmeta} sl ON sl.post_id = pm.post_id AND sl.meta_key = '_sale_price'
         SET pc.meta_value = pm.meta_value
         WHERE pm.meta_key = '_regular_price'
           AND pc.meta_value != pm.meta_value
           AND (sl.meta_value IS NULL OR sl.meta_value = '')
           AND pm.meta_value IS NOT NULL"
    );
    echo "[fix] variaciones _price corregidas: {$n}\n";

    $rows = $wpdb->get_results(
        "SELECT p.ID, MIN(vm.meta_value) AS min_price
         FROM {$wpdb->posts} p
         JOIN {$wpdb->posts} v ON v.post_parent = p.ID AND v.post_type = 'product_variation' AND v.post_status = 'publish'
         JOIN {$wpdb->postmeta} vm ON vm.post_id = v.ID AND vm.meta_key = '_price'
         JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_price'
         WHERE p.post_type = 'product' AND p.post_status = 'publish'
         GROUP BY p.ID
         HAVING pm.meta_value IS NOT NULL AND pm.meta_value != MIN(vm.meta_value)", ARRAY_A
    );
    if ($rows) {
        foreach ($rows as $r) update_post_meta($r['ID'], '_price', $r['min_price']);
        echo "[fix] padres variables _price corregidos: " . count($rows) . "\n";
    }
}

$combos = array();
$posts = get_posts(array('post_type'=>'product', 'post_status'=>'publish', 'posts_per_page'=>-1, 'fields'=>'ids'));
foreach ($posts as $pid) {
    $p = wc_get_product($pid);
    if ($p && $p->is_type('grouped') && get_post_meta($pid, '_combo_price', true) !== '') $combos[] = $pid;
}
echo "\nTotal combos grouped: " . count($combos) . "\n";

$problemas = array();
foreach ($combos as $cid) {
    $p = wc_get_product($cid);
    $retail = 0.0;
    foreach ($p->get_children() as $ch) { $c = wc_get_product($ch); if ($c) $retail += (float)$c->get_price(); }
    $retail = round($retail, 2);
    $cp = (float)get_post_meta($cid, '_combo_price', true);
    $ahorro = round($retail - $cp, 2);

    $desfase_price = array();
    foreach ($p->get_children() as $ch) {
        $reg = get_post_meta($ch, '_regular_price', true);
        $pri = get_post_meta($ch, '_price', true);
        $sale = get_post_meta($ch, '_sale_price', true);
        if ($reg !== '' && $pri !== '' && !$sale && abs((float)$reg - (float)$pri) > 0.01) $desfase_price[] = $ch;
    }

    $text = get_post_field('post_content', $cid) . "\n@@@\n" . get_post_field('post_excerpt', $cid);
    $text = str_replace(array('&iacute;','&aacute;','&eacute;','&oacute;','&uacute;','&mdash;','&ntilde;'), array('i','a','e','o','u','-','n'), $text);
    $text = preg_replace('/<[^>]+>/', ' ', $text);
    $text = html_entity_decode($text);

    $desvios = array();
    if (preg_match_all('/(por separado|suman|individual|regular|valor)[^.|;]{0,50}\\\$([0-9]+(?:\.[0-9]{2})?)/i', $text, $m)) {
        foreach ($m[2] as $d) { $dv = (float)$d; if (abs($dv - $retail) > 0.50 && abs($dv - $ahorro) > 0.50) $desvios[] = "retail declara \$$d (real \$$retail / ahorro \$$ahorro)"; }
    }
    if (preg_match_all('/(ahorr[ae][^.|;]{0,40})\\\$([0-9]+(?:\.[0-9]{2})?)/i', $text, $m2)) {
        foreach ($m2[2] as $d) { $dv = (float)$d; if (abs($dv - $ahorro) > 0.50) $desvios[] = "ahorro declara \$$d (real \$$ahorro)"; }
    }

    if ($desfase_price || $desvios) {
        $problemas[$cid] = array('nombre'=>$p->get_name(),'retail'=>$retail,'ahorro'=>$ahorro,'combo_price'=>$cp,'desfase_price'=>$desfase_price,'textos'=>$desvios);
    }
}

if (!$problemas) {
    echo "RESULTADO: OK — ninguno de los " . count($combos) . " combos tiene cache _price desfasado ni cifras desactualizadas en la ficha.\n";
} else {
    echo "RESULTADO: " . count($problemas) . " combo(s) requieren atencion:\n";
    foreach ($problemas as $id => $info) {
        echo "\n### $id | {$info['nombre']}\n";
        echo "  retail=\${$info['retail']} ahorro=\${$info['ahorro']} combo_price=\${$info['combo_price']}\n";
        if ($info['desfase_price']) echo "  cache _price desfasado en hijos: " . implode(',', $info['desfase_price']) . "\n";
        foreach ($info['textos'] as $t) echo "  FICHA: $t\n";
    }
    echo "\nPara corregir el cache _price automaticamente: wp eval-file sp_auditar_combos.php -- fix\n";
    echo "Para corregir textos de la ficha: usar el skill combo-sync-ahorro.\n";
}
