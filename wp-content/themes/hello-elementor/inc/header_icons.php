<?php
/**
 * Header Icons Shortcode
 * Displays header icons from Custom Post Type
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode: Header Icons from CPT
 * Usage: [header_icons]
 */
function header_icons_from_cpt() {

    $args = [
        'post_type'      => 'icon',
        'posts_per_page' => -1,
        'meta_key'       => 'icon_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC'
    ];

    $q = new WP_Query($args);
    if (!$q->have_posts()) return '';

    /* =========================
       COUNTS
    ========================= */

    // Cart count
    $cart_count = 0;
    if ( function_exists('WC') && WC()->cart ) {
        $cart_count = WC()->cart->get_cart_contents_count();
    }

    // Wishlist count (YITH)
    $wishlist_count = 0;
    if ( function_exists('YITH_WCWL') ) {
        $wishlist_items = YITH_WCWL()->get_products();
        if ( is_array($wishlist_items) ) {
            $wishlist_count = count($wishlist_items);
        }
    }

    ob_start(); ?>

    <div class="header-icons">

        <?php while ($q->have_posts()): $q->the_post();

            $icon_type  = get_field('icon_type');   // cart | wishlist | other
            $show_count = get_field('show_count');

            // Decide which count to show
            $count = 0;
            if ($icon_type === 'cart') {
                $count = $cart_count;
            } elseif ($icon_type === 'wishlist') {
                $count = $wishlist_count;
            }
        ?>

            <a href="<?php echo esc_url( get_field('icon_link') ); ?>" class="icon-btn">
                <i class="<?php echo esc_attr( get_field('icon_class') ); ?>"></i>

                <?php if ( $show_count && is_numeric( $count ) && intval( $count ) > 0 ): ?>
                    <span class="icon-count"><?php echo esc_html( intval( $count ) ); ?></span>
                <?php endif; ?>
            </a>

        <?php endwhile; wp_reset_postdata(); ?>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('header_icons', 'header_icons_from_cpt');
