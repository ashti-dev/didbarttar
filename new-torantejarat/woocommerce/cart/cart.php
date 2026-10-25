<?php
/**
 * Custom FrontBaz Cart Page Template
 * Harmonized with WooCommerce standard template structure and Elementor Pro
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form fb-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="fb-cart-card">
			<div class="fb-cart-header">
				<h2 class="fb-cart-title"><?php esc_html_e( 'سبد خرید شما', 'torantejarat' ); ?></h2>
				<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="fb-cart-continue-link fb-link-arrow">
					<span><?php esc_html_e( 'ادامه خرید', 'torantejarat' ); ?></span>
					<svg class="fb-arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
				</a>
			</div>

			<div class="fb-cart-table-wrapper">
				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents fb-cart-table" cellspacing="0">
					<thead>
						<tr>
							<th class="fb-th-product" colspan="2">محصول</th>
							<th class="fb-th-price">قیمت واحد</th>
							<th class="fb-th-quantity">تعداد</th>
							<th class="fb-th-subtotal">مجموع</th>
							<th class="fb-th-remove"><span class="screen-reader-text">حذف</span></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							$visible = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
								$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
								?>
								<tr class="woocommerce-cart-form__cart-item fb-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">

									<!-- Thumbnail -->
									<td class="product-thumbnail fb-td-thumbnail">
										<?php
										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );

										if ( ! $product_permalink ) {
											echo '<div class="fb-cart-thumb">' . $thumbnail . '</div>';
										} else {
											printf( '<a href="%s" class="fb-cart-thumb">%s</a>', esc_url( $product_permalink ), $thumbnail );
										}
										?>
									</td>

									<!-- Product Info -->
									<td class="product-name fb-td-name" data-title="محصول">
										<?php
										if ( ! $product_permalink ) {
											echo '<h3 class="fb-cart-item-title">' . wp_kses_post( $product_name ) . '</h3>';
										} else {
											printf( '<h3 class="fb-cart-item-title"><a href="%s">%s</a></h3>', esc_url( $product_permalink ), wp_kses_post( $product_name ) );
										}

										do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

										// Variation / Meta data
										echo wc_get_formatted_cart_item_data( $cart_item );

										// Backorder notification
										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification fb-backorder">' . esc_html__( 'موجود در انبار سفارش', 'torantejarat' ) . '</p>', $product_id ) );
										}
										?>
									</td>

									<!-- Unit Price -->
									<td class="product-price fb-td-price" data-title="قیمت واحد">
										<?php
										$is_on_sale = false;
										$reg_price_display = 0;
										$sale_price_display = 0;

										if ( $_product && $_product->is_on_sale() ) {
											$raw_reg  = (float) $_product->get_regular_price();
											$raw_sale = (float) $_product->get_price();
											if ( $raw_reg > $raw_sale ) {
												$is_on_sale          = true;
												$reg_price_display   = wc_get_price_to_display( $_product, array( 'price' => $raw_reg ) );
												$sale_price_display  = wc_get_price_to_display( $_product );
											}
										}

										if ( $is_on_sale ) {
											echo '<div class="fb-cart-item-price-wrap">';
											echo '<del class="fb-price-old">' . esc_html( number_format_i18n( $reg_price_display ) ) . '</del>';
											echo '<ins class="fb-price-current">' . wc_price( $sale_price_display ) . '</ins>';
											echo '</div>';
										} else {
											echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
										}
										?>
									</td>

									<!-- Quantity Input / Buttons -->
									<td class="product-quantity fb-td-quantity" data-title="تعداد">
										<?php
										$max_qty = $_product->get_max_purchase_quantity();
										$is_max  = ( $max_qty > 0 && (int) $cart_item['quantity'] >= $max_qty );
										?>
										<div class="fb-qty-control" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" data-max-qty="<?php echo esc_attr( $max_qty ); ?>">
											<button type="button" class="fb-qty-btn fb-qty-plus <?php echo $is_max ? 'is-disabled' : ''; ?>" <?php echo $is_max ? 'disabled="disabled"' : ''; ?> aria-label="افزایش تعداد" title="<?php echo $is_max ? esc_attr( sprintf( __( 'حداکثر موجودی در انبار (%s عدد)', 'torantejarat' ), number_format_i18n( $max_qty ) ) ) : esc_attr__( 'افزایش تعداد', 'torantejarat' ); ?>">
												<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
											</button>
											<input
												type="number"
												class="input-text qty text fb-qty-input"
												name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
												value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
												min="1"
												max="<?php echo esc_attr( $_product->get_max_purchase_quantity() > 0 ? $_product->get_max_purchase_quantity() : '' ); ?>"
												step="1"
												inputmode="numeric"
												readonly
											/>
											<?php $is_cart_qty_one = ( 1 === (int) $cart_item['quantity'] ); ?>
											<button type="button" class="fb-qty-btn fb-qty-minus <?php echo $is_cart_qty_one ? 'is-trash' : ''; ?>" aria-label="<?php echo $is_cart_qty_one ? 'حذف محصول' : 'کاهش تعداد'; ?>">
												<?php if ( $is_cart_qty_one ) : ?>
													<svg class="fb-trash-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
														<polyline points="3 6 5 6 21 6"></polyline>
														<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
														<line x1="10" y1="11" x2="10" y2="17"></line>
														<line x1="14" y1="11" x2="14" y2="17"></line>
													</svg>
												<?php else : ?>
													<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
												<?php endif; ?>
											</button>
										</div>
									</td>

									<!-- Line Subtotal -->
									<td class="product-subtotal fb-td-subtotal" data-title="مجموع">
										<span class="fb-subtotal-amount">
											<?php
											if ( $is_on_sale ) {
												$reg_subtotal_display  = $reg_price_display * $cart_item['quantity'];
												$sale_subtotal_display = $sale_price_display * $cart_item['quantity'];

												echo '<div class="fb-cart-item-price-wrap fb-cart-subtotal-wrap">';
												echo '<del class="fb-price-old">' . esc_html( number_format_i18n( $reg_subtotal_display ) ) . '</del>';
												echo '<ins class="fb-price-current">' . wc_price( $sale_subtotal_display ) . '</ins>';
												echo '</div>';
											} else {
												echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
											}
											?>
										</span>
									</td>

									<!-- Remove Button -->
									<td class="fb-product-remove fb-td-remove">
										<?php
										echo apply_filters(
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a role="button" href="%s" class="remove fb-cart-remove-btn" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg class="fb-trash-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												/* translators: %s is the product name */
												esc_attr( sprintf( __( 'حذف %s از سبد خرید', 'torantejarat' ), wp_strip_all_tags( $product_name ) ) ),
												esc_attr( $product_id ),
												esc_attr( $cart_item_key ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>

						<tr class="fb-cart-actions-row">
							<td colspan="6" class="actions">
								<button type="submit" class="button fb-hidden-submit" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
									<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
								</button>

								<?php do_action( 'woocommerce_cart_actions' ); ?>
								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
							</td>
						</tr>

						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

	<div class="cart-collaterals fb-cart-collaterals">
		<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
		?>
	</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
