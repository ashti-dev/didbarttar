<?php
/**
 * Comments template.
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
?>
<section id="comments" class="torantejarat-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="torantejarat-comments-title">
			<?php
			$comment_count = get_comments_number();
			/* translators: %s: comment count */
			printf( esc_html( _nx( '%s دیدگاه', '%s دیدگاه', $comment_count, 'comments title', 'torantejarat' ) ), esc_html( number_format_i18n( $comment_count ) ) );
			?>
		</h2>
		<ol class="torantejarat-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="torantejarat-no-comments"><?php esc_html_e( 'دیدگاه‌ها بسته شده‌اند.', 'torantejarat' ); ?></p>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
