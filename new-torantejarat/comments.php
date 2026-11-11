<?php
/**
 * Article comments: styled list, pagination and a card-style form.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

$torantejarat_comment_count = (int) get_comments_number();
?>
<section id="comments" class="torantejarat-article-comments">
	<header class="torantejarat-article-comments-head">
		<h2 class="torantejarat-article-comments-title">
			<?php torantejarat_icon( 'chat' ); ?>
			<span>
				<?php
				if ( 0 === $torantejarat_comment_count ) {
					esc_html_e( 'بدون دیدگاه', 'torantejarat' );
				} else {
					printf(
						/* translators: %s: comment count */
						esc_html__( '%s دیدگاه', 'torantejarat' ),
						esc_html( torantejarat_fa_number( $torantejarat_comment_count ) )
					);
				}
				?>
			</span>
		</h2>
		<?php if ( comments_open() ) : ?>
			<a class="torantejarat-text-link" href="#respond"><?php esc_html_e( 'نوشتن دیدگاه', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
		<?php endif; ?>
	</header>

	<?php if ( have_comments() ) : ?>
		<ol class="torantejarat-article-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 0,
					'callback'    => __NAMESPACE__ . '\\torantejarat_article_comment',
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => '<span aria-hidden="true">›</span><span>' . esc_html__( 'دیدگاه‌های جدیدتر', 'torantejarat' ) . '</span>',
				'next_text' => '<span>' . esc_html__( 'دیدگاه‌های قدیمی‌تر', 'torantejarat' ) . '</span><span aria-hidden="true">‹</span>',
			)
		);
		?>
	<?php elseif ( comments_open() ) : ?>
		<p class="torantejarat-article-comments-intro"><?php esc_html_e( 'هنوز دیدگاهی ثبت نشده؛ اولین نفر باشید.', 'torantejarat' ); ?></p>
	<?php endif; ?>

	<?php if ( ! comments_open() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="torantejarat-article-comments-closed"><?php echo esc_html( $torantejarat_comment_count ? __( 'دیدگاه‌ها برای این مطلب بسته شده‌اند.', 'torantejarat' ) : __( 'ثبت دیدگاه برای این مطلب فعال نیست.', 'torantejarat' ) ); ?></p>
	<?php endif; ?>

	<?php comment_form( torantejarat_article_comment_form_args() ); ?>
</section>
