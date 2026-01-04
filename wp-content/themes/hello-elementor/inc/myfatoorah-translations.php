<?php
// Function to force Arabic formatting
function force_arabic_display_fixes() {
    ?>
    <style>
        .mf-payment-methods-container, .mf-card-container, #mf-form-element {
            direction: rtl !important;
            text-align: right !important;
        }
        .mf-card-title, .mf-price-tag, .mf-grey-text {
            text-align: right !important;
            font-family: inherit !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'force_arabic_display_fixes');

// Translate MyFatoorah Strings
add_filter( 'gettext', 'custom_translate_myfatoorah_strings', 20, 3 );
function custom_translate_myfatoorah_strings( $translated_text, $text, $domain ) {
    
    // Only proceed if it is strictly related to MyFatoorah or Checkout context
    // This reduces overhead
    if ( $domain === 'myfatoorah-woocommerce' || is_checkout() ) {
        switch ( $text ) {
            case 'Checkout with MyFatoorah Payment Gateway':
                $translated_text = 'الدفع عبر بوابة ماي فاتورة';
                break;
            case 'How would you like to pay?':
                $translated_text = 'كيف تود أن تدفع؟';
                break;
            case 'Or ':
                $translated_text = 'أو ';
                break;
            case 'Pay With':
                $translated_text = 'الدفع عبر';
                break;
            case 'Insert Card Details':
                $translated_text = 'أدخل بيانات البطاقة';
                break;
            case 'Pay Now':
                $translated_text = 'ادفع الآن';
                break;
            case 'Save card number for future payments':
                $translated_text = 'حفظ بيانات البطاقة لعمليات الدفع المستقبلية';
                break;
        }
    }
    return $translated_text;
}

// Ensure Gateway Title/Description is also caught if stored in options
add_filter( 'woocommerce_gateway_title', 'custom_translate_gateway_title', 10, 2 );
function custom_translate_gateway_title( $title, $gateway_id ) {
    if ( strpos( $gateway_id, 'myfatoorah' ) !== false ) {
        if ( $title === 'Cards' || $title === 'MyFatoorah - Cards' ) {
            return 'الدفع بالبطاقة';
        }
    }
    return $title;
}

add_filter( 'woocommerce_gateway_description', 'custom_translate_gateway_description', 10, 2 );
function custom_translate_gateway_description( $description, $gateway_id ) {
    if ( strpos( $gateway_id, 'myfatoorah' ) !== false ) {
        if ( strpos( $description, 'Checkout with MyFatoorah Payment Gateway' ) !== false ) {
            return 'الدفع عبر بوابة ماي فاتورة';
        }
    }
    return $description;
}
