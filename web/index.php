<?php
/**
 * Point d'entrée du site : charge WordPress depuis web/wp/.
 *
 * @package WPStarter
 */

define( 'WP_USE_THEMES', true );
require __DIR__ . '/wp/wp-blog-header.php';
