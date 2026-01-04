<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="lost-password-overlay">
    <div class="lost-password-modal">
        <div class="lp-modal-icon">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reset-image.png" alt="Success">
        </div>
        <h3 class="lp-modal-title">تمت العملية بنجاح</h3>
        <p class="lp-modal-text">من فضلك قم بكتابة بياناتك التالية لتسجيل الدخول</p>
        <div class="lp-modal-action">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="lp-modal-btn">عودة للرئيسية</a>
        </div>
    </div>
</div>
