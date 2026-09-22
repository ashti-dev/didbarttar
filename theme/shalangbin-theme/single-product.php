<?php
/**
 * Single product — exact port of reference productPage() template.
 *
 * Reference: product-template.mjs (build.mjs → product-g52.html)
 * All reference CSS classes (pdp-*) are kept identical for pixel parity.
 *
 * @package ShalangBin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	global $product;

	if ( ! $product instanceof WC_Product ) {
		continue;
	}

$fa          = 'shalangbin_fa_num';   // Persian numerals helper.
$reference   = function ( $key, $default = '' ) {
	// Product-level meta: catalog reference data (diameter, length, resolution...).
	$value = get_post_meta( get_the_ID(), '_shalangbin_' . $key, true );
	return $value ? $value : $default;
};
$length      = $reference( 'length', 0 );
$diameter    = $reference( 'diameter', 0 );
$resolution  = $reference( 'resolution', '—' );
$waterproof  = $reference( 'waterproof', 'IP68' );
$recording   = $reference( 'recording', '' );
$limitation  = $reference( 'limitation', '' );
$english     = $reference( 'en', '' );
$tag         = $reference( 'tag', '' );
$price       = $product->get_price();
$price_html  = $product->get_price_html();
$in_stock    = $product->is_in_stock();
$stock_label = $in_stock
	? __( 'موجود در انبار', 'shalangbin' )
	: __( 'نیازمند تأیید موجودی', 'shalangbin' );
?>
<div class="container product-breadcrumb-shell">
	<?php
	// Reference breadcrumbs: خانه / فروشگاه / دسته / نام محصول.
	$terms = get_the_terms( get_the_ID(), 'product_cat' );
	shalangbin_breadcrumbs( array(
		array( 'label' => __( 'فروشگاه', 'shalangbin' ), 'url' => function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ) ),
		$terms && ! is_wp_error( $terms ) ? array( 'label' => $terms[0]->name, 'url' => get_term_link( $terms[0] ) ) : null,
		array( 'label' => get_the_title(), 'url' => '' ),
	) );
	?>
</div>

<section class="pdp-top"><div class="container pdp-grid">
	<div class="pdp-gallery">
		<div class="pdp-photo">
			<?php if ( $tag ) : ?><span class="pdp-photo-tag"><?php echo esc_html( $tag ); ?></span><?php endif; ?>
			<button class="pdp-zoom" data-open-image aria-label="بزرگ‌نمایی تصویر <?php the_title_attribute(); ?>">
				<?php echo woocommerce_get_product_thumbnail( 'woocommerce_single' ); ?>
				<span><?php shalangbin_icon( 'search' ); ?> مشاهده تصویر در اندازه بزرگ</span>
				<?php // Zoom uses reference lightbox (pdp-lightbox) below. ?>
			</button>
		</div>
		<div class="pdp-gallery-bottom">
			<button class="pdp-thumb" data-open-image aria-label="مشاهده تصویر محصول">
				<?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
			</button>
			<p>جزئیات دستگاه را از نزدیک ببینید.<br><small>تصویر موجود در کاتالوگ</small></p>
			<a href="#product-video" class="pdp-video-link"><?php shalangbin_icon( 'play' ); ?> نمونه تصویر</a>
		</div>
	</div>
	<div class="pdp-summary">
		<div class="pdp-meta"><span><?php echo esc_html( $tag ); ?></span><span>شناسه: <b dir="ltr">SB-<?php echo esc_html( strtoupper( $product->get_sku() ? $product->get_sku() : get_the_ID() ) ); ?></b></span></div>
		<h1><?php the_title(); ?></h1>
		<?php if ( $english ) : ?><p class="pdp-english" dir="ltr"><?php echo esc_html( $english ); ?></p><?php endif; ?>
		<p class="pdp-description"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<div class="pdp-specs">
			<div><?php shalangbin_icon( 'pipe' ); ?><span>طول کابل<strong><?php echo esc_html( call_user_func( $fa, $length ) ); ?> متر</strong></span></div>
			<div><?php shalangbin_icon( 'scan' ); ?><span>قطر هد<strong><?php echo esc_html( call_user_func( $fa, $diameter ) ); ?> میلی‌متر</strong></span></div>
			<div><?php shalangbin_icon( 'play' ); ?><span>وضوح تصویر<strong dir="ltr"><?php echo esc_html( $resolution ); ?></strong></span></div>
			<div><?php shalangbin_icon( 'shield' ); ?><span>کلاس درج‌شده هد<strong dir="ltr"><?php echo esc_html( $waterproof ); ?></strong></span></div>
		</div>
		<a class="text-link pdp-spec-link" href="#specifications">مشاهده تمام مشخصات <?php shalangbin_icon( 'arrow' ); ?></a>

		<div class="pdp-purchase">
			<div class="pdp-choice"><span><?php esc_html_e( 'نسخه دستگاه', 'shalangbin' ); ?></span><strong><?php shalangbin_icon( 'check' ); ?> کابل <?php echo esc_html( call_user_func( $fa, $length ) ); ?> متری</strong></div>
			<div class="pdp-price">
				<div>
					<span class="pdp-stock"><?php echo esc_html( $stock_label ); ?></span>
					<small><?php esc_html_e( 'قیمت فروشگاه', 'shalangbin' ); ?></small>
					<strong><?php echo wp_kses_post( $price_html ); ?></strong>
				</div>
			</div>
			<?php woocommerce_template_single_add_to_cart(); ?>
			<p class="pdp-demo-note"><?php esc_html_e( 'قیمت و موجودی نهایی پس از تأیید اعلام می‌شود.', 'shalangbin' ); ?></p>
		</div>
		<div class="pdp-actions">
			<button data-compare="<?php echo esc_attr( get_the_ID() ); ?>" aria-pressed="false"><?php shalangbin_icon( 'compare' ); ?> مقایسه با دستگاه‌های دیگر</button>
			<a href="<?php echo esc_url( shalangbin_contact_url() ); ?>"><?php shalangbin_icon( 'headset' ); ?> مشاوره انتخاب</a>
		</div>
	</div>
</div></section>

<div class="container"><div class="pdp-assurance">
	<div><?php shalangbin_icon( 'scan' ); ?><span><b>انتخاب بر اساس مسیر شما</b><small>بررسی قطر، طول و شرایط محیط</small></span></div>
	<div><?php shalangbin_icon( 'compare' ); ?><span><b>مقایسه جزئیات فنی</b><small>تفاوت‌ها را پیش از انتخاب ببینید</small></span></div>
	<div><?php shalangbin_icon( 'tool' ); ?><span><b>بررسی خدمات دستگاه</b><small>ضمانت و قطعات را استعلام کنید</small></span></div>
</div>

<nav class="pdp-tabs" aria-label="بخش‌های محصول">
	<a href="#overview">معرفی دستگاه</a>
	<a href="#specifications">مشخصات فنی</a>
	<a href="#product-video">تصویر و ویدیو</a>
	<a href="#product-questions">پرسش‌های خرید</a>
</nav>

<section id="overview" class="pdp-overview">
	<div>
		<span class="eyebrow">نگاه نزدیک‌تر به دستگاه</span>
		<h2>انتخاب درست،<br>از شناخت مسیر شروع می‌شود.</h2>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
		<a class="text-link" href="<?php echo esc_url( home_url( '/' ) ); // TODO: پس از ساخت page-templates/quiz.php به آن متصل شود. ?>">این دستگاه برای من مناسب است؟ <?php shalangbin_icon( 'arrow' ); ?></a>
	</div>
	<aside>
		<span><?php shalangbin_icon( 'scan' ); ?> پیش از خرید بررسی کنید</span>
		<h3>محدودیت‌های مهم این مدل</h3>
		<p><?php echo esc_html( $limitation ); ?></p>
		<a href="<?php echo esc_url( shalangbin_contact_url() ); ?>">آماده‌سازی سؤال از کارشناس <?php shalangbin_icon( 'arrow' ); ?></a>
	</aside>
</section>

<section id="specifications" class="pdp-spec-section">
	<div class="section-heading">
		<div><span class="eyebrow">جزئیات، بدون ابهام</span><h2>مشخصات فنی دستگاه</h2></div>
		<span class="pdp-data-label">اطلاعات نمونه کاتالوگ</span>
	</div>
	<table class="spec-table">
		<caption class="sr-only">مشخصات <?php the_title_attribute(); ?></caption>
		<tbody>
			<tr><th scope="row">مدل دستگاه</th><td><?php the_title(); ?></td></tr>
			<tr><th scope="row">طول کابل</th><td><?php echo esc_html( call_user_func( $fa, $length ) ); ?> متر</td></tr>
			<tr><th scope="row">قطر هد</th><td dir="ltr"><?php echo esc_html( call_user_func( $fa, $diameter ) ); ?> میلی‌متر</td></tr>
			<tr><th scope="row">رزولوشن</th><td dir="ltr"><?php echo esc_html( $resolution ); ?></td></tr>
			<tr><th scope="row">کلاس درج‌شده هد</th><td dir="ltr"><?php echo esc_html( $waterproof ); ?></td></tr>
			<tr><th scope="row">قابلیت ضبط</th><td><?php echo esc_html( $recording ? $recording : 'در اطلاعات نمونه درج شده' ); ?></td></tr>
			<tr><th scope="row">اقلام همراه</th><td><?php echo esc_html( $reference( 'box', 'فهرست نهایی باید توسط فروشنده تأیید شود' ) ); ?></td></tr>
			<tr><th scope="row">ضمانت و خدمات</th><td><?php echo esc_html( $reference( 'warranty', 'شرایط نهایی هنوز اعلام نشده است' ) ); ?></td></tr>
		</tbody>
	</table>
</section>

<section id="product-video" class="pdp-video-section">
	<div class="pdp-video-art"><?php shalangbin_icon( 'play' ); ?><span>خروجی همان دستگاه، در شرایط واقعی</span></div>
	<div>
		<span class="eyebrow">قبل از خرید، تصویرش را ببینید</span>
		<h2>کیفیت را با خروجی واقعی بسنجید.</h2>
		<p>ویدیوی تأییدشده این مدل هنوز بارگذاری نشده است. نمونه تصویر را در فاصله و محیط مشابه کارتان درخواست کنید.</p>
		<a class="btn btn-outline" href="<?php echo esc_url( add_query_arg( 'topic', 'video', shalangbin_contact_url() ) ); ?>">درخواست نمونه تصویر <?php shalangbin_icon( 'arrow' ); ?></a>
	</div>
</section>

<section id="product-questions" class="pdp-faq">
	<div><span class="eyebrow">پیش از تصمیم نهایی</span><h2>پرسش‌های خرید</h2><p>پاسخ سؤال‌های اختصاصی مسیرتان را هم بگیرید.</p></div>
	<div class="faq">
		<details>
			<summary>آیا این دستگاه برای کاربرد من مناسب است؟</summary>
			<p>قطر ورودی، طول مسیر و شرایط محیط را با مشخصات تأییدشده مدل تطبیق دهید. راهنمای انتخاب فقط پیشنهاد اولیه ارائه می‌کند.</p>
		</details>
		<details>
			<summary>چه اقلامی همراه دستگاه ارسال می‌شود؟</summary>
			<p>اقلام همراه این نسخه هنوز تأیید نشده‌اند. پیش از سفارش، فهرست دقیق جعبه را از فروشنده بخواهید.</p>
		</details>
		<details>
			<summary>شرایط ضمانت و ارسال چگونه است؟</summary>
			<p>شرایط واقعی ضمانت، موجودی و ارسال هنوز به پیش‌نمایش اضافه نشده است. این موارد باید پیش از خرید مشخص شوند.</p>
		</details>
	</div>
</section></div>

<section class="section soft-section"><div class="container">
	<?php
	// Related products using the same reference markup and theme card.
	$related_ids = array_filter( wc_get_related_products( $product->get_id(), 4 ), 'is_numeric' );
	$related     = $related_ids ? wc_get_products( array( 'include' => $related_ids, 'limit' => 4, 'status' => array( 'publish' ) ) ) : array();
	if ( $related ) :
		?>
		<div class="section-heading"><div><span class="eyebrow">انتخاب‌های قابل مقایسه</span><h2>این دستگاه‌ها را هم بررسی کنید</h2><p>تفاوت مدل‌ها را بر اساس کاربرد و مشخصات بسنجید.</p></div><a class="text-link" href="<?php echo esc_url( shalangbin_compare_url() ); ?>">مقایسه دستگاه‌ها <?php shalangbin_icon( 'arrow' ); ?></a></div>
		<div class="product-grid related-grid">
			<?php
			foreach ( $related as $related_post ) {
				$post_object = get_post( $related_post->get_id() );
				setup_postdata( $GLOBALS['post'] =& $post_object );
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			?>
		</div>
	<?php endif; ?>
</div></section>

<div class="mobile-purchase">
	<div><small>قیمت</small><strong><?php echo wp_kses_post( $price_html ); ?></strong></div>
	<button class="btn btn-primary" data-add="<?php echo esc_attr( get_the_ID() ); ?>" data-add-url="<?php echo esc_url( get_permalink() ); ?>">افزودن به سبد <?php shalangbin_icon( 'cart' ); ?></button>
</div>

<dialog class="pdp-lightbox" aria-label="تصویر بزرگ <?php the_title_attribute(); ?>">
	<button class="icon-button" data-close-image aria-label="بستن تصویر"><?php shalangbin_icon( 'close' ); ?></button>
	<?php echo wp_get_attachment_image( $product->get_image_id(), 'woocommerce_single' ); ?>
	<p><?php the_title(); ?></p>
</dialog>

<?php
endwhile;

get_footer();