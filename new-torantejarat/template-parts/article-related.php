<?php
/**
 * Related articles below the single-post layout.
 *
 * Args: post_id — current article ID.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_related_post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$torantejarat_related_posts   = torantejarat_article_related_posts( $torantejarat_related_post_id, 3 );

if ( ! $torantejarat_related_posts ) {
	return;
}

$torantejarat_related_blog_id  = (int) get_option( 'page_for_posts' );
$torantejarat_related_blog_url = $torantejarat_related_blog_id > 0 && is_post_publicly_viewable( $torantejarat_related_blog_id ) ? get_permalink( $torantejarat_related_blog_id ) : home_url( '/' );
?>
<section class="torantejarat-article-related" aria-labelledby="torantejarat-related-title">
	<div class="torantejarat-container">
		<div class="torantejarat-section-heading">
			<div>
				<span class="torantejarat-eyebrow"><?php esc_html_e( 'ادامه مطالعه', 'torantejarat' ); ?></span>
				<h2 id="torantejarat-related-title"><?php esc_html_e( 'مقالات مرتبط', 'torantejarat' ); ?></h2>
			</div>
			<a class="torantejarat-text-link" href="<?php echo esc_url( $torantejarat_related_blog_url ); ?>"><?php esc_html_e( 'همه مطالب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
		</div>
		<div class="torantejarat-editorial-grid">
			<?php
			foreach ( $torantejarat_related_posts as $torantejarat_related_post ) {
				$GLOBALS['post'] = $torantejarat_related_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Scoped to the related-card loop.
				setup_postdata( $torantejarat_related_post );
				get_template_part( 'template-parts/editorial-card', null, array( 'heading_level' => 3 ) );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
