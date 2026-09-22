<?php
/**
 * Shop archive / product taxonomy.
 *
 * @package ShalangBin
 */

get_header();
?>
<div class="container page-shell">
	<?php shalangbin_breadcrumbs(); ?>

	<header class="shop-heading">
		<div>
			<span class="eyebrow"><?php esc_html_e( 'تجهیزات تخصصی بازرسی تصویری', 'shalangbin' ); ?></span>
			<h1><?php woocommerce_page_title(); ?></h1>
			<?php do_action( 'woocommerce_archive_description' ); ?>
		</div>
	</header>

	<div class="catalog-layout">
		<aside class="filters" id="shop-filters" aria-label="فیلتر محصولات">
			<div class="filter-title">
				<h2><?php shalangbin_icon( 'scan' ); ?> <?php esc_html_e( 'دقیق‌تر انتخاب کنید', 'shalangbin' ); ?></h2>
			</div>
			<?php shalangbin_shop_filters(); ?>
			<div class="filter-help">
				<strong><?php esc_html_e( 'مطمئن نیستید کدام مدل برای شماست؟', 'shalangbin' ); ?></strong>
				<p><?php esc_html_e( 'کارشناسان ما انتخاب درست را راهنمایی می‌کنند.', 'shalangbin' ); ?></p>
			</div>
		</aside>

		<div class="catalog-results">
			<div class="catalog-toolbar">
				<div>
					<h2><?php esc_html_e( 'محصولات', 'shalangbin' ); ?></h2>
					<span role="status"><?php echo esc_html( shalangbin_fa_num( $GLOBALS['wp_query']->found_posts ) ); ?> <?php esc_html_e( 'محصول', 'shalangbin' ); ?></span>
				</div>
				<?php do_action( 'woocommerce_before_shop_loop' ); ?>
			</div>

			<?php if ( woocommerce_product_loop() ) : ?>
				<?php
				woocommerce_product_loop_start();
				while ( have_posts() ) :
					the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile;
				woocommerce_product_loop_end();
				?>
			<?php else : ?>
				<p><?php esc_html_e( 'محصولی یافت نشد.', 'shalangbin' ); ?></p>
			<?php endif; ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
		</div>
	</div>
</div>
<?php
get_footer();
