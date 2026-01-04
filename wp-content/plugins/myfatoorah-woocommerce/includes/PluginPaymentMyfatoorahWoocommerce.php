<?php

class PluginPaymentMyfatoorahWoocommerce {

    //-----------------------------------------------------------------------------------------------------------------------------

    private $code;
    private $plugin;
    private $txtOrderNotFound;

    /**
     * Constructor
     */
    public function __construct($code, $plugin = MYFATOORAH_WOO_PLUGIN) {

        $this->code   = $code;
        $this->plugin = $plugin;

        $this->txtOrderNotFound = __('The Order is not found. Please, contact the store admin.', 'myfatoorah-woocommerce');

        //filters
        add_filter('woocommerce_payment_gateways', array($this, 'register'), 0);
        add_filter('myfatoorah_woocommerce_payment_gateways', [$this, 'myfatoorah_woocommerce_payment_gateways']);

        add_filter('plugin_action_links_' . $this->plugin, array($this, 'plugin_action_links'));
        add_filter('wc_get_price_decimals', array($this, 'wc_get_price_decimals'), 99);
        add_action('woocommerce_api_myfatoorah_process', array($this, 'initLoader'));
        add_action('woocommerce_api_myfatoorah_complete', array($this, 'getPaymentStatus'));
    }

    //-----------------------------------------------------------------------------------------------------------------------------
    public function wc_get_price_decimals($decimals) {
        $shippingOptions = get_option('woocommerce_myfatoorah_shipping_settings');
        if (!isset($shippingOptions['enabled']) || $shippingOptions['enabled'] == 'no' || wc_get_page_id('checkout') <= 0) {
            return $decimals;
        }

        $chosen_methods = filter_input(INPUT_POST, 'shipping_method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (isset($chosen_methods[0])) {
            if ($chosen_methods[0] == 'myfatoorah_shipping:1' || $chosen_methods[0] == 'myfatoorah_shipping:2') {
                return 3;
            }
        } else {
            //select diff country with no other shipping methods
            $payment_method = MyFatoorah::filterInputField('payment_method', 'POST');
            if ($payment_method && str_contains($payment_method, 'myfatoorah_')) {
                return 3;
            }
        }
        return $decimals;
    }

    //-----------------------------------------------------------------------------------------------------------------------------
    public function myfatoorah_woocommerce_payment_gateways($gateways) {

        $gateways[$this->code] = __(ucwords($this->code), dirname($this->plugin));
        return $gateways;
    }

    //-----------------------------------------------------------------------------------------------------------------------------

    /**
     * Register the gateway to WooCommerce
     */
    public function register($gateways) {

        $path = WP_PLUGIN_DIR . '/' . dirname($this->plugin);
        include_once("$path/includes/payments/class-wc-gateway-myfatoorah-$this->code.php");

        $gateways[] = 'WC_Gateway_Myfatoorah_' . $this->code;
        return $gateways;
    }

    //-----------------------------------------------------------------------------------------------------------------------------

    /**
     * Show action links on the plugin screen.
     * Action link will redirect to the payment settings page
     * http://wordpress-5.4.2.com/wp-admin/admin.php?page=wc-settings&tab=checkout&section=myfatoorah_$code
     *
     * @param mixed $links Plugin Action links.
     *
     * @return array
     */
    public function plugin_action_links($links) {

        $gateways = apply_filters('myfatoorah_woocommerce_payment_gateways', []);
        if (!isset($gateways[$this->code])) {
            return $links;
        }

        $newlink = ['myfatoorah_' . $this->code => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_myfatoorah_' . $this->code) . '">' . $gateways[$this->code] . '</a>'];
        return array_merge($links, $newlink);
    }

    //-----------------------------------------------------------------------------------------------------------------------------
    public function getPaymentStatus() {

        // Direct retrieval - removing base64_decode
        $orderId = MyFatoorah::filterInputField('oid');
        
        // Fallback for 'oid'
        if ( empty($orderId) ) {
            if ( isset($_GET['oid']) ) {
                $orderId = sanitize_text_field($_GET['oid']);
            } elseif ( isset($_GET['amp;oid']) ) {
                 $orderId = sanitize_text_field($_GET['amp;oid']);
            }
        }
        
        // Debug Logging
        if ( defined('WP_DEBUG') && WP_DEBUG ) {
            error_log( "MyFatoorah Debug: OID param: " . MyFatoorah::filterInputField('oid') );
            error_log( "MyFatoorah Debug: Decoded Order ID: " . $orderId );
        }

        $order         = wc_get_order($orderId);
        
        if (!$order) {
            error_log( "MyFatoorah Error: Order object not found for ID: " . $orderId );
            wp_die($this->txtOrderNotFound . " (ID: $orderId)"); // Show ID for debug
        }
        
        $paymentMethod = $order->get_payment_method();
        //Fix: Ensure payment method class exists logic 
        //(Sometimes method is myfatoorah_v2 but class expected is WC_Gateway_Myfatoorah_v2 which is correct, 
        //but let's log it).

        //get Payment Id
        $KeyType = 'PaymentId';
        $key     = preg_replace('/[^0-9]/', '', MyFatoorah::filterInputField('paymentId')); //To avoid other strings for app as example
        
        // Fallback for 'paymentId'
        if ( empty($key) ) {
            if ( isset($_GET['paymentId']) ) {
                $key = preg_replace('/[^0-9]/', '', $_GET['paymentId']);
            } elseif ( isset($_GET['amp;paymentId']) ) {
                $key = preg_replace('/[^0-9]/', '', $_GET['amp;paymentId']);
            } elseif ( isset($_GET['id']) ) {
                $key = preg_replace('/[^0-9]/', '', $_GET['id']);
            } elseif ( isset($_GET['amp;id']) ) {
                $key = preg_replace('/[^0-9]/', '', $_GET['amp;id']);
            }
        }
        if (!$key) {
            error_log( "MyFatoorah Error: No payment ID found." );
            wp_die($this->txtOrderNotFound . " (No Payment ID)");
        }
        
        $this->validateCallback($orderId, $key, $paymentMethod);

        //get MyFatoorah object
        $calss   = 'WC_Gateway_' . ucfirst($paymentMethod);
        $gateway = new $calss;

        try {
            $error = $gateway->checkStatus($key, $KeyType, $order);
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
        if ($error) {
            $this->redirectToFailURL($gateway, $order, $error, MyFatoorah::filterInputField('pay_for_order'));
            exit();
        }

        $this->redirectToSuccessURL($gateway, $order, $orderId);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    public function validateCallback($orderId, $key, $paymentMethod) {
        if (!$orderId) {
            wp_die($this->txtOrderNotFound);
        }

        if (!$key) {
            wp_die($this->txtOrderNotFound);
        }

        if (!str_contains($paymentMethod, 'myfatoorah_')) {
            wp_die($this->txtOrderNotFound);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    public function redirectToFailURL($gateway, $order, $error, $isPayForOrderPage) {
        if ($gateway->fail_url) {
            wp_redirect($gateway->fail_url . '?error=' . urlencode($error));
        } else {
            $trError = __($error, 'myfatoorah-woocommerce');
            wc_add_notice($trError, 'error');
            if ($isPayForOrderPage == 'true') {
                wp_redirect($order->get_checkout_payment_url());
            } else if (WC_Blocks_Utils::has_block_in_page(wc_get_page_id('checkout'), 'woocommerce/checkout')) {
                wp_redirect(wc_get_cart_url());
            } else {
                wp_redirect(wc_get_checkout_url());
            }
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    public function redirectToSuccessURL($gateway, $order, $orderId) {
        if ($gateway->success_url) {
            wp_redirect($gateway->success_url . '/' . $orderId . '/?key=' . $order->get_order_key());
        } else {
            //When "thankyou" order-received page is reached …
            wp_redirect($order->get_checkout_order_received_url());
        }
        exit();
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------
    public function initLoader() {
        // Debugging: Log raw GET params
        if ( defined('WP_DEBUG') && WP_DEBUG ) {
            error_log("MyFatoorah initLoader GET: " . print_r($_GET, true));
        }

        // Direct retrieval - removing base64_decode to simplify debugging
        $orderId = MyFatoorah::filterInputField('oid');
        
        // Fallback for 'oid'
        if ( empty($orderId) ) {
            if ( isset($_GET['oid']) ) {
                $orderId = sanitize_text_field($_GET['oid']);
            } elseif ( isset($_GET['amp;oid']) ) {
                 $orderId = sanitize_text_field($_GET['amp;oid']);
            }
        }

        $paymentId = preg_replace('/[^0-9]/', '', MyFatoorah::filterInputField('paymentId'));
        // Fallback for 'paymentId'
        if ( empty($paymentId) ) {
            if ( isset($_GET['paymentId']) ) {
                $paymentId = preg_replace('/[^0-9]/', '', $_GET['paymentId']);
            } elseif ( isset($_GET['amp;paymentId']) ) {
                $paymentId = preg_replace('/[^0-9]/', '', $_GET['amp;paymentId']);
            } elseif ( isset($_GET['id']) ) { // Sometimes passed as 'id'
                $paymentId = preg_replace('/[^0-9]/', '', $_GET['id']);
            } elseif ( isset($_GET['amp;id']) ) {
                $paymentId = preg_replace('/[^0-9]/', '', $_GET['amp;id']);
            }
        }

        if (!$orderId || !$paymentId) {
            $debugMsg = " (Loader Failed - OID: " . ($orderId ? $orderId : 'MISSING') . ", PID: " . ($paymentId ? $paymentId : 'MISSING') . ")";
            wp_die($this->txtOrderNotFound . $debugMsg);
        }

        $args = [
            'wc-api'    => 'myfatoorah_complete',
            'oid'       => $orderId,
            'paymentId' => $paymentId,
        ];

        if (MyFatoorah::filterInputField('pay_for_order') == 'true') {
            $args['pay_for_order'] = 'true';
        }

        include_once(MYFATOORAH_WOO_TEMPLATES_PATH . 'loader.php');
    }

    //-----------------------------------------------------------------------------------------------------------------------------
}
