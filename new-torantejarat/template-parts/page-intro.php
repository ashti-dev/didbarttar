<?php
/**
 * Shared page-intro/breadcrumb component from the HTML pages.
 * Args: title, description, heading (bool), trail (middle crumbs between
 * home and current: array of array('label','url')).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_intro_title   = $args['title'] ?? get_the_title();
$torantejarat_intro_text    = $args['description'] ?? '';
$torantejarat_show_heading  = $args['heading'] ?? true;
$torantejarat_middle_trail  = isset( $args['trail'] ) && is_array( $args['trail'] ) ? $args['trail'] : array();
$torantejarat_full_trail    = array_merge(
	array( torantejarat_home_crumb() ),
	$torantejarat_middle_trail,
	array( array( 'label' => (string) $torantejarat_intro_title ) )
);
?>
<section class="torantejarat-page-intro">
	<div class="torantejarat-container">
		<?php torantejarat_breadcrumb( $torantejarat_full_trail ); ?>
		<?php if ( $torantejarat_show_heading ) : ?>
			<h1><?php echo esc_html( $torantejarat_intro_title ); ?></h1>
			<?php if ( $torantejarat_intro_text ) : ?><p><?php echo esc_html( wp_strip_all_tags( $torantejarat_intro_text ) ); ?></p><?php endif; ?>
		<?php else : ?>
			<h1 class="torantejarat-sr-only"><?php echo esc_html( $torantejarat_intro_title ); ?></h1>
		<?php endif; ?>
	</div>
</section>
