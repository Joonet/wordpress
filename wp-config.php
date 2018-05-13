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
define('DB_PASSWORD', '123456');

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
define('AUTH_KEY',         'r#sr>aO]rENW#C54AA3E|F,^ru} o{!sCV!m$00eW0AK.9=H&1M-3a:B);_a_3az');
define('SECURE_AUTH_KEY',  'LA ,wvi8z~/xRkX%Z;75;=X:!*|NklrWGT(?~w~*5dj^>dpl%``Fm/#`vqCoKFNj');
define('LOGGED_IN_KEY',    'XyS5-kEfdKxTmz!cR?g]4;L]jK.Rn=y)-G8)Gr.u44S]9+j5Rs2ePHgE_gM5+Gn>');
define('NONCE_KEY',        'Wg^1W`Jk~n55ukNcARywx*V^l f]&KFBg!k`oU&2s22iM{ %7:L-bd YXFD0E3[W');
define('AUTH_SALT',        '[?96^>pn7?}[n ZtYV-T^BTP4y-grC+J,}s9kOQou+I_FaY`>j#ojq9ExOz~U@.}');
define('SECURE_AUTH_SALT', 'so6!8SFveu3%@FT)}~Nig Bi_?L)v]p^D&cXAJ:uVUq|-0k@EpssX7aV*#o&%z}a');
define('LOGGED_IN_SALT',   'W~;TA/}NUmGvnFP:k+^fRnq(JUy;qcR//BAM=oWOH]wJD6EO>Kc2nV*@k.PN47sC');
define('NONCE_SALT',       '8:5v=T{IUlA-A0n_0mWw&%u7{fg;fA$m&_vPuKD/?WpQc|9?h0s4hTE`*R+L}:[E');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'prints_';

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
