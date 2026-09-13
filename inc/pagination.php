<?php
/**
 * Pagination Component
 */

if (!function_exists('didbarttar_pagination')) {
    function didbarttar_pagination($query = null) {
        global $wp_query;
        $query = $query ?: $wp_query;
        
        $total = $query->max_num_pages;
        if ($total <= 1) return;

        $current = max(1, get_query_var('paged'));
        $big = 999999999;

        echo '<nav class="did-pagination" aria-label="صفحه‌بندی">';
        echo '<ul class="pg-list">';

        // دکمه قبلی
        if ($current > 1) {
            echo '<li><a href="' . esc_url(get_pagenum_link($current - 1)) . '" class="pg-btn pg-prev" aria-label="صفحه قبل">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="15 18 9 12 15 6"/></svg>
                  </a></li>';
        }

        // صفحات
        $range = 2;
        for ($i = 1; $i <= $total; $i++) {
            if ($i == 1 || $i == $total || ($i >= $current - $range && $i <= $current + $range)) {
                $class = ($i == $current) ? 'pg-btn pg-active' : 'pg-btn';
                echo '<li><a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $class . '">' . number_format_i18n($i) . '</a></li>';
            } elseif ($i == $current - $range - 1 || $i == $current + $range + 1) {
                echo '<li><span class="pg-dots">...</span></li>';
            }
        }

        // دکمه بعدی
        if ($current < $total) {
            echo '<li><a href="' . esc_url(get_pagenum_link($current + 1)) . '" class="pg-btn pg-next" aria-label="صفحه بعد">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="9 18 15 12 9 6"/></svg>
                  </a></li>';
        }

        echo '</ul>';
        echo '</nav>';
    }
}