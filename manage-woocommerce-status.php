<?php
/*
Plugin Name: مدیریت وضعیت ووکامرس
Description: افزونه‌ای برای مدیریت وضعیت‌های سفارشی ووکامرس. نسخه 1 اضافه شدن وضعیت «چاپ شده» قبل از تکمیل شد.
Version: 1.0.0
Author: رافق مجتهدزاده
Author URI: https://rafig.ir
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: manage-woocommerce-status
*/

if (!defined('ABSPATH')) exit;

/**
 * ثبت وضعیت پیش‌فرض: چاپ شده
 * در آینده می‌توان این بخش را به منوی مدیریت وضعیت‌ها گسترش داد.
 */
add_action('init', function () {
    register_post_status('wc-printed', [
        'label'                     => 'چاپ شده',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('چاپ شده <span class="count">(%s)</span>', 'چاپ شده <span class="count">(%s)</span>')
    ]);
});

// افزودن به لیست وضعیت‌ها (قبل از Completed)
add_filter('wc_order_statuses', function ($order_statuses) {
    $new_order_statuses = [];
    foreach ($order_statuses as $key => $label) {
        if ($key === 'wc-completed') {
            $new_order_statuses['wc-printed'] = 'چاپ شده';
        }
        $new_order_statuses[$key] = $label;
    }
    return $new_order_statuses;
});

// دکمه تغییر وضعیت در پنل سفارش
add_filter('woocommerce_admin_order_actions', function ($actions, $order) {
    if ($order->get_status() !== 'printed') {
        $actions['mark_printed'] = [
            'url'    => wp_nonce_url(admin_url(
                'admin-ajax.php?action=woocommerce_mark_order_status&status=printed&order_id=' . $order->get_id()
            ), 'woocommerce-mark-order-status'),
            'name'   => 'علامت‌گذاری به عنوان چاپ شده',
            'action' => 'printed',
        ];
    }
    return $actions;
}, 10, 2);

// استایل رنگ و آیکون وضعیت چاپ شده
add_action('admin_head', function () {
    echo '<style>
        /* رنگ پس‌زمینه در جدول سفارش‌ها */
        .order-status.status-printed {
            background: #f39c12 !important; /* نارنجی */
            color: #fff !important;
        }
        /* آیکون اکشن در لیست سفارش‌ها */
        .wc-action-button-printed::after {
            content: "\270F"; /* قلم */
        }
    </style>';
});

// اجازه تغییر وضعیت در ویرایش سفارش
add_filter('woocommerce_valid_order_statuses_for_order_edit', function ($statuses) {
    $statuses[] = 'printed';
    return $statuses;
});
