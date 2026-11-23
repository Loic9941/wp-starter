<?php
/**
 * Title: Contact – coordonnées
 * Slug: starter-theme/contact-infos
 * Categories: starter
 * Keywords: contact, adresse, horaires, téléphone, email
 * Description: Trois colonnes (adresse, horaires, téléphone et email) suivies d'un bouton pour écrire un email.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

// Coordonnées fictives, à remplacer pour chaque client.
$starter_email     = 'contact@example.com';
$starter_phone     = '01 23 45 67 89';
$starter_phone_uri = 'tel:+33123456789';
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:paragraph {"fontSize":"medium"} -->
	<p class="has-medium-font-size"><?php esc_html_e( 'Une question, un projet ? Écrivez-nous ou appelez-nous, nous vous répondons sous 48 heures ouvrées.', 'starter-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2,"fontSize":"medium"} -->
			<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Adresse', 'starter-theme' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( '12 rue de l’Exemple', 'starter-theme' ); ?><br><?php esc_html_e( '75000 Ville', 'starter-theme' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2,"fontSize":"medium"} -->
			<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Horaires', 'starter-theme' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Du lundi au vendredi : 9 h – 18 h', 'starter-theme' ); ?><br><?php esc_html_e( 'Samedi : 9 h – 12 h', 'starter-theme' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2,"fontSize":"medium"} -->
			<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Nous joindre', 'starter-theme' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><a href="<?php echo esc_url( $starter_phone_uri, array( 'tel' ) ); ?>"><?php echo esc_html( $starter_phone ); ?></a><br><a href="<?php echo esc_url( 'mailto:' . $starter_email ); ?>"><?php echo esc_html( $starter_email ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( 'mailto:' . $starter_email ); ?>"><?php esc_html_e( 'Nous écrire', 'starter-theme' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
