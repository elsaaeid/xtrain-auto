<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 * 
 * Modified to match the strict "Payment & Summary" split layout requested.
 * Billing fields are included but hidden if logged in (or minimized) to match the "clean" look.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Detach payment from review hook (manual placement)
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="custom-checkout-page-wrapper">
    <form name="checkout" method="post" class="checkout woocommerce-checkout custom-image-layout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
        
        <!-- HIDDEN BILLING FIELDS (Required Data) -->
        <!-- We keep these in the DOM so checkout submits correctly, but hide them visually to match your "Payment Only" visual design -->
        <div class="checkout-billing-fields-hidden" style="display:none;">
            <?php if ( $checkout->get_checkout_fields() ) : ?>
                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                <div class="col2-set" id="customer_details">
                    <div class="col-1"><?php do_action( 'woocommerce_checkout_billing' ); ?></div>
                    <div class="col-2"><?php do_action( 'woocommerce_checkout_shipping' ); ?></div>
                </div>
                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
            <?php endif; ?>
        </div>

        <div class="checkout-flex-container">
            
            <!-- SECTION 1: PAYMENT (Right in RTL) -->
            <div class="checkout-col-payment">
                <div class="checkout-payment-section">
                    <h3 class="payment-heading"><?php esc_html_e( 'الدفع', 'woocommerce' ); ?></h3>
                    
                    <!-- Notices Container (for errors) -->
                    <div class="checkout-notices-box">
                         <?php wc_print_notices(); ?> 
                    </div>

                    <?php woocommerce_checkout_payment(); ?>
                </div>
            </div>

            <!-- SECTION 2: SUMMARY (Left in RTL) -->
            <div class="checkout-col-summary">
                <div class="summary-box-inner">
                    <h3 class="summary-title" id="order_review_heading"><?php esc_html_e( 'إجمالي الطلبات', 'woocommerce' ); ?></h3>
                    
                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
