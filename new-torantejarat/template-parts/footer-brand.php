<?php
/**
 * Footer brand block — exact copy of index.html footer brand.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_blog_name = get_bloginfo( 'name' ) ?: __( 'توران تجارت', 'torantejarat' );
$torantejarat_blog_desc = get_bloginfo( 'description' );
?>
<div>
	<a class="torantejarat-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $torantejarat_blog_name ); ?>">
		<span class="torantejarat-brand-mark"><?php torantejarat_icon( 'scan' ); ?></span>
		<span><?php echo esc_html( $torantejarat_blog_name ); ?><small><?php echo esc_html( $torantejarat_blog_desc ); ?></small></span>
	</a>
	<p><?php esc_html_e( 'ابزار مناسب برای هر نقطه دور از دسترس.', 'torantejarat' ); ?><br><?php esc_html_e( 'انتخاب، مقایسه و شناخت دوربین‌های بازرسی.', 'torantejarat' ); ?></p>
</div>
