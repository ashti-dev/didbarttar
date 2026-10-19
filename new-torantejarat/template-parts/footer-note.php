<?php
/**
 * Footer note — brand note by default; optional preview/sample notice
 * controlled by theme settings (general tab, "preview_note").
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_footer_preview_note() {
	$enabled = torantejarat_option( 'general', 'preview_note', 'no' );
	if ( 'yes' !== $enabled ) {
		echo '<div class="torantejarat-footer-note">';
		echo esc_html__( 'تجهیزات تخصصی بازرسی تصویری', 'torantejarat' ) . '<br>';
		echo esc_html__( 'انتخاب و خرید با مشخصات روشن و مقایسه دقیق.', 'torantejarat' );
		echo '</div>';
		return;
	}
	echo '<div class="torantejarat-footer-note is-preview">';
	echo esc_html__( 'نسخه پیش‌نمایش', 'torantejarat' ) . '<br>';
	echo esc_html__( 'قیمت‌ها و مشخصات، داده‌های نمونه‌اند.', 'torantejarat' );
	echo '</div>';
}
