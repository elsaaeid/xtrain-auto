<?php
/**
 * Custom Thank You Page Template
 * Overrides the default WooCommerce order received page to show a success modal.
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Full Screen Modal Overlay -->
<div class="order-success-backdrop">
    <div class="order-success-card">
        
        <!-- Animated Icon -->
        <div class="success-icon-wrapper">
            <div class="success-circle-outer">
                <div class="success-circle-inner">
                    <div class="success-checkmark"></div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <h2 class="order-success-title">تمت العملية بنجاح</h2>
        
        <p class="order-success-message">
            تم إرسال تفاصيل طلبك بنجاح إلى البريد الإلكتروني.<br>
            شكراً لثقتك بنا.
        </p>

        <!-- Return to Home Button -->
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-return-store">العودة للمتجر</a>

    </div>
</div>