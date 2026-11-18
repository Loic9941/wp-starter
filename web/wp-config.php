<?php
/**
 * WordPress cherche wp-config.php dans ABSPATH (web/wp/) puis un niveau
 * au-dessus (web/). La vraie configuration est hors du docroot.
 *
 * Le chargement de wp-settings.php doit rester ici, écrit tel quel :
 * WP-CLI lit ce fichier et refuse de fonctionner s'il ne trouve pas
 * la ligne `require_once ABSPATH . 'wp-settings.php';`.
 *
 * @package WPStarter
 */

require_once dirname( __DIR__ ) . '/wp-config.php';

defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__ . '/wp/' );
require_once ABSPATH . 'wp-settings.php';
