<?php
/**
 * Fallback requis par WordPress. Le rendu est géré par Bricks.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
}

get_footer();
