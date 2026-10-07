<?php
/**
 * Smart shop filters — category, attributes, price, stock, deals.
 * Server-first: plain GET submit works without JS. JS layer (shop-filters.js)
 * reuses the same URLs and swaps only the results subtree (no second
 * rendering path). Attribute facets come from real taxonomy terms only.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_shop_filters_setup() {
	if ( ! torantejarat_woocommerce_available() ) {
		return;
	}
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_filters_layout_start', 5 );
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_filters_results_start', 12 );
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_filters_toolbar_extras', 13 );
	add_action( 'woocommerce_after_shop_loop', __NAMESPACE__ . '\\torantejarat_filters_layout_end', 49 );
	add_action( 'woocommerce_product_query', __NAMESPACE__ . '\\torantejarat_filters_apply' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_filters_canonical' );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\torantejarat_sortbar', 4 );
	add_filter( 'woocommerce_default_catalog_orderby', static fn() => 'date' );
	add_filter( 'woocommerce_get_catalog_ordering_args', __NAMESPACE__ . '\\torantejarat_discount_ordering' );
	add_action( 'wp_head', __NAMESPACE__ . '\\torantejarat_filter_seo', 5 );
}

/**
 * Biggest-discount ordering: Woo does not know it, so map it to the
 * pre-sorted post__in list (built in torantejarat_filters_apply).
 */
function torantejarat_discount_ordering( $args ) {
	if ( isset( $_GET['orderby'] ) && 'discount' === sanitize_key( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$args['orderby']  = 'post__in';
		$args['order']    = 'ASC';
		$args['meta_key'] = '';
	}
	return $args;
}

/**
 * Pill sort bar (count + newest / best-selling / cheapest / priciest / biggest discount).
 * Plain links: works without JS; the JS layer swaps via AJAX.
 */
function torantejarat_sortbar() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	global $wp_query;
	$total   = $wp_query instanceof \WP_Query ? (int) $wp_query->found_posts : 0;
	$current = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$options = array(
		'date'       => __( 'جدیدترین', 'torantejarat' ),
		'popularity' => __( 'پرفروش‌ترین', 'torantejarat' ),
		'price'      => __( 'ارزان‌ترین', 'torantejarat' ),
		'price-desc' => __( 'گران‌ترین', 'torantejarat' ),
		'discount'   => __( 'بیشترین تخفیف', 'torantejarat' ),
	);
	if ( ! isset( $options[ $current ] ) ) {
		$current = 'date';
	}
	echo '<div class="torantejarat-sortbar" data-sortbar>';
	echo '<span class="torantejarat-sortbar-count">' . esc_html( sprintf( __( '%s محصول', 'torantejarat' ), number_format_i18n( $total ) ) ) . '</span>';
	echo '<nav class="torantejarat-sort-pills" aria-label="' . esc_attr__( 'مرتب‌سازی', 'torantejarat' ) . '">';
	foreach ( $options as $key => $label ) {
		$active = $key === $current ? ' is-active' : '';
		echo '<a class="torantejarat-sort-pill' . esc_attr( $active ) . '" href="' . esc_url( torantejarat_filters_url( array( 'orderby' => $key ) ) ) . '"' . ( $active ? ' aria-current="true"' : '' ) . '>' . esc_html( $label ) . '</a>';
	}
	echo '</nav>';
	$filter_count = torantejarat_filters_active_count();
	echo '<div class="torantejarat-mobile-bar">';
	echo '<span class="torantejarat-mobile-count">' . esc_html( sprintf( __( '%s محصول', 'torantejarat' ), number_format_i18n( $total ) ) ) . '</span>';
	echo '<button type="button" data-filter-open>' . esc_html__( 'فیلترها', 'torantejarat' );
	echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h10M18 7h2M4 17h4M12 17h8"/><circle cx="16" cy="7" r="2"/><circle cx="10" cy="17" r="2"/></svg>';
	if ( $filter_count > 0 ) {
		echo '<b>' . esc_html( number_format_i18n( $filter_count ) ) . '</b>';
	}
	echo '</button>';
	echo '<details class="torantejarat-sort-sheet" data-sort-sheet><summary>' . esc_html__( 'مرتب‌سازی', 'torantejarat' ) . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M7 12h10M10 17h4"/></svg></summary><div>';
	foreach ( $options as $key => $label ) {
		$active = $key === $current ? ' is-active' : '';
		echo '<a class="' . esc_attr( $active ) . '" href="' . esc_url( torantejarat_filters_url( array( 'orderby' => $key ) ) ) . '"' . ( $active ? ' aria-current="true"' : '' ) . '>' . esc_html( $label ) . '</a>';
	}
	echo '</div></details>';
	echo '</div>';
	echo '</div>';
}

/**
 * Current filter URL with overrides (sanitized). Basis for pills and chips.
 *
 * @param array $overrides Key => value (null value removes the key).
 * @return string
 */
function torantejarat_filters_url( $overrides = array() ) {
	$input  = torantejarat_filters_input();
	$params = array();
	if ( $input['pcat'] ) {
		$params['pcat'] = $input['pcat'];
	}
	foreach ( $input['attr'] as $taxonomy => $slugs ) {
		$params[ 'att_' . preg_replace( '/^pa_/', '', $taxonomy ) ] = $slugs;
	}
	if ( '' !== $input['min_price'] ) {
		$params['min_price'] = $input['min_price'];
	}
	if ( '' !== $input['max_price'] ) {
		$params['max_price'] = $input['max_price'];
	}
	if ( $input['instock'] ) {
		$params['instock'] = '1';
	}
	if ( $input['onsale'] ) {
		$params['onsale'] = '1';
	}
	if ( isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['orderby'] = sanitize_key( $_GET['orderby'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( isset( $_GET['s'] ) && '' !== $_GET['s'] && ! is_array( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	foreach ( $overrides as $key => $value ) {
		if ( null === $value ) {
			unset( $params[ $key ] );
		} else {
			$params[ $key ] = $value;
		}
	}
	$base = torantejarat_filters_base_url();
	if ( is_wp_error( $base ) ) {
		$base = wc_get_page_permalink( 'shop' );
	}
	return add_query_arg( $params, $base );
}

/**
 * Drop empty price params (plain submits and shared links): Woo's native
 * price filter treats empty strings as a real range and returns nothing.
 */
function torantejarat_filters_canonical() {
	if ( ! ( is_shop() || is_product_taxonomy() ) || is_admin() ) {
		return;
	}
	if ( ( isset( $_GET['min_price'] ) && '' === $_GET['min_price'] ) || ( isset( $_GET['max_price'] ) && '' === $_GET['max_price'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$input  = torantejarat_filters_input();
		$params = array();
		if ( $input['pcat'] ) {
			$params['pcat'] = $input['pcat'];
		}
		foreach ( $input['attr'] as $taxonomy => $slugs ) {
			$params[ 'att_' . preg_replace( '/^pa_/', '', $taxonomy ) ] = $slugs;
		}
		if ( '' !== $input['min_price'] ) {
			$params['min_price'] = $input['min_price'];
		}
		if ( '' !== $input['max_price'] ) {
			$params['max_price'] = $input['max_price'];
		}
		if ( $input['instock'] ) {
			$params['instock'] = '1';
		}
		if ( $input['onsale'] ) {
			$params['onsale'] = '1';
		}
		foreach ( array( 'orderby', 's' ) as $passthrough ) {
			if ( isset( $_GET[ $passthrough ] ) && '' !== $_GET[ $passthrough ] && ! is_array( $_GET[ $passthrough ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$params[ $passthrough ] = sanitize_text_field( wp_unslash( $_GET[ $passthrough ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}
		wp_safe_redirect( esc_url_raw( add_query_arg( $params, torantejarat_filters_base_url() ) ), 301 );
		exit;
	}
	if ( torantejarat_filters_active() ) {
		add_filter(
			'wp_robots',
			static function ( $robots ) {
				$robots['noindex'] = true;
				$robots['follow']  = true;
				return $robots;
			}
		);
	}
}

/**
 * Read and sanitize public filter input. Single shape for render + query.
 *
 * @return array{pcat: string[], attr: array<string, string[]>, min_price: string, max_price: string, instock: bool, onsale: bool}
 */
function torantejarat_filters_input() {
	$get = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only filters.
	$cats = array();
	if ( isset( $get['pcat'] ) ) {
		$raw_cats = is_array( $get['pcat'] ) ? $get['pcat'] : array( $get['pcat'] );
		foreach ( $raw_cats as $slug ) {
			$slug = sanitize_title( wp_unslash( $slug ) );
			if ( '' === $slug ) {
				continue;
			}
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term ) {
				$term = get_term_by( 'slug', urldecode( $slug ), 'product_cat' );
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$cats[] = $term->slug;
			}
		}
	}
	$attrs = array();
	foreach ( wc_get_attribute_taxonomies() as $tax ) {
		$taxonomy = 'pa_' . $tax->attribute_name;
		$key      = 'att_' . $tax->attribute_name;
		if ( empty( $get[ $key ] ) ) {
			continue;
		}
		$raw_terms = is_array( $get[ $key ] ) ? $get[ $key ] : explode( ',', (string) $get[ $key ] );
		$terms     = array();
		foreach ( $raw_terms as $term ) {
			$term = sanitize_title( wp_unslash( $term ) );
			if ( '' === $term ) {
				continue;
			}
			$found = get_term_by( 'slug', $term, $taxonomy );
			if ( ! $found ) {
				$found = get_term_by( 'slug', urldecode( $term ), $taxonomy );
			}
			if ( $found && ! is_wp_error( $found ) ) {
				$terms[] = $found->slug;
			}
		}
		if ( $terms ) {
			$attrs[ $taxonomy ] = array_values( array_unique( $terms ) );
		}
	}
	return array(
		'pcat'       => array_values( array_unique( $cats ) ),
		'attr'      => $attrs,
		'min_price' => isset( $get['min_price'] ) ? sanitize_text_field( wp_unslash( $get['min_price'] ) ) : '',
		'max_price' => isset( $get['max_price'] ) ? sanitize_text_field( wp_unslash( $get['max_price'] ) ) : '',
		'instock'   => isset( $get['instock'] ) && '1' === $get['instock'],
		'onsale'    => isset( $get['onsale'] ) && '1' === $get['onsale'],
	);
}

/**
 * Attribute facets with real terms only (empty taxonomies never render).
 *
 * @return array List of array( 'taxonomy' => string, 'param' => string, 'label' => string, 'terms' => WP_Term[] ).
 */
function torantejarat_filters_facets() {
	$facets = array();
	foreach ( wc_get_attribute_taxonomies() as $tax ) {
		$taxonomy = 'pa_' . $tax->attribute_name;
		$terms    = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'number' => 20 ) );
		if ( is_wp_error( $terms ) || count( $terms ) < 1 ) {
			continue;
		}
		$facets[] = array(
			'taxonomy' => $taxonomy,
			'param'    => 'att_' . $tax->attribute_name,
			'label'    => $tax->attribute_label,
			'terms'    => $terms,
		);
	}
	return $facets;
}

function torantejarat_filters_apply( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	$input    = torantejarat_filters_input();
	$taxquery = (array) $query->get( 'tax_query' );
	if ( $input['pcat'] ) {
		$taxquery[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $input['pcat'],
			'operator' => 'IN',
		);
	}
	foreach ( $input['attr'] as $taxonomy => $slugs ) {
		$taxquery[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		);
	}
	if ( count( $taxquery ) > 1 ) {
		$taxquery['relation'] = 'AND';
	}
	if ( $input['instock'] ) {
		$taxquery[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array( 'outofstock' ),
			'operator' => 'NOT IN',
		);
	}
	if ( $taxquery ) {
		$query->set( 'tax_query', $taxquery );
	}
	if ( $input['onsale'] ) {
		$sale_ids = wc_get_product_ids_on_sale();
		$current  = (array) $query->get( 'post__in' );
		$query->set( 'post__in', $current ? array_intersect( $current, $sale_ids ) : $sale_ids );
	}
	if ( isset( $_GET['orderby'] ) && 'discount' === sanitize_key( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$deal_ids = array();
		foreach ( torantejarat_deals( 100 ) as $deal ) {
			$deal_ids[] = $deal['product']->get_id();
		}
		if ( $deal_ids ) {
			$current = (array) $query->get( 'post__in' );
			$query->set( 'post__in', $current ? array_values( array_intersect( $deal_ids, $current ) ) : $deal_ids );
			$query->set( 'orderby', 'post__in' );
			$query->set( 'order', 'ASC' );
		}
	}
}

function torantejarat_filters_layout_start() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<div class="torantejarat-catalog-layout">';
	torantejarat_filters_sidebar();
}

function torantejarat_filters_results_start() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '<div class="torantejarat-catalog-results" data-catalog-results>';
}

function torantejarat_filters_layout_end() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	echo '</div></div>';
}

function torantejarat_filters_sidebar() {
	$input = torantejarat_filters_input();
	$cats  = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 20 ) );
	?>
	<aside class="torantejarat-filters" aria-label="<?php esc_attr_e( 'فیلترهای فروشگاه', 'torantejarat' ); ?>" data-filters>
		<form method="get" action="<?php echo esc_url( torantejarat_filters_base_url() ); ?>" data-filters-form>
			<div class="torantejarat-filter-head">
				<h2><?php esc_html_e( 'فیلتر محصولات', 'torantejarat' ); ?></h2>
				<button type="button" class="torantejarat-filter-close" data-filter-close aria-label="<?php esc_attr_e( 'بستن فیلترها', 'torantejarat' ); ?>">×</button>
			</div>
			<?php
			foreach ( array( 'orderby', 's', 'post_type' ) as $passthrough ) {
				if ( isset( $_GET[ $passthrough ] ) && ! is_array( $_GET[ $passthrough ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					echo '<input type="hidden" name="' . esc_attr( $passthrough ) . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $passthrough ] ) ) ) . '">'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
			}
			?>
			<div class="torantejarat-filter-toggles">
				<label class="torantejarat-switch">
					<span><?php esc_html_e( 'فقط محصولات موجود', 'torantejarat' ); ?></span>
					<input type="checkbox" name="instock" value="1"<?php checked( $input['instock'] ); ?>>
					<i aria-hidden="true"></i>
				</label>
				<label class="torantejarat-switch">
					<span><?php esc_html_e( 'فقط تخفیف‌دارها', 'torantejarat' ); ?></span>
					<input type="checkbox" name="onsale" value="1"<?php checked( $input['onsale'] ); ?>>
					<i aria-hidden="true"></i>
				</label>
			</div>
			<?php $torantejarat_first_group = true; ?>
			<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
				<details class="torantejarat-filter-group"<?php echo $torantejarat_first_group ? ' open' : ''; $torantejarat_first_group = false; ?>>
					<summary><?php esc_html_e( 'دسته‌بندی', 'torantejarat' ); ?></summary>
					<div>
					<?php foreach ( $cats as $cat ) : ?>
						<label>
							<input type="checkbox" name="pcat[]" value="<?php echo esc_attr( $cat->slug ); ?>"<?php checked( in_array( $cat->slug, $input['pcat'], true ) ); ?>>
							<span><?php echo esc_html( $cat->name ); ?></span>
							<b><?php echo esc_html( number_format_i18n( $cat->count ) ); ?></b>
						</label>
					<?php endforeach; ?>
					</div>
				</details>
			<?php endif; ?>
			<?php foreach ( torantejarat_filters_facets() as $facet ) : ?>
				<details class="torantejarat-filter-group"<?php echo $torantejarat_first_group ? ' open' : ''; $torantejarat_first_group = false; ?>>
					<summary><?php echo esc_html( $facet['label'] ); ?></summary>
					<div>
					<?php foreach ( $facet['terms'] as $term ) : ?>
						<?php $checked = isset( $input['attr'][ $facet['taxonomy'] ] ) && in_array( $term->slug, $input['attr'][ $facet['taxonomy'] ], true ); ?>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $facet['param'] ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( $checked ); ?>>
							<span><?php echo esc_html( $term->name ); ?></span>
							<b><?php echo esc_html( number_format_i18n( $term->count ) ); ?></b>
						</label>
					<?php endforeach; ?>
					</div>
				</details>
			<?php endforeach; ?>
			<details class="torantejarat-filter-group"<?php echo $torantejarat_first_group ? ' open' : ''; ?>>
				<summary><?php esc_html_e( 'قیمت (تومان)', 'torantejarat' ); ?></summary>
				<div>
				<div class="torantejarat-filter-price">
					<input type="number" name="min_price" min="0" inputmode="numeric" placeholder="<?php esc_attr_e( 'از', 'torantejarat' ); ?>" value="<?php echo esc_attr( $input['min_price'] ); ?>">
					<span aria-hidden="true">–</span>
					<input type="number" name="max_price" min="0" inputmode="numeric" placeholder="<?php esc_attr_e( 'تا', 'torantejarat' ); ?>" value="<?php echo esc_attr( $input['max_price'] ); ?>">
				</div>
				</div>
			</details>
			<div class="torantejarat-filter-foot">
				<button type="submit" class="torantejarat-btn torantejarat-btn-primary"><?php esc_html_e( 'اعمال فیلتر', 'torantejarat' ); ?></button>
				<a class="torantejarat-filter-clear" href="<?php echo esc_url( torantejarat_filters_base_url() ); ?>"><?php esc_html_e( 'حذف همه', 'torantejarat' ); ?></a>
			</div>
		</form>
	</aside>
	<?php
}

/**
 * Canonical + robots for filtered catalog views (cannibalization guard).
 * Filtered URLs canonicalize to the clean archive; filtered views are
 * noindexed (follow) so only canonical archives compete in search.
 * Skipped entirely when Rank Math takes over SEO.
 */
function torantejarat_filter_seo() {
	if ( defined( 'RANK_MATH_VERSION' ) || ! apply_filters( 'torantejarat_filter_seo', true ) ) {
		return;
	}
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	if ( ! torantejarat_filters_active() ) {
		return;
	}
	$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	$base  = torantejarat_filters_base_url();
	if ( is_wp_error( $base ) ) {
		return;
	}
	$canonical = $paged > 1 ? trailingslashit( $base ) . 'page/' . $paged . '/' : $base;
	printf(
		'<link rel="canonical" href="%s">' . "\n",
		esc_url( $canonical )
	);
	add_filter(
		'wp_robots',
		static function ( $robots ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			return $robots;
		}
	);
}

function torantejarat_filters_active() {
	$input = torantejarat_filters_input();
	if ( $input['pcat'] || $input['attr'] || $input['instock'] || $input['onsale'] ) {
		return true;
	}
	if ( '' !== $input['min_price'] || '' !== $input['max_price'] ) {
		return true;
	}
	return isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Base shop URL preserving taxonomy context (drops paging, keeps orderby/search).
 */
function torantejarat_filters_base_url() {
	if ( is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term instanceof \WP_Term ) {
			return get_term_link( $term );
		}
	}
	return wc_get_page_permalink( 'shop' );
}

/**
 * Number of active filters (badge for filter buttons).
 */
function torantejarat_filters_active_count() {
	$input = torantejarat_filters_input();
	$count = count( $input['pcat'] ) + ( $input['instock'] ? 1 : 0 ) + ( $input['onsale'] ? 1 : 0 )
		+ ( '' !== $input['min_price'] || '' !== $input['max_price'] ? 1 : 0 );
	foreach ( $input['attr'] as $terms ) {
		$count += count( $terms );
	}
	return $count;
}

function torantejarat_filters_toolbar_extras() {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}
	$input = torantejarat_filters_input();
	$count = torantejarat_filters_active_count();
	?>
	<div class="torantejarat-shop-filterbar">
		<button type="button" class="torantejarat-filter-toggle" data-filter-open>
			<?php esc_html_e( 'فیلترها', 'torantejarat' ); ?>
			<?php if ( $count > 0 ) : ?><b><?php echo esc_html( number_format_i18n( $count ) ); ?></b><?php endif; ?>
		</button>
		<?php torantejarat_filters_chips( $input ); ?>
	</div>
	<?php
}

function torantejarat_filters_chips( $input ) {
	$chips = array();
	foreach ( $input['pcat'] as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term ) {
			$chips[] = array( 'label' => $term->name, 'remove' => array( 'pcat' => $slug ) );
		}
	}
	foreach ( $input['attr'] as $taxonomy => $slugs ) {
		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, $taxonomy );
			if ( $term ) {
				$chips[] = array( 'label' => $term->name, 'remove' => array( 'attr' => $taxonomy . ':' . $slug ) );
			}
		}
	}
	if ( '' !== $input['min_price'] || '' !== $input['max_price'] ) {
		$label   = ( '' !== $input['min_price'] ? $input['min_price'] : '۰' ) . ' – ' . ( '' !== $input['max_price'] ? $input['max_price'] : '∞' );
		$chips[] = array( 'label' => $label, 'remove' => array( 'price' => 1 ) );
	}
	if ( $input['instock'] ) {
		$chips[] = array( 'label' => __( 'فقط موجودها', 'torantejarat' ), 'remove' => array( 'instock' => 1 ) );
	}
	if ( $input['onsale'] ) {
		$chips[] = array( 'label' => __( 'تخفیف‌دارها', 'torantejarat' ), 'remove' => array( 'onsale' => 1 ) );
	}
	if ( ! $chips ) {
		return;
	}
	echo '<div class="torantejarat-active-filters" data-chips>';
	foreach ( $chips as $chip ) {
		$url = torantejarat_filters_remove_url( $chip['remove'] );
		echo '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( sprintf( __( 'حذف فیلتر %s', 'torantejarat' ), $chip['label'] ) ) . '">' . esc_html( $chip['label'] ) . ' <span aria-hidden="true">×</span></a>';
	}
	echo '<a class="torantejarat-clear-all" href="' . esc_url( torantejarat_filters_base_url() ) . '">' . esc_html__( 'حذف همه', 'torantejarat' ) . '</a>';
	echo '</div>';
}

function torantejarat_filters_remove_url( $remove ) {
	$params = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only link builder.
	if ( isset( $remove['pcat'] ) && isset( $params['pcat'] ) ) {
		$params['pcat'] = array_values( array_diff( (array) $params['pcat'], array( $remove['pcat'] ) ) );
		if ( ! $params['pcat'] ) {
			unset( $params['pcat'] );
		}
	}
	if ( isset( $remove['attr'] ) ) {
		list( $taxonomy, $slug ) = explode( ':', $remove['attr'], 2 ) + array( '', '' );
		$tax   = preg_replace( '/^pa_/', '', $taxonomy );
		$key   = 'att_' . $tax;
		if ( isset( $params[ $key ] ) ) {
			$terms = array_values( array_diff( (array) $params[ $key ], array( $slug ) ) );
			if ( $terms ) {
				$params[ $key ] = $terms;
			} else {
				unset( $params[ $key ] );
			}
		}
	}
	if ( isset( $remove['price'] ) ) {
		unset( $params['min_price'], $params['max_price'] );
	}
	if ( isset( $remove['instock'] ) ) {
		unset( $params['instock'] );
	}
	if ( isset( $remove['onsale'] ) ) {
		unset( $params['onsale'] );
	}
	unset( $params['paged'], $params['page'] );
	$base = torantejarat_filters_base_url();
	if ( is_wp_error( $base ) ) {
		$base = wc_get_page_permalink( 'shop' );
	}
	$clean = array();
	foreach ( (array) $params as $key => $value ) {
		$key = sanitize_key( (string) $key );
		if ( is_array( $value ) ) {
			$list = array_values( array_filter( array_map( static fn( $v ) => sanitize_title( wp_unslash( $v ) ), $value ) ) );
			if ( $list ) {
				$clean[ $key ] = $list;
			}
		} elseif ( '' !== $value && null !== $value ) {
			$clean[ $key ] = sanitize_text_field( wp_unslash( $value ) );
		}
	}
	return add_query_arg( $clean, $base );
}
