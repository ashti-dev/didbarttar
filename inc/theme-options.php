<?php
function didbarttar_customize_register($wp_customize) {
    $sections = [
        'did_contact' => ['title' => 'اطلاعات تماس', 'fields' => [
            ['id' => 'phone', 'label' => 'تلفن', 'type' => 'text', 'default' => '۲۱-۸۸۰۰۰۰۰۰'],
            ['id' => 'email', 'label' => 'ایمیل', 'type' => 'email', 'default' => 'info@didbarttar.ir'],
            ['id' => 'address', 'label' => 'آدرس', 'type' => 'textarea'],
        ]],
        'did_social' => ['title' => 'شبکه‌های اجتماعی', 'fields' => [
            ['id' => 'social_instagram', 'label' => 'اینستاگرام', 'type' => 'url'],
            ['id' => 'social_telegram', 'label' => 'تلگرام', 'type' => 'url'],
            ['id' => 'social_whatsapp', 'label' => 'واتساپ', 'type' => 'url'],
        ]],
    ];
    
    foreach ($sections as $id => $sec) {
        $wp_customize->add_section($id, ['title' => $sec['title'], 'priority' => 30]);
        foreach ($sec['fields'] as $f) {
            $wp_customize->add_setting("did_{$f['id']}", ['default' => $f['default'] ?? '', 'sanitize_callback' => $f['type'] === 'email' ? 'sanitize_email' : ($f['type'] === 'url' ? 'esc_url_raw' : 'sanitize_text_field')]);
            $wp_customize->add_control("did_{$f['id']}", ['label' => $f['label'], 'section' => $id, 'type' => $f['type']]);
        }
    }
}
add_action('customize_register', 'didbarttar_customize_register');

function did_get_option($key, $default = '') {
    return get_theme_mod("did_{$key}", $default);
}