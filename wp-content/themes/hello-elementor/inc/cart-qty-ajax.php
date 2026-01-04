<?php
// AJAX Helper to update quantity by Product ID
add_action('wp_ajax_update_cart_item_qty_by_id', 'hello_update_cart_item_qty_by_id');
add_action('wp_ajax_nopriv_update_cart_item_qty_by_id', 'hello_update_cart_item_qty_by_id');

function hello_update_cart_item_qty_by_id() {
    if(!isset($_POST['product_id']) || !isset($_POST['qty'])) wp_send_json_error();
    
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);
    
    $cart = WC()->cart;
    $cart_id = $cart->generate_cart_id($product_id);
    $item_key = $cart->find_product_in_cart($cart_id);
    
    if($item_key) {
        if($qty <= 0) {
            $cart->remove_cart_item($item_key);
        } else {
            $cart->set_quantity($item_key, $qty);
        }
    } else if($qty > 0) {
        $cart->add_to_cart($product_id, $qty);
    }
    
    $cart->calculate_totals();
    $cart->maybe_set_cart_cookies(); 
    
    // Return standard WC fragments so the mini-cart updates immediately
    if ( class_exists('WC_AJAX') ) {
        \WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_success();
    }
    die();
}
