<?php
/**
 * Dedicated search results — intentionally distinct from the shop archive.
 * Shop/filters/sortbar never render here. Products get their own grid,
 * posts and pages fall back to the editorial list.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$torantejarat_q = get_search_query( false );
global $wp_query;
$torantejarat_posts = $wp_query instanceof \WP_Query ? $wp_query->posts : array();
$torantejarat_product_posts = array();
$torantejarat_other_posts   = array();
foreach ( (array) $torantejarat_posts as $torantejarat_post ) {
	if ( $torantejarat_post instanceof \WP_Post && 'product' === $torantejarat_post->post_type ) {
		$torantejarat_product_posts[] = $torantejarat_post;
	} else {
		$torantejarat_other_posts[] = $torantejarat_post;
	}
}
$torantejarat_product_count = count( $torantejarat_product_posts );
$torantejarat_other_count   = count( $torantejarat_other_posts );
/* Partition above is per loaded page; the headline reports the true total
   across all pages so pagination never shows a misleading count. */
$torantejarat_total = $wp_query instanceof \WP_Query ? max( 0, (int) $wp_query->found_posts ) : ( $torantejarat_product_count + $torantejarat_other_count );
?>
<main id="torantejarat-main" class="torantejarat-main torantejarat-search-main" tabindex="-1">
	<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => sprintf( __( 'نتایج جست‌وجو: %s', 'torantejarat' ), $torantejarat_q ), 'description' => '' ) ); ?>
	<section class="torantejarat-section torantejarat-search-body"><div class="torantejarat-container">
		<?php get_search_form(); ?>
		<?php if ( have_posts() ) : ?>
			<p class="torantejarat-search-count" role="status">
				<?php
				printf(
					/* translators: %s: total result count. */
					esc_html__( '%s نتیجه پیدا شد.', 'torantejarat' ),
					esc_html( torantejarat_fa_number( $torantejarat_total ) )
				);
				?>
			</p>
			<?php if ( $torantejarat_product_posts ) : ?>
				<section class="torantejarat-search-products" aria-label="<?php esc_attr_e( 'محصولات', 'torantejarat' ); ?>">
					<div class="torantejarat-section-heading"><div>
						<span class="torantejarat-eyebrow"><?php esc_html_e( 'دستگاه‌ها', 'torantejarat' ); ?></span>
						<h2><?php esc_html_e( 'محصولات مرتبط', 'torantejarat' ); ?></h2>
					</div></div>
					<?php
					global $post, $product;
					$torantejarat_saved_post    = $post;
					$torantejarat_saved_product = $product;
					if ( function_exists( 'wc_setup_loop' ) ) {
						wc_setup_loop( array( 'name' => 'torantejarat-search', 'columns' => 3, 'total' => $torantejarat_product_count, 'is_paginated' => false ) );
					}
					echo '<ul class="products torantejarat-product-grid torantejarat-search-grid">';
					foreach ( $torantejarat_product_posts as $post ) {
						setup_postdata( $post );
						if ( torantejarat_woocommerce_available() ) {
							$product = wc_get_product( $post->ID );
							if ( ! $product instanceof \WC_Product || ! $product->is_visible() ) {
								continue;
							}
							wc_get_template_part( 'content', 'product' );
						}
					}
					echo '</ul>';
					if ( function_exists( 'wc_reset_loop' ) ) {
						wc_reset_loop();
					}
					wp_reset_postdata();
					$post    = $torantejarat_saved_post;
					$product = $torantejarat_saved_product;
					?>
				</section>
			<?php endif; ?>
			<?php if ( $torantejarat_other_posts ) : ?>
				<section class="torantejarat-search-articles" aria-label="<?php esc_attr_e( 'مطالب', 'torantejarat' ); ?>">
					<div class="torantejarat-section-heading"><div>
						<span class="torantejarat-eyebrow"><?php esc_html_e( 'مجله', 'torantejarat' ); ?></span>
						<h2><?php esc_html_e( 'مطالب مرتبط', 'torantejarat' ); ?></h2>
					</div></div>
					<div class="torantejarat-editorial-grid">
						<?php
						global $post;
						$torantejarat_saved_post = $post;
						foreach ( $torantejarat_other_posts as $post ) {
							setup_postdata( $post );
							get_template_part( 'template-parts/editorial-card' );
						}
						wp_reset_postdata();
						$post = $torantejarat_saved_post;
						?>
					</div>
				</section>
			<?php endif; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content-none' ); ?>
		<?php endif; ?>
	</div></section>
</main>
<?php get_footer(); ?>
