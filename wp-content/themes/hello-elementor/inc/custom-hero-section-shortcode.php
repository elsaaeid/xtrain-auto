<?php
function custom_hero_section_shortcode() {
    // Query the latest published 'hero_section' post
    $args = array(
        'post_type'      => 'hero_section',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );
    $hero_query = new WP_Query($args);

    if ($hero_query->have_posts()) {
        $hero_query->the_post();
        $post_id = get_the_ID();
        $hero_image = get_field('hero_image', $post_id);
        $title = get_field('title', $post_id);
        $name = get_field('name', $post_id); // New field for name
        $description = get_field('description', $post_id);
        $button_text = get_field('button_text', $post_id);
        $button_url = get_field('button_url', $post_id);

        ob_start();
        ?>
        <section class="hero-section" style="background-image: url('<?php echo esc_url($hero_image ? $hero_image['url'] : ''); ?>');">
            <div class="hero-search">
                <?php echo do_shortcode('[fast_parts_search]'); ?>
            </div>
            <div class="hero-content">
                <?php if ($name): ?>
                    <div class="hero-name">
                        <?php echo esc_html($name); ?>
                    </div>
                <?php endif; ?>
                <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                <p class="hero-description"><?php echo esc_html($description); ?></p>
                <?php if ($button_text && $button_url): ?>
                    <a href="<?php echo esc_url($button_url); ?>" class="hero-button"><?php echo esc_html($button_text); ?></a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    return '';
}
add_shortcode('hero_section', 'custom_hero_section_shortcode');



