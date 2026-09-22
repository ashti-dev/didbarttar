<?php
/**
 * 404 template.
 *
 * @package ShalangBin
 */
get_header();
?>
<div class="container page-shell page-narrow error-404">
	<span class="eyebrow">۴۰۴</span>
	<h1><?php esc_html_e( 'صفحه‌ای که دنبالش بودید پیدا نشد', 'shalangbin' ); ?></h1>
	<p><?php esc_html_e( 'ممکن است آدرس تغییر کرده باشد یا صفحه حذف شده باشد. از جست‌وجو یا دسته‌بندی‌ها استفاده کنید.', 'shalangbin' ); ?></p>
	<div class="hero-actions">
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'shalangbin' ); ?></a>
		<a class="btn btn-outline" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'shalangbin' ); ?></a>
	</div>
	<?php get_search_form(); ?>
</div>
<?php
get_footer();
