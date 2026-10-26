<?php
/**
 * Settings panel navigation map (appearance phase).
 *
 * STRUCTURE ONLY. This file declares no option key, no field, no input name and
 * no sanitize rule, and it never touches $schema: it only says which EXISTING
 * schema field belongs to which sub-section of which tab, so the panel can draw
 * a tree in the sidebar. Fields and their order always come from $schema.
 *
 * Adding a sub-section = add one entry here. A field that is not listed is not
 * lost: it falls into a trailing "سایر" group, and the self-check
 * torantejarat_panel_nav_check() reports any field that would be unplaced, so
 * the panel can never silently drop a setting.
 *
 * A tab without an entry here stays a single-level menu item (no sub-tree).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical compound identity for one schema field.
 *
 * Field keys are unique inside a tab, not across tabs. Keep the tab in every
 * self-check/search identity instead of comparing bare field keys.
 *
 * @param string $tab_key   Tab key.
 * @param string $field_key Field key.
 * @return string
 */
function torantejarat_panel_field_ref( $tab_key, $field_key ) {
	return (string) $tab_key . '/' . (string) $field_key;
}

/**
 * Build the stable DOM id used by labels, inputs and search targets.
 *
 * The boolean selects the existing input (`tt-`) or wrapper (`ttf-`) prefix;
 * no key is renamed or sanitized here. Escaping remains the renderer's job.
 *
 * @param string $tab_key   Tab key.
 * @param string $field_key Field key.
 * @param bool   $wrapper   Whether to return the searchable wrapper id.
 * @return string
 */
function torantejarat_panel_field_dom_id( $tab_key, $field_key, $wrapper = false ) {
	return ( $wrapper ? 'ttf-' : 'tt-' ) . (string) $tab_key . '-' . (string) $field_key;
}

/**
 * Sub-section definition per tab: id, label, dashicon, and the field keys that
 * belong to it (in schema order, so rendering order is unchanged).
 *
 * @return array<string,array<int,array{id:string,label:string,icon:string,desc?:string,fields:array<int,string>}>>
 */
function torantejarat_panel_nav_map() {
	return array(
		'general' => array(
			array(
				'id'     => 'topbar',
				'label'  => 'نوار بالای سایت',
				'icon'   => 'dashicons-admin-links',
				'desc'   => 'متن و مقصد لینک نواری که بالای همهٔ صفحه‌های سایت دیده می‌شود.',
				'fields' => array( 'topbar_text', 'topbar_link' ),
			),
			array(
				'id'     => 'preview',
				'label'  => 'اعلان پیش‌نمایش',
				'icon'   => 'dashicons-visibility',
				'desc'   => 'فقط متن‌های اطلاع‌رسانی نسخهٔ پیش‌نمایش را کنترل می‌کند؛ عمومی یا غیرعمومی بودن سایت در بخش حالت انتشار است.',
				'fields' => array( 'preview_note' ),
			),
		),
		'home'    => array(
			array(
				'id'     => 'hero',
				'label'  => 'هیرو',
				'icon'   => 'dashicons-cover-image',
				'desc'   => 'متن اصلی و دکمه‌های نخستین بخش صفحهٔ اصلی. تصاویر اسلایدر از محصولات واقعی می‌آیند.',
				'fields' => array( 'hero_kicker', 'hero_title_1', 'hero_title_2', 'hero_lead', 'hero_cta_primary', 'hero_cta_secondary', 'hero_foot_1', 'hero_foot_2' ),
			),
			array(
				'id'     => 'trust',
				'label'  => 'نوار اعتماد',
				'icon'   => 'dashicons-shield-alt',
				'desc'   => 'چهار متن ثابت نوار اعتماد؛ آیکون هر مورد در قالب تعیین شده است.',
				'fields' => array( 'benefit_1', 'benefit_2', 'benefit_3', 'benefit_4' ),
			),
			array(
				'id'     => 'category',
				'label'  => 'دسته‌ها',
				'icon'   => 'dashicons-category',
				'desc'   => 'عنوان و توضیح بخش دسته‌ها. نام، تصویر و تعداد کارت‌ها از دسته‌های واقعی WooCommerce خوانده می‌شود.',
				'fields' => array( 'cat_eyebrow', 'cat_title', 'cat_text' ),
			),
			array(
				'id'     => 'field',
				'label'  => 'بخش فنی و ویدیو',
				'icon'   => 'dashicons-video-alt3',
				'desc'   => 'متن بخش فنی، چک‌لیست و برچسب‌های ویدیو. فایل ویدیوی فعلی از Assets قالب پخش می‌شود.',
				'fields' => array( 'field_eyebrow', 'field_title', 'field_text', 'field_check_1', 'field_check_2', 'field_check_3', 'field_link_label', 'video_label_1', 'video_label_2', 'video_aria', 'video_caption' ),
			),
			array(
				'id'     => 'blog',
				'label'  => 'مجله',
				'icon'   => 'dashicons-welcome-write-blog',
				'desc'   => 'عنوان و لینک بخش مجله؛ کارت‌ها از نوشته‌های واقعی منتشرشده ساخته می‌شوند.',
				'fields' => array( 'blog_eyebrow', 'blog_title', 'blog_text', 'blog_link' ),
			),
			array(
				'id'     => 'faq',
				'label'  => 'پرسش‌های متداول',
				'icon'   => 'dashicons-editor-help',
				'desc'   => 'عنوان بخش و فهرست پرسش‌ها. سه ردیف نخست متن پیش‌فرض متناسب با جای خود دارند.',
				'fields' => array( 'faq_eyebrow', 'faq_title', 'faq_link', 'faq' ),
			),
		),
		'contact' => array(
			array(
				'id'     => 'channels',
				'label'  => 'راه‌های ارتباطی',
				'icon'   => 'dashicons-phone',
				'desc'   => 'شماره‌ها و ایمیل واقعی کسب‌وکار؛ این موارد در کارت تماس، فوتر و برخی داده‌های Schema استفاده می‌شوند.',
				'fields' => array( 'phone', 'phone_display', 'whatsapp', 'email' ),
			),
			array(
				'id'     => 'location',
				'label'  => 'نشانی و حضور حضوری',
				'icon'   => 'dashicons-location-alt',
				'desc'   => 'نشانی، ساعت کاری و کد نقشهٔ صفحهٔ تماس. خالی‌گذاشتن نقشه، جعبهٔ راهنما را نمایش می‌دهد.',
				'fields' => array( 'address', 'working_hours', 'map_embed' ),
			),
		),
		'social'  => array(
			array(
				'id'     => 'profiles',
				'label'  => 'پروفایل‌ها',
				'icon'   => 'dashicons-share',
				'desc'   => 'نشانی کامل چهار پروفایل رسمی؛ فقط موارد پرشده در سایت نمایش داده می‌شوند.',
				'fields' => array( 'instagram', 'telegram', 'aparat', 'youtube' ),
			),
		),
		'footer'  => array(
			array(
				'id'     => 'content',
				'label'  => 'متن‌های فوتر',
				'icon'   => 'dashicons-info-outline',
				'desc'   => 'متن کپیرایت و نوار پایین. لینک ستون‌های فوتر از منوهای بومی وردپرس مدیریت می‌شود.',
				'fields' => array( 'copyright_text', 'bar_text' ),
			),
		),
		'login'   => array(
			array(
				'id'     => 'content',
				'label'  => 'محتوای ورود وردپرس',
				'icon'   => 'dashicons-id',
				'desc'   => 'لوگو و متن صفحهٔ بومی wp-login.php؛ این موارد ظاهر حساب WooCommerce را تغییر نمی‌دهند.',
				'fields' => array( 'login_logo', 'login_title', 'login_text' ),
			),
			array(
				'id'     => 'appearance',
				'label'  => 'ظاهر ورود وردپرس',
				'icon'   => 'dashicons-art',
				'desc'   => 'رنگ پس‌زمینه و Accent صفحهٔ بومی ورود وردپرس.',
				'fields' => array( 'login_bg', 'login_accent' ),
			),
			array(
				'id'     => 'access',
				'label'  => 'قواعد دسترسی',
				'icon'   => 'dashicons-shield',
				'desc'   => 'نمایش لینک ثبت‌نام و بازیابی رمز. غیرفعال‌کردن بازیابی رمز فقط با تصمیم آگاهانه انجام شود.',
				'fields' => array( 'login_hide_register', 'login_hide_lostpassword' ),
			),
		),
		'business' => array(
			array(
				'id'     => 'organization',
				'label'  => 'هویت سازمان',
				'icon'   => 'dashicons-store',
				'desc'   => 'نام و نوع واقعی کسب‌وکار برای دادهٔ ساختاریافتهٔ سازمان.',
				'fields' => array( 'business_name', 'business_type' ),
			),
			array(
				'id'     => 'schema-details',
				'label'  => 'جزئیات Schema',
				'icon'   => 'dashicons-location',
				'desc'   => 'فقط دادهٔ واقعی و تأییدشده وارد شود؛ مقدار نامشخص از خروجی Schema حذف می‌شود.',
				'fields' => array( 'city', 'price_range' ),
			),
		),
	);
}

/**
 * Non-field utility routes. These are deliberately separate from the field
 * map: an empty field group would be discarded by torantejarat_panel_nav(),
 * and the field renderer must never mistake a utility page for "all fields".
 *
 * @return array<string,array<int,array{id:string,label:string,icon:string,desc:string,meta:string}>>
 */
function torantejarat_panel_utility_map() {
	return array(
		'general' => array(
			array(
				'id'    => 'publish',
				'label' => 'حالت انتشار',
				'icon'  => 'dashicons-visibility',
				'desc'  => 'تغییر وضعیت پیش‌نمایش/عمومی با دو Option بومی وردپرس و WooCommerce.',
				'meta'  => 'فرم بومی',
			),
		),
	);
}

/**
 * Build the navigation tree from $schema plus the map above.
 *
 * @param array $schema The real settings schema.
 * @return array<string,array{label:string,icon:string,groups:array<int,array{id:string,label:string,icon:string,desc:string,fields:array<int,string>}>}>
 */
function torantejarat_panel_nav( array $schema ) {
	$map  = torantejarat_panel_nav_map();
	$tree = array();

	foreach ( $schema as $tab_key => $tab ) {
		$defined = isset( $map[ $tab_key ] ) ? $map[ $tab_key ] : array();
		$groups  = array();
		$placed  = array();

		foreach ( $defined as $group ) {
			$fields = array();
			foreach ( $group['fields'] as $field_key ) {
				if ( isset( $tab['fields'][ $field_key ] ) ) {
					$fields[]               = $field_key;
					$placed[ $field_key ]   = true;
				}
			}
			if ( $fields ) {
				$groups[] = array(
					'id'     => $group['id'],
					'label'  => $group['label'],
					'icon'   => $group['icon'],
					'desc'   => isset( $group['desc'] ) ? (string) $group['desc'] : '',
					'fields' => $fields,
				);
			}
		}

		$rest = array();
		foreach ( $tab['fields'] as $field_key => $field ) {
			if ( empty( $placed[ $field_key ] ) ) {
				$rest[] = $field_key;
			}
		}
		if ( $rest ) {
			$groups[] = array(
				'id'     => 'more',
				'label'  => 'سایر',
				'icon'   => 'dashicons-ellipsis',
				'desc'   => 'فیلدهایی که هنوز در نقشهٔ ناوبری دسته‌بندی نشده‌اند.',
				'fields' => $rest,
			);
		}

		$tree[ $tab_key ] = array(
			'label'  => $tab['label'],
			'icon'   => $tab['icon'],
			'groups' => $groups,
		);
	}

	return $tree;
}

/**
 * Self-check used by the acceptance test: every schema field is placed exactly
 * once, and the map references no field that no longer exists.
 *
 * @param array $schema The real settings schema.
 * @return array{unplaced:array<int,string>,duplicated:array<int,string>,unknown:array<int,string>}
 */
function torantejarat_panel_nav_check( array $schema ) {
	$tree      = torantejarat_panel_nav( $schema );
	$seen      = array();
	$duplicate = array();

	foreach ( $tree as $tab_key => $tab ) {
		foreach ( $tab['groups'] as $group ) {
			foreach ( $group['fields'] as $field_key ) {
				$ref = torantejarat_panel_field_ref( $tab_key, $field_key );
				if ( isset( $seen[ $ref ] ) ) {
					$duplicate[] = $ref;
				}
				$seen[ $ref ] = true;
			}
		}
	}

	$unplaced = array();
	foreach ( $schema as $tab_key => $tab ) {
		foreach ( $tab['fields'] as $field_key => $field ) {
			$ref = torantejarat_panel_field_ref( $tab_key, $field_key );
			if ( ! isset( $seen[ $ref ] ) ) {
				$unplaced[] = $ref;
			}
		}
	}

	$unknown = array();
	foreach ( torantejarat_panel_nav_map() as $tab_key => $groups ) {
		foreach ( $groups as $group ) {
			foreach ( $group['fields'] as $field_key ) {
				if ( ! isset( $schema[ $tab_key ]['fields'][ $field_key ] ) ) {
					$unknown[] = torantejarat_panel_field_ref( $tab_key, $field_key );
				}
			}
		}
	}

	return array(
		'unplaced'   => array_values( array_unique( $unplaced ) ),
		'duplicated' => array_values( array_unique( $duplicate ) ),
		'unknown'    => array_values( array_unique( $unknown ) ),
	);
}

/**
 * Search index for the panel-wide combobox.
 *
 * Built from $schema plus the nav tree — never from the DOM and never from
 * stored values. One entry per field, carrying its section and sub-section
 * labels so the UI can show «بخش › زیربخش › فیلد» and jump to the field.
 *
 * @param array $schema The real settings schema.
 * @return array<int,array<string,string>>
 */
function torantejarat_panel_search_index( array $schema ) {
	$tree  = torantejarat_panel_nav( $schema );
	$index = array();

	foreach ( $tree as $section => $tab ) {
		foreach ( $tab['groups'] as $group ) {
			foreach ( $group['fields'] as $field_key ) {
				$field   = $schema[ $section ]['fields'][ $field_key ];
				$index[] = array(
					'section'       => (string) $section,
					'section_label' => (string) $tab['label'],
					'sub'           => (string) $group['id'],
					'sub_label'     => (string) $group['label'],
					'field'         => (string) $field_key,
					'field_ref'     => torantejarat_panel_field_ref( $section, $field_key ),
					'field_label'   => (string) $field['label'],
					'field_id'      => torantejarat_panel_field_dom_id( $section, $field_key, true ),
					'text'          => $tab['label'] . ' › ' . $group['label'] . ' › ' . $field['label'],
				);
			}
		}
	}

	return $index;
}

/**
 * Publish-readiness items — display only.
 *
 * Read-only by design: it inspects already-stored options plus admin_email and
 * returns a list of open items. It declares no option key, writes nothing, and
 * changes no sanitize, nonce or $schema rule. An empty list means
 * «آماده انتشار».
 *
 * @return array<int,array{label:string,section:string,sub:string,url?:string}>
 */
function torantejarat_panel_readiness() {
	$items   = array();
	$contact = get_option( 'torantejarat_contact', array() );
	$contact = is_array( $contact ) ? $contact : array();

	// Sample (demo) contact values that must not survive to launch. Each item is
	// "open" while ANY of its needles is still present, so the href and the
	// display form of the phone count as one single item.
	$demo = array(
		array(
			'label' => 'شماره تماس نمایشی مانده است',
			'sub'   => 'channels',
			'any'   => array(
				array( 'phone', '02112345678' ),
				array( 'phone_display', '۱۲۳۴' ),
			),
		),
		array(
			'label' => 'شماره واتساپ نمایشی مانده است',
			'sub'   => 'channels',
			'any'   => array( array( 'whatsapp', '989121234567' ) ),
		),
		array(
			'label' => 'ایمیل تماس نمایشی مانده است',
			'sub'   => 'channels',
			'any'   => array( array( 'email', 'hello@example.ir' ) ),
		),
		array(
			'label' => 'آدرس نمایشی مانده است',
			'sub'   => 'location',
			'any'   => array( array( 'address', 'نمایشی' ) ),
		),
	);
	foreach ( $demo as $item ) {
		$open = false;
		foreach ( $item['any'] as $pair ) {
			$value = isset( $contact[ $pair[0] ] ) ? (string) $contact[ $pair[0] ] : '';
			if ( '' !== $value && false !== strpos( $value, $pair[1] ) ) {
				$open = true;
				break;
			}
		}
		if ( $open ) {
			$items[] = array( 'label' => $item['label'], 'section' => 'contact', 'sub' => $item['sub'] );
		}
	}

	// Placeholder sender addresses (read-only — never changed here).
	if ( 'admin@example.com' === (string) get_option( 'admin_email', '' ) ) {
		$items[] = array(
			'label'   => 'ایمیل مدیر سایت (admin_email) روی مقدار نمونه است',
			'section' => 'general',
			'sub'     => '',
			'url'     => admin_url( 'options-general.php' ),
		);
	}
	if ( 'admin@example.com' === (string) get_option( 'woocommerce_email_from_address', '' ) ) {
		$items[] = array(
			'label'   => 'ایمیل فرستندهٔ ووکامرس روی مقدار نمونه است',
			'section' => 'general',
			'sub'     => '',
			'url'     => admin_url( 'admin.php?page=wc-settings&tab=email' ),
		);
	}

	// Publish mode: either half of preview means the site is not fully public.
	$coming_soon = (string) get_option( 'woocommerce_coming_soon', 'no' );
	$blog_public = (string) get_option( 'blog_public', '1' );
	if ( 'yes' === $coming_soon || '0' === $blog_public ) {
		$items[] = array( 'label' => 'حالت انتشار روی پیش‌نمایش/غیرعمومی است', 'section' => 'general', 'sub' => 'publish' );
	}

	// Local/dev URLs and demo social profiles — display only, never rewritten here.
	$general     = get_option( 'torantejarat_general', array() );
	$general     = is_array( $general ) ? $general : array();
	$topbar_link = isset( $general['topbar_link'] ) ? (string) $general['topbar_link'] : '';
	if ( '' !== $topbar_link && ( false !== strpos( $topbar_link, '127.0.0.1' ) || false !== strpos( $topbar_link, 'localhost' ) ) ) {
		$items[] = array( 'label' => 'لینک نوار بالای سایت روی آدرس محلی است', 'section' => 'general', 'sub' => 'topbar' );
	}

	$social = get_option( 'torantejarat_social', array() );
	$social = is_array( $social ) ? $social : array();
	foreach ( array( 'instagram', 'telegram', 'aparat', 'youtube' ) as $network ) {
		$url = isset( $social[ $network ] ) ? (string) $social[ $network ] : '';
		if ( '' !== $url && ( false !== strpos( $url, 'demo' ) || false !== strpos( $url, 'example.' ) ) ) {
			$items[] = array( 'label' => 'شبکه‌های اجتماعی نمایشی مانده‌اند', 'section' => 'social', 'sub' => 'profiles' );
			break;
		}
	}

	return $items;
}

/**
 * Resolve the corrective destination for one readiness item.
 *
 * Most items open a field group inside the theme panel. Items backed by a
 * native WordPress/WooCommerce option can provide an explicit URL instead, so
 * the user is not sent to an unrelated theme section.
 *
 * @param array $item Readiness item from torantejarat_panel_readiness().
 * @return string
 */
function torantejarat_panel_readiness_url( array $item ) {
	if ( ! empty( $item['url'] ) ) {
		return (string) $item['url'];
	}
	$section = isset( $item['section'] ) ? (string) $item['section'] : 'general';
	$sub     = isset( $item['sub'] ) ? (string) $item['sub'] : '';
	return admin_url( 'admin.php?page=torantejarat-settings&section=' . $section . ( $sub ? '&sub=' . $sub : '' ) );
}
