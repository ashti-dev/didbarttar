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
$torantejarat_contact_page = get_page_by_path( 'contact' );
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
						<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="acp-btn acp-btn-primary add_to_cart_button product_type_<?php echo esc_attr( sanitize_html_class( $product->get_type() ) ); ?>" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-quantity="1" rel="nofollow"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
					<?php else : ?>
						<span class="acp-stock"><?php esc_html_e( 'ناموجود', 'torantejarat' ); ?></span>
						<?php if ( $torantejarat_contact_page ) : ?>
							<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( add_query_arg( 'device', $product->get_id(), get_permalink( $torantejarat_contact_page ) ) ); ?>"><?php esc_html_e( 'استعلام موجودی', 'torantejarat' ); ?></a>
						<?php else : ?>
							<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'مشاهده محصول', 'torantejarat' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-wishlist-remove-form>
				<input type="hidden" name="action" value="torantejarat_wishlist_toggle">
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
				<input type="hidden" name="torantejarat_wishlist_nonce" value="<?php echo esc_attr( wp_create_nonce( 'torantejarat_wishlist_toggle_' . $product->get_id() ) ); ?>">
				<button type="submit" class="acp-rm" data-remove-wish="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php printf( esc_attr__( 'حذف از علاقه‌مندی‌ها: %s', 'torantejarat' ), esc_attr( $product->get_name() ) ); ?>">
					<svg class="acp-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg>
				</button>
			</form>
			</article>
		<?php endforeach; ?>
	</div>
	<div class="acp-empty" id="wish-empty" hidden>
		<h3><?php esc_html_e( 'فهرست علاقه‌مندی خالی است', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'با دکمهٔ «افزودن به علاقه‌مندی‌ها» در صفحهٔ محصول، آن‌ها را اینجا ذخیره کنید.', 'torantejarat' ); ?></p>
	</div>
<?php else : ?>
	<div class="acp-empty">
		<h3><?php esc_html_e( 'فهرست علاقه‌مندی خالی است', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'با دکمهٔ «افزودن به علاقه‌مندی‌ها» در صفحهٔ محصول، آن‌ها را اینجا ذخیره کنید.', 'torantejarat' ); ?></p>
		<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'torantejarat' ); ?></a>
	</div>
<?php endif; ?>
