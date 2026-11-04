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
			'label'  => 'عمومی و انتشار',
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
					'control'  => 'segments',
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
				'hero_slides'      => array(
					'label'            => 'اسلایدر هیرو — محصولات گلچین',
					'type'             => 'repeater',
					'control'          => 'product-picker',
					'row_label'        => 'اسلاید %s',
					'default'          => array(),
					'desc'             => 'محصول را جستجو و انتخاب کنید؛ نام، کپشن و لینک هر اسلاید به‌صورت خودکار از مشخصات همان محصول خوانده می‌شود. حداکثر ۴ اسلاید نمایش داده می‌شود.',
					'subfields'        => array(
						'product_id' => array( 'label' => 'شناسه محصول (ID)', 'type' => 'number' ),
						'label'      => array( 'label' => 'لیبل نمایشی', 'type' => 'text' ),
						'caption'    => array( 'label' => 'کپشن کوتاه', 'type' => 'text' ),
						'link'       => array( 'label' => 'لینک دلخواه (اختیاری)', 'type' => 'url' ),
					),
					'max'              => 4,
				),
				'home_videos'      => array(
					'label'            => 'ویدیوهای صفحه اصلی',
					'type'             => 'repeater',
					'control'          => 'video-picker',
					'row_label'        => 'ویدیو %s',
					'default'          => array(),
					'desc'             => 'هر ردیف یک ویدیو است. فایل را از کتابخانه رسانه انتخاب کنید (یا لینک مستقیم mp4/webm بدهید)؛ تیتر و کپشن اختیاری‌اند. ردیف خالی نادیده گرفته می‌شود.',
					'subfields'        => array(
						'title'    => array( 'label' => 'تیتر ویدیو', 'type' => 'text' ),
						'video_url'=> array( 'label' => 'لینک فایل ویدیو', 'type' => 'url' ),
						'caption'  => array( 'label' => 'کپشن', 'type' => 'text' ),
					),
					'max'              => 6,
				),
			),
		),
		'contact' => array(
			'label'  => 'اطلاعات تماس',
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
			'label'  => 'ورود و دسترسی',
			'icon'   => 'dashicons-lock',
			'fields' => array(
				'login_logo'            => array(
					'label'    => 'لوگوی صفحه ورود (URL)',
					'type'     => 'url',
					'default'  => '',
					'desc'     => 'انتخاب از کتابخانه یا URL دستی؛ پاک‌کردن انتخاب فایل رسانه را حذف نمی‌کند. خالی = لوگوی سفارشی‌ساز، بعد آیکون قالب.',
					'control'  => 'media-url',
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
					'control'  => 'color-hex',
					'sanitize' => 'sanitize_hex_color',
				),
				'login_accent'          => array(
					'label'    => 'رنگ دکمه و لینک‌ها',
					'type'     => 'color',
					'default'  => '#f77b3e',
					'desc'     => '',
					'control'  => 'color-hex',
					'sanitize' => 'sanitize_hex_color',
				),
				'login_hide_register'   => array(
					'label'    => 'مخفی کردن لینک ثبت‌نام',
					'type'     => 'checkbox',
					'default'  => 0,
					'desc'     => '',
					'control'  => 'switch',
					'sanitize' => 'absint',
				),
				'login_hide_lostpassword' => array(
					'label'    => 'مخفی کردن «رمز را فراموش کرده‌ام»',
					'type'     => 'checkbox',
					'default'  => 0,
					'desc'     => 'توصیه نمی‌شود — فقط اگر ورود فقط با پیامک است.',
					'control'  => 'switch',
					'sanitize' => 'absint',
				),
			),
		),
		'business' => array(
			'label'  => 'کسب‌وکار و Schema',
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
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 50 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_ajax_torantejarat_product_search', array( $this, 'ajax_search_products' ) );
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
	 * Add a quick "Theme Settings" button to the WordPress admin bar
	 * with a gear icon, linking to the theme settings page.
	 */
	public function admin_bar_menu( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$wp_admin_bar->add_node( array(
			'id'    => 'torantejarat-settings',
			'title' => '<span class="dashicons dashicons-settings" aria-hidden="true"></span> ' . __( 'تنظیمات قالب', 'torantejarat' ),
			'href'  => admin_url( 'admin.php?page=torantejarat-settings' ),
			'meta'  => array(
				'title' => __( 'تنظیمات قالب بلکوکم', 'torantejarat' ),
			),
		) );
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

		// The dashboard is a view, not an eighth option group. Keep utility
		// routes out of the schema so they can never register or save a field.
		$is_dashboard = ( '' === $requested || 'dashboard' === $requested );
		$active       = $is_dashboard ? 'dashboard' : ( isset( $this->schema[ $requested ] ) ? $requested : array_key_first( $this->schema ) );
		$nav          = torantejarat_panel_nav( $this->schema );
		$utility_map  = torantejarat_panel_utility_map();
		$groups       = ! $is_dashboard && isset( $nav[ $active ]['groups'] ) ? $nav[ $active ]['groups'] : array();
		$utilities    = ! $is_dashboard && isset( $utility_map[ $active ] ) ? $utility_map[ $active ] : array();

		// Only real field groups split a tab into field sub-pages. Utility pages
		// have their own renderer and never fall back to all fields in the tab.
		$split = count( $groups ) > 1;
		$sub   = ! $is_dashboard && isset( $_GET['sub'] ) ? sanitize_key( $_GET['sub'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page navigation only.

		$active_group   = null;
		$active_utility = null;
		if ( ! $is_dashboard ) {
			if ( $split ) {
				foreach ( $groups as $group ) {
					if ( $group['id'] === $sub ) {
						$active_group = $group;
						break;
					}
				}
			}
			if ( ! $active_group ) {
				foreach ( $utilities as $utility ) {
					if ( $utility['id'] === $sub ) {
						$active_utility = $utility;
						break;
					}
				}
			}
			if ( ! $split && ! $active_utility ) {
				$sub = '';
			} elseif ( $split && ! $active_group && ! $active_utility ) {
				$sub = '';
			}
		}

		if ( $is_dashboard || $active_utility ) {
			$ordered = array();
		} elseif ( $active_group ) {
			$ordered = $active_group['fields'];
		} elseif ( $split ) {
			$ordered = array();
		} else {
			$ordered = array_keys( $this->schema[ $active ]['fields'] );
		}

		$ready          = torantejarat_panel_readiness();
		$version        = (string) wp_get_theme()->get( 'Version' );
		$current_group  = $active_group ? $active_group : $active_utility;
		$nav_groups     = array_merge( $groups, $utilities );
		$has_form       = ! $is_dashboard && ! $active_utility && ! empty( $ordered );
		$has_dirty_form = $has_form || ( $active_utility && 'publish' === $active_utility['id'] );
		$form_id        = 'tt-form-' . $active . ( $sub ? '-' . $sub : '' );
		$page_heading   = $current_group;
		if ( ! $page_heading && ! $split && isset( $groups[0] ) ) {
			$page_heading = $groups[0];
		}
		$heading_label = $page_heading ? $page_heading['label'] : ( ! $is_dashboard && isset( $nav[ $active ]['label'] ) ? $nav[ $active ]['label'] : __( 'داشبورد', 'torantejarat' ) );
		$heading_icon  = $page_heading ? $page_heading['icon'] : ( ! $is_dashboard && isset( $nav[ $active ]['icon'] ) ? $nav[ $active ]['icon'] : 'dashicons-dashboard' );
		$heading_desc  = $page_heading && ! empty( $page_heading['desc'] ) ? $page_heading['desc'] : '';
		?>
		<div class="wrap tt-wrap tt-panel" data-tt-panel>
			<header class="tt-header">
				<div class="tt-header-main">
					<div class="tt-brand">
						<span class="tt-brand-mark dashicons dashicons-store" aria-hidden="true"></span>
						<div>
							<h1>
								<?php esc_html_e( 'تنظیمات قالب بلکوکم', 'torantejarat' ); ?>
								<span class="tt-version"><?php echo esc_html( 'v' . $version ); ?></span>
							</h1>
							<p><?php esc_html_e( 'مدیریت ظاهر و محتوای قالب، کنار ابزارهای بومی فروشگاه', 'torantejarat' ); ?></p>
						</div>
					</div>
					<div class="tt-search" data-tt-search>
						<span class="dashicons dashicons-search" aria-hidden="true"></span>
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
					<div class="tt-header-actions">
						<a class="button tt-view-site" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="dashicons dashicons-external" aria-hidden="true"></span>
							<span class="tt-view-label"><?php esc_html_e( 'مشاهده سایت', 'torantejarat' ); ?></span>
						</a>
						<?php if ( $has_form ) : ?>
							<button type="submit" class="button button-primary tt-save-top" form="<?php echo esc_attr( $form_id ); ?>">
								<?php esc_html_e( 'ذخیره تنظیمات', 'torantejarat' ); ?>
							</button>
						<?php endif; ?>
						<button type="button" class="button tt-nav-toggle" data-tt-nav-toggle aria-controls="tt-sidebar-shell" aria-expanded="false" hidden>
							<span class="dashicons dashicons-menu" aria-hidden="true"></span>
							<?php esc_html_e( 'بخش‌ها', 'torantejarat' ); ?>
						</button>
					</div>
				</div>
				<div class="tt-contextbar">
					<nav class="tt-context-path" aria-label="<?php esc_attr_e( 'مسیر صفحهٔ فعلی', 'torantejarat' ); ?>">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings' ) ); ?>" class="tt-breadcrumb-link"<?php if ( $is_dashboard ) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e( 'بلکوکم', 'torantejarat' ); ?></a>
						<span aria-hidden="true">/</span>
						<?php if ( $is_dashboard ) : ?>
							<span class="tt-breadcrumb-current"><?php esc_html_e( 'داشبورد', 'torantejarat' ); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings&section=' . $active ) ); ?>" class="tt-breadcrumb-link"<?php if ( ! $current_group ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $nav[ $active ]['label'] ); ?></a>
							<?php if ( $current_group ) : ?>
								<span aria-hidden="true">/</span>
								<span class="tt-breadcrumb-current"><?php echo esc_html( $current_group['label'] ); ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</nav>
					<?php if ( ( ! $is_dashboard && $current_group ) || $has_dirty_form ) : ?>
						<div class="tt-context-tools">
							<?php if ( ! $is_dashboard && $current_group ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings&section=' . $active ) ); ?>" class="button tt-back-button">
									<span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
									<span><?php esc_html_e( 'بازگشت به بخش', 'torantejarat' ); ?></span>
								</a>
							<?php endif; ?>
							<?php if ( $has_dirty_form ) : ?>
								<span
									class="tt-dirty-status"
									data-tt-dirty-status
									data-tt-clean-label="<?php esc_attr_e( 'بدون تغییر ذخیره‌نشده', 'torantejarat' ); ?>"
									data-tt-dirty-label="<?php esc_attr_e( 'تغییرات ذخیره‌نشده دارید', 'torantejarat' ); ?>"
									role="status"
									aria-live="polite"
								><?php esc_html_e( 'بدون تغییر ذخیره‌نشده', 'torantejarat' ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</header>
			<div class="tt-readiness" data-tt-readiness>
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
				<button type="button" class="tt-nav-overlay" data-tt-nav-close tabindex="-1" hidden>
					<span class="screen-reader-text"><?php esc_html_e( 'بستن فهرست بخش‌ها', 'torantejarat' ); ?></span>
				</button>
				<aside class="tt-sidebar-shell" id="tt-sidebar-shell" data-tt-nav-shell>
					<div class="tt-sidebar-head">
						<span><?php esc_html_e( 'بخش‌های مدیریت', 'torantejarat' ); ?></span>
						<button type="button" class="button tt-sidebar-close" data-tt-nav-close hidden>
							<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
							<?php esc_html_e( 'بستن', 'torantejarat' ); ?>
						</button>
					</div>
					<nav class="tt-sidebar" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'torantejarat' ); ?>">
						<ul class="tt-nav">
							<li class="tt-nav-item <?php echo $is_dashboard ? 'is-active' : ''; ?>">
								<a class="tt-nav-link" href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings' ) ); ?>"<?php if ( $is_dashboard ) : ?> aria-current="page"<?php endif; ?>>
									<span class="dashicons dashicons-dashboard" aria-hidden="true"></span>
									<span class="tt-nav-text"><?php esc_html_e( 'داشبورد', 'torantejarat' ); ?></span>
								</a>
							</li>
							<?php foreach ( $nav as $key => $tab ) : ?>
								<?php
								$is_open  = ( $key === $active );
								$base_url = 'admin.php?page=torantejarat-settings&section=' . $key;
								?>
								<li class="tt-nav-item <?php echo $is_open ? 'is-active' : ''; ?>">
									<a class="tt-nav-link" href="<?php echo esc_url( admin_url( $base_url ) ); ?>"<?php if ( $is_open && ! $current_group ) : ?> aria-current="page"<?php endif; ?>>
										<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
										<span class="tt-nav-text"><?php echo esc_html( $tab['label'] ); ?></span>
									</a>
									<?php if ( $is_open && count( $nav_groups ) > 1 ) : ?>
										<ul class="tt-subnav">
											<?php foreach ( $nav_groups as $group ) : ?>
												<?php $is_current = $current_group && $group['id'] === $current_group['id']; ?>
												<li>
													<a class="tt-subnav-link <?php echo $is_current ? 'is-active' : ''; ?>"
														href="<?php echo esc_url( admin_url( $base_url . '&sub=' . $group['id'] ) ); ?>"<?php if ( $is_current ) : ?> aria-current="page"<?php endif; ?>>
														<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>" aria-hidden="true"></span>
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
				</aside>
				<main class="tt-main" id="tt-main">
					<?php if ( $is_dashboard ) : ?>
						<?php $this->render_dashboard( $nav ); ?>
					<?php elseif ( $active_utility ) : ?>
						<?php $this->render_publish_mode(); ?>
					<?php elseif ( ! $ordered ) : ?>
						<?php $this->render_overview( $active, $nav[ $active ], $utilities ); ?>
					<?php else : ?>
						<div class="tt-page-heading">
							<h2 id="tt-page-title">
								<span class="dashicons <?php echo esc_attr( $heading_icon ); ?>" aria-hidden="true"></span>
								<?php echo esc_html( $heading_label ); ?>
							</h2>
							<?php if ( $heading_desc ) : ?>
								<p><?php echo esc_html( $heading_desc ); ?></p>
							<?php endif; ?>
						</div>
						<form method="post" action="options.php" class="tt-form" id="<?php echo esc_attr( $form_id ); ?>" data-tt-dirty novalidate>
							<?php settings_fields( 'torantejarat_' . $active ); ?>
							<div class="tt-fields">
								<?php $this->render_fields( $active, $ordered ); ?>
							</div>
							<div class="tt-footer">
								<?php submit_button( __( 'ذخیره تنظیمات', 'torantejarat' ), 'primary', 'submit', false ); ?>
								<span class="tt-saved-hint description">
									<?php esc_html_e( 'ذخیره این فرم فقط فیلدهای همین صفحه را تغییر می‌دهد.', 'torantejarat' ); ?>
								</span>
							</div>
						</form>
					<?php endif; ?>
				</main>
			</div>
		</div>
		<?php
	}

	/**
	 * Dashboard view. It contains links and existing readiness information only;
	 * no option form and no duplicated store metrics.
	 *
	 * @param array $nav Navigation tree from torantejarat_panel_nav().
	 */
	private function render_dashboard( array $nav ) {
		$native = array();
		if ( post_type_exists( 'product' ) && current_user_can( 'edit_products' ) ) {
			$native[] = array(
				'label' => __( 'محصولات و کاتالوگ', 'torantejarat' ),
				'desc'  => __( 'قیمت، موجودی، تصویر و مشخصات واقعی محصولات', 'torantejarat' ),
				'icon'  => 'dashicons-products',
				'url'   => admin_url( 'edit.php?post_type=product' ),
			);
		}
		if ( post_type_exists( 'shop_order' ) && current_user_can( 'edit_shop_orders' ) ) {
			$native[] = array(
				'label' => __( 'سفارش‌ها', 'torantejarat' ),
				'desc'  => __( 'پیگیری سفارش و پرداخت در مدیریت WooCommerce', 'torantejarat' ),
				'icon'  => 'dashicons-list-view',
				'url'   => $this->orders_url(),
			);
		}
		if ( post_type_exists( 'torantejarat_request' ) && current_user_can( 'edit_posts' ) ) {
			$native[] = array(
				'label' => __( 'درخواست‌ها', 'torantejarat' ),
				'desc'  => __( 'تماس، اجاره و درخواست بررسی دستگاه', 'torantejarat' ),
				'icon'  => 'dashicons-clipboard',
				'url'   => admin_url( 'edit.php?post_type=torantejarat_request' ),
			);
		}
		if ( current_user_can( 'edit_posts' ) ) {
			$native[] = array(
				'label' => __( 'نوشته‌های مجله', 'torantejarat' ),
				'desc'  => __( 'ایجاد و ویرایش راهنماها و مقاله‌های واقعی', 'torantejarat' ),
				'icon'  => 'dashicons-welcome-write-blog',
				'url'   => admin_url( 'edit.php' ),
			);
		}
		if ( current_user_can( 'edit_pages' ) ) {
			$native[] = array(
				'label' => __( 'برگه‌ها و خدمات', 'torantejarat' ),
				'desc'  => __( 'محتوای برگه‌های تماس، اجاره، تعمیر و راهنما', 'torantejarat' ),
				'icon'  => 'dashicons-admin-page',
				'url'   => admin_url( 'edit.php?post_type=page' ),
			);
		}
		if ( current_user_can( 'edit_theme_options' ) ) {
			$native[] = array(
				'label' => __( 'منوها', 'torantejarat' ),
				'desc'  => __( 'لینک‌های هدر، فوتر و سایر جایگاه‌های منو', 'torantejarat' ),
				'icon'  => 'dashicons-menu',
				'url'   => admin_url( 'nav-menus.php' ),
			);
			$native[] = array(
				'label' => __( 'هویت سایت', 'torantejarat' ),
				'desc'  => __( 'لوگوی اصلی و هویت بصری بومی وردپرس', 'torantejarat' ),
				'icon'  => 'dashicons-customize',
				'url'   => admin_url( 'customize.php' ),
			);
		}
		?>
		<div class="tt-page-heading">
			<h2 id="tt-page-title">
				<span class="dashicons dashicons-dashboard" aria-hidden="true"></span>
				<?php esc_html_e( 'داشبورد بلکوکم', 'torantejarat' ); ?>
			</h2>
			<p><?php esc_html_e( 'از اینجا به تنظیمات قالب یا مدیریت بومی محتوای فروشگاه بروید. هیچ عدد یا وضعیت نمایشی جدیدی ساخته نمی‌شود.', 'torantejarat' ); ?></p>
		</div>
		<section class="tt-panel-section" aria-labelledby="tt-dashboard-theme">
			<div class="tt-section-heading">
				<h3 id="tt-dashboard-theme"><?php esc_html_e( 'تنظیمات قالب', 'torantejarat' ); ?></h3>
				<p><?php esc_html_e( 'هر بخش فقط Option خودش را ذخیره می‌کند.', 'torantejarat' ); ?></p>
			</div>
			<ul class="tt-cards">
				<?php foreach ( $nav as $key => $tab ) : ?>
					<li>
						<a class="tt-card-link" href="<?php echo esc_url( admin_url( 'admin.php?page=torantejarat-settings&section=' . $key ) ); ?>">
							<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
							<span class="tt-card-body">
								<span class="tt-card-title"><?php echo esc_html( $tab['label'] ); ?></span>
								<span class="tt-card-meta">
									<?php
									printf(
										/* translators: %s: number of fields in a settings section. */
										esc_html__( '%s فیلد', 'torantejarat' ),
										esc_html( number_format_i18n( count( $this->schema[ $key ]['fields'] ) ) )
									);
									?>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php if ( $native ) : ?>
			<section class="tt-panel-section" aria-labelledby="tt-dashboard-native">
				<div class="tt-section-heading">
					<h3 id="tt-dashboard-native"><?php esc_html_e( 'مدیریت بومی سایت و فروشگاه', 'torantejarat' ); ?></h3>
					<p><?php esc_html_e( 'این داده‌ها در Option قالب تکرار نمی‌شوند؛ ویرایش در منبع اصلی انجام می‌شود.', 'torantejarat' ); ?></p>
				</div>
				<ul class="tt-cards">
					<?php foreach ( $native as $item ) : ?>
						<li>
							<a class="tt-card-link" href="<?php echo esc_url( $item['url'] ); ?>">
								<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span>
								<span class="tt-card-body">
									<span class="tt-card-title"><?php echo esc_html( $item['label'] ); ?></span>
									<span class="tt-card-desc"><?php echo esc_html( $item['desc'] ); ?></span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>
		<?php
	}

	/**
	 * Resolve the native WooCommerce orders screen for both HPOS and posts.
	 * The legacy URL remains a safe fallback for sites without the controller.
	 *
	 * @return string
	 */
	private function orders_url() {
		$fallback = admin_url( 'edit.php?post_type=shop_order' );
		$class    = 'Automattic\\WooCommerce\\Internal\\Admin\\Orders\\PageController';
		if ( ! function_exists( 'wc_get_container' ) || ! class_exists( $class ) ) {
			return $fallback;
		}
		try {
			$controller = wc_get_container()->get( $class );
			if ( is_object( $controller ) && method_exists( $controller, 'get_orders_url' ) ) {
				return (string) $controller->get_orders_url();
			}
		} catch ( \Throwable $e ) {
			return $fallback;
		}
		return $fallback;
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
			<h2 class="tt-group-title" id="tt-page-title">
				<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
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
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="tt-form tt-publish-form" id="tt-publish-form" data-tt-dirty>
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
	 * @param string $tab_key   Tab key.
	 * @param array  $tab       Entry from torantejarat_panel_nav().
	 * @param array  $utilities Non-field utility cards for this tab.
	 */
	private function render_overview( $tab_key, array $tab, array $utilities = array() ) {
		$base  = 'admin.php?page=torantejarat-settings&section=' . $tab_key;
		$cards = array_merge( $tab['groups'], $utilities );
		?>
		<div class="tt-overview">
			<div class="tt-page-heading">
				<h2 class="tt-overview-title" id="tt-page-title">
					<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
					<?php echo esc_html( $tab['label'] ); ?>
				</h2>
				<p class="tt-overview-lead">
					<?php esc_html_e( 'هر زیربخش صفحهٔ جداگانه دارد؛ ذخیره در هر صفحه فقط همان فیلدها را می‌نویسد و بقیهٔ مقادیر دست‌نخورده می‌مانند.', 'torantejarat' ); ?>
				</p>
			</div>
			<ul class="tt-cards">
				<?php foreach ( $cards as $group ) : ?>
					<li>
						<a class="tt-card-link" href="<?php echo esc_url( admin_url( $base . '&sub=' . $group['id'] ) ); ?>">
							<span class="dashicons <?php echo esc_attr( $group['icon'] ); ?>" aria-hidden="true"></span>
							<span class="tt-card-body">
								<span class="tt-card-title"><?php echo esc_html( $group['label'] ); ?></span>
								<?php if ( ! empty( $group['desc'] ) ) : ?>
									<span class="tt-card-desc"><?php echo esc_html( $group['desc'] ); ?></span>
								<?php endif; ?>
								<span class="tt-card-meta">
									<?php if ( isset( $group['meta'] ) ) : ?>
										<?php echo esc_html( $group['meta'] ); ?>
									<?php else : ?>
										<?php
										printf(
											/* translators: %s: number of fields in the sub-section. */
											esc_html__( '%s فیلد', 'torantejarat' ),
											esc_html( number_format_i18n( count( $group['fields'] ) ) )
										);
										?>
									<?php endif; ?>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( 'home' === $tab_key ) : ?>
				<?php $this->render_home_data_guide(); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Native-data guide for homepage regions that are not theme text options.
	 * These links explain where real content is managed; they do not add fields.
	 */
	private function render_home_data_guide() {
		$items = array();
		if ( current_user_can( 'edit_products' ) ) {
			$items[] = array(
				'label' => __( 'تصاویر هیرو و بخش‌های محصول', 'torantejarat' ),
				'desc'  => __( 'Hero Visual، تخفیف‌ها، اسلایدرها، پرفروش‌ها و تازه‌ترین‌ها از محصولات و فروش واقعی ساخته می‌شوند.', 'torantejarat' ),
				'icon'  => 'dashicons-products',
				'url'   => admin_url( 'edit.php?post_type=product' ),
			);
		}
		if ( current_user_can( 'manage_product_terms' ) ) {
			$items[] = array(
				'label' => __( 'دسته‌ها و دادهٔ فایندر', 'torantejarat' ),
				'desc'  => __( 'کارت دسته‌ها و گزینه‌های کاربرد از Termهای واقعی WooCommerce خوانده می‌شوند.', 'torantejarat' ),
				'icon'  => 'dashicons-category',
				'url'   => admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ),
			);
		}
		if ( current_user_can( 'edit_pages' ) ) {
			$items[] = array(
				'label' => __( 'کارت‌های خدمات', 'torantejarat' ),
				'desc'  => __( 'مقصد کارت‌های اجاره، تعمیر و آموزش، برگه‌های واقعی با همین Slugهاست.', 'torantejarat' ),
				'icon'  => 'dashicons-admin-page',
				'url'   => admin_url( 'edit.php?post_type=page' ),
			);
		}
		if ( current_user_can( 'edit_posts' ) ) {
			$items[] = array(
				'label' => __( 'کارت‌های مجله', 'torantejarat' ),
				'desc'  => __( 'تیتر، تصویر و خلاصهٔ کارت‌ها از نوشته‌های منتشرشده می‌آید.', 'torantejarat' ),
				'icon'  => 'dashicons-welcome-write-blog',
				'url'   => admin_url( 'edit.php' ),
			);
		}
		if ( ! $items ) {
			return;
		}
		?>
		<section class="tt-panel-section tt-native-guide" aria-labelledby="tt-home-native">
			<div class="tt-section-heading">
				<h3 id="tt-home-native"><?php esc_html_e( 'بخش‌های داده‌محور صفحه اصلی', 'torantejarat' ); ?></h3>
				<p><?php esc_html_e( 'این موارد فیلد متنی قالب ندارند و نباید با دادهٔ دست‌ساز جایگزین شوند.', 'torantejarat' ); ?></p>
			</div>
			<ul class="tt-cards">
				<?php foreach ( $items as $item ) : ?>
					<li>
						<a class="tt-card-link" href="<?php echo esc_url( $item['url'] ); ?>">
							<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span>
							<span class="tt-card-body">
								<span class="tt-card-title"><?php echo esc_html( $item['label'] ); ?></span>
								<span class="tt-card-desc"><?php echo esc_html( $item['desc'] ); ?></span>
								<span class="tt-card-meta"><?php esc_html_e( 'دادهٔ بومی', 'torantejarat' ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
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
	 * @param string $tab_key Tab key.
	 * @param array  $ordered Field keys to render, in order.
	 */
	private function render_fields( $tab_key, array $ordered ) {
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
			$id         = torantejarat_panel_field_dom_id( $tab_key, $key );
			$wrapper_id = torantejarat_panel_field_dom_id( $tab_key, $key, true );
			$name       = 'torantejarat_' . $tab_key . '[' . $key . ']';
			$type       = $field['type'];
			$control    = isset( $field['control'] ) ? (string) $field['control'] : '';
			$desc_id    = ! empty( $field['desc'] ) ? $id . '-description' : '';
			$desc_attr  = $desc_id ? ' aria-describedby="' . esc_attr( $desc_id ) . '"' : '';
			$open_url   = '';
			if ( 'url' === $type && filter_var( (string) $value, FILTER_VALIDATE_URL ) && preg_match( '#^https?://#i', (string) $value ) ) {
				$open_url = esc_url( (string) $value );
			}
			if ( 'repeater' === $type ) {
				$this->render_repeater_field( $tab_key, $key, $field, $value );
				continue;
			}
			?>
			<div class="tt-field" id="<?php echo esc_attr( $wrapper_id ); ?>">
				<?php if ( 'segments' === $control ) : ?>
					<span class="tt-label" id="<?php echo esc_attr( $id . '-label' ); ?>"><?php echo esc_html( $field['label'] ); ?></span>
				<?php elseif ( 'checkbox' !== $type ) : ?>
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
							<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
						><?php echo esc_textarea( $value ); ?></textarea>
					<?php elseif ( 'segments' === $control && 'select' === $type && ! empty( $field['options'] ) && is_array( $field['options'] ) ) : ?>
						<div
							class="tt-segmented"
							id="<?php echo esc_attr( $id ); ?>"
							role="radiogroup"
							aria-labelledby="<?php echo esc_attr( $id . '-label' ); ?>"
							<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
						>
							<?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
								<label class="tt-segment">
									<input
										type="radio"
										id="<?php echo esc_attr( $id . '-' . sanitize_key( $opt_val ) ); ?>"
										name="<?php echo esc_attr( $name ); ?>"
										value="<?php echo esc_attr( $opt_val ); ?>"
										<?php checked( $value, $opt_val ); ?>
									>
									<span><?php echo esc_html( $opt_label ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
					<?php elseif ( 'checkbox' === $type && 'switch' === $control ) : ?>
						<label class="tt-switch">
							<input
								class="tt-switch-input"
								type="checkbox"
								id="<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( $name ); ?>"
								value="1"
								<?php checked( $value, 1 ); ?>
								<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
							>
							<span class="tt-switch-track" aria-hidden="true"><span class="tt-switch-thumb"></span></span>
							<span class="tt-switch-copy">
								<span class="tt-switch-label"><?php echo esc_html( $field['label'] ); ?></span>
								<span class="tt-switch-state">
									<span class="tt-state-on"><?php esc_html_e( 'روشن', 'torantejarat' ); ?></span>
									<span class="tt-state-off"><?php esc_html_e( 'خاموش', 'torantejarat' ); ?></span>
								</span>
							</span>
						</label>
					<?php elseif ( 'checkbox' === $type ) : ?>
						<label class="tt-check">
							<input
								type="checkbox"
								id="<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( $name ); ?>"
								value="1"
								<?php checked( $value, 1 ); ?>
								<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
							>
							<?php echo esc_html( $field['label'] ); ?>
						</label>
					<?php elseif ( 'select' === $type && ! empty( $field['options'] ) && is_array( $field['options'] ) ) : ?>
						<select
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
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
					<?php elseif ( 'url' === $type ) : ?>
						<?php if ( 'media-url' === $control ) : ?>
							<div class="tt-media-control" data-tt-media>
								<div class="tt-media-preview" data-tt-media-preview <?php if ( ! $open_url ) : ?>hidden<?php endif; ?>>
									<img <?php if ( $open_url ) : ?>src="<?php echo esc_url( $open_url ); ?>"<?php endif; ?> alt="" data-tt-media-image loading="lazy">
								</div>
								<div class="tt-media-main">
						<?php endif; ?>
									<div class="tt-url-control">
										<input
											type="url"
											id="<?php echo esc_attr( $id ); ?>"
											name="<?php echo esc_attr( $name ); ?>"
											value="<?php echo esc_attr( (string) $value ); ?>"
											class="regular-text code"
											data-tt-url-input
											<?php if ( 'media-url' === $control ) : ?>data-tt-media-url<?php endif; ?>
											<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
										>
										<a
											class="button tt-url-open"
											data-tt-url-open
											href="<?php echo esc_url( $open_url ); ?>"
											target="_blank"
											rel="noopener noreferrer"
											<?php if ( ! $open_url ) : ?>hidden<?php endif; ?>
										>
											<span class="dashicons dashicons-external" aria-hidden="true"></span>
											<?php esc_html_e( 'بازکردن', 'torantejarat' ); ?>
										</a>
									</div>
						<?php if ( 'media-url' === $control ) : ?>
									<div class="tt-media-actions">
										<button type="button" class="button" data-tt-media-select hidden>
											<span class="dashicons dashicons-admin-media" aria-hidden="true"></span>
											<?php esc_html_e( 'انتخاب تصویر', 'torantejarat' ); ?>
										</button>
										<button type="button" class="button" data-tt-media-clear hidden>
											<?php esc_html_e( 'پاک‌کردن انتخاب', 'torantejarat' ); ?>
										</button>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php elseif ( 'color' === $type && 'color-hex' === $control ) : ?>
						<div class="tt-color-control">
							<input
								type="text"
								id="<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( $name ); ?>"
								value="<?php echo esc_attr( (string) $value ); ?>"
								class="regular-text code tt-color-value"
								data-tt-color
								data-tt-color-label="<?php echo esc_attr( $field['label'] ); ?>"
								pattern="#(?:[0-9a-fA-F]{3}){1,2}"
								maxlength="7"
								spellcheck="false"
								autocomplete="off"
								<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
							>
						</div>
					<?php else : ?>
						<input
							type="<?php echo esc_attr( $type ); ?>"
							id="<?php echo esc_attr( $id ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( (string) $value ); ?>"
							class="regular-text <?php echo 'url' === $type ? 'code' : ''; ?>"
							<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped attribute built above. ?>
						>
					<?php endif; ?>
					<?php if ( $desc_id ) : ?>
						<p class="description" id="<?php echo esc_attr( $desc_id ); ?>">
							<?php echo esc_html( $field['desc'] ); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render a repeater field as cards, with one trailing blank row so a row
	 * can still be added with JavaScript disabled. Collapse and row buttons
	 * are JS enhancements; no-JS content stays open and uses the blank row.
	 * The JS counterpart is assets/js/admin-settings.js (settings screen only).
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
		if ( isset( $field['control'] ) && 'product-picker' === $field['control'] ) {
			$this->render_product_picker_field( $tab_key, $key, $field, $value );
			return;
		}
		if ( isset( $field['control'] ) && 'video-picker' === $field['control'] ) {
			$this->render_video_picker_field( $tab_key, $key, $field, $value );
			return;
		}
		$rows = is_array( $value ) ? array_values( $value ) : array();
		$max  = isset( $field['max'] ) ? (int) $field['max'] : 0;
		if ( $max < 1 || count( $rows ) < $max ) {
			$rows[] = array(); // Trailing blank row = the no-JavaScript way to add a row.
		}
		$base       = 'torantejarat_' . $tab_key . '[' . $key . ']';
		$wrapper_id = torantejarat_panel_field_dom_id( $tab_key, $key, true );
		$row_label  = isset( $field['row_label'] ) ? (string) $field['row_label'] : __( 'پرسش %s', 'torantejarat' );
		?>
		<div
			class="tt-field tt-field-repeater"
			id="<?php echo esc_attr( $wrapper_id ); ?>"
			data-tt-repeater
			data-tt-max="<?php echo esc_attr( (string) $max ); ?>"
			data-tt-row-label="<?php echo esc_attr( trim( str_replace( '%s', '', $row_label ) ) ); ?>"
			role="group"
			aria-labelledby="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"
			<?php if ( ! empty( $field['desc'] ) ) : ?>aria-describedby="<?php echo esc_attr( $wrapper_id . '-description' ); ?>"<?php endif; ?>
		>
			<div class="tt-repeater-head">
				<span class="tt-label" id="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"><?php echo esc_html( $field['label'] ); ?></span>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<p class="description" id="<?php echo esc_attr( $wrapper_id . '-description' ); ?>"><?php echo esc_html( $field['desc'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="tt-control">
				<div class="tt-repeater-list" data-tt-repeater-list>
					<?php foreach ( $rows as $index => $row ) : ?>
						<?php $this->render_repeater_row( $base, (int) $index, $subfields, is_array( $row ) ? $row : array(), $row_label ); ?>
					<?php endforeach; ?>
				</div>
				<noscript>
					<p class="description"><?php esc_html_e( 'برای افزودن، ردیف خالی آخر را پر کنید. برای حذف، محتوای ردیف را پاک کنید؛ تغییر پس از ذخیره اعمال می‌شود.', 'torantejarat' ); ?></p>
				</noscript>
				<p class="tt-repeater-actions">
					<button type="button" class="button" data-tt-repeater-add hidden><?php esc_html_e( 'افزودن ردیف', 'torantejarat' ); ?></button>
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
	private function render_repeater_row( $base, $index, array $subfields, array $row, $row_label = '' ) {
		$pattern   = '' !== $row_label ? $row_label : __( 'پرسش %s', 'torantejarat' );
		$row_label = sprintf(
			/* translators: %s: repeater row number. */
			$pattern,
			number_format_i18n( $index + 1 )
		);
		?>
		<div class="tt-repeater-row" data-tt-repeater-row role="group" aria-label="<?php echo esc_attr( $row_label ); ?>">
			<div class="tt-repeater-card-head">
				<span class="tt-repeater-number" data-tt-repeater-number><?php echo esc_html( $row_label ); ?></span>
				<span class="tt-repeater-summary" data-tt-repeater-summary></span>
				<div class="tt-repeater-card-actions">
					<button
						type="button"
						class="button tt-repeater-toggle"
						data-tt-repeater-toggle
						data-tt-collapse-label="<?php esc_attr_e( 'جمع‌کردن', 'torantejarat' ); ?>"
						data-tt-expand-label="<?php esc_attr_e( 'بازکردن', 'torantejarat' ); ?>"
						aria-expanded="true"
						hidden
					>
						<span data-tt-repeater-toggle-text><?php esc_html_e( 'جمع‌کردن', 'torantejarat' ); ?></span>
						<span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
					</button>
					<button type="button" class="button-link tt-repeater-remove" data-tt-repeater-remove hidden>
						<span aria-hidden="true"><?php esc_html_e( 'حذف', 'torantejarat' ); ?></span>
						<span class="screen-reader-text"><?php esc_html_e( 'حذف این ردیف', 'torantejarat' ); ?></span>
					</button>
				</div>
			</div>
			<div class="tt-repeater-card-body" data-tt-repeater-body>
				<?php foreach ( $subfields as $sub_key => $sub ) : ?>
					<label class="tt-repeater-subfield">
						<span class="tt-repeater-subfield-label"><?php echo esc_html( isset( $sub['label'] ) ? $sub['label'] : $sub_key ); ?></span>
						<?php
						$this->render_subfield_input(
							$base,
							$index,
							(string) $sub_key,
							$sub,
							isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : ''
						);
						?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Product picker: search WooCommerce products and pick slides instead of
	 * typing product IDs. Rows keep the exact same storage shape as the
	 * repeater (product_id | label | caption | link), so the front end and the
	 * sanitizer stay untouched.
	 *
	 * @param string $tab_key Tab key, e.g. "home".
	 * @param string $key     Field key, e.g. "hero_slides".
	 * @param array  $field   Field definition.
	 * @param mixed  $value   Stored rows.
	 */
	private function render_product_picker_field( $tab_key, $key, array $field, $value ) {
		$subfields  = isset( $field['subfields'] ) && is_array( $field['subfields'] ) ? $field['subfields'] : array();
		$rows       = is_array( $value ) ? array_values( $value ) : array();
		$max        = isset( $field['max'] ) ? (int) $field['max'] : 0;
		$base       = 'torantejarat_' . $tab_key . '[' . $key . ']';
		$wrapper_id = torantejarat_panel_field_dom_id( $tab_key, $key, true );
		$search_id  = $wrapper_id . '-search';
		$can_search = function_exists( 'wc_get_products' );
		?>
		<div
			class="tt-field tt-field-picker"
			id="<?php echo esc_attr( $wrapper_id ); ?>"
			data-tt-product-picker
			data-tt-max="<?php echo esc_attr( (string) $max ); ?>"
			data-tt-base="<?php echo esc_attr( $base ); ?>"
			data-tt-ajax="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>"
			data-tt-nonce="<?php echo esc_attr( wp_create_nonce( 'torantejarat_product_search' ) ); ?>"
			role="group"
			aria-labelledby="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"
		>
			<div class="tt-repeater-head">
				<span class="tt-label" id="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"><?php echo esc_html( $field['label'] ); ?></span>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<p class="description" id="<?php echo esc_attr( $wrapper_id . '-description' ); ?>"><?php echo esc_html( $field['desc'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="tt-control">
				<div class="tt-picker-search">
					<span class="dashicons dashicons-search" aria-hidden="true"></span>
					<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'جستجوی محصول', 'torantejarat' ); ?></label>
					<input
						type="search"
						id="<?php echo esc_attr( $search_id ); ?>"
						class="tt-picker-search-input"
						data-tt-picker-search
						placeholder="<?php esc_attr_e( 'نام یا SKU محصول را بنویسید…', 'torantejarat' ); ?>"
						autocomplete="off"
						<?php disabled( ! $can_search ); ?>
					>
					<span class="spinner" data-tt-picker-spinner aria-hidden="true"></span>
				</div>
				<?php if ( ! $can_search ) : ?>
					<p class="description"><?php esc_html_e( 'ووکامرس فعال نیست؛ برای انتخاب محصول، شناسه را دستی وارد کنید.', 'torantejarat' ); ?></p>
				<?php endif; ?>
				<div class="tt-picker-results" data-tt-picker-results hidden></div>
				<p class="tt-picker-status" data-tt-picker-status role="status" aria-live="polite"></p>
				<ul class="tt-picker-list" data-tt-picker-list>
					<?php foreach ( $rows as $index => $row ) : ?>
						<?php $this->render_product_picker_item( $base, (int) $index, $subfields, is_array( $row ) ? $row : array() ); ?>
					<?php endforeach; ?>
				</ul>
				<p class="tt-picker-empty" data-tt-picker-empty<?php echo $rows ? ' hidden' : ''; ?>>
					<?php esc_html_e( 'هنوز محصولی انتخاب نشده است. نام محصول را جستجو کنید و از فهرست انتخاب کنید.', 'torantejarat' ); ?>
				</p>
				<?php if ( $max > 0 ) : ?>
					<p class="tt-picker-note" data-tt-picker-note
						data-tt-text-guide="<?php echo esc_attr( sprintf( /* translators: %s: maximum number of slides. */ __( 'حداکثر %s اسلاید. برای افزودن بیشتر، ابتدا یک اسلاید را حذف کنید.', 'torantejarat' ), number_format_i18n( $max ) ) ); ?>"
						data-tt-text-limit="<?php echo esc_attr( __( 'به سقف رسیدید؛ برای انتخاب اسلاید تازه، ابتدا یکی را حذف کنید.', 'torantejarat' ) ); ?>"
					><?php echo esc_html( sprintf( /* translators: %s: maximum number of slides. */ __( 'حداکثر %s اسلاید. برای افزودن بیشتر، ابتدا یک اسلاید را حذف کنید.', 'torantejarat' ), number_format_i18n( $max ) ) ); ?></p>
				<?php endif; ?>
			</div>
			<noscript>
				<p class="description"><?php esc_html_e( 'جستجو و انتخاب محصول به جاوااسکریپت نیاز دارد. در این حالت شناسه هر محصول را در بخش «متن و لینک دلخواه» ویرایش کنید.', 'torantejarat' ); ?></p>
			</noscript>
		</div>
		<?php
	}

	/**
	 * One selected product inside the picker.
	 *
	 * @param string $base      Field name prefix, without the row index.
	 * @param int    $index     Row index.
	 * @param array  $subfields Sub-field definitions.
	 * @param array  $row       Stored row values.
	 */
	private function render_product_picker_item( $base, $index, array $subfields, array $row ) {
		$product_id = isset( $row['product_id'] ) ? absint( $row['product_id'] ) : 0;
		$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
		$name       = $product ? $product->get_name() : '';
		if ( ! $name ) {
			$name = $product_id
				/* translators: %s: product ID. */
				? sprintf( __( 'محصول #%s', 'torantejarat' ), number_format_i18n( $product_id ) )
				: __( 'محصول انتخاب‌نشده', 'torantejarat' );
		}
		$sku   = $product ? (string) $product->get_sku() : '';
		$thumb = $product && $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : '';
		?>
		<li class="tt-picker-item" data-tt-picker-item data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
			<div class="tt-picker-main">
				<span class="tt-picker-thumb<?php echo $thumb ? '' : ' is-empty'; ?>">
					<?php if ( $thumb ) : ?>
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="48" height="48" loading="lazy">
					<?php else : ?>
						<span class="dashicons dashicons-format-image" aria-hidden="true"></span>
					<?php endif; ?>
				</span>
				<div class="tt-picker-info">
					<strong class="tt-picker-name"><?php echo esc_html( $name ); ?></strong>
					<span class="tt-picker-meta">
						<span class="tt-picker-position" data-tt-picker-position><?php echo esc_html( number_format_i18n( $index + 1 ) ); ?></span>
						<span class="tt-picker-sep" aria-hidden="true">·</span>
						<span dir="ltr">#<?php echo esc_html( (string) $product_id ); ?></span>
						<?php if ( $sku ) : ?>
							<span class="tt-picker-sep" aria-hidden="true">·</span>
							<span dir="ltr"><?php echo esc_html( $sku ); ?></span>
						<?php endif; ?>
					</span>
				</div>
				<div class="tt-picker-actions">
					<button type="button" class="button-link tt-picker-move" data-tt-picker-up>
						<span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'انتقال به بالا', 'torantejarat' ); ?></span>
					</button>
					<button type="button" class="button-link tt-picker-move" data-tt-picker-down>
						<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'انتقال به پایین', 'torantejarat' ); ?></span>
					</button>
					<button type="button" class="button-link tt-picker-remove" data-tt-picker-remove>
						<span aria-hidden="true"><?php esc_html_e( 'حذف', 'torantejarat' ); ?></span>
						<span class="screen-reader-text"><?php esc_html_e( 'حذف این اسلاید', 'torantejarat' ); ?></span>
					</button>
				</div>
			</div>
			<details class="tt-picker-more">
				<summary><?php esc_html_e( 'متن و لینک دلخواه', 'torantejarat' ); ?></summary>
				<div class="tt-picker-fields">
					<?php foreach ( $subfields as $sub_key => $sub ) : ?>
						<label class="tt-picker-field">
							<span class="tt-picker-field-label"><?php echo esc_html( isset( $sub['label'] ) ? $sub['label'] : $sub_key ); ?></span>
							<?php
							$this->render_subfield_input(
								$base,
								$index,
								(string) $sub_key,
								$sub,
								isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : ''
							);
							?>
						</label>
					<?php endforeach; ?>
				</div>
				<p class="description"><?php esc_html_e( 'خالی بگذارید تا لیبل از SKU، کپشن از توضیح کوتاه و لینک از صفحه محصول خوانده شود.', 'torantejarat' ); ?></p>
			</details>
		</li>
		<?php
	}

	/**
	 * Video picker: rows of video cards, each backed by the WordPress media
	 * library. Storage shape stays `home_videos[i][title|video_url|caption]`.
	 *
	 * @param string $tab_key Tab key.
	 * @param string $key     Field key.
	 * @param array  $field   Field definition.
	 * @param mixed  $value   Stored rows.
	 */
	private function render_video_picker_field( $tab_key, $key, array $field, $value ) {
		$rows       = is_array( $value ) ? array_values( $value ) : array();
		$base       = 'torantejarat_' . $tab_key . '[' . $key . ']';
		$wrapper_id = torantejarat_panel_field_dom_id( $tab_key, $key, true );
		$can_media  = current_user_can( 'upload_files' );
		?>
		<div
			class="tt-field tt-field-picker tt-field-video"
			id="<?php echo esc_attr( $wrapper_id ); ?>"
			data-tt-video-picker
			data-tt-base="<?php echo esc_attr( $base ); ?>"
			role="group"
			aria-labelledby="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"
		>
			<div class="tt-repeater-head">
				<span class="tt-label" id="<?php echo esc_attr( $wrapper_id . '-label' ); ?>"><?php echo esc_html( $field['label'] ); ?></span>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<p class="description" id="<?php echo esc_attr( $wrapper_id . '-description' ); ?>"><?php echo esc_html( $field['desc'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="tt-control">
				<ul class="tt-picker-list tt-video-list" data-tt-video-list>
					<?php foreach ( $rows as $index => $row ) : ?>
						<?php $this->render_video_picker_item( $base, (int) $index, is_array( $row ) ? $row : array(), $can_media ); ?>
					<?php endforeach; ?>
				</ul>
				<p class="tt-picker-empty" data-tt-video-empty<?php echo $rows ? ' hidden' : ''; ?>>
					<?php esc_html_e( 'هنوز ویدیویی اضافه نشده است. با دکمهٔ «افزودن ویدیو» شروع کنید.', 'torantejarat' ); ?>
				</p>
				<p class="tt-video-actions">
					<button type="button" class="button" data-tt-video-add hidden><?php esc_html_e( 'افزودن ویدیو', 'torantejarat' ); ?></button>
					<span class="description"><?php esc_html_e( 'فایل‌های mp4 یا webm بهترین نتیجه را می‌دهند.', 'torantejarat' ); ?></span>
				</p>
			</div>
			<noscript>
				<p class="description"><?php esc_html_e( 'افزودن ردیف و انتخاب از کتابخانه رسانه به جاوااسکریپت نیاز دارد؛ در این حالت لینک فایل را دستی در ردیف‌های موجود وارد کنید.', 'torantejarat' ); ?></p>
			</noscript>
			<template data-tt-video-template>
				<?php $this->render_video_picker_item( $base, 0, array(), $can_media ); ?>
			</template>
		</div>
		<?php
	}

	/**
	 * One video row (also used as the JS template).
	 *
	 * @param string $base      Field name prefix.
	 * @param int    $index     Row index.
	 * @param array  $row       Stored row values.
	 * @param bool   $can_media Whether the media library is available.
	 */
	private function render_video_picker_item( $base, $index, array $row, $can_media ) {
		$video_url = isset( $row['video_url'] ) ? (string) $row['video_url'] : '';
		$title     = isset( $row['title'] ) ? (string) $row['title'] : '';
		$caption   = isset( $row['caption'] ) ? (string) $row['caption'] : '';
		?>
		<li class="tt-picker-item tt-video-item" data-tt-video-item>
			<div class="tt-video-row">
				<span class="tt-video-preview<?php echo $video_url ? ' has-video' : ''; ?>" data-tt-video-preview>
					<?php if ( $video_url ) : ?>
						<video src="<?php echo esc_url( $video_url ); ?>" preload="metadata" controls muted playsinline></video>
					<?php else : ?>
						<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
					<?php endif; ?>
				</span>
				<div class="tt-video-fields">
					<label class="tt-picker-field">
						<span class="tt-picker-field-label"><?php esc_html_e( 'تیتر ویدیو', 'torantejarat' ); ?></span>
						<input type="text" name="<?php echo esc_attr( $base . '[' . $index . '][title]' ); ?>" data-tt-name="<?php echo esc_attr( $base . '[%i%][title]' ); ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'خالی = بدون تیتر', 'torantejarat' ); ?>">
					</label>
					<div class="tt-video-source">
						<label class="tt-picker-field">
							<span class="tt-picker-field-label"><?php esc_html_e( 'فایل ویدیو', 'torantejarat' ); ?></span>
							<input type="url" dir="ltr" name="<?php echo esc_attr( $base . '[' . $index . '][video_url]' ); ?>" data-tt-video-url data-tt-name="<?php echo esc_attr( $base . '[%i%][video_url]' ); ?>" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://…/video.mp4">
						</label>
						<button type="button" class="button" data-tt-video-select<?php echo $can_media ? '' : ' hidden'; ?>><?php esc_html_e( 'انتخاب از کتابخانه', 'torantejarat' ); ?></button>
						<button type="button" class="button-link" data-tt-video-clear hidden><?php esc_html_e( 'پاک کردن', 'torantejarat' ); ?></button>
					</div>
					<label class="tt-picker-field">
						<span class="tt-picker-field-label"><?php esc_html_e( 'کپشن', 'torantejarat' ); ?></span>
						<input type="text" name="<?php echo esc_attr( $base . '[' . $index . '][caption]' ); ?>" data-tt-name="<?php echo esc_attr( $base . '[%i%][caption]' ); ?>" value="<?php echo esc_attr( $caption ); ?>">
					</label>
				</div>
				<div class="tt-picker-actions tt-video-controls">
					<button type="button" class="button-link tt-picker-move" data-tt-video-up hidden>
						<span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'انتقال به بالا', 'torantejarat' ); ?></span>
					</button>
					<button type="button" class="button-link tt-picker-move" data-tt-video-down hidden>
						<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'انتقال به پایین', 'torantejarat' ); ?></span>
					</button>
					<button type="button" class="button-link tt-picker-remove" data-tt-video-remove hidden>
						<span aria-hidden="true"><?php esc_html_e( 'حذف', 'torantejarat' ); ?></span>
						<span class="screen-reader-text"><?php esc_html_e( 'حذف این ویدیو', 'torantejarat' ); ?></span>
					</button>
				</div>
			</div>
		</li>
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
				'<textarea rows="3" name="%1$s" data-tt-name="%2$s" aria-label="%4$s">%3$s</textarea>',
				esc_attr( $name ),
				esc_attr( $tmpl ),
				esc_textarea( (string) $value ),
				esc_attr( isset( $sub['label'] ) ? $sub['label'] : $sub_key )
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

	/**
	 * AJAX: search published products for the hero product picker.
	 * Returns a small, presentation-ready list; the client only renders it.
	 */
	public function ajax_search_products() {
		check_ajax_referer( 'torantejarat_product_search', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'دسترسی مجاز نیست.', 'torantejarat' ) ), 403 );
		}

		$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$term = trim( $term );

		$exclude = array();
		if ( isset( $_GET['exclude'] ) ) {
			$exclude = array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) );
		}

		if ( mb_strlen( $term ) < 2 || ! function_exists( 'wc_get_products' ) ) {
			wp_send_json_success( array() );
		}

		$args = array(
			'limit'   => 12,
			'status'  => 'publish',
			'orderby' => 'title',
			'order'   => 'ASC',
			'return'  => 'objects',
		);

		if ( ctype_digit( $term ) ) {
			$args['include'] = array( (int) $term );
			unset( $args['orderby'], $args['order'] );
		} else {
			$args['s'] = $term;
		}

		$items = array();
		foreach ( wc_get_products( $args ) as $product ) {
			if ( ! $product instanceof \WC_Product || in_array( $product->get_id(), $exclude, true ) ) {
				continue;
			}
			$image_id = $product->get_image_id();
			$items[]  = array(
				'id'    => $product->get_id(),
				'name'  => $product->get_name(),
				'sku'   => (string) $product->get_sku(),
				'thumb' => $image_id ? (string) wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '',
				'price' => wp_strip_all_tags( (string) $product->get_price_html() ),
			);
		}

		wp_send_json_success( $items );
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
		$section = '';
		if ( isset( $_GET['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Asset route only.
			$section = sanitize_key( wp_unslash( $_GET['section'] ) );
		} elseif ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Asset route only.
			$section = sanitize_key( wp_unslash( $_GET['tab'] ) );
		}
		$sub = isset( $_GET['sub'] ) ? sanitize_key( wp_unslash( $_GET['sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Asset route only.

		// Media Library is needed by the login logo and by the homepage video
		// picker. Users without upload_files keep the manual URL input and
		// never receive an unusable button.
		$media_enabled = (
			( 'login' === $section && 'content' === $sub )
			|| ( 'home' === $section && 'field' === $sub )
		) && current_user_can( 'upload_files' );
		if ( $media_enabled ) {
			wp_enqueue_media();
		}
		$script_path = get_template_directory() . '/assets/js/admin-settings.js';
		wp_enqueue_script(
			'tt-settings',
			get_template_directory_uri() . '/assets/js/admin-settings.js',
			$media_enabled ? array( 'media-views' ) : array(),
			file_exists( $script_path ) ? (string) filemtime( $script_path ) : (string) wp_get_theme()->get( 'Version' ),
			true
		);
		wp_localize_script(
			'tt-settings',
			'ttSettingsControls',
			array(
				'media' => array(
					'enabled' => $media_enabled,
					'title'   => __( 'انتخاب لوگوی صفحه ورود', 'torantejarat' ),
					'button'  => __( 'استفاده از این تصویر', 'torantejarat' ),
				),
				'video' => array(
					'enabled' => $media_enabled,
					'title'   => __( 'انتخاب ویدیو', 'torantejarat' ),
					'button'  => __( 'استفاده از این ویدیو', 'torantejarat' ),
					'empty'   => __( 'هنوز ویدیویی اضافه نشده است. با دکمهٔ «افزودن ویدیو» شروع کنید.', 'torantejarat' ),
				),
				'productSearch' => array(
					'noResult' => __( 'محصولی با این عنوان پیدا نشد.', 'torantejarat' ),
					'error'    => __( 'جستجو ناموفق بود؛ دوباره تلاش کنید.', 'torantejarat' ),
					'loading'  => __( 'در حال جستجو…', 'torantejarat' ),
					'add'      => __( 'افزودن به اسلایدر', 'torantejarat' ),
					'remove'   => __( 'حذف', 'torantejarat' ),
					'up'       => __( 'انتقال به بالا', 'torantejarat' ),
					'down'     => __( 'انتقال به پایین', 'torantejarat' ),
					'fields'   => array(
						'product_id' => __( 'شناسه محصول (ID)', 'torantejarat' ),
						'label'      => __( 'لیبل نمایشی', 'torantejarat' ),
						'caption'    => __( 'کپشن کوتاه', 'torantejarat' ),
						'link'       => __( 'لینک دلخواه (اختیاری)', 'torantejarat' ),
					),
					'more'     => __( 'متن و لینک دلخواه', 'torantejarat' ),
					'hint'     => __( 'خالی بگذارید تا لیبل از SKU، کپشن از توضیح کوتاه و لینک از صفحه محصول خوانده شود.', 'torantejarat' ),
					'fallback' => __( 'محصول انتخاب‌نشده', 'torantejarat' ),
				),
			)
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
