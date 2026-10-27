<?php
/**
 * Ticket meta, custom fields and attachments for the independent ticket system.
 *
 * Extends inc/tickets.php with Poshtvan-inspired features (reference only —
 * original implementation, different namespace and table prefix):
 *   - ticket_meta table  (key/value storage per ticket)
 *   - ticket_fields table (custom form field definitions)
 *   - ticket_attachments table (uploaded files per ticket/reply)
 *
 * @package ToranTejarat\Theme
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --------------------------------------------------------------
   Schema version for meta/fields/attachments tables
   -------------------------------------------------------------- */
function torantejarat_tickets_meta_schema_version() {
	return '1.0.0';
}

/* --------------------------------------------------------------
   Create meta / fields / attachments tables on init
   -------------------------------------------------------------- */
function torantejarat_tickets_meta_maybe_create_tables() {
	global $wpdb;

	$version = get_option( 'torantejarat_tickets_meta_db_version', '' );
	if ( $version === torantejarat_tickets_meta_schema_version() ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset_collate = $wpdb->get_charset_collate();

	// Ticket meta (key/value per ticket — custom field values, attachment refs, rating)
	$table_meta = $wpdb->prefix . 'torantejarat_ticket_meta';
	$sql_meta = "CREATE TABLE $table_meta (
		meta_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		ticket_id BIGINT UNSIGNED NOT NULL,
		meta_key VARCHAR(255) NOT NULL DEFAULT '',
		meta_value LONGTEXT DEFAULT NULL,
		PRIMARY KEY (meta_id),
		KEY ticket_id (ticket_id),
		KEY meta_key (meta_key)
	) $charset_collate;";

	// Custom field definitions for the ticket form
	$table_fields = $wpdb->prefix . 'torantejarat_ticket_fields';
	$sql_fields = "CREATE TABLE $table_fields (
		field_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		field_name VARCHAR(100) NOT NULL DEFAULT '',
		field_label VARCHAR(150) NOT NULL DEFAULT '',
		field_type VARCHAR(30) NOT NULL DEFAULT 'text',
		is_required TINYINT(1) NOT NULL DEFAULT 0,
		field_priority INT NOT NULL DEFAULT 0,
		PRIMARY KEY (field_id),
		KEY field_priority (field_priority)
	) $charset_collate;";

	// Attachments uploaded on tickets or replies
	$table_attachments = $wpdb->prefix . 'torantejarat_ticket_attachments';
	$sql_attachments = "CREATE TABLE $table_attachments (
		attachment_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		ticket_id BIGINT UNSIGNED NOT NULL,
		message_id BIGINT UNSIGNED DEFAULT NULL,
		file_name VARCHAR(255) NOT NULL DEFAULT '',
		file_path VARCHAR(500) NOT NULL DEFAULT '',
		file_size BIGINT UNSIGNED DEFAULT 0,
		mime_type VARCHAR(100) DEFAULT NULL,
		uploaded_by BIGINT UNSIGNED NOT NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (attachment_id),
		KEY ticket_id (ticket_id),
		KEY message_id (message_id),
		KEY uploaded_by (uploaded_by)
	) $charset_collate;";

	dbDelta( $sql_meta ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	dbDelta( $sql_fields ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	dbDelta( $sql_attachments ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	update_option( 'torantejarat_tickets_meta_db_version', torantejarat_tickets_meta_schema_version() );
}
add_action( 'init', __NAMESPACE__ . '\\\\torantejarat_tickets_meta_maybe_create_tables', 12 );

/* --------------------------------------------------------------
   Meta CRUD
   -------------------------------------------------------------- */
function torantejarat_tickets_meta_get_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_ticket_meta';
}

function torantejarat_tickets_get_meta( $ticket_id, $meta_key, $single = true ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id <= 0 ) {
		return $single ? '' : array();
	}

	if ( $single ) {
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM " . torantejarat_tickets_meta_get_table() .
				" WHERE ticket_id = %d AND meta_key = %s LIMIT 1",
				$ticket_id,
				$meta_key
			)
		);
	}

	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta_key, meta_value FROM " . torantejarat_tickets_meta_get_table() .
			" WHERE ticket_id = %d",
			$ticket_id
		),
		ARRAY_A
	);
}

function torantejarat_tickets_update_meta( $ticket_id, $meta_key, $meta_value ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id <= 0 ) {
		return false;
	}

	$meta_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_id FROM " . torantejarat_tickets_meta_get_table() .
			" WHERE ticket_id = %d AND meta_key = %s LIMIT 1",
			$ticket_id,
			$meta_key
		)
	);

	if ( $meta_id ) {
		return $wpdb->update(
			torantejarat_tickets_meta_get_table(),
			array( 'meta_value' => $meta_value ),
			array( 'meta_id' => $meta_id ),
			array( '%s' ),
			array( '%d' )
		);
	}

	return $wpdb->insert(
		torantejarat_tickets_meta_get_table(),
		array(
			'ticket_id' => $ticket_id,
			'meta_key'    => $meta_key,
			'meta_value'  => $meta_value,
		),
		array( '%d', '%s', '%s' )
	);
}

function torantejarat_tickets_delete_meta( $ticket_id, $meta_key ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id <= 0 ) {
		return false;
	}

	return $wpdb->delete(
		torantejarat_tickets_meta_get_table(),
		array( 'ticket_id' => $ticket_id, 'meta_key' => $meta_key ),
		array( '%d', '%s' )
	);
}

/* --------------------------------------------------------------
   Custom field definitions
   -------------------------------------------------------------- */
function torantejarat_tickets_fields_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_ticket_fields';
}

function torantejarat_tickets_get_fields() {
	global $wpdb;

	return $wpdb->get_results(
		"SELECT * FROM " . torantejarat_tickets_fields_table() .
		" ORDER BY field_priority ASC, field_id ASC",
		ARRAY_A
	);
}

function torantejarat_tickets_add_field( $field_name, $field_label, $field_type = 'text', $is_required = 0, $priority = 0 ) {
	global $wpdb;

	$field_name = sanitize_key( $field_name );
	$field_label = sanitize_text_field( $field_label );
	$field_type = sanitize_key( $field_type );
	$is_required = absint( $is_required );
	$priority = absint( $priority );

	if ( '' === $field_name || '' === $field_label ) {
		return new \WP_Error( 'invalid_field', __( 'Field name and label are required.', 'torantejarat' ) );
	}

	return $wpdb->insert(
		torantejarat_tickets_fields_table(),
		array(
			'field_name'   => $field_name,
			'field_label'  => $field_label,
			'field_type'   => $field_type,
			'is_required'  => $is_required,
			'field_priority' => $priority,
		),
		array( '%s', '%s', '%s', '%d', '%d' )
	);
}

function torantejarat_tickets_update_field( $field_id, $data ) {
	global $wpdb;

	$field_id = absint( $field_id );
	if ( $field_id <= 0 || ! is_array( $data ) ) {
		return false;
	}

	$format = array();
	$where_format = array( '%d' );

	foreach ( $data as $key => $value ) {
		if ( 'is_required' === $key || 'field_priority' === $key ) {
			$format[] = '%d';
		} else {
			$format[] = '%s';
		}
	}

	return $wpdb->update(
		torantejarat_tickets_fields_table(),
		$data,
		array( 'field_id' => $field_id ),
		$format,
		$where_format
	);
}

function torantejarat_tickets_delete_field( $field_id ) {
	global $wpdb;

	$field_id = absint( $field_id );
	if ( $field_id <= 0 ) {
		return false;
	}

	return $wpdb->delete(
		torantejarat_tickets_fields_table(),
		array( 'field_id' => $field_id ),
		array( '%d' )
	);
}

/* --------------------------------------------------------------
   Attachments table
   -------------------------------------------------------------- */
function torantejarat_tickets_attachments_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_ticket_attachments';
}

function torantejarat_tickets_add_attachment( $ticket_id, $file_data, $message_id = 0 ) {
	global $wpdb;

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		return new \WP_Error( 'no_upload_handler', __( 'File upload handler is not available.', 'torantejarat' ) );
	}

	$ticket_id = absint( $ticket_id );
	$message_id = absint( $message_id );
	$uploaded_by = get_current_user_id();

	if ( $ticket_id <= 0 ) {
		return new \WP_Error( 'invalid_ticket', __( 'Invalid ticket ID.', 'torantejarat' ) );
	}

	$upload = wp_handle_upload( $file_data, array( 'test_form' => false ) );
	if ( isset( $upload['error'] ) ) {
		return new \WP_Error( 'upload_error', $upload['error'] );
	}

	$file_size = filesize( $upload['file'] );
	$mime_type = mime_content_type( $upload['file'] );

	$attachment_id = $wpdb->insert(
		torantejarat_tickets_attachments_table(),
		array(
			'ticket_id'   => $ticket_id,
			'message_id'  => $message_id > 0 ? $message_id : null,
			'file_name'   => sanitize_file_name( basename( $upload['file'] ) ),
			'file_path'   => $upload['url'],
			'file_size'   => $file_size,
			'mime_type'   => $mime_type,
			'uploaded_by' => $uploaded_by,
		),
		array( '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s' )
	);

	if ( ! $attachment_id ) {
		return new \WP_Error( 'db_error', __( 'Could not save attachment record.', 'torantejarat' ) );
	}

	return $attachment_id;
}

function torantejarat_tickets_get_attachments( $ticket_id, $message_id = null ) {
	global $wpdb;

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id <= 0 ) {
		return array();
	}

	$sql = "SELECT * FROM " . torantejarat_tickets_attachments_table() . " WHERE ticket_id = %d";
	$params = array( $ticket_id );
	$formats = array( '%d' );

	if ( $message_id !== null ) {
		$message_id = absint( $message_id );
		$sql .= " AND message_id = %d";
		$params[] = $message_id;
		$formats[] = '%d';
	}

	$sql .= " ORDER BY created_at ASC";

	return $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
}

function torantejarat_tickets_delete_attachment( $attachment_id ) {
	global $wpdb;

	$attachment_id = absint( $attachment_id );
	if ( $attachment_id <= 0 ) {
		return false;
	}

	$attachment = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT file_path FROM " . torantejarat_tickets_attachments_table() . " WHERE attachment_id = %d",
			$attachment_id
		)
	);

	if ( $attachment && file_exists( $attachment->file_path ) ) {
		@unlink( $attachment->file_path );
	}

	return $wpdb->delete(
		torantejarat_tickets_attachments_table(),
		array( 'attachment_id' => $attachment_id ),
		array( '%d' )
	);
}

/* --------------------------------------------------------------
   Render custom fields HTML for ticket form
   -------------------------------------------------------------- */
function torantejarat_tickets_render_custom_fields( $prefix = 'torantejarat_ticket_field_' ) {
	$fields = torantejarat_tickets_get_fields();
	if ( ! $fields ) {
		return;
	}

	foreach ( $fields as $field ) : ?>
		<p class="torantejarat-ticket-custom-field">
			<label for="<?php echo esc_attr( $prefix . $field['field_name'] ); ?>"><?php echo esc_html( $field['field_label'] ); ?><?php if ( (int) $field['is_required'] === 1 ) : echo ' <span class="required">*</span>'; endif; ?></label>
			<?php
			$field_name_attr = $prefix . $field['field_name'];
			$field_value = isset( $_POST[ $field_name_attr ] ) ? wp_unslash( $_POST[ $field_name_attr ] ) : '';
			switch ( $field['field_type'] ) :
				case 'textarea':
					?><textarea name="<?php echo esc_attr( $field_name_attr ); ?>" rows="3"><?php echo esc_textarea( $field_value ); ?></textarea><?php
					break;
				case 'select':
					?><select name="<?php echo esc_attr( $field_name_attr ); ?>"><?php
					$options = explode( "\n", $field_value );
					foreach ( $options as $opt ) :
						$opt = trim( $opt );
						if ( '' !== $opt ) :
							?><option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option><?php
						endif;
					endforeach;
					?></select><?php
					break;
				case 'checkbox':
					?><input type="checkbox" name="<?php echo esc_attr( $field_name_attr ); ?>" value="1"<?php checked( $field_value, '1' ); ?>><?php
					break;
				default:
					?><input type="text" name="<?php echo esc_attr( $field_name_attr ); ?>" value="<?php echo esc_attr( $field_value ); ?>"><?php
					break;
			endswitch;
			?>
		</p>
	<?php endforeach;
}

/* --------------------------------------------------------------
   Save custom field values as ticket meta
   -------------------------------------------------------------- */
function torantejarat_tickets_save_custom_fields( $ticket_id, $post_data ) {
	$fields = torantejarat_tickets_get_fields();
	if ( ! $fields ) {
		return;
	}

	$prefix = 'torantejarat_ticket_field_';
	$values = array();

	foreach ( $fields as $field ) {
		$key = $prefix . $field['field_name'];
		if ( isset( $post_data[ $key ] ) ) {
			$value = wp_unslash( $post_data[ $key ] );
			if ( is_array( $value ) ) {
				$value = implode( ', ', array_map( 'sanitize_text_field', $value ) );
			} else {
				$value = sanitize_text_field( $value );
			}
			$values[ $field['field_name'] ] = $value;
		}
	}

	if ( $values ) {
		torantejarat_tickets_update_meta( $ticket_id, 'custom_fields', maybe_serialize( $values ) );
	}
}

/* --------------------------------------------------------------
   Get saved custom field values for a ticket
   -------------------------------------------------------------- */
function torantejarat_tickets_get_custom_field_values( $ticket_id ) {
	$serialized = torantejarat_tickets_get_meta( $ticket_id, 'custom_fields' );
	if ( '' === $serialized ) {
		return array();
	}

	return maybe_unserialize( $serialized );
}
