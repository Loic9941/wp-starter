<?php
/**
 * Plugin Name: Pages du site
 * Description: Crée les pages déclarées ci-dessous si elles n'existent pas, et règle les permaliens. Le contenu des pages vient des templates du thème (templates/page-{slug}.html).
 * Version:     1
 *
 * Chargé automatiquement (mu-plugin) : rien à activer, ni en local ni en
 * ligne. Le travail n'est fait qu'une fois par valeur de SITE_PAGES_VERSION.
 *
 * Ajouter une page :
 * 1. créer templates/page-{slug}.html dans le thème ;
 * 2. ajouter le slug et le titre dans site_pages_list() ;
 * 3. incrémenter SITE_PAGES_VERSION.
 *
 * Une page existante n'est jamais modifiée ni supprimée : retirer un slug
 * de la liste ne supprime pas la page.
 *
 * @package wp-starter
 */

defined( 'ABSPATH' ) || exit;

const SITE_PAGES_VERSION = '1';

/**
 * Pages du site : slug => titre.
 *
 * @return array<string, string>
 */
function site_pages_list(): array {
	return array(
		'a-propos' => 'À propos',
		'contact'  => 'Contact',
	);
}

add_action( 'init', static function (): void {
	if ( wp_installing() || ! is_blog_installed() ) {
		return;
	}

	if ( get_option( 'site_pages_version' ) === SITE_PAGES_VERSION ) {
		return;
	}

	foreach ( site_pages_list() as $slug => $title ) {
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}

		wp_insert_post( array(
			'post_type'   => 'page',
			'post_name'   => $slug,
			'post_title'  => $title,
			'post_status' => 'publish',
		) );
	}

	// Permaliens /%postname%/ : sans eux, les liens du menu (/contact/)
	// renvoient une 404.
	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	// Écrit aussi web/.htaccess sous Apache (OVH) s'il est inscriptible.
	flush_rewrite_rules( true );

	update_option( 'site_pages_version', SITE_PAGES_VERSION );
}, 20 );
