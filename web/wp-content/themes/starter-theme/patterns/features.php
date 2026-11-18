<?php
/**
 * Title: Points forts
 * Slug: starter-theme/features
 * Categories: starter
 * Keywords: fonctionnalités, avantages, services, colonnes, grille
 * Description: Titre de section suivi d'une grille de trois colonnes (titre + texte court).
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;

$starter_features = array(
	array(
		'title' => __( 'Simple', 'starter-theme' ),
		'text'  => __( 'Une prise en main immédiate, sans configuration complexe.', 'starter-theme' ),
	),
	array(
		'title' => __( 'Rapide', 'starter-theme' ),
		'text'  => __( 'Des pages légères qui se chargent en un instant.', 'starter-theme' ),
	),
	array(
		'title' => __( 'Fiable', 'starter-theme' ),
		'text'  => __( 'Une base solide, maintenue et pensée pour durer.', 'starter-theme' ),
	),
);
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","fontSize":"large"} -->
	<h2 class="wp-block-heading has-text-align-center has-large-font-size"><?php esc_html_e( 'Nos points forts', 'starter-theme' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<?php foreach ( $starter_features as $starter_feature ) : ?>
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"fontSize":"medium"} -->
			<h3 class="wp-block-heading has-medium-font-size"><?php echo esc_html( $starter_feature['title'] ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( $starter_feature['text'] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
