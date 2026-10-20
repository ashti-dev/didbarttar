<?php
/** Service-card markup from index.html; destinations are explicit native WP menu selections.
 * @package ToranTejarat
 */
namespace ToranTejarat\Theme;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_services = torantejarat_content_menu_items( 'torantejarat-home-services' );
if ( ! $torantejarat_services ) { return; }
?>
<section class="torantejarat-section"><div class="torantejarat-container">
	<div class="torantejarat-section-heading"><div><h2><?php echo esc_html( torantejarat_menu_title( 'torantejarat-home-services' ) ); ?></h2></div></div>
	<div class="torantejarat-service-grid">
		<?php foreach ( $torantejarat_services as $torantejarat_service ) : ?>
			<a class="torantejarat-service-card" href="<?php echo esc_url( get_permalink( $torantejarat_service['post'] ) ); ?>">
				<?php torantejarat_icon( 'scan' ); ?>
				<h3><?php echo esc_html( get_the_title( $torantejarat_service['post'] ) ); ?></h3>
				<?php if ( $torantejarat_service['post']->post_excerpt ) : ?><p><?php echo esc_html( wp_strip_all_tags( $torantejarat_service['post']->post_excerpt ) ); ?></p><?php endif; ?>
				<span class="torantejarat-text-link"><?php echo esc_html( $torantejarat_service['label'] ); ?> <?php torantejarat_icon( 'arrow' ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div></section>
