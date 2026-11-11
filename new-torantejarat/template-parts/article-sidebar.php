<?php
/**
 * Article sidebar: always-visible table of contents, then owner widgets or
 * curated fallback cards (guide CTA, product picks, support CTA).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Filled by torantejarat_article_anchor_content() while the_content runs. */
$torantejarat_links       = torantejarat_article_toc();
$torantejarat_has_widgets = is_active_sidebar( 'torantejarat-article-sidebar' );

$torantejarat_blog_id  = (int) get_option( 'page_for_posts' );
$torantejarat_blog_url = $torantejarat_blog_id > 0 && is_post_publicly_viewable( $torantejarat_blog_id ) ? get_permalink( $torantejarat_blog_id ) : home_url( '/' );

$torantejarat_quiz     = get_page_by_path( 'quiz' );
$torantejarat_quiz_url = $torantejarat_quiz && is_post_publicly_viewable( $torantejarat_quiz )
	? get_permalink( $torantejarat_quiz )
	: ( torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );

$torantejarat_contact     = get_page_by_path( 'contact' );
$torantejarat_contact_url = $torantejarat_contact && is_post_publicly_viewable( $torantejarat_contact )
	? get_permalink( $torantejarat_contact )
	: home_url( '/' );
?>
<aside class="torantejarat-article-sidebar" aria-label="<?php esc_attr_e( 'ابزارهای مقاله', 'torantejarat' ); ?>">
	<?php if ( $torantejarat_links ) : ?>
		<nav class="torantejarat-article-toc" data-tta-toc aria-labelledby="torantejarat-toc-title">
			<h2 id="torantejarat-toc-title"><?php torantejarat_icon( 'book' ); ?><span><?php esc_html_e( 'فهرست مطالب', 'torantejarat' ); ?></span></h2>
			<ol>
				<?php foreach ( $torantejarat_links as $torantejarat_index => $torantejarat_link ) : ?>
					<li>
						<a href="<?php echo esc_url( '#' . rawurlencode( $torantejarat_link['id'] ) ); ?>">
							<span class="torantejarat-article-toc-index"><?php echo esc_html( torantejarat_fa_number( $torantejarat_index + 1 ) ); ?></span>
							<span><?php echo esc_html( $torantejarat_link['title'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
			<a class="torantejarat-text-link torantejarat-article-toc-all" href="<?php echo esc_url( $torantejarat_blog_url ); ?>"><?php esc_html_e( 'همه مطالب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
		</nav>
	<?php elseif ( ! $torantejarat_has_widgets ) : ?>
		<?php /* Short notes have no sections: keep the slot filled with a compact fact card. */ ?>
		<div class="torantejarat-widget torantejarat-article-facts">
			<h2><?php torantejarat_icon( 'book' ); ?><span><?php esc_html_e( 'در یک نگاه', 'torantejarat' ); ?></span></h2>
			<ul>
				<li>
					<span class="torantejarat-article-facts-icon"><?php torantejarat_icon( 'clock' ); ?></span>
					<span><?php echo esc_html( sprintf( /* translators: %s: reading time in minutes */ __( '%s دقیقه مطالعه', 'torantejarat' ), torantejarat_fa_number( torantejarat_article_reading_time() ) ) ); ?></span>
				</li>
				<li>
					<span class="torantejarat-article-facts-icon"><?php torantejarat_icon( 'calendar' ); ?></span>
					<span><?php esc_html_e( 'انتشار:', 'torantejarat' ); ?> <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( torantejarat_article_date() ); ?></time></span>
				</li>
				<?php if ( torantejarat_article_was_updated() ) : ?>
					<li>
						<span class="torantejarat-article-facts-icon"><?php torantejarat_icon( 'refresh' ); ?></span>
						<span><?php esc_html_e( 'به‌روزرسانی:', 'torantejarat' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( torantejarat_article_date( null, 'modified' ) ); ?></time></span>
					</li>
				<?php endif; ?>
				<li>
					<span class="torantejarat-article-facts-icon"><?php torantejarat_icon( 'chat' ); ?></span>
					<span><a class="torantejarat-text-link" href="#comments"><?php echo esc_html( sprintf( /* translators: %s: comment count */ __( '%s دیدگاه', 'torantejarat' ), torantejarat_fa_number( get_comments_number() ) ) ); ?></a></span>
				</li>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $torantejarat_has_widgets ) : ?>
		<?php dynamic_sidebar( 'torantejarat-article-sidebar' ); ?>
	<?php else : ?>
		<div class="torantejarat-widget torantejarat-article-cta is-featured">
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'راهنمای انتخاب', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'دستگاه مناسب مسیر خود را پیدا کنید', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'در چند پرسش کوتاه، گزینه‌های متناسب با کاربرد و محیط کارتان را ببینید.', 'torantejarat' ); ?></p>
			<a class="torantejarat-btn torantejarat-btn-primary torantejarat-full" href="<?php echo esc_url( $torantejarat_quiz_url ); ?>"><?php esc_html_e( 'شروع راهنمای انتخاب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
		</div>

		<?php
		$torantejarat_products = array();
		if ( torantejarat_woocommerce_available() && function_exists( 'wc_get_products' ) ) {
			/* Featured picks first, best sellers top up the list. */
			$torantejarat_products = wc_get_products(
				array(
					'limit'    => 3,
					'status'   => 'publish',
					'featured' => true,
					'orderby'  => 'date',
					'order'    => 'DESC',
				)
			);

			if ( count( $torantejarat_products ) < 3 ) {
				$torantejarat_products = array_merge(
					$torantejarat_products,
					wc_get_products(
						array(
							'limit'    => 3 - count( $torantejarat_products ),
							'status'   => 'publish',
							'orderby'  => 'popularity',
							'order'    => 'DESC',
							'exclude'  => array_map(
								static function ( $torantejarat_product_item ) {
									return $torantejarat_product_item->get_id();
								},
								$torantejarat_products
							),
						)
					)
				);
			}
		}
		?>
		<?php if ( $torantejarat_products ) : ?>
			<div class="torantejarat-widget torantejarat-article-products">
				<h2><?php esc_html_e( 'پیشنهاد خرید', 'torantejarat' ); ?></h2>
				<ul>
					<?php foreach ( $torantejarat_products as $torantejarat_product ) : ?>
						<?php if ( ! $torantejarat_product instanceof \WC_Product || ! $torantejarat_product->is_visible() ) { continue; } ?>
						<li>
							<a class="torantejarat-article-product-thumb" href="<?php echo esc_url( $torantejarat_product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
								<?php echo $torantejarat_product->get_image( 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce image markup. ?>
							</a>
							<div>
								<a class="torantejarat-article-product-title" href="<?php echo esc_url( $torantejarat_product->get_permalink() ); ?>"><?php echo esc_html( $torantejarat_product->get_name() ); ?></a>
								<?php echo torantejarat_card_price( $torantejarat_product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped parts in helper. ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<?php $torantejarat_latest = torantejarat_article_related_posts( get_the_ID(), 3 ); ?>
			<?php if ( $torantejarat_latest ) : ?>
				<div class="torantejarat-widget torantejarat-article-products">
					<h2><?php esc_html_e( 'آخرین مطالب', 'torantejarat' ); ?></h2>
					<ul class="torantejarat-widget-latest">
						<?php foreach ( $torantejarat_latest as $torantejarat_latest_post ) : ?>
							<li>
								<?php if ( has_post_thumbnail( $torantejarat_latest_post ) ) : ?>
									<a href="<?php echo esc_url( get_permalink( $torantejarat_latest_post ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo get_the_post_thumbnail( $torantejarat_latest_post, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?></a>
								<?php endif; ?>
								<div>
									<a href="<?php echo esc_url( get_permalink( $torantejarat_latest_post ) ); ?>"><?php echo esc_html( get_the_title( $torantejarat_latest_post ) ); ?></a>
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $torantejarat_latest_post ) ); ?>"><?php echo esc_html( get_the_date( '', $torantejarat_latest_post ) ); ?></time>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="torantejarat-widget torantejarat-article-cta is-soft">
			<h2><?php esc_html_e( 'نیاز به مشاوره دارید؟', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'اندازه‌ها و شرایط مسیر را بفرستید تا گزینه مناسب را با هم بررسی کنیم.', 'torantejarat' ); ?></p>
			<a class="torantejarat-btn torantejarat-btn-outline torantejarat-full" href="<?php echo esc_url( $torantejarat_contact_url ); ?>"><?php esc_html_e( 'ارتباط با کارشناس', 'torantejarat' ); ?> <?php torantejarat_icon( 'chat' ); ?></a>
		</div>
	<?php endif; ?>
</aside>
