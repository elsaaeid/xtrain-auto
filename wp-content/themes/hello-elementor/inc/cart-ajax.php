<?php
/**
 * AJAX handler to save cart address
 */
add_action('wp_ajax_save_cart_address', 'hello_save_cart_address');
add_action('wp_ajax_nopriv_save_cart_address', 'hello_save_cart_address');

add_action('wp_ajax_select_cart_address', 'hello_select_cart_address');
add_action('wp_ajax_nopriv_select_cart_address', 'hello_select_cart_address');

function hello_save_cart_address() {
    check_ajax_referer('save-address-nonce', 'security');

    if ( ! isset($_POST['address']) ) {
        wp_send_json_error();
    }

    $raw_address = sanitize_textarea_field($_POST['address']);
    // Split into lines to support Address 1 and Address 2
    $lines = array_filter(explode("\n", $raw_address));
    $lines = array_values($lines); // Re-index

    $addr1 = isset($lines[0]) ? trim($lines[0]) : '';
    $addr2 = isset($lines[1]) ? trim($lines[1]) : '';

    // Check if WC is loaded
    if ( function_exists('WC') && WC()->customer ) {
        
        // 1. Set as Active Session Address
        WC()->customer->set_shipping_address_1($addr1);
        WC()->customer->set_shipping_address_2($addr2);
        
        WC()->customer->set_billing_address_1($addr1);
        WC()->customer->set_billing_address_2($addr2);
        
        // Ensure Default Country
        if( ! WC()->customer->get_shipping_country() ) {
            $default_country = WC()->countries->get_base_country();
            WC()->customer->set_shipping_country($default_country);
            WC()->customer->set_billing_country($default_country);
        }

        WC()->customer->save();

        // 2. Save to User Meta List (Persistent)
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $saved_addresses = get_user_meta($user_id, 'hello_saved_addresses', true);
            if( ! is_array($saved_addresses) ) $saved_addresses = [];
            
            // Create a storable array for this address
            $new_entry = [
                'addr1' => $addr1,
                'addr2' => $addr2
            ];

            // Check uniqueness loosely
            $exists = false;
            foreach($saved_addresses as $entry) {
                if($entry['addr1'] === $addr1 && $entry['addr2'] === $addr2) {
                    $exists = true; 
                    break;
                }
            }
            
            if(!$exists) {
                // Prepend to top
                array_unshift($saved_addresses, $new_entry);
                update_user_meta($user_id, 'hello_saved_addresses', $saved_addresses);
            }
        }
        
        // Recalc
        $packages = WC()->cart->get_shipping_packages();
        foreach ( $packages as $package_key => $package ) {
            WC()->session->set( 'shipping_for_package_' . $package_key, false );
        }
        
        WC()->cart->calculate_totals();
        WC()->cart->calculate_shipping();
    }

    wp_send_json_success(['message' => 'Address saved successfully']);
}

function hello_select_cart_address() {
    check_ajax_referer('save-address-nonce', 'security');
    
    $index = isset($_POST['address_index']) ? intval($_POST['address_index']) : -1;
    
    if ( $index < 0 || ! is_user_logged_in() ) {
        wp_send_json_error();
    }
    
    $user_id = get_current_user_id();
    $saved_addresses = get_user_meta($user_id, 'hello_saved_addresses', true);
    
    if ( is_array($saved_addresses) && isset($saved_addresses[$index]) ) {
        $entry = $saved_addresses[$index];
        $addr1 = $entry['addr1'];
        $addr2 = $entry['addr2'];
        
        if ( function_exists('WC') && WC()->customer ) {
             WC()->customer->set_shipping_address_1($addr1);
             WC()->customer->set_shipping_address_2($addr2);
             WC()->customer->set_billing_address_1($addr1);
             WC()->customer->set_billing_address_2($addr2);
             
             WC()->customer->save();
             
             // Refresh Calc
             $packages = WC()->cart->get_shipping_packages();
             foreach ( $packages as $k => $p ) { WC()->session->set( 'shipping_for_package_' . $k, false ); }
             WC()->cart->calculate_totals();
             WC()->cart->calculate_shipping();
             
             wp_send_json_success();
        }
    }
    
    wp_send_json_error();
}
