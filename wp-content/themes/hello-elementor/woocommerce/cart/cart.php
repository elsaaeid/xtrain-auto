<?php
/**
 * Cart Page - ULTIMATE PIXEL PERFECT TEMPLATE
 * Matches the uploaded image exactly.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="custom-cart-layout">

    <!-- RIGHT SIDE: Cart Items & Progress -->
    <div class="cart-main-content">
        
        <!-- 1. PROGRESS BAR SECTION -->
        <?php
        $min_amount = 600; 
        $current_total = WC()->cart->get_subtotal();
        $needed = $min_amount - $current_total;
        $percentage = ( $current_total / $min_amount ) * 100;
        if ($percentage > 100) $percentage = 100;
        ?>
        <div class="cart-shipping-notice">
            <div class="notice-text">
                <span class="truck-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16.0002L12 21.0002L3 16.0002V8.00018L12 3.00018L21 8.00018V16.0002Z" stroke="#EA7739" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 21.0002V12.0002" stroke="#EA7739" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12.0002L21 7.00018" stroke="#EA7739" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12.0002L3 7.00018" stroke="#EA7739" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <?php if ( $current_total < $min_amount ) : ?>
                    يمكنك إضافة منتجات أخرى بسعر <?php echo wc_price($needed); ?> لتؤهلك للشحن المجاني
                <?php else : ?>
                 !لقد حصلت على شحن مجاني لطلبك
                <?php endif; ?>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-inner" style="width: <?php echo $percentage; ?>%;"></div>
            </div>
        </div>

        <!-- 2. CART TABLE -->
        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-name">المنتج</th>
                        <th class="product-quantity">الكمية</th>
                        <th class="product-price">السعر</th>
                        <th class="product-subtotal">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Remove default theme buttons for this page to avoid duplicates
                    remove_action( 'woocommerce_after_quantity_input_field', 'hello_elementor_quantity_plus' ); // Assuming function name or anonymous, safest is remove_all if anonymous? 
                    // Since functions.php uses anonymous functions for these hooks, we cannot easily remove them by name. 
                    // However, we can use CSS to hide .q_plus and .q_minus as done in CSS file.
                    // But to be cleaner, let's just use our custom buttons and ensure no conflict.

                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
                            $max_qty   = $_product->get_max_purchase_quantity();
                            $max_attr  = ( $max_qty > 0 ) ? ' max="' . esc_attr( $max_qty ) . '"' : '';
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="woocommerce-cart-form__cart-item cart-item-row">

                                <td class="product-name" data-title="المنتج">
                                    <div class="product-info-flex">
                                        <div class="item-thumb">
                                            <?php echo $_product->get_image(); ?>
                                        </div>
                                        <div class="item-details">
                                            <a href="<?php echo esc_url( $product_permalink ); ?>" class="item-title"><?php echo $_product->get_name(); ?></a>
                                        </div>
                                    </div>
                                </td>

                                <td class="product-quantity" data-title="الكمية">
                                    <div class="custom-qty-wrapper">
                                        <button type="button" class="qty-btn custom-minus" aria-label="تقليل الكمية">
                                            <i class="fa fa-trash trash-icon"></i>
                                            <span class="minus-icon" style="display:none;">
                                                <i class="fa fa-minus"></i>
                                            </span>
                                        </button>
                                        <input
                                            type="number"
                                            class="qty"
                                            name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                                            value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
                                            min="1"
                                            <?php echo $max_attr; // Only output max when greater than zero ?>
                                            step="1"
                                            inputmode="numeric"
                                            aria-label="الكمية"
                                        />
                                        <button type="button" class="qty-btn custom-plus">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>

                                <td class="product-price" data-title="السعر">
                                    <span class="custom-price"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></span>
                                </td>

                                <td class="product-subtotal" data-title="الإجمالي">
                                    <span class="custom-price"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- 3. ACTIONS BOX (COUPON & DELETE ALL) -->
            <div class="cart-lower-actions">
                
                <a href="?empty-cart=1" class="btn-clear-all-cart">حذف الكل</a>
                
                <div class="coupon-wrapper">
                    <input type="text" name="coupon_code" class="coupon-field" id="coupon_code" value="" placeholder="كوبون الخصم" />
                    <button type="submit" class="btn-apply-coupon" name="apply_coupon" value="تطبيق">تطبيق</button>
                </div>
                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                <button type="submit" name="update_cart" class="hidden-update" value="Update Cart">Update</button>
            </div>

            <!-- Init shared quantity controls -->
            <script type="text/javascript">
                window.helloInitCartQtyControls && window.helloInitCartQtyControls();
            </script>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>
    </div>

    <!-- LEFT SIDE: Sidebar Cart Totals -->
    <div class="cart-sidebar-totals">
        <?php get_template_part( 'template-parts/cart-sidebar' ); ?>
    </div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
