<?php
/**
 * Contact Info Shortcode
 *
 * Displays contact information (phone, email, address) from ACF fields.
 * Usage: [contact_info]
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function contact_info_shortcode() {

    // Ensure ACF exists
    if ( ! function_exists('get_field') ) {
        return '';
    }

    $args = array(
        'post_type'      => 'contact_info', // change if needed
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );

    $q = new WP_Query($args);

    if ( ! $q->have_posts() ) {
        return '';
    }

    ob_start();

    while ( $q->have_posts() ) : $q->the_post();

        $post_id = get_the_ID();

        // ACF fields
        $phone   = get_field('phone_number', $post_id);
        $email   = get_field('email', $post_id);
        $address = get_field('address', $post_id);
        ?>

        <div class="contact-info-wrapper">
            <div class="contact-info-container">

                <?php if ( $phone ) : ?>
                    <div class="contact-item contact-phone">
                        <h4 class="contact-label">رقم الهاتف</h4>
                        <a href="tel:<?php echo esc_attr( preg_replace('/\s+/', '', $phone) ); ?>">
                            <?php echo esc_html($phone); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ( $email ) : ?>
                    <div class="contact-item contact-email">
                        <h4 class="contact-label">البريد الإلكتروني</h4>
                        <a href="mailto:<?php echo esc_attr($email); ?>">
                            <?php echo esc_html($email); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ( $address ) : ?>
                    <div class="contact-item contact-address">
                        <h4 class="contact-label">العنوان</h4>
                        <p><?php echo esc_html($address); ?></p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php
    endwhile;

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('contact_info', 'contact_info_shortcode');
