<?php
function didbarttar_schema_org() {
    if (is_front_page()) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'دید برتر توران تجارت',
            'url' => home_url('/'),
            'logo' => get_custom_logo() ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : '',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => did_get_option('phone'),
                'contactType' => 'customer service',
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
add_action('wp_head', 'didbarttar_schema_org');

function didbarttar_og_tags() {
    if (is_singular()) {
        global $post;
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        if (has_post_thumbnail()) {
            echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url($post, 'large')) . '">' . "\n";
        }
        echo '<meta property="og:locale" content="fa_IR">' . "\n";
    }
}
add_action('wp_head', 'didbarttar_og_tags');