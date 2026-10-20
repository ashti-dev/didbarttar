<?php
/**
 * Home page — exact match to index.html structure.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'home' );
	return;
}

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( post_password_required() ) : ?>
			<section class="torantejarat-section"><div class="torantejarat-container"><?php the_content(); ?></div></section>
		<?php else : ?>

			<!-- Hero Section — exact match to index.html -->
			<section class="torantejarat-hero"><div class="torantejarat-container torantejarat-hero-grid">
				<div class="torantejarat-hero-copy">
					<span class="torantejarat-hero-kicker"><i></i> <?php echo esc_html( torantejarat_home( 'hero_kicker' ) ); ?></span>
					<h1><?php echo esc_html( torantejarat_home( 'hero_title_1' ) ); ?> <span><?php echo esc_html( torantejarat_home( 'hero_title_2' ) ); ?></span></h1>
					<p><?php echo esc_html( torantejarat_home( 'hero_lead' ) ); ?></p>
					<div class="torantejarat-hero-actions">
						<?php
						$torantejarat_shop_page = wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : '';
						$torantejarat_quiz_page = get_page_by_path( 'quiz' );
						?>
						<?php if ( $torantejarat_shop_page ) : ?><a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $torantejarat_shop_page ); ?>"><?php echo esc_html( torantejarat_home( 'hero_cta_primary' ) ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
						<?php if ( $torantejarat_quiz_page ) : ?><a class="torantejarat-btn torantejarat-btn-dark-ghost" href="<?php echo esc_url( get_permalink( $torantejarat_quiz_page ) ); ?>"><?php echo esc_html( torantejarat_home( 'hero_cta_secondary' ) ); ?> <?php torantejarat_icon( 'scan' ); ?></a><?php endif; ?>
					</div>
					<div class="torantejarat-hero-foot">
						<span><?php torantejarat_icon( 'cart' ); ?> <?php echo esc_html( torantejarat_home( 'hero_foot_1' ) ); ?></span>
						<span><?php torantejarat_icon( 'search' ); ?> <?php echo esc_html( torantejarat_home( 'hero_foot_2' ) ); ?></span>
					</div>
				</div>
				<?php $torantejarat_hero_slides = torantejarat_hero_slides(); ?>
				<?php if ( $torantejarat_hero_slides ) : ?>
					<link rel="preload" as="image" fetchpriority="high" href="<?php echo esc_url( $torantejarat_hero_slides[0]['src'] ); ?>">
					<div class="torantejarat-hero-visual"
						data-hero-slider-autoplay="7000"
						data-hero-slider-loop="true"
						data-hero-slides="<?php echo esc_attr( wp_json_encode( $torantejarat_hero_slides ) ); ?>">
						<div class="torantejarat-visual-grid"></div>
						<div class="torantejarat-visual-label"><span class="torantejarat-status-dot"></span> <?php echo esc_html( $torantejarat_hero_slides[0]['label'] ); ?> <span dir="ltr"><?php echo esc_html( sprintf( '%02d / %02d', 1, count( $torantejarat_hero_slides ) ) ); ?></span></div>
						<a href="<?php echo esc_url( $torantejarat_hero_slides[0]['link'] ); ?>"><img class="torantejarat-hero-device" src="<?php echo esc_url( $torantejarat_hero_slides[0]['src'] ); ?>" alt="<?php echo esc_attr( $torantejarat_hero_slides[0]['alt'] ); ?>" width="1020" height="1020" fetchpriority="high"></a>
						<div class="torantejarat-image-bracket torantejarat-bracket-t"></div>
						<div class="torantejarat-image-bracket torantejarat-bracket-b"></div>
					</div>
				<?php elseif ( has_post_thumbnail() ) : ?>
					<div class="torantejarat-hero-visual">
						<div class="torantejarat-visual-grid"></div>
						<div class="torantejarat-visual-label"><span class="torantejarat-status-dot"></span> <?php esc_html_e( 'INSPECTION EQUIPMENT', 'torantejarat' ); ?> <span dir="ltr">01 / 04</span></div>
						<?php the_post_thumbnail( 'large', array( 'class' => 'torantejarat-hero-device', 'fetchpriority' => 'high', 'loading' => false ) ); ?>
						<div class="torantejarat-image-bracket torantejarat-bracket-t"></div>
						<div class="torantejarat-image-bracket torantejarat-bracket-b"></div>
						<div class="torantejarat-visual-caption"><span><?php esc_html_e( 'از ورودی مسیر تا جزئیات پنهان', 'torantejarat' ); ?></span></div>
					</div>
				<?php endif; ?>
			</div></section>

			<!-- Benefit Strip — exact match to index.html -->
			<div class="torantejarat-benefit-strip"><div class="torantejarat-container">
				<span><?php torantejarat_icon( 'scan' ); ?> <?php echo esc_html( torantejarat_home( 'benefit_1' ) ); ?></span>
				<span><?php torantejarat_icon( 'cart' ); ?> <?php echo esc_html( torantejarat_home( 'benefit_2' ) ); ?></span>
				<span><?php torantejarat_icon( 'search' ); ?> <?php echo esc_html( torantejarat_home( 'benefit_3' ) ); ?></span>
				<span><?php torantejarat_icon( 'home' ); ?> <?php echo esc_html( torantejarat_home( 'benefit_4' ) ); ?></span>
			</div></div>

			<!-- Category Grid — exact match to index.html -->
			<?php if ( torantejarat_woocommerce_available() ) : ?>
				<?php ob_start(); torantejarat_product_categories( true ); $torantejarat_category_html = ob_get_clean(); ?>
				<?php if ( $torantejarat_category_html ) : ?>
				<section class="torantejarat-section"><div class="torantejarat-container">
					<div class="torantejarat-section-heading">
						<div>
							<span class="torantejarat-eyebrow"><?php echo esc_html( torantejarat_home( 'cat_eyebrow' ) ); ?></span>
							<h2><?php echo esc_html( torantejarat_home( 'cat_title' ) ); ?></h2>
							<p><?php echo esc_html( torantejarat_home( 'cat_text' ) ); ?></p>
						</div>
					</div>
					<?php echo $torantejarat_category_html; ?>
				</div></section>
				<?php endif; ?>

				<!-- Amazing deals — one level up, right under categories -->
				<?php get_template_part( 'template-parts/home-deals' ); ?>

				<!-- Finder: one box, three real-data filters, live results (quiz page untouched) -->
				<?php get_template_part( 'template-parts/home-finder' ); ?>

				<!-- Product Grid — exact match to index.html -->
				<?php get_template_part( 'template-parts/home-products' ); ?>
			<?php endif; ?>

			<!-- Field/Video Section — exact match to index.html -->
			<section class="torantejarat-section torantejarat-field-section"><div class="torantejarat-container torantejarat-field-grid">
				<div class="torantejarat-field-copy">
					<span class="torantejarat-eyebrow"><?php echo esc_html( torantejarat_home( 'field_eyebrow' ) ); ?></span>
					<h2><?php echo esc_html( torantejarat_home( 'field_title' ) ); ?></h2>
					<p><?php echo esc_html( torantejarat_home( 'field_text' ) ); ?></p>
					<ul class="torantejarat-check-list">
						<li><?php torantejarat_icon( 'check' ); ?> <?php echo esc_html( torantejarat_home( 'field_check_1' ) ); ?></li>
						<li><?php torantejarat_icon( 'check' ); ?> <?php echo esc_html( torantejarat_home( 'field_check_2' ) ); ?></li>
						<li><?php torantejarat_icon( 'check' ); ?> <?php echo esc_html( torantejarat_home( 'field_check_3' ) ); ?></li>
					</ul>
					<?php
					$torantejarat_contact_page = get_page_by_path( 'contact' );
					if ( $torantejarat_contact_page ) :
						?>
						<a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink( $torantejarat_contact_page ) . '?topic=video' ); ?>"><?php echo esc_html( torantejarat_home( 'field_link_label' ) ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="torantejarat-video-panel">
					<div class="torantejarat-video-top"><span class="torantejarat-status-dot"></span><span><?php echo esc_html( torantejarat_home( 'video_label_1' ) ); ?></span><span dir="ltr"><?php echo esc_html( torantejarat_home( 'video_label_2' ) ); ?></span></div>
					<video controls playsinline preload="none" aria-label="<?php echo esc_attr( torantejarat_home( 'video_aria' ) ); ?>">
						<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/video/tunnel-inspection.mp4' ); ?>" type="video/mp4">
						<?php esc_html_e( 'مرورگر شما پخش ویدیو را پشتیبانی نمی‌کند.', 'torantejarat' ); ?>
					</video>
					<p><?php echo esc_html( torantejarat_home( 'video_caption' ) ); ?></p>
				</div>
			</div></section>

			<!-- Services Section — rendered by template-parts/home-services.php -->
			<?php get_template_part( 'template-parts/home-services' ); ?>

			<!-- Best sellers ranked — renders only with proven sales -->
			<?php get_template_part( 'template-parts/home-bestsellers' ); ?>

			<!-- Latest products bottom slider (auto-hidden on small catalogs) -->
			<?php if ( torantejarat_woocommerce_available() ) : ?>
				<?php get_template_part( 'template-parts/home-latest' ); ?>
			<?php endif; ?>

			<!-- Editorial/Blog Section — exact match to index.html -->
			<section class="torantejarat-section torantejarat-soft-section"><div class="torantejarat-container">
				<div class="torantejarat-section-heading">
					<div>
						<span class="torantejarat-eyebrow"><?php echo esc_html( torantejarat_home( 'blog_eyebrow' ) ); ?></span>
						<h2><?php echo esc_html( torantejarat_home( 'blog_title' ) ); ?></h2>
						<p><?php echo esc_html( torantejarat_home( 'blog_text' ) ); ?></p>
					</div>
					<?php
					$torantejarat_blog_page = get_page_by_path( 'blog' );
					if ( $torantejarat_blog_page ) :
						?>
						<a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink( $torantejarat_blog_page ) ); ?>"><?php echo esc_html( torantejarat_home( 'blog_link' ) ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="torantejarat-editorial-grid">
					<?php
					$torantejarat_posts = new \WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
					if ( $torantejarat_posts->have_posts() ) :
						while ( $torantejarat_posts->have_posts() ) :
							$torantejarat_posts->the_post();
							?>
							<a class="torantejarat-editorial-card" href="<?php the_permalink(); ?>">
								<div class="torantejarat-editorial-art torantejarat-art-0">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium_large' ); ?>
									<?php else : ?>
										<?php torantejarat_icon( 'scan' ); ?><span dir="ltr">FIELD NOTES / 01</span>
									<?php endif; ?>
								</div>
								<div class="torantejarat-editorial-body">
									<span class="torantejarat-eyebrow"><?php esc_html_e( 'راهنمای انتخاب', 'torantejarat' ); ?></span>
									<h3><?php the_title(); ?></h3>
									<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
									<span class="torantejarat-text-link"><?php esc_html_e( 'ادامه مطلب', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></span>
								</div>
							</a>
							<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div></section>

			<!-- FAQ Section — exact match to index.html -->
			<section class="torantejarat-section"><div class="torantejarat-container torantejarat-faq-grid">
				<div>
					<span class="torantejarat-eyebrow"><?php echo esc_html( torantejarat_home( 'faq_eyebrow' ) ); ?></span>
					<h2><?php echo esc_html( torantejarat_home( 'faq_title' ) ); ?></h2>
					<?php
					$torantejarat_contact_page = get_page_by_path( 'contact' );
					if ( $torantejarat_contact_page ) :
						?>
						<a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink( $torantejarat_contact_page ) ); ?>"><?php echo esc_html( torantejarat_home( 'faq_link' ) ); ?> <?php torantejarat_icon( 'arrow' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="torantejarat-faq">
					<details>
						<summary><?php echo esc_html( torantejarat_home_faq_slot( 0, 'q' ) ); ?></summary>
						<p><?php echo esc_html( torantejarat_home_faq_slot( 0, 'a' ) ); ?></p>
					</details>
					<?php if ( torantejarat_is_preview() ) : ?>
						<details>
							<summary><?php esc_html_e( 'آیا قیمت‌ها و موجودی سایت قطعی هستند؟', 'torantejarat' ); ?></summary>
							<p><?php esc_html_e( 'این نسخه، پیش‌نمایش فروشگاه است. قیمت‌ها و مشخصات نمونه‌اند و امکان پرداخت یا ثبت سفارش واقعی فعال نیست.', 'torantejarat' ); ?></p>
						</details>
					<?php endif; ?>
					<details>
						<summary><?php echo esc_html( torantejarat_home_faq_slot( 1, 'q' ) ); ?></summary>
						<p><?php echo esc_html( torantejarat_home_faq_slot( 1, 'a' ) ); ?></p>
					</details>
					<details>
						<summary><?php echo esc_html( torantejarat_home_faq_slot( 2, 'q' ) ); ?></summary>
						<p><?php echo esc_html( torantejarat_home_faq_slot( 2, 'a' ) ); ?></p>
					</details>
<?php foreach ( torantejarat_home_faq_extra() as $torantejarat_faq_item ) : ?>
					<details>
						<summary><?php echo esc_html( $torantejarat_faq_item['q'] ); ?></summary>
						<p><?php echo esc_html( $torantejarat_faq_item['a'] ); ?></p>
					</details>
<?php endforeach; ?>
				</div>
			</div></section>

		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
