<?php
/**
 * Shortcode: Features Display
 * Usage: [features_display]
 * Displays features with image, title, and description from ACF custom fields
 */
function features_display_shortcode() {

    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'feature',
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

    <div class="features-wrapper">
        <div class="features-container">
            <?php while ( $q->have_posts() ) : $q->the_post();
                $post_id = get_the_ID();
                
                $feature_image = get_field('feature_image', $post_id);
                $feature_title = get_field('feature_title', $post_id);
                $feature_desc  = get_field('feature_desc', $post_id);

                // Skip if no title or description
                if ( !$feature_title && !$feature_desc ) {
                    continue;
                }
                ?>

                <div class="feature-item">
                    <?php if ( $feature_image ) : ?>
                        <div class="feature-image">
                            <?php
                            $img_url = $feature_image['url'];
                            $img_ext = strtolower( pathinfo( $img_url, PATHINFO_EXTENSION ) );
                            if ( $img_ext === 'svg' ) {
                                // Try to get SVG contents
                                $svg_path = get_attached_file( $feature_image['ID'] );
                                if ( file_exists( $svg_path ) ) {
                                    $svg_content = file_get_contents( $svg_path );
                                    // Add a class for GSAP animation
                                    $svg_content = preg_replace('/<svg(\s|>)/', '<svg class="gsap-feature-svg"$1', $svg_content, 1);
                                    echo $svg_content;
                                } else {
                                    // fallback to <img>
                                    echo '<img class="gsap-feature-svg" src="' . esc_url($img_url) . '" alt="' . esc_attr($feature_title ? $feature_title : get_the_title()) . '" loading="lazy">';
                                }
                            } else {
                                // fallback to <img>
                                echo '<img class="gsap-feature-svg" src="' . esc_url($img_url) . '" alt="' . esc_attr($feature_title ? $feature_title : get_the_title()) . '" loading="lazy">';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="feature-content">
                        <?php if ( $feature_title ) : ?>
                            <h3 class="feature-title"><?php echo esc_html($feature_title); ?></h3>
                        <?php endif; ?>

                        <?php if ( $feature_desc ) : ?>
                            <p class="feature-desc"><?php echo esc_html($feature_desc); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('features_display', 'features_display_shortcode');
