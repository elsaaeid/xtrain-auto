<?php
/**
 * Shortcode: Categories Slider
 * Usage: [categories_slider]
 * Displays categories with image, name, and count in a slider
 */
function categories_slider_shortcode() {

    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'category_menu',
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

    <div class="categories-slider-wrapper">
        <div class="categories-slider-header">
            <div class="categories-heading">
                <h2 class="categories-title">الأقسام</h2>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('categories'))); ?>" class="categories-view-all">تصفح الأقسام الفرعية</a>
            </div>
            <div class="categories-buttons">
                <button class="slider-nav slider-next" aria-label="Next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="slider-nav slider-prev" aria-label="Previous">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="categories-slider-container">
            <div class="categories-slider">
                <div class="categories-slider-track">
                    <?php while ( $q->have_posts() ) : $q->the_post();
                        $post_id = get_the_ID();
                        
                        $category_name  = get_field('اسم_الصنف', $post_id);
                        $category_image = get_field('صور_الصنف', $post_id);
                        $category_count = get_field('عدد_الاصناف', $post_id);
                        
                        // Skip if no name
                        if ( !$category_name ) {
                            continue;
                        }
                        
                        $category_url = get_permalink($post_id);
                        ?>
                        
                        <div class="category-slide">
                            <a href="<?php echo esc_url($category_url); ?>" class="category-card">
                                <?php if ( $category_image ) : ?>
                                    <div class="category-image">
                                        <img src="<?php echo esc_url($category_image['url']); ?>" 
                                             alt="<?php echo esc_attr($category_name); ?>"
                                             loading="lazy">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="category-info">
                                    <h3 class="category-name"><?php echo esc_html($category_name); ?></h3>
                                    <?php if ( $category_count ) : ?>
                                        <span class="category-count"><?php echo esc_html($category_count); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('categories_slider', 'categories_slider_shortcode');