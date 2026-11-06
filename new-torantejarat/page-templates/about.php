<?php
/**
 * About layout: professional company story, 40-year timeline, advantages,
 * team/manufacturing placeholders and a closing call to action.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: درباره ما — چیدمان منبع */
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$torantejarat_contact_page   = get_page_by_path( 'contact' );
		$torantejarat_contact_url    = $torantejarat_contact_page ? get_permalink( $torantejarat_contact_page ) : home_url( '/' );
		$torantejarat_shop_url       = torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
		$torantejarat_quiz_page      = get_page_by_path( 'quiz' );
		$torantejarat_quiz_url       = $torantejarat_quiz_page && is_post_publicly_viewable( $torantejarat_quiz_page ) ? get_permalink( $torantejarat_quiz_page ) : '';
		$torantejarat_timeline       = array(
			array(
				'year'  => __( '۱۳۶۵', 'torantejarat' ),
				'title' => __( 'آغاز فعالیت', 'torantejarat' ),
				'text'  => __( 'شروع کار در حوزه الکترونیک با تمرکز بر خدمات فنی و تأمین قطعات تخصصی.', 'torantejarat' ),
			),
			array(
				'year'  => __( '۱۳۷۵', 'torantejarat' ),
				'title' => __( 'گسترش خدمات صنعتی', 'torantejarat' ),
				'text'  => __( 'توسعه همکاری با واحدهای صنعتی و ارائه خدمات بازرسی و نگهداری تجهیزات.', 'torantejarat' ),
			),
			array(
				'year'  => __( '۱۳۸۵', 'torantejarat' ),
				'title' => __( 'ورود به بازرسی چشمی', 'torantejarat' ),
				'text'  => __( 'طراحی و عرضه نخستین دوربین‌های شلنگی برای بازرسی داخل لوله و کانال.', 'torantejarat' ),
			),
			array(
				'year'  => __( '۱۳۹۵', 'torantejarat' ),
				'title' => __( 'تولید دانش‌بنیان', 'torantejarat' ),
				'text'  => __( 'تولید انبوه اندوسکوپ‌های صنعتی و شلنگی در بازه ۲ تا ۴۰۰ متر.', 'torantejarat' ),
			),
			array(
				'year'  => __( 'امروز', 'torantejarat' ),
				'title' => __( 'پشتیبانی سراسری', 'torantejarat' ),
				'text'  => __( 'ارسال سفارش به تمام نقاط ایران، پشتیبانی فنی و توسعه سبد محصولات.', 'torantejarat' ),
			),
		);
		$torantejarat_advantages     = array(
			array(
				'icon'  => 'shield',
				'title' => __( 'تضمین کیفیت', 'torantejarat' ),
				'text'  => __( 'هر دستگاه پیش از تحویل، مراحل کنترل کیفیت و تست عملکرد را طی می‌کند.', 'torantejarat' ),
			),
			array(
				'icon'  => 'scan',
				'title' => __( 'تصویر دقیق', 'torantejarat' ),
				'text'  => __( 'لنز و سنسور صنعتی برای تشخیص ترک، خوردگی و انسداد در فضاهای بسته.', 'torantejarat' ),
			),
			array(
				'icon'  => 'wrench',
				'title' => __( 'خدمات پس از فروش', 'torantejarat' ),
				'text'  => __( 'تعمیر، کالیبراسیون و تأمین قطعات یدکی در سراسر کشور.', 'torantejarat' ),
			),
			array(
				'icon'  => 'factory',
				'title' => __( 'تولید داخلی', 'torantejarat' ),
				'text'  => __( 'طراحی و مونتاژ دانش‌بنیان با پشتیبانی بلندمدت قطعات و تجهیزات.', 'torantejarat' ),
			),
		);
		?>
		<?php
		get_template_part(
			'template-parts/page-intro',
			null,
			array(
				'title'       => get_the_title(),
				'description' => __( 'چهار دهه تجربه در الکترونیک و بازرسی چشمی صنعتی؛ سازنده دوربین‌های اندوسکوپی و شلنگی برای صنایع ایران.', 'torantejarat' ),
			)
		);
		?>
		<section class="torantejarat-section torantejarat-about-hero-section">
			<div class="torantejarat-container torantejarat-about-hero">
				<div class="torantejarat-about-hero-copy">
					<span class="torantejarat-eyebrow"><?php esc_html_e( 'درباره توران تجارت', 'torantejarat' ); ?></span>
					<h2><?php esc_html_e( 'چهار دهه تجربه در بازرسی چشمی صنعتی', 'torantejarat' ); ?></h2>
					<p><?php esc_html_e( 'از خدمات فنی الکترونیک تا طراحی و تولید دوربین‌های اندوسکوپی صنعتی؛ تجربه‌ای که امروز در خدمت صنایع نفت و گاز، پتروشیمی، آب و فاضلاب، ساختمان و خودرو قرار دارد.', 'torantejarat' ); ?></p>
					<ul class="torantejarat-about-stats">
						<li><strong><?php esc_html_e( '+۴۰', 'torantejarat' ); ?></strong><span><?php esc_html_e( 'سال تجربه', 'torantejarat' ); ?></span></li>
						<li><strong><?php esc_html_e( '۲ تا ۴۰۰', 'torantejarat' ); ?></strong><span><?php esc_html_e( 'متر برد شلنگ', 'torantejarat' ); ?></span></li>
						<li><strong><?php esc_html_e( 'سراسری', 'torantejarat' ); ?></strong><span><?php esc_html_e( 'ارسال سفارش', 'torantejarat' ); ?></span></li>
					</ul>
				</div>
				<figure class="torantejarat-about-media torantejarat-about-hero-media">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large' ); ?>
					<?php else : ?>
						<span class="torantejarat-about-media-icon" aria-hidden="true"><?php torantejarat_icon( 'factory' ); ?></span>
						<figcaption><?php esc_html_e( 'خط تولید و مونتاژ تجهیزات بازرسی', 'torantejarat' ); ?></figcaption>
					<?php endif; ?>
				</figure>
			</div>
		</section>

		<section class="torantejarat-section torantejarat-soft-section torantejarat-about-story-section">
			<div class="torantejarat-container torantejarat-about-story">
				<div class="torantejarat-about-story-head">
					<span class="torantejarat-eyebrow"><?php esc_html_e( 'داستان ما', 'torantejarat' ); ?></span>
					<h2><?php esc_html_e( 'تخصصی که می‌توانید به آن اعتماد کنید', 'torantejarat' ); ?></h2>
				</div>
				<div class="torantejarat-about-story-body torantejarat-prose">
					<p><?php esc_html_e( 'توران تجارت با بیش از چهار دهه تجربه در حوزه الکترونیک، امروز یکی از پیشگامان طراحی و تولید دوربین‌های اندوسکوپی صنعتی و شلنگی در ایران است. محصولات ما در بازه ۲ تا ۴۰۰ متر، برای بازرسی چشمی دقیق داخل لوله‌ها، کانال‌ها، توربین‌ها و فضاهای بسته طراحی شده‌اند و در صنایع نفت و گاز، پتروشیمی، آب و فاضلاب، ساختمان و خودرو کاربرد دارند.', 'torantejarat' ); ?></p>
					<p><?php esc_html_e( 'تمرکز ما بر کیفیت تصویر، دوام تجهیزات و دقت تشخیص است. هر دستگاه پیش از تحویل، مراحل کنترل کیفیت را طی می‌کند و با پشتیبانی فنی، آموزش استفاده و خدمات تعمیر و نگهداری همراه است. تیم متخصص ما با شناخت نیاز واقعی هر پروژه، راهکار مناسب را پیشنهاد می‌دهد؛ از یک بورسکوپ ساده تا سیستم‌های تخصصی با ضبط و پردازش تصویر.', 'torantejarat' ); ?></p>
					<p><?php esc_html_e( 'ما شرکتی دانش‌بنیان با شبکه توزیع سراسری هستیم و سفارش‌ها را به تمام نقاط ایران ارسال می‌کنیم. تعهد ما، ارائه تجهیزات قابل اعتماد و مشاوره‌ای صادقانه است تا مشتریان بتوانند با اطمینان تصمیم بگیرند و در بلندمدت از سرمایه‌گذاری خود بهره ببرند.', 'torantejarat' ); ?></p>
				</div>
			</div>
		</section>

		<section class="torantejarat-section torantejarat-about-timeline-section">
			<div class="torantejarat-container">
				<div class="torantejarat-section-heading">
					<div>
						<span class="torantejarat-eyebrow"><?php esc_html_e( 'مسیر ما', 'torantejarat' ); ?></span>
						<h2><?php esc_html_e( '۴۰ سال تجربه، مرحله به مرحله', 'torantejarat' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'از نخستین گام‌ها در الکترونیک تا تولید انبوه اندوسکوپ‌های صنعتی.', 'torantejarat' ); ?></p>
				</div>
				<ol class="torantejarat-about-timeline">
					<?php foreach ( $torantejarat_timeline as $torantejarat_step ) : ?>
						<li>
							<time><?php echo esc_html( $torantejarat_step['year'] ); ?></time>
							<div>
								<h3><?php echo esc_html( $torantejarat_step['title'] ); ?></h3>
								<p><?php echo esc_html( $torantejarat_step['text'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>

		<section class="torantejarat-section torantejarat-soft-section torantejarat-about-values-section">
			<div class="torantejarat-container">
				<div class="torantejarat-section-heading">
					<div>
						<span class="torantejarat-eyebrow"><?php esc_html_e( 'مزیت‌های ما', 'torantejarat' ); ?></span>
						<h2><?php esc_html_e( 'چرا توران تجارت؟', 'torantejarat' ); ?></h2>
					</div>
				</div>
				<div class="torantejarat-about-values">
					<?php foreach ( $torantejarat_advantages as $torantejarat_advantage ) : ?>
						<div class="torantejarat-about-value">
							<span class="torantejarat-about-value-icon"><?php torantejarat_icon( $torantejarat_advantage['icon'] ); ?></span>
							<h3><?php echo esc_html( $torantejarat_advantage['title'] ); ?></h3>
							<p><?php echo esc_html( $torantejarat_advantage['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="torantejarat-section torantejarat-about-team-section">
			<div class="torantejarat-container">
				<div class="torantejarat-section-heading">
					<div>
						<span class="torantejarat-eyebrow"><?php esc_html_e( 'پشت صحنه', 'torantejarat' ); ?></span>
						<h2><?php esc_html_e( 'تیم فنی و خط تولید', 'torantejarat' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'طراحی، مونتاژ و پشتیبانی تجهیزات، همه در یک تیم متخصص.', 'torantejarat' ); ?></p>
				</div>
				<div class="torantejarat-about-media-grid">
					<figure class="torantejarat-about-media">
						<span class="torantejarat-about-media-icon" aria-hidden="true"><?php torantejarat_icon( 'factory' ); ?></span>
						<figcaption><?php esc_html_e( 'خط تولید و مونتاژ', 'torantejarat' ); ?></figcaption>
					</figure>
					<figure class="torantejarat-about-media is-alt">
						<span class="torantejarat-about-media-icon" aria-hidden="true"><?php torantejarat_icon( 'user' ); ?></span>
						<figcaption><?php esc_html_e( 'تیم فنی و پشتیبانی', 'torantejarat' ); ?></figcaption>
					</figure>
				</div>
			</div>
		</section>

		<section class="torantejarat-section torantejarat-about-cta-section">
			<div class="torantejarat-container">
				<div class="torantejarat-about-cta-band">
					<div>
						<h2><?php esc_html_e( 'برای انتخاب درست، با کارشناس ما مشورت کنید', 'torantejarat' ); ?></h2>
						<p><?php esc_html_e( 'با توجه به شرایط پروژه، بهترین دوربین و طول شلنگ را پیشنهاد می‌دهیم.', 'torantejarat' ); ?></p>
					</div>
					<div class="torantejarat-about-cta-actions">
						<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $torantejarat_contact_url ); ?>"><?php esc_html_e( 'تماس با کارشناس', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
						<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( $torantejarat_shop_url ); ?>"><?php esc_html_e( 'مشاهده محصولات', 'torantejarat' ); ?></a>
						<?php if ( $torantejarat_quiz_url ) : ?>
							<a class="torantejarat-text-link" href="<?php echo esc_url( $torantejarat_quiz_url ); ?>"><?php esc_html_e( 'راهنمای انتخاب دستگاه', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
