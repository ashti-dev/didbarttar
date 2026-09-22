<?php
/**
 * Source presentation attached to native Woo loops, galleries and forms.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_woocommerce_available() {
	return class_exists( 'WooCommerce', false );
}

function torantejarat_woocommerce_setup() {
	if ( ! torantejarat_woocommerce_available() ) {
		return;
	}
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	add_action( 'woocommerce_before_main_content', __NAMESPACE__ . '\\torantejarat_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', __NAMESPACE__ . '\\torantejarat_woocommerce_wrapper_end', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	add_action( 'woocommerce_shop_loop_item_title', __NAMESPACE__ . '\\torantejarat_loop_product_title', 10 );
	add_filter( 'woocommerce_product_loop_start', __NAMESPACE__ . '\\torantejarat_product_loop_start' );
	add_filter( 'woocommerce_breadcrumb_defaults', __NAMESPACE__ . '\\torantejarat_woo_breadcrumb' );
	add_filter( 'woocommerce_add_to_cart_fragments', __NAMESPACE__ . '\\torantejarat_cart_fragments' );
	add_action( 'woocommerce_shop_loop_header', __NAMESPACE__ . '\\torantejarat_archive_intro_end', 99 );
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_catalog_toolbar_start', 15 );
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_catalog_toolbar_end', 35 );
	add_filter( 'woocommerce_output_related_products_args', __NAMESPACE__ . '\\torantejarat_related_layout' );
	add_action( 'woocommerce_archive_description', __NAMESPACE__ . '\\torantejarat_archive_categories', 30 );

	// Presentation of native cart.php only. Does not assign a Cart/Checkout mode.
	add_action( 'woocommerce_before_cart', __NAMESPACE__ . '\\torantejarat_cart_layout_start', 99 );
	add_action( 'woocommerce_after_cart', __NAMESPACE__ . '\\torantejarat_cart_layout_end', 1 );
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
	add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20 );

	add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_grid_start', 5 );
	add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_gallery_end', 30 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_summary_start', 1 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 2 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_specs', 21 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_purchase_start', 25 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 26 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_purchase_end', 35 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_summary_end', 99 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_details_start', 5 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_details_end', 18 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_related_section', 20 );
}

function torantejarat_woocommerce_wrapper_start() {
	$archive = ! is_product();
	echo '<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">';
	if ( $archive ) {
		echo '<section class="torantejarat-shop-intro"><div class="torantejarat-container">';
	}
}

function torantejarat_woocommerce_wrapper_end() {
	if ( ! is_product() ) {
		echo '</div></section>';
	}
	echo '</main>';
}

function torantejarat_woo_breadcrumb( $defaults ) {
	$defaults['wrap_before'] = '<nav class="torantejarat-woo-breadcrumb' . ( is_product() ? ' torantejarat-container' : '' ) . '" aria-label="' . esc_attr__( 'مسیر صفحه', 'torantejarat' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
}

function torantejarat_product_loop_start( $html ) {
	$class = 'torantejarat-product-grid';
	if ( is_shop() || is_product_taxonomy() ) {
		$class .= ' torantejarat-catalog-grid';
	} elseif ( is_product() ) {
		$class .= ' torantejarat-related-grid';
	}
	return str_replace( 'class="products', 'class="products ' . $class, $html );
}

function torantejarat_cart_link() {
	if ( ! torantejarat_woocommerce_available() || wc_get_page_id( 'cart' ) <= 0 ) {
		return;
	}
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : null;
	$label = null === $count ? __( 'سبد خرید', 'torantejarat' ) : sprintf( __( '%s کالا در سبد خرید', 'torantejarat' ), number_format_i18n( $count ) );
	?>
	<a class="torantejarat-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
		<?php torantejarat_icon( 'cart' ); ?><span class="torantejarat-cart-label"><?php esc_html_e( 'سبد خرید', 'torantejarat' ); ?></span>
		<?php if ( WC()->cart ) : ?><b><?php echo esc_html( number_format_i18n( WC()->cart->get_cart_contents_count() ) ); ?></b><?php endif; ?>
	</a>
	<?php
}

function torantejarat_cart_fragments( $fragments ) {
	if ( wc_get_page_id( 'cart' ) > 0 ) {
		ob_start();
		torantejarat_cart_link();
		$fragments['a.torantejarat-cart-link'] = ob_get_clean();
	}
	return $fragments;
}

function torantejarat_product_categories( $home = false ) {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => $home ? 4 : 0 ) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}
	echo '<div class="' . ( $home ? 'torantejarat-category-grid' : 'torantejarat-shop-categories' ) . '">';
	foreach ( $terms as $index => $term ) {
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) { continue; }
		echo '<a class="' . ( $home ? 'torantejarat-category-card' : 'torantejarat-category-link' ) . '" href="' . esc_url( $url ) . '"' . ( is_tax( 'product_cat', $term->term_id ) ? ' aria-current="page"' : '' ) . '>';
		if ( $home ) {
			echo '<span class="torantejarat-category-number" aria-hidden="true">' . esc_html( sprintf( '%02d', $index + 1 ) ) . '</span><span class="torantejarat-category-icon">'; torantejarat_icon( 'scan' ); echo '</span><h3>' . esc_html( $term->name ) . '</h3><p>' . esc_html( wp_trim_words( wp_strip_all_tags( $term->description ), 18 ) ) . '</p><span class="torantejarat-category-arrow">'; torantejarat_icon( 'arrow' ); echo '</span>';
		} else {
			echo '<span>' . esc_html( $term->name ) . '</span><b>' . esc_html( number_format_i18n( $term->count ) ) . '</b>';
		}
		echo '</a>';
	}
	echo '</div>';
}

function torantejarat_archive_categories() {
	torantejarat_product_categories();
}

function torantejarat_product_grid_start() {
	echo '<section class="torantejarat-pdp-top"><div class="torantejarat-container torantejarat-pdp-grid"><div class="torantejarat-pdp-gallery">';
}
function torantejarat_product_gallery_end() { echo '</div>'; }
function torantejarat_product_summary_start() { echo '<div class="torantejarat-pdp-summary">'; }
function torantejarat_product_summary_end() { echo '</div>'; }
function torantejarat_product_purchase_start() { echo '<div class="torantejarat-pdp-purchase">'; }
function torantejarat_product_purchase_end() { echo '</div>'; }
function torantejarat_product_details_start() { echo '</div></section><div class="torantejarat-container torantejarat-product-details">'; }
function torantejarat_product_details_end() { echo '</div>'; }

function torantejarat_woocommerce_notice() {
	if ( torantejarat_woocommerce_available() || ! current_user_can( 'activate_plugins' ) ) { return; }
	?>
	<div class="notice notice-warning torantejarat-notice"><p><?php esc_html_e( 'بخش فروشگاه به ووکامرس نیاز دارد. محتوای سایت همچنان در دسترس است.', 'torantejarat' ); ?></p></div>
	<?php
}

// The native archive header runs even for empty result sets (Woo archive-product.php).
function torantejarat_archive_intro_end() {
	echo '</div></section><section class="torantejarat-shop-body"><div class="torantejarat-container">';
}
function torantejarat_catalog_toolbar_start() { echo '<div class="torantejarat-catalog-toolbar">'; }
function torantejarat_catalog_toolbar_end() { echo '</div>'; }

function torantejarat_related_layout( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}

/** Visible, merchant-authored attributes only; no guessed slugs, units or shipping dimensions. */
function torantejarat_product_attribute_items( $product, $limit ) {
	$items = array();
	if ( ! $product instanceof \WC_Product || post_password_required( $product->get_id() ) ) {
		return $items;
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute instanceof \WC_Product_Attribute || ! $attribute->get_visible() ) { continue; }
		$value = $product->get_attribute( $attribute->get_name() );
		if ( '' === trim( $value ) ) { continue; }
		$items[] = array( 'label' => wc_attribute_label( $attribute->get_name(), $product ), 'value' => $value );
		if ( count( $items ) >= $limit ) { break; }
	}
	return $items;
}

function torantejarat_product_specs() {
	global $product;
	$items = torantejarat_product_attribute_items( $product, 4 );
	if ( ! $items ) { return; }
	echo '<div class="torantejarat-pdp-specs">';
	foreach ( $items as $item ) {
		echo '<div><div><span>' . esc_html( $item['label'] ) . '</span><strong>' . esc_html( $item['value'] ) . '</strong></div></div>';
	}
	echo '</div>';
}

function torantejarat_is_commerce_page() {
	return torantejarat_woocommerce_available() && ( is_cart() || is_checkout() || is_account_page() );
}

/** Keep Woo's related-product selection; migrate only the source's full-width section. */
function torantejarat_related_section() {
	ob_start();
	woocommerce_output_related_products();
	$html = ob_get_clean();
	if ( '' === trim( $html ) ) { return; }
	echo '<section class="torantejarat-section torantejarat-soft-section"><div class="torantejarat-container">';
	echo $html; // Native Woo template output with the public loop hooks intact.
	echo '</div></section>';
}

/** Source hierarchy: section h2 → card h3; unsectioned catalog cards retain h2. */
function torantejarat_loop_product_title() {
	$level = in_array( wc_get_loop_prop( 'name' ), array( 'torantejarat-home', 'related', 'up-sells', 'cross-sells' ), true ) ? 3 : 2;
	$class = apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' );
	printf( '<h%1$d class="%2$s">%3$s</h%1$d>', $level, esc_attr( $class ), esc_html( get_the_title() ) );
}

/** Keep notices outside the source grid; Woo still renders the form, totals and CTA. */
function torantejarat_cart_layout_start() { echo '<div class="torantejarat-native-cart-layout">'; }
function torantejarat_cart_layout_end() { echo '</div>'; }
