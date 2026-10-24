<?php
/** Source Home sections as editable native WP content, not a Theme metadata model.
 * @package ToranTejarat
 */
namespace ToranTejarat\Theme;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_register_patterns() {
	register_block_pattern_category( 'torantejarat', array( 'label' => __( 'توران تجارت — چیدمان‌های منبع', 'torantejarat' ) ) );
	register_block_pattern(
		'torantejarat/benefits',
		array(
			'title' => __( 'خانه — نوار نکته‌ها', 'torantejarat' ),
			'categories' => array( 'torantejarat' ),
			'postTypes' => array( 'page' ),
			'content' => <<<'HTML'
<!-- wp:group {"className": "torantejarat-benefit-strip", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-benefit-strip"><!-- wp:group {"className": "torantejarat-container", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-container"><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML
		)
	);
	register_block_pattern(
		'torantejarat/field-video',
		array(
			'title' => __( 'خانه — متن و ویدئو', 'torantejarat' ),
			'categories' => array( 'torantejarat' ),
			'postTypes' => array( 'page' ),
			'content' => <<<'HTML'
<!-- wp:group {"className": "torantejarat-section torantejarat-field-section", "tagName": "section", "layout": {"type": "default"}} -->
<section class="wp-block-group torantejarat-section torantejarat-field-section"><!-- wp:group {"className": "torantejarat-container torantejarat-field-grid", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-container torantejarat-field-grid"><!-- wp:group {"className": "torantejarat-field-copy", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-field-copy"><!-- wp:heading -->
<h2 class="wp-block-heading"></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className": "torantejarat-video-panel", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-video-panel"><!-- wp:video -->
<figure class="wp-block-video"><video controls playsinline preload="none"></video></figure>
<!-- /wp:video -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML
		)
	);
	register_block_pattern(
		'torantejarat/faq',
		array(
			'title' => __( 'خانه — پرسش و پاسخ', 'torantejarat' ),
			'categories' => array( 'torantejarat' ),
			'postTypes' => array( 'page' ),
			'content' => <<<'HTML'
<!-- wp:group {"className": "torantejarat-section torantejarat-home-faq", "tagName": "section", "layout": {"type": "default"}} -->
<section class="wp-block-group torantejarat-section torantejarat-home-faq"><!-- wp:group {"className": "torantejarat-container torantejarat-faq-grid", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-container torantejarat-faq-grid"><!-- wp:group {"className": "torantejarat-faq-intro", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-faq-intro"><!-- wp:heading -->
<h2 class="wp-block-heading"></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className": "torantejarat-faq", "layout": {"type": "default"}} -->
<div class="wp-block-group torantejarat-faq"><!-- wp:details {"className":"torantejarat-faq-item"} -->
<details class="wp-block-details torantejarat-faq-item"><summary></summary><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<!-- wp:details {"className":"torantejarat-faq-item"} -->
<details class="wp-block-details torantejarat-faq-item"><summary></summary><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<!-- wp:details {"className":"torantejarat-faq-item"} -->
<details class="wp-block-details torantejarat-faq-item"><summary></summary><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
HTML
		)
	);
}

/** Prune only empty source slots at render time; saved Page content is never rewritten. */
function torantejarat_render_content_slot( $html, $block ) {
	if ( is_admin() ) { return $html; }
	$class_name = $block['attrs']['className'] ?? '';
	$classes = is_string( $class_name ) ? preg_split( '/\s+/', $class_name ) : array();
	$slots   = array( 'torantejarat-benefit-strip', 'torantejarat-field-section', 'torantejarat-field-copy', 'torantejarat-video-panel', 'torantejarat-home-faq', 'torantejarat-faq-intro', 'torantejarat-faq', 'torantejarat-faq-item' );
	if ( ! array_intersect( $slots, $classes ) ) { return $html; }

	$content_html = $html;
	if ( in_array( 'torantejarat-faq-item', $classes, true ) ) {
		if ( ! preg_match( '/<summary\b[^>]*>(.*?)<\/summary>/is', $html, $summary ) || ! torantejarat_slot_has_text( $summary[1] ) ) {
			return ''; // CONTENT PENDING: never invent an accordion label.
		}
		$content_html = preg_replace( '/<summary\b[^>]*>.*?<\/summary>/is', '', $html, 1 );
	}
	$video_panel = in_array( 'torantejarat-video-panel', $classes, true );
	if ( ! $video_panel && torantejarat_slot_has_text( $content_html ) ) { return $html; }
	$media_tags = $video_panel ? array( 'VIDEO', 'SOURCE', 'IFRAME' ) : array( 'IMG', 'VIDEO', 'AUDIO', 'SOURCE', 'IFRAME' );
	$tags = new \WP_HTML_Tag_Processor( $content_html );
	while ( $tags->next_tag() ) {
		if ( in_array( $tags->get_tag(), $media_tags, true ) ) {
			$src = $tags->get_attribute( 'src' );
			if ( is_string( $src ) && '' !== esc_url( trim( $src ) ) ) { return $html; }
		}
	}
	return ''; // CONTENT PENDING: no empty band, player or accordion in production.
}

function torantejarat_slot_has_text( $html ) {
	$text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( '/[\s\x{00a0}\x{200b}\x{200c}\x{feff}]+/u', '', $text );
	return is_string( $text ) && '' !== $text;
}
