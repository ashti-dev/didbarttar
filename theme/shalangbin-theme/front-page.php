<?php
/**
 * Front page — sections rendered from WooCommerce products + WP pages.
 *
 * @package ShalangBin
 */

get_header();
?>

<?php
// ---------- Hero ----------
$hero_title    = get_theme_mod( 'shalangbin_hero_title', 'نگاه دقیق‌تر. انتخاب مطمئن‌تر.' );
$hero_desc     = get_theme_mod( 'shalangbin_hero_desc', 'دوربین شلنگی مناسب خودرو، لوله و صنعت را بر اساس کاربرد و مشخصات انتخاب و مقایسه کنید.' );
$hero_btn_text = get_theme_mod( 'shalangbin_hero_btn', 'مشاهده فروشگاه' );
?>
<section class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<span class="hero-kicker"><i></i> <?php esc_html_e( 'نگاه دقیق‌تر. انتخاب مطمئن‌تر.', 'shalangbin' ); ?></span>
			<h1><?php esc_html_e( 'قبل از باز کردن،', 'shalangbin' ); ?><br><span><?php esc_html_e( 'داخلش را ببین.', 'shalangbin' ); ?></span></h1>
			<p><?php esc_html_e( 'برای هر مسیر پنهان، ابزار مناسبی وجود دارد.', 'shalangbin' ); ?><br><?php esc_html_e( 'دوربین بازرسی را بر اساس کارتان انتخاب کنید؛', 'shalangbin' ); ?><br class="desktop-only"> <?php esc_html_e( 'با مشخصات روشن و مقایسه‌ای ساده.', 'shalangbin' ); ?></p>
			<div class="hero-actions">
				<a class="btn btn-primary" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'پیدا کردن دوربین مناسب', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?>
				</a>
				<a class="btn btn-dark-ghost" href="<?php echo esc_url( shalangbin_quiz_url() ); ?>"><?php esc_html_e( 'راهنمای انتخاب', 'shalangbin' ); ?> <?php shalangbin_icon( 'scan' ); ?></a>
			</div>
			<div class="hero-foot">
				<span><?php shalangbin_icon( 'compare' ); ?> <?php esc_html_e( 'مقایسه فنی دستگاه‌ها', 'shalangbin' ); ?></span>
				<span><?php shalangbin_icon( 'headset' ); ?> <?php esc_html_e( 'مسیر مشاوره تخصصی', 'shalangbin' ); ?></span>
			</div>
		</div>
		<div class="hero-visual">
			<div class="visual-grid"></div>
			<div class="visual-label"><span class="status-dot"></span> <?php esc_html_e( 'INSPECTION EQUIPMENT', 'shalangbin' ); ?> <span>01 / 04</span></div>
			<img class="hero-device" src="<?php echo esc_url( SHALANGBIN_URI . '/assets/images/device-case.webp' ); ?>" alt="<?php esc_attr_e( 'دوربین بازرسی کیف‌دار با کابل حلقه‌شده و نمایشگر', 'shalangbin' ); ?>" width="1020" height="1020" fetchpriority="high">
			<div class="visual-caption">
				<span><?php esc_html_e( 'از ورودی مسیر تا جزئیات پنهان', 'shalangbin' ); ?></span>
				<a href="<?php echo esc_url( shalangbin_first_product_url() ); ?>"><?php esc_html_e( 'بررسی دستگاه', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
			</div>
			<div class="image-bracket bracket-t"></div>
			<div class="image-bracket bracket-b"></div>
		</div>
	</div>
</section>

<div class="benefit-strip">
	<div class="container">
		<span><?php shalangbin_icon( 'scan' ); ?> <?php esc_html_e( 'انتخاب بر اساس کاربرد', 'shalangbin' ); ?></span>
		<span><?php shalangbin_icon( 'compare' ); ?> <?php esc_html_e( 'مشخصات قابل مقایسه', 'shalangbin' ); ?></span>
		<span><?php shalangbin_icon( 'tool' ); ?> <?php esc_html_e( 'مسیر تعمیر و نگهداری', 'shalangbin' ); ?></span>
		<span><?php shalangbin_icon( 'book' ); ?> <?php esc_html_e( 'راهنمای استفاده و انتخاب', 'shalangbin' ); ?></span>
	</div>
</div>

<?php
// ---------- Categories ----------
if ( function_exists( 'is_woocommerce' ) ) :
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'number'     => 8,
		'parent'     => 0,
	) );
	if ( ! is_wp_error( $terms ) && $terms ) :
		?>
		<section class="section">
	<div class="container">
		<?php // Reference: sectionHead('از کارتان شروع کنید','کجا را می‌خواهید ببینید؟','مسیر انتخاب دستگاه مناسب، از شناخت کاربرد شروع می‌شود.') ?>
		<div class="section-heading">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'از کارتان شروع کنید', 'shalangbin' ); ?></span>
				<h2><?php esc_html_e( 'کجا را می‌خواهید ببینید؟', 'shalangbin' ); ?></h2>
				<p><?php esc_html_e( 'مسیر انتخاب دستگاه مناسب، از شناخت کاربرد شروع می‌شود.', 'shalangbin' ); ?></p>
			</div>
		</div>
		<div class="category-grid">
			<?php
			$icons = array( 'scan', 'pipe', 'car', 'factory', 'tool', 'book', 'shield', 'clock' );
			foreach ( $terms as $i => $term ) :
				$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$sub      = $term->description ? wp_trim_words( $term->description, 10 ) : '';
				?>
				<a class="category-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="category-number"><?php echo esc_html( shalangbin_fa_num( $i + 1 < 10 ? '0' . ( $i + 1 ) : $i + 1 ) ); ?></span>
					<span class="category-icon">
						<?php
						if ( $thumb_id ) {
							echo wp_get_attachment_image( $thumb_id, 'thumbnail', false, array( 'class' => 'category-icon' ) );
						} else {
							shalangbin_icon( $icons[ $i % count( $icons ) ] );
						}
						?>
					</span>
					<h3><?php echo esc_html( $term->name ); ?></h3>
					<?php if ( $sub ) : ?><p><?php echo esc_html( $sub ); ?></p><?php endif; ?>
					<span class="category-arrow"><?php shalangbin_icon( 'arrow' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
	<?php endif;
endif;
?>

<?php
// ---------- Featured products ----------
$featured_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 8,
	// Real "featured" products when Woo is active, otherwise latest products.
	'tax_query'      => function_exists( 'wc_get_product' ) ? array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'slug'     => 'featured',
			'operator' => 'IN',
		),
	) : array(),
);
$featured = new WP_Query( $featured_args );
// Fallback: no featured products flagged yet → show latest products instead.
if ( function_exists( 'wc_get_product' ) && ! $featured->have_posts() ) {
	$featured_args['tax_query'] = array();
	$featured                   = new WP_Query( $featured_args );
}
if ( $featured->have_posts() ) :
	?>
	<section class="section soft-section" id="featured-products">
		<div class="container">
			<div class="section-heading">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'کاتالوگ تخصصی', 'shalangbin' ); ?></span>
					<h2><?php esc_html_e( 'هر دستگاه، برای یک مسئله', 'shalangbin' ); ?></h2>
					<p><?php esc_html_e( 'مشخصات کلیدی را ببینید و گزینه‌های نزدیک را مقایسه کنید.', 'shalangbin' ); ?></p>
				</div>
				<a class="text-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>"><?php esc_html_e( 'همه دستگاه‌ها', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
			</div>
			<div class="product-grid">
				<?php
				while ( $featured->have_posts() ) :
					$featured->the_post();
					get_template_part( 'template-parts/product-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<p class="sample-note"><?php shalangbin_icon( 'scan' ); ?> <?php esc_html_e( 'اطلاعات این کاتالوگ نمونه است؛ مشخصات، قیمت و موجودی باید پیش از خرید تأیید شوند.', 'shalangbin' ); ?></p>
		</div>
	</section>
<?php endif; ?>

<?php
// ---------- Finder banner (reference: finder-banner) ----------
$quiz_url = shalangbin_quiz_url();
?>
<section class="section">
	<div class="container">
		<div class="finder-banner">
			<div class="finder-symbol"><?php shalangbin_icon( 'scan' ); ?><span>FIND YOUR FIT</span></div>
			<div>
				<span class="eyebrow"><?php esc_html_e( 'انتخاب را ساده‌تر کنیم', 'shalangbin' ); ?></span>
				<h2><?php esc_html_e( 'بین چند مدل مردد هستید؟', 'shalangbin' ); ?></h2>
				<p><?php esc_html_e( 'کاربرد، طول مسیر و بودجه‌تان را بگویید.', 'shalangbin' ); ?><br><?php esc_html_e( 'گزینه‌های سازگار با پاسخ‌هایتان را بررسی کنید.', 'shalangbin' ); ?></p>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( $quiz_url ); ?>"><?php esc_html_e( 'شروع راهنمای انتخاب', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
		</div>
	</div>
</section>

<?php
// ---------- Field / video section (reference: field-section) ----------
$video_url = SHALANGBIN_URI . '/assets/video/tunnel-inspection.mp4';
?>
<section class="section field-section">
	<div class="container field-grid">
		<div class="field-copy">
			<span class="eyebrow"><?php esc_html_e( 'پیش از تصمیم، دقیق‌تر ببینید', 'shalangbin' ); ?></span>
			<h2><?php esc_html_e( 'فقط تصویر دستگاه کافی نیست؛', 'shalangbin' ); ?><br><?php esc_html_e( 'خروجی آن را هم ببینید.', 'shalangbin' ); ?></h2>
			<p><?php esc_html_e( 'برای ارزیابی هر مدل، نمونه تصویر همان دستگاه در شرایط مشابه کارتان را درخواست کنید.', 'shalangbin' ); ?></p>
			<ul class="check-list">
				<li><?php shalangbin_icon( 'check' ); ?> <?php esc_html_e( 'وضوح جزئیات در فاصله کاری', 'shalangbin' ); ?></li>
				<li><?php shalangbin_icon( 'check' ); ?> <?php esc_html_e( 'نور و میدان دید در محیط مورد نظر', 'shalangbin' ); ?></li>
				<li><?php shalangbin_icon( 'check' ); ?> <?php esc_html_e( 'مشخص بودن مدل و شرایط تصویربرداری', 'shalangbin' ); ?></li>
			</ul>
			<a class="text-link" href="<?php echo esc_url( home_url( '/contact/?topic=video' ) ); ?>"><?php esc_html_e( 'آماده‌سازی درخواست نمونه تصویر', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
		</div>
		<div class="video-panel">
			<div class="video-top"><span class="status-dot"></span><span>INSIDE THE FIELD</span><span dir="ltr">DEMO / 01</span></div>
			<video controls playsinline preload="none" aria-label="<?php esc_attr_e( 'ویدیوی نمایشی بازرسی مسیر', 'shalangbin' ); ?>">
				<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
				<?php esc_html_e( 'مرورگر شما پخش ویدیو را پشتیبانی نمی‌کند.', 'shalangbin' ); ?>
			</video>
			<p><?php esc_html_e( 'ویدیوی نمایشی قالب؛ خروجی تأییدشده هیچ‌یک از مدل‌های فروشگاه نیست.', 'shalangbin' ); ?></p>
		</div>
	</div>
</section>

<?php
// ---------- Services (reference: service-grid) ----------
$services = array(
	array(
		'icon'  => 'clock',
		'title' => __( 'برای یک پروژه نیاز دارید؟', 'shalangbin' ),
		'desc'  => __( 'اطلاعات پروژه را برای بررسی امکان اجاره آماده کنید.', 'shalangbin' ),
		'link'  => home_url( '/rent/' ),
		'label' => __( 'بررسی اجاره', 'shalangbin' ),
	),
	array(
		'icon'  => 'tool',
		'title' => __( 'دستگاه نیاز به بررسی دارد؟', 'shalangbin' ),
		'desc'  => __( 'مدل دستگاه و نشانه‌های خرابی را در یک درخواست جمع کنید.', 'shalangbin' ),
		'link'  => home_url( '/repairs/' ),
		'label' => __( 'درخواست بررسی', 'shalangbin' ),
	),
	array(
		'icon'  => 'book',
		'title' => __( 'دقیق‌تر کار کنید', 'shalangbin' ),
		'desc'  => __( 'راهنماهای انتخاب، آماده‌سازی و نگهداری دستگاه را بخوانید.', 'shalangbin' ),
		'link'  => home_url( '/academy/' ),
		'label' => __( 'ورود به آکادمی', 'shalangbin' ),
	),
);
?>
<section class="section">
	<div class="container">
		<div class="section-heading">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'همراه تجهیزات شما', 'shalangbin' ); ?></span>
				<h2><?php esc_html_e( 'از انتخاب تا نگهداری', 'shalangbin' ); ?></h2>
				<p><?php esc_html_e( 'مسیر خدمات را متناسب با نیازتان دنبال کنید.', 'shalangbin' ); ?></p>
			</div>
		</div>
		<div class="service-grid">
			<?php foreach ( $services as $service ) : ?>
				<a class="service-card" href="<?php echo esc_url( $service['link'] ); ?>">
					<?php shalangbin_icon( $service['icon'] ); ?>
					<h3><?php echo esc_html( $service['title'] ); ?></h3>
					<p><?php echo esc_html( $service['desc'] ); ?></p>
					<span class="text-link"><?php echo esc_html( $service['label'] ); ?> <?php shalangbin_icon( 'arrow' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
// ---------- Latest posts (reference: editorial-grid) ----------
$posts_q = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
if ( $posts_q->have_posts() ) :
	$art_classes = array( 'art-0', 'art-1', 'art-2' );
	$i = 0;
	?>
	<section class="section soft-section">
		<div class="container">
			<div class="section-heading">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'مجله شلنگ‌بین', 'shalangbin' ); ?></span>
					<h2><?php esc_html_e( 'قبل از خرید، بیشتر بدانید', 'shalangbin' ); ?></h2>
					<p><?php esc_html_e( 'یادداشت‌های کوتاه برای سؤال‌های مهم.', 'shalangbin' ); ?></p>
				</div>
				<a class="text-link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'همه راهنماها', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
			</div>
			<div class="editorial-grid">
				<?php
				while ( $posts_q->have_posts() ) :
					$posts_q->the_post();
					?>
					<a class="editorial-card" href="<?php the_permalink(); ?>">
						<div class="editorial-art <?php echo esc_attr( $art_classes[ $i % 3 ] ); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large' ); ?>
							<?php else : ?>
								<?php shalangbin_icon( 'scan' ); ?><span dir="ltr">FIELD NOTES / 0<?php echo (int) ( $i + 1 ); ?></span>
							<?php endif; ?>
						</div>
						<div class="editorial-body">
							<span class="eyebrow"><?php echo esc_html( shalangbin_date( 'j F Y' ) ); ?></span>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							<span class="text-link"><?php esc_html_e( 'ادامه مطلب', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></span>
						</div>
					</a>
					<?php
					$i++;
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
// ---------- FAQ (reference: faq-grid) ----------
$faqs = array(
	array(
		'q' => __( 'از کجا بفهمم کدام مدل مناسب من است؟', 'shalangbin' ),
		'a' => __( 'از راهنمای انتخاب شروع کنید. سپس قطر ورودی، شرایط محیط و محدودیت‌های مسیر را با مشخصات تأییدشده مدل تطبیق دهید.', 'shalangbin' ),
	),
	array(
		'q' => __( 'آیا قیمت‌ها و موجودی سایت قطعی هستند؟', 'shalangbin' ),
		'a' => __( 'این نسخه، پیش‌نمایش فروشگاه است. قیمت‌ها و مشخصات نمونه‌اند و امکان پرداخت یا ثبت سفارش واقعی فعال نیست.', 'shalangbin' ),
	),
	array(
		'q' => __( 'آیا همه دوربین‌ها برای محیط مرطوب مناسب‌اند؟', 'shalangbin' ),
		'a' => __( 'محدوده مقاومت هر بخش دستگاه باید از دفترچه همان مدل بررسی شود. مشخصات هد لزوماً درباره نمایشگر یا اتصال‌ها صدق نمی‌کند.', 'shalangbin' ),
	),
	array(
		'q' => __( 'برای مشاوره چه اطلاعاتی آماده کنم؟', 'shalangbin' ),
		'a' => __( 'کاربرد، قطر ورودی، طول مسیر، شرایط محیط و بودجه تقریبی. عکس محل بازرسی هم می‌تواند به بررسی بهتر کمک کند.', 'shalangbin' ),
	),
);
?>
<section class="section">
	<div class="container faq-grid">
		<div>
			<span class="eyebrow"><?php esc_html_e( 'پرسش‌های متداول', 'shalangbin' ); ?></span>
			<h2><?php esc_html_e( 'از همین‌جا', 'shalangbin' ); ?><br><?php esc_html_e( 'ابهام‌ها را برطرف کنید.', 'shalangbin' ); ?></h2>
			<a class="text-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'پرسش دیگری دارید؟', 'shalangbin' ); ?> <?php shalangbin_icon( 'arrow' ); ?></a>
		</div>
		<div class="faq">
			<?php foreach ( $faqs as $faq ) : ?>
				<details>
					<summary><?php echo esc_html( $faq['q'] ); ?></summary>
					<p><?php echo esc_html( $faq['a'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
// ---------- Trust strip (kept from WP version; placed after FAQ) ----------
?>
<section class="home-section home-trust">
	<div class="container trust-grid">
		<div class="trust-item"><?php shalangbin_icon( 'shield' ); ?><div><b><?php esc_html_e( 'ضمانت اصالت', 'shalangbin' ); ?></b><p><?php esc_html_e( 'کالای اصل با گارانتی معتبر', 'shalangbin' ); ?></p></div></div>
		<div class="trust-item"><?php shalangbin_icon( 'tool' ); ?><div><b><?php esc_html_e( 'خدمات پس از فروش', 'shalangbin' ); ?></b><p><?php esc_html_e( 'تعمیرات تخصصی و تأمین قطعات', 'shalangbin' ); ?></p></div></div>
		<div class="trust-item"><?php shalangbin_icon( 'clock' ); ?><div><b><?php esc_html_e( 'ارسال سریع', 'shalangbin' ); ?></b><p><?php esc_html_e( 'ارسال به سراسر ایران', 'shalangbin' ); ?></p></div></div>
		<div class="trust-item"><?php shalangbin_icon( 'phone' ); ?><div><b><?php esc_html_e( 'مشاوره تخصصی', 'shalangbin' ); ?></b><p><?php esc_html_e( 'پاسخ کارشناسان قبل از خرید', 'shalangbin' ); ?></p></div></div>
	</div>
</section>

<?php
get_footer();
