<?php
/**
 * Request layout: the contact page uses a two-column desktop layout with the
 * form beside info cards, working hours, social links and the map; rent and
 * repairs keep the guide + functional form.
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
		$torantejarat_is_contact = ( 'contact' === $torantejarat_request_type && ! post_password_required() );
		$torantejarat_has_copy    = '' !== trim( wp_strip_all_tags( get_the_content() ) );
		?>
		<?php
		get_template_part(
			'template-parts/page-intro',
			null,
			array(
				'title'       => get_the_title(),
				'description' => $torantejarat_is_contact
					? __( 'برای مشاوره فنی، استعلام قیمت یا هماهنگی بازدید، فرم را تکمیل کنید یا از راه‌های ارتباطی زیر با ما در تماس باشید.', 'torantejarat' )
					: '',
			)
		);
		?>
		<?php if ( $torantejarat_is_contact ) : ?>
			<section class="torantejarat-section torantejarat-contact-page-section">
				<div class="torantejarat-container torantejarat-contact-page">
					<div class="torantejarat-request-form torantejarat-prose torantejarat-contact-form-panel" data-torantejarat-request-ajax>
						<?php if ( $torantejarat_has_copy ) : ?>
							<div class="torantejarat-contact-copy"><?php the_content(); wp_link_pages(); ?></div>
						<?php endif; ?>
						<h2 class="torantejarat-contact-form-title"><?php esc_html_e( 'ارسال پیام', 'torantejarat' ); ?></h2>
						<p class="torantejarat-contact-form-lead"><?php esc_html_e( 'فرم زیر را پر کنید؛ کارشناسان ما در اولین فرصت کاری با شما تماس می‌گیرند.', 'torantejarat' ); ?></p>
						<?php torantejarat_request_form( $torantejarat_request_type ); ?>
						<script type="application/json" class="torantejarat-request-strings">
							<?php
							echo wp_json_encode(
								array(
									'success_title' => __( 'درخواست شما ثبت شد.', 'torantejarat' ),
									'success_text'  => __( 'کارشناس ما پس از بررسی، با همین شماره تماس می‌گیرد.', 'torantejarat' ),
									'error_title'   => __( 'درخواست ثبت نشد.', 'torantejarat' ),
									'name'          => __( 'نام را کامل وارد کنید (حداقل ۲ حرف).', 'torantejarat' ),
									'phone'         => __( 'شماره موبایل معتبر نیست؛ مثل 09123456789 وارد کنید.', 'torantejarat' ),
									'details'       => __( 'شرح درخواست کوتاه است؛ حداقل ۱۰ حرف بنویسید.', 'torantejarat' ),
									'extra'         => __( 'فیلدهای اختصاصی را کامل کنید (تاریخ/شهر/مدل).', 'torantejarat' ),
									'default'       => __( 'اطلاعات را کامل و درست وارد کنید: نام، موبایل معتبر (مثل 09123456789) و شرح درخواست.', 'torantejarat' ),
								),
								JSON_UNESCAPED_UNICODE
							);
							?>
						</script>
					</div>
					<aside class="torantejarat-contact-sidebar" aria-label="<?php esc_attr_e( 'اطلاعات تماس', 'torantejarat' ); ?>">
						<?php if ( has_excerpt() || has_post_thumbnail() ) : ?>
							<div class="torantejarat-contact-widget torantejarat-contact-intro">
								<?php
								if ( has_excerpt() ) {
									the_excerpt();
								}
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'large' );
								}
								?>
							</div>
						<?php endif; ?>
						<?php get_template_part( 'template-parts/contact-cards' ); ?>
						<?php
						$torantejarat_hours = torantejarat_option( 'contact', 'working_hours' );
						if ( $torantejarat_hours ) :
							?>
							<div class="torantejarat-contact-widget">
								<h2><?php torantejarat_icon( 'clock' ); ?><span><?php esc_html_e( 'ساعت کاری', 'torantejarat' ); ?></span></h2>
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
								$torantejarat_links[ $torantejarat_key ] = array( 'label' => $torantejarat_label, 'url' => $torantejarat_url, 'icon' => $torantejarat_key );
							}
						}
						if ( $torantejarat_links ) :
							?>
							<div class="torantejarat-contact-widget">
								<h2><?php esc_html_e( 'ما را دنبال کنید', 'torantejarat' ); ?></h2>
								<ul class="torantejarat-social-icons">
									<?php foreach ( $torantejarat_links as $torantejarat_link ) : ?>
										<li><a href="<?php echo esc_url( $torantejarat_link['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $torantejarat_link['label'] ); ?>"><?php torantejarat_icon( $torantejarat_link['icon'] ); ?><span><?php echo esc_html( $torantejarat_link['label'] ); ?></span></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<div class="torantejarat-contact-widget">
							<h2><?php torantejarat_icon( 'pin' ); ?><span><?php esc_html_e( 'نشانی ما روی نقشه', 'torantejarat' ); ?></span></h2>
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
					</aside>
				</div>
			</section>
			<script>
				( function () {
					var panel = document.querySelector( '[data-torantejarat-request-ajax]' );
					if ( ! panel || ! window.fetch || ! window.FormData ) {
						return;
					}
					var form = panel.querySelector( 'form.torantejarat-request-formfields' );
					var stringsEl = panel.querySelector( 'script.torantejarat-request-strings' );
					if ( ! form || ! stringsEl ) {
						return;
					}
					var strings = {};
					try {
						strings = JSON.parse( stringsEl.textContent || '{}' );
					} catch ( error ) {
						return;
					}

					function showNotice( kind, title, text ) {
						var old = panel.querySelector( '.torantejarat-form-notice.is-ajax' );
						if ( old ) {
							old.remove();
						}
						var notice = document.createElement( 'div' );
						notice.className = 'torantejarat-form-notice is-ajax is-' + ( 'success' === kind ? 'success' : 'error' );
						notice.setAttribute( 'role', 'success' === kind ? 'status' : 'alert' );
						var heading = document.createElement( 'p' );
						var strong = document.createElement( 'strong' );
						strong.textContent = title;
						heading.appendChild( strong );
						var body = document.createElement( 'p' );
						body.textContent = text;
						notice.appendChild( heading );
						notice.appendChild( body );
						form.parentNode.insertBefore( notice, form );
						if ( 'success' === kind ) {
							form.hidden = true;
						} else {
							notice.scrollIntoView( { behavior: 'smooth', block: 'center' } );
						}
					}

					form.addEventListener( 'submit', function ( event ) {
						event.preventDefault();
						var button = form.querySelector( 'button[type="submit"]' );
						if ( button ) {
							button.disabled = true;
							button.classList.add( 'is-loading' );
						}
						fetch( form.action, {
							method: 'POST',
							body: new FormData( form ),
							credentials: 'same-origin',
							redirect: 'follow'
						} )
							.then( function ( response ) {
								var target = new URL( response.url, window.location.href );
								var sent = target.searchParams.get( 'sent' );
								var reason = target.searchParams.get( 'reason' ) || '';
								if ( '1' === sent ) {
									showNotice( 'success', strings.success_title, strings.success_text );
									return;
								}
								var message = strings[ reason ] || strings.default;
								showNotice( 'error', strings.error_title, message );
							} )
							.catch( function () {
								HTMLFormElement.prototype.submit.call( form );
							} )
							.finally( function () {
								if ( button ) {
									button.disabled = false;
									button.classList.remove( 'is-loading' );
								}
							} );
					} );
				}() );
			</script>
		<?php else : ?>
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
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
