<section class="section section-dark">
    <div class="container">
        <div style="text-align:center;margin-bottom:20px;">
            <span class="section-label" data-reveal="up">خدمات تخصصی</span>
            <h2 class="section-title" style="color:#fff;margin:0 auto 16px;" data-reveal="up" data-delay="100">فراتر از فروش محصول</h2>
            <p class="section-subtitle" style="color:var(--color-navy-pale);margin:0 auto;" data-reveal="up" data-delay="200">مشاوره، نصب، راه‌اندازی و پشتیبانی — همه با تیم متخصص.</p>
        </div>
        
        <div class="srv-grid">
            <?php
            $services = [
                ['icon' => 'shield', 'title' => 'هوشمندسازی معادن', 'desc' => 'طراحی و پیاده‌سازی سیستم‌های نظارتی یکپارچه برای معادن با شرایط سخت.', 'features' => ['دوربین‌های ضدانفجار و ضدگردوغبار', 'سیستم کنترل تردد پرسنل', 'مانیتورینگ 24/7 از راه دور', 'یکپارچه‌سازی با سیستم‌های موجود']],
                ['icon' => 'tool', 'title' => 'تعمیرات تخصصی', 'desc' => 'تعمیر انواع دوربین‌های مداربسته، DVR و NVR با قطعات اصلی و گارانتی.', 'features' => ['تعمیر برد و چیپست', 'بازسازی DVR و NVR', 'کالیبراسیون و تنظیم', 'گارانتی ۳ ماهه']],
            ];
            foreach ($services as $i => $s): ?>
            <div class="srv-card" data-reveal="<?php echo $i === 0 ? 'right' : 'left'; ?>">
                <div class="srv-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <?php if ($s['icon'] === 'shield'): ?><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <?php else: ?><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        <?php endif; ?>
                    </svg>
                </div>
                <h3 class="srv-title"><?php echo esc_html($s['title']); ?></h3>
                <p class="srv-desc"><?php echo esc_html($s['desc']); ?></p>
                <ul class="srv-features">
                    <?php foreach ($s['features'] as $f): ?><li><?php echo esc_html($f); ?></li><?php endforeach; ?>
                </ul>
                <a href="#contact" class="btn btn-ghost" style="padding:10px 20px;font-size:13px;">درخواست مشاوره</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>