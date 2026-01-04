<?php
/**
 * Shortcode: Newsletter Section
 * Usage: [newsletter_section]
 * Displays newsletter section from ACF custom fields
 * ACF Fields: Newsletter_image, Newsletter_title, Newsletter_desc
 */
function newsletter_section_shortcode() {

    if (!function_exists('get_field')) {
        return '';
    }

    $args = array(
        'post_type'      => 'newsletter',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '';
    }

    ob_start();

    while ($q->have_posts()) : $q->the_post();

        $post_id = get_the_ID();

        // Get ACF fields
        $newsletter_image = get_field('newsletter_image', $post_id);
        $newsletter_title = get_field('newsletter_title', $post_id);
        $newsletter_desc  = get_field('newsletter_desc', $post_id);
        ?>

        <div class="newsletter-wrapper">
            <div class="newsletter-container">
                
                <?php if ($newsletter_image) : ?>
                    <div class="newsletter-image">
                        <img src="<?php echo esc_url($newsletter_image['url']); ?>" 
                             alt="<?php echo esc_attr($newsletter_title ? $newsletter_title : 'Newsletter'); ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>
                
                <div class="newsletter-content">
                    <?php if ($newsletter_title) : ?>
                        <h2 class="newsletter-title"><?php echo esc_html($newsletter_title); ?></h2>
                    <?php endif; ?>
                    
                    <?php if ($newsletter_desc) : ?>
                        <p class="newsletter-desc"><?php echo esc_html($newsletter_desc); ?></p>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

        <?php
    endwhile;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('newsletter_section', 'newsletter_section_shortcode');
