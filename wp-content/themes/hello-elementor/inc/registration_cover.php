<?php
/**
 * Shortcode: Registration Cover
 * Usage: [registration_cover]
 * Fetches the registration cover image from ACF 'register_cover' field in 'login_content' post type
 */
function registration_cover_shortcode() {
    if ( ! function_exists('get_field') ) {
        return '';
    }

    $covers = [
        'login' => '',
        'register' => '',
        'lost' => ''
    ];

    // Priority 1: ACF Options Page
    $field_login = get_field('login_cover', 'option');
    $field_reg   = get_field('register_cover', 'option');
    $field_lost  = get_field('lost_password_cover', 'option');

    // Priority 2: login_content Post Type (Backward Compatibility/Fallback)
    if (!$field_login && !$field_reg && !$field_lost) {
        $args = array('post_type' => 'registeration_cover', 'posts_per_page' => 1, 'post_status' => 'publish');
        $q = new WP_Query($args);
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $p_id = get_the_ID();
                $field_login = get_field('login_cover', $p_id);
                $field_reg   = get_field('register_cover', $p_id);
                $field_lost  = get_field('lost_password_cover', $p_id);
            }
            wp_reset_postdata();
        }
    }

    // Process URLs
    $covers['login']    = is_array($field_login) ? $field_login['url'] : $field_login;
    $covers['register'] = is_array($field_reg)   ? $field_reg['url']   : $field_reg;
    $covers['lost']     = is_array($field_lost)  ? $field_lost['url']  : $field_lost;

    // Last Resort Fallback (Gallery check in register_cover if single fields are empty)
    if (empty($covers['login']) && is_array($field_reg) && isset($field_reg[0])) {
        $covers['login']    = isset($field_reg[0]['url']) ? $field_reg[0]['url'] : $field_reg[0];
        $covers['register'] = isset($field_reg[1]['url']) ? $field_reg[1]['url'] : (isset($field_reg[1]) ? $field_reg[1] : $covers['login']);
        $covers['lost']     = isset($field_reg[2]['url']) ? $field_reg[2]['url'] : (isset($field_reg[2]) ? $field_reg[2] : $covers['login']);
    }

    // Determine default active class based on page
    $active_type = 'login';
    if ( function_exists('is_lost_password_page') && is_lost_password_page() ) {
        $active_type = 'lost';
    }

    ob_start(); ?>
    <div class="login-registration-cover">
        <?php if ($covers['login']): ?>
            <img src="<?php echo esc_url($covers['login']); ?>" class="cover-img cover-login<?php echo ($active_type === 'login' ? ' active' : ''); ?>" alt="Login">
        <?php endif; ?>
        <?php if ($covers['register']): ?>
            <img src="<?php echo esc_url($covers['register']); ?>" class="cover-img cover-register" alt="Register">
        <?php endif; ?>
        <?php if ($covers['lost']): ?>
            <img src="<?php echo esc_url($covers['lost']); ?>" class="cover-img cover-lost<?php echo ($active_type === 'lost' ? ' active' : ''); ?>" alt="Lost Password">
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('registration_cover', 'registration_cover_shortcode');
