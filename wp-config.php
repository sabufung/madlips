<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'u748031550_face');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ';fc~0Vf/}d!p9BriEhJ=QT.:a;x`s}^2gc!Fz}Z]PvX!Ganmc=<T{q9H>N;Z.s#&');
define('SECURE_AUTH_KEY',  'CIVw!;k;M#<Spa~3_%~UimNWL3|e,4h.+rIOzvgyJlYO^Z7W}i?NO*Ml#8zdwUne');
define('LOGGED_IN_KEY',    'ct&.Z!%1ne_GS6HJsd0lD]?vsH;{t}FiTG}TSchCbEm,C_C-dr:mq)bU6szAZiNI');
define('NONCE_KEY',        '[P!pQo4icvh a9Hft2i[Olyi1tm]7PeNg$_kU;&ju;[Tvc}Uv$(RYQWv&vX(B2l}');
define('AUTH_SALT',        ']&pZq3R+B)$s`FDWQN=~Io_YCta/n&-W)?}@.E][YVS`_nJ_lJvH6u{*#C/F^EsT');
define('SECURE_AUTH_SALT', 'q9T~$-7;-$MUDI$-p-JaI2JxHEK^a]{pZYc]slPUYrf>M:f1B9Gxze P,XV*wEF?');
define('LOGGED_IN_SALT',   'mhDv5OB84a )x]xTwrYHU9pN[Oeebdo ]i;}O^ih[rzr55pS7{O,ZX]i$Ktd^a6Y');
define('NONCE_SALT',       'F50)0f%9Ewc-Wl=6/]@0i=_LBP0Y*8I^R,c=A;}9`rsd#?{t0?8C9?Lk:i9w7X9L');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'face';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
