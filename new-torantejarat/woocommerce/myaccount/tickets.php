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

$ticket_sent   = isset( $_GET['ticket_sent'] ) && '1' === $_GET['ticket_sent'];
$ticket_deleted = isset( $_GET['ticket_deleted'] ) && '1' === $_GET['ticket_deleted'];
$ticket_error  = isset( $_GET['ticket_error'] ) ? sanitize_key( wp_unslash( $_GET['ticket_error'] ) ) : '';

$torantejarat_ticket_error_messages = array(
	'nonce'           => __( 'درخواست نامعتبر است.', 'torantejarat' ),
	'not_found'       => __( 'تیکت یافت نشد.', 'torantejarat' ),
	'empty_title'     => __( 'عنوان تیکت الزامی است.', 'torantejarat' ),
	'title_too_long'  => __( 'عنوان حداکثر ۱۵۰ کاراکتر می‌تواند باشد.', 'torantejarat' ),
	'body_too_short'  => __( 'پیام باید حداقل ۱۰ کاراکتر باشد.', 'torantejarat' ),
	'invalid_order'   => __( 'سفارش معتبر نیست یا به شما تعلق ندارد.', 'torantejarat' ),
	'rate_limit_open' => __( 'شما دارای ۳ تیکت باز هستید. تا بستن یکی از آن‌ها صبر کنید.', 'torantejarat' ),
	'rate_limit_seconds'=> __( 'لطفاً حداقل ۶۰ ثانیه قبل از ارسال تیکت دیگر صبر کنید.', 'torantejarat' ),
	'forbidden'       => __( 'شما دسترسی به این تیکت را ندارید.', 'torantejarat' ),
	'db_error'        => __( 'خطا در ذخیره تیکت.', 'torantejarat' ),
);
$torantejarat_ticket_error_msg = $torantejarat_ticket_error_messages[ $ticket_error ] ?? esc_html( ucfirst( str_replace( '_', ' ', $ticket_error ) ) );
?>
<style>
.torantejarat-tickets-wrap { direction: rtl; text-align: right; }
.torantejarat-tickets-wrap h2,
.torantejarat-tickets-wrap h3,
.torantejarat-tickets-wrap h4 { text-align: right; }

.torantejarat-tickets-list {
	list-style: none;
	margin: 0;
	padding: 0;
}
.torantejarat-tickets-list li {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 14px 18px;
	border: 1px solid #e2e5df;
	border-radius: 12px;
	margin-bottom: 10px;
	background: #fff;
}
.torantejarat-tickets-list li .ticket-info { flex: 1; }
.torantejarat-tickets-list li .ticket-title {
	display: block;
	font-weight: 800;
	color: #172325;
	margin-bottom: 4px;
	font-size: 15px;
}
.torantejarat-tickets-list li .ticket-meta {
	display: block;
	font-size: 12px;
	color: #606c68;
}
.torantejarat-tickets-list li .ticket-actions {
	display: flex;
	gap: 8px;
	align-items: center;
}
.torantejarat-tickets-list li .ticket-badge {
	font-size: 11px;
	font-weight: 800;
	padding: 4px 10px;
	border-radius: 20px;
	margin-left: 8px;
}
.torantejarat-tickets-list li .ticket-badge.open { background: #fef3f2; color: #b42318; }
.torantejarat-tickets-list li .ticket-badge.closed { background: #eef4fa; color: #36567a; }

.torantejarat-tickets-form {
	margin-top: 24px;
	border: 1px solid #e2e5df;
	border-radius: 16px;
	padding: 24px;
	background: #fff;
}
.torantejarat-tickets-form h3 {
	margin-top: 0;
	margin-bottom: 18px;
	font-size: 18px;
	color: #172325;
	text-align: right;
}
.torantejarat-tickets-form .acp-field { margin-bottom: 14px; text-align: right; }
.torantejarat-tickets-form label {
	display: block;
	font-size: 13px;
	font-weight: 800;
	color: #172325;
	margin-bottom: 6px;
	text-align: right;
}
.torantejarat-tickets-form select,
.torantejarat-tickets-form input[type="text"],
.torantejarat-tickets-form textarea {
	width: 100%;
	padding: 10px 14px;
	padding-inline-end: 36px;
	border: 1px solid #dce1db;
	border-radius: 10px;
	font-size: 14px;
	font-family: inherit;
	box-sizing: border-box;
	direction: rtl;
	text-align: right;
}
.torantejarat-tickets-form textarea {
	min-height: 100px;
	resize: vertical;
	text-align: right;
}
.torantejarat-tickets-form .acp-full { grid-column: 1 / -1; }
.torantejarat-tickets-form button {
	background: #f77b3e;
	color: #fff;
	border: none;
	border-radius: 8px;
	padding: 10px 24px;
	font-size: 14px;
	font-weight: 700;
	cursor: pointer;
}
.torantejarat-tickets-form button:hover { background: #e96a2b; }

.torantejarat-tickets-notice {
	padding: 12px 16px;
	border-radius: 10px;
	margin-bottom: 16px;
	font-size: 14px;
	text-align: right;
}
.torantejarat-tickets-notice.is-success { background: #eff9f2; color: #247348; border: 1px solid #d1e9dd; }
.torantejarat-tickets-notice.is-error { background: #fef3f2; color: #b42318; border: 1px solid #f5c7c3; }
.torantejarat-tickets-notice strong { display: block; margin-bottom: 4px; }

.torantejarat-tickets-empty {
	text-align: center;
	padding: 40px 20px;
	color: #606c68;
}
.torantejarat-tickets-empty h3 { color: #172325; margin-bottom: 8px; }

.ticket-thread-toggle {
	margin-top: 8px;
	cursor: pointer;
	text-align: right;
}
.ticket-thread-toggle button {
	background: none;
	border: 1px solid #dce1db;
	border-radius: 8px;
	padding: 6px 12px;
	font-size: 12px;
	cursor: pointer;
	color: #606c68;
}
.ticket-thread-toggle button:hover { border-color: #f77b3e; color: #f77b3e; }

.ticket-messages {
	margin-top: 10px;
	padding: 12px;
	background: #f4f3ef;
	border-radius: 10px;
	border: 1px solid #e2e5df;
}
.ticket-messages p { margin: 0 0 8px; font-size: 13px; color: #172325; }
.ticket-messages p strong { color: #f77b3e; }

.ticket-close-form {
	margin-top: 12px;
	display: inline-block;
	text-align: right;
}
.ticket-close-form button {
	background: #fef3f2;
	color: #b42318;
	border: 1px solid #f5c7c3;
	border-radius: 8px;
	padding: 8px 16px;
	font-size: 13px;
	font-weight: 700;
	cursor: pointer;
}
.ticket-close-form button:hover { background: #fdecea; }

.ticket-view-detail {
	margin-top: 24px;
	border: 1px solid #e2e5df;
	border-radius: 16px;
	padding: 24px;
	background: #fff;
	text-align: right;
}
.ticket-view-detail h3 {
	margin-top: 0;
	margin-bottom: 16px;
	font-size: 18px;
	color: #172325;
}
.ticket-view-detail table {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 20px;
}
.ticket-view-detail th,
.ticket-view-detail td {
	padding: 10px 12px;
	text-align: right;
	border-bottom: 1px solid #f2f3ef;
}
.ticket-view-detail th {
	font-weight: 800;
	color: #172325;
	width: 30%;
}
.ticket-view-detail td { color: #606c68; }

.ticket-history { margin-bottom: 20px; }
.ticket-history h4 {
	margin-top: 0;
	margin-bottom: 10px;
	font-size: 15px;
	color: #172325;
}
.ticket-message {
	margin-bottom: 12px;
	padding: 10px 14px;
	border-radius: 10px;
}
.ticket-message.customer { background: #eef4fa; border-right: 4px solid #36567a; }
.ticket-message.staff { background: #eff9f2; border-right: 4px solid #247348; }
.ticket-message strong {
	display: block;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	margin-bottom: 4px;
}
.ticket-message .ticket-time {
	font-size: 11px;
	color: #606c68;
	display: block;
	margin-top: 2px;
}

.ticket-reply-form {
	margin-top: 20px;
	border-top: 1px solid #f2f3ef;
	padding-top: 20px;
	text-align: right;
}
.ticket-reply-form h4 {
	margin-top: 0;
	margin-bottom: 12px;
	font-size: 15px;
	color: #172325;
}

.acp-btn { display: inline-block; }
</style>
<?php if ( $ticket_sent ) : ?>
	<div class="torantejarat-tickets-notice is-success" role="status">
		<strong><?php esc_html_e( 'تیکت با موفقیت ثبت شد.', 'torantejarat' ); ?></strong>
		<?php esc_html_e( 'پشتیبانی تیکت را بررسی می‌کند و پاسخ در حساب کاربری شما درج می‌شود.', 'torantejarat' ); ?>
	</div>
<?php endif; ?>

<?php if ( $ticket_deleted ) : ?>
	<div class="torantejarat-tickets-notice is-success" role="status">
		<strong><?php esc_html_e( 'تیکت با موفقیت حذف شد.', 'torantejarat' ); ?></strong>
	</div>
<?php endif; ?>

<?php if ( $ticket_error ) : ?>
	<div class="torantejarat-tickets-notice is-error" role="alert">
		<strong><?php esc_html_e( 'ثبت تیکت انجام نشد.', 'torantejarat' ); ?></strong>
		<?php echo $torantejarat_ticket_error_msg; ?>
	</div>
<?php endif; ?>

<div class="acp__hello">
	<h2><?php esc_html_e( 'پشتیبانی', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'درخواست‌های خود را پیگیری کنید.', 'torantejarat' ); ?></p>
</div>

<?php if ( $tickets ) : ?>
	<ul class="torantejarat-tickets-list">
		<?php foreach ( $tickets as $ticket ) : ?>
			<?php
			$messages   = \ToranTejarat\Theme\torantejarat_tickets_get_messages( $ticket['id'] );
			$ticket_st  = isset( $ticket['status'] ) ? $ticket['status'] : 'open';
			$ticket_id  = (int) $ticket['id'];
			$can_close  = in_array( $ticket_st, array( 'open', 'awaiting_support', 'awaiting_customer' ), true );
			$status_badge_class = 'open' === $ticket_st ? 'open' : 'closed';
			?>
			<li>
				<div class="ticket-info">
					<span class="ticket-title"><a href="<?php echo esc_url( add_query_arg( 'view_ticket', $ticket_id, wc_get_account_endpoint_url( 'tickets' ) ) ); ?>"><?php echo esc_html( $ticket['title'] ); ?></a></span>
					<span class="ticket-meta">
						<?php echo esc_html( mysql2date( 'Y/m/d', $ticket['created_at'] ) ); ?>
						<?php if ( ! empty( $ticket['order_id'] ) ) : ?>
							&nbsp;·&nbsp;<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $ticket['order_id'] ) ); ?>
						<?php endif; ?>
					</span>
				</div>
				<div class="ticket-actions">
					<span class="ticket-badge <?php echo esc_attr( $status_badge_class ); ?>">
						<?php echo esc_html( $torantejarat_statuses[ $ticket_st ] ?? $ticket_st ); ?>
					</span>
					<?php if ( $messages ) : ?>
						<div class="ticket-thread-toggle">
							<button type="button" data-toggle-ticket="ticket-<?php echo esc_attr( $ticket_id ); ?>" aria-expanded="false">
								<?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?>
							</button>
						</div>
						<div id="ticket-<?php echo esc_attr( $ticket_id ); ?>" class="ticket-messages" style="display:none;">
							<?php foreach ( $messages as $msg ) : ?>
								<p>
									<strong><?php echo (int) $msg['is_staff'] === 1 ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?>:</strong>
									<?php echo esc_html( $msg['body'] ); ?>
								</p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
<?php if ( $can_close ) : ?>
					<div class="ticket-close-form">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را ببندید؟', 'torantejarat' ); ?>' );">
							<?php wp_nonce_field( 'torantejarat-ticket-close', 'torantejarat_ticket_close_nonce' ); ?>
							<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket_id ); ?>">
							<input type="hidden" name="action" value="torantejarat_ticket_close_frontend">
							<button type="submit"><?php esc_html_e( 'بستن تیکت', 'torantejarat' ); ?></button>
						</form>
					</div>
					<?php endif; ?>
					<?php if ( $ticket_st === 'open' || $ticket_st === 'awaiting_customer' || $ticket_st === 'awaiting_support' ) : ?>
					<div class="ticket-delete-form" style="margin-right:8px;">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را حذف کنید؟ این عمل غیرقابل‌بازگشت است.', 'torantejarat' ); ?>' );">
							<?php wp_nonce_field( 'torantejarat-ticket-delete', 'torantejarat_ticket_delete_nonce' ); ?>
							<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket_id ); ?>">
							<input type="hidden" name="action" value="torantejarat_ticket_delete_frontend">
							<button type="submit" style="background:#fef3f2;color:#b42318;border:1px solid #f5c7c3;"><?php esc_html_e( 'حذف تیکت', 'torantejarat' ); ?></button>
						</form>
					</div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<div class="torantejarat-tickets-empty">
		<h3><?php esc_html_e( 'تیکتی ثبت نشده است.', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'سؤال یا مشکلی دارید؟ از فرم زیر بنویسید.', 'torantejarat' ); ?></p>
	</div>
<?php endif; ?>

<div class="torantejarat-tickets-form">
	<h3><?php esc_html_e( 'ثبت تیکت جدید', 'torantejarat' ); ?></h3>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="acp-form-grid" id="acp-ticket-form" novalidate>
		<input type="hidden" name="action" value="torantejarat_ticket_create_frontend">
		<?php wp_nonce_field( 'torantejarat-ticket-create', 'torantejarat_ticket_create_nonce' ); ?>

		<!-- Honeypot for spam protection -->
		<p class="torantejarat-hp-field" aria-hidden="true" style="display:none;"><label><?php esc_html_e( 'این فیلد را خالی بگذارید', 'torantejarat' ); ?> <input type="text" name="company" value="" tabindex="-1" autocomplete="off"></label></p>

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
				$default_order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
				$orders = wc_get_orders(
					array(
						'customer_id' => $user_id,
						'limit'       => 10,
						'orderby'     => 'date',
						'order'       => 'DESC',
						'return'      => 'ids',
					)
				);
				foreach ( $orders as $oid ) :
					$selected = selected( $default_order_id, $oid, false );
				?>
				<option value="<?php echo esc_attr( $oid ); ?>"<?php echo $selected; ?>><?php printf( esc_html__( '#%s', 'torantejarat' ), esc_html( $oid ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="acp-field acp-full">
			<label for="acp-tk-msg"><?php esc_html_e( 'متن پیام', 'torantejarat' ); ?></label>
			<textarea id="acp-tk-msg" name="body" rows="5" required maxlength="2000"><?php echo esc_textarea( isset( $_POST['body'] ) ? wp_unslash( $_POST['body'] ) : '' ); ?></textarea>
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
		$status_badge_class = 'open' === $view_st ? 'open' : 'closed';
		?>
		<div class="ticket-view-detail">
			<h3><?php printf( esc_html__( 'تیکت #%d', 'torantejarat' ), $view_id ); ?></h3>
			<table>
				<tr>
					<th><?php esc_html_e( 'عنوان', 'torantejarat' ); ?></th>
					<td><?php echo esc_html( $view_ticket['title'] ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></th>
					<td><?php echo esc_html( $torantejarat_statuses[ $view_st ] ?? $view_st ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'تاریخ', 'torantejarat' ); ?></th>
					<td><?php echo esc_html( mysql2date( 'Y/m/d H:i', $view_ticket['created_at'] ) ); ?></td>
				</tr>
				<?php if ( ! empty( $view_ticket['order_id'] ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'سفارش', 'torantejarat' ); ?></th>
					<td>#<?php echo esc_html( $view_ticket['order_id'] ); ?></td>
				</tr>
				<?php endif; ?>
			</table>

			<div class="ticket-history">
				<h4><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></h4>
				<?php if ( $view_messages ) : ?>
					<?php foreach ( $view_messages as $msg ) : ?>
					<div class="ticket-message <?php echo (int) $msg['is_staff'] === 1 ? 'staff' : 'customer'; ?>">
						<strong><?php echo (int) $msg['is_staff'] === 1 ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?></strong>
						<span class="ticket-time"><?php echo esc_html( mysql2date( 'Y/m/d H:i', $msg['created_at'] ) ); ?></span>
						<?php echo esc_html( $msg['body'] ); ?>
					</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'هیچ پیامی ثبت نشده است.', 'torantejarat' ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $can_reply ) : ?>
			<div class="ticket-reply-form">
				<h4><?php esc_html_e( 'پاسخ جدید', 'torantejarat' ); ?></h4>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="torantejarat_ticket_reply_frontend">
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_id ); ?>">
					<?php wp_nonce_field( 'torantejarat-ticket-reply', 'torantejarat_ticket_reply_nonce' ); ?>
					<div class="acp-field acp-full">
						<label for="acp-tk-reply"><?php esc_html_e( 'متن پاسخ', 'torantejarat' ); ?></label>
						<textarea id="acp-tk-reply" name="body" rows="4" required maxlength="2000"></textarea>
					</div>
					<button class="acp-btn acp-btn-primary" type="submit"><?php esc_html_e( 'ارسال پاسخ', 'torantejarat' ); ?></button>
				</form>
			</div>
			<?php endif; ?>

			<?php if ( $can_close ) : ?>
			<div class="ticket-close-form" style="margin-top:16px;">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را ببندید؟', 'torantejarat' ); ?>' );">
					<?php wp_nonce_field( 'torantejarat-ticket-close', 'torantejarat_ticket_close_nonce' ); ?>
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_id ); ?>">
					<input type="hidden" name="action" value="torantejarat_ticket_close_frontend">
					<button type="submit"><?php esc_html_e( 'بستن تیکت', 'torantejarat' ); ?></button>
				</form>
			</div>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'تیکت یافت نشد یا به شما تعلق ندارد.', 'torantejarat' ); ?></p>

	<?php endif; ?>
<?php endif; ?>
