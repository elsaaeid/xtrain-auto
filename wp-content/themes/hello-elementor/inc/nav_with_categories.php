<?php
/**
 * Shortcode: Primary Menu + Categories (ACF)
 * Usage: [nav_with_categories]
 */

add_action('init', function () {

    // Shortcode
    add_shortcode('nav_with_categories', function () {

        ob_start();

        // Current URL used to mark active links (normalize trailing slash)
        $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $current_url = rtrim( $current_url, '/' );

        ?>

        <nav class="header-nav">

            <!-- Categories Dropdown (First in DOM, Right in RTL) -->
            <div class="nav-categories">
                <button class="categories-toggle">
                    <span class="toggle-text desktop-text">الأقسام</span>
                    <span class="toggle-text mobile-text">القائمة</span>
                    
                    <!-- Menu Icon (Hamburger) -->
                    <svg class="toggle-icon icon-menu" width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1H19" stroke="#F47C33" stroke-width="2" stroke-linecap="round"/>
                        <path d="M1 7H19" stroke="#F47C33" stroke-width="2" stroke-linecap="round"/>
                        <path d="M1 13H19" stroke="#F47C33" stroke-width="2" stroke-linecap="round"/>
                    </svg>

                    <!-- Close Icon (X) -->
                    <svg class="toggle-icon icon-close" style="display: none;" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path d="M15 1L1 15M1 1l14 14" stroke="#F47C33" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <div class="categories-dropdown">
                    <!-- Desktop Categories List -->
                    <div class="categories-list desktop-only">
                        <?php
                        $cats = new WP_Query(array(
                            'post_type'      => 'category_menu',
                            'posts_per_page' => -1,
                            'post_status'    => 'publish'
                        ));
                        if ($cats->have_posts()):
                            while ($cats->have_posts()): $cats->the_post();
                                $name    = get_field('اسم_الصنف');
                                $image   = get_field('صور_الصنف');
                                $permalink = rtrim( get_permalink(), '/' );
                                $a_classes = array( 'category-item' );
                                if ( $permalink === $current_url ) {
                                    $a_classes[] = 'active-menu-link';
                                }
                                ?>
                                <a href="<?php echo esc_url( $permalink ); ?>" class="<?php echo esc_attr( implode( ' ', $a_classes ) ); ?>">
                                    <?php if ( $image ): ?>
                                        <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                                    <?php endif; ?>
                                    <span><?php echo esc_html( $name ); ?></span>
                                </a>
                            <?php endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                    <!-- Mobile Menu Items (Visible only on Mobile) -->
                    <ul class="mobile-menu-items">
                        <?php
                        $has_sale = function_exists('has_sale_products') && has_sale_products();
                        $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()['menu-1']);
                        $bestseller_title = 'الأكثر مبيعا';
                        if ($menu_items) {
                            foreach ($menu_items as $item) {
                                $is_bestseller = trim($item->title) === $bestseller_title;
                                $li_class = $is_bestseller && $has_sale ? ' class="menu-item-sale"' : '';
                                echo '<li' . $li_class . '><a href="' . esc_url($item->url) . '">' . esc_html($item->title);
                                if ($is_bestseller && $has_sale) {
                                    echo ' <span class="sale-badge">Sale</span>';
                                }
                                echo '</a></li>';
                            }
                        }
                        ?>
                        <!-- Mobile Sub-Menu Toggle for Categories -->
                        <li class="mobile-cat-wrapper">
                            <button class="mobile-cat-toggle">
                                <span>الأقسام</span>
                                <svg class="arrow-icon" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <div class="categories-list mobile-only">
                                <?php
                                $cats = new WP_Query(array(
                                    'post_type'      => 'category_menu',
                                    'posts_per_page' => -1,
                                    'post_status'    => 'publish'
                                ));
                                if ($cats->have_posts()):
                                    while ($cats->have_posts()): $cats->the_post();
                                        $name      = get_field('اسم_الصنف');
                                        $image     = get_field('صور_الصنف');
                                        $permalink = rtrim( get_permalink(), '/' );
                                        $a_classes = array( 'category-item' );
                                        if ( $permalink === $current_url ) {
                                            $a_classes[] = 'active-menu-link';
                                        }
                                        ?>
                                        <a href="<?php echo esc_url( $permalink ); ?>" class="<?php echo esc_attr( implode( ' ', $a_classes ) ); ?>">
                                            <?php if ( $image ): ?>
                                                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                                            <?php endif; ?>
                                            <span><?php echo esc_html( $name ); ?></span>
                                        </a>
                                    <?php endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Primary Menu (Desktop) -->
            <ul class="primary-menu desktop-menu">
                <?php
                $has_sale = function_exists('has_sale_products') && has_sale_products();
                $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()['menu-1']);
                $bestseller_title = 'الأكثر مبيعا';
                $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $current_hash = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
                $current_fragment = '';
                if (strpos($current_url, '#') !== false) {
                    $current_fragment = substr($current_url, strpos($current_url, '#'));
                }
                if ($menu_items) {
                    foreach ($menu_items as $item) {
                        $is_bestseller = trim($item->title) === $bestseller_title;
                        $li_classes = [];
                        $a_classes = [];
                        $item_url = rtrim($item->url, '/');
                        $is_active = false;
                        // If menu item is an in-page anchor (starts with '#')
                        if (strpos($item_url, '#') === 0) {
                            // If current URL fragment matches the anchor, mark active
                            if ($current_fragment && $item_url === $current_fragment) {
                                $is_active = true;
                            } else {
                                // If we're on the site's home page, treat common anchors as active
                                $current_path = rtrim( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
                                $home_url_trim = rtrim( home_url( '/' ), '/' );
                                if ( $current_path === $home_url_trim && ! $current_fragment && in_array( $item_url, array( '#home', '#top' ), true ) ) {
                                    $is_active = true;
                                }
                            }
                        } else {
                            // Full URL links: exact match
                            if ( $item_url === rtrim( $current_url, '/' ) ) {
                                $is_active = true;
                            }
                            // Also consider links pointing to the site home active when we're on home
                            if ( $item_url === rtrim( home_url( '/' ), '/' ) ) {
                                $current_path = rtrim( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
                                if ( $current_path === rtrim( home_url( '/' ), '/' ) ) {
                                    $is_active = true;
                                }
                            }
                        }
                        if ($is_bestseller && $has_sale) $li_classes[] = 'menu-item-sale';
                        if ($is_active) $a_classes[] = 'active-menu-link';
                        $li_class_attr = $li_classes ? ' class="' . implode(' ', $li_classes) . '"' : '';
                        $a_class_attr = $a_classes ? ' class="' . implode(' ', $a_classes) . '"' : '';
                        echo '<li' . $li_class_attr . '><a' . $a_class_attr . ' href="' . esc_url($item->url) . '">' . esc_html($item->title);
                        if ($is_bestseller && $has_sale) {
                            echo ' <span class="sale-badge">Sale</span>';
                        }
                        echo '</a></li>';
                    }
                }
                ?>
            </ul>

        </nav>

        <?php
        return ob_get_clean();
    });
});
