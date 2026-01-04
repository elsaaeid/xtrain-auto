<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.5' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

        if ( hello_elementor_display_header_footer() ) {
            wp_enqueue_style(
                'hello-elementor-header-footer',
                HELLO_THEME_STYLE_URL . 'header-footer.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }

        // Enqueue single-product.css
        wp_enqueue_style(
            'single-product-style',
            HELLO_THEME_STYLE_URL . 'single-product.css',
            [],
            HELLO_ELEMENTOR_VERSION
        );
        
        // Global Cart Actions (Plus/Minus/Trash logic for all grids)
        wp_enqueue_script( 'global-cart-actions', get_template_directory_uri() . '/assets/js/global-product-actions.js', ['jquery'], time(), true );
        wp_localize_script( 'global-cart-actions', 'global_cart_params', [
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'site_url'    => site_url() 
        ]);

        // Star Rating Handler for Product Reviews
        wp_enqueue_script( 'star-rating-handler', get_template_directory_uri() . '/assets/js/star-rating.js', [], '1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles', 99 );

// Cart logic is now handled in woocommerce/cart/cart.php template

/**
 * Empty Cart Handler
 */
add_action( 'init', function() {
    if ( isset( $_GET['empty-cart'] ) ) {
        WC()->cart->empty_cart();
        wp_redirect( wc_get_cart_url() );
        exit;
    }
});


/**
 * Add Plus Minus buttons globally as fallback
 */

/**
 * Server-side handler for updating cart item quantity via wc-ajax=update_item_qty
 * Provides a resilient endpoint used by the theme JS to increment/decrement or remove items.
 */
add_action( 'wc_ajax_update_item_qty', 'hello_update_item_qty' );
add_action( 'wc_ajax_nopriv_update_item_qty', 'hello_update_item_qty' );
function hello_update_item_qty() {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        wp_send_json_error( array( 'error' => 'woocommerce_not_available' ) );
    }

    $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
    $qty = isset( $_POST['qty'] ) ? intval( $_POST['qty'] ) : 0;
    $cart_item_key = isset( $_POST['cart_item_key'] ) ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) ) : '';

    $updated = false;

    // Prefer provided cart_item_key when available
    if ( $cart_item_key && isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
        if ( $qty > 0 ) {
            $updated = WC()->cart->set_quantity( $cart_item_key, $qty, true );
        } else {
            $updated = WC()->cart->remove_cart_item( $cart_item_key );
        }
    } elseif ( $product_id ) {
        // Find first matching cart item for product_id
        foreach ( WC()->cart->get_cart() as $key => $item ) {
            if ( intval( $item['product_id'] ) === $product_id ) {
                if ( $qty > 0 ) {
                    $updated = WC()->cart->set_quantity( $key, $qty, true );
                } else {
                    $updated = WC()->cart->remove_cart_item( $key );
                }
                break;
            }
        }

        // If not found and qty > 0, try adding to cart
        if ( ! $updated && $qty > 0 ) {
            $added = WC()->cart->add_to_cart( $product_id, $qty );
            $updated = (bool) $added;
        }
    }

    if ( $updated ) {
        wp_send_json_success( array( 'cart_hash' => WC()->cart->get_cart_hash() ) );
    }

    wp_send_json_error( array( 'error' => true ) );
}
add_action( 'woocommerce_after_quantity_input_field', function() {
    echo '<button type="button" class="plus q_plus">+</button>';
});
add_action( 'woocommerce_before_quantity_input_field', function() {
    echo '<button type="button" class="minus q_minus">-</button>';
});

/**
 * Handle quantity button clicks via JS
 */
add_action( 'wp_footer', function() {
    if ( ! is_cart() && strpos($_SERVER['REQUEST_URI'], 'cart') === false ) return;
    ?>
    <script type="text/javascript">
    jQuery( function( $ ) {
        $( document ).on( 'click', '.plus, .minus, .q_plus, .q_minus', function() {
            var $container = $( this ).closest( '.quantity, .custom-qty-box, .custom-qty-wrapper' );
            var $qty = $container.find( '.qty' ),

                currentVal = parseFloat( $qty.val() ),
                max = parseFloat( $qty.attr( 'max' ) ),
                min = parseFloat( $qty.attr( 'min' ) ),
                step = $qty.attr( 'step' );

            if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
            if ( max === '' || max === 'NaN' ) max = '';
            if ( min === '' || min === 'NaN' ) min = 0;
            if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

            if ( $( this ).is( '.plus, .q_plus' ) ) {
                if ( max && ( currentVal >= max ) ) {
                    $qty.val( max );
                } else {
                    $qty.val( currentVal + parseFloat( step ) );
                }
            } else {
                if ( min && ( currentVal <= min ) ) {
                    $qty.val( min );
                } else if ( currentVal > 0 ) {
                    $qty.val( currentVal - parseFloat( step ) );
                }
            }

            $qty.trigger( 'change' );
            $( 'button[name="update_cart"]' ).prop( 'disabled', false ).click();
        });
    });
    </script>
    <?php
});


// Include custom_woo_cart
require_once get_template_directory() . '/inc/custom_woo_cart.php';
require_once get_template_directory() . '/inc/ajax-email-check.php';


/**
 * Explicitly access and load the custom cart.php template path
 */
add_filter( 'woocommerce_locate_template', function( $template, $template_name, $template_path ) {

    $custom_path = HELLO_THEME_PATH . '/woocommerce/cart/cart.php';
    if ( 'cart/cart.php' === $template_name && file_exists( $custom_path ) ) {
        return $custom_path;
    }
    return $template;
}, 99, 3 );

/**
 * Force Translation and Layout fixes if template is bypassed
 */
add_filter( 'gettext', function( $translated_text, $text, $domain ) {
    if ( is_cart() || strpos($_SERVER['REQUEST_URI'], 'cart') !== false ) {
        switch ( $text ) {
            case 'Proceed to checkout' :
                $translated_text = 'متابعة الطلب';
                break;
            case 'Cart totals' :
                $translated_text = 'إجمالي الطلبات';
                break;
            case 'Subtotal' :
                $translated_text = 'الإجمالي';
                break;
            case 'Total' :
                $translated_text = 'المجموع الكلي';
                break;
        }
    }

    // My Account & Orders Translations
    if ( is_account_page() ) {
        switch ( $text ) {
            case 'My account':
            case 'My Account':
                $translated_text = 'حسابي';
                break;
            case 'Orders':
                $translated_text = 'الطلبات';
                break;
            case 'Dashboard':
                $translated_text = 'لوحة التحكم';
                break;
            case 'Downloads':
                $translated_text = 'التنزيلات';
                break;
            case 'Addresses':
            case 'Address':
            case 'addresses':
            case 'address':
                $translated_text = 'العناوين';
                break;
            case 'No downloads available yet.':
                $translated_text = 'لا توجد تنزيلات متاحة بعد.';
                break;
            case 'Browse products':
                $translated_text = 'تصفح المنتجات';
                break;
            case 'Account details':
                $translated_text = 'تفاصيل الحساب';
                break;
            case 'Billing address':
                $translated_text = 'عنوان الفواتير';
                break;
            case 'Shipping address':
                $translated_text = 'عنوان الشحن';
                break;
            case 'Edit':
                $translated_text = 'تعديل';
                break;
            case 'Add':
                $translated_text = 'إضافة';
                break;
            case 'Log out':
                $translated_text = 'تسجيل الخروج';
                break;
            case 'First name':
                $translated_text = 'الاسم الأول';
                break;
            case 'Last name':
                $translated_text = 'الاسم الأخير';
                break;
            case 'Display name':
                $translated_text = 'اسم العرض';
                break;
            case 'Email address':
                $translated_text = 'البريد الإلكتروني';
                break;
            case 'This will be how your name will be displayed in the account section and in reviews':
                $translated_text = 'سيكون هذا هو الاسم الذي سيظهر في قسم الحساب وفي المراجعات';
                break;
            case 'Password change':
                $translated_text = 'تغيير كلمة المرور';
                break;
            case 'Current password (leave blank to leave unchanged)':
                $translated_text = 'كلمة المرور الحالية (اتركها فارغة لإبقائها كما هي)';
                break;
            case 'New password (leave blank to leave unchanged)':
                $translated_text = 'كلمة المرور الجديدة (اتركها فارغة لإبقائها كما هي)';
                break;
            case 'Confirm new password':
                $translated_text = 'تأكيد كلمة المرور الجديدة';
                break;
            case 'Save changes':
                $translated_text = 'حفظ التغييرات';
                break;
            case 'Weak - Please enter a stronger password.':
            case 'Weak - Please enter a stronger password':
                $translated_text = 'ضعيف - يرجى إدخال كلمة مرور أقوى.';
                break;
            case 'Very weak - Please enter a stronger password.':
            case 'Very weak - Please enter a stronger password':
                $translated_text = 'ضعيفة جداً - يرجى إدخال كلمة مرور أقوى.';
                break;
            case 'Medium':
                $translated_text = 'متوسطة';
                break;
            case 'Strong':
                $translated_text = 'قوية';
                break;
            case 'Weak':
                $translated_text = 'ضعيفة';
                break;
            case 'Very weak':
                $translated_text = 'ضعيفة جداً';
                break;
            case 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).':
            case 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & )':
                $translated_text = 'نصيحة: يجب أن تتكون كلمة المرور من اثني عشر حرفاً على الأقل. لجعلها أقوى، استخدم الحروف الكبيرة والصغيرة والأرقام والرموز مثل ! " ? $ % ^ & ).';
                break;
            case 'Your order was cancelled':
                $translated_text = 'تم إلغاء طلبك';
                break;
            case 'The following addresses will be used on the checkout page by default.':
                $translated_text = 'سيتم استخدام العناوين التالية في صفحة إتمام الطلب بشكل افتراضي.';
                break;
            case 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)':
                $translated_text = 'مرحباً %1$s (لست %1$s؟ <a href="%2$s">تسجيل الخروج</a>)';
                break;
            case 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.':
                $translated_text = 'من لوحة تحكم حسابك، يمكنك عرض <a href="%1$s">الطلبات الأخيرة</a>، وإدارة <a href="%2$s">عناوين الشحن والفواتير</a>، و<a href="%3$s">تعديل كلمة المرور وتفاصيل حسابك</a>.';
                break;
            case 'Order':
                $translated_text = 'الطلب';
                break;
            case 'Date':
                $translated_text = 'التاريخ';
                break;
            case 'Status':
                $translated_text = 'الحالة';
                break;
            case 'Actions':
                $translated_text = 'الإجراءات';
                break;
            case 'Pending payment':
                $translated_text = 'في انتظار الدفع';
                break;
            case 'Cancelled':
                $translated_text = 'ملغي';
                break;
            case 'Processing':
                $translated_text = 'قيد التنفيذ';
                break;
            case 'Completed':
                $translated_text = 'مكتمل';
                break;
            case 'Pay':
                $translated_text = 'ادفع';
                break;
            case 'View':
                $translated_text = 'عرض';
                break;
            case 'Cancel':
                $translated_text = 'إلغاء';
                break;
        }

        if ( strpos($text, 'item') !== false ) {
            if ( $text === 'for %s item' || $text === '%1$s for %2$s item' ) {
                $translated_text = '%1$s لـ منتج واحد';
            } elseif ( $text === 'for %s items' || $text === '%1$s for %2$s items' ) {
                $translated_text = '%1$s لـ %2$s منتجات';
            }
        }
    }

    // Login/Registration Translations
    switch ( $text ) {
        case '<strong>Error:</strong> The password you entered for the username %s is incorrect.':
            $translated_text = '<strong>خطأ:</strong> كلمة المرور التي أدخلتها لاسم المستخدم %s غير صحيحة.';
            break;
        case '<strong>Error:</strong> The password you entered for the email address %s is incorrect.':
            $translated_text = '<strong>خطأ:</strong> كلمة المرور التي أدخلتها لعنوان البريد الإلكتروني %s غير صحيحة.';
            break;
        case '<strong>Error:</strong> The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.':
            $translated_text = '<strong>خطأ:</strong> اسم المستخدم <strong>%s</strong> غير مسجل في هذا الموقع. إذا كنت غير متأكد من اسم المستخدم الخاص بك، جرب استخدام بريدك الإلكتروني بدلاً من ذلك.';
            break;
        case '<strong>Error:</strong> Unknown username. Check again or try your email address.':
            $translated_text = '<strong>خطأ:</strong> اسم مستخدم غير معروف. تحقق مرة أخرى أو جرب بريدك الإلكتروني.';
            break;
        case '<strong>Error:</strong> Unknown email address. Check again or try your username.':
            $translated_text = '<strong>خطأ:</strong> عنوان بريد إلكتروني غير معروف. تحقق مرة أخرى أو جرب اسم المستخدم الخاص بك.';
            break;
        case '<strong>Error:</strong> The username field is empty.':
            $translated_text = '<strong>خطأ:</strong> حقل اسم المستخدم فارغ.';
            break;
        case '<strong>Error:</strong> The password field is empty.':
            $translated_text = '<strong>خطأ:</strong> حقل كلمة المرور فارغ.';
            break;
        case '<strong>Error:</strong> The email field is empty.':
            $translated_text = '<strong>خطأ:</strong> حقل البريد الإلكتروني فارغ.';
            break;
        case 'Lost your password?':
            $translated_text = 'هل فقدت كلمة المرور؟';
            break;
    }

    return $translated_text;
}, 20, 3 );

/**
 * Translate WooCommerce Password Strength Meter strings (JS-driven)
 */
add_filter( 'woocommerce_get_script_data', function( $params, $handle ) {
    if ( $handle === 'wc-password-strength-meter' ) {
        $params['i18n_password_error'] = 'يرجى إدخال كلمة مرور أقوى.';
        $params['i18n_password_hint']  = 'نصيحة: يجب أن تتكون كلمة المرور من اثني عشر حرفاً على الأقل. لجعلها أقوى، استخدم الحروف الكبيرة والصغيرة والأرقام والرموز مثل ! " ? $ % ^ & ).';
        $params['short']  = 'ضعيفة جداً';
        $params['weak']   = 'ضعيفة';
        $params['medium'] = 'متوسطة';
        $params['strong'] = 'قوية';
    }
    return $params;
}, 10, 2 );

// Force translations for the meter statuses specifically
add_filter( 'woocommerce_password_meter_settings', function( $settings ) {
    $settings['i18n_password_error'] = 'يرجى إدخال كلمة مرور أقوى.';
    $settings['i18n_password_hint']  = 'نصيحة: يجب أن تتكون كلمة المرور من اثني عشر حرفاً على الأقل. لجعلها أقوى، استخدم الحروف الكبيرة والصغيرة والأرقام والرموز مثل ! " ? $ % ^ & ).';
    return $settings;
});



/**
 * Ensure correct dir/lang when Arabic is active via translators.
 */
function hello_is_arabic_context() {
    if ( function_exists( 'pll_current_language' ) ) {
        $slug = pll_current_language( 'slug' );
        if ( $slug && strpos( $slug, 'ar' ) === 0 ) return true;
    }
    if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE === 'ar' ) {
        return true;
    }
    if ( function_exists( 'trp_get_current_language' ) && trp_get_current_language() === 'ar' ) {
        return true;
    }
    if ( function_exists( 'weglot_get_current_language' ) && weglot_get_current_language() === 'ar' ) {
        return true;
    }
    if ( isset( $_COOKIE['googtrans'] ) && preg_match( '#/(?:[a-z-]{2,5})/ar#i', $_COOKIE['googtrans'] ) ) {
        return true;
    }
    $req = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    if ( preg_match( '#(^|/)(ar)(/|\?|$)#i', $req ) ) return true;
    $loc = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
    return ( strpos( strtolower( (string) $loc ), 'ar' ) === 0 );
}

/**
 * Render a localized price string matching the current language context.
 * Uses the plugin's conversion when available and replaces the currency symbol
 * with an Arabic localized symbol when Arabic context is detected.
 */
function hello_localized_price( $amount, $currency = '' ) {
    if ( ! $currency ) {
        $currency = function_exists('WOOMULTI_CURRENCY_F_Data') ? WOOMULTI_CURRENCY_F_Data::get_ins()->get_current_currency() : get_woocommerce_currency();
    }

    // Convert numeric amount using plugin helper if available
    if ( function_exists( 'wmc_get_price' ) ) {
        $amount = wmc_get_price( $amount, $currency );
    }

    $price_html = wc_price( $amount, array( 'currency' => $currency ) );

    // If Arabic site, replace the currency symbol with an Arabic-friendly string
    if ( function_exists( 'hello_is_arabic_context' ) && hello_is_arabic_context() ) {
        // Mapping for common currencies to Arabic symbols/labels
        $arabic_symbols = array(
            'SAR' => 'ر.س',
            'AED' => 'د.إ',
            'KWD' => 'د.ك',
            'BHD' => 'ب.د',
            'OMR' => 'ر.ع',
            'QAR' => 'ر.ق',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        );

        $current_symbol = get_woocommerce_currency_symbol( $currency );
        if ( isset( $arabic_symbols[ $currency ] ) ) {
            $price_html = str_replace( $current_symbol, $arabic_symbols[ $currency ], $price_html );
        }
    }

    return $price_html;
}

add_filter( 'language_attributes', function( $output ) {
    if ( hello_is_arabic_context() ) {
        if ( preg_match( '/lang="[^"]+"/i', $output ) ) {
            $output = preg_replace( '/lang="[^"]+"/i', 'lang="ar"', $output );
        } else {
            $output .= ' lang="ar"';
        }
        if ( preg_match( '/dir="(ltr|rtl)"/i', $output ) ) {
            $output = preg_replace( '/dir="(ltr|rtl)"/i', 'dir="rtl"', $output );
        } else {
            $output .= ' dir="rtl"';
        }
    }
    return $output;
}, 9999 );

add_filter( 'body_class', function( $classes ) {
    if ( hello_is_arabic_context() && ! in_array( 'rtl', $classes, true ) ) {
        $classes[] = 'rtl';
    }
    return $classes;
});

add_action( 'wp_head', function() {
    if ( ! is_admin() ) {
        ?>
        <script>
        (function(){
            try {
                var isAr = <?php echo hello_is_arabic_context() ? 'true' : 'false'; ?>;
                try {
                    var c = document.cookie.match(/(?:^|; )googtrans=([^;]+)/);
                    if (c && /\/(?:[a-z-]{2,5})\/ar/i.test(decodeURIComponent(c[1]||''))) isAr = true;
                } catch(e){}
                if (isAr) {
                    var html = document.documentElement;
                    if (html.getAttribute('dir') !== 'rtl') html.setAttribute('dir','rtl');
                    if ((html.getAttribute('lang')||'').toLowerCase() !== 'ar') html.setAttribute('lang','ar');
                    if (document.body) document.body.classList.add('rtl');
                }
            } catch(e){}
        })();
        </script>
        <?php
    }
}, 99 );

/**
 * Also ensure wc_price output uses correct symbol.
 */
add_filter( 'wc_price', function( $price, $args ) {
    if ( isset( $args['currency'] ) && $args['currency'] === 'SAR' ) {
        $symbol = hello_is_arabic_context() ? 'ر.س.' : 'R.S.';
        $price = str_ireplace( 
            array( '﷼', 'SAR', 'R.S.', 'ر.س.' ),
            $symbol,
            $price
        );
    }
    return $price;
}, 10, 2 );



if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );



// Include sale helper
require_once get_template_directory() . '/inc/sale-helper.php';
/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();


// Enqueue GSAP from local files for global animations
function enqueue_gsap_cdn() {
    // Main GSAP library
    wp_enqueue_script(
        'gsap',
        get_stylesheet_directory_uri() . '/assets/js/gsap.min.js',
        [],
        '3.14.1',
        true
    );

    // GSAP ScrollTrigger plugin
    wp_enqueue_script(
        'gsap-scrolltrigger',
        get_stylesheet_directory_uri() . '/assets/js/ScrollTrigger.min.js',
        ['gsap'],
        '3.14.1',
        true
    );

    // GSAP MotionPath plugin (required when using motionPath tweens)
    wp_enqueue_script(
        'gsap-motionpath',
        get_stylesheet_directory_uri() . '/assets/js/MotionPathPlugin.min.js',
        ['gsap'],
        '3.14.1',
        true
    );

    // Register the MotionPathPlugin with GSAP after it's loaded
    wp_add_inline_script(
        'gsap-motionpath',
        'if ( typeof gsap !== "undefined" && typeof MotionPathPlugin !== "undefined" ) { gsap.registerPlugin(MotionPathPlugin); }'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_gsap_cdn', 5);

function fps_enqueue_assets() {

    wp_enqueue_style(
        'fps-style',
        get_template_directory_uri() . '/assets/css/fast-parts-search.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'fps-script',
        get_template_directory_uri() . '/assets/js/fast-parts-search.js',
        [],
        '1.0',
        true
    );

}

add_action('wp_enqueue_scripts', 'fps_enqueue_assets');

function brands_display_enqueue_assets() {
    wp_enqueue_style(
        'brands-display-style',
        get_template_directory_uri() . '/assets/css/brands-display.css',
        [],
        '1.0'
    );
}

add_action('wp_enqueue_scripts', 'brands_display_enqueue_assets');

function features_display_enqueue_assets() {
    wp_enqueue_style(
        'features-display-style',
        get_template_directory_uri() . '/assets/css/features-display.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'features-display-script',
        get_template_directory_uri() . '/assets/js/features-display.js',
        ['gsap'], // Depends on GSAP
        '1.0',
        true
    );

    // Enqueue GSAP SVG animation for features
    wp_enqueue_script(
        'gsap-features-svg',
        get_template_directory_uri() . '/assets/js/gsap-features-svg.js',
        ['gsap'],
        filemtime(get_template_directory() . '/assets/js/gsap-features-svg.js'),
        true
    );
}

add_action('wp_enqueue_scripts', 'features_display_enqueue_assets');

function categories_slider_enqueue_assets() {
    wp_enqueue_style(
        'categories-slider-style',
        get_template_directory_uri() . '/assets/css/categories-slider.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'categories-slider-script',
        get_template_directory_uri() . '/assets/js/categories-slider.js',
        ['gsap'], // Depends on GSAP
        '1.0',
        true
    );
}

add_action('wp_enqueue_scripts', 'categories_slider_enqueue_assets');

function products_slider_enqueue_assets() {
    wp_enqueue_style(
        'products-slider-style',
        get_template_directory_uri() . '/assets/css/products-slider.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'products-slider-script',
        get_template_directory_uri() . '/assets/js/products-slider.js',
        ['jquery', 'gsap'], // Depends on jQuery and GSAP
        '1.0',
        true
    );
}

add_action('wp_enqueue_scripts', 'products_slider_enqueue_assets');

function products_list_enqueue_assets() {
    wp_enqueue_style(
        'products-list-style',
        get_template_directory_uri() . '/assets/css/products-list.css',
        [],
        '1.0'
    );
}

// Hide default products and components on Shop page
add_action( 'wp', 'custom_remove_default_shop_elements', 20 );
function custom_remove_default_shop_elements() {
    if ( is_shop() ) {
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
        remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
    }
}

// Force empty query on Shop to prevent default loop
add_action( 'pre_get_posts', 'custom_empty_shop_query', 9999 );
function custom_empty_shop_query( $q ) {
    if ( ! is_admin() && $q->is_main_query() && $q->is_shop() ) {
        $q->set( 'post__in', array(0) );
    }
}

// Suppress "No products found" message
add_action( 'woocommerce_no_products_found', 'custom_remove_no_products_msg', 1 );
function custom_remove_no_products_msg() {
    if ( is_shop() ) {
        remove_action( 'woocommerce_no_products_found', 'wc_no_products_found_message', 10 );
    }
}

// CSS Fallback to strictly hide standard grid if specific conditions leak through
add_action('wp_head', function() {
    if ( is_shop() ) {
        echo '<style>
            .entry-content + .woocommerce-notices-wrapper,
            .entry-content + .products,
            .woocommerce ul.products:not(.full-products-grid),
            .woocommerce-products-header__title.page-title,
            .page-description > h2 {
                display: none !important;
            }
        </style>';
    }
});

// Remove Page Title
add_filter( 'woocommerce_show_page_title', 'custom_hide_shop_page_title' );
function custom_hide_shop_page_title( $title ) {
    if ( is_shop() ) return false;
    return $title;
}
add_action('wp_enqueue_scripts', 'products_list_enqueue_assets');

function car_features_enqueue_assets() {
    wp_enqueue_style(
        'car-features-style',
        get_template_directory_uri() . '/assets/css/car-features.css',
        [],
        '1.0'
    );
}

add_action('wp_enqueue_scripts', 'car_features_enqueue_assets');

function products_grid_enqueue_assets() {
    wp_enqueue_style(
        'products-grid-style',
        get_template_directory_uri() . '/assets/css/products-grid.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'products-grid-script',
        get_template_directory_uri() . '/assets/js/products-grid.js',
        ['jquery'],
        '1.0',
        true
    );
}

add_action('wp_enqueue_scripts', 'products_grid_enqueue_assets');

// Enqueue Equipment Banner CSS
function equipment_banner_enqueue_assets() {
    wp_enqueue_style(
        'equipment-banner-style',
        get_template_directory_uri() . '/assets/css/equipment-banner.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'equipment_banner_enqueue_assets');

// ENABLE ACF OPTIONS PAGE (REQUIRED)
if ( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' => 'Fast Parts Settings',
        'menu_title' => 'Fast Parts',
        'menu_slug'  => 'fast-parts-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    ));
}
// Enqueue Contact Info Section CSS
function contact_info_enqueue_assets() {
    wp_enqueue_style(
        'contact-info-style',
        get_template_directory_uri() . '/assets/css/contact-info.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'contact_info_enqueue_assets');

function header_search_assets() {

    wp_enqueue_style(
        'header-search-style',
        get_template_directory_uri() . '/assets/css/header-search.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'header-search-script',
        get_template_directory_uri() . '/assets/js/header-search.js',
        [],
        '1.0',
        true
    );

    wp_localize_script(
        'header-search-script',
        'header_search',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
}
add_action('wp_enqueue_scripts', 'header_search_assets');


function header_icons_assets() {

    wp_enqueue_style(
        'header-icons-style',
        get_template_directory_uri() . '/assets/css/header-icons.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'hello-header-icons',
        get_template_directory_uri() . '/assets/js/header-icons.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script(
        'hello-header-icons',
        'hello_header_icons',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('hello_header_icons_nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'header_icons_assets');

// AJAX endpoint to return current wishlist count (uses YITH functions if available)
add_action('wp_ajax_hello_get_wishlist_count', 'hello_get_wishlist_count');
add_action('wp_ajax_nopriv_hello_get_wishlist_count', 'hello_get_wishlist_count');
function hello_get_wishlist_count() {
    // Default
    $count = 0;

    // Try YITH helper functions (multiple fallbacks)
    try {
        if (function_exists('yith_wcwl_count_products')) {
            $count = intval( yith_wcwl_count_products() );
        } elseif (function_exists('yith_wcwl_get_wishlist')) {
            $wl = yith_wcwl_get_wishlist();
            if (is_array($wl)) $count = count($wl);
        } elseif (class_exists('YITH_WCWL') && function_exists('YITH_WCWL')) {
            // try calling method if available
            $obj = null;
            try { $obj = YITH_WCWL(); } catch (Exception $e) { $obj = null; }
            if ($obj && is_object($obj) && method_exists($obj, 'count_products')) {
                $count = intval( $obj->count_products() );
            }
        }
    } catch (Exception $e) {
        $count = 0;
    }

    wp_send_json_success(array('count' => $count));
}

// AJAX endpoint to return current cart count
add_action('wp_ajax_hello_get_cart_count', 'hello_get_cart_count');
add_action('wp_ajax_nopriv_hello_get_cart_count', 'hello_get_cart_count');
function hello_get_cart_count() {
    $count = 0;
    if ( function_exists( 'WC' ) && WC()->cart ) {
        $count = WC()->cart->get_cart_contents_count();
    }
    wp_send_json_success(array('count' => $count));
}

// AJAX endpoint to retrieve the cart sidebar HTML (fragments refresh equivalent for custom sidebar)
add_action('wp_ajax_hello_get_cart_sidebar', 'hello_get_cart_sidebar');
add_action('wp_ajax_nopriv_hello_get_cart_sidebar', 'hello_get_cart_sidebar');
function hello_get_cart_sidebar() {
    // 1. Ensure WooCommerce is loaded
    if ( ! class_exists( 'WooCommerce' ) ) wp_die();
    
    // 2. Load Frontend Includes if needed
    $wc_path = WC()->plugin_path();
    include_once $wc_path . '/includes/wc-frontend-functions.php';
    
    // 3. Initialize Session & Cart if needed
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) WC()->initialize_session();
        if ( ! WC()->customer ) WC()->customer = new WC_Customer( get_current_user_id(), true );
        if ( ! WC()->cart ) {
             WC()->cart = new WC_Cart();
             WC()->cart->get_cart();
        }
    }
    
    // Calculate totals to ensure accuracy
    WC()->cart->calculate_totals();

    ob_start();
    get_template_part('template-parts/cart-sidebar');
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}

// AJAX endpoint to return wishlist product IDs for syncing button states
add_action('wp_ajax_hello_get_wishlist_product_ids', 'hello_get_wishlist_product_ids');
add_action('wp_ajax_nopriv_hello_get_wishlist_product_ids', 'hello_get_wishlist_product_ids');
function hello_get_wishlist_product_ids() {
    // 1. Initialize WC Session if needed (Crucial for guest users)
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
    }

    $product_ids = array();
    
    try {
        if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
            $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
            if ( $wishlist && $wishlist->has_items() ) {
                $items = $wishlist->get_items();
                foreach ( $items as $item ) {
                    if ( is_object( $item ) && method_exists( $item, 'get_product_id' ) ) {
                        $product_ids[] = $item->get_product_id();
                    }
                }
            }
            
            // Fallback: If factory returned nothing (common in some session states), try the global function
            if ( empty($product_ids) && function_exists('YITH_WCWL') ) {
                 $raw_items = YITH_WCWL()->get_products( [ 'is_default' => true ] );
                 if ( is_array( $raw_items ) ) {
                    foreach ( $raw_items as $item ) {
                        if ( isset( $item['prod_id'] ) ) $product_ids[] = $item['prod_id'];
                        elseif ( isset( $item['product_id'] ) ) $product_ids[] = $item['product_id'];
                    }
                 }
            }
        } elseif ( function_exists( 'YITH_WCWL' ) ) {
            $items = YITH_WCWL()->get_products( [ 'is_default' => true ] );
            if ( is_array( $items ) ) {
                foreach ( $items as $item ) {
                    if ( isset( $item['prod_id'] ) ) {
                        $product_ids[] = $item['prod_id'];
                    }
                }
            }
        }
    } catch ( Exception $e ) {
        // Return empty array on error
    }
    
    wp_send_json_success( $product_ids );
}

function weglot_switcher_assets() {
	wp_enqueue_script(
		'weglot-switcher-script',
		get_template_directory_uri() . '/assets/js/weglot-switcher.js',
		[],
		'1.0',
		true
	);
}
add_action('wp_enqueue_scripts', 'weglot_switcher_assets');

// Remove WeGlot missing CSS file if referenced to avoid 404 noise
function hello_remove_weglot_front_css() {
    global $wp_styles;
    if ( ! isset( $wp_styles ) ) {
        return;
    }

    foreach ( $wp_styles->registered as $handle => $data ) {
        if ( empty( $data->src ) ) {
            continue;
        }

        // Normalize src and check for the plugin path fragment
        $src = $data->src;
        if ( strpos( $src, "weglot/dist/css/front-css.css" ) !== false || strpos( $src, '/weglot/dist/css/front-css.css' ) !== false ) {
            wp_dequeue_style( $handle );
            wp_deregister_style( $handle );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'hello_remove_weglot_front_css', 100 );

// Remove missing Elementor local Roboto CSS and enqueue Google Fonts fallback
function hello_fix_missing_elementor_roboto() {
    global $wp_styles;
    if ( ! isset( $wp_styles ) ) {
        return;
    }

    $upload_dir = wp_get_upload_dir();
    $local_file_path = trailingslashit( $upload_dir['basedir'] ) . 'elementor/google-fonts/css/roboto.css';

    // If the local Roboto file is missing, dequeue any registered handle pointing to it
    $found_handle = '';
    foreach ( $wp_styles->registered as $handle => $data ) {
        if ( empty( $data->src ) ) {
            continue;
        }
        if ( strpos( $data->src, 'elementor/google-fonts/css/roboto.css' ) !== false ) {
            $found_handle = $handle;
            break;
        }
    }

    if ( ! file_exists( $local_file_path ) ) {
        if ( $found_handle ) {
            wp_dequeue_style( $found_handle );
            wp_deregister_style( $found_handle );
        }

        // Enqueue Roboto from Google Fonts as fallback
        wp_enqueue_style(
            'hello-roboto-fallback',
            'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
            [],
            null
        );
    }
}
add_action( 'wp_enqueue_scripts', 'hello_fix_missing_elementor_roboto', 101 );

// Remove WooCommerce default CSS files
function hello_remove_woocommerce_styles() {
    wp_dequeue_style( 'woocommerce-general' );
    wp_dequeue_style( 'woocommerce-layout' );
    wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_dequeue_style( 'woocommerce-rtl' );
    wp_dequeue_style( 'woocommerce-smallscreen-rtl' );
    wp_dequeue_style( 'wc-blocks-style' );
    wp_deregister_style( 'woocommerce-general' );
    wp_deregister_style( 'woocommerce-layout' );
    wp_deregister_style( 'woocommerce-smallscreen' );
    wp_deregister_style( 'woocommerce-rtl' );
    wp_deregister_style( 'woocommerce-smallscreen-rtl' );
    wp_deregister_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'hello_remove_woocommerce_styles', 999 );

// Enqueue custom JS for the currency dropdown
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'custom-currency-dropdown',
        get_template_directory_uri() . '/assets/js/custom-currency-dropdown.js',
        [],
        '1.0',
        true
    );
});




// Include header_search
require_once get_template_directory() . '/inc/header_search.php';

// Include header_icons
require_once get_template_directory() . '/inc/header_icons.php';


// AJAX handlers
add_action('wp_ajax_header_search', 'header_search_callback');
add_action('wp_ajax_nopriv_header_search', 'header_search_callback');

function header_search_callback() {
    if (!isset($_GET['term'])) wp_die();
    $term = sanitize_text_field($_GET['term']);
    if (strlen($term) < 2) wp_die();

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 6,
        's'              => $term,
        'post_status'    => 'publish',
    ];

    $q = new WP_Query($args);

    if ($q->have_posts()) {
        echo '<ul class="exact-results-list">';
        while ($q->have_posts()) {
            $q->the_post();
            $product = wc_get_product(get_the_ID());
            echo '<li>';
            echo '<a href="' . esc_url(get_permalink()) . '">';
            echo get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            echo '<div class="result-info">';
            echo '<span class="result-title">' . esc_html(get_the_title()) . '</span>';
            if ($product) {
                // Show price in current language/currency
                if ( function_exists( 'icl_object_id' ) && function_exists( 'wcml_multi_currency' ) ) {
                    $price_html = wcml_multi_currency()->prices->get_product_price_in_currency( $product->get_id(), null, true );
                } elseif ( function_exists( 'pll_current_language' ) && function_exists( 'wcml_multi_currency' ) ) {
                    $price_html = wcml_multi_currency()->prices->get_product_price_in_currency( $product->get_id(), null, true );
                } else {
                    $price_html = $product->get_price_html();
                }
                echo '<strong class="result-price">' . $price_html . '</strong>';
            }
            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="exact-no-results">لا توجد نتائج</div>';
    }

    wp_reset_postdata();
    wp_die();
}

// Include nav_with_categories
require_once get_template_directory() . '/inc/nav_with_categories.php';

// Include weglot_switcher
require_once get_template_directory() . '/inc/weglot_switcher.php';

// Include header_nav_with_acf_categories
require_once get_template_directory() . '/inc/header_nav_with_acf_categories.php';

// Include cart_count
require_once get_template_directory() . '/inc/cart_count.php';

// Include contact_info
require_once get_template_directory() . '/inc/contact_info.php';

function header_nav_assets() {

    wp_enqueue_style(
        'header-nav-style',
        get_template_directory_uri() . '/assets/css/header-nav.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'header-nav-script',
        get_template_directory_uri() . '/assets/js/header-nav.js',
        [],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'header_nav_assets');

// Enqueue hero-section.css for global hero section styles
function hello_elementor_hero_section_style() {
    $css_path = get_template_directory() . '/assets/css/hero-section.css';
    if ( file_exists( $css_path ) ) {
        wp_enqueue_style(
            'hello-elementor-hero-section',
            get_template_directory_uri() . '/assets/css/hero-section.css',
            [],
            HELLO_ELEMENTOR_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'hello_elementor_hero_section_style', 6);

// Register the custom hero section shortcode
require_once get_template_directory() . '/inc/custom-hero-section-shortcode.php';



// Include fast_parts_search
require_once get_template_directory() . '/inc/fast_parts_search.php';

// AJAX handler for fast parts search
add_action('wp_ajax_fps_product_search', 'fps_product_search_callback');
add_action('wp_ajax_nopriv_fps_product_search', 'fps_product_search_callback');

function fps_product_search_callback() {
    if (!isset($_GET['term'])) wp_die();
    $term = sanitize_text_field($_GET['term']);
    if (strlen($term) < 2) wp_die();

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 8,
        's'              => $term,
        'post_status'    => 'publish',
    ];
    $q = new WP_Query($args);
    if ($q->have_posts()) {
        echo '<ul class="fps-search-results-list">';
        while ($q->have_posts()) {
            $q->the_post();
            $product = wc_get_product(get_the_ID());
            echo '<li>';
            echo '<a href="' . esc_url(get_permalink()) . '">';
            echo get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            echo '<div class="fps-result-info">';
            echo '<span class="fps-result-title">' . esc_html(get_the_title()) . '</span>';
            if ($product) {
                echo '<strong class="fps-result-price">' . $product->get_price_html() . '</strong>';
            }
            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="fps-no-results">لا توجد نتائج</div>';
    }
    wp_reset_postdata();
    wp_die();
}

// Include registration_cover
require_once get_template_directory() . '/inc/registration_cover.php';

// Localize script for AJAX URL
add_action('wp_enqueue_scripts', function() {
    wp_localize_script(
        'fps-script',
        'fps_search',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
}, 20);

// Include brands_display
require_once get_template_directory() . '/inc/brands_display.php';

// Include features_display
require_once get_template_directory() . '/inc/features_display.php';

// Include categories_slider
require_once get_template_directory() . '/inc/categories_slider.php';

// Include products_slider
require_once get_template_directory() . '/inc/products_slider.php';

// Include products_list
require_once get_template_directory() . '/inc/products_list.php';

// Include car_features
require_once get_template_directory() . '/inc/car_features.php';

// Include products_grid
require_once get_template_directory() . '/inc/products_grid.php';

// Include equipment_banner
require_once get_template_directory() . '/inc/equipment_banner.php';

// Include blogs_slider
require_once get_template_directory() . '/inc/blogs_slider.php';

// Enqueue Blogs Slider CSS and JS
function blogs_slider_enqueue_assets() {
    wp_enqueue_style(
        'blogs-slider-style',
        get_template_directory_uri() . '/assets/css/blogs-slider.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'blogs-slider-script',
        get_template_directory_uri() . '/assets/js/blogs-slider.js',
        [],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'blogs_slider_enqueue_assets');

// Include newsletter_section
require_once get_template_directory() . '/inc/newsletter_section.php';

// Enqueue Newsletter Section CSS and JS
function newsletter_section_enqueue_assets() {
    wp_enqueue_style(
        'newsletter-section-style',
        get_template_directory_uri() . '/assets/css/newsletter-section.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'newsletter-section-script',
        get_template_directory_uri() . '/assets/js/newsletter-section.js',
        ['gsap', 'gsap-scrolltrigger'],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'newsletter_section_enqueue_assets');

// Include products_filter_grid_full
require_once get_template_directory() . '/inc/products_filter_grid_full.php';

// AJAX handler
add_action('wp_ajax_products_filter_grid_full', 'products_filter_grid_full_ajax');
add_action('wp_ajax_nopriv_products_filter_grid_full', 'products_filter_grid_full_ajax');
function products_filter_grid_full_ajax() {
    if (!class_exists('WooCommerce')) wp_die();
    
    // Prevent caching
    if (function_exists('wc_nocache_headers')) {
        wc_nocache_headers();
    }

    // 1. Load Frontend Includes (Crucial for AJAX)
    // This ensures that cookie-handling functions and session classes are fully available.
    $wc_path = WC()->plugin_path();
    include_once $wc_path . '/includes/wc-frontend-functions.php';

    if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
        define( 'WOOCOMMERCE_CART', true );
    }

    // 2. Initialize Session & Cart (The Standard Way)
    if ( function_exists( 'WC' ) ) {
        // Ensure session is running
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
        
        // Ensure customer is loaded
        if ( ! WC()->customer ) {
             WC()->customer = new WC_Customer( get_current_user_id(), true );
        }

        // Ensure cart is loaded and populated
        if ( ! WC()->cart ) {
            WC()->cart = new WC_Cart();
        }
        
        // Force hydration from session always
        WC()->cart->get_cart(); 
    }

    $paged = isset($_REQUEST['paged']) ? intval($_REQUEST['paged']) : 1;
    $args = [
        'post_type' => 'product',
        'posts_per_page' => 12,
        'paged' => $paged,
        'post_status' => 'publish',
        'tax_query' => [],
        'meta_query' => [],
    ];
    // Category filter
    if (!empty($_REQUEST['category'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array_map('sanitize_text_field', (array)$_REQUEST['category']),
        ];
    }
    // Color filter
    if (!empty($_REQUEST['color'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'pa_color',
            'field' => 'slug',
            'terms' => array_map('sanitize_text_field', (array)$_REQUEST['color']),
        ];
    }
    // Stock status
    if (!empty($_REQUEST['stock'])) {
        $stock = $_REQUEST['stock'];
        if (in_array('instock', $stock)) {
            $args['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock',
            ];
        }
        if (in_array('onsale', $stock)) {
            $args['meta_query'][] = [
                'key' => '_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            ];
        }
    }
    // Price filter
    if (isset($_REQUEST['price_min']) && isset($_REQUEST['price_max'])) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => [floatval($_REQUEST['price_min']), floatval($_REQUEST['price_max'])],
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC',
        ];
    }
    
    // Search Query (s or q_search fallback)
    if (!empty($_REQUEST['s'])) {
        $args['s'] = sanitize_text_field($_REQUEST['s']);
    } elseif (!empty($_REQUEST['q_search'])) {
        $args['s'] = sanitize_text_field($_REQUEST['q_search']);
    }
    $q = new WP_Query($args);
    if ($q->have_posts()) {
        // Wrapper removed to allow cols to sit in row
        while ($q->have_posts()) {
            $q->the_post();
            global $product;
            $product_id = get_the_ID();
            $product_obj = wc_get_product($product_id);
            $product_title = get_the_title();
            $product_link = get_permalink();
            $product_image = get_the_post_thumbnail_url($product_id, 'medium');
            $regular_price = $product_obj->get_regular_price();
            $sale_price = $product_obj->get_sale_price();
            $is_on_sale = $product_obj->is_on_sale();
            $is_in_stock = $product_obj->is_in_stock();
            $rating = $product_obj->get_average_rating();
            $stock_qty = $product_obj->get_stock_quantity();
            $sold = get_post_meta($product_id, 'total_sales', true);
            
            // Bootstrap Column Wrapper
            echo '<div class="col-12 col-md-6 col-lg-4 mb-4" style="display:flex;justify-content:center;">';
            echo '<div class="product-list-item" style="height:100%;">'; // Ensure equal height card
            
            // Wishlist Button - Unified Implementation
            if (defined('YITH_WCWL') || function_exists('YITH_WCWL')) {
                // More robust check for AJAX context
                $is_in_wishlist = false;
                if (method_exists(YITH_WCWL(), 'is_product_in_wishlist')) {
                    $is_in_wishlist = YITH_WCWL()->is_product_in_wishlist($product_id);
                }
                
                // Fallback for some YITH versions where the above might be inconsistent in AJAX
                if (!$is_in_wishlist && class_exists('YITH_WCWL_Wishlist_Factory')) {
                    $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
                    if ($wishlist) {
                        $is_in_wishlist = $wishlist->has_product($product_id);
                    }
                }
                $in_wishlist = $is_in_wishlist;
                $base_url = (is_ssl() ? 'https://' : 'http://') . 
                    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') .
                    (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                $add_to_wishlist_url = wp_nonce_url(add_query_arg('add_to_wishlist', $product_id, $base_url), 'add_to_wishlist');
                
                echo '<div class="shop-wishlist-button">';
                echo '<a href="' . esc_url($add_to_wishlist_url) . '" ';
                echo 'class="product-wishlist-btn product-grid-wishlist' . ($in_wishlist ? ' active' : '') . '" ';
                echo 'data-product-id="' . esc_attr($product_id) . '" ';
                echo 'data-product-type="' . esc_attr($product_obj ? $product_obj->get_type() : '') . '" ';
                echo 'data-original-product-id="' . esc_attr($product_obj ? $product_obj->get_parent_id() : '') . '" ';
                echo 'data-title="Add to wishlist" ';
                echo 'rel="nofollow">';
                echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">';
                echo '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                echo '</svg>';
                echo '</a>';
                echo '</div>';
            } else {
                echo '<button class="product-wishlist-btn" data-product-id="' . esc_attr($product_id) . '" type="button">';
                echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">';
                echo '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                echo '</svg>';
                echo '</button>';
            }
            
            // Sale badge
            if ($is_on_sale && $regular_price && $sale_price) {
                $discount = round((($regular_price - $sale_price) / $regular_price) * 100);
                echo '<span class="product-filter-badge product-badge">-' . esc_html($discount) . '%</span>';
            }
            // Product image
            echo '<a href="' . esc_url($product_link) . '" class="product-list-image">';
            if ($product_image) {
                echo '<img src="' . esc_url($product_image) . '" alt="' . esc_attr($product_title) . '" loading="lazy">';
            } else {
                echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr($product_title) . '">';
            }
            echo '</a>';
            // Product details
            echo '<div class="product-list-details">';
            // Title
            echo '<h3 class="product-list-title"><a href="' . esc_url($product_link) . '">' . esc_html($product_title) . '</a></h3>';
            // Rating
            if ($rating > 0) {
                echo '<div class="product-rating" style="font-size:13px;color:#FFA500;">';
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= round($rating) ? '★' : '☆';
                }
                echo ' <span style="color:#333;">(' . number_format($rating, 1) . ')</span>';
                echo '</div>';
            }
            // Price - convert to selected currency if plugin available
            echo '<div class="product-list-price">';
            $wmc_current = function_exists('WOOMULTI_CURRENCY_F_Data') ? WOOMULTI_CURRENCY_F_Data::get_ins()->get_current_currency() : get_woocommerce_currency();
            $converted_regular = $regular_price ? (function_exists('wmc_get_price') ? wmc_get_price($regular_price) : $regular_price) : '';
            $converted_sale = $sale_price ? (function_exists('wmc_get_price') ? wmc_get_price($sale_price) : $sale_price) : '';
            if ($is_on_sale && $sale_price) {
                echo '<span class="price-sale">' . hello_localized_price($converted_sale, $wmc_current) . '</span>';
                echo '<span class="price-regular">' . hello_localized_price($converted_regular, $wmc_current) . '</span>';
            } else {
                echo '<span class="price-sale">' . hello_localized_price($converted_regular, $wmc_current) . '</span>';
            }
            echo '</div>';
            // Progress Bar & Stock Logic
            $is_managed = ! is_null($stock_qty);
            
            if ($is_managed) {
                // Show available count as percentage (capped at 0-100%)
                $available_percent = max(0, min(100, intval($stock_qty)));
                $sold_percent = 0; // Only show available in orange
            }
            
            // Only show progress bar if stock is managed (scarcity implied)
            if ($is_managed) {
                echo '<div class="product-list-progress" style="margin-top:auto;margin-bottom:8px;">';
                echo '<div class="progress-bar" title="Available: ' . intval($available_percent) . '%">';
                echo '<div class="progress-sold" style="width:' . esc_attr($sold_percent) . '%;background:#d1d5db;height:100%;"></div>';
                echo '<div class="progress-available" style="width:' . esc_attr($available_percent) . '%;background:var(--color-primary);height:100%;"></div>';
                echo '</div>';
                echo '</div>';
            } else {
                 // Spacer for alignment consistency?
                 echo '<div style="margin-top:auto;margin-bottom:8px;height:6px;"></div>';
            }

            // Stock info
            echo '<div class="product-list-stock">';
            if ($is_managed) {
                 echo '<span class="stock-item"><span class="stock-label">متوفر:</span><span class="stock-value">' . intval($stock_qty) . '</span></span>';
            } else {
                  echo '<span class="stock-item"><span class="stock-label">متوفر:</span><span class="stock-value">∞</span></span>';
            }
            echo '<span class="stock-item sold"><span class="stock-label">مباع:</span><span class="stock-value">' . intval($sold) . '</span></span>';
            echo '</div>';
            // Add to cart button
            // QUANTITY LOGIC
            $qty_in_cart = 0;
            if ( ! is_null( WC()->cart ) ) {
                foreach ( WC()->cart->get_cart() as $cart_item ) {
                    $c_product_id = isset($cart_item['product_id']) ? intval($cart_item['product_id']) : 0;
                    $c_variation_id = isset($cart_item['variation_id']) ? intval($cart_item['variation_id']) : 0;
                    $current_id = intval($product_id);
                    
                    // Check if the current grid item (product_id) matches the cart item or its parent
                    if ( $c_product_id === $current_id || $c_variation_id === $current_id ) {
                        $qty_in_cart += $cart_item['quantity'];
                    }
                }
            }

            // Fallback: Check frontend passed state if session failed
            if ( $qty_in_cart === 0 && !empty($_POST['current_cart_state']) ) {
                $frontend_cart = json_decode( stripslashes($_POST['current_cart_state']), true );
                if ( is_array($frontend_cart) ) {
                   if ( isset($frontend_cart[$current_id]) ) {
                       $qty_in_cart = intval($frontend_cart[$current_id]);
                   }
                }
            }

            echo '<div class="add-to-cart-wrapper" data-product_id="' . esc_attr($product_id) . '">';
            
            if ($qty_in_cart > 0) {
                // Show Quantity Controls (Input-based)
                $minus_btn_content = ($qty_in_cart == 1) ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>' : '-';
                $minus_btn_style = ($qty_in_cart == 1) ? 'background:#fff0f0; border:none; padding:8px 15px; cursor:pointer; flex: 1; color:#ef4444;' : 'background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;';
                $minus_btn_class = ($qty_in_cart == 1) ? 'grid-qty-btn grid-minus trash-mode' : 'grid-qty-btn grid-minus';

                echo '<div class="grid-qty-control" style="display:flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 100%; justify-content: space-between;">
                        <button class="' . esc_attr($minus_btn_class) . '" type="button" style="' . esc_attr($minus_btn_style) . '">' . $minus_btn_content . '</button>
                        <input type="number" class="grid-qty-val" value="' . intval($qty_in_cart) . '" min="1" style="width:50px; text-align:center; border:none; -moz-appearance:textfield;" readonly>
                        <button class="grid-qty-btn grid-plus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">+</button>
                      </div>';
                
                // Hidden Add Button
                echo '<a href="?add-to-cart=' . esc_attr($product_id) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-btn" data-product_id="' . esc_attr($product_id) . '" rel="nofollow" style="display:none;">أضف للسلة</a>';
            } else {
                // HIDDEN Quantity Controls (Input-based)
                echo '<div class="grid-qty-control" style="display:none; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 100%; justify-content: space-between;">
                        <button class="grid-qty-btn grid-minus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">-</button>
                        <input type="number" class="grid-qty-val" value="1" min="1" style="width:50px; text-align:center; border:none; -moz-appearance:textfield;" readonly>
                        <button class="grid-qty-btn grid-plus" type="button" style="background:#f9f9f9; border:none; padding:8px 15px; cursor:pointer; flex: 1;">+</button>
                      </div>';
                
                // Show Add Button
                echo '<a href="?add-to-cart=' . esc_attr($product_id) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart add-to-cart-btn" data-product_id="' . esc_attr($product_id) . '" aria-label="أضف للسلة" rel="nofollow" style="width:100%;margin-top:10px;">أضف للسلة</a>';
            }
            echo '</div>'; // .add-to-cart-wrapper
            echo '</div>'; // .product-list-details
            echo '</div>'; // .product-list-item
            echo '</div>'; // .col
        }
        
        // Pagination
        $total_pages = $q->max_num_pages;
        if ($total_pages > 1) {
            echo '<div class="col-12">';
            echo '<div class="products-pagination" style="display:flex;justify-content:center;align-items:center;gap:8px;margin-top:30px;margin-bottom:30px;">';
            
            // Previous button
            if ($paged > 1) {
                echo '<button class="pagination-btn pagination-prev" data-page="' . ($paged - 1) . '" style="padding:10px 20px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;font-weight:600;">السابق</button>';
            }
            
            // Show limited page numbers (5 max)
            $range = 2; // Show 2 pages before and after current
            $start = max(1, $paged - $range);
            $end = min($total_pages, $paged + $range);
            
            // First page
            if ($start > 1) {
                echo '<button class="pagination-btn pagination-number" data-page="1" style="padding:8px 12px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;min-width:40px;">1</button>';
                if ($start > 2) {
                    echo '<span style="padding:8px;">...</span>';
                }
            }
            
            // Page range
            for ($i = $start; $i <= $end; $i++) {
                if ($i == $paged) {
                    echo '<button class="pagination-btn pagination-number active" data-page="' . $i . '" style="padding:8px 12px;border:1px solid var(--color-primary);background:var(--color-primary);color:#fff;border-radius:4px;cursor:pointer;min-width:40px;font-weight:600;">' . $i . '</button>';
                } else {
                    echo '<button class="pagination-btn pagination-number" data-page="' . $i . '" style="padding:8px 12px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;min-width:40px;">' . $i . '</button>';
                }
            }
            
            // Last page
            if ($end < $total_pages) {
                if ($end < $total_pages - 1) {
                    echo '<span style="padding:8px;">...</span>';
                }
                echo '<button class="pagination-btn pagination-number" data-page="' . $total_pages . '" style="padding:8px 12px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;min-width:40px;">' . $total_pages . '</button>';
            }
            
            // Next button
            if ($paged < $total_pages) {
                echo '<button class="pagination-btn pagination-next" data-page="' . ($paged + 1) . '" style="padding:10px 20px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;font-weight:600;">التالي</button>';
            }
            
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="products-no-results">لا توجد نتائج</div>';
    }
    wp_reset_postdata();
    wp_die();
}

// Enqueue assets and localize AJAX
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');
    wp_enqueue_style('products-list-style', get_template_directory_uri() . '/assets/css/products-list.css', [], '1.0');
    wp_enqueue_style('products-filter-grid-full-style', get_template_directory_uri() . '/assets/css/products-filter-grid-full.css', [], '1.0');
    // Build Initial Cart State
    $initial_cart = [];
    if ( function_exists('WC') && WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $p_id = $cart_item['product_id'];
            $qty = $cart_item['quantity'];
            
            // Handle Variable Products: Use parent ID or variation ID?
            // The grid usually displays the PARENT ID. If we use variation ID, it might not match.
            // If the grid item is the variation itself, we are good.
            // Let's store by Product ID for the grid to find it, but keep the specific Key.
            
            // Note: If multiple variations of same product exist, this simplified logic 
            // might overwrite and only control the last one.
            // For a "Simple Grid", this is an acceptable tradeoff.
            
            $initial_cart[$p_id] = [
                'qty' => $qty,
                'key' => $cart_item_key
            ];
            
            // Also store by variation ID if available so strict ID matches work
            if ( !empty($cart_item['variation_id']) ) {
                $initial_cart[$cart_item['variation_id']] = [
                    'qty' => $qty,
                    'key' => $cart_item_key
                ];
            }
        }
    }

    wp_enqueue_script('products-filter-grid-full', get_template_directory_uri() . '/assets/js/products-filter-grid-full.js', ['jquery'], filemtime(get_template_directory() . '/assets/js/products-filter-grid-full.js'), true);
    wp_localize_script('products-filter-grid-full', 'products_filter_grid_full', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'initial_cart' => $initial_cart
    ]);
});

// Register Custom WC AJAX Endpoint for reliable session handling
add_action( 'wc_ajax_update_item_qty', 'update_cart_item_qty_by_id_handler' );

// AJAX Handler for Updating Quantity from Grid
add_action('wp_ajax_update_cart_item_qty_by_id', 'update_cart_item_qty_by_id_handler');
add_action('wp_ajax_nopriv_update_cart_item_qty_by_id', 'update_cart_item_qty_by_id_handler');

function update_cart_item_qty_by_id_handler() {
    if ( ! isset($_POST['product_id']) || ! isset($_POST['qty']) ) {
        wp_send_json_error();
    }

    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);
    $item_key_to_update = '';

    // Ensure WC Session and Cart are available
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) WC()->initialize_session();
        if ( ! WC()->customer ) WC()->customer = new WC_Customer( get_current_user_id(), true );
        if ( ! WC()->cart ) WC()->cart = new WC_Cart();
        WC()->cart->get_cart_from_session();
    }
    
    // Optimistic Key Check
    if ( !empty($_POST['cart_item_key']) ) {
        $posted_key = sanitize_text_field($_POST['cart_item_key']);
        if ( isset( WC()->cart->cart_contents[$posted_key] ) ) {
            $item_key_to_update = $posted_key;
        }
    }

    // Fallback search if key not provided or invalid
    if ( empty($item_key_to_update) ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( $cart_item['product_id'] == $product_id || ( isset($cart_item['variation_id']) && $cart_item['variation_id'] == $product_id ) ) {
                $item_key_to_update = $cart_item_key;
                break;
            }
        }
    }

    if ( $item_key_to_update ) {
        if ( $qty <= 0 ) {
            // Use set_quantity(0) as it is the most robust way to trigger removal + recalc hooks
            WC()->cart->set_quantity( $item_key_to_update, 0 );
        } else {
            WC()->cart->set_quantity( $item_key_to_update, $qty );
        }
        // Recalculate and explicit save
        WC()->cart->calculate_totals();
        // Force session update
        WC()->cart->session->set_session();
        // Force persistent database update (for logged in users)
        if ( is_user_logged_in() ) {
            WC()->cart->persistent_cart_update();
        }
        
        // Return fragments for mini cart update
        WC_AJAX::get_refreshed_fragments();
    } else {
        // Item not found in cart, maybe expired
        wp_send_json_error(['message' => 'Item not found']);
    }
    wp_die();
}

// JS: assets/js/products-filter-grid-full.js
// (Create this file with the following content)




// Include acf_categories_list
require_once get_template_directory() . '/inc/acf_categories_list.php';

// Add Scroll to Top Button in Footer
add_action('wp_footer', function() {
        ?>
        <button id="scrollToTopBtn" aria-label="Scroll to top" style="display:none;position:fixed;bottom:32px;right:32px;z-index:9999;background:var(--color-accent,#f47c33);color:#fff;border:none;border-radius:50%;width:40px;height:40px;box-shadow:0 2px 12px rgba(0,0,0,0.12);font-size:28px;cursor:pointer;transition:background 0.2s,opacity 0.2s;opacity:0.85;display:flex;align-items:center;justify-content:center;padding:0;">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" style="display:block;">
                <circle cx="12" cy="12" r="10" stroke="white" stroke-width="1.5" fill="none"/>
                <polyline points="7,14 12,9 17,14" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <style>
            #scrollToTopBtn:hover {
                background: var(--color-accent-dark, #d65a00);
                opacity: 1;
            }
            #scrollToTopBtn:focus {
                outline: 2px solid var(--color-accent, #f47c33);
                outline-offset: 2px;
            }
            #scrollToTopBtn svg { display: block; }
            </style>
        </button>
        <script>
        (function(){
            var btn = document.getElementById('scrollToTopBtn');
            window.addEventListener('scroll', function() {
                if(window.scrollY > 300) { btn.style.display = 'flex'; } else { btn.style.display = 'none'; }
            });
            btn && btn.addEventListener('click', function() {
                window.scrollTo({top:0,behavior:'smooth'});
            });
        })();
        </script>
        <?php
});

// Custom My Account Login/Register Styling
add_action('wp_enqueue_scripts', function() {
    // Check if we are on the account page, lost password page, and not logged in
    if ( function_exists('is_account_page') && (is_account_page() || is_lost_password_page()) && ! is_user_logged_in() ) {
        wp_enqueue_style('my-account-custom', get_template_directory_uri() . '/assets/css/my-account-login.css', [], time()); // Use time() to bust cache
        wp_enqueue_script('my-account-custom-js', get_template_directory_uri() . '/assets/js/my-account-login.js', ['jquery'], time(), true);
    }

    // New: Custom My Account Orders/Dashboard Styling for logged-in users
    if ( function_exists('is_account_page') && is_account_page() && is_user_logged_in() ) {
        wp_enqueue_style('my-account-orders-style', get_template_directory_uri() . '/assets/css/my-account-orders.css', [], time());
    }
});

/**
 * Remove WooCommerce layout styles to prevent conflicts with custom design
 */
add_filter( 'woocommerce_enqueue_styles', function( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-layout'] );
	return $enqueue_styles;
});

// --- WooCommerce Custom Registration Fields Logic ---

// 1. Validate Fields
add_action( 'woocommerce_register_post', 'custom_validate_registration_fields', 10, 3 );
function custom_validate_registration_fields( $username, $email, $validation_errors ) {
    // Validate Confirm Password
    if ( isset( $_POST['password'] ) && isset( $_POST['password_confirm'] ) ) {
        if ( $_POST['password'] !== $_POST['password_confirm'] ) {
            $validation_errors->add( 'password_mismatch', __( '<strong>كلمة المرور</strong> وتأكيد كلمة المرور غير متطابقين.', 'woocommerce' ) );
        }
    }

    // Validate Terms (optional, but handled by 'required' attr in HTML, good to have backend check too)
    if ( ! isset( $_POST['terms'] ) ) {
        $validation_errors->add( 'terms_error', __( 'يجب الموافقة علي الشروط والأحكام.', 'woocommerce' ) );
    }
    
    // Validate Name
    if ( empty( $_POST['billing_first_name'] ) ) {
        $validation_errors->add( 'billing_first_name_error', __( '<strong>الإسم</strong> مطلوب.', 'woocommerce' ) );
    }

    // Validate Phone
    if ( empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>رقم الهاتف</strong> مطلوب.', 'woocommerce' ) );
    }

    return $validation_errors;
}

// 1.5 Change Breadcrumb Home Text
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text' );
function wcc_change_breadcrumb_home_text( $defaults ) {
    $defaults['home'] = 'الرئيسية';
    return $defaults;
}

// 2. Save Fields
add_action( 'woocommerce_created_customer', 'custom_save_registration_fields' );
function custom_save_registration_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    if ( isset( $_POST['billing_phone'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }
}

// Include primary_menu_footer_links
require_once get_template_directory() . '/inc/primary_menu_footer_links.php';

// Include category_menu_footer_links
require_once get_template_directory() . '/inc/category_menu_footer_links.php';

// Include Cart AJAX functions
require_once get_template_directory() . '/inc/cart-ajax.php';
require_once get_template_directory() . '/inc/cart-qty-ajax.php';

// Custom AJAX Handler to Add to Wishlist by Product ID
add_action('wp_ajax_hello_custom_add_to_wishlist', 'hello_custom_add_to_wishlist');
add_action('wp_ajax_nopriv_hello_custom_add_to_wishlist', 'hello_custom_add_to_wishlist');

function hello_custom_add_to_wishlist() {
    // 1. Initialize WC Session if needed (Crucial for guest users)
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
    }
    if ( ! isset( $_POST['product_id'] ) ) {
        wp_send_json_error( 'Missing Product ID' );
    }
    
    $product_id = intval( $_POST['product_id'] );
    
    if ( function_exists( 'YITH_WCWL' ) ) {
        try {
            if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
                $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
                if ( $wishlist ) {
                    // Check if already in wishlist
                    if ( $wishlist->has_product( $product_id ) ) {
                         wp_send_json_success(['message' => 'Exists']);
                    } else {
                        $wishlist->add_product( $product_id );
                        $wishlist->save();
                        
                        // Force Session Cookie for Guests to ensure persistence
                        if ( ! is_user_logged_in() && isset(YITH_WCWL()->session) ) {
                             // Force session save
                             if ( method_exists( YITH_WCWL()->session, 'set_cookie_session' ) ) {
                                 YITH_WCWL()->session->set_cookie_session();
                             }
                             if ( method_exists( YITH_WCWL()->session, 'save_data' ) ) {
                                 YITH_WCWL()->session->save_data();
                             }
                        }
                        
                        wp_send_json_success(['message' => 'Added']);
                    }
                } else {
                    // Try to create a new session/wishlist if defaults missing (Guest edge case)
                    // Usually get_default_wishlist handles creation.
                    wp_send_json_error('No default wishlist');
                }
            } else {
                // Legacy
                YITH_WCWL()->add( array( 'add_to_wishlist' => $product_id ) );
                wp_send_json_success();
            }
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    } else {
        wp_send_json_error( 'Plugin not active' );
    }
    wp_die();
}
add_action('wp_ajax_hello_custom_remove_from_wishlist', 'hello_custom_remove_from_wishlist');
add_action('wp_ajax_nopriv_hello_custom_remove_from_wishlist', 'hello_custom_remove_from_wishlist');

function hello_custom_remove_from_wishlist() {
    if ( ! isset( $_POST['product_id'] ) ) {
        wp_send_json_error( 'Missing Product ID' );
        wp_die();
    }
    
    $product_id = intval( $_POST['product_id'] );
    
    if ( function_exists( 'YITH_WCWL' ) ) {
        // Attempt removal
        try {
            // Check for YITH 3.0+ Factory method
            if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
                $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
                if ( $wishlist ) {
                    $wishlist->remove_product( $product_id );
                    $wishlist->save();
                    
                    // Force session save for guests
                    if ( ! is_user_logged_in() && isset(YITH_WCWL()->session) ) {
                        if ( method_exists( YITH_WCWL()->session, 'set_cookie_session' ) ) {
                            YITH_WCWL()->session->set_cookie_session();
                        }
                        if ( method_exists( YITH_WCWL()->session, 'save_data' ) ) {
                            YITH_WCWL()->session->save_data();
                        }
                    }
                    
                    wp_send_json_success(['message' => 'Removed']);
                    wp_die();
                } else {
                    wp_send_json_error('No default wishlist');
                    wp_die();
                }
            } else {
                // Fallback for older versions
                YITH_WCWL()->remove( array( 'remove_from_wishlist' => $product_id ) );
                wp_send_json_success(['message' => 'Removed']);
                wp_die();
            }
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
            wp_die();
        }
    } else {
        wp_send_json_error( 'Plugin not active' );
        wp_die();
    }
}

// Fix 404 for Elementor Google Fonts
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );


/**
 * ==========================================================
 * Custom Wishlist Sidebar Implementation
 * ==========================================================
 */

// 1. Enqueue Assets
function hello_wishlist_sidebar_assets() {
    wp_enqueue_style(
        'wishlist-sidebar-style',
        get_template_directory_uri() . '/assets/css/wishlist-sidebar.css',
        [],
        '1.0'
    );
     // We will reuse header-icons.js for the triggering logic, 
     // but we can add a small script here if needed, or just let header-icons.js handle it.
}
add_action('wp_enqueue_scripts', 'hello_wishlist_sidebar_assets');


// 2. Render Sidebar HTML in Footer
function hello_render_wishlist_sidebar() {
    ?>
    <!-- Wishlist Sidebar Markup -->
    <div class="wishlist-backdrop" onclick="closeWishlistSidebar()"></div>
    <div class="yith-wcwl-custom-sidebar">
        <div class="wishlist-sidebar-header">
            <h3 class="wishlist-sidebar-title">المفضلة</h3>
            <div class="wishlist-header-actions">
                <button class="close-wishlist-sidebar" onclick="closeWishlistSidebar()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        <div class="wishlist-sidebar-content" id="wishlist-sidebar-items">
            <!-- Items loaded via AJAX -->
             <div class="wishlist-loading" style="text-align:center;padding:20px;">
                <span class="spinner-border text-primary" role="status"></span>
             </div>
        </div>
        <button class="clear-all-wishlist-btn" id="clear-all-wishlist" aria-label="Clear All" title="مسح الكل">
            مسح الكل
        </button>
    </div>
    
    <script>
        function openWishlistSidebar() {
            document.querySelector('.yith-wcwl-custom-sidebar').classList.add('active');
            document.querySelector('.wishlist-backdrop').classList.add('active');
            
            // Highlight the header icon
            const wlIcon = document.querySelector('.icon-type-wishlist');
            if (wlIcon) wlIcon.classList.add('active');

            // Trigger generic "fetch" event if needed
            jQuery(document.body).trigger('hello_fetch_wishlist_items');
        }
        function closeWishlistSidebar() {
            document.querySelector('.yith-wcwl-custom-sidebar').classList.remove('active');
            document.querySelector('.wishlist-backdrop').classList.remove('active');

            // Remove highlight from the header icon
            const wlIcon = document.querySelector('.icon-type-wishlist');
            if (wlIcon) wlIcon.classList.remove('active');
        }
        
        // Listen for triggering event from header-icons.js
        document.addEventListener('hello_open_wishlist_sidebar', openWishlistSidebar);
        
        // AJAX Fetch Logic
        jQuery(document).ready(function($) {
            // Listen for specific 'hello_fetch' OR standard 'added_to_wishlist' / 'removed_from_wishlist'
            $(document.body).on('hello_fetch_wishlist_items added_to_wishlist removed_from_wishlist', function() {
                $.ajax({
                    url: hello_header_icons.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'hello_get_wishlist_sidebar_items',
                        nonce: hello_header_icons.nonce
                    },
                    success: function(res) {
                        if(res.success) {
                            $('#wishlist-sidebar-items').html(res.data);
                        }
                    }
                });
            });

            // Quantity Logic for Wishlist Sidebar
            $(document.body).on('click', '.qty-btn.plus', function() {
                var input = $(this).closest('.wishlist-qty-control').find('.qty-input');
                var val = parseInt(input.val()) || 0;
                var max = parseInt(input.attr('max')) || 9999;
                if (val < max) {
                    input.val(val + 1).trigger('change');
                }
            });
            
            $(document.body).on('click', '.qty-btn.minus', function() {
                var input = $(this).closest('.wishlist-qty-control').find('.qty-input');
                var val = parseInt(input.val()) || 0;
                var min = parseInt(input.attr('min')) || 1;
                    var qtyControl = $(this).closest('.wishlist-qty-control');
                    var productId = qtyControl.data('product-id');
                
                    // If qty is 1, remove from cart by setting to 0
                    if (val === 1) {
                        qtyControl.hide();
                        qtyControl.closest('.wishlist-item-actions').find('.wishlist-add-to-cart-btn').show();
                        input.val(0).trigger('change');
                        $('button[name="update_cart"]').prop('disabled', false).trigger('click');
                    } else if (val > min) {
                        input.val(val - 1).trigger('change');
                    }
                });
            
                // Toggle trash/minus icon based on quantity
                $(document.body).on('change input', '.wishlist-qty-control .qty-input', function() {
                    var val = parseInt($(this).val()) || 1;
                    var minusBtn = $(this).closest('.wishlist-qty-control').find('.qty-btn.minus');
                    var trashIcon = minusBtn.find('.trash-icon');
                    var minusIcon = minusBtn.find('.minus-icon');
                
                    if (val === 1) {
                        trashIcon.show();
                        minusIcon.hide();
                    } else {
                        trashIcon.hide();
                        minusIcon.show();
                    }
            });

            // Update Add to Cart Button when quantity changes
            $(document.body).on('change keyup', '.qty-input', function() {
                var qty = $(this).val();
                var container = $(this).closest('.wishlist-item-actions');
                var btn = container.find('.wishlist-add-to-cart-btn');
                
                // Update data-quantity attribute which standard Woo scripts use
                btn.attr('data-quantity', qty);
                
                // Update href for non-ajax fallback
                var href = btn.attr('href');
                if (href && href.indexOf('?') !== -1) {
                    // Simple regex replacement for safety
                    href = href.replace(/quantity=\d+/, 'quantity=' + qty);
                    // If not found, append
                    if (href.indexOf('quantity=') === -1) {
                        href += '&quantity=' + qty;
                    }
                    btn.attr('href', href);
                }
            });

            // Toggle visibility on add to cart
            $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
                if ( $button && $button.hasClass('wishlist-add-to-cart-btn') ) {
                    $button.hide();
                    $button.closest('.wishlist-item-actions').find('.wishlist-qty-control').css('display', 'flex');
                }
            });


            // Clear All Wishlist Items
            $(document.body).on('click', '#clear-all-wishlist', function(e) {
                e.preventDefault();
                
                if (!confirm('هل أنت متأكد من مسح جميع العناصر من المفضلة؟')) {
                    return;
                }
                
                var btn = $(this);
                btn.css('opacity', '0.5').prop('disabled', true);
                
                $.ajax({
                    url: hello_header_icons.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'hello_clear_all_wishlist',
                        nonce: hello_header_icons.nonce
                    },
                    success: function(res) {
                        if(res.success) {
                            // Remove active class from all wishlist buttons on the page
                            $('.toggle-wishlist-btn, .product-wishlist-btn, .product-grid-wishlist, .product-wishlist').removeClass('active').find('svg').removeClass('active');
                            $('.toggle-wishlist-btn').find('i').removeClass('fa-solid').addClass('fa-regular').css('color', '');
                            $('.toggle-wishlist-btn').data('action', 'add');
                            
                            $('#wishlist-sidebar-items').html('<div class="wishlist-empty-msg">لا توجد منتجات في المفضلة.</div>');
                            // Trigger update of header count
                            jQuery(document.body).trigger('removed_from_wishlist');
                            btn.css('opacity', '1').prop('disabled', false);
                        } else {
                            alert('فشل في مسح المفضلة.');
                            btn.css('opacity', '1').prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('حدث خطأ.');
                        btn.css('opacity', '1').prop('disabled', false);
                    }
                });
            });

            // Remove Item Logic
            $(document.body).on('click', '.wishlist-item-remove', function(e) {
                e.preventDefault();
                var btn = $(this);
                var prodId = btn.data('product-id');
                var itemContainer = btn.closest('.wishlist-sidebar-item');
                
                // Add loading state
                btn.css('opacity', '0.5');

                $.ajax({
                    url: hello_header_icons.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'hello_remove_wishlist_item',
                        nonce: hello_header_icons.nonce,
                        product_id: prodId
                    },
                    success: function(res) {
                        if(res.success) {
                            // Remove active class from the product's wishlist button on the page
                            $('.toggle-wishlist-btn[data-product-id="' + prodId + '"], .product-wishlist-btn[data-product-id="' + prodId + '"], .product-grid-wishlist[data-product-id="' + prodId + '"], .product-wishlist[data-product-id="' + prodId + '"]').removeClass('active').find('svg').removeClass('active');
                            $('.toggle-wishlist-btn[data-product-id="' + prodId + '"]').find('i').removeClass('fa-solid').addClass('fa-regular').css('color', '');
                            $('.toggle-wishlist-btn[data-product-id="' + prodId + '"]').data('action', 'add');
                            
                            // Remove item from DOM with fade out
                            itemContainer.fadeOut(300, function() { 
                                $(this).remove(); 
                                // Check if empty
                                if( $('#wishlist-sidebar-items').children().length === 0 ) {
                                    $('#wishlist-sidebar-items').html('<div class="wishlist-empty-msg">لا توجد منتجات في المفضلة.</div>');
                                }
                            });
                            // Trigger update of header count and sidebar refresh
                            jQuery(document.body).trigger('removed_from_wishlist', [prodId, btn]);
                        } else {
                            alert('Could not remove item.');
                            btn.css('opacity', '1');
                        }
                    }
                });
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'hello_render_wishlist_sidebar');


// 3. AJAX Handler to Get Items
add_action('wp_ajax_hello_get_wishlist_sidebar_items', 'hello_get_wishlist_sidebar_items_callback');
add_action('wp_ajax_nopriv_hello_get_wishlist_sidebar_items', 'hello_get_wishlist_sidebar_items_callback');

function hello_get_wishlist_sidebar_items_callback() {
    // 1. Initialize WC Session if needed (Crucial for guest users)
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
    }

    if ( ! class_exists( 'YITH_WCWL' ) ) {
        wp_send_json_error('<p>Wishlist plugin not active.</p>');
    }

    if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
        $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
        if ( $wishlist && $wishlist->has_items() ) {
            $wishlist_items = $wishlist->get_items();
        } else {
            $wishlist_items = [];
        }
    } else {
        $wishlist_items = YITH_WCWL()->get_products( [ 'is_default' => true ] );
    }

    if ( empty( $wishlist_items ) ) {
        wp_send_json_success('<div class="wishlist-empty-msg">لا توجد منتجات في المفضلة.</div>');
    }

    ob_start();
    foreach ( $wishlist_items as $item ) {
        // YITH 3.0 Item Object vs Legacy Array
        $prod_id = is_object($item) ? $item->get_product_id() : (isset($item['prod_id']) ? $item['prod_id'] : 0);
        
        $product = wc_get_product( $prod_id );
        if ( ! $product ) continue;
        
        $product_id = $product->get_id();
        
        // Check if product is already in cart
        $cart_id_gen = WC()->cart->generate_cart_id( $product_id );
        $in_cart_key = WC()->cart->find_product_in_cart( $cart_id_gen );
        $qty_in_cart = 0;
        
        if ( $in_cart_key && isset( WC()->cart->cart_contents[$in_cart_key] ) ) {
            $qty_in_cart = WC()->cart->cart_contents[$in_cart_key]['quantity'];
        }
        
        ?>
        <div class="wishlist-sidebar-item" data-product-id="<?php echo esc_attr($product_id); ?>">
            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="wishlist-item-image-link">
                <?php 
                $thumbnail = $product->get_image( 'thumbnail', ['class' => 'wishlist-item-image', 'alt' => esc_attr($product->get_name())] );
                if ( $thumbnail && strpos($thumbnail, 'src=""') === false && strpos($thumbnail, 'src=\'\'') === false ) {
                    echo $thumbnail;
                } else {
                    echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" class="wishlist-item-image placeholder-fallback" alt="' . esc_attr($product->get_name()) . '">';
                }
                ?>
            </a>
            <div class="wishlist-item-details">
                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="wishlist-item-title">
                    <?php echo esc_html( $product->get_name() ); ?>
                </a>
                <div class="wishlist-item-price"><?php echo $product->get_price_html(); ?></div>
                <div class="wishlist-item-actions">
                    <?php
                    // Quantity Control
                    if ( $product->is_sold_individually() ) {
                         $min_qty = 1; 
                         $max_qty = 1;
                    } else {
                         $min_qty = 1;
                         $max_qty = $product->get_stock_quantity() ? $product->get_stock_quantity() : '';
                    }
                    
                    $qty_display = $qty_in_cart > 0 ? 'flex' : 'none';
                    $btn_display = $qty_in_cart > 0 ? 'none' : 'block';
                    $qty_value = $qty_in_cart > 0 ? $qty_in_cart : 1;
                    ?>
                    
                    <div class="wishlist-qty-control" data-product-id="<?php echo esc_attr($product_id); ?>" style="display: <?php echo $qty_display; ?>;">
                        <button type="button" class="qty-btn minus">
                            <?php if ($qty_value == 1): ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="trash-icon">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <span class="minus-icon" style="display:none;">-</span>
                            <?php else: ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="trash-icon" style="display:none;">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <span class="minus-icon">-</span>
                            <?php endif; ?>
                        </button>
                        <input type="number" class="qty-input" value="<?php echo esc_attr($qty_value); ?>" min="<?php echo esc_attr($min_qty); ?>" max="<?php echo esc_attr($max_qty); ?>" />
                        <button type="button" class="qty-btn plus">+</button>
                    </div>

                    <?php
                    echo sprintf( '<a href="%s" data-quantity="1" class="%s" %s style="display:%s;">%s</a>',
                        esc_url( $product->add_to_cart_url() ),
                        esc_attr( 'button product_type_' . $product->get_type() . ' add_to_cart_button ajax_add_to_cart wishlist-add-to-cart-btn' ),
                        'data-product_id="' . esc_attr( $product->get_id() ) . '" aria-label="' . esc_attr( $product->add_to_cart_description() ) . '" rel="nofollow"',
                        esc_attr( $btn_display ),
                        esc_html( $product->add_to_cart_text() )
                    );
                    ?>
                </div>
            </div>
            <!-- Remove Button -->
             <button class="wishlist-item-remove" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Remove">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </button>
        </div>
        <?php
    }
    $html = ob_get_clean();
    wp_send_json_success( $html );
}

// 4. AJAX Handler to Remove Item
add_action('wp_ajax_hello_remove_wishlist_item', 'hello_remove_wishlist_item_callback');
add_action('wp_ajax_nopriv_hello_remove_wishlist_item', 'hello_remove_wishlist_item_callback');

function hello_remove_wishlist_item_callback() {
    // 1. Initialize WC Session if needed (Crucial for guest users)
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ( ! class_exists( 'YITH_WCWL' ) || !$product_id ) {
        wp_send_json_error( array( 'message' => 'Invalid request' ) );
    }

    try {
        // Attempt 1: YITH helper (Modern & clean)
        if ( method_exists( YITH_WCWL(), 'remove_from_wishlist' ) ) {
             YITH_WCWL()->remove_from_wishlist( $product_id );
        }

        // Attempt 2: Direct factory removal (Robust for multi-wishlist setups)
        if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
            $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
            if ( $wishlist && is_object( $wishlist ) ) {
                $wishlist->remove_product( $product_id );
                if ( method_exists( $wishlist, 'save' ) ) {
                    $wishlist->save();
                }
            }
        }

        // Attempt 3: Legacy array-based remove (Fallback)
        YITH_WCWL()->remove( array( 'remove_from_wishlist' => $product_id ) );

        wp_send_json_success();
        
    } catch ( Exception $e ) {
        error_log( 'Wishlist remove error: ' . $e->getMessage() );
        wp_send_json_error( array( 'message' => $e->getMessage() ) );
    }
}

// 5. AJAX Handler to Clear All Wishlist Items
add_action('wp_ajax_hello_clear_all_wishlist', 'hello_clear_all_wishlist_callback');
add_action('wp_ajax_nopriv_hello_clear_all_wishlist', 'hello_clear_all_wishlist_callback');

function hello_clear_all_wishlist_callback() {
    // 1. Initialize WC Session if needed (Crucial for guest users)
    if ( function_exists( 'WC' ) ) {
        if ( ! WC()->session ) {
            WC()->initialize_session();
        }
    }

    if ( ! class_exists( 'YITH_WCWL' ) ) {
        wp_send_json_error( array( 'message' => 'Wishlist plugin not active' ) );
    }

    try {
        // Prefer YITH 3.x API when available
        if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
            $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
            if ( $wishlist && is_object( $wishlist ) ) {
                // Get all items and remove them
                $items = $wishlist->get_items();
                if ( is_array( $items ) && count( $items ) > 0 ) {
                    foreach ( $items as $item ) {
                        if ( isset( $item['prod_id'] ) ) {
                            $wishlist->remove_product( $item['prod_id'] );
                        }
                    }
                    if ( method_exists( $wishlist, 'save' ) ) {
                        $wishlist->save();
                    }
                }
                wp_send_json_success();
            }
        }

        // Fallback: get all products and remove one by one
        $wishlist_items = YITH_WCWL()->get_products( [ 'is_default' => true ] );
        if ( is_array( $wishlist_items ) && count( $wishlist_items ) > 0 ) {
            foreach ( $wishlist_items as $item ) {
                if ( isset( $item['prod_id'] ) ) {
                    YITH_WCWL()->remove( array( 'remove_from_wishlist' => $item['prod_id'] ) );
                }
            }
        }
        wp_send_json_success();
        
    } catch ( Exception $e ) {
        error_log( 'Clear wishlist error: ' . $e->getMessage() );
        wp_send_json_error( array( 'message' => $e->getMessage() ) );
    }
}

/**
 * Force RTL attributes for Arabic and fix Weglot direction.
 */
add_filter( 'language_attributes', 'hello_force_rtl_for_arabic' );
function hello_force_rtl_for_arabic( $output ) {
    $lang = get_locale();
    
    // Check if Weglot is active and get current language
    if ( function_exists( 'weglot_get_current_language' ) ) {
        $weglot_lang = weglot_get_current_language();
        if ( $weglot_lang ) {
            $lang = $weglot_lang;
        }
    }

    // Fallback: Check URL for '/ar/'
    if ( isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/ar/') !== false ) {
        $lang = 'ar';
    }

    // Normalized check: if the language code contains 'ar' (ar, ar-sa, etc.)
    if ( strpos( $lang, 'ar' ) === 0 || $lang === 'ar' ) {
        // Force RTL
        if ( strpos( $output, 'dir="rtl"' ) === false ) {
            $output = preg_replace( '/dir="[^"]*"/', '', $output );
            $output .= ' dir="rtl"';
        }
    } else {
        // Force LTR
         if ( strpos( $output, 'dir="ltr"' ) === false ) {
             $output = preg_replace( '/dir="[^"]*"/', '', $output );
             $output .= ' dir="ltr"';
        }
    }
    return $output;
}

/**
 * Add custom styles for Weglot direction.
 */
add_action( 'wp_head', 'hello_weglot_custom_styles' );
function hello_weglot_custom_styles() {
    ?>
    <style>
        /* Weglot RTL fixes */
        html[dir="rtl"] .weglot-container {
            /* direction: rtl !important; */
        }
        /* Fix text alignment for Arabic */
        html[dir="rtl"] body {
            text-align: right;
        }
        /* Ensure specific Weglot dropdown alignment */
        .weglot-container.wg-default {
             direction: ltr; /* Keeps flags/text combo predictable */
        }
        
        /* Fix for Contact Wrapper in LTR */
        html[dir="ltr"] .contact-wrapper {
            direction: ltr !important;
            text-align: left !important;
        }

        /* Hero shape transform for English */
        html[dir="ltr"] .elementor-element.elementor-element-50c7f34.e-con-full.e-transform.e-flex.e-con.e-child {
            transform: scaleX(-1);
        }
    </style>
    <?php
}


/**
 * Force Arabic Strings for Single Product Page
 */

// Translate "Add to cart"
add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
    return 'إضافة للسلة';
} );

// Translate Product Tabs
add_filter( 'woocommerce_product_tabs', function( $tabs ) {
    if ( isset( $tabs['description'] ) ) {
        $tabs['description']['title'] = 'الوصف';
    }
    if ( isset( $tabs['reviews'] ) ) {
        $tabs['reviews']['title'] = 'مراجعات';
    }
    if ( isset( $tabs['additional_information'] ) ) {
        $tabs['additional_information']['title'] = 'معلومات إضافية';
    }
    return $tabs;
} );

// Translate "Related products" Heading
add_filter( 'woocommerce_product_related_products_heading', function() {
    return 'منتجات مشابهة';
} );

// Ensure related products always show (increase limit and posts per page)
add_filter( 'woocommerce_output_related_products_args', function( $args ) {
    $args['posts_per_page'] = 8; // Display 8 related products
    $args['columns'] = 4; // 4 columns
    return $args;
} );

// Translate "You may also like" (Upsells) Heading
add_filter( 'woocommerce_product_upsells_products_heading', function() {
    return 'منتجات قد تعجبك';
} );

/**
 * Universal Translation Filter for Single Product Page
 * Forces specific strings to Arabic.
 */
add_filter( 'gettext', 'force_arabic_product_strings', 20, 3 );
function force_arabic_product_strings( $translated_text, $text, $domain ) {
    // FORCE ON FRONTEND AND ADMIN FOR NOW TO DEBUG
    
    switch ( $text ) {
        case 'Description':
            return 'الوصف';
        case 'Reviews':
        case 'Reviews (%d)':
            return 'المراجعات';
        case 'There are no reviews yet.':
            return 'لا توجد مراجعات بعد.';
        case 'Be the first to review &ldquo;%s&rdquo;':
            return 'كن أول من يقيم &ldquo;%s&rdquo;';
        case 'Your rating':
        case 'Your rating *':
            return 'تقييمك *';
        case 'Your review':
        case 'Your review *':
            return 'مراجعتك *';
        case 'Name':
        case 'Name *':
            return 'الاسم *';
        case 'Email':
        case 'Email *':
            return 'البريد الإلكتروني *';
        case 'Save my name, email, and website in this browser for the next time I comment.':
            return 'احفظ اسمي، بريدي الإلكتروني، والموقع الإلكتروني في هذا المتصفح لاستخدامها المرة المقبلة في تعليقي.';
        case 'Submit':
            return 'إرسال';
        case 'Add a review':
            return 'أضف مراجعة';
        case 'Only logged in customers who have purchased this product may leave a review.':
            return 'يمكن فقط للعملاء الذين اشتروا هذا المنتج ترك مراجعة.';
        case 'Your email address will not be published.':
            return 'لن يتم نشر عنوان بريدك الإلكتروني.';
        case 'Required fields are marked':
        case 'Required fields are marked *':
            return 'الحقول الإلزامية مشار إليها بـ *';
        case 'Category:':
            return 'القسم:';
        case 'Tags:':
            return 'الوسوم:';
        case 'Add to cart':
            return 'إضافة للسلة';
        case 'Ships within 24 hours': // Try to catch the ghost string if it's translatable
            return 'يشحن و يصلك خلال 24 ساعة';
        case '5 Years Warranty':
            return 'يوجد ضمان 5 سنوات علي المنتج';
    }

    // Try partial matches if needed (risky, but valid for debugging "Sold 13 times")
    if ( strpos( $text, 'This product was sold' ) !== false ) {
         return str_replace( 
             ['This product was sold', 'times in the last', 'hours'], 
             ['هذا المنتج تم بيعه', 'مرات في آخر', 'ساعة'], 
             $text 
         );
    }

    return $translated_text;
}
// Custom AJAX Handler to TOGGLE Wishlist (Add/Remove)
add_action('wp_ajax_hello_toggle_wishlist', 'hello_toggle_wishlist');
add_action('wp_ajax_nopriv_hello_toggle_wishlist', 'hello_toggle_wishlist');

function hello_toggle_wishlist() {
    if ( ! isset( $_POST['product_id'] ) ) {
        wp_send_json_error( 'Missing Product ID' );
    }
    
    $product_id = intval( $_POST['product_id'] );
    
    if ( defined( 'YITH_WCWL' ) ) {
        try {
            $response = array();
            
            if ( class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
                 // YITH 3.0+
                 $wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
                 
                 // If no wishlist exists (e.g. new guest), try to generate one
                 if ( ! $wishlist ) {
                     // Check if method exists to create one or just fallback
                     // Usually YITH handles creation on 'add' automatically if we use the object,
                     // but get_default_wishlist returning null means we might need to initialize.
                     // Let's try the global fallback for creation if factory fails.
                 }

                 if ( $wishlist && $wishlist->has_product( $product_id ) ) {
                     $wishlist->remove_product( $product_id );
                     $response['action'] = 'removed';
                 } else {
                     // If wishlist obj exists, add to it. If not, standard add_product might create it.
                     if ( $wishlist ) {
                         $wishlist->add_product( $product_id );
                     } else {
                         YITH_WCWL()->add_product( array( 'add_to_wishlist' => $product_id ) );
                     }
                     $response['action'] = 'added';
                 }
                 
                 if ( $wishlist ) {
                     $wishlist->save();
                 }
                 
            } else {
                 // Legacy
                 if ( YITH_WCWL()->is_product_in_wishlist( $product_id ) ) {
                     YITH_WCWL()->remove( array( 'remove_from_wishlist' => $product_id ) );
                     $response['action'] = 'removed';
                 } else {
                     YITH_WCWL()->add( array( 'add_to_wishlist' => $product_id ) );
                     $response['action'] = 'added';
                 }
            }

            // Return new count
            $count = yith_wcwl_count_products();
            $response['count'] = $count;
            
            wp_send_json_success( $response );

        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }
    
    wp_send_json_error( 'YITH WCWL not active' );
}

// Explicitly Enqueue & Localize Header Icons Script
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'hello-header-icons',
        get_template_directory_uri() . '/assets/js/header-icons.js',
        ['jquery', 'wp-api-fetch'],
        time(), // cache bust
        true
    );
    
    $wishlist_url = home_url('/wishlist');
    if ( function_exists( 'YITH_WCWL' ) ) {
        $wishlist_url = YITH_WCWL()->get_wishlist_url();
    }

    wp_localize_script('hello-header-icons', 'hello_header_icons', [
        'ajax_url'     => admin_url('admin-ajax.php'),
        'nonce'        => wp_create_nonce('hello_header_icons_nonce'),
        'wishlist_url' => $wishlist_url
    ]);
});

// Disable WooCommerce Coming Soon mode
add_action('init', function() {
    update_option('woocommerce_coming_soon', 'no');
    update_option('woocommerce_store_pages_only', 'no');
}, 1);
// Include Custom Checkout Shortcode
require_once get_template_directory() . '/inc/custom_checkout_shortcode.php';


/**
 * FORCE ENABLE GATEWAYS FOR TESTING UI
 * This ensures the user sees the checkout design even without full API configuration.
 */
add_filter( 'option_woocommerce_cod_settings', function($settings) {
    if (!is_array($settings)) $settings = array();
    $settings['enabled'] = 'yes';
    return $settings;
});
add_filter( 'option_woocommerce_myfatoorah_v2_settings', function($settings) {
    if (!is_array($settings)) $settings = array();
    $settings['enabled'] = 'yes';
    return $settings;
});

// Force Availability Verification
add_filter( 'woocommerce_gateway_is_available', function( $available, $gateway ) {
    if ( $gateway->id === 'myfatoorah_v2' ) {
        return true; // Force it to show up for UI testing
    }
    if ( $gateway->id === 'cod' ) {
        return true;
    }
    return $available;
}, 10, 2 );

/**
 * Custom Payment Gateway Icons & Titles
 * Forces specific icons (assets/images/ondelivery.png, assets/images/myfatoorah.png)
 * and specific titles to match the design.
 */
add_filter( 'woocommerce_gateway_icon', function( $icon, $id ) {
    $icon_url = '';
    if ( 'cod' === $id ) {
        $icon_url = get_template_directory_uri() . '/assets/images/ondelivery.png';
    } elseif ( 'myfatoorah_v2' === $id || 'myfatoorah' === $id ) {
        $icon_url = get_template_directory_uri() . '/assets/images/myfatoorah.png';
    }

    if ( $icon_url ) {
        return '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $id ) . '" style="max-height:50px;" />';
    }

    return $icon;
}, 10, 2 );

add_filter( 'woocommerce_gateway_title', function( $title, $id ) {
    if ( 'cod' === $id ) {
        return 'دفع عند الإستلام';
    }
    if ( 'myfatoorah_v2' === $id || 'myfatoorah' === $id ) {
        return 'ماي فاتورة';
    }
    return $title;
}, 10, 2 );


/**
 * FORCE DISPLAY COD (Cash on Delivery)
 * If plugin settings are failing, this forces the gateway to be valid.
 */
/**
 * FORCE DISPLAY COD (Cash on Delivery)
 * If plugin settings are failing, this forces the gateway to be valid.
 */
add_filter( 'woocommerce_available_payment_gateways', function( $available_gateways ) {
    if ( is_admin() ) return $available_gateways;

    // Check if COD is present; if not, forcefully add it back if the class exists
    if ( ! isset( $available_gateways['cod'] ) && class_exists( 'WC_Gateway_COD' ) ) {
        $cod = new WC_Gateway_COD();
        $cod->enabled = 'yes'; // Force enabled
        $cod->title = 'دفع عند الإستلام'; // Initial title
        $available_gateways['cod'] = $cod;
    }
    
    return $available_gateways;
}, 99 );


/**
 * Force Arabic Strings for Checkout
 * Overrides specific English strings to Arabic regardless of language setting.
 */
add_filter( 'gettext', function( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Place order':
            return 'تأكيد الدفع';
        case 'Product':
            return 'المنتج';
        case 'Subtotal':
            return 'المجموع الفرعي';
        case 'Total':
            return 'الإجمالي';
        case 'Shipping':
            return 'الشحن';
        case 'Free shipping':
            return 'شحن مجاني';
        case 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our [privacy_policy]privacy policy[/privacy_policy].':
            return 'سيتم استخدام بياناتك الشخصية لمعالجة طلبك، ودعم تجربتك في هذا الموقع، ولأغراض أخرى موصوفة في [privacy_policy]سياسة الخصوصية[/privacy_policy].';
        case 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our privacy policy.':
             return 'سيتم استخدام بياناتك الشخصية لمعالجة طلبك، ودعم تجربتك في هذا الموقع، ولأغراض أخرى موصوفة في سياسة الخصوصية.';
    }
    return $translated_text;
}, 20, 3 );

// Force Order Button Text specifically (Double check)
add_filter( 'woocommerce_order_button_text', function( $button_text ) {
    return 'تأكيد الدفع';
} );

/**
 * Force Privacy Policy Text (Direct Filter)
 * This is more reliable than gettext for the privacy notice which often has dynamic placeholders.
 */
add_filter( 'woocommerce_checkout_privacy_policy_text', function( $text ) {
    return 'سيتم استخدام بياناتك الشخصية لمعالجة طلبك، ودعم تجربتك في هذا الموقع، ولأغراض أخرى موصوفة في [privacy_policy]سياسة الخصوصية[/privacy_policy].';
} );

/**
 * Force Arabic Checkout Fields
 * Translates field labels and placeholders to Arabic.
 */
add_filter( 'woocommerce_checkout_fields' , function( $fields ) {
    // Billing Fields
    if ( isset( $fields['billing'] ) ) {
        $fields['billing']['billing_first_name']['label']       = 'الاسم الأول';
        $fields['billing']['billing_first_name']['placeholder'] = 'الاسم الأول';

        $fields['billing']['billing_last_name']['label']        = 'الاسم الأخير';
        $fields['billing']['billing_last_name']['placeholder']  = 'الاسم الأخير';

        $fields['billing']['billing_company']['label']          = 'اسم الشركة (اختياري)';
        $fields['billing']['billing_company']['placeholder']    = 'اسم الشركة';

        $fields['billing']['billing_country']['label']          = 'الدولة / المنطقة';
        
        $fields['billing']['billing_address_1']['label']        = 'العنوان';
        $fields['billing']['billing_address_1']['placeholder']  = 'اسم الشارع ورقم المنزل';

        $fields['billing']['billing_address_2']['label']        = 'تفاصيل العنوان الإضافية (اختياري)';
        $fields['billing']['billing_address_2']['placeholder']  = 'رقم الشقة، الجناح، الوحدة، إلخ (اختياري)';

        $fields['billing']['billing_city']['label']             = 'المدينة';
        $fields['billing']['billing_city']['placeholder']       = 'المدينة';

        $fields['billing']['billing_state']['label']            = 'المحافظة / المقاطعة';
        $fields['billing']['billing_state']['placeholder']      = 'المحافظة';

        $fields['billing']['billing_postcode']['label']         = 'الرمز البريدي';
        $fields['billing']['billing_postcode']['placeholder']   = 'الرمز البريدي';

        $fields['billing']['billing_phone']['label']            = 'رقم الهاتف';
        $fields['billing']['billing_phone']['placeholder']      = 'رقم الهاتف';

        $fields['billing']['billing_email']['label']            = 'البريد الإلكتروني';
        $fields['billing']['billing_email']['placeholder']      = 'البريد الإلكتروني';
    }

    // Shipping Fields (if still used somewhere)
    if ( isset( $fields['shipping'] ) ) {
        $fields['shipping']['shipping_first_name']['label']       = 'الاسم الأول';
        $fields['shipping']['shipping_last_name']['label']        = 'الاسم الأخير';
        $fields['shipping']['shipping_company']['label']          = 'اسم الشركة';
        $fields['shipping']['shipping_country']['label']          = 'الدولة';
        $fields['shipping']['shipping_address_1']['label']        = 'العنوان';
        $fields['shipping']['shipping_city']['label']             = 'المدينة';
        $fields['shipping']['shipping_postcode']['label']         = 'الرمز البريدي';
    }

    return $fields;
} );

// Also translate Default Address Fields for 'get_address_fields' users
add_filter( 'woocommerce_default_address_fields', function( $fields ) {
    if ( isset( $fields['first_name'] ) ) $fields['first_name']['label'] = 'الاسم الأول';
    if ( isset( $fields['last_name'] ) ) $fields['last_name']['label'] = 'الاسم الأخير';
    if ( isset( $fields['address_1'] ) ) $fields['address_1']['label'] = 'العنوان';
    if ( isset( $fields['city'] ) ) $fields['city']['label'] = 'المدينة';
    if ( isset( $fields['state'] ) ) $fields['state']['label'] = 'المناطق';
    if ( isset( $fields['postcode'] ) ) $fields['postcode']['label'] = 'الرمز البريدي';
    
    return $fields;
} );

/**
 * Fix Email Sender for Localhost
 * Replaces default 'wordpress@localhost' which often causes mail errors.
 */
add_filter( 'wp_mail_from', function( $email ) {
    return get_option( 'admin_email' );
} );

add_filter( 'wp_mail_from_name', function( $name ) {
    return get_bloginfo( 'name' );
} );

// Include Custom Checkout Shortcode
require_once get_template_directory() . '/inc/custom_checkout_shortcode.php';

// Include MyFatoorah Translations
require_once get_template_directory() . '/inc/myfatoorah-translations.php';



// Ensure 'Required fields are marked' is translated in review form
add_filter('comment_form_defaults', function($defaults) {
    if (isset($defaults['fields']) && isset($defaults['comment_notes_before'])) {
        $defaults['comment_notes_before'] = str_replace(
            'Required fields are marked *',
            'الحقول الإلزامية مشار إليها بـ *',
            $defaults['comment_notes_before']
        );
        $defaults['comment_notes_before'] = str_replace(
            'Required fields are marked',
            'الحقول الإلزامية مشار إليها بـ *',
            $defaults['comment_notes_before']
        );
    }
    return $defaults;
});

/**
 * Fix 400 Bad Request on REST API endpoints when _locale=user is present.
 * This appends a middleware to WordPress's apiFetch to strip the problematic
 * parameter before requests are sent.
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( wp_script_is( 'wp-api-fetch', 'enqueued' ) || wp_script_is( 'wp-api-fetch', 'registered' ) ) {
        wp_add_inline_script( 'wp-api-fetch', '
            if ( window.wp && window.wp.apiFetch ) {
                window.wp.apiFetch.use((options, next) => {
                    const cleanup = (str) => {
                        if (typeof str !== "string") return str;
                        return str.replace(/([?&])_locale=[^&]+/, (m, p1) => (p1 === "?" ? "?" : "")).replace(/\?&/, "?").replace(/\?\?+/, "?").replace(/\?$/, "");
                    };
                    if (options.path) options.path = cleanup(options.path);
                    if (options.url) options.url = cleanup(options.url);
                    if (options.params && options.params._locale) delete options.params._locale;
                    if (options.data && options.data._locale) delete options.data._locale;
                    return next(options);
                });
            }
        ', 'after' );
    }
}, 999 );

/**
 * Robust fix for 400 Bad Request on YITH routes.
 * 1. Server-side: Strip _locale and wp_lang parameters before the REST request is processed.
 */
add_filter( 'rest_pre_dispatch', function( $result, $server, $request ) {
    if ( strpos( $request->get_route(), 'yith' ) !== false ) {
        $params = $request->get_params();
        if ( isset( $params['_locale'] ) || isset( $params['wp_lang'] ) ) {
            $query = $request->get_query_params();
            if ( isset( $query['_locale'] ) ) unset( $query['_locale'] );
            if ( isset( $query['wp_lang'] ) ) unset( $query['wp_lang'] );
            $request->set_query_params( $query );
            
            $body = $request->get_body_params();
            if ( isset( $body['_locale'] ) ) unset( $body['_locale'] );
            if ( isset( $body['wp_lang'] ) ) unset( $body['wp_lang'] );
            $request->set_body_params( $body );
        }
    }
    return $result;
}, 1, 3 );

/**
 * 2. Client-side: Nuclear Option. Override apiFetch globally to ensure parameters are never sent.
 */
add_action( 'wp_footer', function() {
    ?>
    <script>
    (function() {
        var intercept = function() {
            if (window.wp && window.wp.apiFetch) {
                var original = window.wp.apiFetch;
                window.wp.apiFetch = function(options) {
                    var clean = function(s) {
                        return typeof s === 'string' ? s.replace(/([?&])_locale=user(&|$)/, '$1').replace(/[?&]$/, '') : s;
                    };
                    if (options.url) options.url = clean(options.url);
                    if (options.path) options.path = clean(options.path);
                    if (options.params && options.params._locale) delete options.params._locale;
                    if (options.data && options.data._locale) delete options.data._locale;
                    return original(options);
                };
                console.log('Wishlist Fix: apiFetch wrapped');
                return true;
            }
            return false;
        };
        if (!intercept()) {
            var i = setInterval(function() { if (intercept()) clearInterval(i); }, 50);
            setTimeout(function() { clearInterval(i); }, 5000);
        }
    })();
    </script>
    <?php
}, 9999 );


// Enqueue style.css for global CSS variables
function hello_elementor_global_style() {
	wp_enqueue_style(
		'hello-elementor-global-style',
		get_template_directory_uri() . '/style.css',
		[],
		HELLO_ELEMENTOR_VERSION
	);
}
add_action('wp_enqueue_scripts', 'hello_elementor_global_style', 5);
