<?php
/*
Template Name: صفحه تماس
*/
get_header();

didbarttar_breadcrumb();

// پردازش فرم
$form_sent = false;
$form_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['did_contact_nonce'])) {
    if (!wp_verify_nonce($_POST['did_contact_nonce'], 'did_contact_form')) {
        $form_error = 'خطای امنیتی. لطفاً دوباره تلاش کنید.';
    } else {
        $name = sanitize_text_field($_POST['name'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $subject = sanitize_text_field($_POST['subject'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            $form_error = 'نام و شماره تماس الزامی است.';
        } elseif (!preg_match('/^[\d\-\+\s]{10,15}$/', $phone)) {
            $form_error = 'شماره تماس معتبر نیست.';
        } else {
            $to = did_get_option('email', get_option('admin_email'));
            $subj = 'درخواست تماس جدید: ' . $subject;
            $body = "نام: {$name}\nتلفن: {$phone}\nایمیل: {$email}\nموضوع: {$subject}\n\nپیام:\n{$message}";
            $headers = ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email];

            if (wp_mail($to, $subj, $body, $headers)) {
                $form_sent = true;
            } else {
                $form_error = 'خطا در ارسال پیام. لطفاً بعداً تلاش کنید.';
            }
        }
    }
}
?>

<section class="contact-page section">
    <div class="container">
        <header class="contact-header" data-reveal="up">
            <span class="section-label">تماس با ما</span>
            <h1 class="contact-title">در ارتباط باشید</h1>
            <p class="contact-subtitle">
                کارشناسان ما در کمتر از ۲ ساعت پاسخگوی شما هستند
            </p>
        </header>

        <div class="contact-grid">
            <!-- اطلاعات تماس -->
            <div class="contact-info" data-reveal="right">
                <div class="contact-info-card">
                    <h3>راه‌های ارتباطی</h3>
                    <p>از هر طریقی که راحت‌ترید با ما در تماس باشید</p>

                    <div class="contact-methods">
                        <a href="tel:<?php echo esc_attr(did_get_option('phone')); ?>" class="contact-method">
                            <div class="contact-method-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="contact-method-content">
                                <div class="contact-method-label">تماس تلفنی</div>
                                <div class="contact-method-value" dir="ltr">
                                    <?php echo esc_html(did_get_option('phone', '۰۱-۸۸۰۰۰۰۰۰')); ?>
                                </div>
                            </div>
                        </a>

                        <a href="mailto:<?php echo esc_attr(did_get_option('email')); ?>" class="contact-method">
                            <div class="contact-method-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="contact-method-content">
                                <div class="contact-method-label">ایمیل</div>
                                <div class="contact-method-value">
                                    <?php echo esc_html(did_get_option('email', 'info@didbarttar.ir')); ?>
                                </div>
                            </div>
                        </a>

                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div class="contact-method-content">
                                <div class="contact-method-label">آدرس</div>
                                <div class="contact-method-value">
                                    <?php echo esc_html(did_get_option('address', 'تهران، ایران')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="contact-method-content">
                                <div class="contact-method-label">ساعات کاری</div>
                                <div class="contact-method-value">
                                    شنبه تا چهارشنبه: ۹ تا ۱۸<br>
                                    پنجشنبه: ۹ تا ۱۳
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- شبکه‌های اجتماعی -->
                    <div class="contact-social">
                        <h4>ما را دنبال کنید</h4>
                        <div class="contact-social-links">
                            <?php if (did_get_option('social_instagram')) : ?>
                            <a href="<?php echo esc_url(did_get_option('social_instagram')); ?>" target="_blank" rel="noopener" aria-label="اینستاگرام">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="2" y="2" width="20" height="20" rx="5"/>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                            <?php if (did_get_option('social_telegram')) : ?>
                            <a href="<?php echo esc_url(did_get_option('social_telegram')); ?>" target="_blank" rel="noopener" aria-label="تلگرام">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M22 2L11 13"/>
                                    <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                            <?php if (did_get_option('social_whatsapp')) : ?>
                            <a href="<?php echo esc_url(did_get_option('social_whatsapp')); ?>" target="_blank" rel="noopener" aria-label="واتساپ">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- فرم تماس -->
            <div class="contact-form-wrapper" data-reveal="left">
                <div class="contact-form-card">
                    <h3>فرم درخواست مشاوره</h3>
                    <p>فرم زیر را پر کنید، کارشناسان ما با شما تماس می‌گیرند</p>

                    <?php if ($form_sent) : ?>
                        <div class="form-success">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--status-success)" stroke-width="1.8">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <h4>پیام شما ارسال شد</h4>
                            <p>کارشناسان ما به زودی با شما تماس خواهند گرفت.</p>
                        </div>
                    <?php else : ?>
                        <?php if ($form_error) : ?>
                            <div class="form-error">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <?php echo esc_html($form_error); ?>
                            </div>
                        <?php endif; ?>

                        <form class="contact-form" method="POST" action="<?php the_permalink(); ?>" id="contact-form">
                            <?php wp_nonce_field('did_contact_form', 'did_contact_nonce'); ?>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">نام و نام خانوادگی <span class="required">*</span></label>
                                    <input type="text" id="name" name="name" required value="<?php echo esc_attr($_POST['name'] ?? ''); ?>" placeholder="نام کامل خود را وارد کنید">
                                </div>
                                <div class="form-group">
                                    <label for="phone">شماره تماس <span class="required">*</span></label>
                                    <input type="tel" id="phone" name="phone" required value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" placeholder="۰۹۱۲۳۴۵۶۷۸۹" dir="ltr">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">ایمیل (اختیاری)</label>
                                    <input type="email" id="email" name="email" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" placeholder="example@email.com" dir="ltr">
                                </div>
                                <div class="form-group">
                                    <label for="subject">موضوع</label>
                                    <select id="subject" name="subject">
                                        <option value="مشاوره خرید">مشاوره خرید</option>
                                        <option value="استعلام قیمت">استعلام قیمت</option>
                                        <option value="پشتیبانی فنی">پشتیبانی فنی</option>
                                        <option value="تعمیرات">تعمیرات</option>
                                        <option value="همکاری">همکاری</option>
                                        <option value="سایر">سایر</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message">توضیحات</label>
                                <textarea id="message" name="message" rows="5" placeholder="نیاز خود را شرح دهید..."><?php echo esc_textarea($_POST['message'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-hero" style="width:100%;justify-content:center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                ارسال درخواست مشاوره
                            </button>

                            <p class="form-note">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                اطلاعات شما نزد ما محفوظ است و فقط برای تماس استفاده می‌شود.
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- نقشه -->
        <div class="contact-map" data-reveal="up">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d207371.9779837607!2d51.2097334!3d35.6970118!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f8e00491ff3dcd9%3A0xf0b3697c567024bc!2sTehran%2C%20Tehran%20Province%2C%20Iran!5e0!3m2!1sen!2s!4v1234567890"
                width="100%" 
                height="400" 
                style="border:0;border-radius:var(--radius-xl);" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="موقعیت ما روی نقشه">
            </iframe>
        </div>
    </div>
</section>

<?php get_footer(); ?>