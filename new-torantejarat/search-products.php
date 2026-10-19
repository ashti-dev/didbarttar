<?php
/**
 * Product search results (/search/?q=) — Digikala-style search page.
 * Same catalog engine as the shop (grid, filters, sortbar, pagination)
 * with search-only URL and breadcrumb. No shop slug, title or trail here.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$torantejarat_q     = torantejarat_product_search_text();
$torantejarat_query = new \WP_Query( torantejarat_product_search_query_args() );

global $wp_query, $post, $product;
$torantejarat_old_query   = $wp_query;
$torantejarat_old_post    = $post;
$torantejarat_old_product = $product;
/* The custom query replaces the global so loop tags, counts and pagination
   work untouched. It must carry the search flag with it: with a bare
   product query, is_shop() turns true and every catalog gate would resolve
   to the shop URL instead of /search/. */
$torantejarat_query->set( 'torantejarat_product_search', 1 );
$wp_query                 = $torantejarat_query;
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => sprintf( __( 'نتایج جست‌وجو برای %s', 'torantejarat' ), '«' . $torantejarat_q . '»' ), 'description' => '' ) ); ?>
	<section class="torantejarat-shop-body"><div class="torantejarat-container">
		<?php if ( $torantejarat_query->have_posts() ) : ?>
			<?php torantejarat_sortbar(); ?>
			<?php torantejarat_filters_layout_start(); ?>
			<?php torantejarat_filters_results_start(); ?>
			<?php torantejarat_filters_toolbar_extras(); ?>
			<?php
			wc_setup_loop( array( 'name' => 'torantejarat-search', 'columns' => 3, 'total' => (int) $torantejarat_query->found_posts, 'is_paginated' => true ) );
			woocommerce_product_loop_start();
			while ( $torantejarat_query->have_posts() ) {
				$torantejarat_query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();
			the_posts_pagination();
			?>
			<?php torantejarat_filters_layout_end(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content-none' ); ?>
		<?php endif; ?>
	</div></section>
</main>
<?php
$wp_query = $torantejarat_old_query;
$post     = $torantejarat_old_post;
$product  = $torantejarat_old_product;
wp_reset_postdata();
if ( function_exists( 'wc_reset_loop' ) ) {
	wc_reset_loop();
}
get_footer();
