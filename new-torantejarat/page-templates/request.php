<?php
/**
 * Request layout: contact gets info cards + sidebar widgets (hours, social,
 * map); rent/repairs keep the guide + functional form.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: تماس و خدمات — چیدمان منبع */
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$torantejarat_request_type = get_post_field( 'post_name', get_the_ID() );
		if ( ! in_array( $torantejarat_request_type, array( 'contact', 'rent', 'repairs' ), true ) ) {
			$torantejarat_request_type = '';
		}
		?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title() ) ); ?>
		<?php if ( 'contact' === $torantejarat_request_type && ! post_password_required() ) : ?>
			<section class="torantejarat-section torantejarat-contact-cards-section"><div class="torantejarat-container">
				<?php get_template_part( 'template-parts/contact-cards' ); ?>
			</div></section>
		<?php endif; ?>
		<section class="torantejarat-section"><div class="torantejarat-container torantejarat-request-layout<?php echo ( ! post_password_required() && ( has_excerpt() || has_post_thumbnail() ) ) ? '' : ' torantejarat-no-aside'; ?>">
			<?php if ( ! post_password_required() && ( has_excerpt() || has_post_thumbnail() ) ) : ?>
				<aside class="torantejarat-request-guide">
					<?php
					if ( has_excerpt() ) {
						the_excerpt();
					}
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'large' );
					}
					?>
					<?php if ( 'contact' === $torantejarat_request_type ) : ?>
						<?php
						$torantejarat_hours = torantejarat_option( 'contact', 'working_hours' );
						if ( $torantejarat_hours ) :
							?>
							<div class="torantejarat-contact-widget">
								<h2><?php esc_html_e( 'ساعت کاری', 'torantejarat' ); ?></h2>
								<p><?php echo esc_html( $torantejarat_hours ); ?></p>
							</div>
						<?php endif; ?>
						<?php
						$torantejarat_socials = array(
							'instagram' => __( 'اینستاگرام', 'torantejarat' ),
							'telegram'  => __( 'تلگرام', 'torantejarat' ),
							'aparat'    => __( 'آپارات', 'torantejarat' ),
							'youtube'   => __( 'یوتیوب', 'torantejarat' ),
						);
						$torantejarat_links  = array();
						foreach ( $torantejarat_socials as $torantejarat_key => $torantejarat_label ) {
							$torantejarat_url = torantejarat_option( 'social', $torantejarat_key );
							if ( $torantejarat_url ) {
								$torantejarat_links[ $torantejarat_key ] = array( 'label' => $torantejarat_label, 'url' => $torantejarat_url );
							}
						}
						if ( $torantejarat_links ) :
							?>
							<div class="torantejarat-contact-widget">
								<h2><?php esc_html_e( 'ما را دنبال کنید', 'torantejarat' ); ?></h2>
								<ul>
									<?php foreach ( $torantejarat_links as $torantejarat_link ) : ?>
										<li><a href="<?php echo esc_url( $torantejarat_link['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $torantejarat_link['label'] ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<div class="torantejarat-contact-widget">
							<h2><?php esc_html_e( 'نشانی ما روی نقشه', 'torantejarat' ); ?></h2>
							<?php
							$torantejarat_map = torantejarat_option( 'contact', 'map_embed' );
							if ( $torantejarat_map ) {
								/* Already kses-filtered on save (iframe allowlist); admin-provided embed. */
								echo '<div class="torantejarat-contact-map">' . $torantejarat_map . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								echo '<p class="torantejarat-contact-map-empty">' . esc_html__( 'نقشه اینجا قرار می‌گیرد — کد embed گوگل‌مپ را در پنل تنظیمات (تب تماس) بگذارید.', 'torantejarat' ) . '</p>';
							}
							?>
						</div>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
			<div class="torantejarat-request-form torantejarat-prose"><?php the_content(); wp_link_pages(); ?>
				<?php
				if ( ! post_password_required() && '' !== $torantejarat_request_type ) {
					torantejarat_request_form( $torantejarat_request_type );
				}
				?>
			</div>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
