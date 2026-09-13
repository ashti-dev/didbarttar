<?php
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
add_filter('xmlrpc_enabled', '__return_false');

function didbarttar_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'didbarttar_security_headers');

function didbarttar_limit_login() {
    if (isset($_POST['log'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = (int) get_transient("did_login_{$ip}");
        if ($attempts >= 5) wp_die('تلاش بیش از حد', 'خطا', ['response' => 429]);
        set_transient("did_login_{$ip}", $attempts + 1, 900);
    }
}
add_action('wp_login_failed', 'didbarttar_limit_login');
add_action('wp_login', function() { delete_transient("did_login_{$_SERVER['REMOTE_ADDR']}"); });