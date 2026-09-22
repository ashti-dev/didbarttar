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
?>
<form class="torantejarat-header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="torantejarat-sr-only" for="<?php echo esc_attr( $torantejarat_search_id ); ?>"><?php esc_html_e( 'جست‌وجو', 'torantejarat' ); ?></label>
	<?php torantejarat_icon( 'search' ); ?>
	<input id="<?php echo esc_attr( $torantejarat_search_id ); ?>" name="s" type="search" value="<?php echo esc_attr( get_search_query( false ) ); ?>" placeholder="<?php esc_attr_e( 'جست‌وجو…', 'torantejarat' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'جست‌وجو', 'torantejarat' ); ?>"><?php torantejarat_icon( 'arrow' ); ?></button>
</form>
