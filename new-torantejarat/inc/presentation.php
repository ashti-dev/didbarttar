<?php
/**
 * Source-derived, read-only presentation helpers.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_icon( $name ) {
	$paths = array(
		'arrow'   => 'm14 7-5 5 5 5M9 12h12',
		'cart'    => 'M3 3h2l3 12h10l3-9H6M9 21h.01M18 21h.01',
		'check'   => 'm5 12 4 4L20 5',
		'scan'    => 'M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5M7 12h10M12 7v10',
		'search'  => 'm21 21-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0',
		'menu'    => 'M4 6h16M4 12h16M4 18h16',
		'home'    => 'm3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9',
		'chevron' => 'm14 6-6 6 6 6',
		'compare' => 'M5 4v16M19 4v16M2 8h6M16 16h6M12 4v16',
		'clock'   => 'M12 8v5l3 2M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0',
		'phone'   => 'M5 4h4l2 5-2 2a12 12 0 0 0 5 5l2-2 5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2',
		'chat'    => 'M4 5h16v11H9l-5 4V5zM8 9h8M8 12h5',
		'mail'    => 'M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1zM3 7l9 6 9-6',
		'pin'     => 'M12 21s-7-5.5-7-10a7 7 0 0 1 14 0c0 4.5-7 10-7 10zM12 12a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z',
		'instagram' => 'M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zM12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM17.2 6.8h.01',
		'telegram' => 'M21 4 3 11l7 2 2 7 4-5 5-11zM10.5 13.5 14 10',
		'youtube' => 'M3 7a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3zM10 9.5l5 2.5-5 2.5z',
		'aparat'  => 'M4 6h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zM16 10l6-3.5v11L16 14',
		'wrench'  => 'M14 6a5 5 0 0 0-6 6L3 17a3 3 0 0 0 4 4l6-6a5 5 0 0 0 6-6l-3 3-4-4 2-2z',
		'book'    => 'M12 5c-3-2-6-2-10-1v15c4-1 7-1 10 1m0-15c3-2 6-2 10-1v15c-4-1-7-1-10 1V5',
	);
	$path = $paths[ $name ] ?? $paths['scan'];
	$size = 'chevron' === $name ? 14 : 24;
	$class = 'chevron' === $name ? 'torantejarat-icon torantejarat-crumb-chevron' : 'torantejarat-icon';
	echo '<svg class="' . esc_attr( $class ) . '" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="' . esc_attr( $path ) . '"/></svg>';
}

/**
 * Unified breadcrumb trail used by every template.
 * Items: array of array( 'label' => string, 'url' => string|null ).
 * The last item is the current page (no URL). Emits JSON-LD BreadcrumbList.
 */
function torantejarat_breadcrumb( array $trail ) {
	$trail = array_values(
		array_filter(
			$trail,
			static fn( $item ) => is_array( $item ) && '' !== trim( (string) ( $item['label'] ?? '' ) )
		)
	);
	if ( ! $trail ) {
		return;
	}
	$home_url = home_url( '/' );
	?>
	<nav class="torantejarat-site-crumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'torantejarat' ); ?>"><ol>
		<?php foreach ( $trail as $torantejarat_index => $torantejarat_crumb ) : ?>
			<?php
			$torantejarat_label = (string) $torantejarat_crumb['label'];
			$torantejarat_url   = $torantejarat_crumb['url'] ?? null;
			$torantejarat_last  = $torantejarat_index === count( $trail ) - 1;
			$torantejarat_home  = 0 === $torantejarat_index && $torantejarat_url && trailingslashit( $torantejarat_url ) === trailingslashit( $home_url );
			?>
			<li>
				<?php if ( 0 !== $torantejarat_index ) : ?><?php torantejarat_icon( 'chevron' ); ?><?php endif; ?>
				<?php if ( $torantejarat_last || ! $torantejarat_url ) : ?>
					<span class="torantejarat-crumb-current"<?php echo $torantejarat_last ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $torantejarat_label ); ?></span>
				<?php else : ?>
					<a href="<?php echo esc_url( $torantejarat_url ); ?>"><?php if ( $torantejarat_home ) { torantejarat_icon( 'home' ); } ?><span><?php echo esc_html( $torantejarat_label ); ?></span></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol></nav>
	<?php
	$torantejarat_ld = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(),
	);
	$torantejarat_pos = 0;
	foreach ( $trail as $torantejarat_crumb ) {
		++$torantejarat_pos;
		$torantejarat_ld['itemListElement'][] = array(
			'@type'    => 'ListItem',
			'position' => $torantejarat_pos,
			'name'     => (string) $torantejarat_crumb['label'],
			'item'     => ! empty( $torantejarat_crumb['url'] ) ? esc_url_raw( $torantejarat_crumb['url'] ) : esc_url_raw( home_url( add_query_arg( null, null ) ) ),
		);
	}
	/* Rank Math handoff: if the client enables its breadcrumb/schema module,
	   set the torantejarat_breadcrumb_schema filter to false to avoid a
	   duplicate BreadcrumbList. */
	if ( apply_filters( 'torantejarat_breadcrumb_schema', true ) ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $torantejarat_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}

/**
 * Home crumb shared by every trail.
 */
function torantejarat_home_crumb() {
	return array( 'label' => __( 'خانه', 'torantejarat' ), 'url' => home_url( '/' ) );
}

function torantejarat_menu_title( $location ) {
	$locations = get_nav_menu_locations();
	$menu      = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : false;
	return $menu ? $menu->name : '';
}

function torantejarat_post_categories() {
	$categories = get_the_category();
	if ( $categories ) {
		$category = $categories[0];
		echo '<a href="' . esc_url( get_category_link( $category ) ) . '">' . esc_html( $category->name ) . '</a>';
	}
}

function torantejarat_article_links( $blocks ) {
	$links = array();
	foreach ( $blocks as $block ) {
		$anchor = $block['attrs']['anchor'] ?? '';
		if ( 'core/heading' === $block['blockName'] && is_string( $anchor ) && '' !== $anchor && ! preg_match( '/\s/u', $anchor ) ) {
			$title = trim( html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			if ( preg_match( '/[^\s\x{00a0}\x{200b}\x{200c}\x{feff}]/u', $title ) ) {
				$links[] = array( 'id' => $anchor, 'title' => $title );
			}
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$links = array_merge( $links, torantejarat_article_links( $block['innerBlocks'] ) );
		}
	}
	$unique = array();
	foreach ( $links as $link ) {
		if ( ! isset( $unique[ $link['id'] ] ) ) { $unique[ $link['id'] ] = $link; }
	}
	return array_values( $unique );
}

function torantejarat_content_menu_items( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) { return array(); }
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	$cards = array();
	foreach ( $items ?: array() as $item ) {
		if ( 'post_type' !== $item->type || ! in_array( $item->object, array( 'post', 'page' ), true ) ) { continue; }
		$content = get_post( $item->object_id );
		if ( ! $content || ! is_post_publicly_viewable( $content ) || post_password_required( $content ) ) { continue; }
		$cards[] = array( 'post' => $content, 'label' => $item->title );
	}
	return $cards;
}

function torantejarat_home_content_regions() {
	$regions = array( 'benefits' => array(), 'body' => array(), 'faq' => array() );
	if ( post_password_required() ) { return $regions; }
	foreach ( parse_blocks( get_the_content() ) as $block ) {
		$region  = 'body';
		$class_name = $block['attrs']['className'] ?? '';
		$classes = is_string( $class_name ) ? preg_split( '/\s+/', $class_name ) : array();
		if ( 'core/group' === $block['blockName'] ) {
			if ( in_array( 'torantejarat-benefit-strip', $classes, true ) ) { $region = 'benefits'; }
			if ( in_array( 'torantejarat-home-faq', $classes, true ) ) { $region = 'faq'; }
		}
		$regions[ $region ][] = $block;
	}
	return $regions;
}

/**
 * Format a number with Persian digits and ٬ separator.
 */
function torantejarat_fa_number( $number ) {
	$formatted = number_format( (float) $number, 0, '', '٬' );
	return str_replace( array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), $formatted );
}

/**
 * Card price markup matching the HTML template: amount + تومان on the end side.
 */
function torantejarat_card_price( $product ) {
	if ( ! $product instanceof \WC_Product ) { return ''; }
	$price = $product->get_price();
	if ( '' === $price || null === $price ) { return ''; }
	$currency = '<span>' . esc_html__( 'تومان', 'torantejarat' ) . '</span>';
	if ( $product->is_on_sale() ) {
		$regular = $product->get_regular_price();
		if ( '' !== $regular && (float) $regular > (float) $price ) {
			return '<strong><del>' . esc_html( torantejarat_fa_number( $regular ) ) . ' ' . $currency . '</del> <ins>' . esc_html( torantejarat_fa_number( $price ) ) . ' ' . $currency . '</ins></strong>';
		}
	}
	return '<strong>' . esc_html( torantejarat_fa_number( $price ) ) . ' ' . $currency . '</strong>';
}

/**
 * Home service cards — exact index.html copy (rent / repairs / academy).
 *
 * BLOCK CONTRACT: when these cards become Gutenberg blocks, the dynamic
 * block MUST reuse torantejarat_home_services() for its default attributes
 * and torantejarat_render_home_services() as its render_callback, so the
 * editor preview and the frontend markup stay byte-identical.
 * Item shape: array( 'icon', 'title', 'text', 'link', 'slug' ).
 */
function torantejarat_home_services() {
	$services = array(
		array( 'icon' => 'clock', 'title' => __( 'برای یک پروژه نیاز دارید؟', 'torantejarat' ), 'text' => __( 'اطلاعات پروژه را برای بررسی امکان اجاره آماده کنید.', 'torantejarat' ), 'link' => __( 'بررسی اجاره', 'torantejarat' ), 'slug' => 'rent' ),
		array( 'icon' => 'wrench', 'title' => __( 'دستگاه نیاز به بررسی دارد؟', 'torantejarat' ), 'text' => __( 'مدل دستگاه و نشانه‌های خرابی را در یک درخواست جمع کنید.', 'torantejarat' ), 'link' => __( 'درخواست بررسی', 'torantejarat' ), 'slug' => 'repairs' ),
		array( 'icon' => 'book', 'title' => __( 'دقیق‌تر کار کنید', 'torantejarat' ), 'text' => __( 'راهنماهای انتخاب، آماده‌سازی و نگهداری دستگاه را بخوانید.', 'torantejarat' ), 'link' => __( 'آموزش و نگهداری', 'torantejarat' ), 'slug' => 'academy' ),
	);
	return apply_filters( 'torantejarat_home_services', $services );
}

function torantejarat_home_service_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page && is_post_publicly_viewable( $page ) ) {
		return get_permalink( $page );
	}
	return home_url( '/' );
}

function torantejarat_render_home_services() {
	?>
	<section class="torantejarat-section"><div class="torantejarat-container">
		<div class="torantejarat-section-heading">
			<div>
				<span class="torantejarat-eyebrow"><?php esc_html_e( 'همراه تجهیزات شما', 'torantejarat' ); ?></span>
				<h2><?php esc_html_e( 'از انتخاب تا نگهداری', 'torantejarat' ); ?></h2>
				<p><?php esc_html_e( 'مسیر خدمات را متناسب با نیازتان دنبال کنید.', 'torantejarat' ); ?></p>
			</div>
		</div>
		<div class="torantejarat-service-grid">
			<?php foreach ( torantejarat_home_services() as $torantejarat_service ) : ?>
				<a class="torantejarat-service-card" href="<?php echo esc_url( torantejarat_home_service_url( $torantejarat_service['slug'] ) ); ?>">
					<?php torantejarat_icon( $torantejarat_service['icon'] ); ?>
					<h3><?php echo esc_html( $torantejarat_service['title'] ); ?></h3>
					<p><?php echo esc_html( $torantejarat_service['text'] ); ?></p>
					<span class="torantejarat-text-link"><?php echo esc_html( $torantejarat_service['link'] ); ?> <?php torantejarat_icon( 'arrow' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php
}

function torantejarat_home_content_region( $blocks ) {
	$content = serialize_blocks( $blocks );
	if ( '' === trim( $content ) ) { return; }
	$html = apply_filters( 'the_content', $content );
	if ( '' === trim( $html ) ) { return; }
	echo '<div class="torantejarat-authored-sections">';
	echo $html;
	echo '</div>';
}
