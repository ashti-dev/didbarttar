<?php
/**
 * Contact info cards driven by theme settings.
 * Renders only the channels the owner has filled in.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_cards = array();

$torantejarat_phone   = torantejarat_option( 'contact', 'phone' );
$torantejarat_phone_d = torantejarat_option( 'contact', 'phone_display', $torantejarat_phone );
if ( $torantejarat_phone ) {
	$torantejarat_cards[] = array(
		'icon'  => 'phone',
		'title' => __( 'تلفن تماس', 'torantejarat' ),
		'link'  => 'tel:' . $torantejarat_phone,
		'text'  => $torantejarat_phone_d,
	);
}
$torantejarat_whatsapp = torantejarat_option( 'contact', 'whatsapp' );
if ( $torantejarat_whatsapp ) {
	$torantejarat_cards[] = array(
		'icon'  => 'chat',
		'title' => __( 'واتساپ', 'torantejarat' ),
		'link'  => 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $torantejarat_whatsapp ),
		'text'  => __( 'گفت‌وگو در واتساپ', 'torantejarat' ),
	);
}
$torantejarat_email = torantejarat_option( 'contact', 'email' );
if ( $torantejarat_email ) {
	$torantejarat_cards[] = array(
		'icon'  => 'mail',
		'title' => __( 'ایمیل', 'torantejarat' ),
		'link'  => 'mailto:' . $torantejarat_email,
		'text'  => $torantejarat_email,
	);
}
$torantejarat_address = torantejarat_option( 'contact', 'address' );
if ( $torantejarat_address ) {
	$torantejarat_cards[] = array(
		'icon'  => 'pin',
		'title' => __( 'نشانی', 'torantejarat' ),
		'link'  => '',
		'text'  => $torantejarat_address,
	);
}
if ( ! $torantejarat_cards ) {
	return;
}
?>
<div class="torantejarat-contact-cards">
	<?php foreach ( $torantejarat_cards as $torantejarat_card ) : ?>
		<div class="torantejarat-contact-card">
			<span class="torantejarat-contact-card-icon"><?php torantejarat_icon( $torantejarat_card['icon'] ); ?></span>
			<div>
				<h2><?php echo esc_html( $torantejarat_card['title'] ); ?></h2>
				<?php if ( $torantejarat_card['link'] ) : ?>
					<a href="<?php echo esc_url( $torantejarat_card['link'] ); ?>"<?php echo str_starts_with( $torantejarat_card['link'], 'http' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $torantejarat_card['text'] ); ?></a>
				<?php else : ?>
					<p><?php echo esc_html( $torantejarat_card['text'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
