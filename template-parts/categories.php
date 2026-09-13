<section class="section" id="categories">
    <div class="container">
        <div style="text-align:center;margin-bottom:48px;">
            <span class="section-label" data-reveal="up">دسته‌بندی محصولات</span>
            <h2 class="section-title" style="margin:0 auto 12px;" data-reveal="up" data-delay="100">راهکار برای هر نیاز</h2>
            <p class="section-subtitle" style="margin:0 auto;" data-reveal="up" data-delay="200">از تجهیزات امنیتی خانگی تا سیستم‌های صنعتی و معدنی — همه در یک جا.</p>
        </div>

        <div class="cat-grid">
            <?php
            $terms = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'number'     => 4,
                'orderby'    => 'count',
                'order'      => 'DESC',
            ]);

            /* آیکون‌ها به ترتیب چرخشی */
            $icons = [
                '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
                '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>',
                '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
                '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            ];

            if (!is_wp_error($terms) && !empty($terms)) :
                foreach ($terms as $i => $term) :
                    $desc = $term->description ? $term->description : 'مشاهده انواع محصولات ' . $term->name;
            ?>
            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="cat-card" data-reveal="up" data-delay="<?php echo $i * 100; ?>">
                <span class="icon-box-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo $icons[$i % 4]; ?></svg>
                </span>
                <div class="cat-count"><?php echo number_format_i18n($term->count); ?> محصول</div>
                <h3 class="cat-title"><?php echo esc_html($term->name); ?></h3>
                <p class="cat-desc"><?php echo esc_html(wp_trim_words($desc, 12)); ?></p>
                <span class="cat-link">مشاهده محصولات
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                </span>
            </a>
            <?php
                endforeach;
            else :
                /* حالت fallback: هنوز دسته‌بندی ساخته نشده */
            ?>
            <p style="grid-column:1/-1;text-align:center;padding:40px;background:var(--bg-section);border:1px dashed var(--border);border-radius:12px;color:var(--text-mid);">
                هنوز دسته‌بندی‌ای ساخته نشده است. از بخش محصولات → دسته‌بندی‌ها اقدام کنید.
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>