<?php
/**
 * Starter Theme – fonctions du thème.
 *
 * Thème à blocs : l'essentiel de la configuration est dans theme.json.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

define( 'STARTER_THEME_VERSION', '0.2.0' );

/**
 * Supports du thème.
 *
 * WordPress active déjà automatiquement pour les thèmes à blocs :
 * title-tag, post-thumbnails, responsive-embeds, editor-styles, html5,
 * automatic-feed-links. Inutile de les redéclarer ici.
 */
add_action( 'after_setup_theme', static function (): void {
	// Traductions du thème.
	load_theme_textdomain( 'starter-theme', get_template_directory() . '/languages' );

	// Styles « opinionated » du cœur pour les blocs (citations, séparateurs…),
	// non chargés par défaut.
	add_theme_support( 'wp-block-styles' );
} );

/**
 * Catégorie de patterns du thème.
 *
 * Les fichiers de patterns/ sont enregistrés automatiquement par WordPress ;
 * il suffit de déclarer la catégorie qu'ils utilisent.
 */
add_action( 'init', static function (): void {
	register_block_pattern_category(
		'starter',
		array( 'label' => __( 'Starter', 'starter-theme' ) )
	);
} );

/**
 * Feuille de style du thème (front).
 */
add_action( 'wp_enqueue_scripts', static function (): void {
	wp_enqueue_style(
		'starter-theme',
		get_stylesheet_uri(),
		array(),
		STARTER_THEME_VERSION
	);
} );
