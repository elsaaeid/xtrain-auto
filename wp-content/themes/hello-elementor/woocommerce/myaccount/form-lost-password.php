<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="login-page-split-wrapper">
    <div class="custom-login-container custom-lost-password-container">
        <?php do_action( 'woocommerce_before_lost_password_form' ); ?>
        <div class="lost-password-header">
            <h2 class="lost-password-title"><?php esc_html_e( 'نسيت كلمة المرور', 'woocommerce' ); ?></h2>
            <p class="lost-password-subtitle"><?php esc_html_e( 'من فضلك قم بكتابة بياناتك التالية لتسجيل الدخول', 'woocommerce' ); ?></p>
        </div>

        <form method="post" class="woocommerce-ResetPassword lost_reset_password">

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="user_login"><?php esc_html_e( 'البريد الإلكتروني', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
            </p>

            <div class="clear"></div>

            <?php do_action( 'woocommerce_lostpassword_form' ); ?>

            <p class="woocommerce-form-row form-row">
                <input type="hidden" name="wc_reset_password" value="true" />
                <button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'إرسال الكود', 'woocommerce' ); ?>"><?php esc_html_e( 'إرسال الكود', 'woocommerce' ); ?></button>
            </p>

            <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

        </form>
    </div>

    <?php echo do_shortcode('[registration_cover]'); ?>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );
