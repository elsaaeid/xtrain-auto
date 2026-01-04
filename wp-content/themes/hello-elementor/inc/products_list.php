<?php
/**
 * Shortcode: WooCommerce Products List (Vertical)
 * Usage: [products_list]
 * Displays WooCommerce products in a vertical list layout
 */
function products_list_shortcode($atts) {

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '';
    }

    // Shortcode attributes
    $atts = shortcode_atts(array(
        'limit'      => 3,
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

    <div class="products-list-wrapper">
        <div class="products-list-container">
            <?php while ($q->have_posts()) : $q->the_post();
                global $product;
                
                $product_id = get_the_ID();
                $product_obj = wc_get_product($product_id);
                
                if (!$product_obj) continue;
                
                $product_title = get_the_title();
                $product_link = get_permalink($product_id);
                $product_image = get_the_post_thumbnail_url($product_id, 'medium');
                $regular_price = $product_obj->get_regular_price();
                $sale_price = $product_obj->get_sale_price();
                $product_stock = $product_obj->get_stock_quantity();
                $is_on_sale = $product_obj->is_on_sale();
                $is_in_stock = $product_obj->is_in_stock();
                
                // Calculate discount percentage
                $discount_percent = 0;
                if ($is_on_sale && $regular_price && $sale_price) {
                    $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
                }
                ?>
                
                <div class="product-item">
                    <!-- Product Details -->
                    <div class="product-details">
                        <!-- Title -->
                        <h3 class="product-list-title">
                            <a href="<?php echo esc_url($product_link); ?>">
                                <?php echo esc_html($product_title); ?>
                            </a>
                        </h3>

                        <!-- Price -->
                        <div class="item-price">
                            <?php
                            $wmc_current = function_exists('WOOMULTI_CURRENCY_F_Data') ? WOOMULTI_CURRENCY_F_Data::get_ins()->get_current_currency() : get_woocommerce_currency();
                            $converted_regular = $regular_price ? (function_exists('wmc_get_price') ? wmc_get_price($regular_price) : $regular_price) : '';
                            $converted_sale = $sale_price ? (function_exists('wmc_get_price') ? wmc_get_price($sale_price) : $sale_price) : '';
                                if ($is_on_sale && $sale_price) : ?>
                                    <span class="price-regular"><?php echo hello_localized_price($converted_regular, $wmc_current); ?></span>
                                    <span class="price-sale"><?php echo hello_localized_price($converted_sale, $wmc_current); ?></span>
                            <?php else : ?>
                                  <span class="price-sale"><?php echo hello_localized_price($converted_regular, $wmc_current); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php
                        // Progress info: always show sold and remaining; render bar when available exists
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

                        <div class="product-list-stock">
                            <div class="stock-item">
                                <span class="stock-label">متوفر:</span>
                                <span class="stock-value"><?php echo esc_html($remaining_display); ?></span>
                            </div>
                            <div class="stock-item sold">
                                <span class="stock-label">مباع:</span>
                                <span class="stock-value"><?php echo esc_html($product_sold); ?></span>
                            </div>
                        </div>

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
                    </div>
                    <!-- Product Equipment Image -->
                    <a href="<?php echo esc_url($product_link); ?>" class="product-equipment-image">
                        
                        <!-- Sale Badge -->
                        <?php if ($is_on_sale && $discount_percent > 0) : ?>
                            <span class="product-list-badge product-badge">-<?php echo esc_html($discount_percent); ?>%</span>
                        <?php endif; ?>
                        <?php if ($product_image) : ?>
                            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>" loading="lazy">
                        <?php else : ?>
                            <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product_title); ?>">
                        <?php endif; ?>
                    </a>

                </div>
                
            <?php endwhile; ?>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('products_list', 'products_list_shortcode');
