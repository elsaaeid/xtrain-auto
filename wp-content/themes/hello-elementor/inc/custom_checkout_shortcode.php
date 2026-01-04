<?php
/**
 * Custom Split Checkout Shortcode
 * Wraps the split-layout checkout form in a shortcode [custom_split_checkout]
 */

function custom_split_checkout_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    $checkout = WC()->checkout();
    
    if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
        return esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    }

    // Force MyFatoorah Language to Arabic
    add_filter( 'myfatoorah_is_arabic', '__return_true' );
    add_filter( 'myfatoorah_session_language', function() { return 'ar'; } ); 
    
    // Attempt to override config via script injection if the plugin uses a global config
    ?>
    <script type="text/javascript">
        // Force Arabic for MyFatoorah Client Side if applicable
        window.myFatoorah = window.myFatoorah || {};
        window.myFatoorah.language = 'ar';
        if(typeof mfConfig !== 'undefined') { mfConfig.language = 'ar'; }
    </script>
    <?php

    ob_start();
    
    // Detach Payment from default Review Order Hook to place manually
    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
    
    // Filter the "Place Order" button text to match the image "تأكيد الدفع"
    add_filter( 'woocommerce_order_button_text', 'custom_checkout_button_text_override' );

    // Check if we are on the "Order Received" (Thank You) page endpoint
    if ( is_wc_endpoint_url( 'order-received' ) ) {
        $order_id = absint( get_query_var( 'order-received' ) );
        $order    = wc_get_order( $order_id );

        if ( $order ) {
            wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
            return ob_get_clean();
        }
    }

    if ( ! is_admin() ) {
        wp_enqueue_script( 'wc-checkout' );
    }
    ?>
    
    <div class="custom-checkout-page-wrapper">
        <!-- Toast Notification Container -->
        <div id="checkout-toast" style="visibility:hidden; min-width: 300px; background-color: var(--color-secondary); color: #fff; text-align: center; border-radius: 8px; padding: 15px 20px; position: fixed; z-index: 10002; left: 50%; transform: translateX(-50%); top: -100px; font-size: 16px; font-weight:bold; box-shadow: 0 5px 20px rgba(0,0,0,0.2); opacity: 0; transition: top 0.4s ease, opacity 0.4s ease;">
            <span id="checkout-toast-message"></span>
        </div>

        <div class="woocommerce-notices-wrapper"></div>
        <form name="checkout" method="post" class="checkout woocommerce-checkout custom-image-layout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

            <!-- USER INFO MODAL (ADDRESS FORM) -->
            <div class="checkout-modal-backdrop"></div>
            
            <div class="checkout-billing-fields-hidden" id="modal-user-info-form">
                <h3 style="display:flex; justify-content:space-between; align-items:center;">
                    <span>بيانات التوصيل</span>
                    <span style="font-size:14px; font-weight:400; color:#888; cursor:pointer;" onclick="jQuery('#modal-user-info-form').removeClass('active-modal'); jQuery('.checkout-modal-backdrop').removeClass('active-modal');">X</span>
                </h3>

                <?php if ( $checkout->get_checkout_fields() ) : ?>
                    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                    <div class="col2-set" id="customer_details">
                        <div class="col-1"><?php do_action( 'woocommerce_checkout_billing' ); ?></div>
                        <div class="col-2"><?php do_action( 'woocommerce_checkout_shipping' ); ?></div>
                    </div>
                    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
                <?php endif; ?>

                <button type="button" class="btn-close-modal-confirm" id="btn-confirm-user-info">تأكيد البيانات</button>
            </div>



                <div class="checkout-flex-container">
                    <!-- Payment Section (Right in RTL / Image) -->
                    <div class="checkout-col-payment">
                        
                        <!-- Address Summary Box (Visible after Saving) -->
                        <div id="address-summary-box" style="display:none; background:#f9f9f9; padding:15px; margin-bottom:20px; border-radius:8px; border:1px solid #ddd;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <h4 style="margin:0; font-size:16px;">بيانات التوصيل</h4>
                                <button type="button" id="edit-address-btn" style="background:none; border:none; color:#EA7739; cursor:pointer; font-weight:bold; text-decoration:underline;">تعديل</button>
                            </div>
                            <p id="summary-name" style="margin:0 0 5px; font-weight:bold;"></p>
                            <p id="summary-address" style="margin:0 0 5px; color:#555;"></p>
                            <p id="summary-phone" style="margin:0; color:#555; font-size:14px;"></p>
                        </div>

                        <div class="custom-payment-wrapper">
                            <!-- Payment Methods -->
                            <?php woocommerce_checkout_payment(); ?>
                        </div>
                    </div>

                    <!-- Summary Section (Left in RTL / Image) -->
                    <div class="checkout-col-summary">
                        <?php get_template_part( 'template-parts/cart-sidebar' ); ?>
                    </div>
                </div>

            </div>
            
            <script type="text/javascript">
            var isUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
            var loginUrl = '<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>';
            var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

            jQuery(function($){
                var isInfoConfirmed = false;
                
                // Show "Logged in" message if applicable
                if(isUserLoggedIn) {
                   // Append a notice inside the modal
                   $('#modal-user-info-form h3').after('<div style="background:#e7f4e4; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px; font-size:14px;">أنت مسجل الدخول بالفعل. سيتم إرفاق الطلب بحسابك الحالي.</div>');
                }
                
                // Helper: Show Toast
                window.showToast = function(message) {
                    var x = document.getElementById("checkout-toast");
                    var msg = document.getElementById("checkout-toast-message");
                    
                    if(x && msg) {
                        msg.innerText = message;
                        x.style.visibility = "visible";
                        x.style.opacity = "1";
                        x.style.top = "50px"; // Slide down into view
                        
                        setTimeout(function(){ 
                            x.style.visibility = "hidden"; 
                            x.style.opacity = "0";
                            x.style.top = "-100px"; // Slide back up
                        }, 3000);
                    } else {
                        console.log("Toast: " + message);
                    }
                }

                // --- 0. Uncheck Payment Methods Logic ---
                var hasUserSelectedPayment = false;

                // Track if user makes a selection
                $(document.body).on('change', 'input[name="payment_method"]', function(){
                    hasUserSelectedPayment = true;
                });

                // Clear selection on load and after initial updates if user hasn't chosen one
                function clearPaymentSelection() {
                    if (!hasUserSelectedPayment) {
                        $('input[name="payment_method"]').prop('checked', false);
                        $('.payment_box').hide();
                        
                        // Also remove the "selected" class from the LI if theme adds it
                        $('.wc_payment_method').removeClass('payment_method_selected');
                    }
                }

                // Run on load
                setTimeout(clearPaymentSelection, 50);
                setTimeout(clearPaymentSelection, 500); // Fail-safe for slow renders

                // Run after WooCommerce updates the checkout (it often re-checks defaults)
                $(document.body).on('updated_checkout', function(){
                    clearPaymentSelection();
                });
                
                // --- 1. Swap Button Logic (Function to persist across AJAX updates) ---
                function initFakeButton() {
                    var $realBtn = $('#place_order');
                    
                    // If real button exists and we haven't already swapped it (or it was just restored by AJAX)
                    if ($realBtn.length > 0 && $('#fake_place_order').length === 0) {
                        var $fakeBtn = $realBtn.clone().attr('id', 'fake_place_order').attr('type', 'button'); 
                        $realBtn.hide().after($fakeBtn);
                    }
                }

                // Run on load
                initFakeButton();

                // Run after WooCommerce updates the checkout (to handle AJAX restoring the real button)
                $(document.body).on('updated_checkout', function(){
                    initFakeButton();
                });

                // --- 2. Handle Fake Button Click ---
                $(document.body).on('click', '#fake_place_order', function(e){
                    e.preventDefault();

                    var selectedPayment = $('input[name="payment_method"]:checked').val();
                    
                    // VALIDATION: Ensure a payment method is selected
                    if (!selectedPayment) {
                        showToast('يرجى اختيار وسيلة الدفع أولاً'); // "Please select a payment method first"
                        // Optional: remove focus/scroll if toast is sufficient
                        return false;
                    }

                    var isMyFatoorah = (selectedPayment && selectedPayment.indexOf('myfatoorah') !== -1);
                    
                    if (isMyFatoorah) {
                        // Clean defaults (standard Woo notices) to avoid clutter backing the modal or confusing the user
                        $('.woocommerce-error, .woocommerce-notices-wrapper').empty();

                        // Check if user is logged in
                        if (!isUserLoggedIn) {
                            showToast('يجب تسجيل الدخول لإتمام عملية الدفع');
                            setTimeout(function() {
                                window.location.href = loginUrl;
                            }, 2500);
                            return false; 
                        }

                        // Check Address Info for MyFatoorah as well (Billing Address is required for payment frame init usually)
                        if( ! isInfoConfirmed ) {
                            $('.checkout-modal-backdrop').addClass('active-modal');
                            $('#modal-user-info-form').addClass('active-modal');
                            showToast('يرجى تأكيد بيانات التوصيل أولاً حفاظاً على الطلب');
                            return false;
                        }

                        // 1. Try to find the box containing the specific MyFatoorah Frame ID
                        var $mfFrame = $('#mf-form-element');
                        var $paymentBox = $mfFrame.closest('.payment_box');

                        // 2. Fallback: If ID not found/nested differently, search relative to checked input
                        if ($paymentBox.length === 0) {
                             $paymentBox = $('input[name="payment_method"]:checked').closest('li').find('.payment_box');
                        }

                        // 3. Force Show Modal
                        if ($paymentBox.length > 0) {
                            $paymentBox.addClass('active-mf-modal');
                            
                            $paymentBox.css({
                                'display': 'block',
                                'visibility': 'visible',
                                'opacity': '1',
                                'position': 'fixed',
                                'z-index': '100001', // Super high z-index
                                'top': '50%',
                                'left': '50%',
                                'transform': 'translate(-50%, -50%)',
                                'background': '#fff',
                                'padding': '20px',
                                'width': '90%',
                                'max-width': '500px',
                                'border-radius': '8px',
                                'box-shadow': '0 0 50px rgba(0,0,0,0.5)'
                            });

                            $('.checkout-modal-backdrop').addClass('active-modal'); 
                        } else {
                            // Should not happen if plugin is active
                            showToast('خطأ: لم يتم العثور على نافذة الدفع. يرجى تحديث الصفحة.');
                        }
                        
                        return false;
                    }

                    // Check Address Info for COD / Others
                    if( ! isInfoConfirmed ) {
                        $('.checkout-modal-backdrop').addClass('active-modal');
                        $('#modal-user-info-form').addClass('active-modal');
                        return false;
                    }

                    // If Address Confirmed -> Proceed by clicking real button
                    $('#place_order').trigger('click');
                });
                
                // MyFatoorah Fix: Ensure iframe modal is removed if checkout updates
                $(document.body).on('updated_checkout', function(){
                    $('.active-mf-modal').removeClass('active-mf-modal');
                    $('.checkout-modal-backdrop').removeClass('active-modal');
                });
                // --- 3. Save / Confirm Address Details ---
                $(document).on('click', '#btn-confirm-user-info', function(){
                    // 1. Gather Data
                    var fname = $('#billing_first_name').val() || '';
                    var lname = $('#billing_last_name').val() || '';
                    var address = $('#billing_address_1').val() || '';
                    var city = $('#billing_city').val() || '';
                    var phone = $('#billing_phone').val() || '';

                    // Basic Validation (Optional: Check required)
                    if(fname === '' || phone === '') {
                        alert('يرجى ملء البيانات المطلوبة');
                        return;
                    }


                    // 2. Check "Create Account" Logic via AJAX
                    var createAccount = $('#createaccount').is(':checked');
                    var email = $('#billing_email').val();

                    if(createAccount && email) {
                        // Change button text to indicate loading
                        var $btn = $(this);
                        var originalText = $btn.text();
                        $btn.text('جارِ التحقق...');
                        $btn.prop('disabled', true);

                        $.ajax({
                            url: ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'check_checkout_email_exists',
                                email: email
                            },
                            success: function(response) {
                                $btn.text(originalText);
                                $btn.prop('disabled', false);

                                if(response.success && response.data.exists) {
                                    alert('البريد الإلكتروني هذا مسجل بالفعل. يرجى تسجيل الدخول أو استخدام بريد آخر.');
                                    // Optional: Redirect to login or just let them fix it
                                    // window.location.href = loginUrl; 
                                } else {
                                    // Valid (or not existing), proceed
                                    finalizeUserInfo(fname, lname, address, city, phone);
                                }
                            },
                            error: function() {
                                // Fallback if error
                                $btn.text(originalText);
                                $btn.prop('disabled', false);
                                finalizeUserInfo(fname, lname, address, city, phone);
                            }
                        });
                    } else {
                        // No account creation requested, proceed immediately
                        finalizeUserInfo(fname, lname, address, city, phone);
                    }
                });
                
                function finalizeUserInfo(fname, lname, address, city, phone) {
                    // 2. Update Summary UI
                    $('#summary-name').text(fname + ' ' + lname);
                    $('#summary-address').text(address + ' - ' + city);
                    $('#summary-phone').text(phone);
                    
                    $('#address-summary-box').fadeIn();

                    // 3. Close Modal & Mark Confirmed
                    $('.checkout-modal-backdrop').removeClass('active-modal');
                    $('#modal-user-info-form').removeClass('active-modal');
                    isInfoConfirmed = true;
                }

                // --- 4. Edit Button Handler ---
                $(document).on('click', '#edit-address-btn', function(){
                    $('.checkout-modal-backdrop').addClass('active-modal');
                    $('#modal-user-info-form').addClass('active-modal');
                    // We don't reset isInfoConfirmed to false necessarily, 
                    // but we allow them to change it.
                });

                // Close Handlers
                $(document).on('click', '.checkout-modal-backdrop', function(){
                    $(this).removeClass('active-modal');
                    $('#modal-user-info-form').removeClass('active-modal');
                    $('.active-mf-modal').removeClass('active-mf-modal');
                });

                // Close MyFatoorah Modal when clicking the X button (::before pseudo-element)
                $(document).on('click', '.active-mf-modal', function(e){
                    // Check if click is on the top-right area (close button)
                    var $target = $(e.target);
                    var modalBox = $(this);
                    
                    // Get the position of the click relative to the modal
                    var clickX = e.pageX - modalBox.offset().left;
                    var clickY = e.pageY - modalBox.offset().top;
                    
                    // Close button is at top-right corner (within ~40px from right and top)
                    if (clickX > modalBox.width() - 40 && clickY < 40) {
                        modalBox.removeClass('active-mf-modal');
                        $('.checkout-modal-backdrop').removeClass('active-modal');
                        e.stopPropagation();
                    }
                });
            });
            </script>
        </form>
    </div>

    <?php
    // Clean up filter after render
    remove_filter( 'woocommerce_order_button_text', 'custom_checkout_button_text_override' );
    
    return ob_get_clean();
}
add_shortcode( 'custom_split_checkout', 'custom_split_checkout_shortcode' );

// Helper function for button text
if (!function_exists('custom_checkout_button_text_override')) {
    function custom_checkout_button_text_override( $button_text ) {
        return 'تأكيد الدفع'; // "Confirm Payment" in Arabic
    }
}


