<?php
/**
 * Source editorial listing, backed by the main WordPress query.
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
	<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_option( 'page_for_posts' ) ? get_the_title( (int) get_option( 'page_for_posts' ) ) : __( 'مجله', 'torantejarat' ), 'description' => ( get_option( 'page_for_posts' ) && ! post_password_required( (int) get_option( 'page_for_posts' ) ) ) ? get_post_field( 'post_excerpt', (int) get_option( 'page_for_posts' ) ) : '' ) ); ?>
	<section class="torantejarat-section"><div class="torantejarat-container torantejarat-blog-layout">
		<div class="torantejarat-blog-main"><?php get_template_part( 'template-parts/editorial-list' ); ?></div>
		<?php get_template_part( 'template-parts/blog-sidebar' ); ?>
	</div></section>
</main>
<?php get_footer(); ?>
