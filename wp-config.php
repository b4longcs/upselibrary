<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'upselibrary' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'cJkjh6;gYO-i8yP@r_=2@{ha7DE>4VMR|(z45`Oi$<R`z|x=o/`|W8@+D)uo=!Fa' );
define( 'SECURE_AUTH_KEY',   'U@G W=#nUB]>NiV&|DU/7U,F;+~06anQ*(/<^+3T:}^@h_d`O.NFk5beK/qqC!|J' );
define( 'LOGGED_IN_KEY',     '}vmcp9b!6uJ}QzfNj!21lRvkva</}CIo>tN}FCa;?R{Ssms%lMX(^5#8NjoB#`k7' );
define( 'NONCE_KEY',         '?Pfj2BxF>:LPJ1o/,Jez@jCgs__r_,{!bI;6N;kAxVKs{36aI)gERA/H9xK<=.|G' );
define( 'AUTH_SALT',         'NPpd#^P;Or!_k<VI%%M;f>~U$ZH~xzmz*:;v,/I/qLuej9gZt4)p **S#FJ1e&4:' );
define( 'SECURE_AUTH_SALT',  '5:kZ1PH(:f.S2gwQt.x(QZVsL$/poXdpEOt6Hml(f  !b0__mBrAtaz,-Ek}qq@d' );
define( 'LOGGED_IN_SALT',    'IJrT WLz0Npfr5dZfooS/Rhy26In;EJTux{fDBT![)l+r@YQ7>aKdy{=i3ZuQP$h' );
define( 'NONCE_SALT',        'W;:1Wm:,@3yJU!$O~{y;x}FlVP[!rMX2|Q_W%X WuY/+n2BMjJ0DsBn!yL~;_fEE' );
define( 'WP_CACHE_KEY_SALT', 'f6)k3j]Yl wmsD1u[_ch]YF<K3MxjBPmZ_PVm{Pn5Zm>>715C_Lf)O@moBA^T/,f' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
