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
	<?php
	$torantejarat_phone   = torantejarat_option( 'contact', 'phone' );
	$torantejarat_phone_d = torantejarat_option( 'contact', 'phone_display', $torantejarat_phone );
	$torantejarat_socials = array(
		'instagram' => __( 'اینستاگرام', 'torantejarat' ),
		'telegram'  => __( 'تلگرام', 'torantejarat' ),
		'aparat'    => __( 'آپارات', 'torantejarat' ),
		'youtube'   => __( 'یوتیوب', 'torantejarat' ),
	);
	$torantejarat_social_links = array();
	foreach ( $torantejarat_socials as $torantejarat_key => $torantejarat_label ) {
		$torantejarat_url = torantejarat_option( 'social', $torantejarat_key );
		if ( $torantejarat_url ) {
			$torantejarat_social_links[ $torantejarat_key ] = array( 'label' => $torantejarat_label, 'url' => $torantejarat_url );
		}
	}
	if ( $torantejarat_phone || $torantejarat_social_links ) :
		?>
		<div class="torantejarat-footer-contact">
			<?php if ( $torantejarat_phone ) : ?>
				<a class="torantejarat-footer-phone" href="tel:<?php echo esc_attr( $torantejarat_phone ); ?>"><?php echo esc_html( $torantejarat_phone_d ); ?></a>
			<?php endif; ?>
			<?php foreach ( $torantejarat_social_links as $torantejarat_social ) : ?>
				<a href="<?php echo esc_url( $torantejarat_social['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $torantejarat_social['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
