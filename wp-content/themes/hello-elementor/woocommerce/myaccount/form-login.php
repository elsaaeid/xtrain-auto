<?php
/**
 * Login Form - Custom Override
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="login-page-split-wrapper">
    <div class="custom-login-container" id="customer_login">
        <?php do_action( 'woocommerce_before_customer_login_form' ); ?>

        <!-- Custom Tabs Navigation -->
        <div class="login-tabs-nav">
            <div class="login-tab-item active" data-target="login"><?php esc_html_e( 'تسجيل دخول', 'textdomain' ); ?></div>
            <div class="login-tab-item" data-target="register"><?php esc_html_e( 'إنشاء حساب', 'textdomain' ); ?></div>
        </div>
        <div class="login-subtitle"><?php esc_html_e( 'من فضلك قم بكتابة بياناتك التالية لتسجيل الدخول', 'textdomain' ); ?></div>

        <!-- Login Form (Column 1) -->
        <div class="u-column1 active">

            <form class="woocommerce-form woocommerce-form-login login" method="post">

                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="username"><?php esc_html_e( 'البريد الإلكتروني', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide password-row">
                    <label for="password"><?php esc_html_e( 'كلمة المرور', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />
                    <span class="custom-password-eye"></span>
                </p>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <!-- Custom Action Row: Lost Password & Remember Me -->
                <div class="login-actions-row">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                        <span><?php esc_html_e( 'تذكرني', 'woocommerce' ); ?></span>
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                    </label>

                    <p class="woocommerce-LostPassword lost_password">
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'هل نسيت كلمة المرور؟', 'woocommerce' ); ?></a>
                    </p>
                </div>

                <p class="form-row">
                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'تسجيل الدخول', 'woocommerce' ); ?>"><?php esc_html_e( 'تسجيل الدخول', 'woocommerce' ); ?></button>
                </p>

                <?php do_action( 'woocommerce_login_form_end' ); ?>

            </form>

        </div>

        <!-- Register Form (Column 2) -->
        <div class="u-column2">

            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

                <?php do_action( 'woocommerce_register_form_start' ); ?>

                <!-- Name Field -->
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_billing_first_name"><?php esc_html_e( 'الإسم', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php echo ( ! empty( $_POST['billing_first_name'] ) ) ? esc_attr( wp_unslash( $_POST['billing_first_name'] ) ) : ''; ?>" required />
                </p>

                <!-- Email Field -->
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_email"><?php esc_html_e( 'البريد الإلكتروني', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required />
                </p>

                <!-- Phone Field -->
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_billing_phone"><?php esc_html_e( 'رقم الهاتف', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" id="reg_billing_phone" value="<?php echo ( ! empty( $_POST['billing_phone'] ) ) ? esc_attr( wp_unslash( $_POST['billing_phone'] ) ) : ''; ?>" required />
                </p>

                <!-- Password Field -->
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide password-row">
                    <label for="reg_password"><?php esc_html_e( 'كلمة المرور', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required />
                    <span class="custom-password-eye"></span>
                </p>
                
                <!-- Confirm Password Field -->
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide password-row">
                    <label for="reg_password_confirm"><?php esc_html_e( 'تأكيد كلمة المرور', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_confirm" id="reg_password_confirm" required />
                    <span class="custom-password-eye"></span>
                </p>

                <!-- Terms Checkbox -->
                 <div class="woocommerce-form-row form-row terms-row" style="text-align: right; display: flex; align-items: center; gap: 8px;">
                    <label for="reg_terms" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                        <span class="woocommerce-terms-and-conditions-checkbox-text">
                            <?php esc_html_e( 'موافقة علي', 'woocommerce' ); ?> 
                            <span class="terms-link-text"><?php esc_html_e( 'الشروط والأحكام', 'woocommerce' ); ?></span>
                        </span>
                    </label>
                     <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" id="reg_terms" required />
                </div>

                <?php do_action( 'woocommerce_register_form' ); ?>

                <p class="woocommerce-form-row form-row">
                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'إنشاء حساب', 'woocommerce' ); ?></button>
                </p>

                <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>

        </div>

    </div>

    <?php echo do_shortcode('[registration_cover]'); ?>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
