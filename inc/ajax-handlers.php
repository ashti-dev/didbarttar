<?php
// فرم تماس AJAX
function didbarttar_handle_contact() {
    check_ajax_referer('didbarttar_nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    
    if (empty($name) || empty($phone)) {
        wp_send_json_error(['message' => 'لطفاً فیلدهای الزامی را پر کنید']);
    }
    
    $to = did_get_option('email', get_option('admin_email'));
    $subject = 'درخواست مشاوره جدید از ' . $name;
    $body = "نام: {$name}\nتلفن: {$phone}\n\nپیام:\n{$message}";
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    
    $sent = wp_mail($to, $subject, $body, $headers);
    
    if ($sent) {
        wp_send_json_success(['message' => 'پیام شما با موفقیت ارسال شد']);
    } else {
        wp_send_json_error(['message' => 'خطا در ارسال پیام']);
    }
}
add_action('wp_ajax_did_contact', 'didbarttar_handle_contact');
add_action('wp_ajax_nopriv_did_contact', 'didbarttar_handle_contact');