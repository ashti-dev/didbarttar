<?php
/**
 * Author box and share row shown under the article body.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_author_name  = torantejarat_article_author_name();
$torantejarat_author_url   = torantejarat_article_author_url();
$torantejarat_author_bio   = torantejarat_article_author_bio();
$torantejarat_author_posts = torantejarat_article_author_post_count();
$torantejarat_share_links  = torantejarat_article_share_links();
?>
<section class="torantejarat-article-author" aria-labelledby="torantejarat-article-author-title">
	<div class="torantejarat-article-author-avatar" aria-hidden="true"><?php echo esc_html( torantejarat_article_initial( $torantejarat_author_name ) ); ?></div>
	<div class="torantejarat-article-author-body">
		<span class="torantejarat-eyebrow"><?php esc_html_e( 'نویسنده این مطلب', 'torantejarat' ); ?></span>
		<h2 id="torantejarat-article-author-title"><a href="<?php echo esc_url( $torantejarat_author_url ); ?>"><?php echo esc_html( $torantejarat_author_name ); ?></a></h2>
		<p><?php echo esc_html( $torantejarat_author_bio ); ?></p>
		<div class="torantejarat-article-author-foot">
			<a class="torantejarat-text-link" href="<?php echo esc_url( $torantejarat_author_url ); ?>"><?php esc_html_e( 'مشاهده مطالب نویسنده', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
			<?php if ( $torantejarat_author_posts ) : ?>
				<span class="torantejarat-article-author-count"><?php echo esc_html( sprintf( /* translators: %s: number of posts */ __( '%s مطلب منتشرشده', 'torantejarat' ), torantejarat_fa_number( $torantejarat_author_posts ) ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="torantejarat-article-share" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری مطلب', 'torantejarat' ); ?>">
	<span class="torantejarat-article-share-label"><?php torantejarat_icon( 'share' ); ?><span><?php esc_html_e( 'اشتراک‌گذاری:', 'torantejarat' ); ?></span></span>
	<?php foreach ( $torantejarat_share_links as $torantejarat_share ) : ?>
		<a class="torantejarat-article-share-btn" href="<?php echo esc_url( $torantejarat_share['url'] ); ?>" target="_blank" rel="noopener noreferrer">
			<?php torantejarat_icon( $torantejarat_share['icon'] ); ?><span><?php echo esc_html( $torantejarat_share['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
	<button type="button" class="torantejarat-article-share-btn" data-tta-copy>
		<?php torantejarat_icon( 'link' ); ?><span><?php esc_html_e( 'کپی لینک', 'torantejarat' ); ?></span>
	</button>
	<button type="button" class="torantejarat-article-share-btn" data-tta-native-share hidden>
		<?php torantejarat_icon( 'share' ); ?><span><?php esc_html_e( 'اشتراک‌گذاری', 'torantejarat' ); ?></span>
	</button>
	<span class="torantejarat-article-share-status" data-tta-share-status role="status" aria-live="polite"></span>
</div>
