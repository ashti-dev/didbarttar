<?php
/**
 * Checkout Form Template - دید برتر
 */
if ( ! defined( 'ABSPATH' ) ) exit;

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_checkout_enabled() ) : ?>
    <div class="woocommerce-info">پرداخت از طریق سایت غیرفعال شده است.</div>
<?php else : ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

    <section class="checkout-page section">
        <div class="container">
            
            <div class="page-header" data-reveal="up">
                <span class="section-label">تسویه حساب</span>
                <h1 class="page-title">تکمیل سفارش</h1>
            </div>

            <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
            <?php endif; ?>

            <div class="checkout-layout">
                
                <!-- ستون اطلاعات مشتری -->
                <div class="checkout-details" data-reveal="right">
                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                </div>

                <!-- ستون مرور سفارش -->
                <div class="checkout-review" data-reveal="left">
                    <div class="order-review-wrapper">
                        <h3 class="review-title">مرور سفارش</h3>
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>
                    </div>
                </div>

            </div>

            <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
            <?php endif; ?>

        </div>
    </section>

</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>