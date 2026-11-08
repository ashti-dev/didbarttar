<?php
/**
 * Tickets endpoint — list, create and thread views.
 * Uses the independent torantejarat_tickets tables (not the CPT).
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

$user_id    = get_current_user_id();
$tickets    = \ToranTejarat\Theme\torantejarat_tickets_get_user_tickets( $user_id );
$subjects   = \ToranTejarat\Theme\torantejarat_ticket_subjects();
$statuses   = \ToranTejarat\Theme\torantejarat_ticket_statuses();
$priorities = \ToranTejarat\Theme\torantejarat_tickets_priorities();

$ticket_sent    = isset( $_GET['ticket_sent'] ) && '1' === $_GET['ticket_sent'];
$ticket_created = isset( $_GET['ticket_created'] ) && '1' === $_GET['ticket_created'];
$ticket_deleted = isset( $_GET['ticket_deleted'] ) && '1' === $_GET['ticket_deleted'];
$ticket_error   = isset( $_GET['ticket_error'] ) ? sanitize_key( wp_unslash( $_GET['ticket_error'] ) ) : '';

$torantejarat_ticket_error_messages = array(
	'nonce'              => __( 'درخواست نامعتبر است.', 'torantejarat' ),
	'not_found'          => __( 'تیکت یافت نشد.', 'torantejarat' ),
	'empty_title'        => __( 'عنوان تیکت الزامی است.', 'torantejarat' ),
	'title_too_long'     => __( 'عنوان حداکثر ۱۵۰ کاراکتر می‌تواند باشد.', 'torantejarat' ),
	'body_too_short'     => __( 'پیام باید حداقل ۱۰ کاراکتر باشد.', 'torantejarat' ),
	'invalid_order'      => __( 'سفارش معتبر نیست یا به شما تعلق ندارد.', 'torantejarat' ),
	'rate_limit_open'    => __( 'شما دارای ۳ تیکت باز هستید. تا بستن یکی از آن‌ها صبر کنید.', 'torantejarat' ),
	'rate_limit_seconds' => __( 'لطفاً حداقل ۶۰ ثانیه قبل از ارسال تیکت دیگر صبر کنید.', 'torantejarat' ),
	'forbidden'          => __( 'شما دسترسی به این تیکت را ندارید.', 'torantejarat' ),
	'db_error'           => __( 'خطا در ذخیره تیکت.', 'torantejarat' ),
	'not_closed'         => __( 'فقط تیکت‌های بسته قابل بازگشایی هستند.', 'torantejarat' ),
	'invalid_transition' => __( 'تغییر وضعیت مجاز نیست.', 'torantejarat' ),
);
$torantejarat_ticket_error_msg = $torantejarat_ticket_error_messages[ $ticket_error ] ?? esc_html( ucfirst( str_replace( '_', ' ', $ticket_error ) ) );

$endpoint_url = wc_get_account_endpoint_url( 'tickets' );
$create_url   = add_query_arg( 'ticket_view', 'new', $endpoint_url );

$view_id     = isset( $_GET['view_ticket'] ) ? absint( $_GET['view_ticket'] ) : 0;
$view_ticket = null;

if ( $view_id ) {
	$view_ticket = \ToranTejarat\Theme\torantejarat_tickets_get( $view_id );
	if ( ! $view_ticket || (int) $view_ticket['user_id'] !== (int) $user_id ) {
		$view_ticket = null;
		$ticket_error = 'forbidden';
		$torantejarat_ticket_error_msg = $torantejarat_ticket_error_messages['forbidden'];
	}
}

$view_mode   = isset( $_GET['ticket_view'] ) ? sanitize_key( wp_unslash( $_GET['ticket_view'] ) ) : '';
$show_create = ( 'new' === $view_mode ) && ! $view_ticket;
$messages    = $view_ticket ? \ToranTejarat\Theme\torantejarat_tickets_get_messages( $view_id ) : array();

// Orders used by the "اکانت مورد نظر" selector.
$order_objects = array();
if ( isset( $orders ) && is_array( $orders ) ) {
	foreach ( $orders as $order_item ) {
		if ( $order_item instanceof \WC_Order ) {
			$order_objects[] = $order_item;
		} elseif ( is_numeric( $order_item ) ) {
			$order_object = wc_get_order( absint( $order_item ) );
			if ( $order_object ) {
				$order_objects[] = $order_object;
			}
		}
	}
}
if ( ! $order_objects ) {
	$order_objects = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'limit'       => 20,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
}

$default_order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
$default_priority = 'low';
?>
<div class="ttk" dir="rtl">

	<?php if ( $ticket_sent ) : ?>
		<div class="ttk-notice ttk-notice--success" role="status">
			<strong>
				<?php
				if ( $ticket_created ) {
					esc_html_e( 'تیکت با موفقیت ثبت شد.', 'torantejarat' );
				} elseif ( $view_ticket ) {
					esc_html_e( 'پاسخ شما با موفقیت ثبت شد.', 'torantejarat' );
				} else {
					esc_html_e( 'تیکت با موفقیت ثبت شد.', 'torantejarat' );
				}
				?>
			</strong>
			<span><?php esc_html_e( 'پشتیبانی در اسرع وقت به درخواست شما پاسخ می‌دهد.', 'torantejarat' ); ?></span>
		</div>
	<?php endif; ?>

	<?php if ( $ticket_deleted ) : ?>
		<div class="ttk-notice ttk-notice--success" role="status">
			<strong><?php esc_html_e( 'تیکت با موفقیت حذف شد.', 'torantejarat' ); ?></strong>
		</div>
	<?php endif; ?>

	<?php if ( $ticket_error ) : ?>
		<div class="ttk-notice ttk-notice--error" role="alert">
			<strong><?php esc_html_e( 'انجام درخواست ممکن نشد.', 'torantejarat' ); ?></strong>
			<span><?php echo esc_html( $torantejarat_ticket_error_msg ); ?></span>
		</div>
	<?php endif; ?>

	<?php if ( $view_ticket ) : ?>
		<?php
		$current_status   = isset( $view_ticket['status'] ) ? $view_ticket['status'] : 'open';
		$current_priority = isset( $view_ticket['priority'] ) ? $view_ticket['priority'] : 'normal';
		$current_subject  = isset( $view_ticket['subject_key'] ) ? $view_ticket['subject_key'] : 'other';
		$status_label     = $statuses[ $current_status ] ?? $current_status;
		$priority_label   = $priorities[ $current_priority ] ?? $priorities['normal'];
		$subject_label    = \ToranTejarat\Theme\torantejarat_tickets_subject_label( $current_subject );
		$can_reply        = in_array( $current_status, array( 'open', 'awaiting_support', 'awaiting_customer', 'answered', 'reopened' ), true );
		$can_close        = ( 'awaiting_support' === $current_status );
		$is_closed        = ( 'closed' === $current_status );
		$owner            = get_userdata( (int) $view_ticket['user_id'] );
		$owner_name       = $owner ? $owner->display_name : __( 'مشتری', 'torantejarat' );
		?>

		<a class="ttk-back" href="<?php echo esc_url( $endpoint_url ); ?>">
			<span aria-hidden="true">→</span>
			<?php esc_html_e( 'بازگشت به لیست تیکت ها', 'torantejarat' ); ?>
		</a>

		<article class="ttk-thread">
			<header class="ttk-thread__head">
				<div class="ttk-thread__heading">
					<h2 class="ttk-thread__title"><?php echo esc_html( $view_ticket['title'] ); ?></h2>
					<div class="ttk-thread__meta">
						<span class="ttk-badge ttk-badge--status-<?php echo esc_attr( $current_status ); ?>"><?php echo esc_html( $status_label ); ?></span>
						<span class="ttk-badge ttk-badge--priority-<?php echo esc_attr( $current_priority ); ?>"><?php echo esc_html( $priority_label ); ?></span>
						<span><?php echo esc_html( $subject_label ); ?></span>
						<span><?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_format_datetime( $view_ticket['created_at'] ) ); ?></span>
						<?php if ( $can_close ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ttk-inline-form">
								<input type="hidden" name="action" value="torantejarat_ticket_close_frontend">
								<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_ticket['id'] ); ?>">
								<?php wp_nonce_field( 'torantejarat-ticket-close', 'torantejarat_ticket_close_nonce' ); ?>
								<button type="submit" class="ttk-btn ttk-btn--ghost"><?php esc_html_e( 'بستن تیکت', 'torantejarat' ); ?></button>
							</form>
						<?php endif; ?>
					</div>
				</div>
				<span class="ttk-thread__id" dir="ltr">#<?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_fa_digits( $view_ticket['id'] ) ); ?></span>
			</header>

			<div class="ttk-thread__body">
				<?php if ( $messages ) : ?>
					<?php foreach ( $messages as $message ) : ?>
						<?php
						$is_staff      = (int) $message['is_staff'] === 1;
						$author        = get_userdata( (int) $message['author_id'] );
						$author_name   = $author ? $author->display_name : ( $is_staff ? __( 'پشتیبانی', 'torantejarat' ) : $owner_name );
						$author_letter = mb_substr( trim( $author_name ), 0, 1, 'UTF-8' );
						$avatar        = get_avatar(
							(int) $message['author_id'],
							84,
							'',
							$author_name,
							array( 'class' => 'ttk-avatar-img' )
						);
						?>
						<div class="ttk-msg <?php echo $is_staff ? 'ttk-msg--staff' : 'ttk-msg--customer'; ?>">
							<div class="ttk-msg__who">
								<span class="ttk-msg__avatar"><?php echo $avatar ? $avatar : esc_html( $author_letter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="ttk-msg__name"><?php echo esc_html( $author_name ); ?></span>
							</div>
							<div class="ttk-msg__content">
								<div class="ttk-msg__bubble">
									<div class="ttk-msg__text"><?php echo \ToranTejarat\Theme\torantejarat_tickets_render_body( $message['body'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
									<?php echo \ToranTejarat\Theme\torantejarat_tickets_attachments_html( $message['attachments'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<div class="ttk-msg__date"><?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_format_datetime( $message['created_at'] ) ); ?></div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="ttk-empty ttk-empty--inline">
						<p><?php esc_html_e( 'هنوز پیامی در این تیکت ثبت نشده است.', 'torantejarat' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<footer class="ttk-thread__foot">
				<div class="ttk-thread__reply-tab"><span><?php esc_html_e( 'پاسخ به این تیکت', 'torantejarat' ); ?></span></div>

				<?php if ( $can_reply ) : ?>
					<form class="ttk-reply" id="ttk-reply-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate>
						<input type="hidden" name="action" value="torantejarat_ticket_reply_frontend">
						<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_ticket['id'] ); ?>">
						<?php wp_nonce_field( 'torantejarat-ticket-reply', 'torantejarat_ticket_reply_nonce' ); ?>
						<input type="hidden" name="attachments" value="[]">

						<label class="ttk-reply__label" for="ttk-reply-body"><?php esc_html_e( 'پیام خود را بنویسید', 'torantejarat' ); ?></label>
						<textarea class="ttk-textarea" id="ttk-reply-body" name="body" rows="4" maxlength="5000" placeholder="<?php esc_attr_e( 'پاسخ خود را بنویسید...', 'torantejarat' ); ?>"></textarea>

						<div class="ttk-form__error" data-ttk-error role="alert" hidden></div>
						<div class="ttk-chips" data-ttk-chips="reply"></div>
						<input type="file" class="ttk-file" data-ttk-files multiple accept="image/*,.pdf,.zip,.doc,.docx,.xls,.xlsx,.txt">

						<div class="ttk-reply__actions">
							<button type="button" class="ttk-btn ttk-btn--gold" data-ttk-pick="reply"><?php esc_html_e( 'افزودن فایل', 'torantejarat' ); ?></button>
							<button type="submit" class="ttk-btn ttk-btn--navy"><?php esc_html_e( 'ارسال پاسخ', 'torantejarat' ); ?></button>
						</div>
					</form>
				<?php elseif ( $is_closed ) : ?>
					<div class="ttk-reply ttk-reply--closed">
						<p><?php esc_html_e( 'این تیکت بسته شده است. برای ادامه گفت‌وگو آن را باز کنید.', 'torantejarat' ); ?></p>
						<div class="ttk-thread__actions">
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="torantejarat_ticket_reopen_frontend">
								<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_ticket['id'] ); ?>">
								<?php wp_nonce_field( 'torantejarat-ticket-reopen', 'torantejarat_ticket_reopen_nonce' ); ?>
								<button type="submit" class="ttk-btn ttk-btn--outline"><?php esc_html_e( 'باز کردن تیکت', 'torantejarat' ); ?></button>
							</form>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'آیا از حذف این تیکت مطمئن هستید؟ این عمل قابل بازگشت نیست.', 'torantejarat' ) ); ?>');">
								<input type="hidden" name="action" value="torantejarat_ticket_delete_frontend">
								<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $view_ticket['id'] ); ?>">
								<?php wp_nonce_field( 'torantejarat-ticket-delete', 'torantejarat_ticket_delete_nonce' ); ?>
								<button type="submit" class="ttk-btn ttk-btn--ghost"><?php esc_html_e( 'حذف تیکت', 'torantejarat' ); ?></button>
							</form>
						</div>
					</div>
				<?php endif; ?>
			</footer>
		</article>

	<?php else : ?>

		<?php if ( $show_create ) : ?>

			<div class="ttk-page-head">
				<h2 class="ttk-page-title"><?php esc_html_e( 'ارسال تیکت', 'torantejarat' ); ?></h2>
				<span class="ttk-page-head__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
				</span>
			</div>

			<form class="ttk-form" id="ttk-create-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate>
				<input type="hidden" name="action" value="torantejarat_ticket_create_frontend">
				<?php wp_nonce_field( 'torantejarat-ticket-create', 'torantejarat_ticket_create_nonce' ); ?>
				<input type="hidden" name="attachments" value="[]">

				<p class="ttk-hp" aria-hidden="true">
					<label><?php esc_html_e( 'این فیلد را خالی بگذارید', 'torantejarat' ); ?>
						<input type="text" name="company" value="" tabindex="-1" autocomplete="off">
					</label>
				</p>

				<div class="ttk-field">
					<label for="ttk-department"><?php esc_html_e( 'دپارتمان مورد نظر', 'torantejarat' ); ?></label>
					<select class="ttk-select" id="ttk-department" name="subject_key" required>
						<option value="" selected disabled><?php esc_html_e( 'لطفا دپارتمان مورد نظر خود را انتخاب نمایید.', 'torantejarat' ); ?></option>
						<?php foreach ( $subjects as $subject_key => $subject_label ) : ?>
							<option value="<?php echo esc_attr( $subject_key ); ?>"><?php echo esc_html( $subject_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ttk-field">
					<label for="ttk-account"><?php esc_html_e( 'اکانت مورد نظر', 'torantejarat' ); ?></label>
					<select class="ttk-select" id="ttk-account" name="order_id">
						<option value="0"><?php esc_html_e( 'انتخاب اکانت', 'torantejarat' ); ?></option>
						<?php foreach ( $order_objects as $order_object ) : ?>
							<?php
							$order_number = $order_object->get_order_number();
							$order_date   = $order_object->get_date_created();
							$order_label  = '#' . $order_number;
							if ( $order_date ) {
								$order_label .= ' — ' . \ToranTejarat\Theme\torantejarat_tickets_format_datetime( $order_date->date( 'Y-m-d H:i:s' ), false );
							}
							?>
							<option value="<?php echo esc_attr( $order_object->get_id() ); ?>"<?php echo (int) $default_order_id === (int) $order_object->get_id() ? ' selected' : ''; ?>><?php echo esc_html( $order_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( ! $order_objects ) : ?>
						<p class="ttk-hint"><?php esc_html_e( 'در حال حاضر اکانت فعالی برای انتخاب وجود ندارد.', 'torantejarat' ); ?></p>
					<?php endif; ?>
				</div>

				<div class="ttk-row">
					<div class="ttk-field">
						<label for="ttk-title"><?php esc_html_e( 'عنوان تیکت', 'torantejarat' ); ?></label>
						<input class="ttk-input" id="ttk-title" name="title" type="text" required maxlength="150" placeholder="<?php esc_attr_e( 'عنوان تیکت را وارد کنید', 'torantejarat' ); ?>">
					</div>
					<div class="ttk-field">
						<label for="ttk-priority"><?php esc_html_e( 'اولویت', 'torantejarat' ); ?></label>
						<select class="ttk-select" id="ttk-priority" name="priority">
							<?php foreach ( $priorities as $priority_key => $priority_label ) : ?>
								<option value="<?php echo esc_attr( $priority_key ); ?>"<?php echo $default_priority === $priority_key ? ' selected' : ''; ?>><?php echo esc_html( $priority_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="ttk-field">
					<label><?php esc_html_e( 'پیام', 'torantejarat' ); ?></label>

					<div class="ttk-editor" data-ttk-editor data-mode="text">
						<div class="ttk-editor__tabs" role="tablist">
							<button type="button" class="ttk-tab is-active" data-ttk-tab="text" role="tab" aria-selected="true"><?php esc_html_e( 'متن', 'torantejarat' ); ?></button>
							<button type="button" class="ttk-tab" data-ttk-tab="visual" role="tab" aria-selected="false"><?php esc_html_e( 'دیداری', 'torantejarat' ); ?></button>
						</div>

						<div class="ttk-toolbar" data-ttk-toolbar role="toolbar" aria-label="<?php esc_attr_e( 'ابزارهای قالب‌بندی متن', 'torantejarat' ); ?>">
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="formatBlock" title="<?php esc_attr_e( 'پاراگراف', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'پاراگراف', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 4v16"/><path d="M17 4v16"/><path d="M19 16H7.5a3.5 3.5 0 0 1 0-7H13"/></svg>
							</button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="undo" title="<?php esc_attr_e( 'واگرد', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'واگرد', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 0 12h-3"/></svg>
							</button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="redo" title="<?php esc_attr_e( 'از نو', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'از نو', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 14 5-5-5-5"/><path d="M20 9H10a6 6 0 0 0 0 12h3"/></svg>
							</button>
							<span class="ttk-toolbar__sep" aria-hidden="true"></span>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="removeFormat" title="<?php esc_attr_e( 'حذف قالب‌بندی', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'حذف قالب‌بندی', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="m12 4-3 16"/><path d="m3 3 18 18"/></svg>
							</button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="createLink" title="<?php esc_attr_e( 'درج پیوند', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'درج پیوند', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.07 0l2-2a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.07 0l-2 2a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>
							</button>
							<span class="ttk-toolbar__sep" aria-hidden="true"></span>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="insertOrderedList" title="<?php esc_attr_e( 'فهرست شماره‌دار', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'فهرست شماره‌دار', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
							</button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="insertUnorderedList" title="<?php esc_attr_e( 'فهرست نقطه‌ای', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'فهرست نقطه‌ای', 'torantejarat' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="9" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1.4" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.4" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.4" fill="currentColor" stroke="none"/></svg>
							</button>
							<span class="ttk-toolbar__sep" aria-hidden="true"></span>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="underline" title="<?php esc_attr_e( 'زیرخط', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'زیرخط', 'torantejarat' ); ?>"><u>U</u></button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="italic" title="<?php esc_attr_e( 'ایتالیک', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'ایتالیک', 'torantejarat' ); ?>"><i>I</i></button>
							<button type="button" class="ttk-toolbar__btn" data-ttk-cmd="bold" title="<?php esc_attr_e( 'درشت', 'torantejarat' ); ?>" aria-label="<?php esc_attr_e( 'درشت', 'torantejarat' ); ?>"><b>B</b></button>
						</div>

						<div class="ttk-editor__pane" data-ttk-pane="text">
							<textarea class="ttk-textarea" id="ttk-body" name="body" rows="12" maxlength="5000" placeholder="<?php esc_attr_e( 'متن پیام خود را بنویسید...', 'torantejarat' ); ?>"></textarea>
						</div>
						<div class="ttk-editor__pane" data-ttk-pane="visual" hidden>
							<div class="ttk-rich" contenteditable="true" role="textbox" aria-multiline="true" data-ttk-rich data-placeholder="<?php esc_attr_e( 'متن پیام خود را بنویسید...', 'torantejarat' ); ?>"></div>
						</div>
					</div>
				</div>

				<div class="ttk-attach">
					<div class="ttk-attach__head">
						<span class="ttk-attach__label"><?php esc_html_e( 'ضمیمه تیکت', 'torantejarat' ); ?></span>
						<button type="button" class="ttk-btn ttk-btn--outline" data-ttk-pick="create"><?php esc_html_e( 'افزودن فایل', 'torantejarat' ); ?></button>
					</div>
					<input type="file" class="ttk-file" data-ttk-files multiple accept="image/*,.pdf,.zip,.doc,.docx,.xls,.xlsx,.txt">
					<div class="ttk-drop" data-ttk-drop tabindex="0" role="button" aria-label="<?php esc_attr_e( 'افزودن فایل', 'torantejarat' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9"/><path d="M12 12v9"/><path d="m8 17 4-4 4 4"/></svg>
						<span><?php esc_html_e( 'برای بارگذاری تصویر (کشیدن / کلیک کنید)', 'torantejarat' ); ?></span>
					</div>
					<div class="ttk-chips" data-ttk-chips="create"></div>
				</div>

				<div class="ttk-form__error" data-ttk-error role="alert" hidden></div>

				<div class="ttk-form__actions">
					<button type="submit" class="ttk-btn ttk-btn--primary"><?php esc_html_e( 'ارسال تیکت', 'torantejarat' ); ?></button>
				</div>
			</form>

		<?php else : ?>

			<div class="ttk-list">
				<div class="ttk-list__head">
					<h2><?php esc_html_e( 'تیکت های من', 'torantejarat' ); ?></h2>
					<span class="ttk-list__count"><?php printf( esc_html__( '%s تیکت', 'torantejarat' ), esc_html( \ToranTejarat\Theme\torantejarat_tickets_fa_digits( number_format_i18n( count( $tickets ) ) ) ) ); ?></span>
				</div>

				<?php if ( $tickets ) : ?>
					<div class="ttk-cards">
						<?php foreach ( $tickets as $ticket_item ) : ?>
							<?php
							$item_id       = (int) $ticket_item['id'];
							$item_status   = isset( $ticket_item['status'] ) ? $ticket_item['status'] : 'open';
							$item_priority = isset( $ticket_item['priority'] ) ? $ticket_item['priority'] : 'normal';
							$item_subject  = isset( $ticket_item['subject_key'] ) ? $ticket_item['subject_key'] : 'other';
							$item_url      = add_query_arg( 'view_ticket', $item_id, $endpoint_url );
							?>
							<a class="ttk-card" href="<?php echo esc_url( $item_url ); ?>">
								<div class="ttk-card__main">
									<div class="ttk-card__title"><?php echo esc_html( $ticket_item['title'] ); ?></div>
									<div class="ttk-card__meta">
										<span><?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_subject_label( $item_subject ) ); ?></span>
										<span dir="ltr">#<?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_fa_digits( $item_id ) ); ?></span>
										<span><?php echo esc_html( \ToranTejarat\Theme\torantejarat_tickets_format_datetime( $ticket_item['created_at'], false ) ); ?></span>
									</div>
								</div>
								<div class="ttk-card__side">
									<span class="ttk-badge ttk-badge--priority-<?php echo esc_attr( $item_priority ); ?>"><?php echo esc_html( $priorities[ $item_priority ] ?? $priorities['normal'] ); ?></span>
									<span class="ttk-badge ttk-badge--status-<?php echo esc_attr( $item_status ); ?>"><?php echo esc_html( $statuses[ $item_status ] ?? $item_status ); ?></span>
									<span class="ttk-card__chevron" aria-hidden="true">‹</span>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="ttk-empty">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
						<h3><?php esc_html_e( 'تیکتی ثبت نشده است.', 'torantejarat' ); ?></h3>
						<p><?php esc_html_e( 'برای ارتباط با پشتیبانی، اولین تیکت خود را ارسال کنید.', 'torantejarat' ); ?></p>
						<a class="ttk-btn ttk-btn--primary" href="<?php echo esc_url( $create_url ); ?>"><?php esc_html_e( 'ایجاد تیکت', 'torantejarat' ); ?></a>
					</div>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	<?php endif; ?>
</div>
