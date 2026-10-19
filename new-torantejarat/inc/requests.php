<?php
/**
 * Service request forms (contact / rent / repairs) with real submission.
 * Stores every request as a private CPT entry and notifies the admin email.
 * Honeypot + nonce + Iranian mobile validation. No plugin required.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_request_types() {
	return array(
		'contact' => __( 'تماس', 'torantejarat' ),
		'rent'    => __( 'اجاره', 'torantejarat' ),
		'repairs' => __( 'تعمیر و بررسی', 'torantejarat' ),
	);
}

function torantejarat_register_request_type() {
	register_post_type(
		'torantejarat_request',
		array(
			'labels'       => array(
				'name'          => __( 'درخواست‌ها', 'torantejarat' ),
				'singular_name' => __( 'درخواست', 'torantejarat' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-clipboard',
			'supports'     => array( 'title' ),
			'capabilities' => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap' => true,
		)
	);
}

function torantejarat_request_topics() {
	return array(
		'consult' => __( 'مشاوره انتخاب دستگاه', 'torantejarat' ),
		'video'   => __( 'درخواست نمونه تصویر', 'torantejarat' ),
		'product' => __( 'سؤال درباره یک دستگاه', 'torantejarat' ),
	);
}

/**
 * Normalize a phone number to 09xxxxxxxxx. Empty string when invalid.
 */
function torantejarat_request_phone( $raw ) {
	$digits = str_replace(
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', ' ', '-', '(', ')' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '', '', '', '' ),
		(string) $raw
	);
	if ( str_starts_with( $digits, '+98' ) ) {
		$digits = '0' . substr( $digits, 3 );
	} elseif ( str_starts_with( $digits, '0098' ) ) {
		$digits = '0' . substr( $digits, 4 );
	}
	if ( 11 === strlen( $digits ) && str_starts_with( $digits, '09' ) && ctype_digit( $digits ) ) {
		return $digits;
	}
	return '';
}

function torantejarat_request_context_product() {
	if ( empty( $_GET['device'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Prefill only.
		return null;
	}
	$product = wc_get_product( absint( $_GET['device'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return $product instanceof \WC_Product ? $product : null;
}

function torantejarat_request_form( $type ) {
	$types = torantejarat_request_types();
	if ( ! isset( $types[ $type ] ) ) {
		return;
	}
	$sent  = isset( $_GET['sent'] ) ? sanitize_key( $_GET['sent'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$topic = isset( $_GET['topic'] ) ? sanitize_key( $_GET['topic'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '1' === $sent ) {
		echo '<div class="torantejarat-form-notice is-success" role="status">';
		echo '<p><strong>' . esc_html__( 'درخواست شما ثبت شد.', 'torantejarat' ) . '</strong></p>';
		echo '<p>' . esc_html__( 'کارشناس پس از بررسی، با همین شماره تماس می‌گیرد.', 'torantejarat' ) . '</p>';
		echo '</div>';
		return;
	}
	if ( '0' === $sent ) {
		$torantejarat_reasons = array(
			'name'    => __( 'نام را کامل وارد کنید (حداقل ۲ حرف).', 'torantejarat' ),
			'phone'   => __( 'شماره موبایل معتبر نیست؛ مثل 09123456789 وارد کنید.', 'torantejarat' ),
			'details' => __( 'شرح درخواست کوتاه است؛ حداقل ۱۰ حرف بنویسید.', 'torantejarat' ),
			'extra'   => __( 'فیلدهای اختصاصی را کامل کنید (تاریخ/شهر/مدل).', 'torantejarat' ),
		);
		$torantejarat_reason = isset( $_GET['reason'] ) ? sanitize_key( $_GET['reason'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display.
		$torantejarat_msg    = isset( $torantejarat_reasons[ $torantejarat_reason ] ) ? $torantejarat_reasons[ $torantejarat_reason ] : __( 'اطلاعات را کامل و درست وارد کنید: نام، موبایل معتبر (مثل 09123456789) و شرح درخواست.', 'torantejarat' );
		echo '<div class="torantejarat-form-notice is-error" role="alert">';
		echo '<p><strong>' . esc_html__( 'درخواست ثبت نشد.', 'torantejarat' ) . '</strong></p>';
		echo '<p>' . esc_html( $torantejarat_msg ) . '</p>';
		echo '</div>';
	}
	$product = torantejarat_request_context_product();
	?>
	<form class="torantejarat-request-formfields" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="torantejarat_request">
		<input type="hidden" name="request_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="request_page" value="<?php echo esc_attr( get_the_ID() ); ?>">
		<?php wp_nonce_field( 'torantejarat-request', 'torantejarat_request_nonce' ); ?>
		<p class="torantejarat-hp-field" aria-hidden="true"><label><?php esc_html_e( 'این فیلد را خالی بگذارید', 'torantejarat' ); ?> <input type="text" name="company" value="" tabindex="-1" autocomplete="off"></label></p>
		<?php if ( $product ) : ?>
			<p class="torantejarat-request-context">
				<?php echo esc_html( sprintf( __( 'در مورد: %s', 'torantejarat' ), $product->get_name() ) ); ?>
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
			</p>
		<?php endif; ?>
		<p><label for="torantejarat-req-name"><?php esc_html_e( 'نام', 'torantejarat' ); ?></label>
		<input id="torantejarat-req-name" name="name" required maxlength="80" autocomplete="name"></p>
		<p><label for="torantejarat-req-phone"><?php esc_html_e( 'موبایل', 'torantejarat' ); ?></label>
		<input id="torantejarat-req-phone" name="phone" required type="tel" inputmode="tel" dir="ltr" placeholder="09123456789" autocomplete="tel"></p>
		<?php if ( 'contact' === $type ) : ?>
			<p><label for="torantejarat-req-topic"><?php esc_html_e( 'موضوع', 'torantejarat' ); ?></label>
			<select id="torantejarat-req-topic" name="topic">
				<?php foreach ( torantejarat_request_topics() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $topic, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select></p>
		<?php elseif ( 'rent' === $type ) : ?>
			<p><label for="torantejarat-req-date"><?php esc_html_e( 'تاریخ شروع', 'torantejarat' ); ?></label>
			<input id="torantejarat-req-date" name="date" type="date" required></p>
			<p><label for="torantejarat-req-days"><?php esc_html_e( 'مدت (روز)', 'torantejarat' ); ?></label>
			<input id="torantejarat-req-days" name="days" type="number" min="1" max="365" value="1" required></p>
			<p><label for="torantejarat-req-city"><?php esc_html_e( 'شهر محل اجرا', 'torantejarat' ); ?></label>
			<input id="torantejarat-req-city" name="city" required maxlength="80" autocomplete="address-level2"></p>
		<?php elseif ( 'repairs' === $type ) : ?>
			<p><label for="torantejarat-req-model"><?php esc_html_e( 'مدل دستگاه', 'torantejarat' ); ?></label>
			<input id="torantejarat-req-model" name="model" required maxlength="120" value="<?php echo $product ? esc_attr( $product->get_name() ) : ''; ?>" placeholder="<?php esc_attr_e( 'مثلاً ویدئوبورسکوپ صنعتی ۵ اینچ', 'torantejarat' ); ?>"></p>
		<?php endif; ?>
		<p><label for="torantejarat-req-details"><?php esc_html_e( 'شرح درخواست', 'torantejarat' ); ?></label>
		<textarea id="torantejarat-req-details" name="details" rows="4" required maxlength="2000"></textarea></p>
		<p><button type="submit" class="torantejarat-btn torantejarat-btn-primary"><?php esc_html_e( 'ثبت درخواست', 'torantejarat' ); ?></button></p>
	</form>
	<?php
}

function torantejarat_handle_request() {
	$fail = static function ( $page_id, $reason = '' ) {
		$target = $page_id > 0 ? get_permalink( $page_id ) : home_url( '/' );
		$args   = array( 'sent' => '0' );
		if ( '' !== $reason ) {
			$args['reason'] = $reason;
		}
		wp_safe_redirect( add_query_arg( $args, $target ) );
		exit;
	};
	$page_id = isset( $_POST['request_page'] ) ? absint( $_POST['request_page'] ) : 0;
	if ( ! isset( $_POST['torantejarat_request_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['torantejarat_request_nonce'] ), 'torantejarat-request' ) ) {
		$fail( $page_id );
	}
	if ( ! empty( $_POST['company'] ) ) {
		$fail( $page_id );
	}
	$type = isset( $_POST['request_type'] ) ? sanitize_key( $_POST['request_type'] ) : '';
	$types = torantejarat_request_types();
	if ( ! isset( $types[ $type ] ) ) {
		$fail( $page_id );
	}
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = torantejarat_request_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '' );
	$details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';
	if ( mb_strlen( $name ) < 2 ) {
		$fail( $page_id, 'name' );
	}
	if ( '' === $phone ) {
		$fail( $page_id, 'phone' );
	}
	if ( mb_strlen( $details ) < 10 ) {
		$fail( $page_id, 'details' );
	}
	$meta = array(
		'type'    => $type,
		'name'    => $name,
		'phone'   => $phone,
		'details' => $details,
	);
	if ( 'contact' === $type ) {
		$topic = isset( $_POST['topic'] ) ? sanitize_key( $_POST['topic'] ) : 'consult';
		$topics = torantejarat_request_topics();
		$meta['topic'] = isset( $topics[ $topic ] ) ? $topic : 'consult';
	} elseif ( 'rent' === $type ) {
		$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
		$days = isset( $_POST['days'] ) ? absint( $_POST['days'] ) : 0;
		$city = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || $days < 1 || $days > 365 || '' === $city ) {
			$fail( $page_id, 'extra' );
		}
		$meta['date'] = $date;
		$meta['days'] = $days;
		$meta['city'] = $city;
	} elseif ( 'repairs' === $type ) {
		$model = isset( $_POST['model'] ) ? sanitize_text_field( wp_unslash( $_POST['model'] ) ) : '';
		if ( '' === $model ) {
			$fail( $page_id, 'extra' );
		}
		$meta['model'] = $model;
	}
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( $product_id > 0 && 'product' === get_post_type( $product_id ) ) {
		$meta['product_id'] = $product_id;
	}
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'torantejarat_request',
			'post_status' => 'private',
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- Admin title only.
			'post_title'  => sprintf( '%s — %s — %s', $types[ $type ], $name, date( 'Y-m-d H:i' ) ),
			'meta_input'  => array( '_torantejarat_request' => $meta ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		$fail( $page_id );
	}
	$lines = array(
		sprintf( __( 'نوع: %s', 'torantejarat' ), $types[ $type ] ),
		sprintf( __( 'نام: %s', 'torantejarat' ), $name ),
		sprintf( __( 'موبایل: %s', 'torantejarat' ), $phone ),
	);
	foreach ( array( 'topic', 'date', 'days', 'city', 'model', 'product_id' ) as $key ) {
		if ( isset( $meta[ $key ] ) ) {
			$value = $meta[ $key ];
			if ( 'topic' === $key ) {
				$value = torantejarat_request_topics()[ $value ];
			}
			$lines[] = $key . ': ' . $value;
		}
	}
	$lines[] = __( 'شرح:', 'torantejarat' ) . "\n" . $details;
	wp_mail(
		get_option( 'admin_email' ),
		sprintf( __( 'درخواست جدید: %s', 'torantejarat' ), $types[ $type ] ),
		implode( "\n", $lines )
	);
	$target = $page_id > 0 ? get_permalink( $page_id ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'sent', '1', $target ) );
	exit;
}

/**
 * Admin list columns: type / name / phone / date instead of bare titles.
 */
function torantejarat_request_columns( $columns ) {
	return array(
		'cb'       => $columns['cb'],
		'title'    => __( 'درخواست', 'torantejarat' ),
		'type'     => __( 'نوع', 'torantejarat' ),
		'name'     => __( 'نام', 'torantejarat' ),
		'phone'    => __( 'موبایل', 'torantejarat' ),
		'date'     => __( 'تاریخ', 'torantejarat' ),
	);
}

function torantejarat_request_column_content( $column, $post_id ) {
	$meta  = (array) get_post_meta( $post_id, '_torantejarat_request', true );
	$types = torantejarat_request_types();
	if ( 'type' === $column ) {
		echo esc_html( $types[ $meta['type'] ] ?? '—' );
	} elseif ( 'name' === $column ) {
		echo esc_html( $meta['name'] ?? '—' );
	} elseif ( 'phone' === $column && ! empty( $meta['phone'] ) ) {
		echo '<bdi><a href="tel:' . esc_attr( $meta['phone'] ) . '">' . esc_html( $meta['phone'] ) . '</a></bdi>';
	} elseif ( 'date' === $column ) {
		echo esc_html( get_the_date( '', $post_id ) );
	}
}

/* ---------- Support tickets (account panel) ----------
 * Same CPT as service requests; ticket data lives in _torantejarat_ticket.
 * Thread replies live in _torantejarat_ticket_replies (array of author/text/time).
 */

function torantejarat_ticket_subjects() {
	return array(
		'order'   => __( 'پیگیری سفارش', 'torantejarat' ),
		'product' => __( 'مشکل محصول', 'torantejarat' ),
		'invoice' => __( 'فاکتور و پرداخت', 'torantejarat' ),
		'return'  => __( 'بازگشت کالا', 'torantejarat' ),
	);
}

function torantejarat_ticket_statuses() {
	return array(
		'open'     => __( 'باز', 'torantejarat' ),
		'answered' => __( 'پاسخ داده شد', 'torantejarat' ),
		'closed'   => __( 'بسته شد', 'torantejarat' ),
	);
}

function torantejarat_user_tickets( $user_id ) {
	return get_posts(
		array(
			'post_type'   => 'torantejarat_request',
			'post_status' => 'private',
			'author'      => absint( $user_id ),
			'meta_key'    => '_torantejarat_ticket_type',
			'posts_per_page' => 20,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
}

function torantejarat_create_ticket( $user_id, $subject, $order_id, $message ) {
	$user_id = absint( $user_id );
	$subjects = torantejarat_ticket_subjects();
	if ( $user_id <= 0 || ! isset( $subjects[ $subject ] ) || mb_strlen( trim( $message ) ) < 10 ) {
		return new \WP_Error( 'invalid', __( 'اطلاعات تیکت معتبر نیست.', 'torantejarat' ) );
	}
	$order_id = absint( $order_id );
	if ( $order_id > 0 ) {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order || (int) $order->get_customer_id() !== $user_id ) {
			return new \WP_Error( 'invalid_order', __( 'سفارش معتبر نیست.', 'torantejarat' ) );
		}
	}
	$user = get_user_by( 'id', $user_id );
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'torantejarat_request',
			'post_status' => 'private',
			'post_author' => $user_id,
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- Admin title only.
			'post_title'  => sprintf( '%s — %s — %s', __( 'تیکت', 'torantejarat' ), $user ? $user->display_name : $user_id, date( 'Y-m-d H:i' ) ),
			'meta_input'  => array(
				'_torantejarat_ticket_type'   => 'ticket',
				'_torantejarat_ticket'       => array(
					'subject'  => $subject,
					'order_id' => $order_id,
					'message'  => sanitize_textarea_field( $message ),
					'status'   => 'open',
				),
				'_torantejarat_ticket_replies' => array(),
			),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}
	wp_mail(
		get_option( 'admin_email' ),
		sprintf( __( 'تیکت جدید: %s', 'torantejarat' ), $subjects[ $subject ] ),
		$subjects[ $subject ] . "\n" . $message
	);
	return $post_id;
}

function torantejarat_handle_ticket() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}
	/*
	 * Tickets are stored on WooCommerce account endpoints. If WooCommerce is not
	 * loaded (plugin update/deactivation), fail safe instead of a fatal error.
	 */
	if ( ! function_exists( 'wc_add_notice' ) || ! function_exists( 'wc_get_account_endpoint_url' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	if ( ! isset( $_POST['torantejarat_ticket_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_nonce'] ) ), 'torantejarat-ticket' ) ) {
		wc_add_notice( __( 'درخواست نامعتبر است.', 'torantejarat' ), 'error' );
		wp_safe_redirect( wc_get_account_endpoint_url( 'tickets' ) );
		exit;
	}
	$result = torantejarat_create_ticket(
		get_current_user_id(),
		isset( $_POST['subject'] ) ? sanitize_key( wp_unslash( $_POST['subject'] ) ) : '',
		isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0,
		isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : ''
	);
	if ( is_wp_error( $result ) ) {
		wc_add_notice( $result->get_error_message(), 'error' );
	} else {
		wc_add_notice( __( 'تیکت ثبت شد؛ پاسخ در کمتر از ۲۴ ساعت.', 'torantejarat' ) );
	}
	wp_safe_redirect( wc_get_account_endpoint_url( 'tickets' ) );
	exit;
}

function torantejarat_ticket_reply( $post_id, $author, $text ) {
	$post = get_post( $post_id );
	if ( ! $post || 'torantejarat_request' !== $post->post_type ) {
		return false;
	}
	$text = sanitize_textarea_field( $text );
	if ( mb_strlen( trim( $text ) ) < 2 ) {
		return false;
	}
	$replies   = (array) get_post_meta( $post_id, '_torantejarat_ticket_replies', true );
	$replies[] = array(
		'author' => in_array( $author, array( 'user', 'admin' ), true ) ? $author : 'user',
		'text'   => $text,
		'time'   => current_time( 'mysql' ),
	);
	update_post_meta( $post_id, '_torantejarat_ticket_replies', $replies );
	return true;
}

/* Admin: ticket thread + status + reply box on the request screen. */
function torantejarat_ticket_admin_metabox() {
	add_meta_box(
		'torantejarat-ticket-thread',
		__( 'گفت‌وگوی تیکت', 'torantejarat' ),
		__NAMESPACE__ . '\\torantejarat_ticket_admin_html',
		'torantejarat_request',
		'normal',
		'high'
	);
}

function torantejarat_ticket_admin_html( $post ) {
	if ( 'ticket' !== get_post_meta( $post->ID, '_torantejarat_ticket_type', true ) ) {
		echo '<p>' . esc_html__( 'این درخواست تیکت نیست.', 'torantejarat' ) . '</p>';
		return;
	}
	wp_nonce_field( 'torantejarat-ticket-admin', 'torantejarat_ticket_admin_nonce' );
	$ticket   = (array) get_post_meta( $post->ID, '_torantejarat_ticket', true );
	$statuses = torantejarat_ticket_statuses();
	$status   = isset( $ticket['status'] ) ? $ticket['status'] : 'open';
	echo '<p><label for="torantejarat-ticket-status"><strong>' . esc_html__( 'وضعیت', 'torantejarat' ) . '</strong></label><br>';
	echo '<select id="torantejarat-ticket-status" name="torantejarat_ticket_status">';
	foreach ( $statuses as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $status, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
	$replies = (array) get_post_meta( $post->ID, '_torantejarat_ticket_replies', true );
	if ( $replies ) {
		echo '<ul>';
		foreach ( $replies as $reply ) {
			$who = ( isset( $reply['author'] ) && 'admin' === $reply['author'] ) ? __( 'پشتیبانی', 'torantejarat' ) : __( 'مشتری', 'torantejarat' );
			echo '<li><strong>' . esc_html( $who ) . '</strong> <span style="color:#666">' . esc_html( $reply['time'] ?? '' ) . '</span><br>' . esc_html( $reply['text'] ?? '' ) . '</li>';
		}
		echo '</ul>';
	}
	echo '<p><label for="torantejarat-ticket-reply"><strong>' . esc_html__( 'پاسخ جدید', 'torantejarat' ) . '</strong></label><br>';
	echo '<textarea id="torantejarat-ticket-reply" name="torantejarat_ticket_reply" rows="3" style="width:100%"></textarea></p>';
}

function torantejarat_ticket_admin_save( $post_id ) {
	if ( ! isset( $_POST['torantejarat_ticket_admin_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_admin_nonce'] ) ), 'torantejarat-ticket-admin' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'ticket' !== get_post_meta( $post_id, '_torantejarat_ticket_type', true ) ) {
		return;
	}
	$ticket = (array) get_post_meta( $post_id, '_torantejarat_ticket', true );
	if ( isset( $_POST['torantejarat_ticket_status'] ) ) {
		$status = sanitize_key( wp_unslash( $_POST['torantejarat_ticket_status'] ) );
		if ( isset( torantejarat_ticket_statuses()[ $status ] ) ) {
			$ticket['status'] = $status;
			update_post_meta( $post_id, '_torantejarat_ticket', $ticket );
		}
	}
	if ( ! empty( $_POST['torantejarat_ticket_reply'] ) ) {
		torantejarat_ticket_reply( $post_id, 'admin', wp_unslash( $_POST['torantejarat_ticket_reply'] ) );
	}
}

/**
 * Details meta box with one-click reply channels (call / WhatsApp).
 */
function torantejarat_request_metabox() {
	add_meta_box(
		'torantejarat-request-details',
		__( 'جزئیات درخواست', 'torantejarat' ),
		__NAMESPACE__ . '\\torantejarat_request_metabox_html',
		'torantejarat_request',
		'normal',
		'high'
	);
}

function torantejarat_request_metabox_html( $post ) {
	$meta   = (array) get_post_meta( $post->ID, '_torantejarat_request', true );
	$topics = torantejarat_request_topics();
	$rows   = array(
		__( 'نام', 'torantejarat' )    => $meta['name'] ?? '—',
		__( 'موبایل', 'torantejarat' ) => $meta['phone'] ?? '—',
	);
	if ( ! empty( $meta['topic'] ) && isset( $topics[ $meta['topic'] ] ) ) {
		$rows[ __( 'موضوع', 'torantejarat' ) ] = $topics[ $meta['topic'] ];
	}
	foreach ( array( 'date' => __( 'تاریخ شروع', 'torantejarat' ), 'days' => __( 'مدت (روز)', 'torantejarat' ), 'city' => __( 'شهر', 'torantejarat' ), 'model' => __( 'مدل دستگاه', 'torantejarat' ) ) as $key => $label ) {
		if ( isset( $meta[ $key ] ) && '' !== $meta[ $key ] ) {
			$rows[ $label ] = $meta[ $key ];
		}
	}
	if ( ! empty( $meta['product_id'] ) ) {
		$rows[ __( 'محصول مرتبط', 'torantejarat' ) ] = get_the_title( $meta['product_id'] );
	}
	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		echo '<tr><th style="width:140px">' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
	}
	echo '<tr><th>' . esc_html__( 'شرح', 'torantejarat' ) . '</th><td>' . esc_html( $meta['details'] ?? '—' ) . '</td></tr>';
	echo '</tbody></table>';
	if ( ! empty( $meta['phone'] ) ) {
		$digits = preg_replace( '/[^0-9]/', '', $meta['phone'] );
		echo '<p>';
		echo '<a class="button button-primary" href="tel:' . esc_attr( $meta['phone'] ) . '">' . esc_html__( 'تماس', 'torantejarat' ) . '</a> ';
		echo '<a class="button" target="_blank" rel="noopener" href="https://wa.me/98' . esc_attr( ltrim( $digits, '0' ) ) . '">' . esc_html__( 'پاسخ در واتساپ', 'torantejarat' ) . '</a>';
		echo '</p>';
	}
}
