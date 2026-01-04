<?php
/**
 * Cart Count Shortcode
 *
 * Returns the number of items in the shopping cart.
 * Usage: [cart_count]
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wc_cart_items_count() {
    if ( WC()->cart ) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}
add_shortcode('cart_count', 'wc_cart_items_count');
