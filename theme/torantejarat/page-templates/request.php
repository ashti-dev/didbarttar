<?php
/**
 * Request layout from contact.html, rent.html and repairs.html; no submission handler.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: تماس و خدمات — چیدمان منبع */
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php $torantejarat_has_aside = ! post_password_required() && ( has_excerpt() || has_post_thumbnail() ); ?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title() ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container torantejarat-request-layout<?php echo $torantejarat_has_aside ? '' : ' torantejarat-no-aside'; ?>">
			<?php if ( $torantejarat_has_aside ) : ?><aside class="torantejarat-request-guide"><?php if ( ! post_password_required() ) { if ( has_excerpt() ) { the_excerpt(); } if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } } ?></aside><?php endif; ?>
			<div class="torantejarat-request-form torantejarat-prose"><?php the_content(); wp_link_pages(); ?></div>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
