<?php
/**
 * Shared page-intro/breadcrumb component from the HTML pages.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_intro_title = $args['title'] ?? get_the_title();
$torantejarat_intro_text  = $args['description'] ?? '';
?>
<section class="torantejarat-page-intro">
	<div class="torantejarat-container">
		<nav class="torantejarat-site-crumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'torantejarat' ); ?>"><ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php torantejarat_icon( 'home' ); ?><span><?php esc_html_e( 'خانه', 'torantejarat' ); ?></span></a></li>
			<li><?php torantejarat_icon( 'chevron' ); ?><span class="torantejarat-crumb-current" aria-current="page"><?php echo esc_html( $torantejarat_intro_title ); ?></span></li>
		</ol></nav>
		<h1><?php echo esc_html( $torantejarat_intro_title ); ?></h1>
		<?php if ( $torantejarat_intro_text ) : ?><p><?php echo esc_html( wp_strip_all_tags( $torantejarat_intro_text ) ); ?></p><?php endif; ?>
	</div>
</section>
