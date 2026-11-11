<?php
/**
 * Single-article helpers: reading time, author data, related posts, social
 * sharing and the styled comment walker callback.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Estimated reading time in whole minutes (Persian text, ~200 wpm).
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return int
 */
function torantejarat_article_reading_time( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 1;
	}

	$content = strip_shortcodes( (string) $post->post_content );
	$content = wp_strip_all_tags( $content );
	$content = trim( preg_replace( '/\s+/u', ' ', $content ) );
	$words   = '' === $content ? 0 : count( preg_split( '/\s+/u', $content ) );

	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Author ID for a post, or 0 when the post has no valid author.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return int
 */
function torantejarat_article_author_id( $post = null ) {
	$post = get_post( $post );
	return $post ? absint( $post->post_author ) : 0;
}

/**
 * Display name with an editorial-team fallback for imported posts.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return string
 */
function torantejarat_article_author_name( $post = null ) {
	$author_id = torantejarat_article_author_id( $post );
	if ( $author_id ) {
		$name = get_the_author_meta( 'display_name', $author_id );
		if ( is_string( $name ) && '' !== trim( $name ) ) {
			return $name;
		}
	}

	return __( 'تیم تحریریه', 'torantejarat' );
}

/**
 * Author archive URL, falling back to the blog index.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return string
 */
function torantejarat_article_author_url( $post = null ) {
	$author_id = torantejarat_article_author_id( $post );
	if ( $author_id ) {
		return get_author_posts_url( $author_id );
	}

	$blog_id = (int) get_option( 'page_for_posts' );
	if ( $blog_id > 0 && is_post_publicly_viewable( $blog_id ) ) {
		return get_permalink( $blog_id );
	}

	return home_url( '/' );
}

/**
 * Author biography with a site-level fallback.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return string
 */
function torantejarat_article_author_bio( $post = null ) {
	$author_id = torantejarat_article_author_id( $post );
	$bio       = $author_id ? get_the_author_meta( 'description', $author_id ) : '';

	if ( is_string( $bio ) && '' !== trim( $bio ) ) {
		return $bio;
	}

	return sprintf(
		/* translators: %s: site name */
		__( 'راهنماها و یادداشت‌های تخصصی %s بر اساس کاربرد واقعی دستگاه‌ها و مشخصات تأییدشده تهیه می‌شود.', 'torantejarat' ),
		get_bloginfo( 'name' )
	);
}

/**
 * Number of published posts by the article author.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return int
 */
function torantejarat_article_author_post_count( $post = null ) {
	$author_id = torantejarat_article_author_id( $post );
	return $author_id ? (int) count_user_posts( $author_id, 'post' ) : 0;
}

/**
 * One-letter monogram used by the article, author box and comment avatars.
 *
 * @param string $name Display name.
 * @return string
 */
function torantejarat_article_initial( $name ) {
	$name = trim( wp_strip_all_tags( (string) $name ) );
	if ( '' === $name ) {
		return 'ت';
	}

	return function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1, 'UTF-8' ) : substr( $name, 0, 1 );
}

/**
 * Jalali publishing or modification date for a post.
 *
 * @param int|\WP_Post|null $post  Post to inspect.
 * @param string            $field "date" or "modified".
 * @return string
 */
function torantejarat_article_date( $post = null, $field = 'date' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$timestamp = 'modified' === $field
		? get_post_modified_time( 'U', false, $post )
		: get_post_time( 'U', false, $post );

	if ( ! $timestamp ) {
		return '';
	}

	return torantejarat_jalali_ymd(
		(int) wp_date( 'Y', $timestamp ),
		(int) wp_date( 'n', $timestamp ),
		(int) wp_date( 'j', $timestamp )
	);
}

/**
 * Whether the post has a meaningful update date.
 *
 * @param int|\WP_Post|null $post Post to inspect.
 * @return bool
 */
function torantejarat_article_was_updated( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	$published = (int) get_post_time( 'U', false, $post );
	$modified  = (int) get_post_modified_time( 'U', false, $post );

	return $modified - $published > DAY_IN_SECONDS;
}

/**
 * Comments in a post's categories, topped up with the latest articles.
 *
 * @param int|\WP_Post|null $post  Current post.
 * @param int               $limit Number of cards.
 * @return array<int,\WP_Post>
 */
function torantejarat_article_related_posts( $post = null, $limit = 3 ) {
	$post  = get_post( $post );
	$limit = max( 1, (int) $limit );
	if ( ! $post ) {
		return array();
	}

	$base_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'post__not_in'        => array( $post->ID ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$args = $base_args;
	$args['posts_per_page'] = $limit;

	$categories = wp_get_post_categories( $post->ID );
	if ( $categories ) {
		$args['category__in'] = $categories;
	}

	$query = new \WP_Query( $args );
	$posts = $query->posts;

	if ( count( $posts ) < $limit ) {
		$base_args['posts_per_page'] = $limit - count( $posts );
		$base_args['post__not_in']   = array_merge( array( $post->ID ), wp_list_pluck( $posts, 'ID' ) );

		$fill = new \WP_Query( $base_args );
		$posts = array_merge( $posts, $fill->posts );
	}

	return $posts;
}

/**
 * Share targets for the current article.
 *
 * @param int|\WP_Post|null $post Post to share.
 * @return array<string,array{label:string,url:string,icon:string}>
 */
function torantejarat_article_share_links( $post = null ) {
	$url  = get_permalink( $post );
	$text = get_the_title( $post );
	if ( ! $url ) {
		return array();
	}

	return array(
		'telegram' => array(
			'label' => __( 'تلگرام', 'torantejarat' ),
			'url'   => 'https://t.me/share/url?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $text ),
			'icon'  => 'telegram',
		),
		'whatsapp' => array(
			'label' => __( 'واتساپ', 'torantejarat' ),
			'url'   => 'https://wa.me/?text=' . rawurlencode( $text . ' ' . $url ),
			'icon'  => 'chat',
		),
		'mail'     => array(
			'label' => __( 'ایمیل', 'torantejarat' ),
			'url'   => 'mailto:?subject=' . rawurlencode( $text ) . '&body=' . rawurlencode( $url ),
			'icon'  => 'mail',
		),
	);
}

/**
 * Jalali date for a single comment.
 *
 * @param \WP_Comment|int $comment Comment object or ID.
 * @return string
 */
function torantejarat_article_comment_date( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	$parts = explode( '-', (string) get_comment_date( 'Y-m-d', $comment ) );
	if ( 3 !== count( $parts ) ) {
		return get_comment_date( '', $comment );
	}

	return torantejarat_jalali_ymd( $parts[0], $parts[1], $parts[2] );
}

/**
 * Store / read the table-of-contents collected while rendering the content.
 *
 * @param int        $post_id Post ID (0 = current).
 * @param array|null $links   Links to store, or null to read.
 * @return array<int,array<string,string>>
 */
function torantejarat_article_toc( $post_id = 0, $links = null ) {
	static $cache = array();

	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

	if ( null !== $links ) {
		$cache[ $post_id ] = $links;
		return $links;
	}

	return isset( $cache[ $post_id ] ) ? $cache[ $post_id ] : array();
}

/**
 * Add stable IDs to article headings and remember them for the TOC.
 *
 * Runs after blocks and wpautop, so block headings with an explicit anchor keep
 * their ID and legacy headings receive a deterministic `section-N` one.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function torantejarat_article_anchor_content( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	/* Auto excerpts run the_content on truncated HTML; anchoring them would
	   duplicate the article's IDs and poison the TOC with excerpt headings. */
	$filters = isset( $GLOBALS['wp_current_filter'] ) ? (array) $GLOBALS['wp_current_filter'] : array();
	if ( in_array( 'get_the_excerpt', $filters, true ) || in_array( 'the_excerpt', $filters, true ) ) {
		return $content;
	}

	$post_id  = (int) get_the_ID();
	$links    = array();
	$sequence = 0;
	$used     = array();

	$anchored = preg_replace_callback(
		'/<h([2-3])\b([^>]*)>(.*?)<\/h\1>/is',
		static function ( $matches ) use ( &$links, &$sequence, &$used ) {
			$attrs = (string) $matches[2];
			$inner = (string) $matches[3];
			$id    = '';

			/* Accept quoted and unquoted ids: KSES rewrites some attributes bare. */
			if ( preg_match( '/\bid\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/i', $attrs, $id_match ) ) {
				foreach ( array( 1, 2, 3 ) as $group ) {
					if ( isset( $id_match[ $group ] ) && '' !== trim( $id_match[ $group ] ) ) {
						$id = trim( $id_match[ $group ] );
						break;
					}
				}
			}

			if ( '' === $id ) {
				do {
					++$sequence;
					$id = 'section-' . $sequence;
				} while ( isset( $used[ $id ] ) );

				$attrs = rtrim( $attrs ) . ' id="' . esc_attr( $id ) . '"';
			}

			$used[ $id ] = true;
			$title       = trim( html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

			if ( '' !== $title ) {
				$links[] = array(
					'id'    => $id,
					'title' => $title,
				);
			}

			return '<h' . $matches[1] . $attrs . '>' . $inner . '</h' . $matches[1] . '>';
		},
		$content
	);

	if ( null === $anchored ) {
		return $content;
	}

	torantejarat_article_toc( $post_id, $links );

	return $anchored;
}

add_filter( 'the_content', __NAMESPACE__ . '\\torantejarat_article_anchor_content', 20 );

/**
 * Styled comment walker callback for comments.php.
 *
 * @param \WP_Comment $comment Comment object.
 * @param array       $args    wp_list_comments arguments.
 * @param int         $depth   Nesting depth.
 */
function torantejarat_article_comment( $comment, $args, $depth ) {
	$tag = 'div' === $args['style'] ? 'div' : 'li';
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'torantejarat-article-comment', $comment ); ?>>
		<article class="torantejarat-article-comment-body">
			<div class="torantejarat-article-comment-avatar" aria-hidden="true"><?php echo esc_html( torantejarat_article_initial( get_comment_author( $comment ) ) ); ?></div>
			<div class="torantejarat-article-comment-content">
				<header class="torantejarat-article-comment-head">
					<cite class="torantejarat-article-comment-author"><?php echo get_comment_author_link( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds a safe link. ?></cite>
					<time class="torantejarat-article-comment-date" datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( torantejarat_article_comment_date( $comment ) ); ?></time>
					<?php if ( '0' === (string) $comment->comment_approved ) : ?>
						<span class="torantejarat-article-comment-pending"><?php esc_html_e( 'در انتظار تأیید', 'torantejarat' ); ?></span>
					<?php endif; ?>
				</header>
				<div class="torantejarat-article-comment-text"><?php comment_text( $comment ); ?></div>
				<footer class="torantejarat-article-comment-actions">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'reply_text' => __( 'پاسخ', 'torantejarat' ),
								'before'     => '<span class="torantejarat-article-comment-reply">',
								'after'      => '</span>',
							)
						),
						$comment
					);
					edit_comment_link( __( 'ویرایش', 'torantejarat' ), '<span class="torantejarat-article-comment-edit">', '</span>', $comment );
					?>
				</footer>
			</div>
		</article>
	<?php
}

/**
 * Comment form arguments matching the article design.
 *
 * @return array<string,mixed>
 */
function torantejarat_article_comment_form_args() {
	$commenter     = wp_get_current_commenter();
	$require_name  = (bool) get_option( 'require_name_email' );
	$required_mark = $require_name ? ' <span class="required" aria-hidden="true">*</span>' : '';
	$required_attr = $require_name ? ' required="required" aria-required="true"' : '';

	$args = array(
		'class_form'            => 'torantejarat-article-comment-form',
		'class_submit'          => 'torantejarat-btn torantejarat-btn-primary',
		'title_reply'           => __( 'دیدگاه خود را بنویسید', 'torantejarat' ),
		'title_reply_before'    => '<h3 id="reply-title" class="torantejarat-article-comment-form-title">',
		'title_reply_after'     => '</h3>',
		'cancel_reply_link'     => __( 'لغو پاسخ', 'torantejarat' ),
		'comment_notes_before'  => '<p class="torantejarat-article-comment-notes">' . esc_html__( 'ایمیل شما منتشر نمی‌شود. فیلدهای ستاره‌دار الزامی هستند.', 'torantejarat' ) . '</p>',
		'comment_notes_after'   => '',
		'label_submit'          => __( 'ثبت دیدگاه', 'torantejarat' ),
		'format'                => 'html5',
		'comment_field'         => '<p class="comment-form-comment torantejarat-article-field"><label for="comment">' . esc_html__( 'متن دیدگاه', 'torantejarat' ) . $required_mark . '</label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required="required" placeholder="' . esc_attr__( 'تجربه، پرسش یا نکته خود را بنویسید…', 'torantejarat' ) . '"></textarea></p>',
		'fields'                => array(
			'author' => '<p class="comment-form-author torantejarat-article-field"><label for="author">' . esc_html__( 'نام', 'torantejarat' ) . $required_mark . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245" autocomplete="name"' . $required_attr . '></p>',
			'email'  => '<p class="comment-form-email torantejarat-article-field"><label for="email">' . esc_html__( 'ایمیل', 'torantejarat' ) . $required_mark . '</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" autocomplete="email" dir="ltr"' . $required_attr . '><small>' . esc_html__( 'فقط برای اطلاع از پاسخ؛ منتشر نمی‌شود.', 'torantejarat' ) . '</small></p>',
			'url'    => '',
		),
		'submit_button'         => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
		'must_log_in'           => '',
		'logged_in_as'          => '',
	);

	if ( ! is_user_logged_in() ) {
		$args['must_log_in'] = '<p class="torantejarat-article-comment-login">' . sprintf(
			/* translators: %s: login URL */
			wp_kses_post( __( 'برای ثبت دیدگاه باید <a href="%s">وارد حساب کاربری شوید</a>.', 'torantejarat' ) ),
			esc_url( wp_login_url( get_permalink() ) )
		) . '</p>';
	}

	return $args;
}
