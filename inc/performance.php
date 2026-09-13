<?php
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

function didbarttar_preload() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head', 'didbarttar_preload', 1);

function didbarttar_lazy_content($content) {
    if (is_admin()) return $content;
    return preg_replace('/<img(.*?)src=/i', '<img$1loading="lazy" decoding="async" src=', $content);
}
add_filter('the_content', 'didbarttar_lazy_content');
add_filter('post_thumbnail_html', function($html) {
    return preg_replace('/<img/', '<img loading="lazy" decoding="async"', $html, 1);
});

function didbarttar_defer_scripts($tag, $handle) {
    if (in_array($handle, ['jquery-core', 'jquery'])) return str_replace(' src', ' defer src', $tag);
    return $tag;
}
add_filter('script_loader_tag', 'didbarttar_defer_scripts', 10, 2);

// حذف query string از استاتیک‌ها
function didbarttar_remove_query_strings($src) {
    return strpos($src, '?ver=') ? remove_query_arg('ver', $src) : $src;
}
add_filter('script_loader_src', 'didbarttar_remove_query_strings');
add_filter('style_loader_src', 'didbarttar_remove_query_strings');