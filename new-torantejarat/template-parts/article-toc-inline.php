<?php
/**
 * Collapsible table of contents shown above the article body on compact
 * viewports, where the sidebar falls below the text.
 *
 * Args: links — array of array( 'id', 'title' ) from torantejarat_article_toc().
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_toc_links = isset( $args['links'] ) && is_array( $args['links'] ) ? $args['links'] : array();

if ( ! $torantejarat_toc_links ) {
	return;
}

$torantejarat_toc_count = count( $torantejarat_toc_links );
?>
<details class="torantejarat-article-toc-inline" data-tta-toc data-tta-toc-inline>
	<summary>
		<span class="torantejarat-article-toc-inline-title"><?php torantejarat_icon( 'book' ); ?><span><?php esc_html_e( 'فهرست مطالب', 'torantejarat' ); ?></span></span>
		<span class="torantejarat-article-toc-inline-count">
			<?php
			printf(
				/* translators: %s: number of sections */
				esc_html( _n( '%s بخش', '%s بخش', $torantejarat_toc_count, 'toc sections', 'torantejarat' ) ),
				esc_html( torantejarat_fa_number( $torantejarat_toc_count ) )
			);
			?>
		</span>
	</summary>
	<ol>
		<?php foreach ( $torantejarat_toc_links as $torantejarat_toc_index => $torantejarat_toc_link ) : ?>
			<li>
				<a href="<?php echo esc_url( '#' . rawurlencode( $torantejarat_toc_link['id'] ) ); ?>">
					<span class="torantejarat-article-toc-index"><?php echo esc_html( torantejarat_fa_number( $torantejarat_toc_index + 1 ) ); ?></span>
					<span><?php echo esc_html( $torantejarat_toc_link['title'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</details>
