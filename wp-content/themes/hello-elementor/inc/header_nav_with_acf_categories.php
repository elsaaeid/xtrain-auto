<?php
/**
 * Header Navigation with ACF Categories Shortcode
 *
 * Renders primary menu and ACF categories dropdown.
 * Usage: [header_nav]
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function header_nav_with_acf_categories() {

    if ( ! function_exists('get_field') ) return '';

    $categories = get_field('categories_list', 'option'); // repeater

    ob_start(); ?>

    <nav class="header-nav">

        <!-- PRIMARY MENU -->
        <div class="nav-primary">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary_menu',
                'container'      => false,
                'menu_class'     => 'primary-menu',
                'fallback_cb'    => false,
            ]);
            ?>
        </div>

        <!-- ACF CATEGORIES -->
        <?php if ($categories): ?>
        <div class="nav-categories">

            <button class="categories-toggle">
                التصنيفات
                <span class="arrow">▾</span>
            </button>

            <div class="categories-dropdown">
                <?php foreach ($categories as $cat): ?>
                    <a href="#" class="category-item">
                        <img src="<?php echo esc_url($cat['صور_الصنف']['url']); ?>" alt="">
                        <span><?php echo esc_html($cat['اسم_الصنف']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
        <?php endif; ?>

    </nav>

    <?php
    return ob_get_clean();
}
add_shortcode('header_nav', 'header_nav_with_acf_categories');
