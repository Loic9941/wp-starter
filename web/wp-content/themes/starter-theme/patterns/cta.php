<?php
/**
 * Title: Appel à l'action
 * Slug: starter-theme/cta
 * Categories: starter
 * Keywords: cta, appel à l'action, contact, bandeau, bouton
 * Description: Bandeau pleine largeur avec un titre et un bouton.
 *
 * @package starter-theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","backgroundColor":"light-gray","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-light-gray-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading {"fontSize":"large"} -->
		<h2 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Prêt à démarrer votre projet ?', 'starter-theme' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Nous contacter', 'starter-theme' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
