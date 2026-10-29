<?php
/**
 * Home best sellers — ranked strip fed ONLY by real total_sales.
 * Hidden until at least 2 products earn the honesty threshold.
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

$torantejarat_best = torantejarat_bestsellers( 8 );
if ( count( $torantejarat_best ) < 2 ) {
	return;
}
?>
<section class="torantejarat-section"><div class="torantejarat-container">
	<div class="torantejarat-section-heading">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'انتخاب خریداران', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'پرفروش‌ترین کالاها', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'مدل‌هایی که بیشتر خریده شده‌اند — به ترتیب فروش واقعی.', 'torantejarat' ); ?></p>
		</div>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'همه دستگاه‌ها', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<?php
	get_template_part(
		'template-parts/bestseller-rows',
		null,
		array( 'items' => $torantejarat_best )
	);
	?>
</div></section>
