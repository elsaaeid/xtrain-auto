<?php
/**
 * Shortcode: Blogs Slider
 * Usage: [blogs_slider]
 * Displays blogs from ACF custom fields in a slider layout
 * ACF Fields: blog_image, blog_title, blog_desc, blog_author, blog_date
 */
function blogs_slider_shortcode($atts) {

    if (!function_exists('get_field')) {
        return '';
    }

    // Shortcode attributes
    $atts = shortcode_atts(array(
        'limit'   => 8,
        'orderby' => 'date',
        'order'   => 'DESC',
    ), $atts);

    $args = array(
        'post_type'      => 'blog',
        'posts_per_page' => intval($atts['limit']),
        'orderby'        => $atts['orderby'],
        'order'          => $atts['order'],
        'post_status'    => 'publish',
    );

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '';
    }

    ob_start();
    ?>

    <div class="blogs-slider-wrapper">
        <div class="blogs-slider-header">
            <div class="blogs-heading">
                <h2 class="blogs-title">المقالات</h2>
                <a href="#" class="blogs-view-all">مقالات مختارة من المدونة</a>
            </div>
            <div class="blogs-buttons">
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

        <div class="blogs-slider-container">
            <div class="blogs-slider">
                <div class="blogs-slider-track">
                    <?php while ($q->have_posts()) : $q->the_post();
                        $post_id = get_the_ID();
                        
                        // Get ACF fields
                        $blog_image  = get_field('blog_image', $post_id);
                        $blog_title  = get_field('blog_title', $post_id);
                        $blog_desc   = get_field('blog_desc', $post_id);
                        $blog_author = get_field('blog_author', $post_id);
                        $blog_date   = get_field('blog_date', $post_id);
                        
                        // Fallback to post title if no ACF title
                        if (!$blog_title) {
                            $blog_title = get_the_title();
                        }
                        
                        // Fallback to post excerpt if no ACF description
                        if (!$blog_desc) {
                            $blog_desc = get_the_excerpt();
                        }
                        
                        // Fallback to post author if no ACF author
                        if (!$blog_author) {
                            $blog_author = get_the_author();
                        }
                        
                        // Fallback to post date if no ACF date
                        if (!$blog_date) {
                            $blog_date = get_the_date('d F Y');
                        }
                        
                        $blog_url = get_permalink($post_id);
                        ?>
                        
                        <div class="blog-slide">
                            <a href="<?php echo esc_url($blog_url); ?>" class="blog-card">
                                <?php if ($blog_image) : ?>
                                    <div class="blog-image">
                                        <img src="<?php echo esc_url($blog_image['url']); ?>" 
                                             alt="<?php echo esc_attr($blog_title); ?>"
                                             loading="lazy">
                                    </div>
                                <?php elseif (has_post_thumbnail($post_id)) : ?>
                                    <div class="blog-image">
                                        <?php echo get_the_post_thumbnail($post_id, 'medium_large'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-content">
                                    <?php if ($blog_title) : ?>
                                        <h3 class="blog-card-title"><?php echo esc_html($blog_title); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if ($blog_desc) : ?>
                                        <p class="blog-desc"><?php echo esc_html(wp_trim_words($blog_desc, 20)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="blog-meta">
                                        <span class="blog-author">
                                            <span class="blog-author-label">by</span>
                                            <span class="blog-author-name"><?php echo esc_html($blog_author); ?></span>
                                        </span>
                                        <span class="blog-date"><?php echo esc_html($blog_date); ?></span>
                                    </div>
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
add_shortcode('blogs_slider', 'blogs_slider_shortcode');
