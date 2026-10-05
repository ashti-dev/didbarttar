<?php
/**
 * Product Finder — 4-step wizard matching the HTML kit quiz contract.
 * Steps: usecase (category) → min cable length → max head diameter → budget.
 * Each step has an explicit "not sure" skip. Results sort cheapest-first.
 * Progressive enhancement: works without JS (plain form submit).
 *
 * @package ToranTejarat
 */

/* Template Name: راهنمای انتخاب — فیلتر محصول */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_steps = array(
	'usecase'  => array(
		'label'   => __( 'بیشتر برای چه کاری نیاز دارید؟', 'torantejarat' ),
		'hint'    => __( 'کاربرد اصلی، اولین معیار محدود کردن گزینه‌هاست.', 'torantejarat' ),
		'options' => array(
			'plumbing'   => __( 'لوله و فاضلاب', 'torantejarat' ),
			'automotive' => __( 'موتور خودرو', 'torantejarat' ),
			'industrial' => __( 'بازرسی صنعتی', 'torantejarat' ),
			'general'    => __( 'کاربری عمومی', 'torantejarat' ),
			''           => __( 'هنوز مطمئن نیستم', 'torantejarat' ),
		),
	),
	'length'   => array(
		'label'   => __( 'مسیر شما تقریباً چقدر طول دارد؟', 'torantejarat' ),
		'hint'    => __( 'دستگاه باید حداقل طول مورد نیاز شما را پوشش دهد.', 'torantejarat' ),
		'options' => array(
			'5'  => __( 'تا ۵ متر', 'torantejarat' ),
			'10' => __( 'تا ۱۰ متر', 'torantejarat' ),
			'20' => __( 'تا ۲۰ متر', 'torantejarat' ),
			'30' => __( 'بیش از ۲۰ متر', 'torantejarat' ),
			''   => __( 'هنوز اندازه نگرفته‌ام', 'torantejarat' ),
		),
	),
	'diameter' => array(
		'label'   => __( 'قطر ورودی مسیر چقدر است؟', 'torantejarat' ),
		'hint'    => __( 'اندازه‌ها فقط برای غربال اولیه‌اند؛ فضای عبور و خمش باید جداگانه بررسی شود.', 'torantejarat' ),
		'options' => array(
			'5'  => __( 'حداکثر ۵ میلی‌متر', 'torantejarat' ),
			'8'  => __( 'حداکثر ۸ میلی‌متر', 'torantejarat' ),
			'20' => __( 'تا ۲۰ میلی‌متر', 'torantejarat' ),
			''   => __( 'بزرگ‌تر یا نامشخص', 'torantejarat' ),
		),
	),
	'budget'   => array(
		'label'   => __( 'بودجه تقریبی شما چقدر است؟', 'torantejarat' ),
		'hint'    => __( 'مقایسه بر اساس قیمت نمونه کاتالوگ انجام می‌شود.', 'torantejarat' ),
		'options' => array(
			'7000000'  => __( 'تا ۷ میلیون تومان', 'torantejarat' ),
			'10000000' => __( 'تا ۱۰ میلیون تومان', 'torantejarat' ),
			'15000000' => __( 'تا ۱۵ میلیون تومان', 'torantejarat' ),
			''         => __( 'فعلاً محدودیتی تعیین نمی‌کنم', 'torantejarat' ),
		),
	),
);

$torantejarat_answers = array();
foreach ( array_keys( $torantejarat_steps ) as $torantejarat_key ) {
	$torantejarat_answers[ $torantejarat_key ] = isset( $_GET[ $torantejarat_key ] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_text_field( wp_unslash( $_GET[ $torantejarat_key ] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';
}
$torantejarat_submitted = isset( $_GET['finder'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['finder'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$torantejarat_results = array();
$torantejarat_chosen  = array();
if ( $torantejarat_submitted && torantejarat_woocommerce_available() ) {
	$torantejarat_cat_map = array(
		'plumbing'   => 'pipe',
		'automotive' => 'auto',
		'industrial' => 'بازرسی-صنعتی',
		'general'    => 'کاربری-عمومی',
	);
	$torantejarat_query  = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 50,
	);
	if ( '' !== $torantejarat_answers['usecase'] && isset( $torantejarat_cat_map[ $torantejarat_answers['usecase'] ] ) ) {
		$torantejarat_query['category'] = $torantejarat_cat_map[ $torantejarat_answers['usecase'] ];
	}
	$torantejarat_min_length = is_numeric( $torantejarat_answers['length'] ) ? (float) $torantejarat_answers['length'] : 0;
	$torantejarat_max_diam   = is_numeric( $torantejarat_answers['diameter'] ) ? (float) $torantejarat_answers['diameter'] : 0;
	$torantejarat_budget     = is_numeric( $torantejarat_answers['budget'] ) ? (float) $torantejarat_answers['budget'] : 0;

	foreach ( wc_get_products( $torantejarat_query ) as $torantejarat_candidate ) {
		if ( ! $torantejarat_candidate instanceof \WC_Product || ! $torantejarat_candidate->is_visible() ) {
			continue;
		}
		if ( $torantejarat_min_length > 0 ) {
			$torantejarat_cable = torantejarat_finder_meters( $torantejarat_candidate->get_attribute( 'pa_cable-length' ) );
			if ( null === $torantejarat_cable || $torantejarat_cable < $torantejarat_min_length ) {
				continue;
			}
		}
		if ( $torantejarat_max_diam > 0 ) {
			$torantejarat_head = torantejarat_finder_meters( $torantejarat_candidate->get_attribute( 'pa_lens-diameter' ) );
			if ( null === $torantejarat_head || $torantejarat_head > $torantejarat_max_diam ) {
				continue;
			}
		}
		if ( $torantejarat_budget > 0 && (float) $torantejarat_candidate->get_price() > $torantejarat_budget ) {
			continue;
		}
		$torantejarat_results[] = $torantejarat_candidate;
	}
	usort(
		$torantejarat_results,
		static fn( $a, $b ) => (float) $a->get_price() <=> (float) $b->get_price()
	);
	foreach ( $torantejarat_steps as $torantejarat_key => $torantejarat_step ) {
		$torantejarat_value = $torantejarat_answers[ $torantejarat_key ];
		if ( '' !== $torantejarat_value && isset( $torantejarat_step['options'][ $torantejarat_value ] ) ) {
			$torantejarat_chosen[] = $torantejarat_step['options'][ $torantejarat_value ];
		}
	}
}

/**
 * Parse a measure like "۱۰ متر" or "8 میلی‌متر" to a float. Null when unknown.
 */
function torantejarat_finder_meters( $text ) {
	$latin = str_replace(
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		(string) $text
	);
	if ( preg_match( '/\d+(?:\.\d+)?/', $latin, $matches ) ) {
		return (float) $matches[0];
	}
	return null;
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
					<p class="torantejarat-finder-progress" data-finder-progress hidden></p>
					<?php foreach ( $torantejarat_steps as $torantejarat_step_key => $torantejarat_step ) : ?>
						<fieldset class="torantejarat-finder-step" data-step="<?php echo esc_attr( $torantejarat_step_key ); ?>">
							<legend><?php echo esc_html( $torantejarat_step['label'] ); ?></legend>
							<p class="torantejarat-finder-hint"><?php echo esc_html( $torantejarat_step['hint'] ); ?></p>
							<div class="torantejarat-finder-options">
								<?php foreach ( $torantejarat_step['options'] as $torantejarat_opt_key => $torantejarat_opt_label ) : ?>
									<label class="torantejarat-finder-option">
										<input type="radio" name="<?php echo esc_attr( $torantejarat_step_key ); ?>" value="<?php echo esc_attr( $torantejarat_opt_key ); ?>"<?php checked( $torantejarat_answers[ $torantejarat_step_key ], (string) $torantejarat_opt_key ); ?>>
										<span><?php echo esc_html( $torantejarat_opt_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					<div class="torantejarat-finder-foot">
						<button type="button" class="torantejarat-finder-back" data-finder-back hidden><?php esc_html_e( 'بازگشت به سؤال قبل', 'torantejarat' ); ?></button>
						<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-finder-submit"><?php esc_html_e( 'نمایش نتایج', 'torantejarat' ); ?></button>
					</div>
				</form>
				<?php if ( $torantejarat_submitted ) : ?>
					<div class="torantejarat-finder-results" data-finder-results>
						<?php if ( $torantejarat_results ) : ?>
							<span class="torantejarat-eyebrow"><?php esc_html_e( 'نتیجه بررسی اولیه', 'torantejarat' ); ?></span>
							<h2><?php echo esc_html( sprintf( __( '%s گزینه مرتبط با پاسخ‌های شما', 'torantejarat' ), number_format_i18n( count( $torantejarat_results ) ) ) ); ?></h2>
							<?php if ( $torantejarat_chosen ) : ?><p class="torantejarat-finder-summary"><?php echo esc_html( implode( ' • ', $torantejarat_chosen ) ); ?></p><?php endif; ?>
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
										<div class="torantejarat-finder-card-price"><?php echo wp_kses_post( torantejarat_card_price( $torantejarat_item ) ); ?></div>
										<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $torantejarat_item_url ); ?>"><?php esc_html_e( 'بررسی دستگاه', 'torantejarat' ); ?></a>
									</article>
								<?php endforeach; ?>
							</div>
							<p class="torantejarat-finder-note"><?php esc_html_e( 'این نتیجه تأیید تناسب فنی نیست؛ شرایط محیط و اطلاعات سازنده باید بررسی شوند.', 'torantejarat' ); ?></p>
							<p><a class="torantejarat-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'شروع دوباره', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a></p>
						<?php else : ?>
							<div class="torantejarat-empty-state">
								<?php torantejarat_icon( 'search' ); ?>
								<p><?php esc_html_e( 'هیچ دستگاهی تمام شرایط شما را ندارد. پاسخ‌ها را بازبینی کنید یا نیازتان را برای بررسی فنی آماده کنید.', 'torantejarat' ); ?></p>
								<?php
								$torantejarat_contact = get_page_by_path( 'contact' );
								if ( $torantejarat_contact ) :
									?>
									<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( get_permalink( $torantejarat_contact ) ); ?>"><?php esc_html_e( 'آماده‌سازی درخواست بررسی', 'torantejarat' ); ?></a>
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
