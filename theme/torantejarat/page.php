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
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '' ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container torantejarat-page-content<?php echo torantejarat_is_commerce_page() ? '' : ' torantejarat-prose'; ?>">
			<?php the_content(); wp_link_pages(); ?>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
