<?php
/**
 * Wishlist endpoint content.
 *
 * Rendered via woocommerce_account_wishlist_endpoint.
 * Expects: $products (WC_Product[]).
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="acp__hello">
	<h2><?php esc_html_e( 'علاقه‌مندی‌ها', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'محصولاتی که برای بعد نگه داشته‌اید.', 'torantejarat' ); ?></p>
</div>
<?php if ( $products ) : ?>
	<div class="acp-wish-grid" id="wish-grid">
		<?php foreach ( $products as $product ) : ?>
			<article class="acp-wish" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
				<div class="acp-img">
					<?php if ( $product->get_image_id() ) : ?>
						<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
					<?php else : ?>
						<span><?php esc_html_e( 'تصویر محصول', 'torantejarat' ); ?></span>
					<?php endif; ?>
				</div>
				<div class="acp-body">
					<h3>
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
					</h3>
					<div class="acp-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
					<?php if ( $product->is_in_stock() ) : ?>
						<span class="acp-stock ok"><?php esc_html_e( 'موجود', 'torantejarat' ); ?></span>
						<button type="button" class="acp-btn acp-btn-primary" data-add-cart="<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'افزودن به سبد', 'torantejarat' ); ?></button>
					<?php else : ?>
						<span class="acp-stock"><?php esc_html_e( 'ناموجود', 'torantejarat' ); ?></span>
						<button type="button" class="acp-btn acp-btn-outline" data-toast="<?php esc_attr_e( 'به‌محض موجود شدن خبرتان می‌کنیم.', 'torantejarat' ); ?>"><?php esc_html_e( 'اطلاع از موجودی', 'torantejarat' ); ?></button>
					<?php endif; ?>
				</div>
				<button type="button" class="acp-rm" data-remove-wish="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php printf( esc_attr__( 'حذف از علاقه‌مندی‌ها: %s', 'torantejarat' ), esc_attr( $product->get_name() ) ); ?>">
					<svg class="acp-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg>
				</button>
			</article>
		<?php endforeach; ?>
	</div>
	<div class="acp-empty" id="wish-empty" hidden>
		<h3><?php esc_html_e( 'فهرست علاقه‌مندی خالی است', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'با زدن آیکون قلب روی محصولات، آن‌ها را اینجا ذخیره کنید.', 'torantejarat' ); ?></p>
	</div>
<?php else : ?>
	<div class="acp-empty">
		<h3><?php esc_html_e( 'فهرست علاقه‌مندی خالی است', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'با زدن آیکون قلب روی محصولات، آن‌ها را اینجا ذخیره کنید.', 'torantejarat' ); ?></p>
		<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'torantejarat' ); ?></a>
	</div>
<?php endif; ?>
