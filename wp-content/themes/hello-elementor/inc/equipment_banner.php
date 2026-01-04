<?php
/**
 * Shortcode: Equipment Banner
 * Usage: [equipment_banner]
 * Displays a banner using ACF fields: banner_image, banner_thumbnail, banner_title, banner_desc, button_url, button_text
 */
function equipment_banner_shortcode() {
    if ( ! function_exists('get_field') ) {
        return '';
    }

    // Query latest published equipment_banner post
    $args = array(
        'post_type'      => 'equipment_banner',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );
    $q = new WP_Query($args);
    if ( ! $q->have_posts() ) {
        return '';
    }

    ob_start();
    while ( $q->have_posts() ) : $q->the_post();
        $post_id        = get_the_ID();
        $banner_image   = get_field('banner_image', $post_id);
        $banner_thumb   = get_field('banner_thumbnail', $post_id);
        $banner_title   = get_field('banner_title', $post_id);
        $banner_desc    = get_field('banner_desc', $post_id);
        $button_url     = get_field('button_url', $post_id);
        $button_text    = get_field('button_text', $post_id);

        // Normalize button URL: allow relative paths and fall back to shop page
        $button_link = $button_url;
        if (!empty($button_link)) {
            // If it's a relative path (no scheme), prepend home_url
            $parsed = wp_parse_url($button_link);
            if (empty($parsed['scheme']) && !str_starts_with($button_link, '//')) {
                $button_link = home_url( '/' . ltrim($button_link, '/' ) );
            }
        }
        if (empty($button_link)) {
            $button_link = wc_get_page_permalink('shop');
            if (!$button_link || $button_link === home_url()) {
                $shop_id = wc_get_page_id('shop');
                $button_link = ($shop_id > 0) ? get_permalink($shop_id) : home_url('/shop/');
            }
        }

        if ( ! $banner_image ) {
            continue;
        }
        ?>
        <div class="equipment-banner-wrapper">
            <div class="equipment-banner-bg" style="background-image:url('<?php echo esc_url($banner_image['url']); ?>');">
                <div class="equipment-banner-content">
                    <?php if ($banner_thumb): ?>
                        <div class="equipment-banner-thumb">
                            <img src="<?php echo esc_url($banner_thumb['url']); ?>" alt="" />
                        </div>
                    <?php endif; ?>
                    <div class="equipment-banner-text">
                        <div class="equipment-banner-text-inner">
                            <?php if ($banner_title): ?>
                                <h2 class="equipment-banner-title">
                                    <?php
                                    // Highlight the word 'قوية' in orange
                                    if ($banner_title && strpos($banner_title, 'قوية') !== false) {
                                        // Only highlight the first occurrence
                                        $highlighted = preg_replace('/(قوية)/u', '<span style="color:var(--color-primary);">$1</span>', esc_html($banner_title), 1);
                                        echo $highlighted;
                                    } else {
                                        echo esc_html($banner_title);
                                    }
                                    ?>
                                </h2>
                            <?php endif; ?>
                            <?php if ($banner_desc): ?>
                                <div class="equipment-banner-desc">
                                    <?php echo esc_html($banner_desc); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($button_url && $button_text): ?>
                            <a href="<?php echo esc_url($button_url); ?>" class="equipment-banner-btn">
                                <?php echo esc_html($button_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('equipment_banner', 'equipment_banner_shortcode');
