<?php
/**
 * Source editorial grid backed by the latest published WP posts.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_posts = new \WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
if ( ! $torantejarat_posts->have_posts() ) { return; }
?>
<section class="torantejarat-section torantejarat-soft-section"><div class="torantejarat-container">
	<div class="torantejarat-section-heading"><h2><?php esc_html_e( 'تازه‌های مجله', 'torantejarat' ); ?></h2>
		<?php $torantejarat_blog_id = (int) get_option( 'page_for_posts' ); ?>
		<?php if ( $torantejarat_blog_id && is_post_publicly_viewable( $torantejarat_blog_id ) ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink( $torantejarat_blog_id ) ); ?>"><?php echo esc_html( get_the_title( $torantejarat_blog_id ) ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<div class="torantejarat-editorial-grid"><?php while ( $torantejarat_posts->have_posts() ) { $torantejarat_posts->the_post(); get_template_part( 'template-parts/editorial-card', null, array( 'heading_level' => 3 ) ); } ?></div>
</div></section>
<?php wp_reset_postdata(); ?>
