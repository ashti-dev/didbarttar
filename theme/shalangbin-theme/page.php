<?php
/**
 * Single page template.
 *
 * @package ShalangBin
 */
get_header();

// WooCommerce shortcode/block pages (cart, checkout, my account).
// Rendered without the generic page frame so the store layout applies.
if ( function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) :
	?>
	<div class="container page-shell">
		<?php shalangbin_breadcrumbs(); ?>
		<div class="wc-page">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
	<?php
	get_footer();
	return;
endif;
?>
<div class="container page-shell page-narrow">
	<?php shalangbin_breadcrumbs(); ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'page-article' ); ?>>
			<header class="page-head">
				<h1><?php the_title(); ?></h1>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-thumb"><?php the_post_thumbnail( 'shalangbin-wide' ); ?></figure>
			<?php endif; ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</div>
<?php
get_footer();
