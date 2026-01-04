<?php

//kindly refer to the https://karthikbhat.net/woocommerce-api-custom-endpoint/
class PluginWebhookMyfatoorahWoocommerce {

//-----------------------------------------------------------------------------------------------------------------------------

    private $logger;

    /**
     * Constructor
     */
    public function __construct() {
        add_action('woocommerce_api_myfatoorah_webhook', array($this, 'checkEventType'));
        add_action('myfatoorah_woocommerce_webhook_TransactionsStatusChanged', array($this, 'TransactionsStatusChanged'));
        add_action('myfatoorah_woocommerce_webhook_RefundStatusChanged', array($this, 'RefundStatusChanged'));

        $this->logger = WC_LOG_DIR . 'myfatoorah_webhook.log';
    }

//-----------------------------------------------------------------------------------------------------------------------------

    function checkEventType() {
        MyFatoorah::$loggerObj = $this->logger;
        MyFatoorah::log('MyFatoorah WebHook New Request');

        $v2Options = get_option('woocommerce_myfatoorah_v2_settings');
        $secretKey = empty($v2Options['webhookSecretKey']) ? die : $v2Options['webhookSecretKey'];

        $apache      = apache_request_headers();
        $headers     = array_change_key_case($apache);
        $mfSignature = empty($headers['myfatoorah-signature']) ? die : $headers['myfatoorah-signature'];

        $body = file_get_contents('php://input');
        MyFatoorah::log('MyFatoorah WebHook Body: ' . $body);

        $webhook   = json_decode($body, true);
        $eventType = (isset($webhook['EventType']) && isset($webhook['Event'])) ? $webhook['EventType'] : die;
        $data      = (empty($webhook['Data'])) ? die : $webhook['Data'];

        if (MyFatoorah::isSignatureValid($data, $secretKey, $mfSignature, $eventType)) {
            do_action('myfatoorah_woocommerce_webhook_' . $webhook['Event'], $data);
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------

    function TransactionsStatusChanged($data) {

        //to allow the callback code run 1st
        sleep(5);

        $orderId = $data['CustomerReference'];
        $order   = new WC_Order($orderId); //todo switch to wc_get_order

        $orderPaymentId = $order->get_meta('PaymentId', true);
        if ($orderPaymentId == $data['PaymentId']) {
            die;
        }

        $paymentMethod = $order->get_payment_method();
        if (!str_contains($paymentMethod, 'myfatoorah_')) {
            die;
        }

        $calss   = 'WC_Gateway_' . ucfirst($paymentMethod);
        $gateway = new $calss;

        try {
            $gateway->checkStatus($data['InvoiceId'], 'InvoiceId', $order, ' - WebHook');
            $msg = 'Status: ' . $order->get_status();
        } catch (Exception $ex) {
            $msg = 'Error: ' . $ex->getMessage();
        }

        MyFatoorah::$loggerObj = $this->logger;
        MyFatoorah::log("MyFatoorah WebHook TransactionsStatusChanged: Order #$orderId ----- $msg");
    }

//-----------------------------------------------------------------------------------------------------------------------------

    /**
     * 
     * @param type $data
     * @param type $order
     * @param type $gateway
     * @return type
     */
    function RefundStatusChanged($data) {

        MyFatoorah::$loggerObj = $this->logger;

        //get order
        $orderList = wc_get_orders(array(
            'limit'      => -1, // Query all orders
            'meta_key'   => 'InvoiceId', // The postmeta key field
            'meta_value' => $data['InvoiceId'], // The comparison argument
        ));
        $order     = $orderList[0] ?? die;
        $orderId   = $order->get_id();

        //check order status
        $status = $order->get_status();
        if ($status != 'processing' && $status != 'completed') {
            MyFatoorah::log("MyFatoorah WebHook RefundStatusChanged: Order #$orderId ----- Can't complete the refund because the order status is $status");
            die;
        }

        //get RefundStatus array
        $refundData = $order->get_meta('RefundData', true) ?: die;
        $refundObj  = $refundData[$data['RefundId']] ?? die;

        $displayAmount   = $refundObj->DisplayAmount;
        $displayCurrency = $refundObj->DisplayCurrency;

        $orderCurrency = $order->get_currency();
        if ($displayCurrency != $orderCurrency) {
            MyFatoorah::log("MyFatoorah WebHook RefundStatusChanged: Order #$orderId ----- Can't complete the refund because the refund currency status is $displayCurrency and the order currency is $orderCurrency");
            die;
        }

        $noteTitle = '<b>MyFatoorah Refund Details:</b><br>';
        $note      = $noteTitle;

        //update
        if ($data['RefundStatus'] == 'CANCELED') {
            $noteColor = 'brown';
        } else if ($data['RefundStatus'] == 'REFUNDED' && $displayAmount == $order->get_remaining_refund_amount()) {
            $noteColor = 'green';
            $note      .= '<font color="green"><b>Fully Refunded</b></font><br>';
            $order->update_status('refunded', $noteTitle);
        } else {
            $noteColor = 'chocolate';
            $note      .= '<font color="chocolate"><b>Partial Refunded</b></font><br>';

            $default_args = array(
                'amount'   => $displayAmount,
                'reason'   => $data['Comments'],
                'order_id' => $orderId
            );
            wc_create_refund($default_args);
        }

        $note .= 'RefundStatus: <font color="' . $noteColor . '">' . $data['RefundStatus'] . '</font><br>';
        $note .= 'RefundId: ' . $data['RefundId'] . '<br>';
        $note .= 'RefundReference: ' . $data['RefundReference'] . '<br>';

        $createdDate = DateTime::createFromFormat('dmYHis', $data['CreatedDate']);
        $note        .= 'CreatedDate: ' . date_format($createdDate, 'Y-m-d H:i:s') . '<br>';

        $baseCurrency = $order->get_meta('InvoiceBaseCurrency', true) ?: '';
        $note         .= 'BaseAmount: ' . $data['Amount'] . ' ' . $baseCurrency . '<br>';
        $note         .= 'DisplayAmount: ' . $displayAmount . ' ' . $displayCurrency . '<br>';

        $note .= 'Comment: ' . $data['Comments'] . '<br>';

        $order->add_order_note($note);

        MyFatoorah::log("MyFatoorah WebHook RefundStatusChanged: Order #$orderId ----- Status is " . $data['RefundStatus'] . ' for RefundId: ' . $data['RefundId']);
    }

//-----------------------------------------------------------------------------------------------------------------------------
}
