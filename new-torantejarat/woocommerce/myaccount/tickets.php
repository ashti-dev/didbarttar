<?php
/**
 * Tickets endpoint — list + new ticket form + threads.
 * Uses the independent torantejarat_tickets tables (not CPT torantejarat_request).
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

$torantejarat_subjects = \ToranTejarat\Theme\torantejarat_ticket_subjects();
$torantejarat_statuses = \ToranTejarat\Theme\torantejarat_ticket_statuses();

$user_id = get_current_user_id();
$tickets = \ToranTejarat\Theme\torantejarat_tickets_get_user_tickets( $user_id );

$ticket_sent  = isset( $_GET['ticket_sent'] ) && '1' === $_GET['ticket_sent'];
$ticket_error = isset( $_GET['ticket_error'] ) ? sanitize_key( wp_unslash( $_GET['ticket_error'] ) ) : '';
?>

<div class="acp__hello">
	<h2><?php esc_html_e( 'پشتیبانی', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'درخواست‌های خود را پیگیری کنید.', 'torantejarat' ); ?></p>
</div>

<?php if ( $ticket_sent ) : ?>
	<div class="torantejarat-form-notice is-success" role="status">
		<p><strong><?php esc_html_e( 'تیکت با موفقیت ثبت شد.', 'torantejarat' ); ?></strong></p>
		<p><?php esc_html_e( 'پاسخ از طریق ایمیل برایتان ارسال می‌شود.', 'torantejarat' ); ?></p>
	</div>
<?php endif; ?>

<?php if ( $ticket_error ) : ?>
	<div class="torantejarat-form-notice is-error" role="alert">
		<p><strong><?php esc_html_e( 'ثبت تیکت انجام نشد.', 'torantejarat' ); ?></strong></p>
		<p><?php echo esc_html( ucfirst( str_replace( '_', ' ', $ticket_error ) ) ); ?></p>
	</div>
<?php endif; ?>

<?php if ( $tickets ) : ?>
	<ul class="acp-activity">
		<?php foreach ( $tickets as $ticket ) : ?>
			<?php
			$messages   = \ToranTejarat\Theme\torantejarat_tickets_get_messages( $ticket['id'] );
			$ticket_st  = isset( $ticket['status'] ) ? $ticket['status'] : 'open';
			$ticket_id  = (int) $ticket['id'];
			$can_close  = in_array( $ticket_st, array( 'open', 'awaiting_support', 'awaiting_customer' ), true );
			?>
			<li>
				<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'ticket' ); ?></span>
				<div>
					<strong><?php echo esc_html( $ticket['title'] ); ?></strong>
					<br>
					<small>
						<?php echo esc_html( mysql2date( 'Y/m/d', $ticket['created_at'] ) ); ?>
						<?php if ( ! empty( $ticket['order_id'] ) ) : ?>
							· <?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $ticket['order_id'] ) ); ?>
						<?php endif; ?>
					</small>
					<?php if ( $messages ) : ?>
						<div id="ticket-<?php echo esc_attr( $ticket_id ); ?>" hidden>
							<?php foreach ( $messages as $msg ) : ?>
								<p>
									<strong><?php echo (int) $msg['is_staff'] === 1 ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?>:</strong>
									<?php echo esc_html( $msg['body'] ); ?>
								</p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<span class="acp-badge<?php echo 'open' === $ticket_st ? ' warn' : ''; ?>">
					<?php echo esc_html( $torantejarat_statuses[ $ticket_st ] ?? $ticket_st ); ?>
				</span>
				<?php if ( $messages ) : ?>
					<button type="button" class="acp-btn acp-btn-ghost" data-toggle-ticket="ticket-<?php echo esc_attr( $ticket_id ); ?>" aria-expanded="false"><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></button>
				<?php endif; ?>
				<?php if ( $can_close ) : ?>
					<form method="post" action="<?php echo esc_url( wc_get_account_endpoint_url( 'tickets' ) ); ?>" style="display:inline;" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را ببندید؟', 'torantejarat' ); ?>' );">
						<?php wp_nonce_field( 'torantejarat-ticket-close', 'torantejarat_ticket_close_nonce' ); ?>
						<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket_id ); ?>">
						<input type="hidden" name="action" value="close">
						<button type="submit" class="acp-btn acp-btn-ghost"><?php esc_html_e( 'بستن تیکت', 'torantejarat' ); ?></button>
					</form>
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
		<input type="hidden" name="action" value="torantejarat_ticket_create_frontend">
		<?php wp_nonce_field( 'torantejarat-ticket-create', 'torantejarat_ticket_create_nonce' ); ?>

		<!-- Honeypot for spam protection -->
		<p class="torantejarat-hp-field" aria-hidden="true"><label><?php esc_html_e( 'این فیلد را خالی بگذارید', 'torantejarat' ); ?> <input type="text" name="company" value="" tabindex="-1" autocomplete="off"></label></p>

		<div class="acp-field">
			<label for="acp-tk-subject"><?php esc_html_e( 'موضوع', 'torantejarat' ); ?></label>
			<select id="acp-tk-subject" name="subject_key">
				<?php foreach ( $torantejarat_subjects as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="acp-field">
			<label for="acp-tk-title"><?php esc_html_e( 'عنوان تیکت', 'torantejarat' ); ?></label>
			<input id="acp-tk-title" name="title" type="text" required maxlength="150" placeholder="<?php esc_attr_e( 'مثال: مشکل در فاکتور سفارش شماره ۱۲۳', 'torantejarat' ); ?>">
		</div>

		<div class="acp-field">
			<label for="acp-tk-order"><?php esc_html_e( 'سفارش مرتبط (اختیاری)', 'torantejarat' ); ?></label>
			<select id="acp-tk-order" name="order_id">
				<option value="0"><?php esc_html_e( 'بدون سفارش', 'torantejarat' ); ?></option>
				<?php
				$orders = wc_get_orders(
					array(
						'customer_id' => $user_id,
						'limit'       => 10,
						'orderby'     => 'date',
						'order'       => 'DESC',
						'return'      => 'ids',
					)
				);
				foreach ( $orders as $oid ) : ?>
					<option value="<?php echo esc_attr( $oid ); ?>"><?php printf( esc_html__( '#%s', 'torantejarat' ), esc_html( $oid ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="acp-field acp-full">
			<label for="acp-tk-msg"><?php esc_html_e( 'متن پیام', 'torantejarat' ); ?></label>
			<textarea id="acp-tk-msg" name="body" rows="5" required maxlength="2000"><?php echo esc_textarea( isset( $_POST['body'] ) ? wp_unslash( $_POST['body'] ) : '' ); ?></textarea>
			<p class="acp-err" role="alert" hidden></p>
		</div>

		<div class="acp-full">
			<button class="acp-btn acp-btn-primary" type="submit"><?php esc_html_e( 'ارسال تیکت', 'torantejarat' ); ?></button>
		</div>
	</form>
</div>

<?php if ( ! empty( $_GET['view_ticket'] ) && is_numeric( $_GET['view_ticket'] ) ) : ?>
	<?php
	$view_id = absint( $_GET['view_ticket'] );
	$view_ticket = \ToranTejarat\Theme\torantejarat_tickets_get( $view_id );
	if ( $view_ticket && (int) $view_ticket['user_id'] === $user_id ) :
		$view_messages = \ToranTejarat\Theme\torantejarat_tickets_get_messages( $view_id );
		$view_st       = isset( $view_ticket['status'] ) ? $view_ticket['status'] : 'open';
		$can_reply     = in_array( $view_st, array( 'open', 'awaiting_support', 'awaiting_customer' ), true );
		$can_close     = in_array( $view_st, array( 'open', 'awaiting_support', 'awaiting_customer' ), true );
		?>
		<div class="acp-card" style="margin-top:18px">
			<div class="acp-card__head"><h3><?php esc_html_e( 'جزییات تیکت #%d', 'torantejarat' ); ?></h3></div>
			<p><strong><?php esc_html_e( 'عنوان', 'torantejarat' ); ?>:</strong> <?php echo esc_html( $view_ticket['title'] ); ?></p>
			<p><strong><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?>:</strong> <?php echo esc_html( $torantejarat_statuses[ $view_st ] ?? $view_st ); ?></p>
			<p><strong><?php esc_html_e( 'تاریخ', 'torantejarat' ); ?>:</strong> <?php echo esc_html( mysql2date( 'Y/m/d H:i', $view_ticket['created_at'] ) ); ?></p>

			<h4><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></h4>
			<?php if ( $view_messages ) : ?>
				<ul class="acp-activity">
					<?php foreach ( $view_messages as $msg ) : ?>
						<li>
							<strong><?php echo (int) $msg['is_staff'] === 1 ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?>:</strong>
							<span class="ticket-time"><?php echo esc_html( mysql2date( 'Y/m/d H:i', $msg['created_at'] ) ); ?></span>
							<p><?php echo esc_html( $msg['body'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'هیچ پیامی ثبت نشده است.', 'torantejarat' ); ?></p>
			<?php endif; ?>

			<?php if ( $can_reply ) : ?>
				<h4><?php esc_html_e( 'پاسخ جدید', 'torantejarat' ); ?></h4>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="acp-form-grid" novalidate>
					<input type="hidden" name="action" value="torantejarat_ticket_reply_frontend">
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_id ); ?>">
					<?php wp_nonce_field( 'torantejarat-ticket-reply', 'torantejarat_ticket_reply_nonce' ); ?>
					<div class="acp-field acp-full">
						<label for="acp-tk-reply"><?php esc_html_e( 'متن پاسخ', 'torantejarat' ); ?></label>
						<textarea id="acp-tk-reply" name="body" rows="4" required maxlength="2000"></textarea>
					</div>
					<div class="acp-full">
						<button class="acp-btn acp-btn-primary" type="submit"><?php esc_html_e( 'ارسال پاسخ', 'torantejarat' ); ?></button>
					</div>
				</form>
			<?php endif; ?>

			<?php if ( $can_close ) : ?>
				<form method="post" action="<?php echo esc_url( wc_get_account_endpoint_url( 'tickets' ) ); ?>" style="margin-top:12px;" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را ببندید؟', 'torantejarat' ); ?>' );">
					<?php wp_nonce_field( 'torantejarat-ticket-close', 'torantejarat_ticket_close_nonce' ); ?>
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_id ); ?>">
					<input type="hidden" name="action" value="close">
					<button type="submit" class="acp-btn acp-btn-ghost"><?php esc_html_e( 'بستن تیکت', 'torantejarat' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'تیکت یافت نشد یا به شما تعلق ندارد.', 'torantejarat' ); ?></p>
	<?php endif; ?>
<?php endif; ?>