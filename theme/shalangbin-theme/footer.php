</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-top">
			<div><?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
						<span class="brand-mark"><?php shalangbin_icon( 'scan' ); ?></span>
						<span><?php
							$name_f = trim( (string) get_bloginfo( 'name' ) );
							if ( function_exists( 'mb_substr' ) && mb_strlen( $name_f ) > 4 && mb_substr( $name_f, -4 ) === '‌بین' ) {
								echo esc_html( mb_substr( $name_f, 0, -4 ) ) . '<span class="brand-accent">‌بین</span>';
							} else {
								echo esc_html( $name_f );
							}
							?><small><?php echo esc_html( get_theme_mod( 'shalangbin_tagline', 'تخصص در دیدنِ نادیدنی‌ها' ) ); ?></small></span>
					</a>
					<?php
				}
				?>
				<p><?php echo esc_html( get_theme_mod( 'shalangbin_footer_about', 'ابزار مناسب برای هر نقطه دور از دسترس. انتخاب، مقایسه و شناخت دوربین‌های بازرسی.' ) ); ?></p>
			</div>
			<div>
				<h2><?php esc_html_e( 'انتخاب و خرید', 'shalangbin' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer_shop' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_shop', 'container' => false, 'depth' => 1 ) );
				} else {
					$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : -1;
					echo '<a href="' . esc_url( $shop_id > 0 ? get_permalink( $shop_id ) : home_url( '/' ) ) . '">' . esc_html__( 'همه دستگاه‌ها', 'shalangbin' ) . '</a>';
					if ( function_exists( 'wc_get_cart_url' ) ) {
						echo '<a href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'سبد خرید', 'shalangbin' ) . '</a>';
					}
				}
				?>
			</div>
			<div>
				<h2><?php esc_html_e( 'همراه شما', 'shalangbin' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer_support' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_support', 'container' => false, 'depth' => 1 ) );
				} else {
					$contact_id = get_option( 'shalangbin_contact_page' );
					echo '<a href="' . esc_url( $contact_id ? get_permalink( $contact_id ) : home_url( '/' ) ) . '">' . esc_html__( 'ارتباط با ما', 'shalangbin' ) . '</a>';
				}
				?>
			</div>
			<div>
				<h2><?php esc_html_e( 'شلنگ‌بین', 'shalangbin' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer_about' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_about', 'container' => false, 'depth' => 1 ) );
				} else {
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'درباره ما', 'shalangbin' ) . '</a>';
				}
				?>
				<div class="footer-note"><?php echo esc_html( get_theme_mod( 'shalangbin_footer_note', 'قیمت‌ها و موجودی پس از تأیید نهایی اعلام می‌شود.' ) ); ?></div>
			</div>
		</div>
		<div class="footer-bottom">
			<span><?php bloginfo( 'name' ); ?> — <?php echo esc_html( get_theme_mod( 'shalangbin_tagline', 'تخصص در دیدنِ نادیدنی‌ها' ) ); ?></span>
			<span><?php esc_html_e( 'طراحی برای انتخاب آگاهانه', 'shalangbin' ); ?></span>
		</div>
	</div>
</footer>

<div class="toast" role="status" aria-live="polite" hidden></div>
<div class="compare-tray" hidden>
	<div>
		<b><span data-compare-count>۰</span> دستگاه برای مقایسه</b>
		<span>تفاوت‌ها را کنار هم ببینید</span>
	</div>
	<a class="btn btn-primary" href="<?php echo esc_url( shalangbin_compare_url() ); ?>">مقایسه <?php shalangbin_icon( 'arrow' ); ?></a>
	<button class="icon-button" data-clear-compare aria-label="پاک کردن فهرست مقایسه"><?php shalangbin_icon( 'close' ); ?></button>
</div>

<?php wp_footer(); ?>
</body>
</html>
