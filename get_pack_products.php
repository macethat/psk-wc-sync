<?php
global $wpdb;

$groups = [
    'RTD Carnivor 500ml' => ['891597004034', '891597006137', '891597006427', '891597006113'],
    'Batido Proteinas 325ml 12 Pack' => ['748927066593', '748927069815', '748927066586'],
    'ON Amino Energy Pack 12' => ['748927060621', '748927062731', '748927060607', '748927062748', '748927060614', '748927068269', '748927063486'],
    'C4 Ultimate Pack 12' => ['842595131741', '842595135541', '842595131727', '842595135534', '842595135558'],
    'C4 Performance Pack 12' => ['842595111071', '842595106572', '842595131291', '842595131628', '842595134957', '842595131253', '842595121766', '842595121759', '842595134971', '842595106596', '842595131277', '842595133608', '842595111095', '842595134964'],
];

foreach ($groups as $gname => $gskus) {
    $sku_list = "'" . implode("','", $gskus) . "'";
    $rows = $wpdb->get_results(
        "SELECT p.ID, p.post_title, p.post_parent, pm.meta_value as sku
         FROM {$wpdb->posts} p
         JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
         WHERE pm.meta_key = '_sku' AND pm.meta_value IN ($sku_list)"
    );
    echo "\n=== $gname ===\n";
    if (!$rows) {
        echo "  (no products found)\n";
        continue;
    }
    $parent_ids = [];
    foreach ($rows as $r) {
        if ($r->post_parent > 0) {
            $parent_ids[$r->post_parent] = true;
        }
    }
    if (!empty($parent_ids)) {
        foreach ($parent_ids as $pid => $v) {
            $parent = get_post($pid);
            if ($parent) {
                echo "  {$parent->post_title}\n";
            }
        }
    } else {
        foreach ($rows as $r) {
            echo "  {$r->post_title} (SKU: {$r->sku})\n";
        }
    }
}
