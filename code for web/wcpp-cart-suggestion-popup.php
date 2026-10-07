<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCPP_Suggestion_Popup' ) ) {

	class WCPP_Suggestion_Popup {

		private static $instance = null;
		private $option_name     = 'wcpp_settings';
		private $nonce_action    = 'wcpp_nonce';

		public static function instance() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private function __construct() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			add_action( 'wp_footer', array( $this, 'render_frontend' ) );
			add_action( 'wp_ajax_wcpp_get_related', array( $this, 'ajax_get_related' ) );
			add_action( 'wp_ajax_nopriv_wcpp_get_related', array( $this, 'ajax_get_related' ) );
		}

		/* ---------------------------------------------------------------
		 * تنظیمات پیش‌فرض
		 * --------------------------------------------------------------- */
		private function defaults() {
			return array(
				'enabled'         => 'yes',
				'title'           => 'این‌ها هم به سلیقه‌ی شما می‌خورن! 😍',
				'subtitle'        => 'می‌تونی این محصولات رو هم به سبدت اضافه کنی',
				'count'           => 4,
				'source'          => 'related', // related | upsell | cross_sell | category
				'no_thanks_text'  => 'نه ممنون، نیازی ندارم',
				'add_text'        => 'افزودن به سبد',
				'added_text'      => 'اضافه شد ✓',
				'primary_color'   => '#7C3AED',
				'secondary_color' => '#F472B6',
				'bg_color'        => '#FFFFFF',
				'text_color'      => '#1F2937',
				'overlay_opacity' => 55,
				'auto_close'      => 0,
				'show_price'      => 'yes',
				'show_badge'      => 'yes',
				'badge_text'      => 'پیشنهاد ویژه',
				'border_radius'   => 18,
			);
		}

		private function get_settings() {
			$saved = get_option( $this->option_name, array() );
			if ( ! is_array( $saved ) ) {
				$saved = array();
			}
			return wp_parse_args( $saved, $this->defaults() );
		}

		/* ---------------------------------------------------------------
		 * صفحه تنظیمات در پیشخوان
		 * --------------------------------------------------------------- */
		public function admin_menu() {
			add_submenu_page(
				'woocommerce',
				'پاپ‌آپ پیشنهاد محصول',
				'پاپ‌آپ پیشنهاد محصول',
				'manage_woocommerce',
				'wcpp-settings',
				array( $this, 'settings_page' )
			);
		}

		public function admin_assets( $hook ) {
			if ( strpos( $hook, 'wcpp-settings' ) === false ) {
				return;
			}
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_add_inline_script(
				'wp-color-picker',
				"jQuery(function($){ $('.wcpp-color-field').wpColorPicker(); });"
			);
		}

		public function settings_page() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$settings = $this->get_settings();
			$saved_ok = false;

			if ( isset( $_POST['wcpp_save'] ) && check_admin_referer( 'wcpp_save_settings', 'wcpp_settings_nonce' ) ) {

				$new = array();
				$new['enabled']         = isset( $_POST['enabled'] ) ? 'yes' : 'no';
				$new['title']           = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
				$new['subtitle']        = sanitize_text_field( wp_unslash( $_POST['subtitle'] ?? '' ) );
				$new['count']           = max( 1, min( 8, absint( $_POST['count'] ?? 4 ) ) );
				$allowed_sources        = array( 'related', 'upsell', 'cross_sell', 'category' );
				$src                    = sanitize_text_field( wp_unslash( $_POST['source'] ?? 'related' ) );
				$new['source']          = in_array( $src, $allowed_sources, true ) ? $src : 'related';
				$new['no_thanks_text']  = sanitize_text_field( wp_unslash( $_POST['no_thanks_text'] ?? '' ) );
				$new['add_text']        = sanitize_text_field( wp_unslash( $_POST['add_text'] ?? '' ) );
				$new['added_text']      = sanitize_text_field( wp_unslash( $_POST['added_text'] ?? '' ) );
				$new['primary_color']   = sanitize_hex_color( wp_unslash( $_POST['primary_color'] ?? '' ) ) ?: '#7C3AED';
				$new['secondary_color'] = sanitize_hex_color( wp_unslash( $_POST['secondary_color'] ?? '' ) ) ?: '#F472B6';
				$new['bg_color']        = sanitize_hex_color( wp_unslash( $_POST['bg_color'] ?? '' ) ) ?: '#FFFFFF';
				$new['text_color']      = sanitize_hex_color( wp_unslash( $_POST['text_color'] ?? '' ) ) ?: '#1F2937';
				$new['overlay_opacity'] = max( 0, min( 90, absint( $_POST['overlay_opacity'] ?? 55 ) ) );
				$new['auto_close']      = max( 0, min( 60, absint( $_POST['auto_close'] ?? 0 ) ) );
				$new['show_price']      = isset( $_POST['show_price'] ) ? 'yes' : 'no';
				$new['show_badge']      = isset( $_POST['show_badge'] ) ? 'yes' : 'no';
				$new['badge_text']      = sanitize_text_field( wp_unslash( $_POST['badge_text'] ?? '' ) );
				$new['border_radius']   = max( 0, min( 40, absint( $_POST['border_radius'] ?? 18 ) ) );

				update_option( $this->option_name, $new );
				$settings = $new;
				$saved_ok = true;
			}
			?>
			<div class="wrap" dir="rtl" style="font-family:tahoma,sans-serif;">
				<h1 style="display:flex;align-items:center;gap:8px;">🛍️ تنظیمات پاپ‌آپ پیشنهاد محصول</h1>

				<?php if ( $saved_ok ) : ?>
					<div class="notice notice-success is-dismissible"><p>تنظیمات با موفقیت ذخیره شد.</p></div>
				<?php endif; ?>

				<form method="post">
					<?php wp_nonce_field( 'wcpp_save_settings', 'wcpp_settings_nonce' ); ?>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">فعال‌سازی پاپ‌آپ</th>
							<td>
								<label>
									<input type="checkbox" name="enabled" value="1" <?php checked( $settings['enabled'], 'yes' ); ?> />
									نمایش پاپ‌آپ بعد از افزودن به سبد خرید
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="title">عنوان پاپ‌آپ</label></th>
							<td><input type="text" class="regular-text" id="title" name="title" value="<?php echo esc_attr( $settings['title'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="subtitle">زیرعنوان</label></th>
							<td><input type="text" class="regular-text" id="subtitle" name="subtitle" value="<?php echo esc_attr( $settings['subtitle'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="count">تعداد محصولات پیشنهادی</label></th>
							<td><input type="number" min="1" max="8" id="count" name="count" value="<?php echo esc_attr( $settings['count'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="source">منبع محصولات پیشنهادی</label></th>
							<td>
								<select id="source" name="source">
									<option value="related" <?php selected( $settings['source'], 'related' ); ?>>محصولات مرتبط (پیش‌فرض ووکامرس)</option>
									<option value="upsell" <?php selected( $settings['source'], 'upsell' ); ?>>محصولات جایگزین (Upsell)</option>
									<option value="cross_sell" <?php selected( $settings['source'], 'cross_sell' ); ?>>محصولات مکمل (Cross-sell)</option>
									<option value="category" <?php selected( $settings['source'], 'category' ); ?>>محصولات هم‌دسته</option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="add_text">متن دکمه افزودن</label></th>
							<td><input type="text" class="regular-text" id="add_text" name="add_text" value="<?php echo esc_attr( $settings['add_text'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="added_text">متن بعد از افزودن</label></th>
							<td><input type="text" class="regular-text" id="added_text" name="added_text" value="<?php echo esc_attr( $settings['added_text'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="no_thanks_text">متن دکمه انصراف</label></th>
							<td><input type="text" class="regular-text" id="no_thanks_text" name="no_thanks_text" value="<?php echo esc_attr( $settings['no_thanks_text'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row">نمایش قیمت</th>
							<td><label><input type="checkbox" name="show_price" value="1" <?php checked( $settings['show_price'], 'yes' ); ?> /> نمایش قیمت روی کارت محصولات</label></td>
						</tr>
						<tr>
							<th scope="row">نمایش بج پیشنهاد</th>
							<td>
								<label><input type="checkbox" name="show_badge" value="1" <?php checked( $settings['show_badge'], 'yes' ); ?> /> نمایش بج روی کارت محصولات</label><br>
								<input type="text" class="regular-text" style="margin-top:6px;" name="badge_text" value="<?php echo esc_attr( $settings['badge_text'] ); ?>" placeholder="متن بج">
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="primary_color">رنگ اصلی</label></th>
							<td><input type="text" class="wcpp-color-field" id="primary_color" name="primary_color" value="<?php echo esc_attr( $settings['primary_color'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="secondary_color">رنگ ثانویه (گرادیان)</label></th>
							<td><input type="text" class="wcpp-color-field" id="secondary_color" name="secondary_color" value="<?php echo esc_attr( $settings['secondary_color'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="bg_color">رنگ پس‌زمینه پاپ‌آپ</label></th>
							<td><input type="text" class="wcpp-color-field" id="bg_color" name="bg_color" value="<?php echo esc_attr( $settings['bg_color'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="text_color">رنگ متن</label></th>
							<td><input type="text" class="wcpp-color-field" id="text_color" name="text_color" value="<?php echo esc_attr( $settings['text_color'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="overlay_opacity">شفافیت پس‌زمینه تیره (٪)</label></th>
							<td><input type="number" min="0" max="90" id="overlay_opacity" name="overlay_opacity" value="<?php echo esc_attr( $settings['overlay_opacity'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="border_radius">گردی گوشه‌ها (px)</label></th>
							<td><input type="number" min="0" max="40" id="border_radius" name="border_radius" value="<?php echo esc_attr( $settings['border_radius'] ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><label for="auto_close">بسته‌شدن خودکار (ثانیه)</label></th>
							<td>
								<input type="number" min="0" max="60" id="auto_close" name="auto_close" value="<?php echo esc_attr( $settings['auto_close'] ); ?>">
								<p class="description">عدد صفر یعنی بسته‌شدن خودکار غیرفعال است.</p>
							</td>
						</tr>
					</table>
					
					<b style="background:red;color:#fff"><p style="background: red;text-align: center;padding: 10px;font-size: 16px;">ساخته شده توسط علیرضا رحمتی | اینستاگرام : alireza.webcode | تلگرام : alireza_webcode | طراحی و پشتیبانی انواع وبسایت | 09159835031</p></b>

					<?php submit_button( 'ذخیره تنظیمات', 'primary', 'wcpp_save' ); ?>
				</form>
			</div>
			<?php
		}

		/* ---------------------------------------------------------------
		 * دریافت محصولات پیشنهادی از طریق Ajax
		 * --------------------------------------------------------------- */
		public function ajax_get_related() {
			check_ajax_referer( $this->nonce_action, 'nonce', false );

			$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
			if ( ! $product_id || ! function_exists( 'wc_get_product' ) ) {
				wp_send_json_error( array( 'message' => 'invalid product' ) );
			}

			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				wp_send_json_error( array( 'message' => 'product not found' ) );
			}

			$settings = $this->get_settings();
			$count    = (int) $settings['count'];
			$ids      = array();

			switch ( $settings['source'] ) {
				case 'upsell':
					$ids = $product->get_upsell_ids();
					break;
				case 'cross_sell':
					$ids = $product->get_cross_sell_ids();
					break;
				case 'category':
					$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$ids = wc_get_products(
							array(
								'category' => $terms,
								'exclude'  => array( $product_id ),
								'limit'    => $count * 3,
								'status'   => 'publish',
								'orderby'  => 'popularity',
								'return'   => 'ids',
							)
						);
					}
					break;
				case 'related':
				default:
					$ids = wc_get_related_products( $product_id, $count * 3 );
					break;
			}

			$ids = array_filter( array_map( 'absint', (array) $ids ) );
			$ids = array_diff( $ids, array( $product_id ) );

			$html  = '';
			$found = 0;

			foreach ( $ids as $id ) {
				if ( $found >= $count ) {
					break;
				}
				$p = wc_get_product( $id );
				if ( ! $p || ! $p->is_purchasable() || ! $p->is_in_stock() || 'publish' !== $p->get_status() ) {
					continue;
				}

				$badge = '';
				if ( 'yes' === $settings['show_badge'] ) {
					$badge = '<span class="wcpp-badge">' . esc_html( $settings['badge_text'] ) . '</span>';
				}

				$price = '';
				if ( 'yes' === $settings['show_price'] ) {
					$price = '<div class="wcpp-card-price">' . $p->get_price_html() . '</div>';
				}

				$html .= '<div class="wcpp-card">';
				$html .= '<div class="wcpp-card-img"><a href="' . esc_url( get_permalink( $id ) ) . '" target="_blank" rel="noopener">' . $p->get_image( 'woocommerce_thumbnail' ) . '</a>' . $badge . '</div>';
				$html .= '<div class="wcpp-card-body">';
				$html .= '<a class="wcpp-card-title" href="' . esc_url( get_permalink( $id ) ) . '" target="_blank" rel="noopener">' . esc_html( $p->get_name() ) . '</a>';
				$html .= $price;
				$html .= '<button type="button" class="wcpp-add-btn" data-product_id="' . esc_attr( $id ) . '"><span class="wcpp-btn-text">' . esc_html( $settings['add_text'] ) . '</span><span class="wcpp-spinner"></span></button>';
				$html .= '</div></div>';

				$found++;
			}

			if ( $found === 0 ) {
				wp_send_json_success( array( 'html' => '', 'count' => 0 ) );
			}

			wp_send_json_success( array( 'html' => $html, 'count' => $found ) );
		}

		/* ---------------------------------------------------------------
		 * خروجی سمت کاربر: مارک‌آپ + استایل + اسکریپت
		 * --------------------------------------------------------------- */
		public function render_frontend() {
			if ( is_admin() ) {
				return;
			}
			$settings = $this->get_settings();
			if ( 'yes' !== $settings['enabled'] ) {
				return;
			}

			$primary  = esc_attr( $settings['primary_color'] );
			$secondary = esc_attr( $settings['secondary_color'] );
			$bg       = esc_attr( $settings['bg_color'] );
			$text     = esc_attr( $settings['text_color'] );
			$radius   = (int) $settings['border_radius'];
			$overlay_opacity = (int) $settings['overlay_opacity'] / 100;

			$ajax_url   = admin_url( 'admin-ajax.php' );
			$nonce      = wp_create_nonce( $this->nonce_action );
			$added_text = esc_js( $settings['added_text'] );
			$auto_close = (int) $settings['auto_close'];
			?>
			<style>
				.wcpp-overlay{
					--wcpp-primary: <?php echo $primary; ?>;
					--wcpp-secondary: <?php echo $secondary; ?>;
					--wcpp-bg: <?php echo $bg; ?>;
					--wcpp-text: <?php echo $text; ?>;
					--wcpp-radius: <?php echo $radius; ?>px;
					--wcpp-overlay-opacity: <?php echo $overlay_opacity; ?>;
					position:fixed; inset:0; z-index:999999;
					display:flex; align-items:center; justify-content:center;
					background:rgba(15,15,20,var(--wcpp-overlay-opacity));
					opacity:0; visibility:hidden; pointer-events:none;
					transition:opacity .25s ease;
					direction:rtl; font-family:inherit; padding:16px; box-sizing:border-box;
				}
				.wcpp-overlay.wcpp-active{ opacity:1; visibility:visible; pointer-events:auto; }
				body.wcpp-noscroll{ overflow:hidden; }

				.wcpp-modal{
					background:var(--wcpp-bg); color:var(--wcpp-text);
					width:100%; max-width:640px; max-height:88vh;
					border-radius:var(--wcpp-radius);
					box-shadow:0 25px 60px -15px rgba(0,0,0,.35);
					position:relative; overflow:hidden; display:flex; flex-direction:column;
					transform:translateY(24px) scale(.97); opacity:0;
					transition:transform .3s cubic-bezier(.34,1.56,.64,1), opacity .25s ease;
				}
				.wcpp-overlay.wcpp-active .wcpp-modal{ transform:translateY(0) scale(1); opacity:1; }

				.wcpp-progress{ position:absolute; top:0; right:0; left:0; height:3px; background:transparent; z-index:3; }
				.wcpp-progress::after{
					content:""; display:block; height:100%; width:0%;
					background:linear-gradient(90deg,var(--wcpp-primary),var(--wcpp-secondary));
				}
				.wcpp-progress.wcpp-run::after{ width:var(--wcpp-progress-w,0%); }

				.wcpp-close{
					position:absolute; top:12px; left:12px; z-index:5;
					width:34px; height:34px; border-radius:50%; border:none;
					background:rgba(0,0,0,.06); color:var(--wcpp-text);
					font-size:20px; line-height:1; cursor:pointer;
					display:flex; align-items:center; justify-content:center;
					transition:background .2s, transform .2s;
				}
				.wcpp-close:hover{ background:rgba(0,0,0,.12); transform:rotate(90deg); }

				.wcpp-header{
					padding:26px 26px 14px; text-align:center;
					background:linear-gradient(135deg, color-mix(in srgb, var(--wcpp-primary) 12%, transparent), color-mix(in srgb, var(--wcpp-secondary) 10%, transparent));
				}
				.wcpp-title{ margin:0 0 6px; font-size:19px; font-weight:800; }
				.wcpp-subtitle{ margin:0; font-size:13.5px; opacity:.7; }

				.wcpp-body{ padding:18px 20px 6px; overflow-y:auto; flex:1; }
				.wcpp-grid{
					display:grid; gap:14px;
					grid-template-columns:repeat(auto-fill, minmax(130px,1fr));
				}
				.wcpp-card{
					border:1px solid rgba(0,0,0,.08); border-radius:14px; overflow:hidden;
					display:flex; flex-direction:column; background:var(--wcpp-bg);
					transition:transform .2s ease, box-shadow .2s ease;
				}
				.wcpp-card:hover{ transform:translateY(-4px); box-shadow:0 12px 24px -10px rgba(0,0,0,.18); }
				.wcpp-card-img{ position:relative; aspect-ratio:1/1; overflow:hidden; background:#f5f5f7; }
				.wcpp-card-img img{ width:100%; height:100%; object-fit:cover; display:block; }
				.wcpp-badge{
					position:absolute; top:8px; right:8px; font-size:10.5px; font-weight:700;
					background:var(--wcpp-secondary); color:#fff; padding:3px 8px; border-radius:20px;
				}
				.wcpp-card-body{ padding:10px 10px 12px; display:flex; flex-direction:column; gap:6px; flex:1; }
				.wcpp-card-title{
					font-size:12.5px; font-weight:600; color:var(--wcpp-text); text-decoration:none;
					display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
					min-height:2.6em; line-height:1.3;
				}
				.wcpp-card-price{ font-size:12.5px; font-weight:700; color:var(--wcpp-primary); }
				.wcpp-card-price ins{ text-decoration:none; }
				.wcpp-card-price del{ opacity:.5; font-weight:400; font-size:11px; }

				.wcpp-add-btn{
					margin-top:auto; border:none; cursor:pointer; border-radius:10px;
					padding:8px 10px; font-size:12px; font-weight:700; color:#fff;
					background:linear-gradient(90deg,var(--wcpp-primary),var(--wcpp-secondary));
					display:flex; align-items:center; justify-content:center; gap:6px;
					transition:filter .2s, transform .15s;
				}
				.wcpp-add-btn:hover{ filter:brightness(1.08); }
				.wcpp-add-btn:active{ transform:scale(.97); }
				.wcpp-add-btn.wcpp-added{ background:#16a34a; }
				.wcpp-spinner{
					display:none; width:13px; height:13px; border-radius:50%;
					border:2px solid rgba(255,255,255,.5); border-top-color:#fff;
					animation:wcpp-spin .7s linear infinite;
				}
				.wcpp-add-btn.wcpp-loading-btn .wcpp-btn-text{ display:none; }
				.wcpp-add-btn.wcpp-loading-btn .wcpp-spinner{ display:inline-block; }
				@keyframes wcpp-spin{ to{ transform:rotate(360deg); } }

				.wcpp-loading{ display:flex; align-items:center; justify-content:center; padding:50px 0; }
				.wcpp-loader{
					width:34px; height:34px; border-radius:50%;
					border:3px solid rgba(0,0,0,.08); border-top-color:var(--wcpp-primary);
					animation:wcpp-spin .8s linear infinite;
				}

				.wcpp-footer{ padding:14px 20px 20px; text-align:center; }
				.wcpp-no-thanks-btn{
					border:1px solid rgba(0,0,0,.12); background:transparent; color:var(--wcpp-text);
					padding:9px 18px; border-radius:10px; font-size:13px; cursor:pointer; opacity:.75;
					transition:opacity .2s, background .2s;
				}
				.wcpp-no-thanks-btn:hover{ opacity:1; background:rgba(0,0,0,.04); }

				@media (max-width:600px){
					.wcpp-overlay{ align-items:flex-end; padding:0; }
					.wcpp-modal{
						max-width:100%; width:100%; max-height:85vh;
						border-radius:var(--wcpp-radius) var(--wcpp-radius) 0 0;
						transform:translateY(100%); opacity:1;
					}
					.wcpp-overlay.wcpp-active .wcpp-modal{ transform:translateY(0); }
					.wcpp-grid{ grid-template-columns:repeat(auto-fill, minmax(110px,1fr)); }
				}
			</style>

			<div id="wcpp-overlay" class="wcpp-overlay" aria-hidden="true">
				<div id="wcpp-modal" class="wcpp-modal" role="dialog" aria-modal="true" aria-labelledby="wcpp-title-el">
					<div id="wcpp-progress" class="wcpp-progress"></div>
					<button type="button" id="wcpp-close" class="wcpp-close" aria-label="بستن">&times;</button>
					<div class="wcpp-header">
						<h3 id="wcpp-title-el" class="wcpp-title"><?php echo esc_html( $settings['title'] ); ?></h3>
						<p class="wcpp-subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
					</div>
					<div id="wcpp-body" class="wcpp-body">
						<div class="wcpp-loading"><span class="wcpp-loader"></span></div>
					</div>
					<div class="wcpp-footer">
						<button type="button" id="wcpp-no-thanks" class="wcpp-no-thanks-btn"><?php echo esc_html( $settings['no_thanks_text'] ); ?></button>
					</div>
				</div>
			</div>

			<script>
			(function($){
				"use strict";
				if (typeof $ === "undefined") { return; }

				var wcppVars = {
					ajax_url: <?php echo wp_json_encode( $ajax_url ); ?>,
					nonce: <?php echo wp_json_encode( $nonce ); ?>,
					added_text: <?php echo wp_json_encode( $settings['added_text'] ); ?>,
					auto_close: <?php echo (int) $auto_close; ?>
				};

				var wcppSuppress = false;
				var wcppTimer = null;

				function wcppOpenForProduct(productId){
					if(!productId){ return; }
					var $overlay = $('#wcpp-overlay');
					var $body = $('#wcpp-body');
					$body.html('<div class="wcpp-loading"><span class="wcpp-loader"></span></div>');
					$overlay.addClass('wcpp-active').attr('aria-hidden','false');
					$('body').addClass('wcpp-noscroll');

					$.post(wcppVars.ajax_url, {
						action: 'wcpp_get_related',
						nonce: wcppVars.nonce,
						product_id: productId
					}).done(function(res){
						if(res && res.success && res.data && res.data.count > 0){
							$body.html('<div class="wcpp-grid">' + res.data.html + '</div>');
							wcppStartAutoClose();
						} else {
							wcppClose();
						}
					}).fail(function(){
						wcppClose();
					});
				}

				function wcppClose(){
					$('#wcpp-overlay').removeClass('wcpp-active').attr('aria-hidden','true');
					$('body').removeClass('wcpp-noscroll');
					clearTimeout(wcppTimer);
					$('#wcpp-progress').removeClass('wcpp-run').css('--wcpp-progress-w','0%');
				}

				function wcppStartAutoClose(){
					clearTimeout(wcppTimer);
					var seconds = parseInt(wcppVars.auto_close, 10);
					var $prog = $('#wcpp-progress');
					if(!seconds){ $prog.hide(); return; }
					$prog.show().removeClass('wcpp-run').css('--wcpp-progress-w','0%');
					requestAnimationFrame(function(){
						$prog.css('transition', 'none');
						setTimeout(function(){
							$prog.css('transition','').get(0).style.setProperty('--wcpp-progress-w','100%');
							$prog.addClass('wcpp-run');
						}, 30);
					});
					wcppTimer = setTimeout(wcppClose, seconds * 1000);
				}

				$(document).on('click', '#wcpp-close, #wcpp-no-thanks', function(){
					wcppClose();
				});
				$(document).on('click', '#wcpp-overlay', function(e){
					if(e.target && e.target.id === 'wcpp-overlay'){ wcppClose(); }
				});
				$(document).on('keyup', function(e){
					if(e.key === 'Escape'){ wcppClose(); }
				});

				// افزودن به سبد از طریق فرم/دکمه‌های آژاکسی ووکامرس (لیست محصولات، آرشیو و ...)
				$(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button){
					if(wcppSuppress){ return; }
					var pid = null;
					if($button && $button.data){
						pid = $button.data('product_id');
					}
					if(!pid && $button && $button.attr){
						pid = $button.attr('data-product_id');
					}
					if(pid){ wcppOpenForProduct(pid); }
				});

				// افزودن به سبد از داخل خود پاپ‌آپ
				$(document).on('click', '.wcpp-add-btn', function(e){
					e.preventDefault();
					var $btn = $(this);
					if($btn.hasClass('wcpp-added') || $btn.hasClass('wcpp-loading-btn')){ return; }
					var pid = $btn.data('product_id');
					$btn.addClass('wcpp-loading-btn');

					var ajaxUrl = wcppVars.ajax_url;
					if(window.wc_add_to_cart_params && wc_add_to_cart_params.wc_ajax_url){
						ajaxUrl = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%','add_to_cart');
					}

					$.post(ajaxUrl, { product_id: pid, quantity: 1 }).done(function(res){
						$btn.removeClass('wcpp-loading-btn').addClass('wcpp-added');
						$btn.find('.wcpp-btn-text').text(wcppVars.added_text);

						if(res && res.fragments){
							$.each(res.fragments, function(selector, html){
								$(selector).replaceWith(html);
							});
						}
						wcppSuppress = true;
						$(document.body).trigger('wc_fragment_refresh');
						setTimeout(function(){ wcppSuppress = false; }, 400);
					}).fail(function(){
						$btn.removeClass('wcpp-loading-btn');
					});
				});

				// پشتیبانی از صفحه محصول (افزودن غیرآژاکسی که با ری‌دایرکت روی همان صفحه انجام می‌شود)
				$(function(){
					try {
						var params = new URLSearchParams(window.location.search);
						var addedId = params.get('added-to-cart');
						if(addedId){
							wcppOpenForProduct(addedId);
							params.delete('added-to-cart');
							var qs = params.toString();
							var newUrl = window.location.pathname + (qs ? ('?' + qs) : '') + window.location.hash;
							window.history.replaceState({}, document.title, newUrl);
						}
					} catch(err){ /* مرورگرهای خیلی قدیمی نادیده گرفته می‌شوند */ }
				});

			})(window.jQuery);
			</script>
			<?php
		}
	}

	add_action(
		'plugins_loaded',
		function() {
			WCPP_Suggestion_Popup::instance();
		}
	);
}
