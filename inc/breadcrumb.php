<?php
/**
 * Breadcrumb Navigation
 * سازگار با SEO و Schema.org
 */

if (!function_exists('didbarttar_breadcrumb')) {
    function didbarttar_breadcrumb() {
        // نمایش فقط در صفحات داخلی
        if (is_front_page()) return;

        $separator = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>';
        $home = '<a href="' . esc_url(home_url('/')) . '" class="bc-home" aria-label="خانه">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    خانه
                 </a>';

        echo '<nav class="breadcrumb" aria-label="مسیر navigation">';
        echo '<div class="container"><ol class="bc-list" itemscope itemtype="https://schema.org/BreadcrumbList">';

        $items = [];
        $position = 1;

        // خانه
        $items[] = '<li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="' . esc_url(home_url('/')) . '"><span itemprop="name">خانه</span></a>
                        <meta itemprop="position" content="' . $position++ . '">
                    </li>';

        // صفحه وبلاگ
        if (is_home() && !is_front_page()) {
            $items[] = '<li class="bc-item bc-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name">' . get_the_title(get_option('page_for_posts')) . '</span>
                            <meta itemprop="position" content="' . $position . '">
                        </li>';
        }

        // CPT Archive
        if (is_post_type_archive()) {
            $items[] = '<li class="bc-item bc-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name">' . post_type_archive_title('', false) . '</span>
                            <meta itemprop="position" content="' . $position . '">
                        </li>';
        }

        // دسته‌بندی / تگ
        if (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $items[] = '<li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="' . esc_url(get_term_link($term)) . '"><span itemprop="name">' . esc_html($term->name) . '</span></a>
                            <meta itemprop="position" content="' . $position++ . '">
                        </li>';
        }

        // صفحه تکی
        if (is_singular()) {
            // اگر CPT است، آرشیو را اضافه کن
            $post_type = get_post_type();
            if ($post_type !== 'post' && $post_type !== 'page') {
                $pt_obj = get_post_type_object($post_type);
                $items[] = '<li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a itemprop="item" href="' . esc_url(get_post_type_archive_link($post_type)) . '"><span itemprop="name">' . esc_html($pt_obj->labels->name) . '</span></a>
                                <meta itemprop="position" content="' . $position++ . '">
                            </li>';
            }

            // دسته‌بندی‌های پست
            if (is_single() && has_category()) {
                $cat = get_the_category()[0];
                $items[] = '<li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a itemprop="item" href="' . esc_url(get_category_link($cat)) . '"><span itemprop="name">' . esc_html($cat->name) . '</span></a>
                                <meta itemprop="position" content="' . $position++ . '">
                            </li>';
            }

            // عنوان صفحه
            $items[] = '<li class="bc-item bc-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name">' . get_the_title() . '</span>
                            <meta itemprop="position" content="' . $position . '">
                        </li>';
        }

        // جستجو
        if (is_search()) {
            $items[] = '<li class="bc-item bc-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name">جستجو: ' . esc_html(get_search_query()) . '</span>
                            <meta itemprop="position" content="' . $position . '">
                        </li>';
        }

        // 404
        if (is_404()) {
            $items[] = '<li class="bc-item bc-current"><span>صفحه یافت نشد</span></li>';
        }

        echo implode('<li class="bc-sep">' . $separator . '</li>', $items);
        echo '</ol></div>';
        echo '</nav>';
    }
}