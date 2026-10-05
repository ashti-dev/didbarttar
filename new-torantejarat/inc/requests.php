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
		echo '<div class="torantejarat-form-notice is-error" role="alert">';
		echo '<p><strong>' . esc_html__( 'درخواست ثبت نشد.', 'torantejarat' ) . '</strong></p>';
		echo '<p>' . esc_html__( 'اطلاعات را کامل و درست وارد کنید: نام، موبایل معتبر (مثل 09123456789) و شرح درخواست.', 'torantejarat' ) . '</p>';
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
	$fail = static function ( $page_id ) {
		$target = $page_id > 0 ? get_permalink( $page_id ) : home_url( '/' );
		wp_safe_redirect( add_query_arg( 'sent', '0', $target ) );
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
	if ( mb_strlen( $name ) < 2 || '' === $phone || mb_strlen( $details ) < 10 ) {
		$fail( $page_id );
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
			$fail( $page_id );
		}
		$meta['date'] = $date;
		$meta['days'] = $days;
		$meta['city'] = $city;
	} elseif ( 'repairs' === $type ) {
		$model = isset( $_POST['model'] ) ? sanitize_text_field( wp_unslash( $_POST['model'] ) ) : '';
		if ( '' === $model ) {
			$fail( $page_id );
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
