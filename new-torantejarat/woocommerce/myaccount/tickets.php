<?php
/**
 * Tickets endpoint — list + new ticket form + threads.
 *
 * Rendered via woocommerce_account_tickets_endpoint.
 * Expects: $tickets (WP_Post[]), $orders (int[]).
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

$torantejarat_subjects = \ToranTejarat\Theme\torantejarat_ticket_subjects();
$torantejarat_statuses = \ToranTejarat\Theme\torantejarat_ticket_statuses();
?>
<div class="acp__hello">
	<h2><?php esc_html_e( 'پشتیبانی', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'درخواست‌های خود را پیگیری کنید.', 'torantejarat' ); ?></p>
</div>

<?php if ( $tickets ) : ?>
	<ul class="acp-activity">
		<?php foreach ( $tickets as $torantejarat_ticket_post ) : ?>
			<?php
			$torantejarat_t  = (array) get_post_meta( $torantejarat_ticket_post->ID, '_torantejarat_ticket', true );
			$torantejarat_st = isset( $torantejarat_t['status'] ) ? $torantejarat_t['status'] : 'open';
			$torantejarat_replies = (array) get_post_meta( $torantejarat_ticket_post->ID, '_torantejarat_ticket_replies', true );
			$torantejarat_tid = 'ticket-' . $torantejarat_ticket_post->ID;
			?>
			<li>
				<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'ticket' ); ?></span>
				<div>
					<strong><?php echo esc_html( $torantejarat_subjects[ $torantejarat_t['subject'] ] ?? __( 'تیکت', 'torantejarat' ) ); ?></strong>
					<br>
					<small>
						<?php echo esc_html( mysql2date( 'Y/m/d', $torantejarat_ticket_post->post_date ) ); ?>
						<?php if ( ! empty( $torantejarat_t['order_id'] ) ) : ?>
							· <?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $torantejarat_t['order_id'] ) ); ?>
						<?php endif; ?>
					</small>
					<?php if ( $torantejarat_replies ) : ?>
						<div id="<?php echo esc_attr( $torantejarat_tid ); ?>" hidden>
							<p><?php echo esc_html( $torantejarat_t['message'] ?? '' ); ?></p>
							<?php foreach ( $torantejarat_replies as $torantejarat_reply ) : ?>
								<p>
									<strong><?php echo ( isset( $torantejarat_reply['author'] ) && 'admin' === $torantejarat_reply['author'] ) ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?>:</strong>
									<?php echo esc_html( $torantejarat_reply['text'] ?? '' ); ?>
								</p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<span class="acp-badge<?php echo 'open' === $torantejarat_st ? ' warn' : ''; ?>"><?php echo esc_html( $torantejarat_statuses[ $torantejarat_st ] ?? $torantejarat_st ); ?></span>
				<?php if ( $torantejarat_replies ) : ?>
					<button type="button" class="acp-btn acp-btn-ghost" data-toggle-ticket="<?php echo esc_attr( $torantejarat_tid ); ?>" aria-expanded="false"><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></button>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<div class="acp-empty">
		<h3><?php esc_html_e( 'تیکتی ثبت نشده است.', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'سؤال یا مشکلی دارید؟ از فرم زیر بنویسید.', 'torantejarat' ); ?></p>
	</div>
<?php endif; ?>

<div class="acp-card" style="margin-top:18px">
	<div class="acp-card__head"><h3><?php esc_html_e( 'ثبت تیکت جدید', 'torantejarat' ); ?></h3></div>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="acp-form-grid" id="acp-ticket-form" novalidate>
		<input type="hidden" name="action" value="torantejarat_ticket">
		<?php wp_nonce_field( 'torantejarat-ticket', 'torantejarat_ticket_nonce' ); ?>
		<div class="acp-field">
			<label for="acp-tk-subject"><?php esc_html_e( 'موضوع', 'torantejarat' ); ?></label>
			<select id="acp-tk-subject" name="subject">
				<?php foreach ( $torantejarat_subjects as $torantejarat_key => $torantejarat_label ) : ?>
					<option value="<?php echo esc_attr( $torantejarat_key ); ?>"><?php echo esc_html( $torantejarat_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="acp-field">
			<label for="acp-tk-order"><?php esc_html_e( 'سفارش مرتبط (اختیاری)', 'torantejarat' ); ?></label>
			<select id="acp-tk-order" name="order_id">
				<option value="0"><?php esc_html_e( 'بدون سفارش', 'torantejarat' ); ?></option>
				<?php foreach ( $orders as $torantejarat_oid ) : ?>
					<option value="<?php echo esc_attr( $torantejarat_oid ); ?>"><?php printf( esc_html__( '#%s', 'torantejarat' ), esc_html( $torantejarat_oid ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="acp-field acp-full">
			<label for="acp-tk-msg"><?php esc_html_e( 'متن پیام', 'torantejarat' ); ?></label>
			<textarea id="acp-tk-msg" name="message" rows="4" required maxlength="1000"></textarea>
			<p class="acp-err" role="alert" hidden></p>
		</div>
		<div class="acp-full">
			<button class="acp-btn acp-btn-primary" type="submit"><?php esc_html_e( 'ارسال تیکت', 'torantejarat' ); ?></button>
		</div>
	</form>
</div>
