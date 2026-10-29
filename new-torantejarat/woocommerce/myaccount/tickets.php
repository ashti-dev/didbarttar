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
	'not_closed'      => __( 'فقط تیکت‌های بسته قابل حذف هستند.', 'torantejarat' ),
	'invalid_transition'=> __( 'تغییر وضعیت مجاز نیست.', 'torantejarat' ),
);
$torantejarat_ticket_error_msg = $torantejarat_ticket_error_messages[ $ticket_error ] ?? esc_html( ucfirst( str_replace( '_', ' ', $ticket_error ) ) );
?>
<style>
:root {
	--tt-color-primary: #f77b3e;
	--tt-color-primary-dark: #e96a2b;
	--tt-color-bg: #fafaf7;
	--tt-color-card: #ffffff;
	--tt-color-text: #172325;
	--tt-color-text-muted: #606c68;
	--tt-color-border: #e2e5df;
	--tt-color-success: #247348;
	--tt-color-success-bg: #eff9f2;
	--tt-color-error: #b42318;
	--tt-color-error-bg: #fef3f2;
	--tt-radius: 12px;
	--tt-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	--tt-shadow-hover: 0 4px 16px rgba(0, 0, 0, 0.08);
	--tt-font-family: Vazirmatn, Tahoma, Arial, sans-serif;
}

.torantejarat-tickets-wrap {
	direction: rtl;
	text-align: right;
	font-family: var(--tt-font-family);
	background: var(--tt-color-bg);
	min-height: 100vh;
	padding: 24px 16px;
	box-sizing: border-box;
}

.torantejarat-tickets-wrap h2,
.torantejarat-tickets-wrap h3,
.torantejarat-tickets-wrap h4 {
	text-align: right;
	color: var(--tt-color-text);
	margin-top: 0;
}

.acp__hello {
	margin-bottom: 24px;
	text-align: right;
}

.acp__hello h2 {
	font-size: 24px;
	font-weight: 800;
	margin-bottom: 6px;
}

.acp__hello p {
	font-size: 15px;
	color: var(--tt-color-text-muted);
	margin: 0;
}

/* Notices */
.torantejarat-tickets-notice {
	padding: 14px 18px;
	border-radius: 10px;
	margin-bottom: 16px;
	font-size: 14px;
	text-align: right;
	box-shadow: var(--tt-shadow);
	animation: ttSlideIn 0.3s ease;
}

@keyframes ttSlideIn {
	from { opacity: 0; transform: translateY(-8px); }
	to { opacity: 1; transform: translateY(0); }
}

.torantejarat-tickets-notice.is-success {
	background: var(--tt-color-success-bg);
	color: var(--tt-color-success);
	border: 1px solid #d1e9dd;
}

.torantejarat-tickets-notice.is-error {
	background: var(--tt-color-error-bg);
	color: var(--tt-color-error);
	border: 1px solid #f5c7c3;
}

.torantejarat-tickets-notice strong {
	display: block;
	margin-bottom: 4px;
	font-size: 15px;
}

/* Empty state */
.torantejarat-tickets-empty {
	text-align: center;
	padding: 60px 20px;
	background: var(--tt-color-card);
	border-radius: 16px;
	border: 2px dashed var(--tt-color-border);
	margin-bottom: 24px;
}

.torantejarat-tickets-empty h3 {
	color: var(--tt-color-text);
	font-size: 18px;
	margin-bottom: 8px;
}

.torantejarat-tickets-empty p {
	color: var(--tt-color-text-muted);
	font-size: 14px;
	margin-bottom: 20px;
}

/* Ticket list */
.torantejarat-tickets-list {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.torantejarat-tickets-list li {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: space-between;
	padding: 18px 20px;
	border: 1px solid var(--tt-color-border);
	border-radius: var(--tt-radius);
	margin-bottom: 0;
	background: var(--tt-color-card);
	box-shadow: var(--tt-shadow);
	transition: box-shadow 0.2s ease, border-color 0.2s ease;
	cursor: pointer;
	position: relative;
}

.torantejarat-tickets-list li:hover {
	box-shadow: var(--tt-shadow-hover);
	border-color: #d4d8d2;
}

.torantejarat-tickets-list li .ticket-info {
	width: 100%;
	margin-bottom: 10px;
}

.torantejarat-tickets-list li .ticket-title {
	display: block;
	font-weight: 800;
	color: var(--tt-color-text);
	margin-bottom: 4px;
	font-size: 15px;
	text-decoration: none;
}

.torantejarat-tickets-list li .ticket-title a {
	color: inherit;
	text-decoration: none;
}

.torantejarat-tickets-list li .ticket-meta {
	display: block;
	font-size: 12px;
	color: var(--tt-color-text-muted);
}

.torantejarat-tickets-list li .ticket-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	align-items: center;
	width: 100%;
	justify-content: flex-end;
	margin-top: 8px;
}

.torantejarat-tickets-list li .ticket-badge {
	font-size: 11px;
	font-weight: 800;
	padding: 5px 11px;
	border-radius: 20px;
	margin-left: 0;
	margin-right: 8px;
}

.torantejarat-tickets-list li .ticket-badge.open {
	background: #fef3f2;
	color: #b42318;
}

.torantejarat-tickets-list li .ticket-badge.closed {
	background: #eef4fa;
	color: #36567a;
}

.torantejarat-tickets-list li .ticket-badge.awaiting_support {
	background: #fff7e6;
	color: #b26a00;
}

.torantejarat-tickets-list li .ticket-badge.awaiting_customer {
	background: #eef4fa;
	color: #36567a;
}

.torantejarat-tickets-list li .ticket-badge.answered {
	background: #e3f2fd;
	color: #1565c0;
}

.ticket-close-form {
	margin-top: 8px;
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
	transition: all 0.2s ease;
}

.ticket-close-form button:hover {
	background: #fdecea;
	border-color: #f5c7c3;
}

.ticket-delete-form {
	display: inline-block;
	margin-top: 8px;
}

.ticket-delete-form button {
	background: #fef3f2;
	color: #b42318;
	border: 1px solid #f5c7c3;
	border-radius: 8px;
	padding: 8px 16px;
	font-size: 13px;
	font-weight: 700;
	cursor: pointer;
	transition: all 0.2s ease;
}

.ticket-delete-form button:hover {
	background: #fdecea;
	border-color: #f5c7c3;
}

/* Ticket thread toggle */
.ticket-thread-toggle {
	margin-top: 8px;
	cursor: pointer;
	text-align: right;
	width: 100%;
}

.ticket-thread-toggle button {
	background: none;
	border: 1px solid #dce1db;
	border-radius: 8px;
	padding: 6px 12px;
	font-size: 12px;
	cursor: pointer;
	color: #606c68;
	transition: all 0.2s ease;
}

.ticket-thread-toggle button:hover {
	border-color: var(--tt-color-primary);
	color: var(--tt-color-primary);
}

.ticket-messages {
	margin-top: 10px;
	padding: 12px;
	background: #f4f3ef;
	border-radius: 10px;
	border: 1px solid #e2e5df;
	width: 100%;
}

.ticket-messages p {
	margin: 0 0 8px;
	font-size: 13px;
	color: #172325;
}

.ticket-messages p strong {
	color: #f77b3e;
}

/* New ticket form */
.torantejarat-tickets-form {
	margin-top: 24px;
	border: 1px solid var(--tt-color-border);
	border-radius: 16px;
	padding: 24px;
	background: var(--tt-color-card);
	box-shadow: var(--tt-shadow);
}

.torantejarat-tickets-form h3 {
	font-size: 18px;
	color: var(--tt-color-text);
	margin-bottom: 18px;
	text-align: right;
}

.acp-field {
	margin-bottom: 14px;
	text-align: right;
}

.acp-field label {
	display: block;
	font-size: 13px;
	font-weight: 800;
	color: var(--tt-color-text);
	margin-bottom: 6px;
	text-align: right;
}

.acp-field select,
.acp-field input[type="text"],
.acp-field textarea {
	width: 100%;
	padding: 11px 14px;
	padding-inline-end: 36px;
	border: 1px solid #dce1db;
	border-radius: 10px;
	font-size: 14px;
	font-family: inherit;
	box-sizing: border-box;
	direction: rtl;
	text-align: right;
	transition: border-color 0.2s ease, box-shadow 0.2s ease;
	background: #fff;
}

.acp-field select:focus,
.acp-field input[type="text"]:focus,
.acp-field textarea:focus {
	outline: none;
	border-color: var(--tt-color-primary);
	box-shadow: 0 0 0 3px rgba(247, 123, 62, 0.15);
}

.acp-field textarea {
	min-height: 120px;
	resize: vertical;
	text-align: right;
}

.acp-full {
	grid-column: 1 / -1;
}

button.acp-btn-primary {
	background: var(--tt-color-primary);
	color: #fff;
	border: none;
	border-radius: 8px;
	padding: 12px 28px;
	font-size: 14px;
	font-weight: 700;
	cursor: pointer;
	transition: background 0.2s ease, transform 0.1s ease;
}

button.acp-btn-primary:hover {
	background: var(--tt-color-primary-dark);
}

button.acp-btn-primary:active {
	transform: scale(0.98);
}

.acp-btn {
	display: inline-block;
}

/* Honeypot */
.torantejarat-hp-field {
	display: none !important;
}

/* Ticket detail view */
.ticket-view-detail {
	margin-top: 24px;
	border: 1px solid var(--tt-color-border);
	border-radius: 16px;
	padding: 24px;
	background: var(--tt-color-card);
	box-shadow: var(--tt-shadow);
	text-align: right;
	max-width: 100%;
	box-sizing: border-box;
}

.ticket-view-detail h3 {
	margin-top: 0;
	margin-bottom: 16px;
	font-size: 18px;
	color: var(--tt-color-text);
}

.ticket-view-detail table {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 20px;
}

.ticket-view-detail th,
.ticket-view-detail td {
	padding: 11px 12px;
	text-align: right;
	border-bottom: 1px solid #f2f3ef;
}

.ticket-view-detail th {
	font-weight: 800;
	color: var(--tt-color-text);
	width: 30%;
}

.ticket-view-detail td {
	color: var(--tt-color-text-muted);
}

.ticket-view-detail tr:last-child td,
.ticket-view-detail tr:last-child th {
	border-bottom: none;
}

.ticket-history {
	margin-bottom: 20px;
	background: #f4f3ef;
	border: 1px solid #e2e5df;
	border-radius: 12px;
	padding: 16px;
}

.ticket-history h4 {
	margin-top: 0;
	margin-bottom: 10px;
	font-size: 15px;
	color: var(--tt-color-text);
}

.ticket-message {
	margin-bottom: 12px;
	padding: 11px 14px;
	border-radius: 10px;
	direction: rtl;
	animation: ttMsgSlide 0.3s ease;
}

@keyframes ttMsgSlide {
	from { opacity: 0; transform: translateX(12px); }
	to { opacity: 1; transform: translateX(0); }
}

.ticket-message.customer {
	background: #eef4fa;
	border-right: 4px solid #36567a;
}

.ticket-message.staff {
	background: #eff9f2;
	border-right: 4px solid #247348;
}

.ticket-message strong {
	display: block;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	margin-bottom: 4px;
	color: var(--tt-color-text);
}

.ticket-message .ticket-time {
	font-size: 11px;
	color: var(--tt-color-text-muted);
	display: block;
	margin-top: 2px;
	text-align: right;
}

.ticket-reply-form {
	margin-top: 20px;
	border-top: 1px solid #f2f3ef;
	padding-top: 20px;
	text-align: right;
	padding-bottom: 20px;
	background: #fafaf7;
	border-radius: 12px;
}

.ticket-reply-form h4 {
	margin-top: 0;
	margin-bottom: 12px;
	font-size: 15px;
	color: var(--tt-color-text);
}

/* Back button */
.ticket-back-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 6px;
	background: #f4f3ef;
	color: var(--tt-color-text-muted);
	border: 1px solid #dce1db;
	border-radius: 8px;
	padding: 10px 16px;
	font-size: 13px;
	font-weight: 700;
	cursor: pointer;
	text-decoration: none;
	margin-bottom: 20px;
	transition: all 0.2s ease;
}

.ticket-back-btn:hover {
	background: #e9ece8;
	color: var(--tt-color-text);
	border-color: #c9cfc6;
}

/* =========================================
   MOBILE RESPONSIVE (≤768px)
   ========================================= */
@media (max-width: 768px) {
	.torantejarat-tickets-wrap {
		padding: 16px 12px;
	}

	.acp__hello h2 {
		font-size: 20px;
	}

	.acp__hello p {
		font-size: 14px;
	}

	.torantejarat-tickets-list li {
		padding: 16px 18px;
	}

	.torantejarat-tickets-list li .ticket-actions {
		flex-direction: column;
		align-items: flex-end;
		width: 100%;
	}

	.torantejarat-tickets-list li .ticket-badge {
		margin: 0 0 8px 0;
		width: fit-content;
	}

	.ticket-close-form,
	.ticket-delete-form {
		width: 100%;
		text-align: center;
		margin-top: 10px;
	}

	.ticket-close-form button,
	.ticket-delete-form button {
		width: 100%;
		padding: 12px 0;
		font-size: 14px;
	}

	.ticket-thread-toggle button {
		width: 100%;
		justify-content: center;
		display: flex;
		justify-content: center;
	}

	.torantejarat-tickets-form {
		padding: 18px 16px;
		margin-top: 16px;
	}

	.acp-field {
		margin-bottom: 12px;
	}

	.acp-field select,
	.acp-field input[type="text"],
	.acp-field textarea {
		padding: 12px 16px;
		font-size: 16px; /* Prevent zoom on iOS */
	}

	button.acp-btn-primary {
		width: 100%;
		padding: 14px 0;
		font-size: 16px;
	}

	.ticket-view-detail {
		padding: 18px 16px;
		margin-top: 16px;
	}

	.ticket-view-detail h3 {
		font-size: 17px;
	}

	.ticket-view-detail table {
		font-size: 14px;
	}

	.ticket-view-detail th,
	.ticket-view-detail td {
		padding: 10px 12px;
		font-size: 14px;
	}

	.ticket-message {
		padding: 10px 12px;
		font-size: 13px;
	}

	.ticket-reply-form {
		padding: 16px;
		margin-top: 16px;
	}

	.ticket-back-btn {
		width: 100%;
		justify-content: center;
		padding: 12px 0;
		font-size: 14px;
	}
}

/* =========================================
   EXTRA SMALL MOBILE (≤480px)
   ========================================= */
@media (max-width: 480px) {
	.torantejarat-tickets-wrap {
		padding: 12px 10px;
	}

	.acp__hello h2 {
		font-size: 18px;
	}

	.torantejarat-tickets-list li {
		padding: 14px 16px;
	}

	.torantejarat-tickets-list li .ticket-title {
		font-size: 14px;
	}

	.torantejarat-tickets-list li .ticket-meta {
		font-size: 11px;
	}

	.ticket-badge {
		font-size: 10px;
		padding: 4px 9px;
	}

	.ticket-view-detail {
		padding: 14px 12px;
	}

	.ticket-view-detail h3 {
		font-size: 16px;
	}

	button.acp-btn-primary {
		padding: 16px 0;
		font-size: 15px;
	}

	.acp-field select,
	.acp-field input[type="text"],
	.acp-field textarea {
		font-size: 16px;
		padding: 12px 14px;
	}
}
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
			$can_close  = in_array( $ticket_st, array( 'awaiting_support' ), true );
			$status_badge_class = 'open' === $ticket_st ? 'open' : 'closed';
			?>
			<li data-view-url="<?php echo esc_url( add_query_arg( 'view_ticket', $ticket_id, wc_get_account_endpoint_url( 'tickets' ) ) ); ?>" class="ticket-row" style="cursor:pointer;">
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
					<?php if ( $ticket_st === 'closed' ) : ?>
						<div class="ticket-delete-form">
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm( '<?php esc_attr_e( 'آیا مطمئن هستید می‌خواهید این تیکت را حذف کنید؟ این عمل غیرقابل‌بازگشت است.', 'torantejarat' ); ?>' );">
								<?php wp_nonce_field( 'torantejarat-ticket-delete', 'torantejarat_ticket_delete_nonce' ); ?>
								<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket_id ); ?>">
								<input type="hidden" name="action" value="torantejarat_ticket_delete_frontend">
								<button type="submit"><?php esc_html_e( 'حذف تیکت', 'torantejarat' ); ?></button>
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

	<?php if ( empty( $_GET["view_ticket"] ) ) : ?>
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
	<?php endif; ?>

<?php if ( ! empty( $_GET['view_ticket'] ) && is_numeric( $_GET['view_ticket'] ) ) : ?>
	<?php
	$view_id = absint( $_GET['view_ticket'] );
	$view_ticket = \ToranTejarat\Theme\torantejarat_tickets_get( $view_id );
	if ( $view_ticket && (int) $view_ticket['user_id'] === $user_id ) :
		$view_messages = \ToranTejarat\Theme\torantejarat_tickets_get_messages( $view_id );
		$view_st       = isset( $view_ticket['status'] ) ? $view_ticket['status'] : 'open';
		$can_reply     = in_array( $view_st, array( 'open', 'awaiting_support', 'awaiting_customer' ), true );
		$can_close     = in_array( $view_st, array( 'awaiting_support' ), true );
		$status_badge_class = 'open' === $view_st ? 'open' : 'closed';
		?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'tickets' ) ); ?>" class="ticket-back-btn">
			<span>↩</span> <?php esc_html_e( 'بازگشت به لیست تیکت‌ها', 'torantejarat' ); ?>
		</a>
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
			<div class="ticket-close-form">
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


<script>
(function() {
    var rows = document.querySelectorAll('.ticket-row');
    rows.forEach(function(row) {
        row.addEventListener('click', function(e) {
            // Don't navigate if clicking on a link, button, or form element
            var target = e.target;
            while (target && target !== row) {
                if (target.tagName === 'A' || target.tagName === 'BUTTON' || target.tagName === 'INPUT' || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA') {
                    return;
                }
                target = target.parentElement;
            }
            var url = row.getAttribute('data-view-url');
            if (url) {
                window.location.href = url;
            }
        });
    });

    // Toggle conversation thread
    var toggles = document.querySelectorAll('.ticket-thread-toggle button');
    toggles.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var ticketId = btn.getAttribute('data-toggle-ticket');
            var box = document.getElementById(ticketId);
            if (box) {
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', !expanded);
                box.style.display = expanded ? 'none' : 'block';
            }
        });
    });
})();
</script>