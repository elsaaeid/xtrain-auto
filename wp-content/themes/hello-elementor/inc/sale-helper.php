<?php
// Helper: Check if there are any WooCommerce products on sale
function has_sale_products() {
    if ( ! class_exists( 'WooCommerce' ) ) return false;
    $sale_products = wc_get_product_ids_on_sale();
    return !empty($sale_products);
}
