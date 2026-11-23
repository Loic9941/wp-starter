<?php
/**
 * Title: Hero
 * Slug: starter-theme/hero
 * Categories: starter
 * Keywords: hero, bannière, accroche, couverture
 * Description: Titre, sous-titre et bouton sur fond de couleur. Une image de fond peut être ajoutée depuis la barre d'outils du bloc Couverture.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:cover {"overlayColor":"primary","isUserOverlayColor":true,"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container">
	<!-- wp:heading {"textAlign":"center","textColor":"background","fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-background-color has-text-color has-x-large-font-size"><?php esc_html_e( 'Un titre clair et accrocheur', 'starter-theme' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","textColor":"background","fontSize":"medium"} -->
	<p class="has-text-align-center has-background-color has-text-color has-medium-font-size"><?php esc_html_e( 'Un sous-titre qui présente votre proposition de valeur en une phrase.', 'starter-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"accent","textColor":"background"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-background-color has-accent-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( home_url( '/a-propos/' ) ); ?>"><?php esc_html_e( 'En savoir plus', 'starter-theme' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div></div>
<!-- /wp:cover -->
