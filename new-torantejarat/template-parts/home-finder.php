<?php
/**
 * Homepage finder — one filter card, results in a separate section below it.
 * Three real-data filters, live AJAX results. Quiz page stays untouched.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! torantejarat_woocommerce_available() ) {
	return;
}

$torantejarat_cats    = torantejarat_finder_cats();
$torantejarat_lengths = torantejarat_finder_lengths();
$torantejarat_bands   = torantejarat_finder_bands();
if ( ! $torantejarat_cats && ! $torantejarat_lengths && ! $torantejarat_bands ) {
	return;
}
$torantejarat_nonce    = wp_create_nonce( 'torantejarat-finder' );
$torantejarat_shop_url = wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<section class="torantejarat-section torantejarat-finder-section"><div class="torantejarat-container">
	<div class="torantejarat-finder-block" data-finder
		data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
		data-nonce="<?php echo esc_attr( $torantejarat_nonce ); ?>"
		data-action="torantejarat_finder">

		<div class="torantejarat-finder">
			<div class="torantejarat-finder__head">
				<div>
					<p class="torantejarat-finder__kicker"><?php esc_html_e( 'فیلتر هوشمند محصولات', 'torantejarat' ); ?></p>
					<h2 class="torantejarat-finder__title"><?php esc_html_e( 'دوربین مناسب کارتان را پیدا کنید', 'torantejarat' ); ?></h2>
					<p class="torantejarat-finder__sub"><?php esc_html_e( 'گزینه‌های مدنظر خود را انتخاب کنید تا دستگاه‌های سازگار نمایش داده شوند.', 'torantejarat' ); ?></p>
				</div>
				<button class="torantejarat-finder__reset" type="button" data-finder-reset hidden><?php esc_html_e( 'پاک‌کردن فیلترها ↺', 'torantejarat' ); ?></button>
			</div>

			<div class="torantejarat-finder__filters">
				<?php if ( $torantejarat_cats ) : ?>
					<div class="torantejarat-finder__group">
						<label class="torantejarat-finder__label" for="torantejarat-finder-type"><?php esc_html_e( 'نوع کاربرد و نمایشگر', 'torantejarat' ); ?></label>
						<div class="torantejarat-finder__select">
							<select id="torantejarat-finder-type" name="fcat">
								<option value=""><?php esc_html_e( 'همه موارد (پیش‌فرض)', 'torantejarat' ); ?></option>
								<?php foreach ( $torantejarat_cats as $torantejarat_cat ) : ?>
									<option value="<?php echo esc_attr( $torantejarat_cat['slug'] ); ?>"><?php echo esc_html( $torantejarat_cat['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $torantejarat_lengths ) : ?>
					<div class="torantejarat-finder__group">
						<label class="torantejarat-finder__label" for="torantejarat-finder-length"><?php esc_html_e( 'طول کابل', 'torantejarat' ); ?></label>
						<div class="torantejarat-finder__select">
							<select id="torantejarat-finder-length" name="flen">
								<option value=""><?php esc_html_e( 'همه طول‌ها (پیش‌فرض)', 'torantejarat' ); ?></option>
								<?php foreach ( $torantejarat_lengths as $torantejarat_len ) : ?>
									<option value="<?php echo esc_attr( $torantejarat_len['id'] ); ?>"><?php echo esc_html( $torantejarat_len['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $torantejarat_bands ) : ?>
					<div class="torantejarat-finder__group">
						<label class="torantejarat-finder__label" for="torantejarat-finder-budget"><?php esc_html_e( 'محدوده بودجه', 'torantejarat' ); ?></label>
						<div class="torantejarat-finder__select">
							<select id="torantejarat-finder-budget" name="fband">
								<option value=""><?php esc_html_e( 'همه قیمت‌ها (پیش‌فرض)', 'torantejarat' ); ?></option>
								<?php foreach ( $torantejarat_bands as $torantejarat_i => $torantejarat_band ) : ?>
									<option value="<?php echo esc_attr( $torantejarat_i + 1 ); ?>"><?php echo esc_html( $torantejarat_band['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="torantejarat-finder__footer">
				<p class="torantejarat-finder__hint"><?php esc_html_e( 'برای مشاهده همه‌ی محصولات، فیلترها را دست‌نخورده بگذارید.', 'torantejarat' ); ?></p>
				<div class="torantejarat-finder__actions">
					<span class="torantejarat-finder__count" data-finder-count><?php esc_html_e( 'بدون فیلتر', 'torantejarat' ); ?></span>
					<button class="torantejarat-finder__apply" type="button" data-finder-apply><?php esc_html_e( 'مشاهده نتایج ←', 'torantejarat' ); ?></button>
				</div>
			</div>
		</div>

		<section class="torantejarat-finder-results-wrap" data-finder-results-wrap hidden aria-labelledby="torantejarat-finder-results-title">
			<div class="torantejarat-finder-results-head">
				<h2 id="torantejarat-finder-results-title"><?php esc_html_e( 'دستگاه‌های پیشنهادی', 'torantejarat' ); ?></h2>
				<span class="torantejarat-finder-result-count" data-finder-result-count aria-live="polite"></span>
			</div>
			<div class="torantejarat-finder__results" data-finder-results></div>
			<p class="torantejarat-finder__more">
				<a class="torantejarat-btn torantejarat-btn-outline" data-finder-shop-link href="<?php echo esc_url( $torantejarat_shop_url ); ?>"><?php esc_html_e( 'مشاهده همه در فروشگاه', 'torantejarat' ); ?></a>
			</p>
		</section>

	</div>
</div></section>
