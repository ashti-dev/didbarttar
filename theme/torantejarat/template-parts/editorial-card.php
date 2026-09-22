<?php
/**
 * Editorial card from blog.html; no sample article data or invented read time.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'product' === get_post_type() && torantejarat_woocommerce_available() ) {
	$torantejarat_search_product = wc_get_product( get_the_ID() );
	if ( ! $torantejarat_search_product || ! $torantejarat_search_product->is_visible() ) {
		return;
	}
}
$torantejarat_heading = 3 === ( $args['heading_level'] ?? 2 ) ? 'h3' : 'h2';
$torantejarat_categories = get_the_category();
$torantejarat_type = get_post_type_object( get_post_type() );
?>
<article <?php post_class( 'torantejarat-editorial-card' ); ?>>
	<a class="torantejarat-card-link" href="<?php the_permalink(); ?>">
		<div class="torantejarat-editorial-art torantejarat-art-0">
			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large' ); } else { torantejarat_icon( 'scan' ); } ?>
		</div>
		<div class="torantejarat-editorial-body">
			<span class="torantejarat-eyebrow"><?php echo esc_html( $torantejarat_categories ? $torantejarat_categories[0]->name : ( $torantejarat_type ? $torantejarat_type->labels->singular_name : '' ) ); ?></span>
			<?php echo '<' . $torantejarat_heading . '>' . esc_html( get_the_title() ) . '</' . $torantejarat_heading . '>'; ?>
			<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<span class="torantejarat-text-link"><?php esc_html_e( 'ادامه مطلب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></span>
		</div>
	</a>
</article>
