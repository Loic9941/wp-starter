<?php
/**
 * WordPress cherche wp-config.php dans ABSPATH (web/wp/) puis un niveau
 * au-dessus (web/). La vraie configuration est hors du docroot.
 *
 * @package WPStarter
 */

require_once dirname( __DIR__ ) . '/wp-config.php';
