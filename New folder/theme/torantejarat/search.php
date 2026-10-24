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
	<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => sprintf( __( 'نتایج جست‌وجو: %s', 'torantejarat' ), get_search_query( false ) ), 'description' => '' ) ); ?>
	<section class="torantejarat-section"><div class="torantejarat-container">
		<?php get_search_form(); ?>
		<?php get_template_part( 'template-parts/editorial-list' ); ?>
	</div></section>
</main>
<?php get_footer(); ?>
