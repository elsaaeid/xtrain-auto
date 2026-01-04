<?php
/**
 * Shortcode: ACF Categories List
 * Usage: [acf_categories_list]
 * Displays categories from ACF fields: اسم_الصنف, صور_الصنف, عدد_الاصناف
 * in a grid layout similar to the uploaded image.
 */
function acf_categories_list_shortcode() {
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
    <div class="acf-categories-list-wrapper" style="width:100%;margin:0 auto;">
        <div class="acf-categories-list-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;">
            <?php while ( $q->have_posts() ) : $q->the_post();
                $post_id = get_the_ID();
                $cat_name  = get_field('اسم_الصنف', $post_id);
                $cat_img   = get_field('صور_الصنف', $post_id);
                $cat_count = get_field('عدد_الاصناف', $post_id);
                if ( !$cat_name ) continue;
                $cat_url = get_permalink($post_id);
            ?>
            <a href="<?php echo esc_url($cat_url); ?>" class="acf-category-card category-card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 12px 18px 12px;background:#fff;border:1px solid #e5e9f2;border-radius:10px;text-align:center;transition:box-shadow 0.2s,border-color 0.2s;text-decoration:none;min-height:220px;">
                <?php if ($cat_img) : ?>
                    <div class="category-image" style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                        <img src="<?php echo esc_url($cat_img['url']); ?>" alt="<?php echo esc_attr($cat_name); ?>" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
                    </div>
                <?php endif; ?>
                <div class="category-info">
                    <div class="category-name" style="font-weight:700;font-size:18px;color:#232e35;margin-bottom:4px;line-height:1.3;"> <?php echo esc_html($cat_name); ?> </div>
                    <div class="category-count" style="font-size:15px;color:#232e35;opacity:0.7;"> <?php echo esc_html($cat_count); ?> </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('acf_categories_list', 'acf_categories_list_shortcode');
