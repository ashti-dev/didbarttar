<section class="section section-alt" id="products">
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:20px;margin-bottom:40px;">
            <div data-reveal="up">
                <span class="section-label">پرفروش‌ترین‌ها</span>
                <h2 class="section-title">محصولات ویژه</h2>
            </div>
            <a href="<?php echo esc_url(did_shop_url()); ?>" class="btn btn-outline" data-reveal="up">
                مشاهده همه محصولات
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            </a>
        </div>

        <div class="prod-grid">
            <?php
            /* اولویت ۱: محصولات ویژه (Featured) ووکامرس */
            $query = new WP_Query([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'no_found_rows'  => true,
                'tax_query'      => [[
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                ]],
            ]);

            /* اولویت ۲: اگر ویژه‌ای نبود، جدیدترین‌ها */
            if (!$query->have_posts()) {
                $query = new WP_Query([
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => 3,
                    'no_found_rows'  => true,
                ]);
            }

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if (!$product) continue;

                    /* برچسب: تخفیف > پرفروش > جدید */
                    $badge = '';
                    $created = $product->get_date_created();
                    if ($product->is_on_sale()) {
                        $badge = '<span class="prod-badge badge-sale">تخفیف</span>';
                    } elseif ($product->is_featured()) {
                        $badge = '<span class="prod-badge badge-hot">پرفروش</span>';
                    } elseif ($created && $created->getTimestamp() > strtotime('-30 days')) {
                        $badge = '<span class="prod-badge badge-new">جدید</span>';
                    }
            ?>
            <article class="prod-card" data-reveal="up">
                <div class="prod-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url(wc_placeholder_img('medium_large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                        <?php endif; ?>
                    </a>
                    <?php echo $badge; ?>
                </div>
                <div class="prod-body">
                    <div class="prod-cat"><?php echo wc_get_product_category_list(get_the_ID(), ', '); ?></div>
                    <h3 class="prod-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="prod-desc"><?php echo wp_trim_words(wp_strip_all_tags($product->get_short_description()), 15); ?></p>
                    <div class="prod-footer">
                        <div class="prod-price"><?php echo $product->get_price_html(); ?></div>
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="padding:8px 16px;font-size:12px;">جزئیات</a>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p style="grid-column:1/-1;text-align:center;padding:48px 24px;background:#fff;border:1px dashed var(--border);border-radius:12px;color:var(--text-mid);">
                    هنوز محصولی در فروشگاه ثبت نشده است.
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>