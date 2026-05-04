<?php
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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'dor|/s/7R)i%;74/f;mAjxj}tg0|}+s<hUj>|&%9U~g,B> /J||e?UhFd=4[Rt*e' );
define( 'SECURE_AUTH_KEY',  'E`8a-aEI35DckZa9dDTUG@b@fKg6;^OQA)I2v53a0;}T!A6p><P<lQxhFlfa[fi@' );
define( 'LOGGED_IN_KEY',    'qYVnka5)3D|I#=yE;ak23xu16A,29Q J#Y%wSho7,flcMRSss-8RVyZIDf;7ae( ' );
define( 'NONCE_KEY',        '=As2JUfqIM?v@w-L8UT1DTBqj&*udEH<Ol ?Gf}*(>kd5ZF!O| )*46_xqC6e<#u' );
define( 'AUTH_SALT',        'A3,U0@p7`!g3kj9tlz>)xLd*[6MHGDg>tN3`!~%Ju87(UiP})!bd|`i=;#|sfDrF' );
define( 'SECURE_AUTH_SALT', 'N0.$n?ws{zS#1<yQT9m5m65IGY%nic^>t~==Aa?nB^v,;7<IaUzCbyuuc6x3h4W*' );
define( 'LOGGED_IN_SALT',   'E;j8nzg;5sh-L4tqPZ%Z<N9:I8]Z]+>s#g`]OTsdPk{pk`w( dtJgE#ScZ`+&@?9' );
define( 'NONCE_SALT',       'QKWq:?G-l=gUyk9)A/;,G03kvA6ll[IiJE3X*aqklxCJUwl&/V7+]Tz7ea.2S0ox' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
