<?php
/**
 * Search results template.
 *
 * @package ShalangBin
 */
get_header();
?>
<div class="container page-shell">
	<header class="page-head">
		<h1><?php printf( esc_html__( 'نتایج جست‌وجو برای: %s', 'shalangbin' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
	</header>
	<?php shalangbin_breadcrumbs(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="blog-list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="post-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
					<?php endif; ?>
					<div class="post-body">
						<span class="eyebrow"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</div>
				</article>
				<?php
			endwhile;
			?>
			<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?></div>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید.', 'shalangbin' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
