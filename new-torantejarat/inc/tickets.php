<?php
/**
 * Independent Support Ticket System for 블ković (ToranTejarat theme).
 *
 * Tables:
 *   {$wpdb->prefix}torantejarat_tickets
 *   {$wpdb->prefix}torantejarat_ticket_messages
 *
 * This system is independent of CPT torantejarat_request (contact/rent/repairs).
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

	// Rate limit: max 3 open tickets per user
	$open_count = (int) get_transient( torantejarat_tickets_open_count_key( $user_id ) );
	if ( $open_count >= 3 ) {
		return new \WP_Error( 'rate_limit_open', __( 'You already have 3 open tickets. Please wait until one is closed.', 'torantejarat' ) );
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
		array( '%s', '%s', '%s', '%s', '%d', '%s' )
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
function torantejarat_tickets_get_all( $status = '', $search = '' ) {
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

	if ( $params ) {
		return $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
	}

	return $wpdb->get_results( $sql, ARRAY_A );
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
	if ( ! torantejarat_tickets_user_can_manage() && $ticket['user_id'] !== $user_id ) {
		return new \WP_Error( 'forbidden', __( 'You cannot modify this ticket.', 'torantejarat' ) );
	}

	$wpdb->update(
		torantejarat_tickets_get_table(),
		array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $ticket_id ),
		array( '%s', '%s', '%d' )
	);

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
add_action( 'init', __NAMESPACE__ . '\\torantejarat_tickets_setup_capability', 10 );

/* --------------------------------------------------------------
   Admin menu (phase 4)
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_menu() {
	add_menu_page(
		__( 'Tickets', 'torantejarat' ),
		__( 'Support Tickets', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_page',
		'dashicons-ticket',
		55
	);

	add_submenu_page(
		'torantejarat-tickets',
		__( 'All Tickets', 'torantejarat' ),
		__( 'All Tickets', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_page'
	);

	add_submenu_page(
		'torantejarat-tickets',
		__( 'Add Ticket', 'torantejarat' ),
		__( 'Add Ticket', 'torantejarat' ),
		torantejarat_tickets_capability(),
		'torantejarat-tickets-add',
		__NAMESPACE__ . '\\torantejarat_tickets_admin_add_page'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\torantejarat_tickets_admin_menu', 30 );

/* --------------------------------------------------------------
   Admin page: list tickets (simple HTML list for now; WP_List_Table can replace later)
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_page() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'You do not have permission to manage tickets.', 'torantejarat' ) );
	}

	$status  = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
	$search  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$statuses = torantejarat_ticket_statuses();

	$tickets = torantejarat_tickets_get_all( $status, $search );

	?>
<style>
.torantejarat-tickets-admin { direction: rtl; }
.torantejarat-tickets-admin .wrap > h1,
.torantejarat-tickets-admin h1 { text-align: right; }
.torantejarat-tickets-admin .wp-list-table th,
.torantejarat-tickets-admin .wp-list-table td { text-align: right; vertical-align: middle; }
.torantejarat-tickets-admin .wp-list-table .actions { text-align: center; }
.torantejarat-tickets-admin .form-table th { text-align: right; }
.torantejarat-tickets-admin .form-table td { text-align: right; }
.torantejarat-tickets-admin .form-table select,
.torantejarat-tickets-admin .form-table input[type="text"],
.torantejarat-tickets-admin .form-table textarea { direction: rtl; }
.torantejarat-tickets-admin .button { float: right; }
.torantejarat-tickets-admin .updated,
.torantejarat-tickets-admin .error { text-align: right; direction: rtl; }
.torantejarat-tickets-admin label { display: block; text-align: right; }
.torantejarat-tickets-admin .ticket-time { float: left; text-align: left; }
</style>
<div class="wrap torantejarat-tickets-admin">
	<h1><?php esc_html_e( 'Support Tickets', 'torantejarat' ); ?></h1>

		<form method="get" action="">
			<input type="hidden" name="page" value="torantejarat-tickets">
			<label for="status"><?php esc_html_e( 'Status', 'torantejarat' ); ?></label>
			<select name="status" id="status">
				<option value=""><?php esc_html_e( 'All statuses', 'torantejarat' ); ?></option>
				<?php foreach ( $statuses as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<label for="s"><?php esc_html_e( 'Search by title or ticket ID', 'torantejarat' ); ?></label>
			<input type="search" name="s" id="s" value="<?php echo esc_attr( $search ); ?>">
			<?php submit_button( __( 'Filter', 'torantejarat' ), '', 'filter', false ); ?>
		</form>

		<?php if ( $tickets ) : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Title', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Customer', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Order', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Status', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Updated', 'torantejarat' ); ?></th>
						<th><?php esc_html_e( 'Action', 'torantejarat' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tickets as $ticket ) : ?>
						<?php
						$user = get_user_by( 'id', $ticket['user_id'] );
						$customer_name = $user ? esc_html( $user->display_name ) : sprintf( __( 'User #%d', 'torantejarat' ), $ticket['user_id'] );
						$order_label = $ticket['order_id'] ? sprintf( '#%d', $ticket['order_id'] ) : __( '—', 'torantejarat' );
						$status_label = $statuses[ $ticket['status'] ] ?? $ticket['status'];
						?>
						<tr>
							<td><?php echo esc_html( $ticket['id'] ); ?></td>
							<td><?php echo esc_html( $ticket['title'] ); ?></td>
							<td><?php echo esc_html( $customer_name ); ?></td>
							<td><?php echo esc_html( $order_label ); ?></td>
							<td><?php echo esc_html( $status_label ); ?></td>
							<td><?php echo esc_html( mysql2date( 'Y/m/d H:i', $ticket['updated_at'] ) ); ?></td>
							<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket['id'] ) ); ?>"><?php esc_html_e( 'View', 'torantejarat' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php esc_html_e( 'No tickets found.', 'torantejarat' ); ?></p>
		<?php endif; ?>

		<?php if ( isset( $_GET['view'] ) && 'details' === $_GET['view'] && ! empty( $_GET['id'] ) ) : ?>
			<?php torantejarat_tickets_admin_details_page( absint( $_GET['id'] ) ); ?>
		<?php endif; ?>
	</div>
	<?php
}

/* --------------------------------------------------------------
   Admin page: ticket details + reply
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_details_page( $ticket_id ) {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'You do not have permission to manage tickets.', 'torantejarat' ) );
	}

	$ticket = torantejarat_tickets_get( $ticket_id );
	if ( ! $ticket ) {
		wp_die( __( 'Ticket not found.', 'torantejarat' ) );
	}
	$ticket = (array) $ticket;

	$messages = torantejarat_tickets_get_messages( $ticket_id );
	$statuses = torantejarat_ticket_statuses();

	$user = get_user_by( 'id', $ticket['user_id'] );
	$order = $ticket['order_id'] ? wc_get_order( $ticket['order_id'] ) : null;

	// Handle status change
	if ( isset( $_POST['torantejarat_ticket_status_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_status_nonce'] ) ), 'torantejarat-ticket-status' ) ) {
		if ( isset( $_POST['new_status'] ) ) {
			$result = torantejarat_tickets_set_status( $ticket_id, sanitize_key( wp_unslash( $_POST['new_status'] ) ), get_current_user_id() );
			if ( is_wp_error( $result ) ) {
				echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
			} else {
				echo '<div class="updated"><p>' . esc_html__( 'Status updated.', 'torantejarat' ) . '</p></div>';
				$ticket = torantejarat_tickets_get( $ticket_id );
				$ticket = (array) $ticket;
			}
		}
		wp_safe_redirect( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket_id ) );
		exit;
	}

	// Handle reply
	if ( isset( $_POST['torantejarat_ticket_reply_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_ticket_reply_nonce'] ) ), 'torantejarat-ticket-reply' ) ) {
		$body = isset( $_POST['ticket_reply'] ) ? wp_unslash( $_POST['ticket_reply'] ) : '';
		$result = torantejarat_tickets_add_message( $ticket_id, get_current_user_id(), $body, true );
		if ( is_wp_error( $result ) ) {
			echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
		} else {
			echo '<div class="updated"><p>' . esc_html__( 'Reply sent.', 'torantejarat' ) . '</p></div>';
		}
		wp_safe_redirect( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $ticket_id ) );
		exit;
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Ticket Details', 'torantejarat' ); ?> #<?php echo esc_html( $ticket_id ); ?></h1>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Title', 'torantejarat' ); ?></th>
				<td><?php echo esc_html( $ticket['title'] ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Customer', 'torantejarat' ); ?></th>
				<td>
					<?php echo esc_html( $user ? $user->display_name : sprintf( __( 'User #%d', 'torantejarat' ), $ticket['user_id'] ) ); ?>
					<?php if ( $user ) : ?>
						(<?php echo esc_html( $user->user_email ); ?>)
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Order', 'torantejarat' ); ?></th>
				<td><?php echo $order ? sprintf( '<a href="%s">#%d</a>', esc_url( $order->get_view_order_url() ), $order->get_id() ) : __( '—', 'torantejarat' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Subject', 'torantejarat' ); ?></th>
				<td><?php echo esc_html( torantejarat_ticket_subjects()[ $ticket['subject_key'] ] ?? $ticket['subject_key'] ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Status', 'torantejarat' ); ?></th>
				<td><?php echo esc_html( $statuses[ $ticket['status'] ] ?? $ticket['status'] ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Created', 'torantejarat' ); ?></th>
				<td><?php echo esc_html( mysql2date( 'Y/m/d H:i', $ticket['created_at'] ) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Last Reply', 'torantejarat' ); ?></th>
				<td><?php echo esc_html( $ticket['last_reply_at'] ? mysql2date( 'Y/m/d H:i', $ticket['last_reply_at'] ) : __( '—', 'torantejarat' ) ) . ' (' . esc_html( $ticket['last_reply_by'] ?? '—' ) . ')'; ?></td>
			</tr>
		</table>

		<h2><?php esc_html_e( 'Conversation', 'torantejarat' ); ?></h2>
		<?php if ( $messages ) : ?>
			<ul>
				<?php foreach ( $messages as $msg ) : ?>
					<li>
						<strong><?php echo $msg['is_staff'] ? esc_html__( 'Support', 'torantejarat' ) : esc_html__( 'Customer', 'torantejarat' ); ?></strong>
						<span class="ticket-time"><?php echo esc_html( mysql2date( 'Y/m/d H:i', $msg['created_at'] ) ); ?></span>
						<p><?php echo esc_html( $msg['body'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php esc_html_e( 'No messages yet.', 'torantejarat' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Send Reply', 'torantejarat' ); ?></h2>
		<form method="post" action="">
			<?php wp_nonce_field( 'torantejarat-ticket-reply', 'torantejarat_ticket_reply_nonce' ); ?>
			<?php wp_nonce_field( 'torantejarat-ticket-status', 'torantejarat_ticket_status_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'New Status', 'torantejarat' ); ?></th>
					<td>
						<select name="new_status">
							<?php foreach ( $statuses as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $ticket['status'], $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Reply', 'torantejarat' ); ?></th>
					<td>
						<textarea name="ticket_reply" rows="5" style="width:100%;" required><?php echo esc_html( isset( $_POST['ticket_reply'] ) ? wp_unslash( $_POST['ticket_reply'] ) : '' ); ?></textarea>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Update Status & Send Reply', 'torantejarat' ), 'primary', 'save_ticket', false ); ?>
		</form>
	</div>
	<?php
}

/* --------------------------------------------------------------
   Admin page: add ticket (staff-initiated)
   -------------------------------------------------------------- */
function torantejarat_tickets_admin_add_page() {
	if ( ! torantejarat_tickets_user_can_manage() ) {
		wp_die( __( 'You do not have permission to manage tickets.', 'torantejarat' ) );
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
			echo '<div class="updated"><p>' . esc_html__( 'Ticket created successfully.', 'torantejarat' ) . '</p></div>';
			wp_safe_redirect( admin_url( 'admin.php?page=torantejarat-tickets&view=details&id=' . $result ) );
			exit;
		}
	}

	$users = get_users( array( 'role__in' => array( 'administrator', 'customer', 'shop_manager' ), 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );
	$subjects = torantejarat_ticket_subjects();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Add New Ticket', 'torantejarat' ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'torantejarat-ticket-create', 'torantejarat_ticket_create_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Customer', 'torantejarat' ); ?></th>
					<td>
						<select name="user_id" required>
							<?php foreach ( $users as $u ) : ?>
								<option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ) . ' (' . esc_html( $u->user_email ) . ')'; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Subject', 'torantejarat' ); ?></th>
					<td>
						<select name="subject_key">
							<?php foreach ( $subjects as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Title', 'torantejarat' ); ?></th>
					<td><input type="text" name="title" required maxlength="150" style="width:100%;"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Order ID (optional)', 'torantejarat' ); ?></th>
					<td><input type="number" name="order_id" min="0" value="0"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Message', 'torantejarat' ); ?></th>
					<td><textarea name="body" rows="5" required style="width:100%;"></textarea></td>
				</tr>
			</table>
			<?php submit_button( __( 'Create Ticket', 'torantejarat' ), 'primary', 'create_ticket', false ); ?>
		</form>
	</div>
	<?php
}