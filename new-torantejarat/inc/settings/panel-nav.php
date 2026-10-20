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
 * Sub-section definition per tab: id, label, dashicon, and the field keys that
 * belong to it (in schema order, so rendering order is unchanged).
 *
 * @return array<string,array<int,array{id:string,label:string,icon:string,fields:array<int,string>}>>
 */
function torantejarat_panel_nav_map() {
	return array(
		'home' => array(
			array(
				'id'     => 'hero',
				'label'  => 'هیرو',
				'icon'   => 'dashicons-cover-image',
				'fields' => array( 'hero_kicker', 'hero_title_1', 'hero_title_2', 'hero_lead', 'hero_cta_primary', 'hero_cta_secondary', 'hero_foot_1', 'hero_foot_2' ),
			),
			array(
				'id'     => 'trust',
				'label'  => 'نوار اعتماد',
				'icon'   => 'dashicons-shield-alt',
				'fields' => array( 'benefit_1', 'benefit_2', 'benefit_3', 'benefit_4' ),
			),
			array(
				'id'     => 'category',
				'label'  => 'دسته‌ها',
				'icon'   => 'dashicons-category',
				'fields' => array( 'cat_eyebrow', 'cat_title', 'cat_text' ),
			),
			array(
				'id'     => 'field',
				'label'  => 'بخش فنی و ویدیو',
				'icon'   => 'dashicons-video-alt3',
				'fields' => array( 'field_eyebrow', 'field_title', 'field_text', 'field_check_1', 'field_check_2', 'field_check_3', 'field_link_label', 'video_label_1', 'video_label_2', 'video_aria', 'video_caption' ),
			),
			array(
				'id'     => 'blog',
				'label'  => 'مجله',
				'icon'   => 'dashicons-welcome-write-blog',
				'fields' => array( 'blog_eyebrow', 'blog_title', 'blog_text', 'blog_link' ),
			),
			array(
				'id'     => 'faq',
				'label'  => 'پرسش‌های متداول',
				'icon'   => 'dashicons-editor-help',
				'fields' => array( 'faq_eyebrow', 'faq_title', 'faq_link', 'faq' ),
			),
		),
	);
}

/**
 * Build the navigation tree from $schema plus the map above.
 *
 * @param array $schema The real settings schema.
 * @return array<string,array{label:string,icon:string,groups:array<int,array{id:string,label:string,icon:string,fields:array<int,string>}>}>
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

	foreach ( $tree as $tab ) {
		foreach ( $tab['groups'] as $group ) {
			foreach ( $group['fields'] as $field_key ) {
				if ( isset( $seen[ $field_key ] ) ) {
					$duplicate[] = $field_key;
				}
				$seen[ $field_key ] = true;
			}
		}
	}

	$unplaced = array();
	foreach ( $schema as $tab ) {
		foreach ( $tab['fields'] as $field_key => $field ) {
			if ( ! isset( $seen[ $field_key ] ) ) {
				$unplaced[] = $field_key;
			}
		}
	}

	$unknown = array();
	foreach ( torantejarat_panel_nav_map() as $tab_key => $groups ) {
		foreach ( $groups as $group ) {
			foreach ( $group['fields'] as $field_key ) {
				if ( ! isset( $schema[ $tab_key ]['fields'][ $field_key ] ) ) {
					$unknown[] = $tab_key . '/' . $field_key;
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
					'field_label'   => (string) $field['label'],
					'field_id'      => 'ttf-' . $section . '-' . $field_key,
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
 * @return array<int,array{label:string,section:string,sub:string}>
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
			'any'   => array(
				array( 'phone', '02112345678' ),
				array( 'phone_display', '۱۲۳۴' ),
			),
		),
		array(
			'label' => 'شماره واتساپ نمایشی مانده است',
			'any'   => array( array( 'whatsapp', '989121234567' ) ),
		),
		array(
			'label' => 'ایمیل تماس نمایشی مانده است',
			'any'   => array( array( 'email', 'hello@example.ir' ) ),
		),
		array(
			'label' => 'آدرس نمایشی مانده است',
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
			$items[] = array( 'label' => $item['label'], 'section' => 'contact', 'sub' => '' );
		}
	}

	// Placeholder sender addresses (read-only — never changed here).
	if ( 'admin@example.com' === (string) get_option( 'admin_email', '' ) ) {
		$items[] = array( 'label' => 'ایمیل مدیر سایت (admin_email) روی مقدار نمونه است', 'section' => 'general', 'sub' => '' );
	}
	if ( 'admin@example.com' === (string) get_option( 'woocommerce_email_from_address', '' ) ) {
		$items[] = array( 'label' => 'ایمیل فرستندهٔ ووکامرس روی مقدار نمونه است', 'section' => 'general', 'sub' => '' );
	}

	// Publish mode: either half of preview means the site is not fully public.
	$coming_soon = (string) get_option( 'woocommerce_coming_soon', 'no' );
	$blog_public = (string) get_option( 'blog_public', '1' );
	if ( 'yes' === $coming_soon || '0' === $blog_public ) {
		$items[] = array( 'label' => 'حالت انتشار روی پیش‌نمایش/غیرعمومی است', 'section' => 'general', 'sub' => '' );
	}

	// Local/dev URLs and demo social profiles — display only, never rewritten here.
	$general     = get_option( 'torantejarat_general', array() );
	$general     = is_array( $general ) ? $general : array();
	$topbar_link = isset( $general['topbar_link'] ) ? (string) $general['topbar_link'] : '';
	if ( '' !== $topbar_link && ( false !== strpos( $topbar_link, '127.0.0.1' ) || false !== strpos( $topbar_link, 'localhost' ) ) ) {
		$items[] = array( 'label' => 'لینک نوار بالای سایت روی آدرس محلی است', 'section' => 'general', 'sub' => '' );
	}

	$social = get_option( 'torantejarat_social', array() );
	$social = is_array( $social ) ? $social : array();
	foreach ( array( 'instagram', 'telegram', 'aparat', 'youtube' ) as $network ) {
		$url = isset( $social[ $network ] ) ? (string) $social[ $network ] : '';
		if ( '' !== $url && ( false !== strpos( $url, 'demo' ) || false !== strpos( $url, 'example.' ) ) ) {
			$items[] = array( 'label' => 'شبکه‌های اجتماعی نمایشی مانده‌اند', 'section' => 'social', 'sub' => '' );
			break;
		}
	}

	return $items;
}
