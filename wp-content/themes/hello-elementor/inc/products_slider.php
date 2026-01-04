<?php
/**
 * Shortcode: WooCommerce Products Slider
 * Usage: [products_slider]
 * Displays WooCommerce products in a slider with full product details
 */
function products_slider_shortcode($atts) {

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '';
    }

    // Shortcode attributes
    $atts = shortcode_atts(array(
        'limit'      => 8,
        'orderby'    => 'date',
        'order'      => 'DESC',
        'category'   => '',
        'featured'   => false,
        'on_sale'    => false,
    ), $atts);

    // Build query args
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => intval($atts['limit']),
        'orderby'        => $atts['orderby'],
        'order'          => $atts['order'],
        'post_status'    => 'publish',
    );

    // Filter by category
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    // Filter featured products
    if ($atts['featured']) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
        );
    }

    // Filter on sale products
    if ($atts['on_sale']) {
        $args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key'     => '_sale_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'NUMERIC'
            ),
        );
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '';
    }

    ob_start();
    ?>

    <style>
    .product-progress{margin:12px 0 6px}
    .product-progress .progress-track{background:#eef2f6;border-radius:6px;overflow:hidden;height:8px;position:relative;display:flex}
    .product-progress .progress-sold{background:#d1d5db;height:100%;transition:width .6s ease}
    .product-progress .progress-available{background:#f97316;height:100%;transition:width .6s ease}
    .product-progress .progress-track.unknown{background:#f3f4f6;display:block}
    .product-progress .progress-track.unknown .progress-indicator{width:6px;height:100%;background:#f97316;border-radius:3px;margin-left:6px}
    .product-progress .progress-meta{display:flex;justify-content:space-between;font-size:13px;color:#6b7280;margin-top:6px}
    </style>

    <div class="products-slider-wrapper">
        <div class="products-slider-header">
            <div class="products-heading">
                <h2 class="products-title">المنتجات</h2>
                <?php
                    // Get shop URL with fallback
                    $shop_url = wc_get_page_permalink('shop');
                    if (!$shop_url || $shop_url === home_url()) {
                        $shop_page_id = wc_get_page_id('shop');
                        if ($shop_page_id > 0) {
                            $shop_url = get_permalink($shop_page_id);
                        } else {
                            $shop_url = home_url('/shop/');
                        }
                    }
                ?>
                <a href="<?php echo esc_url($shop_url); ?>" class="products-view-all">تصفح المزيد من المنتجات</a>
            </div>
            <div class="products-buttons">
                <button class="slider-nav products-slider-next" aria-label="Next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="slider-nav products-slider-prev" aria-label="Previous">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="products-slider-container">
            <div class="products-slider">
                <div class="products-slider-track">
                    <?php while ($q->have_posts()) : $q->the_post();
                        global $product;
                        
                        $product_id = get_the_ID();
                        $product_obj = wc_get_product($product_id);
                        
                        if (!$product_obj) continue;
                        
                        $product_title = get_the_title();
                        $product_link = get_permalink($product_id);
                        $product_image = get_the_post_thumbnail($product_id, 'medium');
                        $product_price = $product_obj->get_price_html();
                        $product_rating = $product_obj->get_average_rating();
                        $product_stock = $product_obj->get_stock_quantity();
                        $is_on_sale = $product_obj->is_on_sale();
                        $is_in_stock = $product_obj->is_in_stock();
                        ?>
                        
                        <div class="product-slide">
                            <div class="product-card">
                                <!-- Wishlist Button -->
                                <?php
                                // YITH Wishlist plugin integration
                                if (defined('YITH_WCWL') || function_exists('YITH_WCWL')) :
                                    $base_url = (is_ssl() ? 'https://' : 'http://') . 
                                        (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') .
                                        (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                                    $add_to_wishlist_url = wp_nonce_url(add_query_arg('add_to_wishlist', $product_id, $base_url), 'add_to_wishlist');
                                ?>
                                    <div class="yith-wcwl-add-button">
                                        <a
                                            href="<?php echo esc_url($add_to_wishlist_url); ?>"
                                            class="add_to_wishlist product-wishlist product-wishlist-btn"
                                            data-product-id="<?php echo esc_attr($product_id); ?>"
                                            data-product-type="<?php echo esc_attr($product_obj ? $product_obj->get_type() : ''); ?>"
                                            data-original-product-id="<?php echo esc_attr($product_obj ? $product_obj->get_parent_id() : ''); ?>"
                                            data-title="<?php echo esc_attr('Add to wishlist'); ?>"
                                            rel="nofollow"
                                        >
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                <?php else : ?>
                                    <button class="product-wishlist product-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" type="button">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>

                                <!-- Sale Badge -->
                                <?php if ($is_on_sale) : ?>
                                    <span class="slider-product-badge">-<?php echo esc_html($product_obj->get_sale_price() ? round((($product_obj->get_regular_price() - $product_obj->get_sale_price()) / $product_obj->get_regular_price()) * 100) : '0'); ?>%</span>
                                <?php endif; ?>

                                <!-- Product Image -->
                                <a href="<?php echo esc_url($product_link); ?>" class="product-image-link">
                                    <div class="product-image">
                                        <?php if ($product_image) : ?>
                                            <?php echo $product_image; ?>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product_title); ?>">
                                        <?php endif; ?>
                                    </div>
                                </a>

                                <!-- Product Info -->
                                <div class="product-info">
                                    <!-- Rating -->
                                    <?php if ($product_rating > 0) : ?>
                                        <div class="product-rating">
                                            <div class="stars">
                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <span class="star <?php echo $i <= $product_rating ? 'filled' : ''; ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="rating-value"><?php echo esc_html(number_format($product_rating, 2)); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Title -->
                                    <h3 class="product-title">
                                        <a href="<?php echo esc_url($product_link); ?>">
                                            <?php echo esc_html($product_title); ?>
                                        </a>
                                    </h3>

                                    <!-- Price -->
                                    <div class="product-price">
                                        <?php
                                        // Prefer numeric conversion where possible; fall back to original HTML
                                        $wmc_current = function_exists('WOOMULTI_CURRENCY_F_Data') ? WOOMULTI_CURRENCY_F_Data::get_ins()->get_current_currency() : get_woocommerce_currency();
                                        $numeric_price = $product_obj->get_price();
                                        $numeric_regular = $product_obj->get_regular_price();
                                        $numeric_sale = $product_obj->get_sale_price();
                                        if ($numeric_price !== '' && $numeric_price !== null) {
                                            $converted = function_exists('wmc_get_price') ? wmc_get_price($numeric_price) : $numeric_price;
                                            echo hello_localized_price($converted, $wmc_current);
                                        } else {
                                            // complex product types: use original HTML
                                            echo $product_price;
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    // Progress info: show sold and remaining; render bar only when available count exists
                                    $product_sold = (int) $product_obj->get_total_sales();
                                    $manages_stock = (method_exists($product_obj, 'managing_stock') ? $product_obj->managing_stock() : (bool) $product_obj->get_manage_stock());
                                    $available_count = ($manages_stock && $product_stock !== null) ? intval($product_stock) : null;
                                    $remaining_display = $available_count !== null ? $available_count : '∞';

                                    $show_bar = ($available_count !== null);
                                    if ($show_bar) {
                                        // Show available count as percentage (capped at 0-100%)
                                        $available_percent = max(0, min(100, intval($available_count)));
                                        $sold_percent = 0; // Only show available in orange
                                    }
                                    ?>

                                    <div class="product-progress" aria-hidden="false">
                                        <?php if ($available_count !== null) : ?>
                                            <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr(isset($available_percent) ? $available_percent : 0); ?>" data-sold="<?php echo esc_attr($product_sold); ?>" data-available="<?php echo esc_attr($available_count); ?>">
                                                <div class="progress-sold" style="width:<?php echo esc_attr(isset($sold_percent) ? $sold_percent : 0); ?>%"></div>
                                                <div class="progress-available" style="width:<?php echo esc_attr(isset($available_percent) ? $available_percent : 0); ?>%"></div>
                                            </div>
                                        <?php else : ?>
                                            <div class="progress-track unknown" role="progressbar" data-sold="<?php echo esc_attr($product_sold); ?>" data-available="∞">
                                                <div class="progress-indicator" aria-hidden="true"></div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="progress-meta">
                                            <span class="available">متوفر: <?php echo esc_html($remaining_display); ?></span>
                                            <span class="sold">مباع: <?php echo esc_html($product_sold); ?></span>
                                        </div>
                                    </div>

                                    <!-- Add to Cart Button -->
                                    <div class="product-actions add-to-cart-wrapper" data-product_id="<?php echo esc_attr($product_id); ?>">
                                        <?php 
                                        // Check quantity in cart
                                        $qty_in_cart = 0;
                                        if (class_exists('WooCommerce') && WC()->cart) {
                                            $cart_id = WC()->cart->generate_cart_id($product_id);
                                            $in_cart_key = WC()->cart->find_product_in_cart($cart_id);
                                            if ($in_cart_key && isset(WC()->cart->cart_contents[$in_cart_key])) {
                                                $qty_in_cart = WC()->cart->cart_contents[$in_cart_key]['quantity'];
                                            }
                                        }
                                        
                                        if ($is_in_stock) : ?>
                                            <!-- Quantity Control (Visible if in cart) -->
                                            <div class="grid-qty-control" style="display: <?php echo ($qty_in_cart > 0) ? 'flex' : 'none'; ?>; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 100%; justify-content: space-between;">
                                                <?php if ($qty_in_cart == 1) : ?>
                                                    <button class="grid-qty-btn grid-minus trash-mode" type="button" style="background:#fff0f0; border:none; padding:8px 15px; cursor:pointer; flex: 1; color: #ef4444;">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </button>
                                                <?php else : ?>
                                                    <button class="grid-qty-btn grid-minus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">-</button>
                                                <?php endif; ?>
                                                
                                                <input type="number" class="grid-qty-val" value="<?php echo ($qty_in_cart > 0) ? intval($qty_in_cart) : 1; ?>" min="1" style="width:50px; text-align:center; border:none; -moz-appearance:textfield;" readonly>
                                                <button class="grid-qty-btn grid-plus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">+</button>
                                            </div>

                                            <!-- Add to Cart Button (Visible if NOT in cart) -->
                                            <a href="<?php echo esc_url($product_obj->add_to_cart_url()); ?>" 
                                               class="add-to-cart-btn product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-btn-init" 
                                               data-product_id="<?php echo esc_attr($product_id); ?>"
                                               data-quantity="1"
                                               rel="nofollow"
                                               style="display: <?php echo ($qty_in_cart > 0) ? 'none' : 'block'; ?>;">
                                                أضف للسلة
                                            </a>
                                        <?php else : ?>
                                            <button class="add-to-cart-btn disabled" disabled>غير متوفر</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Pagination Dots -->
            <div class="products-slider-pagination">
                <?php 
                $total_products = $q->post_count;
                $slides_to_show = 5; // Desktop default
                $total_pages = ceil($total_products / $slides_to_show);
                
                for ($i = 0; $i < $total_pages; $i++) : ?>
                    <span class="pagination-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('products_slider', 'products_slider_shortcode');
