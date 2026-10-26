<?php
/**
 * Theme Settings Panel — بلکوکم
 * Native WordPress Settings API — No dependencies.
 *
 * To add a tab or field, edit the $schema array only. Scalar home defaults
 * are supplied by torantejarat_home_defaults() so panel and frontend copy
 * cannot diverge.
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
		'home'    => array(
			'label'  => 'صفحه اصلی',
			'icon'   => 'dashicons-admin-appearance',
			'fields' => array(
				'hero_kicker'        => array(
					'label'    => 'کیکر بالای تیتر',
					'type'     => 'text',
					'desc'     => 'خالی = متن پیش‌فرض قالب.',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_title_1'       => array(
					'label'    => 'تیتر، خط اول',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_title_2'       => array(
					'label'    => 'تیتر، خط دوم (رنگی)',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_lead'          => array(
					'label'    => 'متن توضیح',
					'type'     => 'textarea',
					'desc'     => '',
					'sanitize' => 'sanitize_textarea_field',
				),
				'hero_cta_primary'   => array(
					'label'    => 'دکمه اصلی',
					'type'     => 'text',
					'desc'     => 'مقصد این دکمه صفحهٔ فروشگاه است؛ فقط متن تغییر می‌کند.',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_cta_secondary' => array(
					'label'    => 'دکمه دوم',
					'type'     => 'text',
					'desc'     => 'مقصد این دکمه صفحهٔ راهنما (quiz) است.',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_foot_1'        => array(
					'label'    => 'نشانه پایین ۱',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'hero_foot_2'        => array(
					'label'    => 'نشانه پایین ۲',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'benefit_1'          => array(
					'label'    => 'مورد ۱',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'benefit_2'          => array(
					'label'    => 'مورد ۲',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'benefit_3'          => array(
					'label'    => 'مورد ۳',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'benefit_4'          => array(
					'label'    => 'مورد ۴',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'cat_eyebrow'        => array(
					'label'    => 'پیش‌تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'cat_title'          => array(
					'label'    => 'تیتر',
					'type'     => 'text',
					'desc'     => 'کارت‌های دسته‌بندی از خود ووکامرس می‌آیند و اینجا نیستند.',
					'sanitize' => 'sanitize_text_field',
				),
				'cat_text'           => array(
					'label'    => 'متن',
					'type'     => 'textarea',
					'desc'     => '',
					'sanitize' => 'sanitize_textarea_field',
				),
				'field_eyebrow'      => array(
					'label'    => 'پیش‌تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'field_title'        => array(
					'label'    => 'تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'field_text'         => array(
					'label'    => 'متن',
					'type'     => 'textarea',
					'desc'     => '',
					'sanitize' => 'sanitize_textarea_field',
				),
				'field_check_1'      => array(
					'label'    => 'چک‌لیست ۱',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'field_check_2'      => array(
					'label'    => 'چک‌لیست ۲',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'field_check_3'      => array(
					'label'    => 'چک‌لیست ۳',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'field_link_label'   => array(
					'label'    => 'متن لینک',
					'type'     => 'text',
					'desc'     => 'مقصد این لینک صفحهٔ تماس است.',
					'sanitize' => 'sanitize_text_field',
				),
				'video_label_1'      => array(
					'label'    => 'برچسب چپ (انگلیسی)',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'video_label_2'      => array(
					'label'    => 'برچسب راست (انگلیسی)',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'video_aria'         => array(
					'label'    => 'برچسب دسترس‌پذیری',
					'type'     => 'text',
					'desc'     => 'در aria-label ویدیو استفاده می‌شود؛ متن جایگزین ویدیو برای صفحه‌خوان است.',
					'sanitize' => 'sanitize_text_field',
				),
				'video_caption'      => array(
					'label'    => 'زیرنویس',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'blog_eyebrow'       => array(
					'label'    => 'پیش‌تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'blog_title'         => array(
					'label'    => 'تیتر',
					'type'     => 'text',
					'desc'     => 'کارت‌های مقاله از نوشته‌های واقعی می‌آیند و اینجا نیستند.',
					'sanitize' => 'sanitize_text_field',
				),
				'blog_text'          => array(
					'label'    => 'متن',
					'type'     => 'textarea',
					'desc'     => '',
					'sanitize' => 'sanitize_textarea_field',
				),
				'blog_link'          => array(
					'label'    => 'متن لینک',
					'type'     => 'text',
					'desc'     => 'مقصد این لینک صفحهٔ مجله است.',
					'sanitize' => 'sanitize_text_field',
				),
				'faq_eyebrow'        => array(
					'label'    => 'پیش‌تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'faq_title'          => array(
					'label'    => 'تیتر',
					'type'     => 'text',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
				),
				'faq_link'           => array(
					'label'    => 'متن لینک',
					'type'     => 'text',
					'desc'     => 'مقصد این لینک صفحهٔ تماس است.',
					'sanitize' => 'sanitize_text_field',
				),
				'faq'                => array(
					'label'            => 'پرسش‌های متداول',
					'type'             => 'repeater',
					'default'          => array(),
					'default_callback' => 'ToranTejarat\\Theme\\torantejarat_home_faq_defaults',
					'desc'             => 'خالی = سه پرسش پیش‌فرض قالب. هر ردیف به جای خودش گره خورده است: خالی گذاشتن یک فیلد، متن پیش‌فرض همان ردیف را برمی‌گرداند. ردیف چهارم به بعد به انتهای فهرست اضافه می‌شود. متن «حالت پیش‌نمایش» (تب عمومی) همیشه بعد از ردیف اول و فقط وقتی روشن باشد نمایش داده می‌شود.',
					'subfields'        => array(
						'q' => array( 'label' => 'پرسش', 'type' => 'text' ),
						'a' => array( 'label' => 'پاسخ', 'type' => 'textarea' ),
					),
					'max'              => 12,
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
		'footer'  => array(
			'label'  => 'فوتر',
			'icon'   => 'dashicons-info-outline',
			'fields' => array(
				'copyright_text' => array(
					'label'    => 'متن کپیرایت',
					'type'     => 'textarea',
					'default'  => '',
					'desc'     => 'خالی = نام سایت و توضیح پیش‌فرض نمایش داده می‌شود.',
					'sanitize' => 'sanitize_textarea_field',
				),
				'bar_text'       => array(
					'label'    => 'متن نوار پاسخ‌گویی',
					'type'     => 'text',
					'default'  => 'طراحی برای انتخاب آگاهانه',
					'desc'     => '',
					'sanitize' => 'sanitize_text_field',
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
					'default'  => '',
					'desc'     => 'اختیاری — فقط شهر واقعی را وارد کنید؛ خالی = بدون addressLocality در Schema.',
					'sanitize' => 'sanitize_text_field',
				),
				'price_range'   => array(
					'label'    => 'محدوده قیمت',
					'type'     => 'select',
					'default'  => '',
					'desc'     => 'اختیاری — فقط مقدار تأییدشده را انتخاب کنید؛ نامشخص = بدون priceRange در Schema.',
					'options'  => array(
						''     => 'نامشخص — در Schema نمایش داده نشود',
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

	/**
	 * Whether the shared frontend defaults have been injected into home fields.
	 *
	 * @var bool
	 */
	private $home_defaults_hydrated = false;

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

	/**
	 * Inject the canonical frontend copy into the admin schema once.
	 *
	 * This runs from admin_init/render paths, not the constructor: the defaults
	 * are translation-ready and must not force textdomain loading before init.
	 */
	private function hydrate_home_defaults() {
		if ( $this->home_defaults_hydrated ) {
			return;
		}
		$defaults = torantejarat_home_defaults();
		foreach ( $defaults as $key => $default ) {
			if ( isset( $this->schema['home']['fields'][ $key ] ) ) {
				$this->schema['home']['fields'][ $key ]['default'] = $default;
			}
		}
		$this->home_defaults_hydrated = true;
	}

	public function register_settings() {
		$this->hydrate_home_defaults();
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

	/**
	 * Sanitize a submitted tab.
	 *
	 * A form may render only part of a tab: every home sub-section is its own
	 * page, so the hero form posts eight fields out of thirty-four. Such a form
	 * declares what it rendered in a hidden `_tt_scope` field, and every key
	 * outside that scope is carried over from the stored option untouched —
	 * otherwise saving the hero page would wipe the stored trust items and the
	 * whole FAQ repeater.
	 *
	 * Without a `_tt_scope` marker the submitted keys are used as the scope:
	 * a partial payload (an old cached form, a script, a plugin) can no longer
	 * blank the keys it did not send. Clearing a field still works, because a
	 * rendered form always submits every field it shows — including the empty
	 * ones — and a full payload (all keys) behaves exactly as before.
	 *
	 * @param mixed  $input   Submitted value for the option.
	 * @param string $tab_key Tab key.
	 * @return array
	 */
	private function sanitize_tab( $input, $tab_key ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$fields = isset( $this->schema[ $tab_key ]['fields'] ) ? $this->schema[ $tab_key ]['fields'] : array();
		$stored = get_option( 'torantejarat_' . $tab_key, array() );
		$stored = is_array( $stored ) ? $stored : array();

		$scope = null;
		if ( isset( $input['_tt_scope'] ) && is_array( $input['_tt_scope'] ) ) {
			$scope = array_map( 'strval', $input['_tt_scope'] );
		} elseif ( isset( $input['_tt_scope'] ) && is_string( $input['_tt_scope'] ) ) {
			$scope = array_map( 'trim', explode( ',', $input['_tt_scope'] ) );
		}

		if ( null === $scope ) {
			// No marker: the scope is exactly what was submitted, so a partial
			// payload cannot empty the keys it left out.
			$scope = array();
			foreach ( $fields as $key => $field ) {
				if ( array_key_exists( $key, $input ) ) {
					$scope[] = (string) $key;
				}
			}
		}

		$output = array();
		foreach ( $fields as $key => $field ) {
			if ( ! in_array( (string) $key, $scope, true ) ) {
				// Not sent by this form: keep whatever is stored.
				if ( array_key_exists( $key, $stored ) ) {
					$output[ $key ] = $stored[ $key ];
				}
				continue;
			}
			$raw            = isset( $input[ $key ] ) ? $input[ $key ] : '';
			$output[ $key ] = ( 'repeater' === $field['type'] )
				? $this->sanitize_repeater( $raw, $field, array_key_exists( $key, $stored ) ? $stored[ $key ] : array() )
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
	 * Whether a field value counts as "not filled in".
	 *
	 * Used only by the optional default_callback path; a flat field is blank
	 * when it is the empty string, a repeater is blank when it has no rows.
	 *
	 * @param mixed $value Raw field value.
	 * @return bool
	 */
	private static function value_is_blank( $value ) {
		if ( is_array( $value ) ) {
			return ! $value;
		}
		return '' === (string) $value;
	}

	/**
	 * Sanitize a repeater value.
	 *
	 * Every row is filtered sub-field by sub-field with the same rules as a
	 * flat field, rows that end up completely empty are dropped, and the list
	 * is re-indexed so the stored option is always a clean 0..n array. A
	 * declared "max" is enforced here too, not only in the browser. Existing
	 * legacy overflow is allowed to shrink or stay unchanged, but cannot grow;
	 * this avoids silently deleting old rows merely by saving the form.
	 *
	 * @param mixed $raw    Submitted rows.
	 * @param array $field  Repeater definition; needs "subfields".
	 * @param mixed $stored Currently stored rows, used only for legacy overflow.
	 * @return array
	 */
	private function sanitize_repeater( $raw, array $field, $stored = array() ) {
		$subfields = isset( $field['subfields'] ) && is_array( $field['subfields'] ) ? $field['subfields'] : array();
		if ( ! $subfields || ! is_array( $raw ) ) {
			return array();
		}
		$max          = isset( $field['max'] ) ? (int) $field['max'] : 0;
		$stored_count = is_array( $stored ) ? count( $stored ) : 0;
		$limit        = $max > 0 ? max( $max, $stored_count ) : 0;
		$rows         = array();
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
				if ( $limit > 0 && count( $rows ) >= $limit ) {
					break;
				}
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
		$this->hydrate_home_defaults();
		$requested = '';
		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation only.
			$requested = sanitize_key( $_GET['tab'] );
		} elseif ( isset( $_GET['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation only.
			$requested = sanitize_key( $_GET['section'] );
		}
		$active = isset( $this->schema[ $requested ] ) ? $requested : array_key_first( $this->schema );

		$nav    = torantejarat_panel_nav( $this->schema );
		$groups = isset( $nav[ $active ]['groups'] ) ? $nav[ $active ]['groups'] : array();
		// Only a tab that really has sub-sections is split into separate pages.
		$split = count( $groups ) > 1;

		$sub = isset( $_GET['sub'] ) ? sanitize_key( $_GET['sub'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page navigation only.

		$active_group = null;
		if ( $split ) {
			foreach ( $groups as $group ) {
				if ( $group['id'] === $sub ) {
					$active_group = $group;
					break;
				}
			}
		} else {
			$sub = '';
		}

		// A sub-page renders its own fields only; without a sub a split tab shows
		// its section index, and a flat tab keeps rendering one single form.
		if ( $active_group ) {
			$ordered = $active_group['fields'];
		} elseif ( $split ) {
			$ordered = array();
		} else {
			$ordered = array_keys( $this->schema[ $active ]['fields'] );
		}

		$ready   = torantejarat_panel_readiness();
		$version = (string) wp_get_theme()->get( 'Version' );
		?>
		<div class="wrap tt-wrap">
			<div class="tt-header">
				<h1>
					<span class="dashicons dashicons-store"></span>
					<?php esc_html_e( 'تنظیمات قالب بلکوکم', 'torantejarat' ); ?>
					<span class="tt-version"><?php echo esc_html( 'v' . $version ); ?></span>
				</h1>
				<div class="tt-search" data-tt-search>
					<label class="screen-reader-text" for="tt-search-input">
						<?php esc_html_e( 'جست‌وجو در تنظیمات', 'torantejarat' ); ?>
					</label>
					<input
						type="search"
						id="tt-search-input"
						class="tt-search-input"
						role="combobox"
						aria-expanded="false"
						aria-controls="tt-search-results"
						aria-autocomplete="list"
						autocomplete="off"
						placeholder="<?php esc_attr_e( 'جست‌وجو در تنظیمات…', 'torantejarat' ); ?>"
					>
					<ul class="tt-search-results" id="tt-search-results" role="listbox" hidden></ul>
				</div>
			</div>
			<div class="tt-readiness" role="status" data-tt-readiness>
				<?php if ( ! $ready ) : ?>
					<span class="tt-ready-ok">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'آماده انتشار', 'torantejarat' ); ?>
					</span>
				<?php else : ?>
					<span class="tt-ready-title">
						<span class="dashicons dashicons-warning"></span>
						<?php
						printf(
							/* translators: %s: number of open items. */
							esc_html__( '%s مورد باز پیش از انتشار', 'torantejarat' ),
							esc_html( number_format_i18n( count( $ready ) ) )
						);
						?>
					</span>
					<ul class="tt-ready-list">
						<?php foreach ( $ready as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( torantejarat_panel_readiness_url( $item ) ); ?>">
									<?php echo esc_html( $item['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="tt-layout">
				<nav class="tt-sidebar" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'torantejarat' ); ?>">
					<ul class="tt-nav">
						<?php foreach ( $nav as $key => $tab ) : ?>
							<?php
							$tab_groups = $tab['groups'];
							$is_open    = ( $key === $active );
							$base_url   = 'admin.php?page=torantejarat-settings&section=' . $key;
							?>
							<li class="tt-nav-item <?php echo $is_open ? 'is-active' : ''; ?>">
								<a class="tt-nav-link" href="<?php echo esc_url( admin_url( $base_url ) ); ?>">
									<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
									<span class="tt-nav-text"><?php echo esc_html( $tab['label'] ); ?></span>
								</a>
								<?php if ( $is_open && count( $tab_groups ) > 1 ) : ?>
									<ul class="tt-subnav">
										<?php foreach ( $tab_groups as $group ) : ?>
											<li>
												<a class="tt-subnav-link <?php echo $group['id'] === $sub ? 'is-active' : ''; ?>"
													href="<?php echo esc_url( admin_url( $base_url . '&sub=' . $group['id'] ) ); ?>">
													<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>"></span>
													<span class="tt-subnav-text"><?php echo esc_html( $group['label'] ); ?></span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
				<main class="tt-main">
					<?php if ( ! $ordered ) : ?>
						<?php $this->render_overview( $active, $nav[ $active ] ); ?>
					<?php else : ?>
						<form method="post" action="options.php" class="tt-form" novalidate>
							<?php settings_fields( 'torantejarat_' . $active ); ?>
							<div class="tt-fields">
								<?php $this->render_fields( $active, $ordered, $active_group ); ?>
							</div>
							<div class="tt-footer">
								<?php submit_button( __( 'ذخیره تنظیمات', 'torantejarat' ), 'primary', 'submit', false ); ?>
								<span class="tt-saved-hint description">
									<?php esc_html_e( 'خالی گذاشتن هر فیلد یعنی برگشت به متن پیش‌فرض قالب.', 'torantejarat' ); ?>
								</span>
							</div>
						</form>
					<?php endif; ?>
					<?php if ( 'general' === $active ) : ?>
						<?php $this->render_publish_mode(); ?>
					<?php endif; ?>
				</main>
			</div>
		</div>
		<?php
	}

	/**
	 * Publish-mode card on the General tab.
	 *
	 * Display-only status plus a separate admin-post form. It never enters the
	 * Settings API form, so $schema, sanitize and the stored option stay untouched.
	 */
	private function render_publish_mode() {
		$coming_soon = (string) get_option( 'woocommerce_coming_soon', 'no' );
		$blog_public = (string) get_option( 'blog_public', '1' );
		$is_public   = ( 'no' === $coming_soon && '1' === $blog_public );
		$is_preview  = ( 'yes' === $coming_soon && '0' === $blog_public );
		$mode_label  = $is_public ? __( 'عمومی', 'torantejarat' ) : ( $is_preview ? __( 'پیش‌نمایش', 'torantejarat' ) : __( 'ناهمخوان', 'torantejarat' ) );
		$selected    = $is_public ? 'public' : 'preview';

		$notice  = isset( $_GET['tt_publish'] ) ? sanitize_key( wp_unslash( $_GET['tt_publish'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Status display only.
		$notices = array(
			'public'  => array( 'success', __( 'حالت عمومی فعال شد.', 'torantejarat' ) ),
			'preview' => array( 'success', __( 'حالت پیش‌نمایش فعال شد.', 'torantejarat' ) ),
			'email'   => array( 'error', __( 'برای رفتن به حالت عمومی، ابتدا ایمیل‌های نمونه را اصلاح کنید.', 'torantejarat' ) ),
			'confirm' => array( 'error', __( 'برای رفتن به حالت عمومی، تأیید لازم است.', 'torantejarat' ) ),
			'invalid' => array( 'error', __( 'حالت انتخاب‌شده معتبر نیست.', 'torantejarat' ) ),
		);
		?>
		<section class="tt-publish-mode" id="tt-publish-mode">
			<h2 class="tt-group-title">
				<span class="dashicons dashicons-visibility"></span>
				<?php esc_html_e( 'حالت انتشار', 'torantejarat' ); ?>
			</h2>
			<?php if ( isset( $notices[ $notice ] ) ) : ?>
				<div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?>">
					<p><?php echo esc_html( $notices[ $notice ][1] ); ?></p>
				</div>
			<?php endif; ?>
			<p class="description">
				<?php esc_html_e( 'این وضعیت از دو گزینهٔ بومی وردپرس/ووکامرس خوانده می‌شود و در تنظیمات عمومی قالب ذخیره نمی‌شود.', 'torantejarat' ); ?>
			</p>
			<div class="tt-field">
				<span class="tt-label"><?php esc_html_e( 'وضعیت فعلی', 'torantejarat' ); ?></span>
				<div class="tt-control">
					<p>
						<strong><?php echo esc_html( $mode_label ); ?></strong>
						— <?php echo esc_html( sprintf( 'woocommerce_coming_soon=%s / blog_public=%s', $coming_soon, $blog_public ) ); ?>
					</p>
				</div>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="tt-form">
				<input type="hidden" name="action" value="torantejarat_publish_mode">
				<?php wp_nonce_field( 'torantejarat_publish_mode' ); ?>
				<fieldset>
					<legend class="tt-label"><?php esc_html_e( 'حالت مقصد', 'torantejarat' ); ?></legend>
					<label>
						<input type="radio" name="mode" value="preview" <?php checked( 'preview' === $selected ); ?>>
						<?php esc_html_e( 'پیش‌نمایش — فروشگاه به‌زودی، سایت noindex', 'torantejarat' ); ?>
					</label>
					<br>
					<label>
						<input type="radio" name="mode" value="public" <?php checked( 'public' === $selected ); ?>>
						<?php esc_html_e( 'عمومی — فروشگاه باز، سایت ایندکس', 'torantejarat' ); ?>
					</label>
				</fieldset>
				<p>
					<label>
						<input type="checkbox" name="confirm" value="yes">
						<?php esc_html_e( 'تأیید می‌کنم که برای حالت عمومی، ایمیل‌ها واقعی هستند.', 'torantejarat' ); ?>
					</label>
				</p>
				<p class="tt-footer">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'اعمال حالت', 'torantejarat' ); ?></button>
				</p>
			</form>
		</section>
		<?php
	}

	/**
	 * Section index: the list of sub-sections of a split tab. Each entry links
	 * to its own page; no form is rendered here, so nothing can be saved from
	 * this screen.
	 *
	 * @param string $tab_key Tab key.
	 * @param array  $tab     Entry from torantejarat_panel_nav().
	 */
	private function render_overview( $tab_key, array $tab ) {
		$base = 'admin.php?page=torantejarat-settings&section=' . $tab_key;
		?>
		<div class="tt-overview">
			<h2 class="tt-overview-title">
				<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
				<?php echo esc_html( $tab['label'] ); ?>
			</h2>
			<p class="tt-overview-lead">
				<?php esc_html_e( 'هر زیربخش صفحهٔ جداگانه دارد؛ ذخیره در هر صفحه فقط همان فیلدها را می‌نویسد و بقیهٔ مقادیر دست‌نخورده می‌مانند.', 'torantejarat' ); ?>
			</p>
			<ul class="tt-cards">
				<?php foreach ( $tab['groups'] as $group ) : ?>
					<li>
						<a class="tt-card-link" href="<?php echo esc_url( admin_url( $base . '&sub=' . $group['id'] ) ); ?>">
							<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>"></span>
							<span class="tt-card-title"><?php echo esc_html( $group['label'] ); ?></span>
							<span class="tt-card-meta">
								<?php
								printf(
									/* translators: %s: number of fields in the sub-section. */
									esc_html__( '%s فیلد', 'torantejarat' ),
									esc_html( number_format_i18n( count( $group['fields'] ) ) )
								);
								?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render one page's fields.
	 *
	 * A tab with sub-sections is split across pages, so this renders only the
	 * fields of the active page and declares them in hidden `_tt_scope` markers.
	 * The sanitizer uses that list to leave every other stored key untouched —
	 * saving the hero page must not wipe the trust items or the FAQ repeater.
	 *
	 * @param string     $tab_key Tab key.
	 * @param array      $ordered Field keys to render, in order.
	 * @param array|null $group   Sub-section being rendered, if any.
	 */
	private function render_fields( $tab_key, array $ordered, $group = null ) {
		$fields = $this->schema[ $tab_key ]['fields'];
		$saved  = get_option( 'torantejarat_' . $tab_key, array() );

		$ordered = array_values(
			array_filter(
				$ordered,
				static function ( $field_key ) use ( $fields ) {
					return isset( $fields[ $field_key ] );
				}
			)
		);
		if ( ! $ordered ) {
			$ordered = array_keys( $fields );
		}

		// Declare which keys this form owns.
		foreach ( $ordered as $scope_key ) {
			echo '<input type="hidden" name="' . esc_attr( 'torantejarat_' . $tab_key . '[_tt_scope][]' ) . '" value="' . esc_attr( (string) $scope_key ) . '">';
		}

		if ( is_array( $group ) ) {
			?>
			<section class="tt-group">
				<h2 class="tt-group-title">
					<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>"></span>
					<?php echo esc_html( $group['label'] ); ?>
				</h2>
			<?php
		}

		foreach ( $ordered as $key ) {
			$field = $fields[ $key ];

			$value = isset( $saved[ $key ] ) ? $saved[ $key ] : $field['default'];
			// Optional: a field may supply a callback for its blank state when
			// the default cannot be a constant expression — e.g. a repeater
			// whose built-in rows live in a theme function. This keeps the
			// panel showing the same rows the frontend falls back to.
			if ( ! empty( $field['default_callback'] ) && is_callable( $field['default_callback'] ) && self::value_is_blank( $value ) ) {
				$value = call_user_func( $field['default_callback'] );
			}
			$id    = 'tt-' . $tab_key . '-' . $key;
			$name  = 'torantejarat_' . $tab_key . '[' . $key . ']';
			$type  = $field['type'];
			if ( 'repeater' === $type ) {
				$this->render_repeater_field( $tab_key, $key, $field, $value );
				continue;
			}
			?>
			<div class="tt-field" id="ttf-<?php echo esc_attr( $tab_key . '-' . $key ); ?>">
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
		if ( is_array( $group ) ) {
			echo '</section>';
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
		$rows = is_array( $value ) ? array_values( $value ) : array();
		$max  = isset( $field['max'] ) ? (int) $field['max'] : 0;
		if ( $max < 1 || count( $rows ) < $max ) {
			$rows[] = array(); // Trailing blank row = the no-JavaScript way to add a row.
		}
		$base = 'torantejarat_' . $tab_key . '[' . $key . ']';
		?>
		<div class="tt-field tt-field-repeater" id="ttf-<?php echo esc_attr( $tab_key . '-' . $key ); ?>" data-tt-repeater data-tt-max="<?php echo esc_attr( (string) $max ); ?>">
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
				<?php if ( $max > 0 ) : ?>
					<?php
					$guide = sprintf(
						/* translators: %s: maximum number of rows. */
						__( 'حداکثر %s ردیف. برای افزودن بیشتر، ابتدا یک ردیف را حذف کنید.', 'torantejarat' ),
						number_format_i18n( (int) $max )
					);
					$limit = __( 'به سقف رسیدید؛ برای افزودن ردیف تازه، ابتدا یک ردیف را حذف کنید.', 'torantejarat' );
					?>
					<p class="tt-repeater-note" data-tt-repeater-note
						data-tt-text-guide="<?php echo esc_attr( $guide ); ?>"
						data-tt-text-limit="<?php echo esc_attr( $limit ); ?>">
						<?php echo esc_html( $guide ); ?>
					</p>
				<?php endif; ?>
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
		$this->hydrate_home_defaults();
		// Styling lives in a real stylesheet (assets/admin/panel.css), scoped to
		// this screen only. filemtime() busts the browser cache while the file
		// is still being iterated on.
		$css_path = get_template_directory() . '/assets/admin/panel.css';
		wp_enqueue_style(
			'tt-panel',
			get_template_directory_uri() . '/assets/admin/panel.css',
			array(),
			file_exists( $css_path ) ? (string) filemtime( $css_path ) : (string) wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_script(
			'tt-settings',
			get_template_directory_uri() . '/assets/js/admin-settings.js',
			array(),
			(string) wp_get_theme()->get( 'Version' ),
			true
		);
		// Panel-wide search. The index is generated server-side from $schema and
		// the nav map (inc/settings/panel-nav.php) — the script never discovers
		// fields from the DOM and never searches stored values.
		$search_path = get_template_directory() . '/assets/admin/panel-search.js';
		wp_enqueue_script(
			'tt-panel-search',
			get_template_directory_uri() . '/assets/admin/panel-search.js',
			array(),
			file_exists( $search_path ) ? (string) filemtime( $search_path ) : (string) wp_get_theme()->get( 'Version' ),
			true
		);
		wp_localize_script(
			'tt-panel-search',
			'ttPanelSearch',
			array(
				'baseUrl'   => admin_url( 'admin.php?page=torantejarat-settings' ),
				'noResults' => __( 'موردی یافت نشد', 'torantejarat' ),
				'index'     => torantejarat_panel_search_index( $this->schema ),
			)
		);
	}

}

TT_Settings::get_instance();
