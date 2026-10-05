<?php
/**
 * Theme Settings Panel — توران‌تجارت
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
			),
		),
		'social'  => array(
			'label'  => 'شبکه‌های اجتماعی',
			'icon'   => 'dashicons-share',
			'fields' => array(
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
			__( 'تنظیمات قالب توران‌تجارت', 'torantejarat' ),
			__( 'توران‌تجارت', 'torantejarat' ),
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
			$raw  = isset( $input[ $key ] ) ? $input[ $key ] : '';
			$type = $field['type'];
			if ( 'url' === $type ) {
				$output[ $key ] = esc_url_raw( trim( $raw ) );
			} elseif ( 'email' === $type ) {
				$output[ $key ] = sanitize_email( $raw );
			} elseif ( 'textarea' === $type ) {
				$output[ $key ] = sanitize_textarea_field( $raw );
			} elseif ( 'tel' === $type ) {
				$output[ $key ] = preg_replace( '/[^0-9+]/', '', (string) $raw );
			} else {
				$callback        = isset( $field['sanitize'] ) ? $field['sanitize'] : 'sanitize_text_field';
				$output[ $key ] = call_user_func( $callback, $raw );
			}
		}
		return $output;
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
					<?php esc_html_e( 'تنظیمات قالب توران‌تجارت', 'torantejarat' ); ?>
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
			?>
			<div class="tt-field">
				<label class="tt-label" for="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $field['label'] ); ?>
				</label>
				<div class="tt-control">
					<?php if ( 'textarea' === $type ) : ?>
						<textarea
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							rows="4"
							class="large-text"
						><?php echo esc_textarea( $value ); ?></textarea>
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

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_torantejarat-settings' !== $hook ) {
			return;
		}
		wp_add_inline_style( 'wp-admin', $this->get_admin_css() );
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
			.tt-footer { margin-top: 20px; padding-top: 18px; border-top: 1px solid #f0f0f1; }
		';
	}
}

TT_Settings::get_instance();
