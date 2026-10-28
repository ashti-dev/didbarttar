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
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 20 );
	add_filter( 'woocommerce_product_loop_start', __NAMESPACE__ . '\\torantejarat_product_loop_start' );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	add_action( 'woocommerce_before_main_content', __NAMESPACE__ . '\\torantejarat_woocommerce_breadcrumb', 20 );
	add_filter( 'woocommerce_add_to_cart_fragments', __NAMESPACE__ . '\\torantejarat_cart_fragments' );
	add_filter( 'woocommerce_add_to_cart_fragments', __NAMESPACE__ . '\\torantejarat_dropdown_fragments', 20 );
	add_filter( 'woocommerce_currency_symbol', __NAMESPACE__ . '\\torantejarat_currency_symbol', 10, 2 );
	add_action( 'init', __NAMESPACE__ . '\\torantejarat_oembed_providers' );
	add_action( 'woocommerce_shop_loop_header', __NAMESPACE__ . '\\torantejarat_archive_intro_end', 99 );
	add_filter( 'woocommerce_output_related_products_args', __NAMESPACE__ . '\\torantejarat_related_layout' );
	add_filter( 'woocommerce_cross_sell_columns', __NAMESPACE__ . '\\torantejarat_cross_sell_size' );
	add_filter( 'woocommerce_cross_sell_total', __NAMESPACE__ . '\\torantejarat_cross_sell_size' );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_alternatives', 19 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_recently_viewed_slot', 25 );
	add_action( 'woocommerce_after_shop_loop', __NAMESPACE__ . '\\torantejarat_recently_viewed_slot', 51 );
	add_action( 'woocommerce_archive_description', __NAMESPACE__ . '\\torantejarat_archive_categories', 30 );
	add_action( 'wp_ajax_torantejarat_compare', __NAMESPACE__ . '\\torantejarat_compare_fragment' );
	add_action( 'wp_ajax_nopriv_torantejarat_compare', __NAMESPACE__ . '\\torantejarat_compare_fragment' );
	add_action( 'wp_ajax_torantejarat_finder', __NAMESPACE__ . '\\torantejarat_finder_fragment' );
	add_action( 'wp_ajax_nopriv_torantejarat_finder', __NAMESPACE__ . '\\torantejarat_finder_fragment' );
	add_action( 'rest_api_init', __NAMESPACE__ . '\\torantejarat_search_rest_route' );
	foreach ( array( 'save_post_product', 'created_product_cat', 'edited_product_cat', 'delete_product_cat' ) as $torantejarat_search_hook ) {
		add_action( $torantejarat_search_hook, __NAMESPACE__ . '\\torantejarat_search_bust' );
	}
	add_filter( 'woocommerce_states', __NAMESPACE__ . '\\torantejarat_ir_states' );
	add_filter( 'default_checkout_billing_country', static fn() => 'IR' );
	add_filter( 'default_checkout_shipping_country', static fn() => 'IR' );
	add_filter( 'woocommerce_checkout_fields', __NAMESPACE__ . '\\torantejarat_checkout_fields', 20 );
	add_action( 'woocommerce_checkout_process', __NAMESPACE__ . '\\torantejarat_checkout_phone_check', 20 );
	add_filter( 'woocommerce_checkout_posted_data', __NAMESPACE__ . '\\torantejarat_checkout_force_country' );
	add_action( 'woocommerce_admin_order_data_after_shipping_address', __NAMESPACE__ . '\\torantejarat_tracking_field' );
	add_action( 'woocommerce_process_shop_order_meta', __NAMESPACE__ . '\\torantejarat_tracking_save' );
	add_action( 'woocommerce_order_details_after_order_table', __NAMESPACE__ . '\\torantejarat_tracking_account' );
	add_filter( 'woocommerce_email_order_meta_fields', __NAMESPACE__ . '\\torantejarat_tracking_email', 10, 3 );
	add_action( 'init', __NAMESPACE__ . '\\torantejarat_register_shipped_status' );
	add_filter( 'wc_order_statuses', __NAMESPACE__ . '\\torantejarat_shipped_status_list' );
	add_action( 'woocommerce_order_details_before_order_table', __NAMESPACE__ . '\\torantejarat_order_timeline' );
	add_action( 'wp_footer', __NAMESPACE__ . '\\torantejarat_recent_source', 5 );

	add_action( 'woocommerce_before_cart', __NAMESPACE__ . '\\torantejarat_cart_layout_start', 99 );
	add_action( 'woocommerce_after_cart', __NAMESPACE__ . '\\torantejarat_cart_layout_end', 1 );
	add_filter( 'woocommerce_return_to_shop_text', __NAMESPACE__ . '\\torantejarat_return_to_shop_text' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_cart_empty_takeover' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_shop_search_redirect', 9 );
	add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_empty_search_redirect' );
	add_action( 'woocommerce_cart_is_empty', __NAMESPACE__ . '\\torantejarat_cart_empty_extras', 20 );
	add_action( 'woocommerce_cart_is_empty', __NAMESPACE__ . '\\torantejarat_cart_empty_suggestions', 30 );
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
	add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20 );

	add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_grid_start', 5 );
	add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_gallery_bottom', 25 );
	add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_gallery_end', 30 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_summary_start', 1 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_meta', 2 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_specs', 21 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_purchase_start', 25 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 26 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_social_proof', 34 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_purchase_end', 35 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_actions', 36 );
	add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_summary_end', 99 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_details_start', 5 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_assurance', 6 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_pdp_tabs_nav', 7 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_overview', 8 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_spec_section', 10 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_video_section', 12 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_features', 13 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_faq', 14 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_product_details_end', 18 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	add_action( 'woocommerce_after_single_product_summary', __NAMESPACE__ . '\\torantejarat_related_section', 20 );
	add_filter( 'woocommerce_single_product_image_thumbnail_html', __NAMESPACE__ . '\\torantejarat_main_image_tag', 10, 2 );
	add_filter( 'gettext', __NAMESPACE__ . '\\torantejarat_related_heading', 10, 3 );
	add_filter( 'woocommerce_no_available_payment_methods_message', __NAMESPACE__ . '\\torantejarat_no_payment_message' );
	add_action( 'before_woocommerce_pay_form', __NAMESPACE__ . '\\torantejarat_invoice_header', 5 );
	add_action( 'before_woocommerce_pay', __NAMESPACE__ . '\\torantejarat_receipt_invoice', 5 );
	add_action( 'after_woocommerce_pay', __NAMESPACE__ . '\\torantejarat_receipt_actions', 5 );
	add_action( 'woocommerce_pay_order_before_payment', __NAMESPACE__ . '\\torantejarat_pay_reminder' );
	add_filter( 'woocommerce_pay_order_button_text', __NAMESPACE__ . '\\torantejarat_pay_button_text' );
	add_action( 'woocommerce_view_order', __NAMESPACE__ . '\\torantejarat_invoice_header', 5 );
	add_action( 'woocommerce_order_details_after_order_table', __NAMESPACE__ . '\\torantejarat_invoice_front_actions', 20 );
	add_filter( 'formatted_woocommerce_price', __NAMESPACE__ . '\\torantejarat_price_digits' );
	add_filter( 'woocommerce_price_format', __NAMESPACE__ . '\\torantejarat_price_format' );
	add_filter( 'woocommerce_gateway_title', __NAMESPACE__ . '\\torantejarat_gateway_title_clean' );
	add_action( 'woocommerce_login_form_end', __NAMESPACE__ . '\\torantejarat_wc_register_link' );
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\torantejarat_product_meta_boxes' );
	add_action( 'save_post_product', __NAMESPACE__ . '\\torantejarat_save_product_meta', 10, 3 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_admin_product_assets' );
	add_action( 'wp_footer', __NAMESPACE__ . '\\torantejarat_mobile_purchase_bar' );
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

function torantejarat_currency_symbol( $symbol, $currency ) {
	if ( in_array( $currency, array( 'IRHT', 'IRT' ), true ) ) {
		return __( 'تومان', 'torantejarat' );
	}
	return $symbol;
}

/**
 * Register missing video providers (Aparat) for WordPress oEmbed.
 */
function torantejarat_oembed_providers() {
	wp_oembed_add_provider( 'https://www.aparat.com/*', 'https://www.aparat.com/oembed', false );
}

/**
 * Resolve a merchant video URL to safe player markup.
 * Order: media-library file → direct file → YouTube → Aparat → oEmbed → watch link.
 * Returns array( 'html' => string, 'empty' => bool ).
 */
function torantejarat_product_video_player( $product_id ) {
	$attachment_id = (int) get_post_meta( $product_id, '_torantejarat_video_id', true );
	$url           = trim( (string) get_post_meta( $product_id, '_torantejarat_video', true ) );
	if ( $attachment_id > 0 ) {
		$file = wp_get_attachment_url( $attachment_id );
		if ( $file ) { $url = $file; }
	}
	if ( '' === $url ) {
		return array( 'html' => '', 'empty' => true );
	}
	// Direct video file (mp4/webm/ogg)
	if ( preg_match( '/\.(mp4|webm|ogg)(\?|#|$)/i', $url ) ) {
		return array( 'html' => '<video controls playsinline preload="none" src="' . esc_url( $url ) . '"></video>', 'empty' => false );
	}
	// YouTube (watch, youtu.be, embed, shorts)
	if ( preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([a-zA-Z0-9_-]{6,})#', $url, $matches ) ) {
		$embed = 'https://www.youtube.com/embed/' . $matches[1];
		return array( 'html' => '<iframe src="' . esc_url( $embed ) . '" width="800" height="450" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" title="' . esc_attr__( 'ویدیوی محصول', 'torantejarat' ) . '"></iframe>', 'empty' => false );
	}
	// Aparat (v/, video/video/embed/videohash/, /v/ pattern)
	if ( preg_match( '#aparat\.com/(?:v/|video/video/embed/videohash/)([a-zA-Z0-9]+)#i', $url, $matches ) ) {
		$embed = 'https://www.aparat.com/video/video/embed/videohash/' . $matches[1] . '/vt/frame';
		return array( 'html' => '<iframe src="' . esc_url( $embed ) . '" width="800" height="450" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" title="' . esc_attr__( 'ویدیوی محصول', 'torantejarat' ) . '"></iframe>', 'empty' => false );
	}
	// Generic embed URL (contains /embed/)
	if ( false !== strpos( $url, '/embed/' ) ) {
		return array( 'html' => '<iframe src="' . esc_url( $url ) . '" width="800" height="450" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" title="' . esc_attr__( 'ویدیوی محصول', 'torantejarat' ) . '"></iframe>', 'empty' => false );
	}
	// Vimeo
	if ( preg_match( '#vimeo\.com/(\d+)#', $url, $matches ) ) {
		$embed = 'https://player.vimeo.com/video/' . $matches[1];
		return array( 'html' => '<iframe src="' . esc_url( $embed ) . '" width="800" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" title="' . esc_attr__( 'ویدیوی محصول', 'torantejarat' ) . '"></iframe>', 'empty' => false );
	}
	// oEmbed fallback (WordPress built-in + Aparat provider)
	$oembed = wp_oembed_get( $url, array( 'width' => 800 ) );
	if ( $oembed ) {
		return array( 'html' => $oembed, 'empty' => false );
	}
	// Final fallback: watch link
	return array(
		'html'  => '<p><a class="torantejarat-btn torantejarat-btn-outline" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html__( 'مشاهده ویدیو', 'torantejarat' ) . '</a></p>',
		'empty' => false,
	);
}

/**
 * Unified breadcrumb on Woo templates — same component as every other page.
 * Shop/category archives render inside .torantejarat-shop-intro;
 * single products get a contained shell with mobile wrap for long names.
 */
function torantejarat_woocommerce_breadcrumb() {
	$trail = array( torantejarat_home_crumb() );
	if ( is_product() ) {
		global $product;
		if ( wc_get_page_id( 'shop' ) > 0 ) {
			$trail[] = array( 'label' => get_the_title( wc_get_page_id( 'shop' ) ), 'url' => wc_get_page_permalink( 'shop' ) );
		}
		if ( $product instanceof \WC_Product ) {
			$terms = get_the_terms( $product->get_id(), 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( array_reverse( get_ancestors( $terms[0]->term_id, 'product_cat' ) ) as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, 'product_cat' );
					if ( $ancestor && ! is_wp_error( $ancestor ) ) {
						$trail[] = array( 'label' => $ancestor->name, 'url' => get_term_link( $ancestor ) );
					}
				}
				$trail[] = array( 'label' => $terms[0]->name, 'url' => get_term_link( $terms[0] ) );
			}
			$trail[] = array( 'label' => $product->get_name() );
		} else {
			$trail[] = array( 'label' => get_the_title() );
		}
		echo '<div class="torantejarat-container torantejarat-product-breadcrumb-shell">';
		torantejarat_breadcrumb( $trail );
		echo '</div>';
		return;
	}
	if ( is_product_taxonomy() ) {
		if ( wc_get_page_id( 'shop' ) > 0 ) {
			$trail[] = array( 'label' => get_the_title( wc_get_page_id( 'shop' ) ), 'url' => wc_get_page_permalink( 'shop' ) );
		}
		$term = get_queried_object();
		if ( $term instanceof \WP_Term ) {
			foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, $term->taxonomy );
				if ( $ancestor && ! is_wp_error( $ancestor ) ) {
					$trail[] = array( 'label' => $ancestor->name, 'url' => get_term_link( $ancestor ) );
				}
			}
			$trail[] = array( 'label' => $term->name );
		}
	} elseif ( is_shop() || is_cart() || is_checkout() || is_account_page() ) {
		/* Explicit page titles: on archives get_the_title() would return the
		   first loop product, not the page. */
		$torantejarat_title = '';
		if ( is_shop() && wc_get_page_id( 'shop' ) > 0 ) {
			$torantejarat_title = get_the_title( wc_get_page_id( 'shop' ) );
		} elseif ( ( is_cart() || is_checkout() || is_account_page() ) && get_the_ID() ) {
			$torantejarat_title = get_the_title( get_the_ID() );
		}
		if ( '' === $torantejarat_title ) {
			$torantejarat_title = get_the_title();
		}
		$trail[] = array( 'label' => $torantejarat_title );
	}
	torantejarat_breadcrumb( $trail );
}

function torantejarat_product_loop_start( $html ) {
	$class = 'torantejarat-product-grid';
	if ( torantejarat_is_catalog() ) {
		$class .= ' torantejarat-catalog-grid';
	} elseif ( is_product() || is_cart() ) {
		$class .= ' torantejarat-related-grid';
	}
	return str_replace( 'class="products', 'class="products ' . $class, $html );
}

function torantejarat_cart_link() {
	if ( ! torantejarat_woocommerce_available() || wc_get_page_id( 'cart' ) <= 0 ) {
		return;
	}
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : null;
	$label = null === $count ? __( 'سبد خرید', 'torantejarat' ) : sprintf( __( '%s کالا در سبد خرید', 'torantejarat' ), torantejarat_fa_number( $count ) );
	?>
	<a class="torantejarat-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" aria-haspopup="true">
		<?php torantejarat_icon( 'cart' ); ?><span class="torantejarat-cart-label"><?php esc_html_e( 'سبد خرید', 'torantejarat' ); ?></span>
		<?php if ( WC()->cart ) : ?><b><?php echo esc_html( torantejarat_fa_number( WC()->cart->get_cart_contents_count() ) ); ?></b><?php endif; ?>
	</a>
	<?php
}

/**
 * Header mini-cart dropdown. Refreshed as a whole via its own fragment,
 * so the empty box and the native list never duplicate each other.
 */
function torantejarat_cart_dropdown() {
	if ( ! torantejarat_woocommerce_available() || wc_get_page_id( 'cart' ) <= 0 ) {
		return;
	}
	$has_items = WC()->cart && ! WC()->cart->is_empty();
	?>
	<div class="torantejarat-cart-dropdown" role="region" aria-label="<?php esc_attr_e( 'سبد خرید', 'torantejarat' ); ?>">
		<div class="widget_shopping_cart_content">
			<?php if ( $has_items ) : ?>
				<?php wc_get_template( 'cart/mini-cart.php', array( 'list_class' => '' ) ); ?>
			<?php else : ?>
				<span class="torantejarat-mini-empty">
					<span class="torantejarat-mini-empty-icon"><?php torantejarat_icon( 'cart' ); ?></span>
					<b><?php esc_html_e( 'سبد خرید شما خالی است.', 'torantejarat' ); ?></b>
					<span><?php esc_html_e( 'هنوز محصولی اضافه نکرده‌اید.', 'torantejarat' ); ?></span>
					<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'مشاهده محصولات', 'torantejarat' ); ?></a><?php endif; ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

function torantejarat_cart_fragments( $fragments ) {
	if ( wc_get_page_id( 'cart' ) > 0 ) {
		ob_start();
		torantejarat_cart_link();
		$fragments['a.torantejarat-cart-link'] = ob_get_clean();
	}
	/* Bottom mobile menu badge. Always rendered (hidden when zero) so the
	   fragment can update it in place after every AJAX add-to-cart. */
	$menu_count = ( WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
	ob_start();
	?>
	<b data-menu-cart-count<?php echo $menu_count > 0 ? '' : ' hidden'; ?>><?php echo esc_html( torantejarat_fa_number( $menu_count ) ); ?></b>
	<?php
	$fragments['b[data-menu-cart-count]'] = ob_get_clean();
	return $fragments;
}

function torantejarat_dropdown_fragments( $fragments ) {
	unset( $fragments['div.widget_shopping_cart_content'] );
	ob_start();
	torantejarat_cart_dropdown();
	$dropdown = ob_get_clean();
	if ( '' !== trim( $dropdown ) ) {
		$fragments['div.torantejarat-cart-dropdown'] = $dropdown;
	}
	return $fragments;
}

function torantejarat_product_categories( $home = false ) {
	if ( $home ) {
		$torantejarat_home_slugs = apply_filters(
			'torantejarat_home_categories',
			array( 'industrial', 'monitor', 'deep-well', 'wifi' )
		);
		$terms = array();
		foreach ( $torantejarat_home_slugs as $torantejarat_slug ) {
			$torantejarat_term = get_term_by( 'slug', $torantejarat_slug, 'product_cat' );
			if ( $torantejarat_term && ! is_wp_error( $torantejarat_term ) ) {
				$terms[] = $torantejarat_term;
			}
		}
	} else {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 0 ) );
	}
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}
	echo '<div class="' . ( $home ? 'torantejarat-category-grid' : 'torantejarat-shop-categories' ) . '">';
	foreach ( $terms as $term ) {
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) { continue; }
		$torantejarat_icons = array(
			'industrial' => 'factory',
			'monitor'    => 'monitor',
			'deep-well'  => 'droplet',
			'wifi'       => 'wifi',
			'imported'   => 'globe',
		);
		$torantejarat_icon = isset( $torantejarat_icons[ $term->slug ] ) ? $torantejarat_icons[ $term->slug ] : 'scan';
		$torantejarat_mod  = $home && isset( $torantejarat_icons[ $term->slug ] ) ? ' is-' . $term->slug : '';
		echo '<a class="' . ( $home ? 'torantejarat-category-card' . $torantejarat_mod : 'torantejarat-category-link' ) . '" href="' . esc_url( $url ) . '"' . ( is_tax( 'product_cat', $term->term_id ) ? ' aria-current="page"' : '' ) . '>';
		if ( $home ) {
			echo '<span class="torantejarat-category-icon">'; torantejarat_icon( $torantejarat_icon ); echo '</span><h3>' . esc_html( $term->name ) . '</h3><p>' . esc_html( wp_trim_words( wp_strip_all_tags( $term->description ), 18 ) ) . '</p><span class="torantejarat-category-foot"><span class="torantejarat-category-count">' . esc_html( sprintf( __( '%s دستگاه', 'torantejarat' ), torantejarat_fa_number( $term->count ) ) ) . '</span><span class="torantejarat-category-arrow">'; torantejarat_icon( 'arrow' ); echo '</span></span>';
		} else {
			echo '<span>' . esc_html( $term->name ) . '</span><b>' . esc_html( torantejarat_fa_number( $term->count ) ) . '</b>';
		}
		echo '</a>';
	}
	echo '</div>';
}

function torantejarat_archive_categories() {
	torantejarat_product_categories();
}

/**
 * Hero slider slides built from real WooCommerce products.
 * Each slide: src/alt/caption/link/linkText/label for hero-slider.js.
 */
function torantejarat_hero_slides() {
	if ( ! torantejarat_woocommerce_available() ) { return array(); }
	$slides = array();
	foreach ( array( 'SB-CASE', 'SB-PRO', 'SB-G52', 'SB-HEAD' ) as $sku ) {
		$found = wc_get_products( array( 'status' => 'publish', 'limit' => 1, 'sku' => $sku ) );
		if ( ! $found || ! $found[0] instanceof \WC_Product ) { continue; }
		$slide = torantejarat_hero_slide_data( $found[0] );
		if ( $slide ) { $slides[] = $slide; }
		if ( count( $slides ) >= 4 ) { break; }
	}
	if ( count( $slides ) < 2 ) {
		$fallback = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
		foreach ( $fallback as $product ) {
			if ( ! $product instanceof \WC_Product || ! $product->is_visible() ) { continue; }
			$exists = false;
			foreach ( $slides as $slide ) {
				if ( isset( $slide['id'] ) && (int) $slide['id'] === $product->get_id() ) { $exists = true; break; }
			}
			if ( $exists ) { continue; }
			$slide = torantejarat_hero_slide_data( $product );
			if ( $slide ) { $slides[] = $slide; }
			if ( count( $slides ) >= 4 ) { break; }
		}
	}
	return array_values( $slides );
}

function torantejarat_hero_slide_data( $product ) {
	if ( ! $product instanceof \WC_Product || ! $product->is_visible() || ! $product->get_image_id() ) {
		return null;
	}
	$image = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
	if ( ! $image ) { return null; }
	$description = $product->get_short_description() ? $product->get_short_description() : $product->get_name();
	return array(
		'id'       => $product->get_id(),
		'src'      => $image,
		'alt'      => $product->get_name(),
		'caption'  => wp_trim_words( wp_strip_all_tags( $description ), 12 ),
		'link'     => get_permalink( $product->get_id() ),
		'linkText' => __( 'بررسی دستگاه', 'torantejarat' ),
		'label'    => $product->get_sku() ? $product->get_sku() : $product->get_name(),
	);
}

function torantejarat_product_grid_start() {
	echo '<section class="torantejarat-pdp-top"><div class="torantejarat-container torantejarat-pdp-grid"><div class="torantejarat-pdp-gallery">';
}
function torantejarat_product_gallery_end() { echo '</div>'; }
function torantejarat_product_summary_start() { echo '<div class="torantejarat-pdp-summary">'; }
function torantejarat_product_summary_end() { echo '</div>'; }
function torantejarat_product_purchase_start() {
	echo '<div class="torantejarat-pdp-purchase">';
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	if ( $product->is_type( 'variable' ) ) {
		$version = sprintf( __( '%s نسخه', 'torantejarat' ), torantejarat_fa_number( count( $product->get_children() ) ) );
	} elseif ( $product->get_sku() ) {
		$version = $product->get_sku();
	} else {
		$version = __( 'نسخه استاندارد', 'torantejarat' );
	}
	echo '<div class="torantejarat-pdp-choice"><span>' . esc_html__( 'نسخه دستگاه', 'torantejarat' ) . '</span><strong>';
	torantejarat_icon( 'check' );
	echo ' <span>' . esc_html( $version ) . '</span></strong></div>';
}
function torantejarat_product_purchase_end() {
	if ( torantejarat_is_preview() ) {
		echo '<p class="torantejarat-pdp-demo-note">' . esc_html__( 'این نسخه پیش‌نمایش است؛ قیمت و مشخصات نمونه‌اند و پرداخت فعال نیست.', 'torantejarat' ) . '</p>';
	}
	echo '</div>';
}

/**
 * Honest social proof — real WooCommerce sales count only.
 * Shown only when total sales reach the threshold; otherwise nothing.
 */
function torantejarat_product_social_proof() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$count = absint( $product->get_total_sales() );
	if ( $count < 5 ) { return; }
	echo '<div class="torantejarat-pdp-proof">';
	torantejarat_icon( 'check' );
	echo '<span>' . sprintf(
		/* translators: %s: number of completed purchases. */
		esc_html__( 'تا الان %s نفر این محصول را خریداری کرده‌اند.', 'torantejarat' ),
		'<strong>' . esc_html( torantejarat_fa_number( $count ) ) . '</strong>'
	) . '</span></div>';
}
function torantejarat_product_details_start() { echo '</div></section><div class="torantejarat-container torantejarat-product-details">'; }
function torantejarat_product_details_end() { echo '</div>'; }

function torantejarat_woocommerce_notice() {
	if ( torantejarat_woocommerce_available() || ! current_user_can( 'activate_plugins' ) ) { return; }
	?>
	<div class="notice notice-warning torantejarat-notice"><p><?php esc_html_e( 'بخش فروشگاه به ووکامرس نیاز دارد. محتوای سایت همچنان در دسترس است.', 'torantejarat' ); ?></p></div>
	<?php
}

function torantejarat_archive_intro_end() {
	echo '</div></section><section class="torantejarat-shop-body"><div class="torantejarat-container">';
}

function torantejarat_related_layout( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}

function torantejarat_cross_sell_size() {
	return 4;
}

function torantejarat_product_attribute_items( $product, $limit ) {
	$items = array();
	if ( ! $product instanceof \WC_Product || post_password_required( $product->get_id() ) ) {
		return $items;
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute instanceof \WC_Product_Attribute || ! $attribute->get_visible() ) { continue; }
		$value = $product->get_attribute( $attribute->get_name() );
		if ( '' === trim( $value ) ) { continue; }
		$name = $attribute->get_name();
		// For custom (non-taxonomy) attributes, decode the stored slug to get the label
		if ( ! $attribute->get_taxonomy() ) {
			$label = str_replace( '-', ' ', urldecode( preg_replace( '/^pa_/', '', $name ) ) );
		} else {
			$label = wc_attribute_label( $name, $product );
		}
		if ( '' === trim( $label ) ) { continue; }
		$items[] = array( 'label' => $label, 'value' => $value );
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

/**
 * PDP meta row — exact product.html copy: category tag + SKU.
 */
function torantejarat_product_meta() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	$tag   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	echo '<div class="torantejarat-pdp-meta">';
	if ( $tag ) { echo '<span>' . esc_html( $tag ) . '</span>'; }
	if ( $product->get_sku() ) {
		echo '<span>' . esc_html__( 'شناسه:', 'torantejarat' ) . ' <b dir="ltr">' . esc_html( $product->get_sku() ) . '</b></span>';
	}
	echo '</div>';
}

/**
 * Gallery bottom row — exact product.html copy (text + video anchor).
 * Native Woo gallery keeps its own thumbnails/zoom/lightbox.
 */
function torantejarat_product_gallery_bottom() {
	?>
	<div class="torantejarat-pdp-gallery-bottom">
		<p><?php esc_html_e( 'جزئیات دستگاه را از نزدیک ببینید.', 'torantejarat' ); ?><br><small><?php esc_html_e( 'تصویر موجود در کاتالوگ', 'torantejarat' ); ?></small></p>
		<a href="#torantejarat-product-video" class="torantejarat-pdp-video-link"><?php torantejarat_icon( 'search' ); ?> <?php esc_html_e( 'نمونه تصویر', 'torantejarat' ); ?></a>
	</div>
	<?php
}

/**
 * Category tag overlay on the main gallery image — exact product.html copy.
 */
function torantejarat_main_image_tag( $html, $attachment_id ) {
	global $product;
	if ( ! $product instanceof \WC_Product || (int) $attachment_id !== (int) $product->get_image_id() ) {
		return $html;
	}
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	$tag   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	if ( '' === $tag ) { return $html; }
	return '<span class="torantejarat-pdp-photo-tag">' . esc_html( $tag ) . '</span>' . $html;
}

/**
 * Actions row under the purchase box — consult + compare links to real pages.
 */
function torantejarat_product_actions() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$contact = get_page_by_path( 'contact' );
	$compare = get_page_by_path( 'compare' );
	if ( ! $contact && ! $compare ) { return; }
	echo '<div class="torantejarat-pdp-actions">';
	if ( $compare ) {
		$url = add_query_arg( 'add', $product->get_id(), get_permalink( $compare ) );
		echo '<a href="' . esc_url( $url ) . '">';
		torantejarat_icon( 'compare' );
		echo ' ' . esc_html__( 'مقایسه با دستگاه‌های دیگر', 'torantejarat' ) . '</a>';
	}
	if ( $contact ) {
		$url = add_query_arg( 'device', $product->get_id(), get_permalink( $contact ) );
		echo '<a href="' . esc_url( $url ) . '">';
		torantejarat_icon( 'search' );
		echo ' ' . esc_html__( 'مشاوره انتخاب', 'torantejarat' ) . '</a>';
	}
	echo '</div>';
}

/**
 * Assurance strip — exact product.html copy (static, no business claims).
 */
function torantejarat_product_assurance() {
	if ( ! is_product() ) { return; }
	?>
	<div class="torantejarat-container"><div class="torantejarat-pdp-assurance">
		<div><?php torantejarat_icon( 'scan' ); ?><span><b><?php esc_html_e( 'انتخاب بر اساس مسیر شما', 'torantejarat' ); ?></b><small><?php esc_html_e( 'بررسی قطر، طول و شرایط محیط', 'torantejarat' ); ?></small></span></div>
		<div><?php torantejarat_icon( 'compare' ); ?><span><b><?php esc_html_e( 'مقایسه جزئیات فنی', 'torantejarat' ); ?></b><small><?php esc_html_e( 'تفاوت‌ها را پیش از انتخاب ببینید', 'torantejarat' ); ?></small></span></div>
		<div><?php torantejarat_icon( 'wrench' ); ?><span><b><?php esc_html_e( 'بررسی خدمات دستگاه', 'torantejarat' ); ?></b><small><?php esc_html_e( 'ضمانت و قطعات را استعلام کنید', 'torantejarat' ); ?></small></span></div>
	</div></div>
	<?php
}

/**
 * Anchor tabs nav — exact product.html copy.
 */
function torantejarat_pdp_tabs_nav() {
	if ( ! is_product() ) { return; }
	?>
	<div class="torantejarat-container"><nav class="torantejarat-pdp-tabs" aria-label="<?php esc_attr_e( 'بخش‌های محصول', 'torantejarat' ); ?>">
		<a href="#torantejarat-overview"><?php esc_html_e( 'معرفی دستگاه', 'torantejarat' ); ?></a>
		<a href="#torantejarat-specifications"><?php esc_html_e( 'مشخصات فنی', 'torantejarat' ); ?></a>
		<a href="#torantejarat-product-video"><?php esc_html_e( 'تصویر و ویدیو', 'torantejarat' ); ?></a>
		<a href="#torantejarat-product-questions"><?php esc_html_e( 'پرسش‌های خرید', 'torantejarat' ); ?></a>
	</nav></div>
	<?php
}

/**
 * Overview section — short description + limitation aside (per-product meta).
 */
function torantejarat_product_overview() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$quiz = get_page_by_path( 'quiz' );
	?>
	<div class="torantejarat-container"><section id="torantejarat-overview" class="torantejarat-pdp-overview">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'نگاه نزدیک‌تر به دستگاه', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'انتخاب درست از شناخت مسیر شروع می‌شود.', 'torantejarat' ); ?></h2>
			<p><?php echo esc_html( wp_strip_all_tags( $product->get_short_description() ) ); ?> <?php esc_html_e( 'برای تصمیم دقیق‌تر، مشخصات این دستگاه را با ابعاد ورودی، طول مسیر و شرایط کاری خود تطبیق دهید.', 'torantejarat' ); ?></p>
		<?php $torantejarat_full_description = $product->get_description(); ?>
		<?php if ( '' !== trim( $torantejarat_full_description ) ) : ?><div class="torantejarat-pdp-description-full"><?php echo apply_filters( 'the_content', $torantejarat_full_description ); ?></div><?php endif; ?>
			<?php if ( $quiz ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink( $quiz ) ); ?>"><?php esc_html_e( 'این دستگاه برای من مناسب است؟', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
		</div>
		<?php /* Limitation aside removed per owner request; content is full-width. */ ?>
	</section></div>
	<?php
}

/**
 * Full spec table — model name + visible attributes (no invented rows).
 */
function torantejarat_product_spec_section() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$items = torantejarat_product_attribute_items( $product, 6 );
	?>
	<div class="torantejarat-container"><section id="torantejarat-specifications" class="torantejarat-pdp-spec-section">
		<div class="torantejarat-section-heading"><div><span class="torantejarat-eyebrow"><?php esc_html_e( 'جزئیات، بدون ابهام', 'torantejarat' ); ?></span><h2><?php esc_html_e( 'مشخصات فنی دستگاه', 'torantejarat' ); ?></h2></div><span class="torantejarat-pdp-data-label"><?php esc_html_e( 'مشخصات ثبت‌شده محصول', 'torantejarat' ); ?></span></div>
		<table class="torantejarat-spec-table"><caption class="torantejarat-sr-only"><?php echo esc_html( sprintf( __( 'مشخصات %s', 'torantejarat' ), $product->get_name() ) ); ?></caption><tbody>
			<tr><th scope="row"><?php esc_html_e( 'مدل دستگاه', 'torantejarat' ); ?></th><td><?php echo esc_html( $product->get_name() ); ?></td></tr>
			<?php foreach ( $items as $item ) : ?><tr><th scope="row"><?php echo esc_html( $item['label'] ); ?></th><td><?php echo esc_html( $item['value'] ); ?></td></tr><?php endforeach; ?>
		</tbody></table>
	</section></div>
	<?php
}

/**
 * Product video section — full-width click-to-play facade.
 * Poster = product image; player markup lives in <template> and loads only on click.
 * Meta key _torantejarat_video is set from the product edit screen (no plugin).
 */
function torantejarat_product_video_section() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$contact = get_page_by_path( 'contact' );
	$player  = torantejarat_product_video_player( $product->get_id() );
	$embed   = $player['html'];
	$poster  = $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'large' ) : '';
	?>
	<div class="torantejarat-container"><section id="torantejarat-product-video" class="torantejarat-pdp-video-section">
		<?php if ( $embed ) : ?>
			<div class="torantejarat-pdp-video-frame" data-video-facade>
				<button type="button" class="torantejarat-video-cover" data-video-cover aria-label="<?php esc_attr_e( 'پخش ویدیوی محصول', 'torantejarat' ); ?>">
					<?php if ( $poster ) : ?><img src="<?php echo esc_url( $poster ); ?>" alt="" width="800" height="450" loading="lazy"><?php endif; ?>
					<span class="torantejarat-video-play" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
				</button>
				<template><?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Merchant-authored oEmbed/video markup. ?></template>
			</div>
		<?php else : ?>
			<div class="torantejarat-pdp-video-placeholder">
				<?php torantejarat_icon( 'search' ); ?>
				<p><?php esc_html_e( 'ویدیوی تأییدشده این مدل هنوز بارگذاری نشده است.', 'torantejarat' ); ?></p>
				<?php if ( $contact ) : ?><a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( add_query_arg( array( 'device' => $product->get_id(), 'topic' => 'video' ), get_permalink( $contact ) ) ); ?>"><?php esc_html_e( 'درخواست نمونه تصویر', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
			</div>
		<?php endif; ?>
	</section></div>
	<?php
}

/**
 * Product features grid — built from the product's own visible attributes.
 * No invented claims: empty attributes = no section.
 */
function torantejarat_product_features() {
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	$attribute_items = torantejarat_product_attribute_items( $product, 4 );
	if ( ! $attribute_items ) { return; }
	$icons    = array( 'scan', 'compare', 'wrench', 'search' );
	$features = array();
	foreach ( $attribute_items as $index => $attribute_item ) {
		$features[] = array( 'icon' => $icons[ $index % count( $icons ) ], 'title' => $attribute_item['label'], 'desc' => $attribute_item['value'] );
	}
	?>
	<div class="torantejarat-container"><section class="torantejarat-pdp-features">
		<div class="torantejarat-section-heading">
			<div>
				<span class="torantejarat-eyebrow"><?php esc_html_e( 'چرا این دستگاه؟', 'torantejarat' ); ?></span>
				<h2><?php esc_html_e( 'ویژگی‌های کلیدی', 'torantejarat' ); ?></h2>
			</div>
		</div>
		<div class="torantejarat-pdp-features-grid">
			<?php foreach ( $features as $feature ) : ?>
				<div class="torantejarat-pdp-feature-card">
					<span class="torantejarat-pdp-feature-icon"><?php torantejarat_icon( $feature['icon'] ); ?></span>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section></div>
	<?php
}

/**
/**
 * Product FAQ — per-product Q&A from meta key _torantejarat_faq.
 * Format: one per line as "question ||| answer". Falls back to generic guidance.
 */
function torantejarat_product_faq() {
	if ( ! is_product() ) { return; }
	global $product;
	$custom = array();
	if ( $product instanceof \WC_Product ) {
		$raw = (string) get_post_meta( $product->get_id(), '_torantejarat_faq', true );
		foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) { continue; }
			$parts = false !== strpos( $line, '|||' ) ? explode( '|||', $line, 2 ) : explode( '|', $line, 2 );
			$parts = array_map( 'trim', $parts );
			if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
				$custom[] = array( 'q' => $parts[0], 'a' => $parts[1] );
			}
		}
	}
	?>
	<div class="torantejarat-container"><section id="torantejarat-product-questions" class="torantejarat-pdp-faq">
		<div><span class="torantejarat-eyebrow"><?php esc_html_e( 'پیش از تصمیم نهایی', 'torantejarat' ); ?></span><h2><?php esc_html_e( 'پرسش‌های مهم خرید', 'torantejarat' ); ?></h2><p><?php esc_html_e( 'پاسخ سؤال‌های اختصاصی مسیرتان را هم بگیرید.', 'torantejarat' ); ?></p></div>
		<div class="torantejarat-faq">
			<?php if ( $custom ) : ?>
				<?php foreach ( $custom as $faq ) : ?><details><summary><?php echo esc_html( $faq['q'] ); ?></summary><p><?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?></p></details><?php endforeach; ?>
			<?php else : ?>
				<details><summary><?php esc_html_e( 'آیا این دستگاه برای کاربرد من مناسب است؟', 'torantejarat' ); ?></summary><p><?php esc_html_e( 'قطر ورودی، طول مسیر و شرایط محیط را با مشخصات تأییدشده مدل تطبیق دهید. راهنمای انتخاب فقط پیشنهاد اولیه ارائه می‌کند.', 'torantejarat' ); ?></p></details>
				<details><summary><?php esc_html_e( 'چه اقلامی همراه دستگاه ارسال می‌شود؟', 'torantejarat' ); ?></summary><p><?php esc_html_e( 'اقلام همراه این نسخه هنوز تأیید نشده‌اند. پیش از سفارش، فهرست دقیق جعبه را از فروشنده بخواهید؛ وجود وسیله در عکس به‌تنهایی تأیید اقلام همراه نیست.', 'torantejarat' ); ?></p></details>
				<?php if ( torantejarat_is_preview() ) : ?>
					<details><summary><?php esc_html_e( 'شرایط ضمانت و ارسال چگونه است؟', 'torantejarat' ); ?></summary><p><?php esc_html_e( 'شرایط واقعی ضمانت، موجودی و ارسال هنوز به پیش‌نمایش اضافه نشده است. این موارد باید پیش از خرید مشخص شوند.', 'torantejarat' ); ?></p></details>
				<?php else : ?>
					<details><summary><?php esc_html_e( 'شرایط ضمانت و ارسال چگونه است؟', 'torantejarat' ); ?></summary><p><?php esc_html_e( 'شرایط ضمانت و نحوه ارسال را پیش از خرید از پشتیبانی بپرسید تا با مدل انتخابی و شهر شما تطبیق داده شود.', 'torantejarat' ); ?></p></details>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</section></div>
	<?php
}

/**
 * Sticky mobile purchase bar — exact product.html copy; the button
 * triggers the native add-to-cart form via torantejarat-pdp.js.
 */
function torantejarat_mobile_purchase_bar() {
	if ( ! is_product() ) { return; }
	global $product;
	if ( ! $product instanceof \WC_Product ) { return; }
	?>
	<div class="torantejarat-mobile-purchase">
		<div><small><?php esc_html_e( 'قیمت', 'torantejarat' ); ?></small><?php echo wp_kses_post( torantejarat_card_price( $product ) ); ?></div>
		<button type="button" class="torantejarat-btn torantejarat-btn-primary" data-torantejarat-mobile-add><?php esc_html_e( 'افزودن به سبد', 'torantejarat' ); ?> <?php torantejarat_icon( 'cart' ); ?></button>
	</div>
	<?php
}

/**
 * Related heading in Persian — exact product.html copy.
 */
function torantejarat_related_heading( $translated, $text, $domain ) {
	if ( 'woocommerce' === $domain && 'Related products' === $text && is_product() ) {
		return __( 'این دستگاه‌ها را هم بررسی کنید', 'torantejarat' );
	}
	if ( 'woocommerce' === $domain && 'No products in the cart.' === $text ) {
		return __( 'سبد خرید شما خالی است.', 'torantejarat' );
	}
	if ( 'woocommerce' === $domain && is_cart() ) {
		$map = array(
			'Your cart is currently empty!'   => __( 'سبد خرید شما خالی است.', 'torantejarat' ),
			'Browse store'                    => __( 'مشاهده محصولات', 'torantejarat' ),
			'New in store'                    => __( 'تازه‌های فروشگاه', 'torantejarat' ),
			'You may be interested in.'       => __( 'شاید این‌ها هم لازم داشته باشید', 'torantejarat' ),
			'You may be interested in&hellip;' => __( 'شاید این‌ها هم لازم داشته باشید', 'torantejarat' ),
		);
		if ( isset( $map[ $text ] ) ) {
			return $map[ $text ];
		}
	}
	if ( 'woocommerce' === $domain && str_contains( (string) $text, 'Your personal data will be used to process your order' ) ) {
		return __( 'اطلاعات شما فقط برای پردازش سفارش و پشتیبانی استفاده می‌شود. جزئیات در [privacy_policy].', 'torantejarat' );
	}
	return $translated;
}

/**
 * No gateway configured yet: honest dead-end with a path forward.
 */
function torantejarat_no_payment_message() {
	$contact = get_page_by_path( 'contact' );
	$message = __( 'تسویه آنلاین هنوز فعال نشده است. برای نهایی کردن خرید، درخواست خود را ثبت کنید تا کارشناس با شما تماس بگیرد.', 'torantejarat' );
	if ( $contact ) {
		$message .= ' <a class="torantejarat-btn torantejarat-btn-primary" href="' . esc_url( get_permalink( $contact ) ) . '">' . esc_html__( 'ثبت درخواست خرید', 'torantejarat' ) . '</a>';
	}
	return $message;
}

/**
 * Persian price order: amount first, currency symbol after (e.g. ۱۲۵۰۰۰۰ تومان).
 */
function torantejarat_price_format( $format ) {
	return '%2$s&nbsp;%1$s';
}

/**
 * Persian digits for WooCommerce prices (storefront only).
 */
function torantejarat_price_digits( $formatted ) {
	if ( is_admin() ) { return $formatted; }
	return str_replace( array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), $formatted );
}

/**
 * Native product meta box: video URL + limitation text (no plugin).
 */
function torantejarat_product_meta_boxes() {
	add_meta_box( 'torantejarat-product-media', __( 'ویدیو محصول — بلکوکم', 'torantejarat' ), __NAMESPACE__ . '\\torantejarat_product_meta_box', 'product', 'side', 'default' );
	add_meta_box( 'torantejarat-product-faq', __( 'پرسش‌های اختصاصی — بلکوکم', 'torantejarat' ), __NAMESPACE__ . '\\torantejarat_product_faq_box', 'product', 'side', 'default' );
}

function torantejarat_product_meta_box( $post ) {
	wp_nonce_field( 'torantejarat_product_meta', 'torantejarat_product_meta_nonce' );
	$video      = get_post_meta( $post->ID, '_torantejarat_video', true );
	$video_id   = (int) get_post_meta( $post->ID, '_torantejarat_video_id', true );
	$preview    = $video_id > 0 ? wp_get_attachment_url( $video_id ) : $video;
	$is_file    = (bool) preg_match( '/\.(mp4|webm|ogg)(\?|#|$)/i', $preview );
	$is_aparat = (bool) preg_match( '#aparat\.com/(?:v/|video/video/embed/videohash/)([a-zA-Z0-9]+)#i', $preview );
	$is_youtube = (bool) preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([a-zA-Z0-9_-]{6,})#', $preview );
	$is_vimeo  = (bool) preg_match( '#vimeo\.com/(\d+)#', $preview );
	$type_label = $is_file ? __( 'فایل ویدیو', 'torantejarat' ) : ( $is_aparat ? __( 'آپارات', 'torantejarat' ) : ( $is_youtube ? __( 'یوتیوب', 'torantejarat' ) : ( $is_vimeo ? __( 'ویمیو', 'torantejarat' ) : __( 'لینک عمومی', 'torantejarat' ) ) ) );
	?>
	<p><label for="torantejarat-video"><strong><?php esc_html_e( 'آدرس ویدیو', 'torantejarat' ); ?></strong></label><br>
	<input type="url" id="torantejarat-video" name="torantejarat_video" value="<?php echo esc_attr( $video ); ?>" dir="ltr" style="width:100%" placeholder="https://…"></p>
	<input type="hidden" id="torantejarat-video-id" name="torantejarat_video_id" value="<?php echo esc_attr( $video_id > 0 ? $video_id : '' ); ?>">
	<p>
		<button type="button" class="button" id="torantejarat-video-select"><?php esc_html_e( 'انتخاب از کتابخانه', 'torantejarat' ); ?></button>
		<button type="button" class="button-link-delete button" id="torantejarat-video-remove"<?php echo $preview ? '' : ' style="display:none"'; ?>><?php esc_html_e( 'حذف', 'torantejarat' ); ?></button>
		<?php if ( $preview ) : ?><span class="description" style="margin-inline-start:8px"><?php echo esc_html( $type_label ); ?></span><?php endif; ?>
	</p>
	<div id="torantejarat-video-preview"<?php echo $preview ? '' : ' style="display:none"'; ?>>
		<?php if ( $is_file ) : ?>
			<video controls preload="none" src="<?php echo esc_url( $preview ); ?>" style="width:100%"></video>
		<?php elseif ( $is_aparat && preg_match( '#aparat\.com/(?:v/|video/video/embed/videohash/)([a-zA-Z0-9]+)#i', $preview, $m ) ) : ?>
			<iframe src="<?php echo esc_url( 'https://www.aparat.com/video/video/embed/videohash/' . $m[1] . '/vt/frame' ); ?>" width="100%" height="200" frameborder="0" allowfullscreen style="border-radius:8px"></iframe>
		<?php elseif ( $is_youtube && preg_match( '#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([a-zA-Z0-9_-]{6,})#', $preview, $m ) ) : ?>
			<iframe src="<?php echo esc_url( 'https://www.youtube.com/embed/' . $m[1] ); ?>" width="100%" height="200" frameborder="0" allowfullscreen style="border-radius:8px"></iframe>
		<?php elseif ( $is_vimeo && preg_match( '#vimeo\.com/(\d+)#', $preview, $m ) ) : ?>
			<iframe src="<?php echo esc_url( 'https://player.vimeo.com/video/' . $m[1] ); ?>" width="100%" height="200" frameborder="0" allowfullscreen style="border-radius:8px"></iframe>
		<?php elseif ( $preview ) : ?>
			<p class="description" style="overflow-wrap:anywhere"><?php echo esc_html( $preview ); ?></p>
		<?php endif; ?>
	</div>
	<p class="description"><?php esc_html_e( 'آپلود mp4 در کتابخانه، یا لینک مستقیم/یوتیوب/آپارات/ویمیو. خالی = حالت «هنوز بارگذاری نشده».', 'torantejarat' ); ?></p>
	<?php
}

/**
 * Standalone FAQ meta box: one question per line as "question ||| answer".
 */
function torantejarat_product_faq_box( $post ) {
	wp_nonce_field( 'torantejarat_product_meta', 'torantejarat_product_meta_nonce' );
	$faq = get_post_meta( $post->ID, '_torantejarat_faq', true );
	?>
	<p><label for="torantejarat-faq"><strong><?php esc_html_e( 'پرسش‌های اختصاصی این محصول', 'torantejarat' ); ?></strong></label><br>
	<textarea id="torantejarat-faq" name="torantejarat_faq" rows="6" dir="rtl" style="width:100%" placeholder="<?php esc_attr_e( 'گارانتی این مدل چقدر است؟ ||| دو سال گارانتی معتبر دارد.', 'torantejarat' ); ?>"><?php echo esc_textarea( $faq ); ?></textarea></p>
	<p class="description"><?php esc_html_e( 'هر خط یک پرسش با این قالب: سؤال ||| پاسخ. خالی = سؤالات عمومی نمایش داده می‌شود.', 'torantejarat' ); ?></p>
	<?php
}

function torantejarat_save_product_meta( $post_id, $post, $update ) {
	if ( ! isset( $_POST['torantejarat_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['torantejarat_product_meta_nonce'] ), 'torantejarat_product_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	if ( isset( $_POST['torantejarat_video'] ) ) {
		update_post_meta( $post_id, '_torantejarat_video', esc_url_raw( wp_unslash( $_POST['torantejarat_video'] ) ) );
	}
	if ( isset( $_POST['torantejarat_video_id'] ) ) {
		update_post_meta( $post_id, '_torantejarat_video_id', absint( $_POST['torantejarat_video_id'] ) );
	}
	if ( isset( $_POST['torantejarat_faq'] ) ) {
		update_post_meta( $post_id, '_torantejarat_faq', sanitize_textarea_field( wp_unslash( $_POST['torantejarat_faq'] ) ) );
	}
}

function torantejarat_admin_product_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || 'product' !== $screen->post_type ) { return; }
	wp_enqueue_media();
	wp_enqueue_script( 'torantejarat-admin-product', get_template_directory_uri() . '/assets/js/admin-product.js', array( 'jquery' ), torantejarat_asset_version( 'assets/js/admin-product.js' ), true );
}

/**
 * Related section with latest-products fallback (never an empty heading).
 */
function torantejarat_related_section() {
	$related_ids = array_values( array_filter( array_map( 'absint', wc_get_related_products( get_the_ID(), 8 ) ) ) );
	$query       = array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 6, 'exclude' => array( get_the_ID() ), 'orderby' => 'date', 'order' => 'DESC' );
	if ( $related_ids ) {
		$query['include'] = $related_ids;
		unset( $query['orderby'], $query['order'] );
	}
	$items = array_values( array_filter( wc_get_products( $query ), static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
	if ( ! $items ) { return; }
	get_template_part(
		'template-parts/product-slider',
		null,
		array(
			'products' => $items,
			'eyebrow'  => __( 'انتخاب آگاهانه', 'torantejarat' ),
			'title'    => __( 'این دستگاه‌ها را هم بررسی کنید', 'torantejarat' ),
		)
	);
}

/**
 * In-stock alternatives for out-of-stock products (native related data only).
 */
function torantejarat_product_alternatives() {
	global $product;
	if ( ! $product instanceof \WC_Product || $product->is_in_stock() ) { return; }
	$related = array_values( array_filter( array_map( 'absint', wc_get_related_products( $product->get_id(), 8 ) ) ) );
	$query   = array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 4, 'exclude' => array( $product->get_id() ), 'stock_status' => 'instock', 'orderby' => 'date', 'order' => 'DESC' );
	if ( $related ) {
		$query['include'] = $related;
		unset( $query['orderby'], $query['order'] );
	}
	$items = array_values( array_filter( wc_get_products( $query ), static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() && $item->is_in_stock() ) );
	if ( ! $items ) { return; }
	global $post;
	$saved_product = $product;
	$saved_post    = $post;
	echo '<section class="torantejarat-section"><div class="torantejarat-container">';
	echo '<div class="torantejarat-section-heading"><div><span class="torantejarat-eyebrow">' . esc_html__( 'ناموجود است؟', 'torantejarat' ) . '</span><h2>' . esc_html__( 'این مدل‌های آماده را ببینید', 'torantejarat' ) . '</h2></div></div>';
	wc_setup_loop( array( 'name' => 'torantejarat-alternatives', 'columns' => 4, 'total' => count( $items ), 'is_paginated' => false ) );
	woocommerce_product_loop_start();
	foreach ( $items as $product ) {
		$post = get_post( $product->get_id() );
		setup_postdata( $post );
		wc_get_template_part( 'content', 'product' );
	}
	woocommerce_product_loop_end();
	wc_reset_loop();
	wp_reset_postdata();
	$product = $saved_product;
	$post    = $saved_post;
	echo '</div></section>';
}

/**
 * Recently-viewed slot — filled client-side from localStorage (no backend store).
 */
function torantejarat_recently_viewed_slot() {
	if ( is_product() ) {
		global $product;
		if ( ! $product instanceof \WC_Product ) { return; }
	}
	echo '<section class="torantejarat-section torantejarat-recent-section" hidden><div class="torantejarat-container">';
	echo '<div class="torantejarat-section-heading"><div><span class="torantejarat-eyebrow">' . esc_html__( 'ادامه از جایی که بودید', 'torantejarat' ) . '</span><h2>' . esc_html__( 'بازدیدهای اخیر شما', 'torantejarat' ) . '</h2></div></div>';
	echo '<div class="torantejarat-recent-grid"></div>';
	echo '</div></section>';
}

/**
 * Compare selection logic shared by the compare page and its AJAX fragment.
 * Input: raw (already sanitized) keys compare/p1/p2/p3/add/diff.
 * Returns: ids (deduped table IDs), slots (per-dropdown choices),
 * items, maps, labels, only_diff.
 */
function torantejarat_compare_data( array $raw ) {
	$catalog = torantejarat_woocommerce_available()
		? wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 50, 'orderby' => 'name', 'order' => 'ASC' ) )
		: array();
	$catalog = array_values( array_filter( $catalog, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
	$by_id   = array();
	foreach ( $catalog as $item ) {
		$by_id[ $item->get_id() ] = $item;
	}
	$wanted = array();
	if ( ! empty( $raw['compare'] ) ) {
		$wanted = array_merge( $wanted, explode( ',', (string) $raw['compare'] ) );
	}
	foreach ( array( 'p1', 'p2', 'p3' ) as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$wanted[] = (string) $raw[ $key ];
		}
	}
	if ( ! empty( $raw['add'] ) ) {
		array_unshift( $wanted, (string) $raw['add'] );
	}
	$ids = array();
	foreach ( $wanted as $raw_id ) {
		$id = absint( $raw_id );
		if ( $id > 0 && isset( $by_id[ $id ] ) && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
		if ( count( $ids ) >= 3 ) { break; }
	}
	/* Per-slot choices drive the dropdowns (duplicates preserved); leftover
	   table IDs fill empty slots so ?add= / ?compare= also preselect. */
	$slots = array( 0, 0, 0 );
	foreach ( array( 'p1', 'p2', 'p3' ) as $slot_index => $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$slot_id = absint( (string) $raw[ $key ] );
			if ( $slot_id > 0 && isset( $by_id[ $slot_id ] ) ) {
				$slots[ $slot_index ] = $slot_id;
			}
		}
	}
	foreach ( $ids as $id ) {
		if ( in_array( $id, $slots, true ) ) {
			continue;
		}
		foreach ( $slots as $slot_index => $slot_value ) {
			if ( 0 === $slot_value ) {
				$slots[ $slot_index ] = $id;
				break;
			}
		}
	}
	$only_diff = isset( $raw['diff'] ) && '1' === (string) $raw['diff'];
	$items = array();
	foreach ( $ids as $id ) {
		$items[] = $by_id[ $id ];
	}
	$maps   = array();
	$labels = array();
	foreach ( $items as $item ) {
		$map = array();
		foreach ( torantejarat_product_attribute_items( $item, 10 ) as $attr ) {
			$map[ $attr['label'] ] = $attr['value'];
			if ( ! in_array( $attr['label'], $labels, true ) && count( $labels ) < 8 ) {
				$labels[] = $attr['label'];
			}
		}
		$maps[] = $map;
	}
	if ( $only_diff && count( $items ) > 1 ) {
		$labels = array_values(
			array_filter(
				$labels,
				static function ( $label ) use ( $maps ) {
					$seen = array();
					foreach ( $maps as $map ) {
						$seen[] = $map[ $label ] ?? null;
					}
					return count( array_unique( $seen ) ) > 1;
				}
			)
		);
	}
	return array(
		'catalog'   => $catalog,
		'ids'       => $ids,
		'slots'     => $slots,
		'items'     => $items,
		'maps'      => $maps,
		'labels'    => $labels,
		'only_diff' => $only_diff,
	);
}

/**
 * Empty search (?s=) matches everything by default — redirect to the shop
 * instead of a meaningless "all results" page.
 */
function torantejarat_empty_search_redirect() {
	if ( is_admin() || ! is_search() ) {
		return;
	}
	if ( '' === trim( (string) get_search_query( false ) ) ) {
		$shop = torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0
			? wc_get_page_permalink( 'shop' )
			: home_url( '/' );
		wp_safe_redirect( esc_url_raw( $shop ), 302 );
		exit;
	}
}

/**
 * Legacy shop search (?s= on shop/category) bounces to the dedicated
 * product search page (/search/?q=), which owns its URL and breadcrumb.
 */
function torantejarat_shop_search_redirect() {
	if ( is_admin() || ! torantejarat_woocommerce_available() ) {
		return;
	}
	if ( ! isset( $_GET['s'] ) || is_array( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only redirect.
		return;
	}
	$q = trim( (string) wp_unslash( $_GET['s'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $q ) {
		return;
	}
	if ( is_shop() || is_product_taxonomy() ) {
		wp_safe_redirect( esc_url_raw( torantejarat_product_search_url( $q ) ), 302 );
		exit;
	}
}

/**
 * Live search core — shared by the REST endpoint and the legacy admin-ajax
 * action. Fast WP_Query with IDs only (no full product hydration),
 * SKU fast-path, Persian/Latin digit variants, versioned transient.
 */
function torantejarat_search_normalize( $value ) {
	$value = wp_strip_all_tags( (string) $value );
	$value = strtr( $value, array( 'ي' => 'ی', 'ك' => 'ک' ) );
	$value = trim( (string) preg_replace( '/\s+/u', ' ', $value ) );
	return mb_substr( $value, 0, 60 );
}

function torantejarat_search_variants( $q ) {
	$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$q  = str_replace( $fa, $en, $q );
	return array_values( array_unique( array( $q, str_replace( $en, $fa, $q ) ) ) );
}

function torantejarat_search_bust( $post_id = 0 ) {
	/* save_post_product also fires for autosaves/revisions — those must not
	   churn the index version. Term hooks pass a term_id, skip the guards. */
	if ( 'save_post_product' === current_action() ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( $post_id && ( wp_is_post_revision( $post_id ) || 'auto-draft' === get_post_status( $post_id ) ) ) {
			return;
		}
	}
	update_option( 'torantejarat_search_ver', (int) get_option( 'torantejarat_search_ver', 1 ) + 1, false );
}

/**
 * Single shape for "no results" so the REST handler, the data builder
 * and the AJAX wrapper can never drift apart.
 */
function torantejarat_search_empty_response( $q = '' ) {
	return array( 'q' => $q, 'total' => 0, 'products' => array(), 'cats' => array(), 'categories' => array() );
}

function torantejarat_search_rest_route() {
	register_rest_route(
		'torantejarat/v1',
		'/search',
		array(
			'methods'             => 'GET',
			'callback'            => __NAMESPACE__ . '\\torantejarat_search_rest_handle',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => __NAMESPACE__ . '\\torantejarat_search_normalize',
				),
			),
		)
	);
	register_rest_route(
		'torantejarat/v1',
		'/catalog',
		array(
			'methods'             => 'GET',
			'callback'            => __NAMESPACE__ . '\\torantejarat_search_catalog_handle',
			'permission_callback' => '__return_true',
		)
	);
}

/**
 * Compact whole-catalog payload for instant client-side search.
 * One small request per page load; the modal then answers keystrokes
 * with zero network. Versioned transient, busted with the search index.
 */
function torantejarat_search_catalog_handle() {
	$key    = 'torantejarat_catalog_' . (int) get_option( 'torantejarat_search_ver', 1 );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		$response = rest_ensure_response( $cached );
		$response->header( 'Cache-Control', 'public, max-age=3600' );
		return $response;
	}
	$items = array();
	if ( torantejarat_woocommerce_available() ) {
		$ids = wc_get_products(
			array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'limit'      => 300,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'return'     => 'ids',
			)
		);
		foreach ( array_slice( (array) $ids, 0, 300 ) as $id ) {
			$p = wc_get_product( absint( $id ) );
			if ( ! $p instanceof \WC_Product || ! $p->is_visible() ) {
				continue;
			}
			$terms = get_the_terms( $p->get_id(), 'product_cat' );
			$cat   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
			$img   = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
			$price = $p->get_price();
			$items[] = array(
				'url'   => get_permalink( $p->get_id() ),
				'name'  => html_entity_decode( $p->get_name(), ENT_QUOTES, 'UTF-8' ),
				'sku'   => (string) $p->get_sku(),
				'cat'   => $cat,
				'image' => $img ? $img : '',
				'price' => ( '' !== $price && null !== $price ) ? trim( torantejarat_fa_number( $price ) . ' ' . __( 'تومان', 'torantejarat' ) ) : '',
			);
		}
	}
	$cats  = array();
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC' ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$url = get_term_link( $t );
			if ( ! is_wp_error( $url ) ) {
				$cats[] = array( 'url' => $url, 'name' => $t->name );
			}
		}
	}
	$data = array(
		'ver'      => (int) get_option( 'torantejarat_search_ver', 1 ),
		'products' => $items,
		'cats'     => $cats,
	);
	set_transient( $key, $data, 12 * HOUR_IN_SECONDS );
	$response = rest_ensure_response( $data );
	$response->header( 'Cache-Control', 'public, max-age=3600' );
	return $response;
}

function torantejarat_search_rest_handle( \WP_REST_Request $request ) {
	$q = torantejarat_search_normalize( (string) $request['q'] );
	if ( mb_strlen( $q ) < 2 || ! torantejarat_woocommerce_available() ) {
		return rest_ensure_response( torantejarat_search_empty_response( $q ) );
	}
	$data     = torantejarat_search_data( $q );
	$response = rest_ensure_response( $data );
	$response->header( 'Cache-Control', 'public, max-age=60' );
	return $response;
}

function torantejarat_search_data( $q ) {
	$q = torantejarat_search_normalize( $q );
	if ( mb_strlen( $q ) < 2 ) {
		return torantejarat_search_empty_response( $q );
	}
	$key    = 'torantejarat_search_' . (int) get_option( 'torantejarat_search_ver', 1 ) . '_' . md5( mb_strtolower( $q ) );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$ids   = array();
	$total = 0;
	if ( torantejarat_woocommerce_available() ) {
		if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
			$sku_id = (int) wc_get_product_id_by_sku( $q );
			if ( $sku_id > 0 ) {
				$ids[] = $sku_id;
			}
		}
		foreach ( torantejarat_search_variants( $q ) as $term ) {
			$query = new \WP_Query(
				array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					's'                   => $term,
					'posts_per_page'      => 6,
					'fields'              => 'ids',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => false,
					/* IDs only — hydration happens per visible hit via
					   wc_get_product, so skip WP's own cache priming. */
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- Bounded to 6 IDs for live search.
						array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => array( 'exclude-from-search' ),
							'operator' => 'NOT IN',
						),
					),
				)
			);
			foreach ( (array) $query->posts as $found_id ) {
				$ids[] = (int) $found_id;
			}
			$total = max( $total, (int) $query->found_posts );
		}
	}
	$ids      = array_slice( array_unique( array_filter( $ids ) ), 0, 6 );
	$products = array();
	foreach ( $ids as $id ) {
		$p = wc_get_product( absint( $id ) );
		if ( ! $p instanceof \WC_Product || ! $p->is_visible() ) {
			continue;
		}
		$img   = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
		$price = $p->get_price();
		$products[] = array(
			'url'   => get_permalink( $p->get_id() ),
			'name'  => html_entity_decode( $p->get_name(), ENT_QUOTES, 'UTF-8' ),
			'image' => $img ? $img : '',
			'price' => ( '' !== $price && null !== $price ) ? trim( torantejarat_fa_number( $price ) . ' ' . __( 'تومان', 'torantejarat' ) ) : '',
		);
	}
	$cats  = array();
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'name__like' => $q,
			'hide_empty' => true,
			'number'     => 4,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( ! is_wp_error( $terms ) ) {
		foreach ( array_slice( $terms, 0, 3 ) as $t ) {
			$url = get_term_link( $t );
			if ( ! is_wp_error( $url ) ) {
				$cats[] = array( 'url' => $url, 'name' => $t->name );
			}
		}
	}
	$data = array(
		'q'          => $q,
		'total'      => max( $total, count( $products ) ),
		'products'   => $products,
		'cats'       => $cats,
		'categories' => $cats,
	);
	set_transient( $key, $data, 10 * MINUTE_IN_SECONDS );
	return $data;
}

/**
 * Live search endpoint (legacy admin-ajax, nonce-verified).
 * Thin wrapper over torantejarat_search_data so old cached pages keep working.
 */
function torantejarat_live_search() {
	check_ajax_referer( 'torantejarat-search', 'nonce' );
	$q = isset( $_GET['q'] ) ? torantejarat_search_normalize( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified above.
	wp_send_json_success( torantejarat_search_data( $q ) );
}
/**
 * Sanitize raw compare input (GET or AJAX) to the torantejarat_compare_data shape.
 */
function torantejarat_compare_raw_input( array $input ) {
	$raw = array();
	foreach ( array( 'compare', 'p1', 'p2', 'p3', 'add', 'diff' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$raw[ $key ] = sanitize_text_field( wp_unslash( $input[ $key ] ) );
		}
	}
	return $raw;
}

/**
 * AJAX fragment: refreshed compare results without a full page load.
 * Read-only public catalog data; nonce verified.
 */
function torantejarat_compare_fragment() {
	check_ajax_referer( 'torantejarat-compare', 'nonce' );
	$data = torantejarat_compare_data( torantejarat_compare_raw_input( $_GET ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified above.
	ob_start();
	get_template_part(
		'template-parts/compare-results',
		null,
		array(
			'items'  => $data['items'],
			'maps'   => $data['maps'],
			'labels' => $data['labels'],
		)
	);
	wp_send_json_success(
		array(
			'html'  => ob_get_clean(),
			'count' => count( $data['items'] ),
		)
	);
}
/**
 * Checkout redesign: minimal Iranian billing form (7 fields only).
 * Removes company / country (fixed to Iran) / address line 2 / email.
 */
function torantejarat_ir_states( $states ) {
	$states['IR'] = array(
		'EAZ' => __( 'آذربایجان شرقی', 'torantejarat' ),
		'WAZ' => __( 'آذربایجان غربی', 'torantejarat' ),
		'ADL' => __( 'اردبیل', 'torantejarat' ),
		'ESF' => __( 'اصفهان', 'torantejarat' ),
		'ABZ' => __( 'البرز', 'torantejarat' ),
		'ILM' => __( 'ایلام', 'torantejarat' ),
		'BHR' => __( 'بوشهر', 'torantejarat' ),
		'THR' => __( 'تهران', 'torantejarat' ),
		'CHB' => __( 'چهارمحال و بختیاری', 'torantejarat' ),
		'SKH' => __( 'خراسان جنوبی', 'torantejarat' ),
		'RKH' => __( 'خراسان رضوی', 'torantejarat' ),
		'NKH' => __( 'خراسان شمالی', 'torantejarat' ),
		'KHZ' => __( 'خوزستان', 'torantejarat' ),
		'ZJN' => __( 'زنجان', 'torantejarat' ),
		'SMN' => __( 'سمنان', 'torantejarat' ),
		'SBN' => __( 'سیستان و بلوچستان', 'torantejarat' ),
		'FRS' => __( 'فارس', 'torantejarat' ),
		'GZN' => __( 'قزوین', 'torantejarat' ),
		'QHM' => __( 'قم', 'torantejarat' ),
		'KRD' => __( 'کردستان', 'torantejarat' ),
		'KRN' => __( 'کرمان', 'torantejarat' ),
		'KRH' => __( 'کرمانشاه', 'torantejarat' ),
		'KBD' => __( 'کهگیلویه و بویراحمد', 'torantejarat' ),
		'GLS' => __( 'گلستان', 'torantejarat' ),
		'GIL' => __( 'گیلان', 'torantejarat' ),
		'LRS' => __( 'لرستان', 'torantejarat' ),
		'MZN' => __( 'مازندران', 'torantejarat' ),
		'MKZ' => __( 'مرکزی', 'torantejarat' ),
		'HRZ' => __( 'هرمزگان', 'torantejarat' ),
		'HDN' => __( 'همدان', 'torantejarat' ),
		'YZD' => __( 'یزد', 'torantejarat' ),
	);
	return $states;
}

function torantejarat_checkout_fields( $fields ) {
	if ( ! isset( $fields['billing'] ) ) {
		return $fields;
	}
	$keep = array(
		'billing_first_name' => array( 'label' => __( 'نام', 'torantejarat' ), 'placeholder' => '', 'priority' => 10, 'autocomplete' => 'given-name', 'class' => array( 'form-row-first' ) ),
		'billing_last_name'  => array( 'label' => __( 'نام خانوادگی', 'torantejarat' ), 'placeholder' => '', 'priority' => 20, 'autocomplete' => 'family-name', 'class' => array( 'form-row-last' ) ),
		'billing_state'      => array( 'label' => __( 'استان', 'torantejarat' ), 'placeholder' => '', 'priority' => 30, 'autocomplete' => 'address-level1', 'class' => array( 'form-row-first' ) ),
		'billing_city'       => array( 'label' => __( 'شهر', 'torantejarat' ), 'placeholder' => '', 'priority' => 40, 'autocomplete' => 'address-level2', 'class' => array( 'form-row-last' ) ),
		'billing_address_1'  => array( 'label' => __( 'آدرس خیابان', 'torantejarat' ), 'placeholder' => __( 'نام خیابان، پلاک و واحد', 'torantejarat' ), 'priority' => 50, 'autocomplete' => 'street-address', 'class' => array( 'form-row-wide' ) ),
		'billing_postcode'   => array( 'label' => __( 'کد پستی', 'torantejarat' ), 'placeholder' => __( 'کد پستی ۱۰ رقمی', 'torantejarat' ), 'priority' => 60, 'autocomplete' => 'postal-code', 'class' => array( 'form-row-first' ), 'custom_attributes' => array( 'dir' => 'ltr', 'inputmode' => 'numeric' ) ),
		'billing_phone'      => array( 'label' => __( 'تلفن همراه', 'torantejarat' ), 'placeholder' => '09123456789', 'priority' => 70, 'autocomplete' => 'tel', 'class' => array( 'form-row-last' ), 'custom_attributes' => array( 'dir' => 'ltr' ) ),
		// WooCommerce uses this field for order emails and checkout account creation.
		'billing_email'      => array( 'label' => __( 'ایمیل', 'torantejarat' ), 'placeholder' => 'name@example.com', 'priority' => 80, 'autocomplete' => 'email', 'class' => array( 'form-row-wide' ), 'custom_attributes' => array( 'dir' => 'ltr', 'inputmode' => 'email' ) ),
	);
	$billing = array();
	foreach ( $keep as $key => $overrides ) {
		if ( ! isset( $fields['billing'][ $key ] ) ) {
			continue;
		}
		$billing[ $key ] = array_merge( $fields['billing'][ $key ], $overrides, array( 'required' => true ) );
	}
	$fields['billing'] = $billing;
	return $fields;
}

/**
 * Country field is hidden (fixed to Iran): enforce it on posted data so
 * state/postcode validate against IR instead of the store base country.
 */
function torantejarat_checkout_force_country( $data ) {
	$data['billing_country']  = 'IR';
	$data['shipping_country'] = 'IR';
	return $data;
}

/**
 * Strict Iranian mobile validation at checkout (accepts Persian digits).
 */
function torantejarat_checkout_phone_check() {
	if ( empty( $_POST['billing_phone'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Woo validates its own nonce.
		return;
	}
	$phone = torantejarat_request_phone( wp_unslash( $_POST['billing_phone'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( '' === $phone ) {
		wc_add_notice( __( 'شماره موبایل معتبر نیست؛ مثل 09123456789 وارد کنید.', 'torantejarat' ), 'error' );
	}
}

function torantejarat_loop_product_title() {
	$level = in_array( wc_get_loop_prop( 'name' ), array( 'torantejarat-home', 'related', 'up-sells', 'cross-sells' ), true ) ? 3 : 2;
	$class = apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' );
	printf( '<h%1$d class="%2$s">%3$s</h%1$d>', $level, esc_attr( $class ), esc_html( get_the_title() ) );
}

/**
 * Structured recorder source for recently-viewed (exact product name,
 * image and price) — meta scraping (og:title/document.title) is only
 * the fallback and carries the site-name suffix.
 */
function torantejarat_recent_source() {
	if ( ! is_product() ) {
		return;
	}
	global $product;
	if ( ! $product instanceof \WC_Product ) {
		return;
	}
	$image = $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'torantejarat-card' ) : '';
	$price = $product->get_price();
	$data  = array(
		'url'   => get_permalink( $product->get_id() ),
		'title' => $product->get_name(),
		'image' => $image ? $image : '',
		'price' => '' !== $price && null !== $price ? trim( torantejarat_fa_number( $price ) . ' ' . __( 'تومان', 'torantejarat' ) ) : '',
	);
	echo '<script type="application/json" id="torantejarat-recent-source">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * Postal tracking per order: admin field + account card + emails + thankyou.
 * Meta keys: _torantejarat_tracking (code), _torantejarat_carrier (post|chapar|tipax).
 * Single source of truth — no parallel tracking system.
 */

/**
 * Carrier registry: slug => array( label, url ). Filterable.
 */
function torantejarat_tracking_carriers() {
	return apply_filters(
		'torantejarat_tracking_carriers',
		array(
			'post'   => array( 'label' => __( 'پست ایران', 'torantejarat' ), 'url' => 'https://tracking.post.ir/' ),
			'chapar' => array( 'label' => __( 'چاپار', 'torantejarat' ), 'url' => 'https://www.chaparnet.com/track' ),
			'tipax'  => array( 'label' => __( 'تیپاکس', 'torantejarat' ), 'url' => 'https://tipaxco.com/tracking' ),
		)
	);
}

function torantejarat_tracking_carrier_label( $carrier ) {
	$carriers = torantejarat_tracking_carriers();
	return isset( $carriers[ $carrier ] ) ? $carriers[ $carrier ]['label'] : '';
}

function torantejarat_tracking_carrier_url( $carrier ) {
	$carriers = torantejarat_tracking_carriers();
	return isset( $carriers[ $carrier ] ) ? $carriers[ $carrier ]['url'] : '';
}

function torantejarat_tracking_field( $order = null ) {
	if ( ! $order instanceof \WC_Order ) {
		$order = $order ? wc_get_order( $order ) : null;
	}
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	$value   = $order->get_meta( '_torantejarat_tracking' );
	$carrier = $order->get_meta( '_torantejarat_carrier' );
	?>
	<p class="form-field form-field-wide">
		<label for="torantejarat_carrier"><?php esc_html_e( 'شرکت حمل‌ونقل', 'torantejarat' ); ?></label>
		<select id="torantejarat_carrier" name="torantejarat_carrier" style="width:100%">
			<option value=""><?php esc_html_e( '— انتخاب کنید —', 'torantejarat' ); ?></option>
			<?php foreach ( torantejarat_tracking_carriers() as $key => $info ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $carrier, $key ); ?>><?php echo esc_html( $info['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class="form-field form-field-wide">
		<label for="torantejarat_tracking"><?php esc_html_e( 'کد مرسوله پستی', 'torantejarat' ); ?></label>
		<input type="text" id="torantejarat_tracking" name="torantejarat_tracking" value="<?php echo esc_attr( $value ); ?>" dir="ltr" style="width:100%">
		<span class="description"><?php esc_html_e( 'خالی = هنوز ارسال نشده. در حساب کاربری، صفحه تشکر و ایمیل مشتری نمایش داده می‌شود.', 'torantejarat' ); ?></span>
	</p>
	<?php
}

function torantejarat_tracking_save( $order_id ) {
	if ( ! current_user_can( 'edit_shop_order', $order_id ) ) {
		return;
	}
	/* HPOS: orders live in custom tables — CRUD only, never postmeta. */
	$torantejarat_order = wc_get_order( $order_id );
	if ( ! $torantejarat_order instanceof \WC_Order ) {
		return;
	}
	if ( isset( $_POST['torantejarat_carrier'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Woo verifies its own order nonce.
		$carrier = sanitize_key( wp_unslash( $_POST['torantejarat_carrier'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$carriers = array_keys( torantejarat_tracking_carriers() );
		if ( '' === $carrier || in_array( $carrier, $carriers, true ) ) {
			if ( '' === $carrier ) {
				$torantejarat_order->delete_meta_data( '_torantejarat_carrier' );
			} else {
				$torantejarat_order->update_meta_data( '_torantejarat_carrier', $carrier );
			}
		}
	}
	if ( isset( $_POST['torantejarat_tracking'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Woo verifies its own order nonce.
		$code = sanitize_text_field( wp_unslash( $_POST['torantejarat_tracking'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' === $code ) {
			$torantejarat_order->delete_meta_data( '_torantejarat_tracking' );
		} else {
			$torantejarat_order->update_meta_data( '_torantejarat_tracking', $code );
		}
	}
	$torantejarat_order->save();
}

function torantejarat_tracking_account( $order ) {
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	$code = $order->get_meta( '_torantejarat_tracking' );
	if ( '' === $code ) {
		return;
	}
	$carrier       = $order->get_meta( '_torantejarat_carrier' );
	$carrier_label = torantejarat_tracking_carrier_label( $carrier );
	$carrier_url   = torantejarat_tracking_carrier_url( $carrier );
	?>
	<section class="torantejarat-tracking-card" aria-label="<?php esc_attr_e( 'پیگیری مرسوله', 'torantejarat' ); ?>">
		<div class="torantejarat-tracking-card__head">
			<span class="torantejarat-tracking-card__icon" aria-hidden="true"><?php torantejarat_icon( 'cart' ); ?></span>
			<div>
				<strong><?php esc_html_e( 'پیگیری مرسوله شما', 'torantejarat' ); ?></strong>
				<span><?php esc_html_e( 'وضعیت ارسال بسته خود را از سامانه شرکت حمل‌ونقل ببینید.', 'torantejarat' ); ?></span>
			</div>
			<?php if ( '' !== $carrier_label ) : ?><b class="torantejarat-tracking-card__chip"><?php echo esc_html( $carrier_label ); ?></b><?php endif; ?>
		</div>
		<div class="torantejarat-tracking-card__code">
			<div>
				<span><?php esc_html_e( 'کد رهگیری', 'torantejarat' ); ?></span>
				<strong id="torantejarat-tracking-code" dir="ltr"><?php echo esc_html( $code ); ?></strong>
			</div>
			<button type="button" class="torantejarat-btn torantejarat-btn-outline torantejarat-copy-btn" data-copy-target="torantejarat-tracking-code"><span data-copy-label><?php esc_html_e( 'کپی', 'torantejarat' ); ?></span></button>
		</div>
		<?php if ( '' !== $carrier_url ) : ?>
			<a class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block" href="<?php echo esc_url( $carrier_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'پیگیری مرسوله', 'torantejarat' ); ?></a>
		<?php endif; ?>
	</section>
	<?php
}

function torantejarat_tracking_email( $fields, $sent_to_admin, $order ) {
	if ( $sent_to_admin || ! $order instanceof \WC_Order ) {
		return $fields;
	}
	$code = $order->get_meta( '_torantejarat_tracking' );
	if ( '' !== $code ) {
		$carrier_label = torantejarat_tracking_carrier_label( $order->get_meta( '_torantejarat_carrier' ) );
		$fields['torantejarat_tracking'] = array(
			'label' => __( 'کد مرسوله پستی', 'torantejarat' ),
			'value' => '' !== $carrier_label ? $code . ' (' . $carrier_label . ')' : $code,
		);
	}
	return $fields;
}

/**
 * Best sellers by real completed sales. Only products with proven demand
 * (total_sales threshold) qualify — never hand-picked, never faked.
 *
 * @param int $limit Max items.
 * @return \WC_Product[]
 */
function torantejarat_bestsellers( $limit = 8 ) {
	if ( ! torantejarat_woocommerce_available() ) {
		return array();
	}
	$candidates = wc_get_products(
		array(
			'status'     => 'publish',
			'visibility' => 'catalog',
			'limit'      => max( 1, (int) $limit ) * 2,
			'orderby'    => 'meta_value_num',
			'meta_key'   => 'total_sales',
			'order'      => 'DESC',
		)
	);
	$items = array();
	foreach ( $candidates as $item ) {
		if ( ! $item instanceof \WC_Product || ! $item->is_visible() ) {
			continue;
		}
		if ( (int) $item->get_total_sales() < 5 ) {
			continue;
		}
		$items[] = $item;
		if ( count( $items ) >= (int) $limit ) {
			break;
		}
	}
	return $items;
}

/**
 * Real deals only: on-sale products with a genuine price gap,
 * sorted by discount percent (highest first).
 *
 * @param int $limit Max items.
 * @return array List of array( 'product' => WC_Product, 'percent' => int ).
 */
function torantejarat_deals( $limit = 4 ) {
	if ( ! torantejarat_woocommerce_available() ) {
		return array();
	}
	$candidates = wc_get_products(
		array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 50, 'orderby' => 'date', 'order' => 'DESC' )
	);
	$deals = array();
	foreach ( $candidates as $item ) {
		if ( ! $item instanceof \WC_Product || ! $item->is_visible() || ! $item->is_on_sale() ) {
			continue;
		}
		$regular = (float) $item->get_regular_price();
		$price   = (float) $item->get_price();
		if ( $regular <= 0 || $price >= $regular ) {
			continue;
		}
		$deals[] = array(
			'product' => $item,
			'percent' => (int) round( ( $regular - $price ) / $regular * 100 ),
		);
	}
	usort( $deals, static fn( $a, $b ) => $b['percent'] <=> $a['percent'] );
	return array_slice( $deals, 0, max( 1, (int) $limit ) );
}

/**
 * Order pipeline: pending → processing → shipped (custom) → completed.
 * Timeline in the customer account reads status + tracking code; no fake dates.
 */
function torantejarat_register_shipped_status() {
	register_post_status(
		'wc-shipped',
		array(
			'label'                     => __( 'ارسال شد', 'torantejarat' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders. */
			'label_count'               => _n_noop( 'ارسال شد (%s)', 'ارسال شد (%s)', 'torantejarat' ),
		)
	);
}

function torantejarat_shipped_status_list( $statuses ) {
	$ordered = array();
	foreach ( $statuses as $key => $label ) {
		$ordered[ $key ] = $label;
		if ( 'wc-processing' === $key ) {
			$ordered['wc-shipped'] = __( 'ارسال شد', 'torantejarat' );
		}
	}
	return $ordered;
}

function torantejarat_order_timeline( $order ) {
	if ( ! $order instanceof \WC_Order ) {
		$order = wc_get_order( $order );
	}
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	$status = $order->get_status();
	if ( in_array( $status, array( 'cancelled', 'refunded', 'failed' ), true ) ) {
		$messages = array(
			'cancelled' => __( 'این سفارش لغو شده است. برای ثبت مجدد یا پیگیری با ما در تماس باشید.', 'torantejarat' ),
			'refunded'  => __( 'مبلغ این سفارش بازگردانده شده است.', 'torantejarat' ),
			'failed'    => __( 'پرداخت این سفارش ناموفق بود. لطفاً دوباره تلاش کنید.', 'torantejarat' ),
		);
		echo '<div class="torantejarat-form-notice is-error" role="alert"><p>' . esc_html( $messages[ $status ] ) . '</p></div>';
		return;
	}
	$stage = 0;
	if ( in_array( $status, array( 'processing', 'shipped', 'completed' ), true ) ) {
		$stage = 1;
	}
	if ( 'shipped' === $status || 'completed' === $status ) {
		$stage = 2;
	}
	if ( 'completed' === $status ) {
		$stage = 3;
	}
	$tracking = $order->get_meta( '_torantejarat_tracking' );
	$created  = $order->get_date_created();
	$done     = $order->get_date_completed();
	$steps    = array(
		array(
			'title' => __( 'ثبت سفارش', 'torantejarat' ),
			'desc'  => $created
				? sprintf( __( 'سفارش %s ثبت شد.', 'torantejarat' ), torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) )
				: __( 'سفارش ثبت شد.', 'torantejarat' ),
		),
		array(
			'title' => __( 'تأیید و آماده‌سازی', 'torantejarat' ),
			'desc'  => __( 'سفارش تأیید و برای ارسال آماده می‌شود.', 'torantejarat' ),
		),
		array(
			'title' => __( 'ارسال شد', 'torantejarat' ),
			'desc'  => '' !== $tracking
				? sprintf( __( 'کد مرسوله: %s', 'torantejarat' ), $tracking )
				: __( 'پس از ارسال، کد مرسوله اینجا نمایش داده می‌شود.', 'torantejarat' ),
		),
		array(
			'title' => __( 'تحویل شد', 'torantejarat' ),
			'desc'  => $done
				? sprintf( __( 'تحویل در %s.', 'torantejarat' ), torantejarat_jalali_ymd( $done->date( 'Y' ), $done->date( 'n' ), $done->date( 'j' ) ) )
				: __( 'پس از تحویل، اینجا ثبت می‌شود.', 'torantejarat' ),
		),
	);
	echo '<section class="torantejarat-timeline" aria-label="' . esc_attr__( 'مراحل سفارش', 'torantejarat' ) . '"><ol>';
	$torantejarat_last = count( $steps ) - 1;
	foreach ( $steps as $index => $step ) {
		$state = ( $index < $stage || $stage === $torantejarat_last ) ? 'is-done' : ( $index === $stage ? 'is-current' : 'is-todo' );
		echo '<li class="torantejarat-timeline-step ' . esc_attr( $state ) . '">';
		echo '<span class="torantejarat-timeline-dot" aria-hidden="true"></span>';
		echo '<div><strong>' . esc_html( $step['title'] ) . '</strong><span>' . esc_html( $step['desc'] ) . '</span></div>';
		echo '</li>';
	}
	echo '</ol></section>';
}

function torantejarat_cart_layout_start() { echo '<div class="torantejarat-native-cart-layout">'; }
function torantejarat_cart_layout_end() { echo '</div>'; }

/**
 * Branded empty-cart copy via native Woo filters (no template override).
 */
/**
 * Empty-cart takeover: single designed card instead of the legacy Woo notice.
 */
function torantejarat_cart_empty_takeover() {
	if ( function_exists( 'is_cart' ) && is_cart() && WC()->cart && WC()->cart->is_empty() ) {
		remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
	}
}
function torantejarat_return_to_shop_text() {
	return __( 'مشاهده محصولات', 'torantejarat' );
}
function torantejarat_cart_empty_extras() {
	echo '<div class="torantejarat-cart-emptybox">';
	echo '<span class="torantejarat-cart-emptybox-icon">';
	torantejarat_icon( 'cart' );
	echo '</span>';
	echo '<h2>' . esc_html__( 'سبد خرید شما خالی است.', 'torantejarat' ) . '</h2>';
	echo '<p>' . esc_html__( 'به نظر می‌رسد هنوز محصولی به سبد خرید خود اضافه نکرده‌اید.', 'torantejarat' ) . '</p>';
	echo '</div>';
}

/**
 * Product suggestions on the empty cart (latest in-stock, native data).
 */
function torantejarat_cart_empty_suggestions() {
	$items = array_values( array_filter( wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 6, 'stock_status' => 'instock', 'orderby' => 'date', 'order' => 'DESC' ) ), static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
	if ( ! $items ) { return; }
	get_template_part(
		'template-parts/product-slider',
		null,
		array(
			'products' => $items,
			'eyebrow'  => __( 'شروع کنید', 'torantejarat' ),
			'title'    => __( 'پیشنهادهای امروز', 'torantejarat' ),
		)
	);
}

/**
 * Receipt-flow order on order-pay (ZarinPal-style redirect page).
 * Same key standard as Woo's own receipt branch; the classic pay-form flow
 * returns false here (covered by before_woocommerce_pay_form).
 *
 * @return \WC_Order|false
 */
function torantejarat_receipt_order() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ! is_wc_endpoint_url( 'order-pay' ) ) {
		return false;
	}
	if ( isset( $_GET['pay_for_order'], $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only flow selector; key verified below.
		return false;
	}
	$order_id = absint( get_query_var( 'order-pay' ) );
	if ( $order_id <= 0 ) {
		return false;
	}
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof \WC_Order ) {
		return false;
	}
	$key = isset( $_GET['key'] ) && is_string( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Bearer-style order key, compared with hash_equals like Woo core.
	if ( '' === $key || ! hash_equals( $order->get_order_key(), $key ) ) {
		return false;
	}
	return $order;
}

/**
 * Invoice header on the receipt page (before_woocommerce_pay).
 */
function torantejarat_receipt_invoice() {
	$order = torantejarat_receipt_order();
	if ( ! $order ) {
		return;
	}
	torantejarat_invoice_header( $order );
}

/**
 * Invoice buttons at the end of the receipt page (after_woocommerce_pay).
 */
function torantejarat_receipt_actions() {
	$order = torantejarat_receipt_order();
	if ( ! $order ) {
		return;
	}
	torantejarat_invoice_buttons( $order );
}

/**
 * Build an honest payment/fulfilment badge without treating every status as paid.
 * A custom shipped status is not necessarily paid; a previous payment date is.
 *
 * @param \WC_Order $order Order object.
 * @return array{label:string,state:string}
 */
function torantejarat_order_status_badge( $order ) {
	$status       = $order->get_status();
	$method       = $order->get_payment_method();
	$is_cod       = 'cod' === $method;
	$has_payment  = ! $is_cod && ( $order->is_paid() || (bool) $order->get_date_paid() );
	$label        = __( 'در انتظار پرداخت', 'torantejarat' );
	$state        = 'pending';

	if ( in_array( $status, array( 'failed', 'cancelled', 'refunded' ), true ) ) {
		$label = 'failed' === $status ? __( 'پرداخت ناموفق', 'torantejarat' ) : wc_get_order_status_name( $status );
		$state = 'failed';
	} elseif ( 'shipped' === $status ) {
		$label = $is_cod ? __( 'ارسال شد — پرداخت هنگام تحویل', 'torantejarat' ) : ( $has_payment ? __( 'پرداخت شد — ارسال شد', 'torantejarat' ) : __( 'ارسال شد', 'torantejarat' ) );
		$state = 'shipped';
	} elseif ( 'completed' === $status ) {
		$label = $is_cod ? __( 'تحویل شد — پرداخت هنگام تحویل', 'torantejarat' ) : ( $has_payment ? __( 'پرداخت شد — تحویل شد', 'torantejarat' ) : __( 'تحویل شد', 'torantejarat' ) );
		$state = 'shipped';
	} elseif ( 'processing' === $status ) {
		$label = $is_cod ? __( 'در حال آماده‌سازی — پرداخت هنگام تحویل', 'torantejarat' ) : ( $has_payment ? __( 'پرداخت شد — در حال آماده‌سازی', 'torantejarat' ) : __( 'در حال آماده‌سازی', 'torantejarat' ) );
		$state = $has_payment ? 'paid' : 'pending';
	} elseif ( 'on-hold' === $status ) {
		if ( $is_cod ) {
			$label = __( 'پرداخت هنگام تحویل', 'torantejarat' );
		} elseif ( $has_payment ) {
			$label = __( 'پرداخت شد — در انتظار بررسی', 'torantejarat' );
			$state = 'paid';
		} elseif ( 'bacs' === $method ) {
			$label = __( 'در انتظار پرداخت', 'torantejarat' );
		} else {
			$label = __( 'در انتظار بررسی', 'torantejarat' );
		}
	} elseif ( $is_cod ) {
		$label = __( 'پرداخت هنگام تحویل', 'torantejarat' );
	} elseif ( $has_payment ) {
		$label = __( 'پرداخت شد', 'torantejarat' );
		$state = 'paid';
	}

	return array( 'label' => $label, 'state' => $state );
}

/**
 * Professional invoice header — order-pay (object), thankyou + view-order (ID).
 * Hooks only; no Woo template override. Native Woo data, HPOS-safe.
 *
 * @param \WC_Order|int $maybe_order Order object or ID.
 */
function torantejarat_invoice_header( $maybe_order = 0 ) {
	$order = $maybe_order instanceof \WC_Order ? $maybe_order : wc_get_order( absint( $maybe_order ) );
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	$badge   = torantejarat_order_status_badge( $order );
	$created = $order->get_date_created();
	$date    = $created ? torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) : __( '—', 'torantejarat' );
	$name    = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
	$phone   = trim( (string) $order->get_billing_phone() );
	$phone_digits = preg_replace( '/[^0-9+]/', '', $phone );
	$state_code = $order->get_billing_state();
	$state_name = $state_code;
	if ( function_exists( 'WC' ) && WC()->countries ) {
		$states = WC()->countries->get_states( $order->get_billing_country() ? $order->get_billing_country() : 'IR' );
		if ( isset( $states[ $state_code ] ) ) {
			$state_name = $states[ $state_code ];
		}
	}
	$address_parts = array_filter( array( $state_name, $order->get_billing_city(), $order->get_billing_address_1(), $order->get_billing_address_2() ) );
	if ( $order->get_billing_postcode() ) {
		$address_parts[] = __( 'کد پستی:', 'torantejarat' ) . ' ' . $order->get_billing_postcode();
	}
	$txn = trim( (string) $order->get_transaction_id() );
	?>
	<section class="torantejarat-invoice" aria-label="<?php esc_attr_e( 'فاکتور سفارش', 'torantejarat' ); ?>">
		<div class="torantejarat-invoice-head">
			<div>
				<strong class="torantejarat-invoice-brand"><?php esc_html_e( 'بلکوکم', 'torantejarat' ); ?></strong>
				<span class="torantejarat-invoice-sub"><?php esc_html_e( 'فاکتور سفارش', 'torantejarat' ); ?></span>
			</div>
			<span class="torantejarat-invoice-badge is-<?php echo esc_attr( $badge['state'] ); ?>"><?php echo esc_html( $badge['label'] ); ?></span>
		</div>
		<dl class="torantejarat-invoice-meta">
			<div><dt><?php esc_html_e( 'شماره سفارش', 'torantejarat' ); ?></dt><dd><?php echo esc_html( torantejarat_fa_number( $order->get_order_number() ) ); ?></dd></div>
			<div><dt><?php esc_html_e( 'تاریخ ثبت', 'torantejarat' ); ?></dt><dd><?php echo esc_html( $date ); ?></dd></div>
			<div><dt><?php esc_html_e( 'مبلغ نهایی', 'torantejarat' ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
			<div><dt><?php esc_html_e( 'روش پرداخت', 'torantejarat' ); ?></dt><dd><?php echo $order->get_payment_method_title() ? esc_html( $order->get_payment_method_title() ) : esc_html__( '—', 'torantejarat' ); ?></dd></div>
		</dl>
		<?php if ( '' !== $txn ) : ?>
			<p class="torantejarat-invoice-txn"><?php esc_html_e( 'کد پیگیری پرداخت:', 'torantejarat' ); ?> <strong dir="ltr"><?php echo esc_html( $txn ); ?></strong></p>
		<?php endif; ?>
		<div class="torantejarat-invoice-customer">
			<strong><?php esc_html_e( 'مشخصات گیرنده', 'torantejarat' ); ?></strong>
			<p>
				<?php if ( '' !== $name ) : ?><span><?php echo esc_html( $name ); ?></span><?php endif; ?>
				<?php if ( '' !== $phone && '' !== $phone_digits ) : ?><span><a href="tel:<?php echo esc_attr( $phone_digits ); ?>" dir="ltr"><?php echo esc_html( $phone ); ?></a></span><?php endif; ?>
				<?php if ( $address_parts ) : ?><span><?php echo esc_html( implode( ' — ', $address_parts ) ); ?></span><?php endif; ?>
			</p>
		</div>
	</section>
	<?php
}

/**
 * Amount reminder above the payment methods on order-pay.
 */
function torantejarat_pay_reminder() {
	$order_id = absint( get_query_var( 'order-pay' ) );
	$order    = $order_id > 0 ? wc_get_order( $order_id ) : false;
	if ( ! $order instanceof \WC_Order || ! $order->needs_payment() ) {
		return;
	}
	echo '<div class="torantejarat-pay-reminder" role="note"><div><strong>' . esc_html__( 'مبلغ قابل پرداخت', 'torantejarat' ) . '</strong><span>' . wp_kses_post( $order->get_formatted_order_total() ) . '</span></div>';
	echo '<p>' . esc_html__( 'پس از پرداخت موفق، فاکتور و کد پیگیری همین‌جا نمایش داده می‌شود.', 'torantejarat' ) . '</p></div>';
}

/**
 * Pay button with the real payable amount.
 */
function torantejarat_pay_button_text( $text ) {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ! is_wc_endpoint_url( 'order-pay' ) ) {
		return $text;
	}
	$order_id = absint( get_query_var( 'order-pay' ) );
	$order    = $order_id > 0 ? wc_get_order( $order_id ) : false;
	if ( ! $order instanceof \WC_Order || ! $order->needs_payment() ) {
		return $text;
	}
	return sprintf(
		/* translators: %s: formatted order total. */
		__( 'پرداخت %s', 'torantejarat' ),
		wp_strip_all_tags( $order->get_formatted_order_total() )
	);
}

/**
 * Footer actions under the invoice — thankyou page.
 *
 * @param int $order_id Order ID.
 */
function torantejarat_invoice_actions( $order_id ) {
	$order = wc_get_order( absint( $order_id ) );
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	torantejarat_invoice_buttons( $order );
}

/**
 * Footer actions under the invoice — frontend order-details tables only.
 * Skips Woo emails and admin screens; timeline/tracking boxes stay untouched.
 *
 * @param \WC_Order $order Order object.
 */
function torantejarat_invoice_front_actions( $order ) {
	if ( is_admin() || function_exists( 'doing_action' ) && doing_action( 'woocommerce_email_order_details' ) ) {
		return;
	}
	if ( ! $order instanceof \WC_Order ) {
		return;
	}
	if ( ! ( function_exists( 'is_checkout' ) && is_checkout() ) && ! ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
		return;
	}
	/* thankyou already prints the same buttons via woocommerce_thankyou. */
	if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
		return;
	}
	torantejarat_invoice_buttons( $order );
}

/**
 * Shared invoice buttons: print + account + shop (+ repay on failure).
 *
 * @param \WC_Order $order Order object.
 */
function torantejarat_invoice_buttons( $order ) {
	$buttons = array();
	if ( $order->has_status( 'failed' ) && $order->needs_payment() ) {
		$buttons[] = '<a class="torantejarat-btn torantejarat-btn-primary" href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'تلاش مجدد برای پرداخت', 'torantejarat' ) . '</a>';
	}
	$buttons[] = '<button type="button" class="torantejarat-btn torantejarat-btn-outline" onclick="window.print()">' . esc_html__( 'چاپ فاکتور', 'torantejarat' ) . '</button>';
	if ( wc_get_page_id( 'myaccount' ) > 0 ) {
		$buttons[] = '<a class="torantejarat-btn torantejarat-btn-outline" href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'پیگیری در حساب کاربری', 'torantejarat' ) . '</a>';
	}
	if ( wc_get_page_id( 'shop' ) > 0 ) {
		$buttons[] = '<a class="torantejarat-text-link" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'ادامه خرید', 'torantejarat' ) . '</a>';
	}
	echo '<div class="torantejarat-invoice-actions">' . implode( '', $buttons ) . '</div>';
}

/**
 * Strip test-mode markers from gateway titles on the frontend.
 */
function torantejarat_gateway_title_clean( $title, $gateway_id = '' ) {
	if ( is_admin() ) {
		return $title;
	}
	return str_replace( array( '(تست)', 'تست', '(test)', '(Test)' ), '', $title );
}

/**
 * Registration link inside the WooCommerce login form (My Account, logged out).
 * wp-login.php already shows one in #nav, but the WooCommerce form itself
 * renders on /my-account/ without it — add an explicit styled link.
 */
function torantejarat_wc_register_link() {
	if ( ! get_option( 'users_can_register' ) ) {
		return;
	}
	$register_url = wp_registration_url();
	echo '<p class="torantejarat-wc-register-link">';
	printf(
		/* translators: %s: registration URL */
		esc_html__( 'حساب ندارید؟ %s', 'torantejarat' ),
		'<a href="' . esc_url( $register_url ) . '">' . esc_html__( 'ثبت‌نام کنید', 'torantejarat' ) . '</a>'
	);
	echo '</p>';
}
