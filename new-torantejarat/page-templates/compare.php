<?php
/**
 * Compare layout — pick up to 3 catalog products, full spec table.
 * Selection via ?p1=&p2=&p3=, ?compare=1,2,3 or ?add= (prefill). Native data only.
 * Results refresh via AJAX (assets/js/compare.js); plain GET submit is the fallback.
 *
 * @package ToranTejarat
 */

/* Template Name: مقایسه — جدول مشخصات */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_compare = torantejarat_compare_data( torantejarat_compare_raw_input( $_GET ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only filter.
$torantejarat_catalog   = $torantejarat_compare['catalog'];
$torantejarat_slots     = $torantejarat_compare['slots'];
$torantejarat_only_diff = $torantejarat_compare['only_diff'];

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '' ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container">
			<form class="torantejarat-compare-pickers" method="get" action="<?php echo esc_url( get_permalink() ); ?>" data-compare-form>
				<?php for ( $torantejarat_slot = 1; $torantejarat_slot <= 3; $torantejarat_slot++ ) : ?>
					<?php $torantejarat_current = $torantejarat_slots[ $torantejarat_slot - 1 ] ?? 0; ?>
					<label>
						<span><?php echo esc_html( sprintf( __( 'دستگاه %s', 'torantejarat' ), torantejarat_fa_number( $torantejarat_slot ) ) ); ?></span>
						<select name="<?php echo esc_attr( 'p' . $torantejarat_slot ); ?>">
							<option value="0"><?php esc_html_e( 'انتخاب کنید', 'torantejarat' ); ?></option>
							<?php foreach ( $torantejarat_catalog as $torantejarat_option ) : ?>
								<option value="<?php echo esc_attr( $torantejarat_option->get_id() ); ?>"<?php selected( $torantejarat_current, $torantejarat_option->get_id() ); ?>><?php echo esc_html( $torantejarat_option->get_name() ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				<?php endfor; ?>
				<label class="torantejarat-compare-diff">
					<input type="checkbox" name="diff" value="1"<?php checked( $torantejarat_only_diff ); ?>>
					<span><?php esc_html_e( 'فقط تفاوت‌ها', 'torantejarat' ); ?></span>
				</label>
				<button type="submit" class="torantejarat-btn torantejarat-btn-primary"><?php esc_html_e( 'مقایسه کن', 'torantejarat' ); ?></button>
			</form>
			<div class="torantejarat-compare-results" id="torantejarat-compare-results" data-compare-results aria-live="polite">
				<?php
				get_template_part(
					'template-parts/compare-results',
					null,
					array(
						'items'  => $torantejarat_compare['items'],
						'maps'   => $torantejarat_compare['maps'],
						'labels' => $torantejarat_compare['labels'],
					)
				);
				?>
			</div>
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
