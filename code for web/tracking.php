<?php


// ثبت وضعیت جدید به‌عنوان یک post status در وردپرس
add_action('init', function () {
    register_post_status('wc-shipped', [
        'label'                     => 'ارسال شده',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop(
            'ارسال شده <span class="count">(%s)</span>',
            'ارسال شده <span class="count">(%s)</span>'
        ),
    ]);
});

// معرفی وضعیت جدید به ووکامرس تا در دراپ‌داون تغییر وضعیت سفارش (در پنل ادمین) نمایش داده شود
add_filter('wc_order_statuses', function ($order_statuses) {
    $new_statuses = [];
    foreach ($order_statuses as $key => $label) {
        $new_statuses[$key] = $label;
        // بلافاصله بعد از "در حال پردازش"، وضعیت "ارسال شده" رو اضافه می‌کنیم
        if ('wc-processing' === $key) {
            $new_statuses['wc-shipped'] = 'ارسال شده';
        }
    }
    return $new_statuses;
});

// اضافه کردن وضعیت جدید به لیست وضعیت‌های قابل‌انتخاب در اکشن‌های دسته‌جمعی (Bulk Actions) پنل سفارشات
add_filter('bulk_actions-edit-shop_order', function ($actions) {
    $actions['mark_shipped'] = 'تغییر وضعیت به ارسال شده';
    return $actions;
});

add_filter('handle_bulk_actions-edit-shop_order', function ($redirect_to, $action, $post_ids) {
    if ($action !== 'mark_shipped') {
        return $redirect_to;
    }
    foreach ($post_ids as $post_id) {
        $order = wc_get_order($post_id);
        if ($order) {
            $order->update_status('shipped');
        }
    }
    return $redirect_to;
}, 10, 3);


/* ============================================================
   بخش ۲: نمایش ویجت ترکینگ سفارش در پنل کاربری
   ============================================================ */

add_action('woocommerce_account_dashboard', function () {

    if (!is_user_logged_in()) return;

    $orders = wc_get_orders([
        'customer_id' => get_current_user_id(),
        'limit'       => 1,
        'orderby'     => 'date',
        'order'       => 'DESC'
    ]);

    if (empty($orders)) return;

    $order  = $orders[0];
    $status = $order->get_status(); // مقدار بدون پیشوند wc- برمی‌گرده، مثل: processing, shipped, completed

    // نگاشت وضعیت‌های واقعی ووکامرس به مراحل نمایش (۱، ۲، ۳)
    $steps = [
        'pending'    => 1, // در انتظار پرداخت -> هنوز اول کاره
        'processing' => 1, // در حال پردازش
        'on-hold'    => 1, // در انتظار بررسی
        'shipped'    => 2, // ارسال شده (وضعیت سفارشی که در بخش ۱ اضافه شد)
        'completed'  => 3, // تحویل شده / تکمیل‌شده
    ];

    // اگر وضعیت سفارش لغو‌شده، مرجوعی یا ناموفق بود، ویجت نمایش داده نشه
    // (چون نمایش نوار پیشرفت برای این وضعیت‌ها گمراه‌کننده‌ست)
    $hidden_statuses = ['cancelled', 'refunded', 'failed', 'checkout-draft'];
    if (in_array($status, $hidden_statuses, true)) {
        return;
    }

    $current = isset($steps[$status]) ? $steps[$status] : 1;

    $order_date  = $order->get_date_created() ? $order->get_date_created()->date_i18n('j F Y') : '';
    $order_total = $order->get_formatted_order_total();
    $items_count = $order->get_item_count();
    $view_url    = $order->get_view_order_url();

    // آیکون‌های SVG (وکتور، بدون تاری در هیچ سایزی)
    $icon_check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';
    $icon_box   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>';
    $icon_truck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>';
    $icon_home  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>';
    $icon_cal   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
    $icon_tag   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 12l9 9 10-10V2z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor" stroke="none"/></svg>';
    $icon_arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

    ?>
    <style>
        .track-ui {
            --c1: #4f46e5;
            --c2: #06b6d4;
            --ink: #0f1222;
            --sub: #8b8fa3;
            --line: #eef0f6;
            position: relative;
            background: #ffffff;
            border: 1px solid #eef0f6;
            border-radius: 24px;
            padding: 2px;
            margin: 26px 0;
            font-family: inherit;
            box-shadow: 0 1px 2px rgba(15, 18, 34, 0.04), 0 20px 45px -12px rgba(15, 18, 34, 0.12);
            overflow: hidden;
            animation: mds-fade-in 0.5s ease;
        }

        .track-ui::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--c1), var(--c2), var(--c1));
            background-size: 200% 100%;
            animation: mds-shine 4s linear infinite;
        }

        @keyframes mds-shine {
            to { background-position: -200% 0; }
        }

        @keyframes mds-fade-in {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .track-inner {
            padding: 30px 30px 28px;
        }

        .track-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 24px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--line);
        }

        .track-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .track-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            background: linear-gradient(145deg, var(--c1), var(--c2));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 18px -4px rgba(79, 70, 229, 0.45);
        }

        .track-icon svg {
            width: 24px;
            height: 24px;
            color: #fff;
        }

        .track-title {
            font-size: 14px;
            color: var(--sub);
            font-weight: 500;
        }

        .track-title b {
            color: var(--ink);
            font-weight: 800;
            font-size: 16.5px;
            letter-spacing: -0.01em;
        }

        .track-meta {
            font-size: 12.5px;
            color: var(--sub);
            margin-top: 8px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .track-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .track-meta svg {
            width: 14px;
            height: 14px;
            opacity: 0.65;
        }

        .track-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }

        .track-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 7px 14px 7px 16px;
            border-radius: 999px;
            background: linear-gradient(90deg, #eef2ff, #ecfeff);
            color: #4338ca;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: inset 0 0 0 1px rgba(79, 70, 229, 0.08);
        }

        .track-badge .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--c1);
            position: relative;
        }

        .track-badge .pulse-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: var(--c1);
            opacity: 0.4;
            animation: mds-pulse 1.8s ease-out infinite;
        }

        @keyframes mds-pulse {
            0% { transform: scale(0.6); opacity: 0.5; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        .track-link {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            background: #f7f7fb;
            transition: all 0.2s ease;
        }

        .track-link svg {
            width: 13px;
            height: 13px;
            transition: transform 0.2s ease;
        }

        .track-link:hover {
            background: var(--ink);
            color: #fff;
        }

        .track-link:hover svg {
            transform: translateX(-3px);
        }

        .track-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 0 8px;
        }

        .track-bar::before {
            content: '';
            position: absolute;
            top: 26px;
            left: 8px;
            right: 8px;
            height: 4px;
            background: var(--line);
            border-radius: 10px;
            z-index: 0;
        }

        .track-bar::after {
            content: '';
            position: absolute;
            top: 26px;
            right: 8px;
            left: auto;
            height: 4px;
            background: linear-gradient(270deg, var(--c1), var(--c2));
            border-radius: 10px;
            width: calc((100% - 16px) * <?php echo ($current - 1) / 2; ?>);
            transition: width 0.6s cubic-bezier(.4,0,.2,1);
            z-index: 0;
        }

        .step {
            width: 33.33%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .dot {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            margin: 0 auto 14px;
            background: #ffffff;
            border: 2.5px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c4c7d4;
            transition: all 0.4s cubic-bezier(.4,0,.2,1);
            position: relative;
            z-index: 2;
        }

        .dot svg {
            width: 21px;
            height: 21px;
        }

        .step.active .dot {
            background: linear-gradient(145deg, var(--c1), var(--c2));
            color: #ffffff;
            box-shadow: 0 10px 20px -6px rgba(79, 70, 229, 0.5);
            outline: 1px solid #fff;
        }

        .step.current .dot {
            box-shadow: 0 0 0 7px rgba(79, 70, 229, 0.12), 0 10px 20px -6px rgba(79, 70, 229, 0.5);
        }

        .step.current .dot::after {
            content: '';
            position: absolute;
            width: 66px;
            height: 66px;
            border-radius: 50%;
            border: 1.5px solid rgba(79, 70, 229, 0.3);
            animation: mds-ring 2s ease-out infinite;
        }

        @keyframes mds-ring {
            0% { transform: scale(0.8); opacity: 0.8; }
            100% { transform: scale(1.15); opacity: 0; }
        }

        .label {
            font-size: 13px;
            color: var(--sub);
            font-weight: 600;
            line-height: 1.5;
        }

        .sub-label {
            font-size: 11px;
            color: #c4c7d4;
            margin-top: 3px;
            font-weight: 500;
        }

        .step.active .label {
            color: var(--ink);
        }

        .step.active .sub-label {
            color: var(--sub);
        }

        @media (max-width: 480px) {
            .track-inner {
                padding: 24px 18px 22px;
            }
            .track-header {
                flex-direction: column;
            }
            .track-right {
                align-items: flex-start;
                width: 100%;
            }
            .dot {
                width: 42px;
                height: 42px;
            }
            .dot svg {
                width: 17px;
                height: 17px;
            }
            .label {
                font-size: 11.5px;
            }
            .sub-label {
                display: none;
            }
        }
    </style>

    <div class="track-ui">
        <div class="track-inner">
            <div class="track-header">
                <div class="track-title-wrap">
                    <span class="track-icon"><?php echo $icon_box; ?></span>
                    <div>
                        <div class="track-title">آخرین سفارش شما &nbsp;<b>#<?php echo esc_html($order->get_id()); ?></b></div>
                        <div class="track-meta">
                            <?php if ($order_date) : ?>
                                <span><?php echo $icon_cal; ?><?php echo esc_html($order_date); ?></span>
                            <?php endif; ?>
                            <span><?php echo $icon_tag; ?><?php echo wp_kses_post($order_total); ?></span>
                            <span><?php echo $icon_box; ?><?php echo esc_html($items_count); ?> کالا</span>
                        </div>
                    </div>
                </div>
                <div class="track-right">
                    <span class="track-badge">
                        <span class="pulse-dot"></span>
                        <?php echo esc_html(wc_get_order_status_name($status)); ?>
                    </span>
                    <?php if ($view_url) : ?>
                        <a class="track-link" href="<?php echo esc_url($view_url); ?>">
                            مشاهده جزئیات <?php echo $icon_arrow; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="track-bar">
                <div class="step <?php echo ($current >= 1 ? 'active' : ''); ?> <?php echo ($current == 1 ? 'current' : ''); ?>">
                    <div class="dot"><?php echo ($current > 1 ? $icon_check : $icon_box); ?></div>
                    <div class="label">در حال پردازش</div>
                    <div class="sub-label">ثبت و آماده‌سازی</div>
                </div>
                <div class="step <?php echo ($current >= 2 ? 'active' : ''); ?> <?php echo ($current == 2 ? 'current' : ''); ?>">
                    <div class="dot"><?php echo ($current > 2 ? $icon_check : $icon_truck); ?></div>
                    <div class="label">ارسال شده</div>
                    <div class="sub-label">در مسیر تحویل</div>
                </div>
                <div class="step <?php echo ($current >= 3 ? 'active' : ''); ?> <?php echo ($current == 3 ? 'current' : ''); ?>">
                    <div class="dot"><?php echo ($current >= 3 ? $icon_check : $icon_home); ?></div>
                    <div class="label">تحویل شده</div>
                    <div class="sub-label">رسیده به مقصد</div>
                </div>
            </div>
        </div>
    </div>
    <?php
});
