<?php
/**
 * Source empty-state without a fake result or catalog fallback.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="torantejarat-empty-state">
	<?php torantejarat_icon( 'search' ); ?>
	<p><?php echo esc_html( is_search() ? __( 'نتیجه‌ای پیدا نشد؛ عبارت دیگری را جست‌وجو کنید.', 'torantejarat' ) : __( 'هنوز مطلبی برای نمایش وجود ندارد.', 'torantejarat' ) ); ?></p>
	<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'torantejarat' ); ?></a>
</div>
