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

// AJAX data for ticket forms (nonces + ajaxurl)
$tt_ajax_nonces = array(
	'create' => wp_create_nonce( 'torantejarat-ticket-create' ),
	'reply'  => wp_create_nonce( 'torantejarat-ticket-reply' ),
	'close'  => wp_create_nonce( 'torantejarat-ticket-close' ),
	'delete' => wp_create_nonce( 'torantejarat-ticket-delete' ),
	'reopen' => wp_create_nonce( 'torantejarat-ticket-reopen' ),
	'detail' => wp_create_nonce( 'torantejarat-ticket-detail' ),
);
?>
<script>
var torantejarat_ajax = {
	ajaxurl : '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>',
	nonces  : <?php echo wp_json_encode( $tt_ajax_nonces ); ?>,
};
</script>
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

/* =========================================
   Compact ticket list table
   ========================================= */
.torantejarat-tickets-table {
	width: 100%;
	border-collapse: collapse;
	margin: 0 0 16px 0;
	background: var(--tt-color-card);
	border-radius: var(--tt-radius);
	overflow: hidden;
	border: 1px solid var(--tt-color-border);
	box-shadow: var(--tt-shadow);
}

.torantejarat-tickets-table th,
.torantejarat-tickets-table td {
	padding: 10px 14px;
	text-align: right;
	border-bottom: 1px solid var(--tt-color-border);
	font-size: 14px;
}

.torantejarat-tickets-table th {
	background: #f9faf8;
	font-weight: 800;
	color: var(--tt-color-text);
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 0.03em;
}

.torantejarat-tickets-table td {
	color: var(--tt-color-text-muted);
}

.torantejarat-tickets-table tr {
	cursor: pointer;
	transition: background 0.15s ease;
}

.torantejarat-tickets-table tr:hover {
	background: #f5f7f4;
}

.torantejarat-tickets-table tr:last-child td {
	border-bottom: none;
}

.torantejarat-tickets-table .ticket-status-badge {
	font-size: 11px;
	font-weight: 800;
	padding: 4px 10px;
	border-radius: 20px;
	display: inline-block;
}

.torantejarat-tickets-table .ticket-actions-cell {
	text-align: left;
	white-space: nowrap;
}

.torantejarat-tickets-table .ticket-actions-cell button {
	font-size: 12px;
	padding: 6px 12px;
	border-radius: 6px;
	border: 1px solid var(--tt-color-border);
	background: #fff;
	cursor: pointer;
	margin-left: 4px;
	transition: all 0.15s ease;
}

.torantejarat-tickets-table .ticket-actions-cell button:hover {
	border-color: var(--tt-color-primary);
	color: var(--tt-color-primary);
}

.torantejarat-tickets-table .ticket-actions-cell .btn-reopen {
	background: #eef4fa;
	color: #36567a;
	border-color: #c9d6df;
}

/* Ticket detail panel (AJAX-loaded, separate detail view) */
.ticket-detail-panel {
	display: none;
	margin-top: 20px;
	border: 1px solid var(--tt-color-border);
	border-radius: 16px;
	background: var(--tt-color-card);
	box-shadow: var(--tt-shadow);
	overflow: hidden;
	animation: ttSlideIn 0.3s ease;
}

.ticket-detail-panel .ticket-detail-header {
	padding: 16px 20px;
	border-bottom: 1px solid var(--tt-color-border);
	background: #f9faf8;
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.ticket-detail-panel .ticket-detail-back {
	background: none;
	border: none;
	color: var(--tt-color-primary);
	cursor: pointer;
	font-size: 14px;
	font-weight: 700;
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 8px 12px;
	border-radius: 6px;
	transition: all 0.15s ease;
}

.ticket-detail-panel .ticket-detail-back:hover {
	background: #f4f3ef;
	color: var(--tt-color-primary-dark);
}

.ticket-detail-panel .ticket-detail-content {
	padding: 20px;
}

.ticket-detail-panel .ticket-detail-reply {
	margin-top: 20px;
	border-top: 1px solid var(--tt-color-border);
	padding-top: 20px;
	background: #fafaf7;
	border-radius: 12px;
}

/* AJAX loading states */
.ticket-ajax-loading {
	opacity: 0.6;
	pointer-events: none;
	position: relative;
}

.ticket-ajax-loading::after {
	content: '';
	position: absolute;
	top: 50%;
	left: 50%;
	width: 16px;
	height: 16px;
	margin: -8px 0 0 -8px;
	border: 2px solid var(--tt-color-border);
	border-top-color: var(--tt-color-primary);
	border-radius: 50%;
	animation: ttSpin 0.8s linear infinite;
}

@keyframes ttSpin {
	to { transform: rotate(360deg); }
}

/* Notice for AJAX results */
.torantejarat-tickets-ajax-notice {
	margin-bottom: 16px;
	padding: 12px 16px;
	border-radius: 10px;
	font-size: 14px;
	display: none;
	animation: ttSlideIn 0.3s ease;
}

.torantejarat-tickets-ajax-notice.is-success {
	background: var(--tt-color-success-bg);
	color: var(--tt-color-success);
	border: 1px solid #d1e9dd;
}

.torantejarat-tickets-ajax-notice.is-error {
	background: var(--tt-color-error-bg);
	color: var(--tt-color-error);
	border: 1px solid #f5c7c3;
}

@media (max-width: 768px) {
	.torantejarat-tickets-table,
	.torantejarat-tickets-table thead,
	.torantejarat-tickets-table tbody,
	.torantejarat-tickets-table th,
	.torantejarat-tickets-table td,
	.torantejarat-tickets-table tr {
		display: block;
	}

	.torantejarat-tickets-table thead tr {
		position: absolute;
		top: -9999px;
		left: -9999px;
	}

	.torantejarat-tickets-table tr {
		margin-bottom: 10px;
	}

	.torantejarat-tickets-table td {
		border: none;
		border-bottom: 1px solid var(--tt-color-border);
		position: relative;
		padding-left: 50%;
		text-align: right;
	}

	.torantejarat-tickets-table td::before {
		content: attr(data-label);
		position: absolute;
		left: 12px;
		width: 45%;
		padding-right: 10px;
		white-space: nowrap;
		font-weight: 800;
		font-size: 12px;
		color: var(--tt-color-text-muted);
		text-align: right;
	}

	.torantejarat-tickets-table .ticket-actions-cell {
		text-align: right;
	}

	.torantejarat-tickets-table .ticket-actions-cell button {
		display: block;
		width: 100%;
		margin-left: 0;
		margin-top: 6px;
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
	<div class="torantejarat-tickets-table-wrap">
	<table class="torantejarat-tickets-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'عنوان', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'تاریخ', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'عمليات', 'torantejarat' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $tickets as $ticket ) : ?>
			<?php
			$messages   = \ToranTejarat\Theme\torantejarat_tickets_get_messages( $ticket['id'] );
			$ticket_st  = isset( $ticket['status'] ) ? $ticket['status'] : 'open';
			$ticket_id  = (int) $ticket['id'];
			$can_close  = in_array( $ticket_st, array( 'awaiting_support' ), true );
			$status_badge_class = 'open' === $ticket_st ? 'open' : 'closed';
			?>
<td data-label="<?php esc_attr_e( 'عنوان', 'torantejarat' ); ?>">
			<a href="<?php echo esc_url( add_query_arg( 'view_ticket', $ticket_id, wc_get_account_endpoint_url( 'tickets' ) ) ); ?>" class="ticket-table-title"><?php echo esc_html( $ticket['title'] ); ?></a>
			<div class="ticket-table-meta">
				<?php echo esc_html( mysql2date( 'Y/m/d', $ticket['created_at'] ) ); ?>
				<?php if ( ! empty( $ticket['order_id'] ) ) : ?>
					&nbsp;·&nbsp;<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $ticket['order_id'] ) ); ?>
				<?php endif; ?>
			</div>
		</td>
		<td data-label="<?php esc_attr_e( 'وضعیت', 'torantejarat' ); ?>">
			<span class="ticket-status-badge <?php echo esc_attr( $status_badge_class ); ?>">
				<?php echo esc_html( $torantejarat_statuses[ $ticket_st ] ?? $ticket_st ); ?>
			</span>
		</td>
		<td data-label="<?php esc_attr_e( 'تاریخ', 'torantejarat' ); ?>">
			<?php echo esc_html( mysql2date( 'Y/m/d', $ticket['created_at'] ) ); ?>
		</td>
		<td data-label="<?php esc_attr_e( 'عمليات', 'torantejarat' ); ?>" class="ticket-actions-cell">
			<?php if ( $messages ) : ?>
				<button type="button" class="btn-toggle-thread" data-toggle-ticket="ticket-<?php echo esc_attr( $ticket_id ); ?>" aria-expanded="false"><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></button>
			<?php endif; ?>
			<?php if ( $can_close ) : ?>
				<button type="button" class="btn-ajax btn-close" data-ticket-id="<?php echo esc_attr( $ticket_id ); ?>" data-nonce="<?php echo esc_attr( $tt_ajax_nonces['close'] ); ?>"><?php esc_html_e( 'بستن', 'torantejarat' ); ?></button>
			<?php endif; ?>
			<?php if ( $ticket_st === 'closed' ) : ?>
				<button type="button" class="btn-ajax btn-reopen" data-ticket-id="<?php echo esc_attr( $ticket_id ); ?>" data-nonce="<?php echo esc_attr( $tt_ajax_nonces['reopen'] ); ?>"><?php esc_html_e( 'باز کردن', 'torantejarat' ); ?></button>
				<button type="button" class="btn-ajax btn-delete" data-ticket-id="<?php echo esc_attr( $ticket_id ); ?>" data-nonce="<?php echo esc_attr( $tt_ajax_nonces['delete'] ); ?>"><?php esc_html_e( 'حذف', 'torantejarat' ); ?></button>
			<?php endif; ?>
		</td>
	</tr>
	<tr class="ticket-thread-row" id="ticket-<?php echo esc_attr( $ticket_id ); ?>" style="display:none;">
		<td colspan="4">
			<div class="ticket-messages">
				<?php foreach ( $messages as $msg ) : ?>
					<p>
						<strong><?php echo (int) $msg['is_staff'] === 1 ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'شما', 'torantejarat' ); ?>:</strong>
						<?php echo esc_html( $msg['body'] ); ?>
					</p>
				<?php endforeach; ?>
			</div>
		</td>
	</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php else : ?>
	<div class="torantejarat-tickets-empty">
		<h3><?php esc_html_e( 'تیکتی ثبت نشده است.', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'سؤال یا مشکلی دارید؟ از فرم زیر بنویسید.', 'torantejarat' ); ?></p>
	</div>
<?php endif; ?>

	<?php if ( empty( $_GET["view_ticket"] ) ) : ?>
<div class="torantejarat-tickets-form">
	<h3><?php esc_html_e( 'ثبت تیکت جدید', 'torantejarat' ); ?></h3>
	<form class="acp-form-grid ajax-ticket-form" id="acp-ticket-form" data-ajax-action="torantejarat_ticket_create_ajax" novalidate>
		<input type="hidden" name="action" value="torantejarat_ticket_create_ajax">
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

<div class="ticket-detail-panel" id="ticket-detail-panel">
	<div class="ticket-detail-header">
		<button class="ticket-detail-back" id="ticket-detail-back">
			<span>↩</span> <?php esc_html_e( 'بازگشت به لیست', 'torantejarat' ); ?>
		</button>
		<span class="ticket-detail-title" id="ticket-detail-title"></span>
	</div>
	<div class="ticket-detail-content" id="ticket-detail-content">
		<!-- Projon baraye kheili AJAX barmigardade -->
	</div>
</div>

<!-- Fallback for direct ?view_ticket= URLs (non-JS or direct access) -->
<?php if ( ! empty( $_GET['view_ticket'] ) && is_numeric( $_GET['view_ticket'] ) ) : ?>
	<div class="torantejarat-tickets-ajax-notice is-error" style="display:block;">
		<?php esc_html_e( 'لطفاً از دکمه‌های تیکت در لیست استفاده کنید یا صفحه را رفرش نمایید.', 'torantejarat' ); ?>
	</div>
<?php endif; ?>


<script>
var torantejaratTickets = (function() {
	'use strict';

	var panel   = document.getElementById('ticket-detail-panel');
	var backBtn = document.getElementById('ticket-detail-back');
	var titleEl = document.getElementById('ticket-detail-title');
	var content = document.getElementById('ticket-detail-content');
	var noticeWrap = document.createElement('div');
	noticeWrap.className = 'torantejarat-tickets-ajax-notice';
	panel.parentNode.insertBefore(noticeWrap, panel);

	function showNotice(msg, type) {
		noticeWrap.className = 'torantejarat-tickets-ajax-notice is-' + type;
		noticeWrap.textContent = msg;
		noticeWrap.style.display = 'block';
		setTimeout(function() { noticeWrap.style.display = 'none'; }, 5000);
	}

	function ajax(action, data, onSuccess, onError) {
		data = data || {};
		data.action = action;
		if (typeof torantejarat_ajax !== 'undefined' && torantejarat_ajax.nonces) {
			var nonceMap = {
				'torantejarat_ticket_create_ajax': 'create',
				'torantejarat_ticket_reply_ajax': 'reply',
				'torantejarat_ticket_close_ajax': 'close',
				'torantejarat_ticket_delete_ajax': 'delete',
				'torantejarat_ticket_reopen_ajax': 'reopen'
			};
			var key = nonceMap[action];
			if (key) {
				data['torantejarat_ticket_' + key + '_nonce'] = torantejarat_ajax.nonces[key];
			}
		}

		var formData = new FormData();
		for (var k in data) {
			formData.append(k, data[k]);
		}

		fetch(torantejarat_ajax.ajaxurl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		}).then(function(r) { return r.json(); }).then(function(res) {
			if (res.success) {
				if (onSuccess) onSuccess(res.data);
			} else {
				if (onError) onError(res.data || { message: 'خطای نامشخص' });
				else showNotice((res.data && res.data.message) || 'خطای نامشخص', 'error');
			}
		}).catch(function() {
			showNotice('اتصال به سرور برقرار نشد.', 'error');
		});
	}

	// --- Create ticket form (AJAX) ---
	var createForm = document.getElementById('acp-ticket-form');
	if (createForm) {
		createForm.addEventListener('submit', function(e) {
			e.preventDefault();
			var form = e.target;
			var btn = form.querySelector('button[type="submit"]');
			btn.disabled = true;
			btn.textContent = 'در حال ارسال...';

			var fd = new FormData(form);
			var fdObj = {};
			fd.forEach(function(v, k) { fdObj[k] = v; });
			ajax('torantejarat_ticket_create_ajax', fdObj, function(data) {
				showNotice(data.message || 'تیکت ثبت شد.', 'success');
				form.reset();
				window.location.reload();
			}, function(err) {
				showNotice(err.message || 'خطا در ثبت تیکت.', 'error');
				btn.disabled = false;
				btn.textContent = 'ارسال تیکت';
			});
		});
	}

	// --- Reply form (AJAX) ---
	document.addEventListener('submit', function(e) {
		if (e.target.classList.contains('ajax-reply-form')) {
			e.preventDefault();
			var form = e.target;
			var btn = form.querySelector('button[type="submit"]');
			btn.disabled = true;
			btn.textContent = 'در حال ارسال...';

			var fd = new FormData(form);
			var fdObj = {};
			fd.forEach(function(v, k) { fdObj[k] = v; });
			ajax('torantejarat_ticket_reply_ajax', fdObj, function(data) {
				showNotice(data.message || 'پاسخ ارسال شد.', 'success');
				form.reset();
				var ticketId = form.querySelector('input[name="ticket_id"]').value;
				loadDetail(ticketId);
			}, function(err) {
				showNotice(err.message || 'خطا در ارسال پاسخ.', 'error');
				btn.disabled = false;
				btn.textContent = 'ارسال پاسخ';
			});
		}
	});

	// --- AJAX buttons (close, reopen, delete) ---
	document.addEventListener('click', function(e) {
		var btn = e.target.closest('.btn-ajax');
		if (!btn) return;

		e.preventDefault();
		e.stopPropagation();

		var action = btn.classList.contains('btn-close')    ? 'torantejarat_ticket_close_ajax' :
		             btn.classList.contains('btn-reopen')   ? 'torantejarat_ticket_reopen_ajax' :
		             btn.classList.contains('btn-delete')   ? 'torantejarat_ticket_delete_ajax' : null;
		if (!action) return;

		var ticketId = btn.getAttribute('data-ticket-id');
		var nonce    = btn.getAttribute('data-nonce');

		var msg = btn.classList.contains('btn-delete')
			? 'آیا مطمئن هستید می‌خواهید این تیکت را حذف کنید؟ این عمل غیرقابل‌بازگشت است.'
			: btn.classList.contains('btn-reopen')
			? 'آیا مطمئن هستید می‌خواهید این تیکت را باز کنید؟'
			: 'آیا مطمئن هستید می‌خواهید این تیکت را ببندید؟';

		if (!confirm(msg)) return;

		btn.disabled = true;
		btn.textContent = 'در حال پردازش...';

		var nonceKey = action.indexOf('close') > -1 ? 'close' : action.indexOf('reopen') > -1 ? 'reopen' : 'delete';
		ajax(action, {
			ticket_id: ticketId,
			'torantejarat_ticket_' + nonceKey + '_nonce': nonce
		}, function(data) {
			showNotice(data.message || 'انجام شد.', 'success');
			if (data.status) {
				var row = document.querySelector('tr[data-view-url*="view_ticket=' + ticketId + '"]');
				if (row) {
					var badge = row.querySelector('.ticket-status-badge');
					if (badge) {
						badge.textContent = data.status;
						badge.className = 'ticket-status-badge ' + data.status;
					}
				}
				if (panel.style.display !== 'none' && titleEl.textContent.indexOf(ticketId) > -1) {
					loadDetail(ticketId);
				}
			}
			window.location.reload();
		}, function(err) {
			showNotice(err.message || 'خطا در انجام عملیات.', 'error');
			btn.disabled = false;
		});
	});

	// --- Load ticket detail via AJAX ---
	window.loadDetail = function(ticketId) {
		panel.classList.add('ticket-ajax-loading');
		content.innerHTML = '<p style="text-align:center;color:#606c68;">در حال بارگذاری...</p>';
		panel.style.display = 'block';
		titleEl.textContent = 'تیکت #' + ticketId;

		ajax('torantejarat_ticket_detail_ajax', {
			ticket_id: ticketId,
			'torantejarat_ticket_detail_nonce': torantejarat_ajax.nonces.detail
		}, function(data) {
			panel.classList.remove('ticket-ajax-loading');
			content.innerHTML = data.html;
		}, function(err) {
			panel.classList.remove('ticket-ajax-loading');
			showNotice(err.message || 'امکان بارگذاری جزئیات تیکت وجود ندارد.', 'error');
		});
	};

	// --- Row click → load detail ---
	document.addEventListener('click', function(e) {
		var row = e.target.closest('tr.ticket-row');
		if (!row) return;

		if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		var url = row.getAttribute('data-view-url');
		if (!url) return;

		var ticketId = url.match(/view_ticket=(\d+)/);
		if (ticketId) {
			loadDetail(ticketId[1]);
		}
	});

	// --- Back button ---
	if (backBtn) {
		backBtn.addEventListener('click', function(e) {
			e.preventDefault();
			panel.style.display = 'none';
			content.innerHTML = '';
			titleEl.textContent = '';
		});
	}

	// Thread toggle (for inline messages in table rows)
	document.addEventListener('click', function(e) {
		var btn = e.target.closest('.btn-toggle-thread');
		if (!btn) return;
		e.stopPropagation();
		var ticketId = btn.getAttribute('data-toggle-ticket');
		var box = document.getElementById(ticketId);
		if (box) {
			var expanded = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', !expanded);
			box.style.display = expanded ? 'none' : 'table-row';
		}
	});

})();
</script>