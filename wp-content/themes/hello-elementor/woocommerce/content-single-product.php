<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-single-wrapper', $product ); ?>>

    <!-- single Title/Header Section at Top (Full Width) -->
    <div class="product-header-full-width">
        <h1 class="single-product-title"><?php the_title(); ?></h1>
        
        <div class="product-meta-row">
            <!-- 1. Rating (interactive preview updated by comment-form stars) -->
            <?php $product_rating = (float) $product->get_average_rating(); ?>
            <div class="product-grid-rating">
                <div class="stars">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <span class="star <?php echo $i <= $product_rating ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <span class="rating-value"><?php echo esc_html($product_rating ? number_format($product_rating, 2) : '0.00'); ?></span>
            </div>

            <!-- 2. Status (Middle) -->
            <div class="stock-status-container">
                 <span class="stock-status-text">متاح</span>
                 <span class="stock-status-dot">•</span>
            </div>

            <!-- 3. SKU (Left in RTL) -->
            <div class="product-sku">
                <span class="sku_wrapper">
                    <span class="sku-label"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?></span> 
                    <span class="sku">
                        <?php 
                        $sku = $product->get_sku();
                        echo $sku ? $sku : 'U6W7E1K8S1'; // Mock SKU if empty
                        ?>
                    </span>
                </span>
            </div>
        </div>
        

    </div>


    <div class="product-top-section">
        
        <!-- Product Images Column -->
        <div class="product-gallery-column">
            <?php 
                $post_thumbnail_id = $product->get_image_id();
                $attachment_ids = $product->get_gallery_image_ids();
                
                // Sale Badge
                if ( $product->is_on_sale() ) : 
                    $regular_price = (float) $product->get_regular_price();
                    $sale_price = (float) $product->get_sale_price();
                    if ( $regular_price > 0 && $sale_price > 0 ) {
                        $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
                        echo '<span class="single-product-badge">-' . $percentage . '%</span>';
                    }
                endif; 
            ?>
            
            <?php 
                // Wishlist Button - Unified Implementation
                if (defined('YITH_WCWL') || function_exists('YITH_WCWL')) {
                    $product_id = $product->get_id();
                    $in_wishlist = YITH_WCWL()->is_product_in_wishlist($product_id);
                    $base_url = (is_ssl() ? 'https://' : 'http://') . 
                        (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') .
                        (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                    $add_to_wishlist_url = wp_nonce_url(add_query_arg('add_to_wishlist', $product_id, $base_url), 'add_to_wishlist');
                    
                    echo '<div class="yith-wcwl-add-button">';
                    echo '<a href="' . esc_url($add_to_wishlist_url) . '" ';
                    echo 'class="add_to_wishlist product-wishlist-btn' . ($in_wishlist ? ' active' : '') . '" ';
                    echo 'data-product-id="' . esc_attr($product_id) . '" ';
                    echo 'data-product-type="' . esc_attr($product->get_type()) . '" ';
                    echo 'data-original-product-id="' . esc_attr($product->get_parent_id()) . '" ';
                    echo 'data-title="Add to wishlist" ';
                    echo 'rel="nofollow">';
                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">';
                    echo '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo '</a>';
                    echo '</div>';
                } else {
                    echo '<button class="product-wishlist-btn" data-product-id="' . esc_attr($product->get_id()) . '" type="button">';
                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">';
                    echo '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo '</button>';
                }
            ?>

            <div class="product-gallery-main">
                <?php if ( $post_thumbnail_id ) : ?>
                    <img src="<?php echo wp_get_attachment_image_url( $post_thumbnail_id, 'large' ); ?>" 
                         alt="<?php echo esc_attr( $product->get_name() ); ?>" 
                         id="main-product-image">
                <?php else : ?>
                    <img src="<?php echo wc_placeholder_img_src(); ?>" alt="Placeholder">
                <?php endif; ?>
            </div>

            <?php if ( $attachment_ids && $product->get_image_id() ) : ?>
                <div class="product-gallery-thumbnails">
                    <!-- Include main image as first thumb -->
                     <div class="gallery-thumb active" onclick="changeMainImage(this, '<?php echo wp_get_attachment_image_url( $post_thumbnail_id, 'large' ); ?>')">
                        <img src="<?php echo wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' ); ?>" alt="Main">
                    </div>
                    <?php 
                    $count = 0;
                    foreach ( $attachment_ids as $attachment_id ) {
                        if($count >= 4) break; // Limit thumbs
                        ?>
                        <div class="gallery-thumb" onclick="changeMainImage(this, '<?php echo wp_get_attachment_image_url( $attachment_id, 'large' ); ?>')">
                            <img src="<?php echo wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>" alt="">
                        </div>
                        <?php 
                        $count++;
                    } 
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info Column -->
        <div class="product-info-column">
            
            <div class="single-product-price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- Excerpt -->
            <div class="single-product-excerpt">
                <?php the_excerpt(); ?>
            </div>

            <!-- 1. Orange Notification Box (Stats) -->
            <div class="product-notification-box orange-box">
                <span class="notify-icon"><i class="fas fa-shopping-cart"></i></span>
                <span><?php echo sprintf( 'هذا المنتج تم إضافته في سلة %d أشخاص في آخر 24 ساعة', rand(3,15) ); ?></span>
            </div>

            <!-- 2. Blue Info Box (Shipping & Warranty) -->
            <div class="sold-stats-box blue-box">
                <div class="sold-stat-row">
                    <span class="sold-icon"><i class="fas fa-cubes"></i></span>
                    <span>يشحن ويصل سريعا خلال 24 ساعة</span>
                </div>
                <div class="sold-stat-row">
                    <span class="sold-icon"><i class="fas fa-shield-alt"></i></span>
                    <span>يوجد ضمان 3 سنوات علي المنتج</span>
                </div>
            </div>

            <div class="product-actions-row">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <!-- 3. Support Section -->
            <?php
                if ( function_exists('get_field') ) {
                    // Fetch from 'contact_info' single Post Type
                    $args = array(
                        'post_type'      => 'contact_info',
                        'posts_per_page' => 1,
                        'post_status'    => 'publish',
                        'fields'         => 'ids',
                    );
                    $contact_posts = get_posts($args);
                    
                    if ( ! empty($contact_posts) ) {
                        $contact_id = $contact_posts[0];
                        $acf_phone = get_field('phone_number', $contact_id);
                        
                        if ( $acf_phone ) {
                            $support_phone_display = $acf_phone;
                            // Sanitize for URL (remove spaces, dashes, parentheses)
                            $clean_phone = preg_replace('/[^0-9]/', '', $acf_phone);
                            $whatsapp_url = 'https://wa.me/' . $clean_phone;
                        }
                    }
                }
            ?>
            <a href="<?php echo esc_url($whatsapp_url); ?>" class="support-section-row" target="_blank">
                <div class="support-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="support-text">
                    <div class="support-title">خدمة عملائنا دائما جاهزين لطلبك</div>
                    <div class="support-phone">رقم الطلب السريع <strong><?php echo esc_html($support_phone_display); ?></strong></div>
                </div>
            </a>

            <div class="product-footer-meta">
                <div class="share-row-container">
                    <span class="share-label">شارك المنتج</span>
                    <div class="share-icons">
                        <?php
                            $product_url = urlencode( get_permalink() );
                            $product_title = urlencode( get_the_title() );
                            $product_image = urlencode( wp_get_attachment_url( $product->get_image_id() ) );
                        ?>
                        <div class="share-icons-list">
                             <a href="https://wa.me/?text=<?php echo $product_url; ?>" target="_blank" class="share-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                             <a href="https://pinterest.com/pin/create/button/?url=<?php echo $product_url; ?>" target="_blank" class="share-icon pinterest"><i class="fab fa-pinterest-p"></i></a>
                             <a href="https://twitter.com/intent/tweet?url=<?php echo $product_url; ?>" target="_blank" class="share-icon twitter"><i class="fab fa-twitter"></i></a>
                             <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $product_url; ?>" target="_blank" class="share-icon facebook"><i class="fab fa-facebook-f"></i></a>
                        </div>
                    </div>
                </div>

                <div class="footer-meta-item">
                    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in_row"><span class="meta-label">القسم:</span> ', '</span>' ); ?>
                    <!-- Brand Mock -->
                     <div class="posted_in_row brand-row">
                        <span class="meta-label">البراند:</span> Castrol
                     </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Feature Icons Bar (PNG Images) -->
    <div class="product-features-bar">
        <!-- 1. Right (Shield) -->
        <div class="feature-box">
             <div class="feature-icon">
                 <img src="<?php echo get_template_directory_uri(); ?>/assets/images/single-image-box3.svg.png" alt="Protection">
             </div>
             <div class="feature-title">حماية وضمان</div>
             <div class="feature-desc">هنالك العديد من الأنواع المتوفرة للنصوص لوريم إيبسوم ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر</div>
        </div>

        <!-- 2. Mid-Right (Return) -->
        <div class="feature-box">
             <div class="feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/single-image-box2.svg.png" alt="Return">
             </div>
             <div class="feature-title">رجوع سهل</div>
             <div class="feature-desc">هنالك العديد من الأنواع المتوفرة للنصوص لوريم إيبسوم ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر</div>
        </div>

        <!-- 3. Mid-Left (Truck) -->
        <div class="feature-box">
             <div class="feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/single-image-box.svg.png" alt="Shipping">
             </div>
             <div class="feature-title">شحن سريع</div>
             <div class="feature-desc">هنالك العديد من الأنواع المتوفرة للنصوص لوريم إيبسوم ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر</div>
        </div>

        <!-- 4. Left (Package) -->
        <div class="feature-box">
             <div class="feature-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Check package.png" alt="Warranty">
             </div>
             <div class="feature-title">حماية وضمان</div>
             <div class="feature-desc">هنالك العديد من الأنواع المتوفرة للنصوص لوريم إيبسوم ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="product-tabs-section">
        <?php woocommerce_output_product_data_tabs(); ?>
    </div>

    <!-- Related Products -->
    <?php
    // Access WooCommerce plugin's related products function directly
    woocommerce_output_related_products();
    ?>

</div>

<script>
function changeMainImage(el, src) {
    var mainImg = document.getElementById('main-product-image');
    mainImg.src = src;
    
    var thumbs = document.querySelectorAll('.gallery-thumb');
    thumbs.forEach(function(t) { t.classList.remove('active'); });
    el.classList.add('active');
}

jQuery(document).ready(function($) {
    // single Quantity Buttons Logic
    var $qtyInputs = $('form.cart .quantity input.qty');
    var $addToCartBtn = $('.single_add_to_cart_button');
    
    // PHP State from server
    var productInCartQty = <?php 
        $in_cart_qty = 0;
        if ( WC()->cart ) {
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                if ( $cart_item['product_id'] == $product->get_id() ) {
                    $in_cart_qty += $cart_item['quantity'];
                }
            }
        }
        echo $in_cart_qty; 
    ?>;

    $qtyInputs.each(function() {
        var $input = $(this);

        // Remove default WC buttons
        $input.siblings('.plus, .minus').remove();

        // Check/Wrap
        if (!$input.parent('.single-qty-wrapper').length) {
            // Determine initial visibility based on Cart State
            var initialDisplay = (productInCartQty > 0) ? 'flex' : 'none';
            
            $input.wrap('<div class="single-qty-wrapper" style="display:' + initialDisplay + ';"></div>');
            $input.before('<button type="button" class="qty-btn minus"><i class="fas fa-trash-alt"></i></button>'); 
            $input.after('<button type="button" class="qty-btn plus">+</button>');
            
            // Sync Input Value if in cart
            if (productInCartQty > 0) {
                $input.val(productInCartQty);
                // Also hide the separate Add to Cart button immediately
                $addToCartBtn.hide(); 
            }
        }
    });

    // Initial State Check for trash icons
    function updateQtyState($input) {
        var val = parseFloat($input.val()) || 0;
        var $wrapper = $input.closest('.single-qty-wrapper');
        var $minusBtn = $wrapper.find('.qty-btn.minus');
        
        if (val <= 1) {
            $minusBtn.html('<i class="fas fa-trash-alt"></i>');
            $minusBtn.addClass('is-trash');
        } else {
            $minusBtn.text('-');
            $minusBtn.removeClass('is-trash');
        }
    }
    
    // Run update immediately
    $qtyInputs.each(function() { updateQtyState($(this)); });

    // Add to Cart Button Click -> AJAX Add -> Then Show Wrapper
    $addToCartBtn.on('click', function(e) {
        e.preventDefault(); 
        
        var $btn = $(this);
        var $form = $btn.closest('form.cart');
        
        $btn.addClass('loading').css('opacity', '0.7');

        var formData = $form.serializeArray();
        formData.push({ name: 'add-to-cart', value: $btn.val() }); 
        
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
            data: formData,
            success: function(response) {
                $btn.removeClass('loading').css('opacity', '1');
                
                if ( response.error && response.product_url ) {
                    window.location = response.product_url;
                    return;
                }

                $(document.body).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, $btn ] );

                $btn.hide();
                $('.single-qty-wrapper').css('display', 'flex');
                
                var $input = $('.single-qty-wrapper input.qty');
                var currentVal = parseFloat($input.val()) || 0;
                // If it was 0 or empty, default to 1. If it was already set (from manual input?), keep it.
                // Usually for "Add to Cart", the user intended 1 (or whatever was in the number box if it was visible? Wait, input is usually hidden with wrapper hidden).
                // If wrapper hidden, input default is 1.
                if(currentVal < 1) $input.val(1);
                
                updateQtyState($input);
            },
            error: function() {
                $btn.removeClass('loading').css('opacity', '1');
                $form.off('submit').submit(); 
            }
        });
    });

    // Qty Button Click Logic
    $(document).on('click', '.qty-btn', function() {
        var $btn = $(this);
        var $input = $btn.siblings('input.qty');
        var val = parseFloat($input.val()) || 0;
        var step = parseFloat($input.attr('step')) || 1;
        var min = parseFloat($input.attr('min')) || 1;
        var max = parseFloat($input.attr('max')) || 9999;

        if ($btn.hasClass('plus')) {
            if (val < max) {
                $input.val(val + step).trigger('change');
            }
        } else {
            // Minus or Trash
            if ($btn.hasClass('is-trash')) {
                // If Trash clicked: Revert to Add to Cart button
                $('.single-qty-wrapper').hide();
                $addToCartBtn.show();
                $input.val(1); // Reset or Keep?
            } else {
                if (val > min) {
                    $input.val(val - step).trigger('change');
                }
            }
        }
        updateQtyState($input);
    });

    // Update state on manual input change
    $('input.qty').on('change', function() {
        updateQtyState($(this));
    });
});
</script>

<?php do_action( 'woocommerce_after_single_product' ); ?>
