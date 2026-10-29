<?php
/**
 * Native GET search with source header-search markup.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_search_id = wp_unique_id( 'torantejarat-search-' );
/* Header searches products on the dedicated /search/ page; everywhere else
   (blog sidebar, 404, search page) keeps the native site search (?s=). */
$torantejarat_product_search = ! empty( $args['torantejarat_product_search'] );
$torantejarat_action        = $torantejarat_product_search ? home_url( '/search/' ) : home_url( '/' );
$torantejarat_name          = $torantejarat_product_search ? 'q' : 's';
$torantejarat_value         = $torantejarat_product_search
	? ( isset( $_GET['q'] ) && is_string( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '' ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only search field value.
	: get_search_query( false );
?>
<form class="torantejarat-header-search" role="search" method="get" action="<?php echo esc_url( $torantejarat_action ); ?>">
	<label class="torantejarat-sr-only" for="<?php echo esc_attr( $torantejarat_search_id ); ?>"><?php esc_html_e( 'جست‌وجو', 'torantejarat' ); ?></label>
	<?php torantejarat_icon( 'search' ); ?>
	<input id="<?php echo esc_attr( $torantejarat_search_id ); ?>" name="<?php echo esc_attr( $torantejarat_name ); ?>" type="search" value="<?php echo esc_attr( $torantejarat_value ); ?>" placeholder="<?php esc_attr_e( 'جست‌وجو…', 'torantejarat' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'جست‌وجو', 'torantejarat' ); ?>"><?php torantejarat_icon( 'arrow' ); ?></button>
</form>
