<?php
/**
 * Boilerplate Theme – fonctions du thème.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

define( 'BOILERPLATE_THEME_VERSION', '0.1.0' );

/**
 * Supports du thème.
 */
add_action( 'after_setup_theme', static function (): void {
	load_theme_textdomain( 'starter-theme', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
} );

/**
 * Styles du thème (chargés après ceux de Bricks si présent).
 */
add_action( 'wp_enqueue_scripts', static function (): void {
	wp_enqueue_style(
		'starter-theme',
		get_stylesheet_uri(),
		wp_style_is( 'bricks-frontend', 'registered' ) ? array( 'bricks-frontend' ) : array(),
		BOILERPLATE_THEME_VERSION
	);
}, 20 );

/**
 * Hooks Bricks – actifs uniquement si Bricks est installé.
 */
add_action( 'init', static function (): void {
	if ( ! defined( 'BRICKS_VERSION' ) ) {
		return;
	}

	// Enregistrer ici les éléments Bricks personnalisés, ex. :
	// \Bricks\Elements::register_element( __DIR__ . '/elements/mon-element.php' );
}, 11 );
