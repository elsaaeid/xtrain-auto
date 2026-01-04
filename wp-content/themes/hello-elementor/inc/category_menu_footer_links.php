<?php
/**
 * Shortcode: Category Menu Links for Footer
 * Usage: [category_menu_footer_links]
 * Displays links from the 'category_menu' post type with an 'active-menu-link' class on the current page.
 */
function category_menu_footer_links_shortcode() {
    // Current URL used to mark active links (normalize trailing slash)
    $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $current_url = rtrim( $current_url, '/' );

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
    <ul class="footer-category-links-list">
        <?php while ( $q->have_posts() ) : $q->the_post(); 
            $post_id = get_the_ID();
            $name = get_field('اسم_الصنف', $post_id) ?: get_the_title($post_id);
            $item_url = rtrim( get_permalink($post_id), '/' );
            $is_active = ( $item_url === $current_url );
            $link_class = $is_active ? 'active-menu-link' : '';
        ?>
            <li class="footer-category-link-item <?php echo $is_active ? 'is-active-item' : ''; ?>">
                <a href="<?php echo esc_url( get_permalink($post_id) ); ?>" class="<?php echo esc_attr( $link_class ); ?>">
                    <?php echo esc_html( $name ); ?>
                </a>
            </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>
    <?php
    return ob_get_clean();
}
add_shortcode('category_menu_footer_links', 'category_menu_footer_links_shortcode');
