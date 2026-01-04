<?php
/**
 * Shortcode: Fast Parts Search
 * Usage: [fast_parts_search]
 */
function fast_parts_search_shortcode() {

    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'fast_search_part',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );

    $q = new WP_Query($args);
    if ( ! $q->have_posts() ) {
        return '';
    }

    ob_start();

    while ( $q->have_posts() ) : $q->the_post();

        $post_id  = get_the_ID();

        $title    = get_field('fps_title', $post_id);
        $subtitle = get_field('fps_subtitle', $post_id);
        $f1       = get_field('fps_field1', $post_id);
        $f2       = get_field('fps_field2', $post_id);
        $f3       = get_field('fps_field3', $post_id);
        $f4       = get_field('fps_field4', $post_id);
        $btn      = get_field('fps_button', $post_id);
        ?>

        <div class="fps-wrapper">
            <div class="fps-card">

                <?php if ($title): ?>
                    <h3 class="fps-title"><?php echo esc_html($title); ?></h3>
                <?php endif; ?>

                <?php if ($subtitle): ?>
                    <p class="fps-subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>

                <form class="fps-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">

                    <div class="fps-input">
                        <span>01</span>
                        <input type="text" name="type" placeholder="<?php echo esc_attr($f1); ?>">
                    </div>

                    <div class="fps-input">
                        <span>02</span>
                        <input type="text" name="location" placeholder="<?php echo esc_attr($f2); ?>">
                    </div>

                    <div class="fps-input">
                        <span>03</span>
                        <input type="number"
                            name="year"
                            placeholder="<?php echo esc_attr($f3); ?>"
                            min="1900"
                            max="<?php echo esc_attr( date('Y') ); ?>"
                            step="1">
                    </div>
                    <div class="fps-input">
                        <span>04</span>
                        <input type="text" name="model" placeholder="<?php echo esc_attr($f4); ?>">
                    </div>

                    <?php if ($btn): ?>
                        <button type="submit"><?php echo esc_html($btn); ?></button>
                    <?php endif; ?>

                </form>
            </div>
        </div>

        <?php
    endwhile;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('fast_parts_search', 'fast_parts_search_shortcode');