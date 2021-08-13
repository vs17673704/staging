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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'staging' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         '6p<.U8rNT=9-3_pB.P_CRI!C=-i[W_>jffd^ZF15))6KlnYpX}G%=m**yxp{+5{k' );
define( 'SECURE_AUTH_KEY',  'sjKRpi-]~XT_Ng)QW&)0suLH9CiZy<oIaVg.3yk#7Vy=rN*5,nxVA;=l#$!EMg>B' );
define( 'LOGGED_IN_KEY',    '3l:C&F@b*T`<EYdBl#C%Ixg%>fMyR@/aX;azxUqx^q_ 8D7P!8?;^]gf}o@%[E{+' );
define( 'NONCE_KEY',        '3J|C@TzVcK~YUteCW=H%q|SW=UrbSfeNG%ByT333dvK^pxi8@b+-Kj!3+>zy^a[t' );
define( 'AUTH_SALT',        '|)yz?38GJV/{p77RbZBR-l<kmzYM>hCSOW|z30*ahX){QlND_J8@3[|vHw1eQK~f' );
define( 'SECURE_AUTH_SALT', 'q7RXWDra7J3=O/=lG36|A_,Z1^1h){6Ce~&&<o7!ItQuqXx|GuH)=B#}CS:z^0dn' );
define( 'LOGGED_IN_SALT',   'OR+3ElyG+d,m$ix+*;.FH75lTS,<1O,]]u!*yAISA5:8urQ&rytm.D[LRo!RE05o' );
define( 'NONCE_SALT',       '0/DC0>:*A3^$V_3-YDIEz2> iSB|O:PV3~]C;ENmgDH+(h[Pnsy`(ZF(pEg!V0m&' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
