</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-about">
                <div class="logo" style="margin-bottom:16px;">
                    <span class="logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                        </svg>
                    </span>
                    <span>دید برتر</span>
                </div>
                <p>تأمین‌کننده تخصصی تجهیزات امنیتی، نظارتی و ردیابی با بیش از ۶ سال تجربه در بازار ایران.</p>
                <div class="footer-social">
                    <?php if (get_theme_mod('did_social_instagram')): ?>
                    <a href="<?php echo esc_url(get_theme_mod('did_social_instagram')); ?>" aria-label="اینستاگرام" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('did_social_telegram')): ?>
                    <a href="<?php echo esc_url(get_theme_mod('did_social_telegram')); ?>" aria-label="تلگرام" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('did_social_whatsapp')): ?>
                    <a href="<?php echo esc_url(get_theme_mod('did_social_whatsapp')); ?>" aria-label="واتساپ" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>محصولات</h4>
                <ul>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('products'))); ?>">دوربین مداربسته</a></li>
                    <li><a href="#">دوربین خورشیدی</a></li>
                    <li><a href="#">سیستم ردیابی</a></li>
                    <li><a href="#">فلزیاب</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>خدمات</h4>
                <ul>
                    <li><a href="#">هوشمندسازی معادن</a></li>
                    <li><a href="#">تعمیرات تخصصی</a></li>
                    <li><a href="#">نصب و راه‌اندازی</a></li>
                    <li><a href="#">مشاوره رایگان</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>تماس با ما</h4>
                <ul>
                    <li>📞 <?php echo esc_html(get_theme_mod('did_phone', '۰۲۱-۸۸۰۰۰۰۰۰')); ?></li>
                    <li>✉️ <?php echo esc_html(get_theme_mod('did_email', 'info@didbarttar.ir')); ?></li>
                    <li>📍 <?php echo esc_html(get_theme_mod('did_address', 'تهران')); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© <?php echo date('Y'); ?> دید برتر توران تجارت. تمامی حقوق محفوظ است.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<?php
function didbarttar_default_menu() {
    echo '<li><a href="' . esc_url(home_url('/')) . '">خانه</a></li>';
    echo '<li><a href="' . esc_url(home_url('/products')) . '">محصولات</a></li>';
    echo '<li><a href="' . esc_url(home_url('/services')) . '">خدمات</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">تماس</a></li>';
}