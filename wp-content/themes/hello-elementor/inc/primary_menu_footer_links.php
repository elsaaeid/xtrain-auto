<?php
/**
 * Shortcode: Primary Menu Links for Footer
 * Usage: [primary_menu_footer_links]
 * Displays the primary menu (menu-1) with an 'active-menu-link' class on the current page.
 */
function primary_menu_footer_links_shortcode() {
    // Current URL used to mark active links (normalize trailing slash)
    $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $current_url = rtrim( $current_url, '/' );
    
    $locations = get_nav_menu_locations();
    if ( ! isset( $locations['menu-1'] ) ) {
        return '';
    }
    
    $menu_items = wp_get_nav_menu_items( $locations['menu-1'] );
    if ( ! $menu_items ) {
        return '';
    }
    
    ob_start();
    ?>
    <ul class="footer-primary-links-list">
        <?php foreach ( $menu_items as $item ) : 
            $item_url = rtrim( $item->url, '/' );
            $is_active = ( $item_url === $current_url );
            $link_class = $is_active ? 'active-menu-link' : '';
        ?>
            <li class="footer-primary-link-item <?php echo $is_active ? 'is-active-item' : ''; ?>">
                <a href="<?php echo esc_url( $item->url ); ?>" class="<?php echo esc_attr( $link_class ); ?>">
                    <?php echo esc_html( $item->title ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
    return ob_get_clean();
}
add_shortcode('primary_menu_footer_links', 'primary_menu_footer_links_shortcode');