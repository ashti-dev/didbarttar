<?php
/**
 * Article layout from guide-selection.html with native post fields and optional anchored TOC.
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
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => has_excerpt() && ! post_password_required() ? get_the_excerpt() : '' ) );
		$torantejarat_blog_page = (int) get_option( 'page_for_posts' );
		$torantejarat_blog_url = $torantejarat_blog_page > 0 && is_post_publicly_viewable( $torantejarat_blog_page ) && ! post_password_required( $torantejarat_blog_page ) ? get_permalink( $torantejarat_blog_page ) : ( 'posts' === get_option( 'show_on_front' ) ? home_url( '/' ) : '' );
		$torantejarat_links = post_password_required() ? array() : torantejarat_article_links( parse_blocks( get_the_content() ) );
		?>
		<section class="torantejarat-section">
			<article <?php post_class( 'torantejarat-container ' . ( $torantejarat_links ? 'torantejarat-article-layout' : 'torantejarat-content-wide' ) ); ?>>
				<?php if ( $torantejarat_links ) : ?>
					<nav class="torantejarat-article-nav" aria-labelledby="torantejarat-toc-title">
						<h2 id="torantejarat-toc-title"><?php esc_html_e( 'در این مطلب', 'torantejarat' ); ?></h2>
						<ul>
						<?php foreach ( $torantejarat_links as $torantejarat_link ) : ?><li><a href="<?php echo esc_url( '#' . rawurlencode( $torantejarat_link['id'] ) ); ?>"><?php echo esc_html( $torantejarat_link['title'] ); ?></a></li><?php endforeach; ?>
						</ul>
						<?php if ( $torantejarat_blog_url ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( $torantejarat_blog_url ); ?>"><?php esc_html_e( 'همه مطالب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
					</nav>
				<?php endif; ?>
				<div class="torantejarat-prose">
					<p class="torantejarat-post-meta"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a> · <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time> · <?php torantejarat_post_categories(); ?></p>
					<?php if ( has_post_thumbnail() && ! post_password_required() ) { the_post_thumbnail( 'large' ); } ?>
					<div class="torantejarat-article-body"><?php the_content(); wp_link_pages(); ?></div>
					<?php the_tags( '<p class="torantejarat-post-tags">', ' · ', '</p>' ); ?>
				</div>
			</article>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
