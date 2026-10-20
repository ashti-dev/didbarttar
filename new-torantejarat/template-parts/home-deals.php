<?php
/**
 * Home amazing deals — renders ONLY with 2+ genuine on-sale products.
 * Hidden otherwise: no fake urgency, ever.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! torantejarat_woocommerce_available() ) {
	return;
}

$torantejarat_deals = torantejarat_deals( 4 );
if ( count( $torantejarat_deals ) < 2 ) {
	return;
}

get_template_part(
	'template-parts/deals-panel',
	null,
	array( 'deals' => $torantejarat_deals )
);
