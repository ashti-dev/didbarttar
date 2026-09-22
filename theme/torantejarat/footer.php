<?php
/**
 * Footer columns from source, backed by WordPress navigation menus.
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
			<div><?php get_template_part( 'template-parts/brand' ); ?></div>
			<?php foreach ( array( 'torantejarat-footer-shop', 'torantejarat-footer-help', 'torantejarat-footer-company' ) as $torantejarat_location ) : ?>
				<?php if ( has_nav_menu( $torantejarat_location ) ) : ?>
					<div>
						<h2><?php echo esc_html( torantejarat_menu_title( $torantejarat_location ) ); ?></h2>
						<?php wp_nav_menu( array( 'theme_location' => $torantejarat_location, 'container' => false, 'menu_class' => 'torantejarat-footer-menu', 'fallback_cb' => false ) ); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="torantejarat-footer-bottom">
			<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php echo esc_html( wp_date( 'Y' ) ); ?></span>
			<?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'حریم خصوصی', 'torantejarat' ); ?></a><?php endif; ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
