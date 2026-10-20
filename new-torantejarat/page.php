<?php
/**
 * Native page content, including Woo Cart/Checkout/Account blocks or shortcodes.
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
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$torantejarat_ancestors = array();
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $torantejarat_ancestor_id ) {
			if ( is_post_publicly_viewable( $torantejarat_ancestor_id ) ) {
				$torantejarat_ancestors[] = array( 'label' => get_the_title( $torantejarat_ancestor_id ), 'url' => get_permalink( $torantejarat_ancestor_id ) );
			}
		}
		get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '', 'heading' => ! torantejarat_is_commerce_page(), 'trail' => $torantejarat_ancestors ) );
		?>
		<section class="torantejarat-section"><div class="torantejarat-container torantejarat-page-content<?php echo torantejarat_is_commerce_page() ? '' : ' torantejarat-prose'; ?>">
			<?php the_content(); wp_link_pages(); ?>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
