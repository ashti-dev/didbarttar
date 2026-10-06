<?php
/**
 * Blog sidebar: owner widgets when present, curated defaults otherwise.
 * Defaults: search, post categories with counts, latest posts.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_active_sidebar( 'torantejarat-blog-sidebar' ) ) {
	echo '<aside class="torantejarat-blog-sidebar">';
	dynamic_sidebar( 'torantejarat-blog-sidebar' );
	echo '</aside>';
	return;
}

$torantejarat_cats = get_categories( array( 'hide_empty' => true, 'number' => 10 ) );
$torantejarat_latest = new \WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<aside class="torantejarat-blog-sidebar">
	<div class="torantejarat-widget">
		<h2><?php esc_html_e( 'جست‌وجو در مجله', 'torantejarat' ); ?></h2>
		<?php get_search_form(); ?>
	</div>
	<?php if ( $torantejarat_cats && ! is_wp_error( $torantejarat_cats ) ) : ?>
		<div class="torantejarat-widget">
			<h2><?php esc_html_e( 'دسته‌ها', 'torantejarat' ); ?></h2>
			<ul class="torantejarat-widget-cats">
				<?php foreach ( $torantejarat_cats as $torantejarat_cat ) : ?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $torantejarat_cat ) ); ?>"><?php echo esc_html( $torantejarat_cat->name ); ?></a>
						<span><?php echo esc_html( number_format_i18n( $torantejarat_cat->count ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if ( $torantejarat_latest->have_posts() ) : ?>
		<div class="torantejarat-widget">
			<h2><?php esc_html_e( 'آخرین مطالب', 'torantejarat' ); ?></h2>
			<ul class="torantejarat-widget-latest">
				<?php
				while ( $torantejarat_latest->have_posts() ) :
					$torantejarat_latest->the_post();
					?>
					<li>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
						<?php endif; ?>
						<div>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	<?php endif; ?>
</aside>
