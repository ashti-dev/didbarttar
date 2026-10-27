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
		'percent' => 'M19 5 5 19M7.5 9.5h.01M16.5 14.5h.01',
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
		'user'    => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5',
		'shop'    => 'M4 9l1.5-5h13L20 9M4 9v11h16V9M4 9h16M9.5 20v-6h5v6',
		'factory' => 'M3 21h18M5 21V8l6-4 6 4v13M9 21v-4h6v4',
		'monitor' => 'M4 5h16v11H4zM9 20h6M12 16v4',
		'droplet' => 'M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z',
		'wifi'    => 'M5 10a10 10 0 0 1 14 0M8 13a6 6 0 0 1 8 0M12 16.5h.01',
		'globe'   => 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3.5 3 14 0 18M12 3c-3 3.5-3 14 0 18',
		'heart'   => 'M12 20s-7-4.4-9-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c-2 4.6-9 9-9 9z',
		'box'     => 'M21 8 12 3 3 8v8l9 5 9-5zM3 8l9 5 9-5M12 13v8',
		'ticket'  => 'M3 9a2 2 0 0 0 0 4v4h18v-4a2 2 0 0 0 0-4V5H3zM14 5v3M14 11v2M14 16v3',
		'shield'  => 'M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6zM9 12l2 2 4-4',
		'bell'    => 'M6 8a6 6 0 0 1 12 0c0 6 2 7 2 7H4s2-1 2-7M10 19a2 2 0 0 0 4 0',
		'ticket'  => 'M3 9a2 2 0 0 0 0 4v4h18v-4a2 2 0 0 0 0-4V5H3zM14 5v3M14 11v2M14 16v3',
		'logout'  => 'M15 4h4v16h-4M10 12h10M13 8l-4 4 4 4',
		'trash'   => 'M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13',
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
 * Gregorian → Jalali Y/m/d with Persian digits (dependency-free).
 * Input parts must already be in site timezone (e.g. WC_DateTime->date()).
 */
function torantejarat_jalali_ymd( $gy, $gm, $gd ) {
	$gy = (int) $gy;
	$gm = max( 1, min( 12, (int) $gm ) );
	$gd = max( 1, min( 31, (int) $gd ) );
	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$jy = ( $gy <= 1600 ) ? 0 : 979;
	$gy2 = $gy - ( ( $gy <= 1600 ) ? 621 : 1600 );
	$gy_next = ( $gm > 2 ) ? ( $gy2 + 1 ) : $gy2;
	$days = ( 365 * $gy2 ) + (int) ( ( $gy_next + 3 ) / 4 ) - (int) ( ( $gy_next + 99 ) / 100 ) + (int) ( ( $gy_next + 399 ) / 400 ) - 80 + $gd + $g_d_m[ $gm - 1 ];
	$jy += 33 * (int) ( $days / 12053 );
	$days %= 12053;
	$jy += 4 * (int) ( $days / 1461 );
	$days %= 1461;
	if ( $days > 365 ) {
		$jy += (int) ( ( $days - 1 ) / 365 );
		$days = ( $days - 1 ) % 365;
	}
	$jm = ( $days < 186 ) ? 1 + (int) ( $days / 31 ) : 7 + (int) ( ( $days - 186 ) / 30 );
	$jd = 1 + ( ( $days < 186 ) ? ( $days % 31 ) : ( ( $days - 186 ) % 30 ) );
	$out = sprintf( '%04d/%02d/%02d', $jy, $jm, $jd );
	return str_replace( array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), $out );
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
 * Home page copy — the single source of truth for the "home" settings tab.
 *
 * CONTRACT (Phase 3.2): every key below is a field of the `home` tab in
 * inc/settings/class-theme-settings.php, and every frontend consumer reads it
 * through torantejarat_home(). TT_Settings lazily injects these same values into its
 * schema; scalar defaults must not be duplicated in that class. Each default
 * is the exact text that was hardcoded in
 * front-page.php before the panel existed, so an empty (never saved) option
 * reproduces the previous markup byte for byte — that equality is asserted by
 * the acceptance test (MD5 of "/" with every field empty).
 *
 * Keys deliberately NOT here (data-driven, not copy): hero slides (built from
 * products by SKU), category grid, deals/products/finder/bestsellers/latest
 * partials, and the blog cards.
 *
 * @return array<string,string> Field key => default text.
 */
function torantejarat_home_defaults() {
	return array(
		'hero_kicker'        => __( 'نگاه دقیق‌تر. انتخاب مطمئن‌تر.', 'torantejarat' ),
		'hero_title_1'       => __( 'قبل از باز کردن،', 'torantejarat' ),
		'hero_title_2'       => __( 'داخلش را ببین.', 'torantejarat' ),
		'hero_lead'          => __( 'برای هر مسیر پنهان، ابزار مناسبی وجود دارد. دوربین بازرسی را بر اساس کارتان انتخاب کنید؛ با مشخصات روشن و مقایسه‌ای ساده.', 'torantejarat' ),
		'hero_cta_primary'   => __( 'پیدا کردن دوربین مناسب', 'torantejarat' ),
		'hero_cta_secondary' => __( 'راهنمای انتخاب', 'torantejarat' ),
		'hero_foot_1'        => __( 'مقایسه فنی دستگاه‌ها', 'torantejarat' ),
		'hero_foot_2'        => __( 'مسیر مشاوره تخصصی', 'torantejarat' ),
		'benefit_1'          => __( 'انتخاب بر اساس کاربرد', 'torantejarat' ),
		'benefit_2'          => __( 'مشخصات قابل مقایسه', 'torantejarat' ),
		'benefit_3'          => __( 'مسیر تعمیر و نگهداری', 'torantejarat' ),
		'benefit_4'          => __( 'راهنمای استفاده و انتخاب', 'torantejarat' ),
		'cat_eyebrow'        => __( 'از کارتان شروع کنید', 'torantejarat' ),
		'cat_title'          => __( 'کجا را می‌خواهید ببینید؟', 'torantejarat' ),
		'cat_text'           => __( 'مسیر انتخاب دستگاه مناسب، از شناخت کاربرد شروع می‌شود.', 'torantejarat' ),
		'field_eyebrow'      => __( 'پیش از تصمیم، دقیق‌تر ببینید', 'torantejarat' ),
		'field_title'        => __( 'فقط تصویر دستگاه کافی نیست؛ خروجی آن را هم ببینید.', 'torantejarat' ),
		'field_text'         => __( 'برای ارزیابی هر مدل، نمونه تصویر همان دستگاه در شرایط مشابه کارتان را درخواست کنید.', 'torantejarat' ),
		'field_check_1'      => __( 'وضوح جزئیات در فاصله کاری', 'torantejarat' ),
		'field_check_2'      => __( 'نور و میدان دید در محیط مورد نظر', 'torantejarat' ),
		'field_check_3'      => __( 'مشخص بودن مدل و شرایط تصویربرداری', 'torantejarat' ),
		'field_link_label'   => __( 'آماده‌سازی درخواست نمونه تصویر', 'torantejarat' ),
		'video_label_1'      => 'INSIDE THE FIELD',
		'video_label_2'      => 'DEMO / 01',
		'video_aria'         => __( 'ویدیوی نمایشی بازرسی مسیر', 'torantejarat' ),
		'video_caption'      => __( 'ویدیوی نمایشی قالب؛ خروجی تأییدشده هیچ‌یک از مدل‌های فروشگاه نیست.', 'torantejarat' ),
		'blog_eyebrow'       => __( 'مجله بلکوکم', 'torantejarat' ),
		'blog_title'         => __( 'قبل از خرید، بیشتر بدانید', 'torantejarat' ),
		'blog_text'          => __( 'یادداشت‌های کوتاه برای سؤال‌های مهم.', 'torantejarat' ),
		'blog_link'          => __( 'همه راهنماها', 'torantejarat' ),
		'faq_eyebrow'        => __( 'پرسش‌های متداول', 'torantejarat' ),
		'faq_title'          => __( 'از همین‌جا ابهام‌ها را برطرف کنید.', 'torantejarat' ),
		'faq_link'           => __( 'پرسش دیگری دارید؟', 'torantejarat' ),
	);
}

/**
 * Read one home page string. Empty option = built-in default, so clearing a
 * field in the panel restores the text the theme shipped with.
 *
 * @param string $key Key from torantejarat_home_defaults().
 * @return string
 */
function torantejarat_home( $key ) {
	$defaults = torantejarat_home_defaults();
	return torantejarat_option( 'home', $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Built-in home FAQ items — the fallback used when the repeater is empty.
 *
 * The fourth item on the home page is a preview-only notice gated by
 * torantejarat_is_preview(); it is intentionally NOT part of this list.
 *
 * @return array<int,array<string,string>>
 */
function torantejarat_home_faq_defaults() {
	return array(
		array(
			'q' => __( 'از کجا بفهمم کدام مدل مناسب من است؟', 'torantejarat' ),
			'a' => __( 'از راهنمای انتخاب شروع کنید. سپس قطر ورودی، شرایط محیط و محدودیت‌های مسیر را با مشخصات تأییدشده مدل تطبیق دهید.', 'torantejarat' ),
		),
		array(
			'q' => __( 'آیا همه دوربین‌ها برای محیط مرطوب مناسب‌اند؟', 'torantejarat' ),
			'a' => __( 'محدوده مقاومت هر بخش دستگاه باید از دفترچه همان مدل بررسی شود. مشخصات هد لزوماً درباره نمایشگر یا اتصال‌ها صدق نمی‌کند.', 'torantejarat' ),
		),
		array(
			'q' => __( 'برای مشاوره چه اطلاعاتی آماده کنم؟', 'torantejarat' ),
			'a' => __( 'کاربرد، قطر ورودی، طول مسیر، شرایط محیط و بودجه تقریبی. عکس محل بازرسی هم می‌تواند به بررسی بهتر کمک کند.', 'torantejarat' ),
		),
	);
}

/**
 * Home FAQ rows exactly as saved in the repeater (empty when never saved).
 *
 * @return array<int,array<string,string>>
 */
function torantejarat_home_faq_rows() {
	$rows = torantejarat_option( 'home', 'faq', array() );
	return ( is_array( $rows ) && $rows ) ? array_values( $rows ) : array();
}

/**
 * One half of one FAQ slot, with a positional fallback to the built-in text.
 *
 * The three built-in slots are bound to their position, so clearing a field
 * restores the default for that slot instead of leaving an empty <details>.
 *
 * @param int    $index Slot index (0-based).
 * @param string $part  "q" or "a".
 * @return string
 */
function torantejarat_home_faq_slot( $index, $part ) {
	$defaults = torantejarat_home_faq_defaults();
	$rows     = torantejarat_home_faq_rows();
	$value    = isset( $rows[ $index ][ $part ] ) ? (string) $rows[ $index ][ $part ] : '';
	if ( '' !== trim( $value ) ) {
		return $value;
	}
	return isset( $defaults[ $index ][ $part ] ) ? $defaults[ $index ][ $part ] : '';
}

/**
 * Extra FAQ rows past the three built-in slots.
 *
 * @return array<int,array<string,string>>
 */
function torantejarat_home_faq_extra() {
	return array_slice( torantejarat_home_faq_rows(), count( torantejarat_home_faq_defaults() ) );
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
		array( 'icon' => 'book', 'title' => __( 'دقیق‌تر کار کنید', 'torantejarat' ), 'text' => __( 'راهنماهای انتخاب، آماده‌سازی و نگهداری دستگاه را بخوانید.', 'torantejarat' ), 'link' => __( 'آموزش و نگهداری', 'torantejarat' ), 'slug' => 'blog' ),
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
