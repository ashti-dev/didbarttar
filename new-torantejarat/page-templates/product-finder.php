<?php
/**
 * Product Finder — multi-step wizard filtering real products by attributes.
 * Progressive enhancement: works without JS (form submit), enhanced with JS.
 *
 * @package ToranTejarat
 */

/* Template Name: راهنمای انتخاب — فیلتر محصول */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_steps = array(
	'usecase' => array(
		'label'   => __( 'چه چیزی را می‌خواهید ببینید؟', 'torantejarat' ),
		'options' => array(
			'plumbing'  => __( 'لوله و فاضلاب', 'torantejarat' ),
			'automotive' => __( 'موتور خودرو', 'torantejarat' ),
			'well'      => __( 'چاه و حفاری', 'torantejarat' ),
			'hvac'      => __( 'تأسیسات ساختمان', 'torantejarat' ),
		),
	),
	'length'  => array(
		'label'   => __( 'طول مسیر چقدر است؟', 'torantejarat' ),
		'options' => array(
			'short'  => __( 'تا ۱۰ متر', 'torantejarat' ),
			'medium' => __( '۱۰ تا ۳۰ متر', 'torantejarat' ),
			'long'   => __( 'بالای ۳۰ متر', 'torantejarat' ),
		),
	),
	'display' => array(
		'label'   => __( 'نمایشگر مستقل می‌خواهید؟', 'torantejarat' ),
		'options' => array(
			'yes' => __( 'بله، مانیتور مستقل', 'torantejarat' ),
			'no'  => __( 'خیر، موبایل/لپ‌تاپ', 'torantejarat' ),
		),
	),
);

$torantejarat_results = array();
$torantejarat_submitted = isset( $_GET['finder'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['finder'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( $torantejarat_submitted ) {
	$torantejarat_usecase = isset( $_GET['usecase'] ) ? sanitize_text_field( wp_unslash( $_GET['usecase'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$torantejarat_length  = isset( $_GET['length'] ) ? sanitize_text_field( wp_unslash( $_GET['length'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$torantejarat_query = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 10,
	);

	if ( $torantejarat_usecase ) {
		$torantejarat_cat_map = array(
			'plumbing'  => 'pipe',
			'automotive' => 'auto',
			'well'      => 'بازرسی-صنعتی',
			'hvac'      => 'کاربری-عمومی',
		);
		if ( isset( $torantejarat_cat_map[ $torantejarat_usecase ] ) ) {
			$torantejarat_query['category'] = $torantejarat_cat_map[ $torantejarat_usecase ];
		}
	}

	if ( $torantejarat_length ) {
		$torantejarat_len_map = array(
			'short'  => array( '۵ متر', '۱۰ متر' ),
			'medium' => array( '۱۰ متر', '۲۰ متر' ),
			'long'   => array( '۲۰ متر' ),
		);
		if ( isset( $torantejarat_len_map[ $torantejarat_length ] ) ) {
			$torantejarat_query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'pa_cable-length',
					'field'    => 'name',
					'terms'    => $torantejarat_len_map[ $torantejarat_length ],
				),
			);
		}
	}

	$torantejarat_results = wc_get_products( $torantejarat_query );
	$torantejarat_results = array_values( array_filter( $torantejarat_results, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
}

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '' ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container">
			<div class="torantejarat-finder" data-finder>
				<form class="torantejarat-finder-form" method="get" action="<?php echo esc_url( get_permalink() ); ?>" data-finder-form>
					<input type="hidden" name="finder" value="1">
					<?php foreach ( $torantejarat_steps as $torantejarat_step_key => $torantejarat_step ) : ?>
						<fieldset class="torantejarat-finder-step" data-step="<?php echo esc_attr( $torantejarat_step_key ); ?>">
							<legend><?php echo esc_html( $torantejarat_step['label'] ); ?></legend>
							<div class="torantejarat-finder-options">
								<?php foreach ( $torantejarat_step['options'] as $torantejarat_opt_key => $torantejarat_opt_label ) : ?>
									<label class="torantejarat-finder-option">
										<input type="radio" name="<?php echo esc_attr( $torantejarat_step_key ); ?>" value="<?php echo esc_attr( $torantejarat_opt_key ); ?>">
										<span><?php echo esc_html( $torantejarat_opt_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-finder-submit"><?php esc_html_e( 'نمایش نتایج', 'torantejarat' ); ?></button>
				</form>
				<?php if ( $torantejarat_submitted ) : ?>
					<div class="torantejarat-finder-results" data-finder-results>
						<?php if ( $torantejarat_results ) : ?>
							<h2><?php esc_html_e( 'مدل‌های مناسب شما', 'torantejarat' ); ?></h2>
							<div class="torantejarat-finder-grid">
								<?php foreach ( $torantejarat_results as $torantejarat_item ) : ?>
									<?php
									$torantejarat_item_id  = $torantejarat_item->get_id();
									$torantejarat_item_url = get_permalink( $torantejarat_item_id );
									?>
									<article class="torantejarat-finder-card">
										<a class="torantejarat-finder-card-image" href="<?php echo esc_url( $torantejarat_item_url ); ?>" tabindex="-1" aria-hidden="true">
											<?php if ( $torantejarat_item->get_image_id() ) : ?>
												<?php echo wp_get_attachment_image( $torantejarat_item->get_image_id(), 'medium', false, array( 'loading' => 'lazy' ) ); ?>
											<?php else : ?>
												<?php torantejarat_icon( 'scan' ); ?>
											<?php endif; ?>
										</a>
										<h3><a href="<?php echo esc_url( $torantejarat_item_url ); ?>"><?php echo esc_html( $torantejarat_item->get_name() ); ?></a></h3>
										<div class="torantejarat-finder-card-specs">
											<?php foreach ( torantejarat_product_attribute_items( $torantejarat_item, 3 ) as $torantejarat_attr ) : ?>
												<span><?php echo esc_html( $torantejarat_attr['label'] . ': ' . $torantejarat_attr['value'] ); ?></span>
											<?php endforeach; ?>
										</div>
										<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $torantejarat_item_url ); ?>"><?php esc_html_e( 'بررسی دستگاه', 'torantejarat' ); ?></a>
									</article>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<div class="torantejarat-empty-state">
								<?php torantejarat_icon( 'search' ); ?>
								<p><?php esc_html_e( 'محصولی با این مشخصات پیدا نشد. شرایط را تغییر دهید یا از کارشناس بپرسید.', 'torantejarat' ); ?></p>
								<?php
								$torantejarat_contact = get_page_by_path( 'contact' );
								if ( $torantejarat_contact ) :
									?>
									<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( get_permalink( $torantejarat_contact ) ); ?>"><?php esc_html_e( 'سؤال از کارشناس', 'torantejarat' ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
