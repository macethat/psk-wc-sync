<?php
$products = array();
$args = array('post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'fields' => 'ids');
foreach (get_posts($args) as $id) {
    $p = wc_get_product($id);
    if (!$p) continue;
    $products[] = array(
        'id'            => $id,
        'sku'           => $p->get_sku(),
        'type'          => $p->get_type(),
        'parent'        => 0,
        'name'          => $p->get_name(),
        'stock_qty'     => $p->get_stock_quantity(),
        'stock_st'      => $p->get_stock_status(),
        'manage'        => $p->get_manage_stock(),
        'regular_price' => $p->get_regular_price(),
        'sale_price'    => $p->get_sale_price(),
    );
    if ($p->is_type('variable')) {
        foreach ($p->get_available_variations('objects') as $v) {
            $products[] = array(
                'id'            => $v->get_id(),
                'sku'           => $v->get_sku(),
                'type'          => 'variation',
                'parent'        => $id,
                'name'          => $v->get_name(),
                'stock_qty'     => $v->get_stock_quantity(),
                'stock_st'      => $v->get_stock_status(),
                'manage'        => $v->get_manage_stock(),
                'regular_price' => $v->get_regular_price(),
                'sale_price'    => $v->get_sale_price(),
            );
        }
    }
}
echo json_encode($products, JSON_UNESCAPED_UNICODE);
