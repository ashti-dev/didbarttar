<?php
/** Main-query listing shared by Blog, Category/Archive, Search and index. */
namespace ToranTejarat\Theme;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_visible_results = false;
while ( have_posts() ) {
	the_post();
	ob_start();
	get_template_part( 'template-parts/editorial-card' );
	$torantejarat_result = ob_get_clean();
	if ( '' === trim( $torantejarat_result ) ) { continue; }
	if ( ! $torantejarat_visible_results ) { echo '<div class="torantejarat-editorial-grid">'; }
	$torantejarat_visible_results = true;
	echo $torantejarat_result;
}
if ( $torantejarat_visible_results ) { echo '</div>'; }
else { get_template_part( 'template-parts/content-none' ); }
the_posts_pagination();
