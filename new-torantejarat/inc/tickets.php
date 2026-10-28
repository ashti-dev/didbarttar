<?php
/**
 * Independent Support Ticket System for 블ković (ToranTejarat theme).
 *
 * Tables:
 *   {$wpdb->prefix}torantejarat_tickets
 *   {$wpdb->prefix}torantejarat_ticket_messages
 *
 * Tickets are for logged-in customers only. Guest contact form stays separate.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --------------------------------------------------------------
   Schema version
   -------------------------------------------------------------- */
function torantejarat_tickets_schema_version() {
	return '1.0.0';
}

/* --------------------------------------------------------------
   Statuses & Subject keys
   --------------------------------------------------------------
   Defined in inc/requests.php (loaded before this file in bootstrap.php).
   Do NOT redeclare here to avoid "Cannot redeclare" fatal.
   -------------------------------------------------------------- */

/* --------------------------------------------------------------
   Create tables on init (dbDelta)
   -------------------------------------------------------------- */
function torantejarat_tickets_maybe_create_tables() {
	global $wpdb;

	$version = get_option( 'torantejarat_tickets_db_version', '' );
	if ( $version === torantejarat_tickets_schema_version() ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset_collate = $wpdb->get_charset_collate();

	$table_tickets = $wpdb->prefix . 'torantejarat_tickets';
	$sql_tickets = "CREATE TABLE $table_tickets (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id BIGINT UNSIGNED NOT NULL,
		order_id BIGINT UNSIGNED DEFAULT NULL,
		subject_key VARCHAR(50) NOT NULL DEFAULT 'other',
		title VARCHAR(150) NOT NULL DEFAULT '',
		status VARCHAR(30) NOT NULL DEFAULT 'open',
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		last_reply_by VARCHAR(20) DEFAULT NULL,
		last_reply_at DATETIME DEFAULT NULL,
		PRIMARY KEY (id),
		KEY user_status (user_id, status),
		KEY user_created (user_id, created_at),
		KEY order_id (order_id)
	) $charset_collate;";

	$table_messages = $wpdb->prefix . 'torantejarat_ticket_messages';
	$sql_messages = "CREATE TABLE $table_messages (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		ticket_id BIGINT UNSIGNED NOT NULL,
		author_id BIGINT UNSIGNED NOT NULL,
		is_staff TINYINT(1) NOT NULL DEFAULT 0,
		body TEXT NOT NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY ticket_created (ticket_id, created_at),
		KEY author_created (author_id, created_at)
	) $charset_collate;";

	dbDelta( $sql_tickets ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- dbDelta takes raw schema.
	dbDelta( $sql_messages ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- dbDelta takes raw schema.

	update_option( 'torantejarat_tickets_db_version', torantejarat_tickets_schema_version() );
}
add_action( 'init', __NAMESPACE__ . '\\torantejarat_tickets_maybe_create_tables', 5 );

/* --------------------------------------------------------------
   Capability for admin panel
   -------------------------------------------------------------- */
function torantejarat_tickets_capability() {
	return 'manage_torantejarat_tickets';
}

function torantejarat_tickets_user_can_manage() {
	return current_user_can( torantejarat_tickets_capability() );
}

/* --------------------------------------------------------------
   Rate limit helpers (transients)
   -------------------------------------------------------------- */
function torantejarat_tickets_rate_key( $user_id ) {
	return 'torantejarat_tickets_rate_' . absint( $user_id );
}

function torantejarat_tickets_open_count_key( $user_id ) {
	return 'torantejarat_tickets_open_' . absint( $user_id );
}

/* --------------------------------------------------------------
   Ticket CRUD
   -------------------------------------------------------------- */
function torantejarat_tickets_get_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_tickets';
}

function torantejarat_tickets_messages_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_ticket_messages';
}

/**
 * Create a new ticket.
 *
 * @param int    $user_id
 * @param string $subject_key
 * @param string $title        Custom title (max 150 chars)
 * @param int    $order_id     Optional, must belong to user if > 0
 * @param string $body         First message body
 * @return int|WP_Error Ticket ID on success
 */
function torantejarat_tickets_create( $user_id, $subject_key, $title, $body, $order_id = 0 ) {
	global $wpdb;

	$user_id = absint( $user_id );
	if ( $user_id <= 0 ) {
		return new \WP_Error( 'not_logged_in', __( 'You must be logged in.', 'torantejarat' ) );
	}

	$subjects = torantejarat_ticket_subjects();
	if ( ! isset( $subjects[ $subject_key ] ) ) {
		$subject_key = 'other';
	}

	$title = sanitize_text_field( $title );
	if ( '' === $title ) {
		return new \WP_Error( 'empty_title', __( 'Title is required.', 'torantejarat' ) );
	}
	if ( mb_strlen( $title ) > 150 ) {
		return new \WP_Error( 'title_too_long', __( 'Title must be at most 150 characters.', 'torantejarat' ) );
	}

	$order_id = absint( $order_id );
	if ( $order_id > 0 ) {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order || (int) $order->get_customer_id() !== $user_id ) {
			return new \WP_Error( 'invalid_order', __( 'Order is not valid or does not belong to you.', 'torantejarat' ) );
		}
	}

	$body = sanitize_textarea_field( $body );
	if ( mb_strlen( trim( $body ) ) < 10 ) {
		return new \WP_Error( 'body_too_short', __( 'Message must be at least 10 characters.', 'torantejarat' ) );
	}

	// Rate limit: max 3 open tickets per user (admins/staff bypass)
	if ( ! torantejarat_tickets_user_can_manage() ) {
		$open_count = (int) get_transient( torantejarat_tickets_open_count_key( $user_id ) );
		if ( $open_count >= 3 ) {
			return new \WP_Error( 'rate_limit_open', __( 'شما دارای ۳ تیکت باز هستید. تا بستن یکی از آن‌ها صبر کنید.', 'torantejarat' ) );
		}
	}

	// Rate limit: min 60 seconds between submissions
	$last_submit = (int) get_transient( torantejarat_tickets_rate_key( $user_id ) );
	$now         = time();
	if ( $last_submit > 0 && ( $now - $last_submit ) < 60 ) {
		return new \WP_Error( 'rate_limit_seconds', __( 'Please wait at least 60 seconds before sending another ticket.', 'torantejarat' ) );
	}

	$wpdb->insert(
		torantejarat_tickets_get_table(),
		array(
			'user_id'     => $user_id,
			'order_id'    => $order_id > 0 ? $order_id : null,
			'subject_key' => $subject_key,
			'title'       => $title,
			'status'      => 'open',
			'created_at'  => current_time( 'mysql' ),
			'updated_at'  => current_time( 'mysql' ),
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s', '%s' )
	);

	$ticket_id = (int) $wpdb->insert_id;
	if ( $ticket_id <= 0 ) {
		return new \WP_Error( 'db_error', __( 'Could not create ticket.', 'torantejarat' ) );
	}

	// First message (customer)
	torantejarat_tickets_add_message( $ticket_id, $user_id, $body, false );

	// Update counters
	set_transient( torantejarat_tickets_rate_key( $user_id ), $now, 60 * MINUTE_IN_SECONDS );
	set_transient( torantejarat_tickets_open_count_key( $user_id ), $open_count + 1, DAY_IN_SECONDS );

	// Notify admin (asynchronous if possible)
	torantejarat_tickets_notify_admin_new( $ticket_id );

	return $ticket_id;
}

/**
 * Add a message to a ticket.
 *
 * @param int    $ticket_id
 * @param int    $author_id
 * @param bool   $is_staff
 * @param string $body
 * @return int|WP_Error
 */
function torantejarat_tickets_add_message( $ticket_id, $author_id, $body, $is_staff = false ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	$author_id = absint( $author_id );
	$is_staff  = $is_staff ? 1 : 0;
	$body      = sanitize_textarea_field( $body );

	if ( mb_strlen( trim( $body ) ) < 2 ) {
		return new \WP_Error( 'empty_body', __( 'Message is required.', 'torantejarat' ) );
	}

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		return new \WP_Error( 'not_found', __( 'Ticket not found.', 'torantejarat' ) );
	}

	$wpdb->insert(
		torantejarat_tickets_messages_table(),
		array(
			'ticket_id' => $ticket_id,
			'author_id' => $author_id,
			'is_staff'  => $is_staff,
			'body'      => $body,
			'created_at'=> current_time( 'mysql' ),
		),
		array( '%d', '%d', '%d', '%s', '%s' )
	);

	$message_id = (int) $wpdb->insert_id;

	// Update ticket timestamps and status
	$new_status = $ticket['status'];
	if ( ! $is_staff && $ticket['status'] === 'closed' ) {
		$new_status = 'reopened';
	} elseif ( ! $is_staff && $ticket['status'] === 'awaiting_support' ) {
		$new_status = 'awaiting_support';
	} elseif ( $is_staff && $ticket['status'] === 'awaiting_customer' ) {
		$new_status = 'awaiting_support';
	}

	$statuses = torantejarat_ticket_statuses();
	if ( ! isset( $statuses[ $new_status ] ) ) {
		$new_status = 'open';
	}

	$reply_by = $is_staff ? 'admin' : 'customer';

	$wpdb->update(
		torantejarat_tickets_get_table(),
		array(
			'status'       => $new_status,
			'updated_at'   => current_time( 'mysql' ),
			'last_reply_by'=> $reply_by,
			'last_reply_at'=> current_time( 'mysql' ),
		),
		array( 'id' => $ticket_id ),
		array( '%s', '%s', '%s', '%s' ),
		array( '%d' )
	);

	// Notify the other party
	if ( $is_staff ) {
		torantejarat_tickets_notify_customer_reply( $ticket_id );
	} else {
		torantejarat_tickets_notify_admin_reply( $ticket_id );
	}

	return $message_id;
}

/**
 * Get a single ticket by ID.
 *
 * @return array|null
 */
function torantejarat_tickets_get( $ticket_id ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id <= 0 ) {
		return null;
	}

	return $wpdb->get_row(
		$wpdb->prepare( "SELECT * FROM " . torantejarat_tickets_get_table() . " WHERE id = %d", $ticket_id ),
		ARRAY_A
	);
}

/**
 * Get tickets for a user.
 *
 * @return array
 */
function torantejarat_tickets_get_user_tickets( $user_id ) {
	global $wpdb;

	$user_id = absint( $user_id );
	if ( $user_id <= 0 ) {
		return array();
	}

	return $wpdb->get_results(
		$wpdb->prepare( "SELECT * FROM " . torantejarat_tickets_get_table() . " WHERE user_id = %d ORDER BY updated_at DESC, created_at DESC", $user_id ),
		ARRAY_A
	);
}

/**
 * Get all tickets (admin).
 *
 * @return array
 */
function torantejarat_tickets_get_all( $status = '', $search = '', $per_page = 20, $offset = 0 ) {
	global $wpdb;

	$sql = "SELECT * FROM " . torantejarat_tickets_get_table();
	$where = array();
	$params = array();

	if ( '' !== $status ) {
		$where[] = "status = %s";
		$params[] = $status;
	}

	if ( '' !== $search ) {
		$where[] = "(title LIKE %s OR id = %d)";
		$params[] = '%' . $wpdb->esc_like( $search ) . '%';
		$params[] = absint( $search );
	}

	if ( $where ) {
		$sql .= ' WHERE ' . implode( ' AND ', $where );
	}

	$sql .= " ORDER BY updated_at DESC, created_at DESC";
	$sql .= $wpdb->prepare( " LIMIT %d OFFSET %d", absint( $per_page ), absint( $offset ) );

	if ( $params ) {
		return $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
	}

	return $wpdb->get_results( $sql, ARRAY_A );
}

function torantejarat_tickets_count_all( $status = '', $search = '' ) {
	global $wpdb;

	$sql = "SELECT COUNT(*) FROM " . torantejarat_tickets_get_table();
	$where = array();
	$params = array();

	if ( '' !== $status ) {
		$where[] = "status = %s";
		$params[] = $status;
	}

	if ( '' !== $search ) {
		$where[] = "(title LIKE %s OR id = %d)";
		$params[] = '%' . $wpdb->esc_like( $search ) . '%';
		$params[] = absint( $search );
	}

	if ( $where ) {
		$sql .= ' WHERE ' . implode( ' AND ', $where );
	}

	if ( $params ) {
		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $params ) );
	}

	return (int) $wpdb->get_var( $sql );
}

/**
 * Get messages for a ticket.
 *
 * @return array
 */
function torantejarat_tickets_get_messages( $ticket_id ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );

	return $wpdb->get_results(
		$wpdb->prepare( "SELECT * FROM " . torantejarat_tickets_messages_table() . " WHERE ticket_id = %d ORDER BY created_at ASC", $ticket_id ),
		ARRAY_A
	);
}

/* --------------------------------------------------------------
   State machine: valid ticket status transitions
   -------------------------------------------------------------- */
function torantejarat_tickets_can_transition( $from_status, $to_status, $user_id ) {
    $is_staff = torantejarat_tickets_user_can_manage();

    $allowed = array(
        'open'              => array( 'awaiting_customer' ),
        'awaiting_customer' => array( 'answered' ),
        'answered'          => array( 'closed', 'awaiting_support' ),
        'closed'            => array( 'reopened' ),
        'reopened'          => array( 'awaiting_customer' ),
        'awaiting_support'  => array( 'answered', 'closed' ),
    );

    if ( ! isset( $allowed[ $from_status ] ) || ! in_array( $to_status, $allowed[ $from_status ], true ) ) {
        return false;
    }

    // Only staff can move tickets into waiting-for-customer states
    $staff_only = array(
        'open'    => array( 'awaiting_customer' ),
        'reopened'=> array( 'awaiting_customer' ),
    );
    if ( isset( $staff_only[ $from_status ] ) && in_array( $to_status, $staff_only[ $from_status ], true ) && ! $is_staff ) {
        return false;
    }

    // Only customers can reply (awaiting_customer -> answered) or reopen closed tickets
    $customer_only = array(
        'awaiting_customer' => array( 'answered' ),
        'closed'            => array( 'reopened' ),
    );
    if ( isset( $customer_only[ $from_status ] ) && in_array( $to_status, $customer_only[ $from_status ], true ) && $is_staff ) {
        return false;
    }

    return true;
}

/**
 * Update ticket status (admin or owner).
 *
 * @return bool|WP_Error
 */
function torantejarat_tickets_set_status( $ticket_id, $status, $user_id ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	$user_id   = absint( $user_id );
	$statuses  = torantejarat_ticket_statuses();

	if ( ! isset( $statuses[ $status ] ) ) {
		return new \WP_Error( 'invalid_status', __( 'Invalid status.', 'torantejarat' ) );
	}

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		return new \WP_Error( 'not_found', __( 'Ticket not found.', 'torantejarat' ) );
	}

	$ticket = (array) $ticket;

	// Owners can only close/reopen their own tickets; staff can change any
	if ( ! torantejarat_tickets_user_can_manage() && (int) $ticket['user_id'] !== $user_id ) {
		return new \WP_Error( 'forbidden', __( 'You cannot modify this ticket.', 'torantejarat' ) );
	}

	if ( ! torantejarat_tickets_can_transition( $ticket['status'], $status, $user_id ) ) {
		return new \WP_Error( 'invalid_transition', __( 'تغییر وضعیت از %1$s به %2$s مجاز نیست.', 'torantejarat' ) );
	}

	$wpdb->update(
		torantejarat_tickets_get_table(),
		array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $ticket_id ),
		array( '%s', '%s', '%d' )
	);

	// Update open ticket counter when closing/reopening
	$ticket_user_id = (int) $ticket['user_id'];
	if ( $status === 'closed' ) {
		$open_count = (int) get_transient( torantejarat_tickets_open_count_key( $ticket_user_id ) );
		set_transient( torantejarat_tickets_open_count_key( $ticket_user_id ), max( 0, $open_count - 1 ), DAY_IN_SECONDS );
	} elseif ( $status === 'open' || $status === 'reopened' ) {
		$open_count = (int) get_transient( torantejarat_tickets_open_count_key( $ticket_user_id ) );
		set_transient( torantejarat_tickets_open_count_key( $ticket_user_id ), $open_count + 1, DAY_IN_SECONDS );
	}

	return true;
}

/* --------------------------------------------------------------
   Notifications
   -------------------------------------------------------------- */
function torantejarat_tickets_notify_admin_new( $ticket_id ) {
	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		return;
	}
	$ticket = (array) $ticket;

	$user = get_user_by( 'id', $ticket['user_id'] );
	$subject = sprintf( __( 'New ticket: %s', 'torantejarat' ), esc_html( $ticket['title'] ) );
	$message = sprintf(
		__( 'Ticket #%d\nTitle: %s\nCustomer: %s (%s)\nSubject: %s\nStatus: open\n\n---\nFirst message stored in database.', 'torantejarat' ),
		$ticket_id,
		esc_html( $ticket['title'] ),
		esc_html( $user ? $user->display_name : $ticket['user_id'] ),
		esc_html( $user ? $user->user_email : '' ),
		esc_html( torantejarat_ticket_subjects()[ $ticket['subject_key'] ] ?? $ticket['subject_key'] )
	);

	if ( wp_schedule_single_event( time(), 'torantejarat_tickets_send_admin_email', array( 'admin', get_option( 'admin_email' ), $subject, $message ) ) === false ) {
		// Fallback direct send if scheduling failed
		wp_mail( get_option( 'admin_email' ), $subject, $message );
	}
}

function torantejarat_tickets_notify_admin_reply( $ticket_id ) {
	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		return;
	}
	$ticket = (array) $ticket;

	$subject = sprintf( __( 'Customer replied to ticket #%d', 'torantejarat' ), $ticket_id );
	$message = sprintf(
		__( 'Ticket #%d (%s) has a new customer reply.\nStatus: %s\n\nReply stored in database.', 'torantejarat' ),
		$ticket_id,
		esc_html( $ticket['title'] ),
		esc_html( $ticket['status'] )
	);

	if ( wp_schedule_single_event( time(), 'torantejarat_tickets_send_admin_email', array( 'admin_reply', get_option( 'admin_email' ), $subject, $message ) ) === false ) {
		wp_mail( get_option( 'admin_email' ), $subject, $message );
	}
}

function torantejarat_tickets_notify_customer_reply( $ticket_id ) {
	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		return;
	}
	$ticket = (array) $ticket;

	$user = get_user_by( 'id', $ticket['user_id'] );
	if ( ! $user ) {
		return;
	}

	$subject = sprintf( __( 'Support replied to your ticket #%d', 'torantejarat' ), $ticket_id );
	$message = sprintf(
		__( 'Hello %s,\n\nOur support team has replied to your ticket #%d (%s).\nPlease log in to your account to view the response.', 'torantejarat' ),
		esc_html( $user->display_name ),
		$ticket_id,
		esc_html( $ticket['title'] )
	);

	if ( wp_schedule_single_event( time(), 'torantejarat_tickets_send_customer_email', array( 'customer', $user->user_email, $subject, $message ) ) === false ) {
		wp_mail( $user->user_email, $subject, $message );
	}
}

/* --------------------------------------------------------------
   Send scheduled emails (wired to cron in bootstrap)
   -------------------------------------------------------------- */
function torantejarat_tickets_send_email( $type, $to, $subject, $message ) {
	if ( 'admin' === $type || 'admin_reply' === $type ) {
		$to = get_option( 'admin_email' );
	}
	wp_mail( $to, $subject, $message );
}

add_action( 'torantejarat_tickets_send_admin_email', __NAMESPACE__ . '\\torantejarat_tickets_send_email', 10, 4 );
add_action( 'torantejarat_tickets_send_customer_email', __NAMESPACE__ . '\\torantejarat_tickets_send_email', 10, 4 );

/* --------------------------------------------------------------
   Frontend handler: create ticket from /my-account/tickets/
   -------------------------------------------------------------- */
function torantejarat_tickets_handle_create_frontend() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}

	if ( ! isset( $_POST['torantejarat_ticket_create_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_create_nonce'] ) ), 'torantejarat-ticket-create' ) ) {
		wp_safe_redirect( wc_get_account_endpoint_url( 'tickets' ) . '?ticket_error=nonce' );
		exit;
	}

	$subject_key = isset( $_POST['subject_key'] ) ? sanitize_key( wp_unslash( $_POST['subject_key'] ) ) : 'other';
	$title       = isset( $_POST['title'] ) ? wp_unslash( $_POST['title'] ) : '';
	$order_id    = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
	$body        = isset( $_POST['body'] ) ? wp_unslash( $_POST['body'] ) : '';

	$result = torantejarat_tickets_create( get_current_user_id(), $subject_key, $title, $body, $order_id );

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', $result->get_error_code(), wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'ticket_sent', '1', wc_get_account_endpoint_url( 'tickets' ) ) );
	exit;
}

/* --------------------------------------------------------------
   Frontend handler: reply to ticket from /my-account/tickets/
   -------------------------------------------------------------- */
function torantejarat_tickets_handle_reply_frontend() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}

	if ( ! isset( $_POST['torantejarat_ticket_reply_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_reply_nonce'] ) ), 'torantejarat-ticket-reply' ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'nonce', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$body      = isset( $_POST['body'] ) ? wp_unslash( $_POST['body'] ) : '';

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket || (int) $ticket['user_id'] !== get_current_user_id() ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'not_found', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	$result = torantejarat_tickets_add_message( $ticket_id, get_current_user_id(), $body, false );

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( array( 'view_ticket' => $ticket_id, 'ticket_error' => $result->get_error_code() ), wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( array( 'view_ticket' => $ticket_id, 'ticket_sent' => '1' ), wc_get_account_endpoint_url( 'tickets' ) ) );
	exit;
}

/* --------------------------------------------------------------
   Frontend handler: close ticket from /my-account/tickets/
   -------------------------------------------------------------- */
function torantejarat_tickets_handle_close_frontend() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}

	if ( ! isset( $_POST['torantejarat_ticket_close_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_close_nonce'] ) ), 'torantejarat-ticket-close' ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'nonce', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket || (int) $ticket['user_id'] !== get_current_user_id() ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'not_found', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	$result = torantejarat_tickets_set_status( $ticket_id, 'closed', get_current_user_id() );

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', $result->get_error_code(), wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'ticket_sent', '1', wc_get_account_endpoint_url( 'tickets' ) ) );
	exit;
}
function torantejarat_tickets_setup_capability() {
	$role = get_role( 'administrator' );
	if ( $role ) {
		$role->add_cap( torantejarat_tickets_capability() );
	}
}

/* --------------------------------------------------------------
   Frontend: delete ticket (customer owns it)
   -------------------------------------------------------------- */
function torantejarat_tickets_handle_delete_frontend() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}

	if ( ! isset( $_POST['torantejarat_ticket_delete_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_delete_nonce'] ) ), 'torantejarat-ticket-delete' ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'nonce', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket || (int) $ticket['user_id'] !== get_current_user_id() ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'not_found', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	// فقط تیکت‌های بسته (closed) توسط کاربر قابل حذف هستند
	if ( $ticket['status'] !== 'closed' ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'not_closed', wc_get_account_endpoint_url( 'tickets' ) ) );
		exit;
	}

	global $wpdb;
	// Delete messages first
	$wpdb->delete(
		torantejarat_tickets_messages_table(),
		array( 'ticket_id' => $ticket_id ),
		array( '%d' )
	);
	// Delete ticket
	$wpdb->delete(
		torantejarat_tickets_get_table(),
		array( 'id' => $ticket_id ),
		array( '%d' )
	);

	// Decrease open count if ticket was open
	$ticket = (array) $ticket;
	if ( in_array( $ticket['status'], array( 'open', 'awaiting_customer', 'awaiting_support' ), true ) ) {
		$open_count = (int) get_transient( torantejarat_tickets_open_count_key( get_current_user_id() ) );
		set_transient( torantejarat_tickets_open_count_key( get_current_user_id() ), max( 0, $open_count - 1 ), DAY_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'ticket_deleted', '1', wc_get_account_endpoint_url( 'tickets' ) ) );
	exit;
}


/* --------------------------------------------------------------
   Admin: delete selected tickets (bulk, destructive)
   -------------------------------------------------------------- */
function torantejarat_tickets_handle_admin_delete_selected() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'شما دسترسی به مدیریت تیکت‌ها را ندارید.', 'torantejarat' ) );
	}

	if ( ! isset( $_POST['torantejarat_ticket_delete_selected_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_delete_selected_nonce'] ) ), 'torantejarat-ticket-delete-selected' ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'nonce', admin_url( 'admin.php?page=torantejarat-tickets' ) ) );
		exit;
	}

	if ( ! isset( $_POST['ticket_ids'] ) || ! is_array( $_POST['ticket_ids'] ) ) {
		wp_safe_redirect( add_query_arg( 'ticket_error', 'no_selection', admin_url( 'admin.php?page=torantejarat-tickets' ) ) );
		exit;
	}

	global $wpdb;
	$ticket_table  = torantejarat_tickets_get_table();
	$message_table = torantejarat_tickets_messages_table();

	$deleted_count = 0;
	$skipped_count = 0;

	foreach ( $_POST['ticket_ids'] as $ticket_id ) {
		$ticket_id = absint( $ticket_id );
		if ( $ticket_id <= 0 ) {
			continue;
		}

		// فقط تیکت‌های بسته (closed) قابل حذف هستند
		$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT status FROM $ticket_table WHERE id = %d", $ticket_id ), ARRAY_A );
		if ( ! $ticket || $ticket['status'] !== 'closed' ) {
			$skipped_count++;
			continue;
		}

		// حذف پیام‌های تیکت ابتدا
		$wpdb->delete(
			$message_table,
			array( 'ticket_id' => $ticket_id ),
			array( '%d' )
		);

		// حذف تیکت
		$wpdb->delete(
			$ticket_table,
			array( 'id' => $ticket_id ),
			array( '%d' )
		);

		$deleted_count++;
	}

	$redirect_url = add_query_arg( array(
		'ticket_deleted_selected' => $deleted_count,
		'ticket_skipped'          => $skipped_count,
	), admin_url( 'admin.php?page=torantejarat-tickets' ) );

	wp_safe_redirect( $redirect_url );
	exit;
}


add_action( 'init', __NAMESPACE__ . '\\torantejarat_tickets_setup_capability', 10 );

/* --------------------------------------------------------------
   Admin menu (phase 4)
   -------------------------------------------------------------- */
/* --------------------------------------------------------------
   Admin menu
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_menu() {
	add_menu_page(
		__( 'پشتیبانی تیکت', 'torantejarat' ),
		__( 'پشتیبانی تیکت', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_page',
		'dashicons-ticket',
	55
	);

	add_submenu_page(
		'torantejarat-tickets',
		__( 'همه تیکت‌ها', 'torantejarat' ),
		__( 'همه تیکت‌ها', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_page'
	);

	add_submenu_page(
		'torantejarat-tickets',
		__( 'ثبت تیکت جدید', 'torantejarat' ),
		__( 'ثبت تیکت جدید', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets-add',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_add_page'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\torantejarat_tickets_admin_menu', 30 );

/* --------------------------------------------------------------
   Admin page: list tickets
   -------------------------------------------------------------- */
/* --------------------------------------------------------------
   Admin page: list tickets (AJAX load more)
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_page() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'شما دسترسی به مدیریت تیکت‌ها را ندارید.', 'torantejarat' ) );
	}

	// If viewing details, redirect to standalone details page
	if ( isset( $_GET['view'] ) && 'details' === $_GET['view'] && ! empty( $_GET['id'] ) ) {
		$ticket_id = absint( $_GET['id'] );
		torantejarat_tickets_admin_details_page( $ticket_id );
		return;
	}

	$status  = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
	$search  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$page    = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$per_page = 20;
	$offset  = ( $page - 1 ) * $per_page;

	$statuses = torantejarat_ticket_statuses();
	$tickets  = torantejarat_tickets_get_all( $status, $search, $per_page, $offset );
	$total    = torantejarat_tickets_count_all( $status, $search );
	$total_pages = ceil( $total / $per_page );

	?>
	<style>
	.torantejarat-tickets-admin {
		direction: rtl;
		text-align: right;
		font-family: Vazirmatn, Tahoma, Arial, sans-serif;
		max-width: 1400px;
		margin: 0 auto;
		padding: 20px;
	}
	.torantejarat-tickets-admin .wrap > h1,
	.torantejarat-tickets-admin h1 {
		text-align: right;
		font-size: 28px;
		color: #172325;
		margin-bottom: 20px;
	}
	.torantejarat-tickets-admin .wp-list-table {
		border-collapse: separate;
		border-spacing: 0;
		width: 100%;
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
		overflow: hidden;
		margin-bottom: 24px;
	}
	.torantejarat-tickets-admin .wp-list-table th,
	.torantejarat-tickets-admin .wp-list-table td {
		text-align: right;
		vertical-align: middle;
		padding: 14px 16px;
		border-bottom: 1px solid #f2f3ef;
	}
	.torantejarat-tickets-admin .wp-list-table th {
		background: #fafaf7;
		font-weight: 800;
		color: #172325;
		font-size: 13px;
		text-transform: uppercase;
		letter-spacing: 0.05em;
	}
	.torantejarat-tickets-admin .wp-list-table tbody tr:hover {
		background: #f9fbf6;
	}
	.torantejarat-tickets-admin .ticket-id {
		font-family: monospace;
		font-weight: 800;
		color: #36567a;
	}
	.torantejarat-tickets-admin .ticket-title {
		font-weight: 800;
		color: #172325;
		font-size: 15px;
	}
	.torantejarat-tickets-admin .ticket-title a {
		color: #172325;
		text-decoration: none;
	}
	.torantejarat-tickets-admin .ticket-title a:hover {
		color: #f77b3e;
	}
	.torantejarat-tickets-admin .ticket-customer {
		color: #606c68;
		font-size: 14px;
	}
	.torantejarat-tickets-admin .ticket-order {
		color: #606c68;
		font-size: 14px;
	}
	.torantejarat-tickets-admin .ticket-status {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 12px;
		font-size: 11px;
		font-weight: 800;
		text-transform: uppercase;
		letter-spacing: 0.03em;
	}
	.torantejarat-tickets-admin .status-open { background: #fef3f2; color: #b42318; }
	.torantejarat-tickets-admin .status-closed { background: #eef4fa; color: #36567a; }
	.torantejarat-tickets-admin .status-answered { background: #eef9f2; color: #247348; }
	.torantejarat-tickets-admin .status-awaiting_customer,
	.torantejarat-tickets-admin .status-awaiting_support { background: #fffbeb; color: #b45309; }
	.torantejarat-tickets-admin .status-reopened { background: #f3e8ff; color: #7c3aed; }
	.torantejarat-tickets-admin .ticket-actions a {
		display: inline-block;
		margin-left: 8px;
		padding: 6px 12px;
		background: #f4f5f1;
		color: #172325;
		text-decoration: none;
		border-radius: 6px;
		font-size: 12px;
		font-weight: 700;
	}
	.torantejarat-tickets-admin .ticket-actions a:hover {
		background: #e9ece6;
		color: #f77b3e;
	}
	.torantejarat-tickets-admin .ticket-actions .view-btn {
		background: #eef4fa;
		color: #36567a;
	}
	.torantejarat-tickets-admin .ticket-actions .view-btn:hover {
		background: #dcebf5;
		color: #2c5282;
	}
	.torantejarat-tickets-admin .filter-form {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
		padding: 20px;
		margin-bottom: 24px;
		display: flex;
		flex-wrap: wrap;
		gap: 16px;
		align-items: center;
	}
	.torantejarat-tickets-admin .filter-form label {
		display: block;
		font-weight: 800;
		font-size: 13px;
		color: #172325;
		margin-bottom: 6px;
	}
	.torantejarat-tickets-admin .filter-form select,
	.torantejarat-tickets-admin .filter-form input[type="search"] {
		direction: rtl;
		padding: 8px 12px;
		border: 1px solid #d5ddd5;
		border-radius: 8px;
		font-size: 13px;
		font-family: inherit;
	}
	.torantejarat-tickets-admin .filter-form button {
		margin-top: 8px;
	}
	.torantejarat-tickets-admin .no-tickets {
		text-align: center;
		padding: 60px 20px;
		color: #606c68;
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
	}
	.torantejarat-tickets-admin .no-tickets p {
		font-size: 16px;
		font-weight: 700;
		color: #172325;
		margin: 0;
	}
	.torantejarat-tickets-admin .updated,
	.torantejarat-tickets-admin .error {
		text-align: right;
		direction: rtl;
		border-radius: 10px;
		padding: 12px 16px;
		margin-bottom: 20px;
	}
	.torantejarat-tickets-admin .button {
		float: right;
	}
	.torantejarat-tickets-admin .button-danger {
		background: #dc2626;
		color: #fff;
		border: none;
	}
	.torantejarat-tickets-admin .button-danger:hover {
		background: #b91c1c;
	}
	.torantejarat-tickets-admin .delete-selected-form {
	.torantejarat-tickets-admin .delete-ticket-btn {
\tdisplay: inline-block;
		margin-bottom: 20px;
		background: #dc2626;
		color: #fff;
		border: none;
		padding: 4px 8px;
		font-size: 11px;
		border-radius: 4px;
		text-decoration: none;
	}
	.torantejarat-tickets-admin .delete-ticket-btn:hover {
		background: #b91c1c;
	}
	.torantejarat-tickets-admin th input[type="checkbox"] {
		margin: 0;
	}
		display: inline-block;
		margin-bottom: 20px;
	}
	.torantejarat-tickets-admin label {
		display: block;
		text-align: right;
	}
	.torantejarat-tickets-admin .load-more-wrap {
		text-align: center;
		margin-top: 24px;
	}
	.torantejarat-tickets-admin .load-more-btn {
		background: #f77b3e;
		color: #fff;
		border: none;
		padding: 12px 30px;
		border-radius: 8px;
		font-weight: 700;
		font-family: inherit;
		font-size: 14px;
		cursor: pointer;
	}
	.torantejarat-tickets-admin .load-more-btn:hover {
		background: #e96a2b;
	}
	.torantejarat-tickets-admin .load-more-btn:disabled {
		background: #ccc;
		cursor: not-allowed;
	}
	.torantejarat-tickets-admin .load-more-spinner {
		display: none;
		width: 16px;
		height: 16px;
		border: 2px solid #fff;
		border-top-color: transparent;
		border-radius: 50%;
		animation: spin 1s linear infinite;
		margin-left: 8px;
		vertical-align: middle;
	}
	@keyframes spin {
		to { transform: rotate(360deg); }
	}
	</style>
<div class="wrap torantejarat-tickets-admin">
	<h1><?php esc_html_e( 'پشتیبانی تیکت', 'torantejarat' ); ?></h1>

	<?php if ( ! empty( $_GET["ticket_deleted_selected"] ) ) : ?>
<div class="updated notice is-dismissible"><p><?php echo sprintf( _n( "%d تیکت حذف شد.", "%d تیکت حذف شد.", (int) $_GET["ticket_deleted_selected"], "torantejarat" ), (int) $_GET["ticket_deleted_selected"] ); if ( ! empty( $_GET["ticket_skipped"] ) ) echo " " . sprintf( _n( "%d تیکت قابل حذف نبود و نادیده گرفت.", "%d تیکت قابل حذف نبود و نادیده گرفت.", (int) $_GET["ticket_skipped"], "torantejarat" ), (int) $_GET["ticket_skipped"] ); ?></p></div>
<?php endif; ?>
<?php if ( ! empty( $_GET['ticket_sent'] ) ) : ?>
	<div class="updated notice is-dismissible"><p><?php esc_html_e( 'تیکت با موفقیت ثبت شد.', 'torantejarat' ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $_GET['ticket_error'] ) ) : ?>
	<div class="error notice is-dismissible"><p><?php echo esc_html( $_GET['ticket_error'] === 'forbidden' ? __( 'شما دسترسی به این تیکت را ندارید.', 'torantejarat' ) : __( 'تیکت یافت نشد.', 'torantejarat' ) ); ?></p></div>
	<form method="post" action="<?php echo esc_url( admin_url( "admin-post.php" ) ); ?>" class="delete-selected-form" onsubmit="return confirm( 'آیا مطمئن هستید که تیکت‌های انتخاب شده به طور دائم حذف شوند؟ این عمل غیرقابل بازگشت است.' );">
	<?php wp_nonce_field("torantejarat-ticket-delete-selected", "torantejarat_ticket_delete_selected_nonce" ); ?>
	<button type="submit" class="button button-danger delete-selected-btn"><?php esc_html_e( "حذف تیکت‌های انتخاب شده", "torantejarat" ); ?></button>
<?php endif; ?>
<form method="get" action="" class="filter-form">
		<input type="hidden" name="page" value="torantejarat-tickets">
		<div style="min-width: 150px;">
			<label for="status"><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></label>
			<select name="status" id="status">
				<option value=""><?php esc_html_e( 'همه وضعیت‌ها', 'torantejarat' ); ?></option>
				<?php foreach ( $statuses as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div style="min-width: 250px; flex: 1;">
			<label for="s"><?php esc_html_e( 'جستجو در عنوان یا شماره تیکت', 'torantejarat' ); ?></label>
			<input type="search" name="s" id="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'جستجو...', 'torantejarat' ); ?>">
		</div>
		<button type="submit" class="button button-primary"><?php esc_html_e( 'فیلتر کردن', 'torantejarat' ); ?></button>
	</form>

	<?php if ( $tickets ) : ?>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-tickets" onclick="torantejaratToggleAllTickets(this)"></th>
				<th style="width: 60px;"><?php esc_html_e( 'شناسه', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'عنوان', 'torantejarat' ); ?></th>
				<th style="width: 160px;"><?php esc_html_e( 'مشتری', 'torantejarat' ); ?></th>
				<th style="width: 100px;"><?php esc_html_e( 'سفارش', 'torantejarat' ); ?></th>
				<th style="width: 130px;"><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></th>
				<th style="width: 150px;"><?php esc_html_e( 'آخرین به‌روزرسانی', 'torantejarat' ); ?></th>
				<th style="width: 100px;"><?php esc_html_e( 'عملیات', 'torantejarat' ); ?></th>
			</tr>
		</thead>
		<tbody id="torantejarat-tickets-list">
			<?php foreach ( $tickets as $ticket ) : ?>
				<?php
				$user = get_user_by( 'id', $ticket['user_id'] );
				$customer_name = $user ? esc_html( $user->display_name ) : sprintf( __( 'کاربر #%d', 'torantejarat' ), $ticket['user_id'] );
				$order_label = $ticket['order_id'] ? sprintf( '#%d', $ticket['order_id'] ) : __( '—', 'torantejarat' );
				$status_label = $statuses[ $ticket['status'] ] ?? $ticket['status'];
				?>
				<tr>
					<?php if ( $ticket["status"] === "closed" ) : ?><td style="text-align: center;"><input type="checkbox" name="ticket_ids[]" value="<?php echo esc_attr( $ticket["id"] ); ?>"></td><?php else : ?><td style="text-align: center;"><span class="dashicons dashicons-lock" title="<?php esc_attr_e( "فقط تیکت‌های بسته قابل حذف هستند", "torantejarat" ); ?>"></span></td><?php endif; ?><td class="ticket-id"><?php echo esc_html( $ticket['id'] ); ?></td>
					<td class="ticket-title"><a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket['id'] ) ); ?>"><?php echo esc_html( $ticket['title'] ); ?></a></td>
					<td class="ticket-customer"><?php echo esc_html( $customer_name ); ?></td>
					<td class="ticket-order"><?php echo esc_html( $order_label ); ?></td>
					<td><span class="ticket-status status-<?php echo esc_attr( $ticket['status'] ); ?>"><?php echo esc_html( $status_label ); ?></span></td>
					<td><?php echo esc_html( mysql2date( 'Y/m/d H:i', $ticket['updated_at'] ) ); ?></td>
					<td class="ticket-actions"><a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket['id'] ) ); ?>" class="view-btn"><?php esc_html_e( 'مشاهده', 'torantejarat' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="load-more-wrap">
		<?php if ( $page < $total_pages ) : ?>
		<button class="load-more-btn" id="torantejarat-load-more" data-page="<?php echo esc_attr( $page + 1 ); ?>" data-total="<?php echo esc_attr( $total_pages ); ?>" data-status="<?php echo esc_attr( $status ); ?>" data-search="<?php echo esc_attr( $search ); ?>">
			<span class="load-more-text"><?php esc_html_e( 'بارگذاری المزيد', 'torantejarat' ); ?></span>
			<span class="load-more-spinner"></span>
		</button>
		<?php else : ?>
		<p style="color:#606c68; font-size:13px;"><?php esc_html_e( 'هیچ تیکت دیگر نیست.', 'torantejarat' ); ?></p>
		<?php endif; ?>
	</div>
	<?php else : ?>
	<div class="no-tickets">
		<p><?php esc_html_e( 'تیکتی یافت نشد.', 'torantejarat' ); ?></p>
	</div>
	<?php endif; ?>
</div>
	<?php

	// AJAX load more script
	?><script>
	(function() {
		var btn = document.getElementById( 'torantejarat-load-more' );
		if ( ! btn ) return;
		var tbody = document.getElementById( 'torantejarat-tickets-list' );
		var spinner = btn.querySelector( '.load-more-spinner' );
		var text = btn.querySelector( '.load-more-text' );
		var currentPage = parseInt( btn.getAttribute( 'data-page' ) );
		var totalPages = parseInt( btn.getAttribute( 'data-total' ) );
		var status = btn.getAttribute( 'data-status' );
		var search = btn.getAttribute( 'data-search' );
		var loading = false;

		btn.addEventListener( 'click', function() {
			if ( loading || currentPage > totalPages ) return;
			loading = true;
			btn.disabled = true;
			spinner.style.display = 'inline-block';
			text.textContent = '<?php esc_html_e( 'بارگذاری...', 'torantejarat' ); ?>';

			var formData = new FormData();
			formData.append( 'action', 'torantejarat_tickets_load_more' );
			formData.append( 'status', status );
			formData.append( 'search', search );
			formData.append( 'page', currentPage );

			fetch( ajaxurl, {
				method: 'POST',
				body: formData
			} )
			.then( function( response ) { return response.json(); } )
			.then( function( data ) {
				if ( data.success && data.data.html ) {
					tbody.insertAdjacentHTML( 'beforeend', data.data.html );
					currentPage++;
					btn.setAttribute( 'data-page', currentPage );
					if ( currentPage > totalPages ) {
						btn.style.display = 'none';
					}
				}
				loading = false;
				btn.disabled = false;
				spinner.style.display = 'none';
				text.textContent = '<?php esc_html_e( 'بارگذاری المزيد', 'torantejarat' ); ?>';
			} )
			.catch( function() {
				loading = false;
				btn.disabled = false;
				spinner.style.display = 'none';
				text.textContent = '<?php esc_html_e( 'بارگذاری المزيد', 'torantejarat' ); ?>';
			} );
		} );
	})();

		// Select all / deselect all checkboxes
		function torantejaratToggleAllTickets( checkbox ) {
			var isChecked = checkbox.checked;
			var checkboxes = document.querySelectorAll( 'input[name="ticket_ids[]"]' );
			for ( var i = 0; i < checkboxes.length; i++ ) {
				checkboxes[ i ].checked = isChecked;
			}
		}
	</script>
	<?php
}

/* --------------------------------------------------------------
   Admin page: ticket details + reply
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_details_page( $ticket_id ) {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'شما دسترسی به مدیریت تیکت‌ها را ندارید.', 'torantejarat' ) );
	}

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		wp_die( __( 'تیکت یافت نشد.', 'torantejarat' ) );
	}
	$ticket = (array) $ticket;

	$messages = torantejarat_tickets_get_messages( $ticket_id );
	$statuses = torantejarat_ticket_statuses();

	$user = get_user_by( 'id', $ticket['user_id'] );
	$order = $ticket['order_id'] ? wc_get_order( $ticket['order_id'] ) : null;

	// Handle reply (processed FIRST so both reply and status change can be saved)
	$reply_sent = false;
	if ( isset( $_POST['torantejarat_ticket_reply_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_reply_nonce'] ) ), 'torantejarat-ticket-reply' ) ) {
		$body = isset( $_POST['ticket_reply'] ) ? wp_unslash( $_POST['ticket_reply'] ) : '';
		if ( trim( $body ) !== '' ) {
			$result = torantejarat_tickets_add_message( $ticket_id, get_current_user_id(), $body, true );
			$reply_sent = ! is_wp_error( $result );
		}
	}

	// Handle status change
	$status_updated = false;
	if ( isset( $_POST['torantejarat_ticket_status_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_status_nonce'] ) ), 'torantejarat-ticket-status' ) ) {
		if ( isset( $_POST['new_status'] ) ) {
			$result = torantejarat_tickets_set_status( $ticket_id, sanitize_key( wp_unslash( $_POST['new_status'] ) ), get_current_user_id() );
			$status_updated = ! is_wp_error( $result );
		}
	}

	$redirect_url = admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket_id );
	$redirect_args = array();
	if ( $reply_sent ) {
		$redirect_args['reply_sent'] = '1';
	}
	if ( $status_updated ) {
		$redirect_args['status_updated'] = '1';
	}
	if ( ! empty( $redirect_args ) ) {
		$redirect_url = add_query_arg( $redirect_args, $redirect_url );
	}
	wp_safe_redirect( $redirect_url );
	exit;
		wp_safe_redirect( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket_id ) );
		exit;
	}

	?>
	<style>
	.torantejarat-ticket-details {
		direction: rtl;
		text-align: right;
		font-family: Vazirmatn, Tahoma, Arial, sans-serif;
		max-width: 1000px;
		margin: 0 auto;
		padding: 24px;
	}
	.torantejarat-ticket-details .wrap > h1,
	.torantejarat-ticket-details h1 {
		text-align: right;
		font-size: 28px;
		color: #172325;
		margin-bottom: 24px;
	}
	.torantejarat-ticket-details .ticket-header {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 16px;
		padding: 24px;
		margin-bottom: 24px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.04);
	}
	.torantejarat-ticket-details .ticket-header h2 {
		font-size: 20px;
		color: #172325;
		margin: 0 0 4px 0;
		font-weight: 800;
	}
	.torantejarat-ticket-details .ticket-header .ticket-meta {
		color: #606c68;
		font-size: 13px;
	}
	.torantejarat-ticket-details .info-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
		gap: 16px;
		margin-bottom: 24px;
	}
	.torantejarat-ticket-details .info-card {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
		padding: 16px;
	}
	.torantejarat-ticket-details .info-card .label {
		display: block;
		font-size: 11px;
		font-weight: 800;
		color: #8a9590;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		margin-bottom: 6px;
	}
	.torantejarat-ticket-details .info-card .value {
		font-size: 15px;
		color: #172325;
		font-weight: 700;
	}
	.torantejarat-ticket-details .ticket-status-badge {
		display: inline-block;
		padding: 6px 14px;
		border-radius: 20px;
		font-size: 11px;
		font-weight: 800;
		text-transform: uppercase;
		letter-spacing: 0.03em;
	}
	.torantejarat-ticket-details .status-open { background: #fef3f2; color: #b42318; }
	.torantejarat-ticket-details .status-closed { background: #eef4fa; color: #36567a; }
	.torantejarat-ticket-details .status-answered { background: #eef9f2; color: #247348; }
	.torantejarat-ticket-details .status-awaiting_customer,
	.torantejarat-ticket-details .status-awaiting_support { background: #fffbeb; color: #b45309; }
	.torantejarat-ticket-details .status-reopened { background: #f3e8ff; color: #7c3aed; }
	.torantejarat-ticket-details h2 {
		font-size: 18px;
		color: #172325;
		margin: 28px 0 16px 0;
		font-weight: 800;
		border-bottom: 2px solid #f2f3ef;
		padding-bottom: 8px;
	}
	.torantejarat-ticket-details .conversation {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
		padding: 20px;
		margin-bottom: 24px;
	}
	.torantejarat-ticket-details .ticket-message {
		margin-bottom: 16px;
		padding: 14px 18px;
		border-radius: 12px;
		line-height: 1.6;
	}
	.torantejarat-ticket-details .ticket-message.customer {
		background: #eef4fa;
		border-left: 4px solid #36567a;
	}
	.torantejarat-ticket-details .ticket-message.staff {
		background: #eef9f2;
		border-left: 4px solid #247348;
	}
	.torantejarat-ticket-details .ticket-message strong {
		display: block;
		font-size: 11px;
		font-weight: 800;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		margin-bottom: 6px;
	}
	.torantejarat-ticket-details .ticket-message.staff strong { color: #247348; }
	.torantejarat-ticket-details .ticket-message.customer strong { color: #36567a; }
	.torantejarat-ticket-details .ticket-time {
		font-size: 11px;
		color: #8a9590;
		margin-top: 4px;
		display: block;
	}
	.torantejarat-ticket-details .reply-form {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 12px;
		padding: 24px;
	}
	.torantejarat-ticket-details .reply-form table.form-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
	}
	.torantejarat-ticket-details .reply-form th,
	.torantejarat-ticket-details .reply-form td {
		text-align: right;
		padding: 14px 0;
		border-bottom: 1px solid #f2f3ef;
	}
	.torantejarat-ticket-details .reply-form th {
		font-weight: 800;
		color: #172325;
		font-size: 14px;
		width: 130px;
	}
	.torantejarat-ticket-details .reply-form td select {
		width: 100%;
		max-width: 220px;
		padding: 8px 12px;
		border: 1px solid #d5ddd5;
		border-radius: 8px;
		font-family: inherit;
		font-size: 13px;
		direction: rtl;
	}
	.torantejarat-ticket-details .reply-form textarea {
		width: 100%;
		padding: 12px 16px;
		border: 1px solid #d5ddd5;
		border-radius: 8px;
		font-family: inherit;
		font-size: 14px;
		min-height: 120px;
		direction: rtl;
		resize: vertical;
	}
	.torantejarat-ticket-details .reply-form .button-primary {
		float: right;
		padding: 10px 24px;
		font-size: 14px;
		font-weight: 700;
	}
	.torantejarat-ticket-details .updated,
	.torantejarat-ticket-details .error {
		border-radius: 10px;
		padding: 12px 16px;
		margin-bottom: 20px;
		text-align: right;
		direction: rtl;
	}
	</style>
<div class="wrap torantejarat-ticket-details">
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-tickets' ) ); ?>" class="button button-secondary" style="float: left; margin-bottom: 16px;"><?php esc_html_e( 'بازگشت به لیست', 'torantejarat' ); ?></a>
	<h1>تیکت #<?php echo esc_html( $ticket_id ); ?></h1>
	
	<?php if ( ! empty( $_GET['reply_sent'] ) ) : ?>
	<div class="updated notice is-dismissible"><p><?php esc_html_e( 'پاسخ ارسال شد.', 'torantejarat' ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $_GET['status_updated'] ) ) : ?>
	<div class="updated notice is-dismissible"><p><?php esc_html_e( 'وضعیت تیکت به‌روزرسانی شد.', 'torantejarat' ); ?></p></div>
	<?php endif; ?>

	<div class="ticket-header">
		<h2><?php echo esc_html( $ticket['title'] ); ?></h2>
		<div class="ticket-meta">
			<?php echo esc_html( $statuses[ $ticket['status'] ] ?? $ticket['status'] ); ?>
			 &nbsp;|&nbsp;
			<?php echo esc_html( mysql2date( 'Y/m/d H:i', $ticket['created_at'] ) ); ?>
		</div>
	</div>

	<div class="info-grid">
		<div class="info-card">
			<span class="label"><?php esc_html_e( 'مشتری', 'torantejarat' ); ?></span>
			<span class="value"><?php echo esc_html( $user ? $user->display_name : sprintf( __( 'کاربر #%d', 'torantejarat' ), $ticket['user_id'] ) ); ?></span>
			<?php if ( $user ) : ?>
				<br><span style="font-size:12px;color:#606c68;"><?php echo esc_html( $user->user_email ); ?></span>
			<?php endif; ?>
		</div>
		<div class="info-card">
			<span class="label"><?php esc_html_e( 'سفارش', 'torantejarat' ); ?></span>
			<span class="value"><?php echo $order ? sprintf( '<a href="%s">#%d</a>', esc_url( $order->get_view_order_url() ), $order->get_id() ) : __( '—', 'torantejarat' ); ?></span>
		</div>
		<div class="info-card">
			<span class="label"><?php esc_html_e( 'موضوع', 'torantejarat' ); ?></span>
			<span class="value"><?php echo esc_html( torantejarat_ticket_subjects()[ $ticket['subject_key'] ] ?? $ticket['subject_key'] ); ?></span>
		</div>
		<div class="info-card">
			<span class="label"><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></span>
			<span class="ticket-status-badge status-<?php echo esc_attr( $ticket['status'] ); ?>"><?php echo esc_html( $statuses[ $ticket['status'] ] ?? $ticket['status'] ); ?></span>
		</div>
	</div>

	<h2><?php esc_html_e( 'گفت‌وگو', 'torantejarat' ); ?></h2>
	<div class="conversation">
		<?php if ( $messages ) : ?>
			<?php foreach ( $messages as $msg ) : ?>
				<div class="ticket-message <?php echo $msg['is_staff'] ? 'staff' : 'customer'; ?>">
					<strong><?php echo $msg['is_staff'] ? esc_html__( 'پشتیبانی', 'torantejarat' ) : esc_html__( 'مشتری', 'torantejarat' ); ?></strong>
					<span class="ticket-time"><?php echo esc_html( mysql2date( 'Y/m/d H:i', $msg['created_at'] ) ); ?></span>
					<p><?php echo esc_html( $msg['body'] ); ?></p>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'هنوز پیامی ثبت نشده است.', 'torantejarat' ); ?></p>
		<?php endif; ?>
	</div>

	<div class="reply-form">
		<h2><?php esc_html_e( 'ارسال پاسخ', 'torantejarat' ); ?></h2>
		<form method="post" action="">
			<?php wp_nonce_field( 'torantejarat-ticket-reply', 'torantejarat_ticket_reply_nonce' ); ?>
			<?php wp_nonce_field( 'torantejarat-ticket-status', 'torantejarat_ticket_status_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'وضعیت جدید', 'torantejarat' ); ?></th>
					<td>
						<select name="new_status">
							<?php foreach ( $statuses as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $ticket['status'], $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'متن پاسخ', 'torantejarat' ); ?></th>
					<td>
						<textarea name="ticket_reply" rows="5" required><?php echo esc_html( isset( $_POST['ticket_reply'] ) ? wp_unslash( $_POST['ticket_reply'] ) : '' ); ?></textarea>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'بروزرسانی وضعیت و ارسال پاسخ', 'torantejarat' ), 'primary', 'save_ticket', false ); ?>
		</form>
	</div>
</div>
	<?php
}

/* --------------------------------------------------------------
   Admin page: add ticket (staff-initiated)
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_add_page() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'شما دسترسی به مدیریت تیکت‌ها را ندارید.', 'torantejarat' ) );
	}

	if ( isset( $_POST['torantejarat_ticket_create_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_create_nonce'] ) ), 'torantejarat-ticket-create' ) ) {
		$user_id = absint( $_POST['user_id'] );
		$subject_key = isset( $_POST['subject_key'] ) ? sanitize_key( wp_unslash( $_POST['subject_key'] ) ) : 'other';
		$title = isset( $_POST['title'] ) ? wp_unslash( $_POST['title'] ) : '';
		$order_id = absint( $_POST['order_id'] );
		$body = isset( $_POST['body'] ) ? wp_unslash( $_POST['body'] ) : '';

		$result = torantejarat_tickets_create( $user_id, $subject_key, $title, $body, $order_id );
		if ( is_wp_error( $result ) ) {
			echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
		} else {
			echo '<div class="updated"><p>' . esc_html__( 'تیکت با موفقیت ایجاد شد.', 'torantejarat' ) . '</p></div>';
			wp_safe_redirect( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $result ) );
			exit;
		}
	}

	$users = get_users( array( 'role__in' => array( 'administrator', 'customer', 'shop_manager' ), 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );
	$subjects = torantejarat_ticket_subjects();
	?>
	<style>
	.torantejarat-tickets-add {
		direction: rtl;
		text-align: right;
		font-family: Vazirmatn, Tahoma, Arial, sans-serif;
		max-width: 700px;
		margin: 0 auto;
		padding: 24px;
	}
	.torantejarat-tickets-add .wrap > h1,
	.torantejarat-tickets-add h1 {
		text-align: right;
		font-size: 28px;
		color: #172325;
		margin-bottom: 24px;
	}
	.torantejarat-tickets-add form {
		background: #fff;
		border: 1px solid #e2e5df;
		border-radius: 16px;
		padding: 30px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.04);
	}
	.torantejarat-tickets-add .form-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
	}
	.torantejarat-tickets-add .form-table th,
	.torantejarat-tickets-add .form-table td {
		text-align: right;
		padding: 16px 0;
		border-bottom: 1px solid #f2f3ef;
	}
	.torantejarat-tickets-add .form-table th {
		font-weight: 800;
		color: #172325;
		font-size: 14px;
		width: 150px;
	}
	.torantejarat-tickets-add .form-table select,
	.torantejarat-tickets-add .form-table input[type="text"],
	.torantejarat-tickets-add .form-table input[type="number"],
	.torantejarat-tickets-add .form-table textarea {
		width: 100%;
		padding: 10px 14px;
		border: 1px solid #d5ddd5;
		border-radius: 8px;
		font-family: inherit;
		font-size: 13px;
		direction: rtl;
	}
	.torantejarat-tickets-add .button-primary {
		float: right;
		padding: 10px 24px;
		font-size: 14px;
		font-weight: 700;
	}
	</style>
<div class="wrap torantejarat-tickets-add">
	<h1><?php esc_html_e( 'ثبت تیکت جدید', 'torantejarat' ); ?></h1>
	<form method="post" action="">
		<?php wp_nonce_field( 'torantejarat-ticket-create', 'torantejarat_ticket_create_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'مشتری', 'torantejarat' ); ?></th>
				<td>
					<select name="user_id" required>
						<?php foreach ( $users as $u ) : ?>
							<option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ) . ' (' . esc_html( $u->user_email ) . ')'; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'موضوع', 'torantejarat' ); ?></th>
				<td>
					<select name="subject_key">
						<?php foreach ( $subjects as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'عنوان تیکت', 'torantejarat' ); ?></th>
				<td><input type="text" name="title" required maxlength="150" style="width:100%;"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'شماره سفارش (اختیاری)', 'torantejarat' ); ?></th>
				<td><input type="number" name="order_id" min="0" value="0"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'متن پیام', 'torantejarat' ); ?></th>
				<td><textarea name="body" rows="5" required style="width:100%;"></textarea></td>
			</tr>
		</table>
		<?php submit_button( __( 'ایجاد تیکت', 'torantejarat' ), 'primary', 'create_ticket', false ); ?>
	</form>
</div>
	<?php
}

/* --------------------------------------------------------------
   AJAX: Load more tickets for admin list
   -------------------------------------------------------------- */
function torantejarat_tickets_load_more() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_send_json_error( array( 'message' => 'دسترسی ندارید.' ), 403 );
	}

	$status  = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
	$search  = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$page    = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$per_page = 20;
	$offset  = ( $page - 1 ) * $per_page;

	$tickets = torantejarat_tickets_get_all( $status, $search, $per_page, $offset );
	$total   = torantejarat_tickets_count_all( $status, $search );

	$statuses = torantejarat_ticket_statuses();

	$html = '';
	foreach ( $tickets as $ticket ) {
		$user = get_user_by( 'id', $ticket['user_id'] );
		$customer_name = $user ? esc_html( $user->display_name ) : sprintf( __( 'کاربر #%d', 'torantejarat' ), $ticket['user_id'] );
		$order_label = $ticket['order_id'] ? sprintf( '#%d', $ticket['order_id'] ) : __( '—', 'torantejarat' );
		$status_label = $statuses[ $ticket['status'] ] ?? $ticket['status'];

		$html .= '<tr>';
		$html .= '<td class="ticket-id">' . esc_html( $ticket['id'] ) . '</td>';
		$html .= '<td class="ticket-title"><a href="' . esc_url( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket['id'] ) ) . '">' . esc_html( $ticket['title'] ) . '</a></td>';
		$html .= '<td class="ticket-customer">' . esc_html( $customer_name ) . '</td>';
		$html .= '<td class="ticket-order">' . esc_html( $order_label ) . '</td>';
		$html .= '<td><span class="ticket-status status-' . esc_attr( $ticket['status'] ) . '">' . esc_html( $status_label ) . '</span></td>';
		$html .= '<td>' . esc_html( mysql2date( 'Y/m/d H:i', $ticket['updated_at'] ) ) . '</td>';
		$html .= '<td class="ticket-actions"><a href="' . esc_url( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket['id'] ) ) . '" class="view-btn">' . esc_html__( 'مشاهده', 'torantejarat' ) . '</a></td>';
		$html .= '</tr>';
	}

	$has_more = ( $page * $per_page ) < $total;

	wp_send_json_success( array(
		'html'      => $html,
		'has_more'  => $has_more,
		'next_page' => $page + 1,
	) );
}
