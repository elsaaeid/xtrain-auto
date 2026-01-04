<?php
/**
 * Cart Sidebar Totals Template Part
 * Shared between Cart and Checkout pages.
 */

// If we are on checkout, we might want to hide the "Proceed" button logic 
// because standard checkout has its own "Place Order" button elsewhere.
$is_checkout_page = is_checkout() && ! is_wc_endpoint_url( 'order-received' );
?>

<div class="totals-glass-card">
    <h2>إجمالي الطلبات</h2>

    <!-- Subtotal Row -->
    <div class="side-row">
        <span class="side-value"><?php wc_cart_totals_subtotal_html(); ?></span>
        <span class="side-label">الإجمالي</span>
    </div>

    <!-- Shipping Row & Address Selection -->
    <div class="side-row shipping-section">
        
        <div class="shipping-header-row">
            <span class="side-label">الشحن</span>
        </div>

        <div class="shipping-methods-container">
            
            <?php
            // Get Saved Addresses
            $current_addr1 = WC()->customer->get_shipping_address_1();
            $saved_addresses = [];
            if ( is_user_logged_in() ) {
                $saved_addresses = get_user_meta( get_current_user_id(), 'hello_saved_addresses', true );
            }
            if ( ! is_array($saved_addresses) ) $saved_addresses = [];
            
            if ( ! empty($saved_addresses) ) : ?>
                <div class="saved-addresses-list">
                    <?php foreach($saved_addresses as $index => $addr) : 
                        $full_text = $addr['addr1'];
                        
                        // Check if active
                        $is_active = ( trim($current_addr1) == trim($addr['addr1']) ); 
                    ?>
                    <label class="address-radio-row">
                        <input type="radio" name="selected_shipping_address_sidebar" value="<?php echo $index; ?>" <?php checked($is_active, true); ?>>
                        <span class="radio-label-text">
                            <?php echo esc_html( mb_strimwidth($full_text, 0, 30, '...') ); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="#" class="orange-link-add">إضافة عنوان</a>
            
            <!-- Hidden Address Form -->
            <div id="add-address-form">
                <textarea id="new_address_text" placeholder="مثال: الرياض، حي الملز، شارع..." class="address-input-area"></textarea>
                <div class="address-form-actions">
                    <button type="button" id="save_address_btn" class="btn-save-address">حفظ</button>
                    <button type="button" id="cancel_address_btn" class="btn-cancel-address">إلغاء</button>
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function($){
        // 1. Toggle Add Form (Delegated)
        $(document).on('click', '.orange-link-add', function(e){
            e.preventDefault();
            $('#add-address-form').slideToggle();
        });

        // 2. Hide Form (Delegated)
        $(document).on('click', '#cancel_address_btn', function(){
            $('#add-address-form').slideUp();
        });

        // 3. Save New Address (Delegated)
        $(document).on('click', '#save_address_btn', function(){
            var address = $('#new_address_text').val();
            if(!address) { 
                alert("الرجاء إدخال العنوان"); 
                return; 
            }
            
            var $btn = $(this);
            $btn.text('...').prop('disabled', true);

            $.ajax({
                url: <?php echo json_encode( admin_url('admin-ajax.php') ); ?>,
                type: 'POST',
                data: {
                    action: 'save_cart_address',
                    address: address,
                    security: <?php echo json_encode( wp_create_nonce('save-address-nonce') ); ?>
                },
                success: function(res) {
                    if(res.success) {
                        window.location.reload(); 
                    } else {
                        alert("Error saving address");
                    }
                },
                complete: function() { $btn.text("حفظ").prop('disabled', false); }
            });
        });

        // 4. Select Existing Address (Delegated)
        $(document).on('change', 'input[name="selected_shipping_address_sidebar"]', function(){
            var idx = $(this).val();
            $('.totals-glass-card').css('opacity', '0.5');
            
            $.ajax({
                url: <?php echo json_encode( admin_url('admin-ajax.php') ); ?>,
                type: 'POST',
                data: {
                    action: 'select_cart_address',
                    address_index: idx,
                    security: <?php echo json_encode( wp_create_nonce('save-address-nonce') ); ?>
                },
                success: function(res) {
                    if(res.success) {
                        window.location.reload();
                    }
                }
            });
        });
    });
    </script>

    <!-- Grand Total Row -->
    <div class="side-row grand-row">
        <span class="side-value"><?php wc_cart_totals_order_total_html(); ?></span>
        <span class="side-label">المجموع الكلي</span>
    </div>

    <?php if ( ! $is_checkout_page ) : ?>
        <!-- Proceed Button (Only on Cart Page) -->
        <div class="checkout-wrapper">
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-proceed-dark">متابعة الطلب</a>
        </div>
    <?php endif; ?>
</div>
