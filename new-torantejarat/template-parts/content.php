<?php
/**
 * Native content output with context-escaped titles and links.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_title = get_the_title();
$torantejarat_title = $torantejarat_title ? $torantejarat_title : __( 'Untitled', 'torantejarat' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'torantejarat-entry' ); ?>>
	<header class="torantejarat-entry-header">
		<?php if ( is_singular() ) : ?>
			<h1><?php echo esc_html( $torantejarat_title ); ?></h1>
		<?php else : ?>
			<h2><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $torantejarat_title ); ?></a></h2>
		<?php endif; ?>
	</header>
	<div class="torantejarat-entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="torantejarat-page-links" aria-label="' . esc_attr__( 'Content pages', 'torantejarat' ) . '">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>
</article>
