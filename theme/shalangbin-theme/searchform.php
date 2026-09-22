<?php
/**
 * Search form.
 *
 * @package ShalangBin
 */
?>
<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="sb-search-<?php echo esc_attr( uniqid() ); ?>"><?php esc_html_e( 'جست‌وجو', 'shalangbin' ); ?></label>
	<?php shalangbin_icon( 'search' ); ?>
	<input type="search" name="s" placeholder="<?php esc_attr_e( 'نام دستگاه، مدل یا کاربرد...', 'shalangbin' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button aria-label="<?php esc_attr_e( 'جست‌وجو', 'shalangbin' ); ?>" type="submit"><?php shalangbin_icon( 'arrow' ); ?></button>
</form>
