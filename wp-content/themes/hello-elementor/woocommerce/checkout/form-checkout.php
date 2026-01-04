<?php
/**
 * Checkout Form
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Remove the payment section from the order review hook so we can place it manually
// This splits the "Product Table" from the "Payment Methods"
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout modern-split-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

    <div class="checkout-layout-wrapper">
        
        <!-- Left Column: Information & Payment -->
        <div class="checkout-main-col">
            <div class="checkout-branding-mobile">
                <!-- Logo could go here for mobile -->
            </div>

            <?php if ( $checkout->get_checkout_fields() ) : ?>
                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                <div class="col2-set" id="customer_details">
                    <div class="col-1">
                        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    </div>

                    <div class="col-2">
                        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                    </div>
                </div>

                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
            <?php endif; ?>

            <!-- Payment Section Moved Here -->
             <div class="checkout-payment-section">
                <h3 class="payment-heading"><?php esc_html_e( 'الدفع', 'woocommerce' ); ?></h3>
                <?php woocommerce_checkout_payment(); ?>
             </div>
        </div>

        <!-- Right Column: Order Summary (Sidebar) -->
        <div class="checkout-sidebar-col">
            <div class="checkout-sidebar-inner">
                <h3 id="order_review_heading"><?php esc_html_e( 'ملخص الطلب', 'woocommerce' ); ?></h3>
                
                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php 
                        // This now only outputs the table (and potentially error messages), since we removed payment
                        do_action( 'woocommerce_checkout_order_review' ); 
                    ?>
                </div>

                <!-- Coupon Toggle (Optional custom placement if needed, simplified here) -->
                <div class="checkout-coupon-mobile-toggle">
                     <!-- logic handled by WC usually inside review-order or separate -->
                </div>
            </div>
        </div>

    </div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
