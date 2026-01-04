<?php
/**
 * Shortcode: WooCommerce Products Grid
 * Usage: [products_grid]
 * Displays WooCommerce products in a filterable grid with category tabs
 */
function products_grid_shortcode($atts) {

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '';
    }

    // Shortcode attributes
    $atts = shortcode_atts(array(
        'limit'      => 12,
        'orderby'    => 'popularity',
        'order'      => 'DESC',
        'category'   => '',
        'title'      => 'الأكثر طلبا',
        'show_tabs'  => true,
    ), $atts);

    // Get product categories for tabs
    $product_categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 3,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ));

    // Build query args
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => intval($atts['limit']),
        'orderby'        => $atts['orderby'],
        'order'          => $atts['order'],
        'post_status'    => 'publish',
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
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

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <style>
    .product-grid-progress{width:100%;height:6px;background:#E2E8F0;border-radius:3px;margin:8px 0;overflow:hidden}
    .product-grid-progress .progress-track{display:flex;width:100%;height:100%}
    .product-grid-progress .progress-sold{height:100%;background:#d1d5db;border-radius:3px;transition:width .6s ease}
    .product-grid-progress .progress-available{height:100%;background:#F47C33;border-radius:3px;transition:width .6s ease}
    .product-grid-progress .progress-track.unknown{background:#f3f4f6;display:block}
    .product-grid-progress .progress-track.unknown .progress-indicator{width:6px;height:100%;background:#F47C33;border-radius:3px;margin-left:6px}
    .product-grid-stock .stock-item{display:flex;justify-content:space-between;font-size:13px;color:#6b7280;margin-top:6px}
    </style>

    <div class="products-grid-wrapper">
        <div class="products-grid-header">
            <h2 class="products-grid-title"><?php echo esc_html($atts['title']); ?></h2>
            
            <?php if ($atts['show_tabs'] && !empty($product_categories) && !is_wp_error($product_categories)) : ?>
                <div class="products-grid-tabs">
                    <button class="tab-btn active" data-category="all">الكل</button>
                    <?php foreach ($product_categories as $cat) : 
                        $cat_name = $cat->name;
                        // Shorten long category names
                        if (mb_strlen($cat_name) > 20) {
                            $cat_name = mb_substr($cat_name, 0, 20) . '...';
                        }
                    ?>
                        <button class="tab-btn" data-category="<?php echo esc_attr($cat->slug); ?>" title="<?php echo esc_attr($cat->name); ?>">
                            <?php echo esc_html($cat_name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="products-grid-view-all">عرض الكل</a>
        </div>

        <div class="products-grid-container">
            <div class="products-grid">
                <?php while ($q->have_posts()) : $q->the_post();
                    global $product;
                    
                    $product_id = get_the_ID();
                    $product_obj = wc_get_product($product_id);
                    
                    if (!$product_obj) continue;
                    
                    $product_title = get_the_title();
                    $product_link = get_permalink($product_id);
                    $product_image = get_the_post_thumbnail($product_id, 'medium');
                    // Get product price in current language/currency
                    if ( function_exists( 'icl_object_id' ) && function_exists( 'wcml_multi_currency' ) ) {
                        $product_price = wcml_multi_currency()->prices->get_product_price_in_currency( $product_obj->get_id(), null, true );
                    } elseif ( function_exists( 'pll_current_language' ) && function_exists( 'wcml_multi_currency' ) ) {
                        $product_price = wcml_multi_currency()->prices->get_product_price_in_currency( $product_obj->get_id(), null, true );
                    } else {
                        $product_price = $product_obj->get_price();
                    }
                    $regular_price = $product_obj->get_regular_price();
                    $sale_price = $product_obj->get_sale_price();
                    $product_rating = $product_obj->get_average_rating();
                    $product_stock = $product_obj->get_stock_quantity();
                    $total_sales = get_post_meta($product_id, 'total_sales', true) ?: 0;
                    $is_on_sale = $product_obj->is_on_sale();
                    $is_in_stock = $product_obj->is_in_stock();
                    
                    // Calculate sale percentage
                    $sale_percent = 0;
                    if ($is_on_sale && $regular_price > 0 && $sale_price) {
                        $sale_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
                    }
                    ?>
                    
                    <div class="product-grid-item" data-category="<?php 
                        $terms = get_the_terms($product_id, 'product_cat');
                        if ($terms && !is_wp_error($terms)) {
                            echo esc_attr(implode(' ', wp_list_pluck($terms, 'slug')));
                        }
                    ?>">
                        <div class="product-grid-card">
                            <!-- Wishlist Button -->
                            <?php
                            // YITH Wishlist plugin integration
                            if (defined('YITH_WCWL') || function_exists('YITH_WCWL')) :
                                $base_url = (is_ssl() ? 'https://' : 'http://') . 
                                    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') .
                                    (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                                $add_to_wishlist_url = wp_nonce_url(add_query_arg('add_to_wishlist', $product_id, $base_url), 'add_to_wishlist');
                            ?>
                                <div class="grid-wishlist-button">
                                    <a
                                        href="javascript:void(0);"
                                        class="add_to_wishlist product-wishlist-btn <?php echo (YITH_WCWL()->is_product_in_wishlist($product_id)) ? 'active' : ''; ?>"
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
                                <button class="product-grid-wishlist product-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" type="button">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            <?php endif; ?>

                            <!-- Sale Badge -->
                            <?php if ($is_on_sale && $sale_percent > 0) : ?>
                                <span class="product-grid-badge"><?php echo esc_html($sale_percent); ?>%</span>
                            <?php endif; ?>

                            <!-- Product Image -->
                            <a href="<?php echo esc_url($product_link); ?>" class="product-grid-image-link">
                                <div class="product-grid-image">
                                    <?php if ($product_image) : ?>
                                        <?php echo $product_image; ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product_title); ?>">
                                    <?php endif; ?>
                                </div>
                            </a>

                            <!-- Product Info -->
                            <div class="product-grid-info">
                                <!-- Rating -->
                                <div class="product-grid-rating">
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="star <?php echo $i <= $product_rating ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-value"><?php echo esc_html($product_rating ? number_format($product_rating, 2) : '0.00'); ?></span>
                                </div>

                                <!-- Title -->
                                <h3 class="product-grid-title">
                                    <a href="<?php echo esc_url($product_link); ?>">
                                        <?php echo esc_html($product_title); ?>
                                    </a>
                                </h3>

                                <!-- Price -->
                                <div class="product-grid-price">
                                    <?php
                                    // Convert prices to current selected currency using plugin helper
                                    $wmc_current = function_exists('WOOMULTI_CURRENCY_F_Data') ? WOOMULTI_CURRENCY_F_Data::get_ins()->get_current_currency() : get_woocommerce_currency();
                                    $converted_regular = $regular_price ? (function_exists('wmc_get_price') ? wmc_get_price($regular_price) : $regular_price) : '';
                                    $converted_sale = $sale_price ? (function_exists('wmc_get_price') ? wmc_get_price($sale_price) : $sale_price) : '';
                                    $converted_current = (function_exists('wmc_get_price') ? wmc_get_price($product_price) : $product_price);
                                    if ($is_on_sale && $sale_price) : ?>
                                        <span class="price-old"><?php echo hello_localized_price($converted_regular, $wmc_current); ?></span>
                                    <?php endif; ?>
                                    <span class="price-current"><?php echo hello_localized_price($is_on_sale && $sale_price ? $converted_sale : $converted_current, $wmc_current); ?></span>
                                </div>

                                <?php
                                // Progress info: always show remaining and sold; render bar when available exists
                                $product_sold = intval($total_sales);
                                $manages_stock = (method_exists($product_obj, 'managing_stock') ? $product_obj->managing_stock() : (bool) $product_obj->get_manage_stock());
                                // Robustly parse product stock: accept numeric strings with whitespace
                                $available_count = null;
                                if ($product_stock !== null && $product_stock !== '') {
                                    $stock_trim = trim((string) $product_stock);
                                    if ($stock_trim !== '' && is_numeric($stock_trim)) {
                                        $available_count = intval($stock_trim);
                                    }
                                }
                                $remaining_display = $available_count !== null ? $available_count : '∞';

                                $show_bar = ($available_count !== null);
                                if ($show_bar) {
                                    // Show available count as percentage (capped at 0-100%)
                                    $available_percent = max(0, min(100, intval($available_count)));
                                    $sold_percent = 0; // Only show available in orange
                                }
                                ?>

                                <div class="product-grid-stock">
                                    <div class="stock-item">
                                        <span class="stock-label">متوفر:</span>
                                        <span class="stock-value"><?php echo esc_html($remaining_display); ?></span>
                                    </div>
                                    <div class="stock-item sold">
                                        <span class="stock-label">مباع:</span>
                                        <span class="stock-value"><?php echo esc_html($product_sold); ?></span>
                                    </div>
                                </div>

                                <div class="product-grid-progress">
                                    <?php if ($show_bar) : ?>
                                        <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr($available_percent); ?>" data-sold="<?php echo esc_attr($product_sold); ?>" data-available="<?php echo esc_attr($available_count); ?>">
                                            <div class="progress-sold" style="width:<?php echo esc_attr(isset($sold_percent) ? $sold_percent : 0); ?>%"></div>
                                            <div class="progress-available" style="width:<?php echo esc_attr(isset($available_percent) ? $available_percent : 0); ?>%"></div>
                                        </div>
                                    <?php else : ?>
                                        <div class="progress-track unknown" role="progressbar" data-sold="<?php echo esc_attr($product_sold); ?>" data-available="∞">
                                            <div class="progress-indicator" aria-hidden="true"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Add to Cart Button -->
                                <div class="product-grid-actions add-to-cart-wrapper" data-product_id="<?php echo esc_attr($product_id); ?>">
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

                                        <a href="<?php echo esc_url($product_obj->add_to_cart_url()); ?>" 
                                           class="add-to-cart-grid-btn add-to-cart-btn product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-btn-init" 
                                           data-product_id="<?php echo esc_attr($product_id); ?>"
                                           data-quantity="1"
                                           rel="nofollow"
                                           style="display: <?php echo ($qty_in_cart > 0) ? 'none' : 'block'; ?>;">
                                            أضف للسلة
                                        </a>
                                    <?php else : ?>
                                        <button class="add-to-cart-grid-btn disabled" disabled>غير متوفر</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
            </div>

            <!-- Pagination Dots -->
            <div class="products-grid-pagination">
                <?php 
                $total_products = $q->post_count;
                $products_per_page = 6; // Products per page
                $total_pages = ceil($total_products / $products_per_page);
                
                for ($i = 0; $i < $total_pages; $i++) : ?>
                    <span class="grid-pagination-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('products_grid', 'products_grid_shortcode');
