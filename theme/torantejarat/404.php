<?php
/**
 * 404 composed from the source page-intro and empty-state; no new visual system.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => __( 'صفحه پیدا نشد', 'torantejarat' ) ) ); ?>
	<section class="torantejarat-section"><div class="torantejarat-container torantejarat-empty-state">
		<?php torantejarat_icon( 'search' ); ?>
		<p><?php esc_html_e( 'صفحه درخواستی در دسترس نیست.', 'torantejarat' ); ?></p>
		<?php get_search_form(); ?>
		<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'torantejarat' ); ?></a>
	</div></section>
</main>
<?php get_footer(); ?>
