<?php
/**
 * Shortcode: Car Features Display
 * Usage: [car_features]
 * Displays car features with number, title, and description from ACF custom fields
 * Features are grouped into columns of 3 items each
 */
function car_features_shortcode() {

    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'car_feature',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC'
    );

    $q = new WP_Query($args);
    if ( ! $q->have_posts() ) {
        return '';
    }

    // Collect all features first
    $features = array();
    while ( $q->have_posts() ) : $q->the_post();
        $post_id = get_the_ID();
        
        $feature_number = get_field('feature_number', $post_id);
        $feature_title  = get_field('feature_title', $post_id);
        $feature_desc   = get_field('feature_desc', $post_id);
        
        // Skip if no title
        if ( !$feature_title ) {
            continue;
        }

        $features[] = array(
            'number' => $feature_number ? sprintf('%02d', intval($feature_number)) : '01',
            'title'  => $feature_title,
            'desc'   => $feature_desc
        );
    endwhile;
    wp_reset_postdata();

    if ( empty($features) ) {
        return '';
    }

    // Split features into chunks of 3
    $feature_columns = array_chunk($features, 3);

    ob_start();
    ?>

    <div class="car-features-wrapper">
        <div class="car-features-container">
            <?php foreach ( $feature_columns as $column ) : ?>
                <div class="car-features-card">
                    <?php foreach ( $column as $index => $feature ) : 
                        $is_last = ($index === count($column) - 1);
                    ?>
                        <div class="car-feature-item<?php echo $is_last ? ' last' : ''; ?>">
                            <div class="car-feature-number">
                                <span><?php echo esc_html($feature['number']); ?></span>
                            </div>
                            
                            <div class="car-feature-content">
                                <h3 class="car-feature-title"><?php echo esc_html($feature['title']); ?></h3>
                                
                                <?php if ( $feature['desc'] ) : ?>
                                    <p class="car-feature-desc"><?php echo esc_html($feature['desc']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('car_features', 'car_features_shortcode');