<?php
/**
 * WeGlot Switcher Shortcode
 *
 * Renders the WeGlot language switcher container.
 * Usage: [weglot_switcher]
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_shortcode( 'weglot_switcher', function () {
	return '<div id="weglot_here"></div>';
} );
