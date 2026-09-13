<?php
/**
 * Cart Page Template - دید برتر
 * مطابق Design System
 */
if ( ! defined( 'ABSPATH' ) ) exit;

wc_print_notices();
do_action( 'woocommerce_before_cart' ); ?>

<section class="cart-page section">
    <div class="container">
        
        <div class="page-header" data-reveal="up">
            <span class="section-label">فروشگاه</span>
            <h1 class="page-title">سبد خرید شما</h1>
        </div>

        <?php if ( WC()->cart->is_empty() ) : ?>
            <div class="cart-empty" data-reveal="up">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <h2>سبد خرید شما خالی است</h2>
                <p>برای مشاهده محصولات به فروشگاه بروید</p>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-primary btn-hero">
                    بازگشت به فروشگاه
                </a>
            </div>
        <?php else : ?>

        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="cart-layout">
                
                <!-- جدول محصولات -->
                <div class="cart-items-wrapper" data-reveal="up">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="product-thumbnail">تصویر</th>
                                <th class="product-name">محصول</th>
                                <th class="product-price">قیمت واحد</th>
                                <th class="product-quantity">تعداد</th>
                                <th class="product-subtotal">جمع کل</th>
                                <th class="product-remove"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                            
                            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                                
                                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                                
                                <td class="product-thumbnail">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key );
                                    if ( ! $product_permalink ) echo $thumbnail;
                                    else printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                    ?>
                                </td>
                                
                                <td class="product-name" data-title="محصول">
                                    <?php if ( ! $product_permalink ) : ?>
                                        <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
                                    <?php else : ?>
                                        <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) ); ?>
                                    <?php endif; ?>
                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                </td>
                                
                                <td class="product-price" data-title="قیمت">
                                    <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                                </td>
                                
                                <td class="product-quantity" data-title="تعداد">
                                    <?php
                                    if ( $_product->is_sold_individually() ) {
                                        $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                    } else {
                                        $product_quantity = woocommerce_quantity_input( array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                            'min_value'    => '0',
                                            'product_name' => $_product->get_name(),
                                        ), $_product, false );
                                    }
                                    echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                    ?>
                                </td>
                                
                                <td class="product-subtotal" data-title="جمع">
                                    <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                </td>
                                
                                <td class="product-remove">
                                    <?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_html__( 'حذف این محصول', 'woocommerce' ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ), $cart_item_key ); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <?php do_action( 'woocommerce_cart_contents' ); ?>
                            
                            <tr>
                                <td colspan="6" class="actions">
                                    <?php if ( wc_coupons_enabled() ) : ?>
                                    <div class="coupon">
                                        <label for="coupon_code"><?php esc_html_e( 'کد تخفیف', 'woocommerce' ); ?></label>
                                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="<?php echo esc_attr( WC()->cart->get_coupon_discount_codes() ); ?>" placeholder="کد را وارد کنید" />
                                        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'اعمال', 'woocommerce' ); ?>"><?php esc_html_e( 'اعمال', 'woocommerce' ); ?></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'به‌روزرسانی سبد', 'woocommerce' ); ?>"><?php esc_html_e( 'به‌روزرسانی سبد خرید', 'woocommerce' ); ?></button>
                                    
                                    <?php do_action( 'woocommerce_cart_actions' ); ?>
                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                                </td>
                            </tr>
                            
                            <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                        </tbody>
                    </table>
                </div>

                <!-- خلاصه سبد خرید -->
                <div class="cart-totals-wrapper" data-reveal="left">
                    <div class="cart-totals">
                        <h2 class="totals-title">خلاصه سفارش</h2>
                        <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
                        <table class="totals-table">
                            <tr class="cart-subtotal">
                                <th><?php esc_html_e( 'جمع جزء', 'woocommerce' ); ?></th>
                                <td><?php wc_cart_totals_subtotal_html(); ?></td>
                            </tr>
                            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if ( wc_shipping_enabled() && WC()->cart->needs_shipping() ) : ?>
                            <tr class="shipping">
                                <th><?php esc_html_e( 'حمل و نقل', 'woocommerce' ); ?></th>
                                <td><?php wc_cart_totals_shipping_html(); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                            <tr class="fee">
                                <th><?php echo esc_html( $fee->name ); ?></th>
                                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                            <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $tax_rate->label ) ); ?>">
                                <th><?php echo esc_html( $tax_rate->label ); ?></th>
                                <td><?php wc_cart_totals_tax_html(); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="order-total">
                                <th><?php esc_html_e( 'مبلغ قابل پرداخت', 'woocommerce' ); ?></th>
                                <td><?php wc_cart_totals_order_total_html(); ?></td>
                            </tr>
                        </table>
                        <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
                        
                        <div class="checkout-actions">
                            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary btn-hero" style="width:100%;justify-content:center;">
                                ادامه فرآیند خرید
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            </a>
                            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:12px;">
                                بازگشت به فروشگاه
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>
        <?php endif; ?>
    </div>
</section>

<?php do_action( 'woocommerce_after_cart' ); ?>