<?php
/**
 * Footer — exact index.html structure: brand + 3 menu columns + bottom bar.
 * Columns use native WP menus; footer-note renders inside the company column.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<footer class="torantejarat-site-footer">
	<div class="torantejarat-container">
		<div class="torantejarat-footer-top">
			<div><?php get_template_part( 'template-parts/footer-brand' ); ?></div>
			<?php foreach ( array( 'torantejarat-footer-shop', 'torantejarat-footer-help', 'torantejarat-footer-company' ) as $torantejarat_location ) : ?>
				<?php if ( has_nav_menu( $torantejarat_location ) ) : ?>
					<div>
						<h2><?php echo esc_html( torantejarat_menu_title( $torantejarat_location ) ); ?></h2>
						<?php wp_nav_menu( array( 'theme_location' => $torantejarat_location, 'container' => false, 'menu_class' => 'torantejarat-footer-menu', 'fallback_cb' => false ) ); ?>
						<?php if ( 'torantejarat-footer-company' === $torantejarat_location ) { torantejarat_footer_preview_note(); } ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="torantejarat-footer-bottom">
			<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
			<span><?php esc_html_e( 'طراحی برای انتخاب آگاهانه', 'torantejarat' ); ?></span>
		</div>
	</div>
</footer>
<?php torantejarat_mobile_menu(); ?>
<?php wp_footer(); ?>
</body>
</html>
