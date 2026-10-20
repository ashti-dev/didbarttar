<?php
/** Academy source layout; curated links to existing WP posts/pages, not a new content model.
 * @package ToranTejarat
 */
namespace ToranTejarat\Theme;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: آکادمی — منابع موجود */
global $post;
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '' ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container">
			<?php if ( post_password_required() ) : ?>
				<?php the_content(); ?>
			<?php else : ?>
				<?php
				$torantejarat_resources = torantejarat_content_menu_items( 'torantejarat-academy' );
				$torantejarat_academy_page = $post;
				?>
				<?php if ( $torantejarat_resources ) : ?>
					<div class="torantejarat-section-heading"><div><h2><?php echo esc_html( torantejarat_menu_title( 'torantejarat-academy' ) ); ?></h2></div></div>
					<div class="torantejarat-editorial-grid">
						<?php foreach ( $torantejarat_resources as $torantejarat_resource ) {
							$post = $torantejarat_resource['post'];
							setup_postdata( $post );
							get_template_part( 'template-parts/editorial-card', null, array( 'heading_level' => 3 ) );
						} ?>
					</div>
					<?php wp_reset_postdata(); $post = $torantejarat_academy_page; ?>
				<?php endif; ?>
				<div class="torantejarat-prose torantejarat-academy-content"><?php the_content(); wp_link_pages(); ?></div>
			<?php endif; ?>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
