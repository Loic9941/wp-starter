<?php
/**
 * Title: À propos – présentation
 * Slug: starter-theme/about-intro
 * Categories: starter
 * Keywords: à propos, présentation, histoire, chiffres clés, équipe
 * Description: Texte de présentation suivi de trois chiffres clés.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

$starter_figures = array(
	array(
		'value' => __( '10 ans', 'starter-theme' ),
		'label' => __( 'd’expérience dans notre domaine', 'starter-theme' ),
	),
	array(
		'value' => __( '500+', 'starter-theme' ),
		'label' => __( 'clients accompagnés', 'starter-theme' ),
	),
	array(
		'value' => __( '98 %', 'starter-theme' ),
		'label' => __( 'de clients satisfaits', 'starter-theme' ),
	),
);
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size"><?php esc_html_e( 'Nous accompagnons particuliers et professionnels avec une conviction simple : un service de qualité repose sur l’écoute et la proximité.', 'starter-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Fondée par une petite équipe passionnée, notre structure a grandi au fil des rencontres et des projets. Chaque demande est traitée avec le même soin, du premier échange jusqu’au suivi.', 'starter-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Aujourd’hui, nous continuons d’avancer avec les mêmes valeurs : transparence, exigence et disponibilité.', 'starter-theme' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<?php foreach ( $starter_figures as $starter_figure ) : ?>
		<!-- wp:column {"backgroundColor":"light-gray","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20"}}} -->
		<div class="wp-block-column has-light-gray-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
			<!-- wp:paragraph {"align":"center","textColor":"primary","fontSize":"x-large","style":{"typography":{"fontWeight":"700"}}} -->
			<p class="has-text-align-center has-primary-color has-text-color has-x-large-font-size" style="font-weight:700"><?php echo esc_html( $starter_figure['value'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"align":"center","textColor":"secondary"} -->
			<p class="has-text-align-center has-secondary-color has-text-color"><?php echo esc_html( $starter_figure['label'] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
