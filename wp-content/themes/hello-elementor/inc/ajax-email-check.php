<?php
/**
 * AJAX Handler to check if email exists.
 */
add_action( 'wp_ajax_check_checkout_email_exists', 'hello_check_checkout_email_exists' );
add_action( 'wp_ajax_nopriv_check_checkout_email_exists', 'hello_check_checkout_email_exists' );

function hello_check_checkout_email_exists() {
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    
    if ( empty($email) || ! is_email($email) ) {
        wp_send_json_success(['exists' => false]);
    }

    if ( email_exists($email) ) {
        wp_send_json_success(['exists' => true]);
    } else {
        wp_send_json_success(['exists' => false]);
    }
}
