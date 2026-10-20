<?php
/**
 * Source brand lockup with WordPress identity instead of demo copy.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="torantejarat-brand-wrap">
	<?php if ( has_custom_logo() ) { the_custom_logo(); } ?>
	<a class="torantejarat-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php if ( ! has_custom_logo() ) : ?><span class="torantejarat-brand-mark"><?php torantejarat_icon( 'scan' ); ?></span><?php endif; ?>
		<span><?php echo esc_html( get_bloginfo( 'name' ) ?: __( 'توران تجارت', 'torantejarat' ) ); ?><small><?php echo esc_html( get_bloginfo( 'description' ) ); ?></small></span>
	</a>
</div>
