<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ipartsksa_xtrain_auto');

/** Database username */
define('DB_USER', 'ipartsksa_saeid');

/** Database password */
define('DB_PASSWORD', '@IfW8QbNB@lM7a2B');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '(A&#9W&a`ZXwyz+:}T^W*)>v?{1fT[@cri&A_*ldMNxe~`ZE66>MQcSN{!pW%+-5' );
define( 'SECURE_AUTH_KEY',  'HHD1j%.[WWO`h<Tx{FM03DeSCtnQc86x2{!DEt]Nm,OV:<XT/(m%$1k=1s!2hMl-' );
define( 'LOGGED_IN_KEY',    '{aV;/#UPDkS MKy+f:c*vao{]r#pg(e]?^AD;YXu3gUUCEegw`k@>+rwEr6^zup`' );
define( 'NONCE_KEY',        'm&+S<)!io[AH|n2JWoPa!HJgnCR,c~!aSTl]j$IR^.(6MX9#VTH)w*V+P[VNDnvl' );
define( 'AUTH_SALT',        'HP$I_{`eHr8`BjTXVaGj+6NhK2*Mk2p#|m2g!RY3v~G)`c|&k4H~BJtG!3_v4jSG' );
define( 'SECURE_AUTH_SALT', 'nVdQFW7%R;<XEyVnQ.]b_E>gkdIFo tF0@61EYZ:xyAG,pWA+s`Qd7W7]nRKos-#' );
define( 'LOGGED_IN_SALT',   '?U@=4v;Spw9+lmPMHy&ej[mr0]*^0Y5Ng*N+o2F|i{(cRxkL^p&<l:xesXy+#=q7' );
define( 'NONCE_SALT',       '#.}Y$c]8K ZuDq;Re$.i8`:7uM~&Q/KOy3z,SoRa2AW-jX19/Msi|hXg8 ur[(4J' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */

// Force HTTPS on staging site (disabled for localhost)
define( 'FORCE_SSL_ADMIN', false );
define( 'FORCE_SSL_LOGIN', false );

// Update site URLs for staging environment
define( 'WP_HOME', 'https://said-staging.xtrainauto.com' );
define( 'WP_SITEURL', 'https://said-staging.xtrainauto.com' );

// Increase memory limit for Elementor
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Increase maximum execution time
@ini_set( 'max_execution_time', '300' );
@set_time_limit( 300 );

// Allow unfiltered uploads for Elementor templates
define( 'ALLOW_UNFILTERED_UPLOADS', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
