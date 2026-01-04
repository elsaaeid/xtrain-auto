<?php
/**
 * Custom Currency Switcher Dropdown (Checkbox-based)
 * Place in your theme: /wp-content/themes/hello-elementor/woo-multi-currency/woo-multi-currency-selector.php
 * 
 * Displays the active currency symbol and allows switching between available currencies.
 * Currency symbols are automatically localized: "ر.س." for Arabic, "R.S." for English.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$currencies       = $settings->get_list_currencies();
$current_currency = $settings->get_current_currency();
$links            = $settings->get_links();
$currency_name    = get_woocommerce_currencies();

// Choose short labels per language (fallback to code)
$is_ar = function_exists( 'hello_is_arabic_context' ) && hello_is_arabic_context();
$labels_en = array(
    'SAR' => 'R.S.',
    'AED' => 'AED',
    'EGP' => 'EGP',
);
$labels_ar = array(
    'SAR' => 'ر.س.',
    'AED' => 'د.إ.',
    'EGP' => 'ج.م.',
);
$current_label = $is_ar
    ? ( $labels_ar[ $current_currency ] ?? $current_currency )
    : ( $labels_en[ $current_currency ] ?? $current_currency );
$dropdown_id = 'wmc-currency-toggle-' . uniqid();
$arrow_url = get_stylesheet_directory_uri() . '/assets/images/wgarrowdown.png';
?>
<div class="woo-multi-currency wmc-shortcode">
    <div class="wmc-currency custom-currency-dropdown dropdown">
        <input type="checkbox" id="<?php echo esc_attr($dropdown_id); ?>" hidden>
        <label for="<?php echo esc_attr($dropdown_id); ?>" class="wmc-current-currency">
            <?php echo esc_html($current_label); ?>
            <span class="wmc-current-currency-arrow dropdown-arrow"></span>
        </label>
        <ul class="currency-list wmc-sub-currency">
            <?php foreach ($links as $code => $link) {
                if ($current_currency === $code) continue;
                $label = $is_ar ? ( $labels_ar[$code] ?? $code ) : ( $labels_en[$code] ?? $code );
                $name = $currency_name[$code];
                $name = apply_filters('wmc_shortcode_currency_display_text', $name, $code);
            ?>
                <li class="wmc-currency">
                    <a href="<?php echo esc_url($link); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
