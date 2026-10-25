<?php
/**
 * Custom FrontBaz Empty Cart Template
 * Clean, Friendly RTL Empty State
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="fb-cart-wrapper fb-cart-empty-wrapper woocommerce">

	<?php wc_print_notices(); ?>

	<div class="fb-cart-empty-card">
		<div class="fb-cart-empty-icon-wrap">
			<svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="fb-empty-icon">
				<circle cx="9" cy="21" r="1"></circle>
				<circle cx="20" cy="21" r="1"></circle>
				<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
			</svg>
		</div>

		<h2 class="fb-cart-empty-title"><?php esc_html_e( 'سبد خرید شما در حال حاضر خالی است!', 'hello-elementor-child' ); ?></h2>
		<p class="fb-cart-empty-desc"><?php printf( esc_html__( 'می‌توانید برای مشاهده محصولات جدید، شگفت‌انگیز و پرفروش به فروشگاه %s سر بزنید.', 'hello-elementor-child' ), esc_html( get_bloginfo( 'name' ) ) ); ?></p>

		<?php do_action( 'woocommerce_cart_is_empty' ); ?>

		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<div class="fb-cart-empty-action">
				<a class="fb-btn-primary fb-empty-shop-btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
					<span class="fb-btn-text"><?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'مشاهده محصولات و شروع خرید', 'hello-elementor-child' ) ) ); ?></span>
				</a>
			</div>
		<?php endif; ?>
	</div>

</div>
