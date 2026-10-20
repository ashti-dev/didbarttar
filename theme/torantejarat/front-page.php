<?php
/**
 * Home sections migrated from index.html; all business content comes from WP/Woo.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Preserve the native posts-front-page behavior instead of using the first post as hero data.
if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'home' );
	return;
}
$torantejarat_home_regions = array( 'benefits' => array(), 'body' => array(), 'faq' => array() );
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( post_password_required() ) : ?>
			<section class="torantejarat-section"><div class="torantejarat-container"><?php the_content(); ?></div></section>
		<?php else : ?>
			<?php $torantejarat_home_regions = torantejarat_home_content_regions(); ?>
			<section class="torantejarat-hero"><div class="torantejarat-container torantejarat-hero-grid<?php echo has_post_thumbnail() ? '' : ' torantejarat-hero-text-only'; ?>">
				<div class="torantejarat-hero-copy">
					<?php if ( get_bloginfo( 'description' ) ) : ?><span class="torantejarat-hero-kicker"><i></i><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span><?php endif; ?>
					<h1><?php echo esc_html( get_the_title() ); ?></h1>
					<?php if ( has_excerpt() ) { the_excerpt(); } ?>
					<?php wp_nav_menu( array( 'theme_location' => 'torantejarat-home-actions', 'container' => false, 'menu_class' => 'torantejarat-hero-actions', 'fallback_cb' => false, 'depth' => 1 ) ); ?>
				</div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="torantejarat-hero-visual"><div class="torantejarat-visual-grid"></div>
						<?php the_post_thumbnail( 'large', array( 'class' => 'torantejarat-hero-device', 'fetchpriority' => 'high', 'loading' => false ) ); ?>
						<div class="torantejarat-image-bracket torantejarat-bracket-t"></div><div class="torantejarat-image-bracket torantejarat-bracket-b"></div>
					</div>
				<?php endif; ?>
			</div></section>
			<?php torantejarat_home_content_region( $torantejarat_home_regions['benefits'] ); ?>
			<?php if ( torantejarat_woocommerce_available() ) : ?>
				<?php ob_start(); torantejarat_product_categories( true ); $torantejarat_category_html = ob_get_clean(); ?>
				<?php if ( $torantejarat_category_html ) : ?>
				<section class="torantejarat-section"><div class="torantejarat-container">
					<div class="torantejarat-section-heading"><h2><?php esc_html_e( 'دسته‌بندی محصولات', 'torantejarat' ); ?></h2></div>
					<?php echo $torantejarat_category_html; // Escaped by the category renderer. ?>
				</div></section>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/home-products' ); ?>
			<?php endif; ?>
			<?php torantejarat_home_content_region( $torantejarat_home_regions['body'] ); wp_link_pages(); ?>
			<?php get_template_part( 'template-parts/home-services' ); ?>
			<?php get_template_part( 'template-parts/home-posts' ); ?>
			<?php torantejarat_home_content_region( $torantejarat_home_regions['faq'] ); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
