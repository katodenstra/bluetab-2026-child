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
define( 'DB_NAME', 'wp_Bluetab_db' );

/** Database username */
define( 'DB_USER', 'wp_Bluetab_user' );

/** Database password */
define( 'DB_PASSWORD', 'wp_Bluetab_pw' );

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
define( 'AUTH_KEY',          '6jzof1|Mxz0J[`,mWwf :p$!$&HQUH1 .5K3pSQ`+y]($~$piwdniLoNnX25|iL_' );
define( 'SECURE_AUTH_KEY',   'so1qAv>3sEWj/y5!dCXx3yhRGSYJuNG ^`BNU><@3%bi2KP?whJ*5p-u_=00w{,&' );
define( 'LOGGED_IN_KEY',     'xTmDIDK$*kp^YJ`Hm7`bv&Y:XZIB`hISG^`XMGZ4*#e!F$PZ1Cu4Aw64vK t5U/p' );
define( 'NONCE_KEY',         'mqGG^pv~oov/,~^GK;^XNq!iRl!;u`%*9E[!{yWT!Xq-iF}^rp)5K?s.X~YlZeEo' );
define( 'AUTH_SALT',         'd!:>w@x yM7|1e-z@6{aR=R+!+OXthmucEahk^)OI}{P~|0Vd5M}`I lTk0=L1_n' );
define( 'SECURE_AUTH_SALT',  'xU$]= }0K;dn_h>zaB[jBJG5/{Yp*1j+1ZC{f,g0:JB[..FC(LYvKu8zzJpF7Ot~' );
define( 'LOGGED_IN_SALT',    'Uyv,<un /yAu^tg<$!~Y^*nU1DhS`ca_NU@DRu3jCO0Bl&CV`]uEB3 :@QR%%:{q' );
define( 'NONCE_SALT',        'Nw1[(g~,t^2`^DX=}SAseiw@[+1i(.hATt{+DlPm LxnHR}7NvK[-X(R$nr(FM6]' );
define( 'WP_CACHE_KEY_SALT', '8m,eaA1!3=rREDElM`ov:@2h,gn!$PJ0o:Ti iO96N%@>t!NOThpxb`cvRiIhQ7c' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
