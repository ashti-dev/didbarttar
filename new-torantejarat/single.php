<?php
/**
 * Single article layout: hero, two-column body with a widget-driven sidebar,
 * author box, share row, related posts and styled comments.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<div class="torantejarat-article-progress" data-tta-progress aria-hidden="true"><span></span></div>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$torantejarat_post_id     = get_the_ID();
		$torantejarat_blog_id     = (int) get_option( 'page_for_posts' );
		$torantejarat_author_name = torantejarat_article_author_name();
		$torantejarat_author_url  = torantejarat_article_author_url();
		$torantejarat_categories  = get_the_category();
		$torantejarat_category    = $torantejarat_categories ? $torantejarat_categories[0] : null;
		$torantejarat_trail       = array( torantejarat_home_crumb() );
		if ( $torantejarat_blog_id > 0 && is_post_publicly_viewable( $torantejarat_blog_id ) && ! post_password_required( $torantejarat_blog_id ) ) {
			$torantejarat_trail[] = array( 'label' => get_the_title( $torantejarat_blog_id ), 'url' => get_permalink( $torantejarat_blog_id ) );
		}
		$torantejarat_trail[] = array( 'label' => get_the_title() );
		?>
		<header class="torantejarat-article-hero">
			<div class="torantejarat-container">
				<?php torantejarat_breadcrumb( $torantejarat_trail ); ?>
				<div class="torantejarat-article-hero-copy">
					<?php if ( $torantejarat_category ) : ?>
						<a class="torantejarat-article-cat" href="<?php echo esc_url( get_category_link( $torantejarat_category ) ); ?>"><?php echo esc_html( $torantejarat_category->name ); ?></a>
					<?php endif; ?>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() && ! post_password_required() ) : ?>
						<p class="torantejarat-article-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<div class="torantejarat-article-meta">
						<a class="torantejarat-article-byline" href="<?php echo esc_url( $torantejarat_author_url ); ?>">
							<span class="torantejarat-article-avatar" aria-hidden="true"><?php echo esc_html( torantejarat_article_initial( $torantejarat_author_name ) ); ?></span>
							<span><?php echo esc_html( $torantejarat_author_name ); ?></span>
						</a>
						<span class="torantejarat-article-meta-item"><?php torantejarat_icon( 'calendar' ); ?><?php esc_html_e( 'انتشار:', 'torantejarat' ); ?> <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( torantejarat_article_date() ); ?></time></span>
						<?php if ( torantejarat_article_was_updated() ) : ?>
							<span class="torantejarat-article-meta-item"><?php torantejarat_icon( 'refresh' ); ?><?php esc_html_e( 'به‌روزرسانی:', 'torantejarat' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( torantejarat_article_date( null, 'modified' ) ); ?></time></span>
						<?php endif; ?>
						<span class="torantejarat-article-meta-item"><?php torantejarat_icon( 'clock' ); ?><?php echo esc_html( sprintf( /* translators: %s: reading time in minutes */ __( '%s دقیقه مطالعه', 'torantejarat' ), torantejarat_fa_number( torantejarat_article_reading_time() ) ) ); ?></span>
						<a class="torantejarat-article-meta-item" href="#comments"><?php torantejarat_icon( 'chat' ); ?><?php echo esc_html( sprintf( /* translators: %s: comment count */ __( '%s دیدگاه', 'torantejarat' ), torantejarat_fa_number( get_comments_number() ) ) ); ?></a>
					</div>
				</div>
			</div>
		</header>

		<section class="torantejarat-section torantejarat-article-section">
			<div class="torantejarat-container torantejarat-article-layout">
				<article <?php post_class( 'torantejarat-article-main' ); ?>>
					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<figure class="torantejarat-article-figure">
							<?php
							the_post_thumbnail(
								'large',
								array(
									'class'         => 'torantejarat-article-figure-image',
									'fetchpriority' => 'high',
								)
							);
							$torantejarat_caption = wp_get_attachment_caption( get_post_thumbnail_id() );
							?>
							<?php if ( $torantejarat_caption ) : ?><figcaption><?php echo esc_html( $torantejarat_caption ); ?></figcaption><?php endif; ?>
						</figure>
					<?php endif; ?>

					<?php
					/* Render the body first so the TOC collector has run before the
					   compact-viewport table of contents is printed above it. */
					ob_start();
					the_content();
					$torantejarat_body = ob_get_clean();
					?>
					<?php get_template_part( 'template-parts/article-toc-inline', null, array( 'links' => torantejarat_article_toc() ) ); ?>
					<div class="torantejarat-prose torantejarat-article-body">
						<?php echo $torantejarat_body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-filtered post content. ?>
						<?php wp_link_pages(); ?>
					</div>

					<?php
					$torantejarat_tags = get_the_tags();
					if ( $torantejarat_tags ) :
						?>
						<p class="torantejarat-article-tags">
							<span class="torantejarat-article-tags-label"><?php esc_html_e( 'برچسب‌ها:', 'torantejarat' ); ?></span>
							<?php foreach ( $torantejarat_tags as $torantejarat_tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $torantejarat_tag ) ); ?>" rel="tag"><?php echo esc_html( $torantejarat_tag->name ); ?></a>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if ( ! post_password_required() ) : ?>
						<?php get_template_part( 'template-parts/article-author' ); ?>
					<?php endif; ?>

					<?php comments_template(); ?>
				</article>

				<?php get_template_part( 'template-parts/article-sidebar' ); ?>
			</div>
		</section>

		<?php if ( ! post_password_required() ) : ?>
			<?php get_template_part( 'template-parts/article-related', null, array( 'post_id' => $torantejarat_post_id ) ); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
