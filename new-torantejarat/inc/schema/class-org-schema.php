<?php
/**
 * Organization Schema — بلکوکم.
 * Reads from theme settings only; no storage logic here.
 * Disable via the torantejarat_org_schema filter (Rank Math handoff).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TT_Org_Schema {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_head', array( $this, 'render' ), 5 );
	}

	public function render() {
		if ( ! is_front_page() ) {
			return;
		}
		if ( ! apply_filters( 'torantejarat_org_schema', true ) ) {
			return;
		}
		$data = $this->build();
		if ( empty( $data['name'] ) ) {
			return;
		}
		echo '<script type="application/ld+json">'
			. wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
			. '</script>' . "\n";
	}

	private function build() {
		$name  = torantejarat_option( 'business', 'business_name' );
		if ( '' === $name ) {
			$name = get_bloginfo( 'name' );
		}
		$type  = torantejarat_option( 'business', 'business_type', 'Store' );
		$city  = torantejarat_option( 'business', 'city' );
		$range = torantejarat_option( 'business', 'price_range' );

		$allowed_types = array( 'Store', 'OnlineStore', 'LocalBusiness', 'ProfessionalService' );
		if ( ! in_array( $type, $allowed_types, true ) ) {
			$type = 'Store';
		}

		$phone_intl = $this->normalize_phone( (string) torantejarat_option( 'contact', 'phone' ) );

		$same_as = array_values(
			array_filter(
				array(
					torantejarat_option( 'social', 'instagram' ),
					torantejarat_option( 'social', 'telegram' ),
					torantejarat_option( 'social', 'aparat' ),
					torantejarat_option( 'social', 'youtube' ),
				)
			)
		);

		$street = torantejarat_option( 'contact', 'address' );

		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => $type,
			'name'     => $name,
			'url'      => home_url( '/' ),
		);

		if ( '' !== $phone_intl ) {
			$schema['telephone'] = $phone_intl;
		}

		$address = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'addressCountry'  => 'IR',
				'addressLocality' => $city,
				'streetAddress'   => $street ? $street : null,
			)
		);
		if ( count( $address ) > 2 ) {
			$schema['address'] = $address;
		}

		if ( '' !== $range ) {
			$schema['priceRange'] = $range;
		}

		if ( ! empty( $same_as ) ) {
			$schema['sameAs'] = $same_as;
		}

		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $logo_url ) {
				$schema['logo'] = array(
					'@type' => 'ImageObject',
					'url'   => $logo_url,
				);
			}
		}

		return $schema;
	}

	/**
	 * Iranian numbers to E.164.
	 * 09121234567 → +989121234567; 9121234567 → +989121234567;
	 * +98… / 98… pass through; anything else → ''.
	 *
	 * @param string $raw Raw setting value.
	 * @return string
	 */
	private function normalize_phone( $raw ) {
		$clean = preg_replace( '/[^0-9+]/', '', $raw );
		if ( '' === $clean || '+' === $clean ) {
			return '';
		}
		if ( str_starts_with( $clean, '+98' ) && ctype_digit( substr( $clean, 1 ) ) ) {
			return $clean;
		}
		if ( str_starts_with( $clean, '98' ) && 12 === strlen( $clean ) && ctype_digit( $clean ) ) {
			return '+' . $clean;
		}
		if ( str_starts_with( $clean, '0' ) && 11 === strlen( $clean ) && ctype_digit( $clean ) ) {
			return '+98' . substr( $clean, 1 );
		}
		if ( 10 === strlen( $clean ) && '9' === $clean[0] && ctype_digit( $clean ) ) {
			return '+98' . $clean;
		}
		return '';
	}
}

TT_Org_Schema::get_instance();
