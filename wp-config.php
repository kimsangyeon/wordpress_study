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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '3v9!02ISmK{J`lZyuDXZVG!.+kguea8rl,o^9om:(RY}dtooP,V:&1syM<x`Pn>.');
define('SECURE_AUTH_KEY',  '&uP!C.q(7Nm)vRsOYj,b|{|!w@SA-}AL2OQ+rpRa^g%ibl|F]&p`Sb)8Hcia&Mz^');
define('LOGGED_IN_KEY',    'okfkG{cOJ<I*T7DE:D&LD-ROf.K98ytHVM%@vl.QZK 0x-@X9}X|bQJ~Z{CVR5ES');
define('NONCE_KEY',        'L H0P.1pmNw9T~lb:ohjc{w>+:B.(hf%)*m;Hq>jqs] Ds W_?:.l78HbeJpjWYC');
define('AUTH_SALT',        'vw9~htu8J;N|V? k}Su}/%@da_Cmh9)ZYtE{d]scI8I3+|8!an8N/K_i-.`#w3wn');
define('SECURE_AUTH_SALT', 'l3&{AsGlM O5H!O(SH5rul2@17`Cz1$+2JO,^Rbz6#{wDm&9{vJ-t@~hhP>p#TqM');
define('LOGGED_IN_SALT',   'SD82V#8B!YhzFu!.`7vC7O$vpD$8N{1}!n)-vK=w4q]S87&^b71.gkFVf:v@;$oc');
define('NONCE_SALT',       '?s$UXMl|Amf]49pNO&EuSra.yr<XJz4D8{T9/4 %YG:5288G1H;U6V[zJ 03C<f#');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
