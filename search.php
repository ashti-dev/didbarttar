<?php get_header(); ?>

<?php didbarttar_breadcrumb(); ?>

<section class="section">
    <div class="container">
        <header class="page-header" data-reveal="up">
            <span class="section-label">نتایج جستجو</span>
            <h1 class="page-title">جستجو برای: «<?php echo get_search_query(); ?>»</h1>
            <p class="section-subtitle" style="margin:12px auto 0;">
                <?php echo number_format_i18n($wp_query->found_posts); ?> نتیجه یافت شد
            </p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="prod-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="prod-card" data-reveal="up">
                        <div class="prod-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?>
                            </a>
                        </div>
                        <div class="prod-body">
                            <div class="prod-cat"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?></div>
                            <h3 class="prod-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="prod-desc"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                            <div class="prod-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="padding:8px 16px;font-size:12px;">مشاهده</a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php didbarttar_pagination(); ?>
        <?php else : ?>
            <div class="archive-empty">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
                <h3>نتیجه‌ای یافت نشد</h3>
                <p>عبارت دیگری را امتحان کنید یا به فروشگاه سر بزنید.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">بازگشت به خانه</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>