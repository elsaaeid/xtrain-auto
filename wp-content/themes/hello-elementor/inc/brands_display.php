<?php
/**
 * Shortcode: Brands Display
 * Usage: [brands_display]
 * Displays brand images from ACF custom fields
 */
function brands_display_shortcode() {

    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'brand',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC'
    );

    $q = new WP_Query($args);
    if ( ! $q->have_posts() ) {
        return '';
    }

    ob_start();
    ?>

    <div class="brands-wrapper">
        <div class="brands-container">
            <?php while ( $q->have_posts() ) : $q->the_post();
                $post_id = get_the_ID();
                $brand_image = get_field('brand_image', $post_id);
                
                if ( $brand_image ) :
                    $brand_name = get_the_title();
                    $brand_url = get_field('brand_url', $post_id); // Optional: link to brand page
                    ?>
                    
                    <div class="brand-item">
                        <?php if ( $brand_url ) : ?>
                            <a href="<?php echo esc_url($brand_url); ?>" title="<?php echo esc_attr($brand_name); ?>">
                                <img src="<?php echo esc_url($brand_image['url']); ?>" 
                                     alt="<?php echo esc_attr($brand_name); ?>"
                                     loading="lazy">
                            </a>
                        <?php else : ?>
                            <img src="<?php echo esc_url($brand_image['url']); ?>" 
                                 alt="<?php echo esc_attr($brand_name); ?>"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    
                <?php endif;
            endwhile; ?>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('brands_display', 'brands_display_shortcode');