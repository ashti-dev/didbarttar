<?php
/**
 * Theme Settings Panel — بلکوکم
 * Native WordPress Settings API — No dependencies.
 *
 * To add a tab or field, edit the $schema array only.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TT_Settings {

	private static $instance = null;

	/**
	 * Full tab/field definition. Each field: label, type, default, desc, sanitize.
	 *
	 * @var array
	 */
	private $schema = array(
		'general' => array(
			'label'  => 'عمومی',
			'icon'   => 'dashicons-admin-home',
			'fields' => array(
				'topbar_text' => array(
					'label'    => 'متن تاپ‌بار',
					'type'     => 'text',
					'default'  => '',
					'desc'     => 'خالی = توضیح کوتاه سایت نمایش داده می‌شود.',
					'sanitize' => 'sanitize_text_field',
				),
				'topbar_link' => array(
					'label'    => 'لینک تاپ‌بار',
					'type'     => 'url',
					'default'  => '',
					'desc'     => 'اختیاری — کل متن تاپ‌بار را کلیک‌پذیر می‌کند.',
					'sanitize' => 'esc_url_raw',
				),
				'preview_note' => array(
					'label'    => 'حالت پیش‌نمایش',
					'type'     => 'select',
					'default'  => 'no',
					'desc'     => 'با روشن شدن، متن‌های «نسخه پیش‌نمایش» در فوتر، پرسش‌های صفحهٔ اصلی و صفحهٔ محصول نمایش داده می‌شوند.',
					'options'  => array(
						'no'  => 'خاموش — متن برند',
						'yes' => 'روشن — متن پیش‌نمایش',
					),
					'sanitize' => 'sanitize_key',
				),
			),
		),
		'contact' => array(
			'label'  => 'تماس',
			'icon'   => 'dashicons-phone',
			'fields' => array(
				'phone'         => array(
					'label'    => 'شماره تماس (href)',
					'type'     => 'tel',
					'default'  => '',
					'desc'     => 'برای لینک tel: — مثال: 02112345678',
					'sanitize' => 'sanitize_text_field',
				),
				'phone_display' => array(
					'label'    => 'شماره تماس (نمایشی)',
					'type'     => 'text',
					'default'  => '',
					'desc'     => 'مثال: ۰۲۱–۱۲۳۴–۵۶۷۸',
					'sanitize' => 'sanitize_text_field',
				),
				'whatsapp'      => array(
					'label'    => 'شماره واتساپ',
					'type'     => 'text',
					'default'  => '',
					'desc'     => 'با کد کشور بدون + — مثال: 989121234567',
					'sanitize' => 'sanitize_text_field',
				),
				'email'         => array(
					'label'    => 'ایمیل',
					'type'     => 'email',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'sanitize_email',
				),
				'address'       => array(
					'label'    => 'آدرس',
					'type'     => 'textarea',
					'default'  => '',
					'desc'     => 'در فوتر و Schema استفاده می‌شود.',
					'sanitize' => 'sanitize_textarea_field',
				),
				'working_hours' => array(
					'label'    => 'ساعت کاری',
					'type'     => 'text',
					'default'  => 'شنبه تا پنجشنبه — ۹ تا ۱۸',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'map_embed'     => array(
					'label'    => 'نقشه (embed)',
					'type'     => 'embed',
					'default'  => '',
					'desc'     => 'کد iframe گوگل‌مپ (Share ← Embed a map). خالی = جعبه راهنما نمایش داده می‌شود.',
					'sanitize' => 'embed',
				),
			),
		),
		'social'  => array(
			'label'  => 'شبکه‌های اجتماعی',
			'icon'   => 'dashicons-share',			'fields' => array(
				'instagram' => array(
					'label'    => 'اینستاگرام',
					'type'     => 'url',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'esc_url_raw',
				),
				'telegram'  => array(
					'label'    => 'تلگرام',
					'type'     => 'url',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'esc_url_raw',
				),
				'aparat'    => array(
					'label'    => 'آپارات',
					'type'     => 'url',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'esc_url_raw',
				),
				'youtube'   => array(
					'label'    => 'یوتیوب',
					'type'     => 'url',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'esc_url_raw',
				),
			),
		),
		'login'    => array(
			'label'  => 'صفحه ورود',
			'icon'   => 'dashicons-lock',
			'fields' => array(
				'login_logo'            => array(
					'label'    => 'لوگوی صفحه ورود (URL)',
					'type'     => 'url',
					'default'  => '',
					'desc'     => 'خالی = لوگوی سفارشی‌ساز، بعد آیکون قالب.',
					'sanitize' => 'esc_url_raw',
				),
				'login_title'           => array(
					'label'    => 'تیتر خوش‌آمد',
					'type'     => 'text',
					'default'  => '',
					'desc'     => 'خالی = نام سایت نمایش داده می‌شود.',
					'sanitize' => 'sanitize_text_field',
				),
				'login_text'            => array(
					'label'    => 'متن زیر تیتر',
					'type'     => 'textarea',
					'default'  => '',
					'desc'     => '',
					'sanitize' => 'sanitize_textarea_field',
				),
				'login_bg'              => array(
					'label'    => 'رنگ پس‌زمینه',
					'type'     => 'color',
					'default'  => '#172325',
					'desc'     => '',
					'sanitize' => 'sanitize_hex_color',
				),
				'login_accent'          => array(
					'label'    => 'رنگ دکمه و لینک‌ها',
					'type'     => 'color',
					'default'  => '#f77b3e',
					'desc'     => '',
					'sanitize' => 'sanitize_hex_color',
				),
				'login_hide_register'   => array(
					'label'    => 'مخفی کردن لینک ثبت‌نام',
					'type'     => 'checkbox',
					'default'  => 0,
					'desc'     => '',
					'sanitize' => 'absint',
				),
				'login_hide_lostpassword' => array(
					'label'    => 'مخفی کردن «رمز را فراموش کرده‌ام»',
					'type'     => 'checkbox',
					'default'  => 0,
					'desc'     => 'توصیه نمی‌شود — فقط اگر ورود فقط با پیامک است.',
					'sanitize' => 'absint',
				),
			),
		),
		'business' => array(
			'label'  => 'کسب‌وکار',
			'icon'   => 'dashicons-store',
			'fields' => array(
				'business_name' => array(
					'label'    => 'نام کسب‌وکار',
					'type'     => 'text',
					'default'  => '',
					'desc'     => 'پیش‌فرض: نام سایت از تنظیمات وردپرس.',
					'sanitize' => 'sanitize_text_field',
				),
				'business_type' => array(
					'label'    => 'نوع کسب‌وکار',
					'type'     => 'select',
					'default'  => 'Store',
					'desc'     => 'مستقیم در Schema استفاده می‌شود — مقدار آزاد وارد نکنید.',
					'options'  => array(
						'Store'               => 'فروشگاه (Store)',
						'OnlineStore'         => 'فروشگاه آنلاین (OnlineStore)',
						'LocalBusiness'       => 'کسب‌وکار محلی (LocalBusiness)',
						'ProfessionalService' => 'خدمات حرفه‌ای (ProfessionalService)',
					),
					'sanitize' => 'sanitize_text_field',
				),
				'city'          => array(
					'label'    => 'شهر',
					'type'     => 'text',
					'default'  => 'تهران',
					'desc'     => 'در addressLocality اسکیما استفاده می‌شود.',
					'sanitize' => 'sanitize_text_field',
				),
				'price_range'   => array(
					'label'    => 'محدوده قیمت',
					'type'     => 'select',
					'default'  => '$$',
					'desc'     => 'اختیاری — در rich result گوگل نمایش داده می‌شود.',
					'options'  => array(
						'$'    => '$ — اقتصادی',
						'$$'   => '$$ — متوسط',
						'$$$'  => '$$$ — گران',
						'$$$$' => '$$$$ — لوکس',
					),
					'sanitize' => 'sanitize_text_field',
				),
			),
		),
	);

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'تنظیمات قالب بلکوکم', 'torantejarat' ),
			__( 'بلکوکم', 'torantejarat' ),
			'manage_options',
			'torantejarat-settings',
			array( $this, 'render_page' ),
			'dashicons-store',
			3
		);
	}

	public function register_settings() {
		foreach ( $this->schema as $tab_key => $tab ) {
			register_setting(
				'torantejarat_' . $tab_key,
				'torantejarat_' . $tab_key,
				array(
					'sanitize_callback' => function ( $input ) use ( $tab_key ) {
						return $this->sanitize_tab( $input, $tab_key );
					},
				)
			);
		}
	}

	private function sanitize_tab( $input, $tab_key ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$fields = isset( $this->schema[ $tab_key ]['fields'] ) ? $this->schema[ $tab_key ]['fields'] : array();
		$output = array();
		foreach ( $fields as $key => $field ) {
			$raw            = isset( $input[ $key ] ) ? $input[ $key ] : '';
			$output[ $key ] = ( 'repeater' === $field['type'] )
				? $this->sanitize_repeater( $raw, $field )
				: $this->sanitize_value( $raw, $field );
		}
		return $output;
	}

	/**
	 * Sanitize one scalar value using the field's declared type.
	 *
	 * Shared by flat fields and repeater sub-fields so a new field type is
	 * implemented in exactly one place.
	 *
	 * @param mixed $raw   Submitted value.
	 * @param array $field Field definition; needs "type", may need "options"/"default"/"sanitize".
	 * @return mixed
	 */
	private function sanitize_value( $raw, array $field ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		if ( 'url' === $type ) {
			return esc_url_raw( trim( (string) $raw ) );
		}
		if ( 'email' === $type ) {
			return sanitize_email( (string) $raw );
		}
		if ( 'textarea' === $type || 'embed' === $type ) {
			return ( 'embed' === $type )
				? wp_kses( (string) $raw, array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'style' => true, 'loading' => true, 'title' => true, 'allowfullscreen' => true ) ) )
				: sanitize_textarea_field( (string) $raw );
		}
		if ( 'tel' === $type ) {
			return preg_replace( '/[^0-9+]/', '', (string) $raw );
		}
		if ( 'select' === $type ) {
			return $this->sanitize_select( $raw, isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array() );
		}
		if ( 'color' === $type ) {
			$torantejarat_color = sanitize_hex_color( (string) $raw );
			return $torantejarat_color ? $torantejarat_color : ( isset( $field['default'] ) ? $field['default'] : '' );
		}
		if ( 'checkbox' === $type ) {
			return ! empty( $raw ) ? 1 : 0;
		}
		$callback = isset( $field['sanitize'] ) ? $field['sanitize'] : 'sanitize_text_field';
		return call_user_func( $callback, $raw );
	}

	/**
	 * Sanitize a repeater value.
	 *
	 * Every row is filtered sub-field by sub-field with the same rules as a
	 * flat field, rows that end up completely empty are dropped, and the list
	 * is re-indexed so the stored option is always a clean 0..n array.
	 *
	 * @param mixed $raw   Submitted rows.
	 * @param array $field Repeater definition; needs "subfields".
	 * @return array
	 */
	private function sanitize_repeater( $raw, array $field ) {
		$subfields = isset( $field['subfields'] ) && is_array( $field['subfields'] ) ? $field['subfields'] : array();
		if ( ! $subfields || ! is_array( $raw ) ) {
			return array();
		}
		$rows = array();
		foreach ( array_values( $raw ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean     = array();
			$not_empty = false;
			foreach ( $subfields as $sub_key => $sub ) {
				$value             = isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : '';
				$value             = $this->sanitize_value( $value, $sub );
				$clean[ $sub_key ] = $value;
				if ( '' !== (string) $value && '0' !== (string) $value ) {
					$not_empty = true;
				}
			}
			if ( $not_empty ) {
				$rows[] = $clean;
			}
		}
		return $rows;
	}

	/**
	 * Select accepts only values present in options; tampered input falls
	 * back to the first allowed value instead of erroring.
	 *
	 * @param mixed $raw     Submitted value.
	 * @param array $allowed option value => label map.
	 * @return string
	 */
	private function sanitize_select( $raw, array $allowed ) {
		$raw = (string) $raw;
		return array_key_exists( $raw, $allowed ) ? $raw : (string) array_key_first( $allowed );
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$requested = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation only.
		$active    = isset( $this->schema[ $requested ] ) ? $requested : array_key_first( $this->schema );
		?>
		<div class="wrap tt-wrap">
			<div class="tt-header">
				<h1>
					<span class="dashicons dashicons-store"></span>
					<?php esc_html_e( 'تنظیمات قالب بلکوکم', 'torantejarat' ); ?>
				</h1>
			</div>
			<nav class="tt-tabs" role="tablist" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'torantejarat' ); ?>">
				<?php foreach ( $this->schema as $key => $tab ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings&tab=' . $key ) ); ?>"
						class="tt-tab <?php echo $key === $active ? 'is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $key === $active ? 'true' : 'false'; ?>">
						<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<form method="post" action="options.php" class="tt-form" novalidate>
				<?php settings_fields( 'torantejarat_' . $active ); ?>
				<div class="tt-fields">
					<?php $this->render_fields( $active ); ?>
				</div>
				<div class="tt-footer">
					<?php submit_button( __( 'ذخیره تنظیمات', 'torantejarat' ), 'primary', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}

	private function render_fields( $tab_key ) {
		$fields = $this->schema[ $tab_key ]['fields'];
		$saved  = get_option( 'torantejarat_' . $tab_key, array() );
		foreach ( $fields as $key => $field ) {
			$value = isset( $saved[ $key ] ) ? $saved[ $key ] : $field['default'];
			$id    = 'tt-' . $tab_key . '-' . $key;
			$name  = 'torantejarat_' . $tab_key . '[' . $key . ']';
			$type  = $field['type'];
			if ( 'repeater' === $type ) {
				$this->render_repeater_field( $tab_key, $key, $field, $value );
				continue;
			}
			?>
			<div class="tt-field">
				<?php if ( 'checkbox' !== $type ) : ?>
				<label class="tt-label" for="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $field['label'] ); ?>
				</label>
				<?php endif; ?>
				<div class="tt-control">
					<?php if ( 'textarea' === $type || 'embed' === $type ) : ?>
						<textarea
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							rows="4"
							class="large-text"
						><?php echo esc_textarea( $value ); ?></textarea>
					<?php elseif ( 'checkbox' === $type ) : ?>
						<label class="tt-check">
							<input
								type="checkbox"
								id="<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( $name ); ?>"
								value="1"
								<?php checked( $value, 1 ); ?>
							>
							<?php echo esc_html( $field['label'] ); ?>
						</label>
					<?php elseif ( 'select' === $type && ! empty( $field['options'] ) && is_array( $field['options'] ) ) : ?>
						<select
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
						>
							<?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
								<option
									value="<?php echo esc_attr( $opt_val ); ?>"
									<?php selected( $value, $opt_val ); ?>
								>
									<?php echo esc_html( $opt_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<input
							type="<?php echo esc_attr( $type ); ?>"
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( (string) $value ); ?>"
							class="regular-text <?php echo 'url' === $type ? 'code' : ''; ?>"
						>
					<?php endif; ?>
					<?php if ( ! empty( $field['desc'] ) ) : ?>
						<p class="description">
							<?php echo esc_html( $field['desc'] ); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render a repeater field: a table of saved rows plus one trailing blank
	 * row, so a row can still be added with JavaScript disabled. The JS
	 * counterpart is assets/js/admin-settings.js (settings screen only).
	 *
	 * @param string $tab_key Tab the field belongs to.
	 * @param string $key     Field key.
	 * @param array  $field   Field definition; needs "subfields".
	 * @param mixed  $value   Stored rows.
	 */
	private function render_repeater_field( $tab_key, $key, array $field, $value ) {
		$subfields = isset( $field['subfields'] ) && is_array( $field['subfields'] ) ? $field['subfields'] : array();
		if ( ! $subfields ) {
			return;
		}
		$rows   = is_array( $value ) ? array_values( $value ) : array();
		$rows[] = array(); // Trailing blank row = the no-JavaScript way to add a row.
		$base   = 'torantejarat_' . $tab_key . '[' . $key . ']';
		?>
		<div class="tt-field tt-field-repeater" data-tt-repeater>
			<div class="tt-repeater-head">
				<span class="tt-label"><?php echo esc_html( $field['label'] ); ?></span>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="tt-control">
				<table class="widefat striped tt-repeater-table">
					<thead>
						<tr>
							<?php foreach ( $subfields as $sub ) : ?>
								<th scope="col"><?php echo esc_html( isset( $sub['label'] ) ? $sub['label'] : '' ); ?></th>
							<?php endforeach; ?>
							<th scope="col" class="tt-repeater-act"><span class="screen-reader-text"><?php esc_html_e( 'حذف ردیف', 'torantejarat' ); ?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $index => $row ) : ?>
							<?php $this->render_repeater_row( $base, (int) $index, $subfields, is_array( $row ) ? $row : array() ); ?>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p class="tt-repeater-actions">
					<button type="button" class="button" data-tt-repeater-add><?php esc_html_e( 'افزودن ردیف', 'torantejarat' ); ?></button>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * One repeater row. `data-tt-name` keeps an "%i%" placeholder so the admin
	 * script can re-number rows after an add or a remove.
	 *
	 * @param string $base      Field name prefix, without the row index.
	 * @param int    $index     Row index.
	 * @param array  $subfields Sub-field definitions.
	 * @param array  $row       Stored row values.
	 */
	private function render_repeater_row( $base, $index, array $subfields, array $row ) {
		?>
		<tr class="tt-repeater-row">
			<?php foreach ( $subfields as $sub_key => $sub ) : ?>
				<td>
					<?php
					$this->render_subfield_input(
						$base,
						$index,
						(string) $sub_key,
						$sub,
						isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : ''
					);
					?>
				</td>
			<?php endforeach; ?>
			<td class="tt-repeater-act">
				<button type="button" class="button-link tt-repeater-remove" data-tt-repeater-remove>
					<?php esc_html_e( 'حذف', 'torantejarat' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'حذف این ردیف', 'torantejarat' ); ?></span>
				</button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render one repeater sub-field input, reusing the same type vocabulary as
	 * flat fields (text | url | email | tel | textarea | embed | select | color | checkbox).
	 *
	 * @param string $base    Field name prefix, without the row index.
	 * @param int    $index   Row index.
	 * @param string $sub_key Sub-field key.
	 * @param array  $sub     Sub-field definition.
	 * @param mixed  $value   Stored value.
	 */
	private function render_subfield_input( $base, $index, $sub_key, array $sub, $value ) {
		$type = isset( $sub['type'] ) ? $sub['type'] : 'text';
		$name = $base . '[' . $index . '][' . $sub_key . ']';
		$tmpl = $base . '[%i%][' . $sub_key . ']';

		if ( 'textarea' === $type || 'embed' === $type ) {
			printf(
				'<textarea rows="3" name="%1$s" data-tt-name="%2$s">%3$s</textarea>',
				esc_attr( $name ),
				esc_attr( $tmpl ),
				esc_textarea( (string) $value )
			);
			return;
		}

		if ( 'checkbox' === $type ) {
			printf(
				'<label class="tt-check"><input type="checkbox" name="%1$s" data-tt-name="%2$s" value="1"%3$s></label>',
				esc_attr( $name ),
				esc_attr( $tmpl ),
				checked( $value, 1, false )
			);
			return;
		}

		if ( 'select' === $type && ! empty( $sub['options'] ) && is_array( $sub['options'] ) ) {
			$html = sprintf( '<select name="%1$s" data-tt-name="%2$s">', esc_attr( $name ), esc_attr( $tmpl ) );
			foreach ( $sub['options'] as $opt_val => $opt_label ) {
				$html .= sprintf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $opt_val ),
					selected( $value, $opt_val, false ),
					esc_html( $opt_label )
				);
			}
			echo $html . '</select>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
			return;
		}

		printf(
			'<input type="%1$s" name="%2$s" data-tt-name="%3$s" value="%4$s" placeholder="%5$s">',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $tmpl ),
			esc_attr( (string) $value ),
			esc_attr( isset( $sub['label'] ) ? $sub['label'] : '' )
		);
	}

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_torantejarat-settings' !== $hook ) {
			return;
		}
		wp_add_inline_style( 'wp-admin', $this->get_admin_css() );
		wp_enqueue_script(
			'tt-settings',
			get_template_directory_uri() . '/assets/js/admin-settings.js',
			array(),
			(string) wp_get_theme()->get( 'Version' ),
			true
		);
	}

	private function get_admin_css() {
		return '
			.tt-wrap { max-width: 860px; }
			.tt-header { display: flex; align-items: center; padding: 20px 0 0; border-bottom: 1px solid #dcdcde; margin-bottom: 0; }
			.tt-header h1 { display: flex; align-items: center; gap: 8px; font-size: 1.35rem; margin: 0 0 16px; }
			.tt-tabs { display: flex; gap: 2px; margin-bottom: 0; padding-top: 8px; }
			.tt-tab { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; text-decoration: none; color: #50575e; border: 1px solid transparent; border-bottom: none; border-radius: 3px 3px 0 0; font-size: .85rem; transition: color .15s; background: transparent; }
			.tt-tab:hover { color: #2271b1; }
			.tt-tab.is-active { color: #2271b1; font-weight: 600; background: #fff; border-color: #dcdcde; margin-bottom: -1px; padding-bottom: 10px; }
			.tt-form { background: #fff; border: 1px solid #dcdcde; border-top: none; border-radius: 0 0 3px 3px; padding: 24px; }
			.tt-fields { display: grid; gap: 0; }
			.tt-field { display: grid; grid-template-columns: 180px 1fr; gap: 16px; align-items: start; padding: 18px 0; border-bottom: 1px solid #f0f0f1; }
			.tt-field:last-child { border-bottom: none; }
			.tt-label { font-weight: 600; font-size: .875rem; padding-top: 6px; color: #1d2327; }
			.tt-control input, .tt-control textarea { width: 100% !important; }
			.tt-field-repeater { grid-template-columns: 1fr; gap: 8px; }
			.tt-repeater-head { display: flex; flex-direction: column; gap: 2px; }
			.tt-repeater-table { border-collapse: collapse; }
			.tt-repeater-table th { font-weight: 600; font-size: .8125rem; }
			.tt-repeater-table td { vertical-align: middle; }
			.tt-repeater-table input[type="text"], .tt-repeater-table input[type="url"], .tt-repeater-table input[type="email"], .tt-repeater-table input[type="tel"], .tt-repeater-table textarea, .tt-repeater-table select { width: 100% !important; }
			.tt-repeater-act { width: 64px; text-align: center; }
			.tt-repeater-remove { color: #b32d2e; }
			.tt-repeater-remove:hover { color: #8a2424; }
			.tt-repeater-actions { margin: 12px 0 0; }
			.tt-footer { margin-top: 20px; padding-top: 18px; border-top: 1px solid #f0f0f1; }
		';
	}
}

TT_Settings::get_instance();
