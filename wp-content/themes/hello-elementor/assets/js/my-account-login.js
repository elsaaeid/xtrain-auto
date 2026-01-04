jQuery(document).ready(function ($) {

    // Only run if the login container exists
    if ($('#customer_login').length) {

        // Logic to switch tabs
        $('.login-tab-item').on('click', function () {
            var target = $(this).data('target'); // 'login' or 'register'

            // Update Tab Active State
            $('.login-tab-item').removeClass('active');
            $(this).addClass('active');

            // Hide any existing notices when switching tabs
            $('.woocommerce-notices-wrapper').slideUp(200);

            // Toggle Cover Images
            $('.login-registration-cover .cover-img').removeClass('active');
            if (target === 'login') {
                $('.login-registration-cover .cover-login').addClass('active');
                $('.u-column2, .col-2').removeClass('active').hide();
                $('.u-column1, .col-1').addClass('active').show();
            } else {
                $('.login-registration-cover .cover-register').addClass('active');
                $('.u-column1, .col-1').removeClass('active').hide();
                $('.u-column2, .col-2').addClass('active').show();
            }
        });

        // If on Lost Password page, show the 'lost' cover
        if ($('.custom-lost-password-container').length) {
            $('.login-registration-cover .cover-img').removeClass('active');
            $('.login-registration-cover .cover-lost').addClass('active');
        }

        // Cleanup: Hide the wrapper if it has no list items (li)
        $('.woocommerce-error, .woocommerce-message, .woocommerce-info').each(function () {
            var $notice = $(this);
            if ($notice.find('li').length > 0) {
                $notice.addClass('show-notice').show();
            } else {
                $notice.removeClass('show-notice').hide();
            }
        });

        // If the wrapper itself is empty of everything, remove it
        if ($('.woocommerce-notices-wrapper').is(':empty') || $('.woocommerce-notices-wrapper').children(':visible').length === 0) {
            $('.woocommerce-notices-wrapper').hide();
        }

        // Initialize: Ensure Login (col-1) is shown by default and Register hidden
        // This is important because CSS might hide both by default
        if ($('.login-tab-item[data-target="login"]').hasClass('active')) {
            $('.u-column1, .col-1').addClass('active').show();
            $('.u-column2, .col-2').removeClass('active').hide();
        }
    }

    // Password Toggle Logic - Use native events to bypass hijacking
    document.addEventListener('mousedown', function (e) {
        var btn = e.target.closest('.custom-password-eye');
        if (!btn) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        var row = btn.closest('.password-row');
        var input = row ? row.querySelector('input') : null;

        if (input) {
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.classList.toggle('visible', isPassword);
        }
    }, true); // Use capture phase to be first in line

});
