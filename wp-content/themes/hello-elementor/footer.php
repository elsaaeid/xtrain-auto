<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// First try to use Elementor Theme Builder footer, fallback to template footer
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	// Fallback to template site footer if no Elementor footer template exists
	get_template_part( 'template-parts/footer' );
}
?>

<?php wp_footer(); ?>

</body>
</html>
