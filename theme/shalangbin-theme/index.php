<?php
/**
 * Main index — blog archive fallback.
 *
 * @package ShalangBin
 */
get_header();
?>
<div class="container page-shell">
	<header class="page-head">
		<h1><?php echo is_home() ? esc_html__( 'مجله شلنگ‌بین', 'shalangbin' ) : esc_html( get_the_archive_title() ); ?></h1>
	</header>
	<?php shalangbin_breadcrumbs(); ?>

	<div class="blog-layout">
		<div class="blog-list">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'shalangbin-wide' ); ?></a>
						<?php endif; ?>
						<div class="post-body">
							<span class="eyebrow"><?php echo esc_html( shalangbin_date( 'j F Y' ) ); ?></span>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							<a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'ادامه مطلب', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
				<div class="pagination">
					<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'مطلبی یافت نشد.', 'shalangbin' ); ?></p>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
