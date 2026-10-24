<?php
/**
 * Home length strip — real cable lengths only (pa_cable-length terms
 * with products). Auto-grows when new lengths gain products.
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

$torantejarat_lengths = get_terms(
	array(
		'taxonomy'   => 'pa_cable-length',
		'hide_empty' => true,
		'orderby'    => 'slug',
		'order'      => 'ASC',
	)
);
if ( is_wp_error( $torantejarat_lengths ) || ! $torantejarat_lengths ) {
	return;
}
$torantejarat_shop = wc_get_page_permalink( 'shop' );
if ( ! $torantejarat_shop ) {
	return;
}
?>
<section class="torantejarat-section torantejarat-length-section"><div class="torantejarat-container">
	<div class="torantejarat-section-heading">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'بر اساس متراژ', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'مسیر شما چقدر طول دارد؟', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'متراژ را انتخاب کنید تا دستگاه‌های همان طول را ببینید.', 'torantejarat' ); ?></p>
		</div>
	</div>
	<div class="torantejarat-length-chips">
		<?php foreach ( $torantejarat_lengths as $torantejarat_term ) : ?>
			<a class="torantejarat-length-chip" href="<?php echo esc_url( add_query_arg( 'att_cable-length', $torantejarat_term->slug, $torantejarat_shop ) ); ?>">
				<strong><?php echo esc_html( $torantejarat_term->name ); ?></strong>
				<span><?php echo esc_html( sprintf( __( '%s دستگاه', 'torantejarat' ), torantejarat_fa_number( $torantejarat_term->count ) ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div></section>
