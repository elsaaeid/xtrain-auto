<?php
/**
 * Shortcode: Custom WooCommerce Cart
 * Usage: [custom_woo_cart]
 */
function custom_woo_cart_shortcode() {
    if ( is_null( WC()->cart ) ) {
        return '';
    }

    ob_start();

    // Ensure style is enqueued when shortcode is used
    wp_enqueue_style( 'hello-elementor-cart' );

    if ( WC()->cart->is_empty() ) {

        wc_get_template( 'cart/cart-empty.php' );
    } else {
        // Force include our custom template
        include HELLO_THEME_PATH . '/woocommerce/cart/cart.php';
    }

    return ob_get_clean();
}
add_shortcode( 'custom_woo_cart', 'custom_woo_cart_shortcode' );
