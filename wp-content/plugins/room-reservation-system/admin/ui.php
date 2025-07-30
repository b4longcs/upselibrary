<?php
if (!defined('ABSPATH')) exit;

// Filters: Room & Date
add_action('restrict_manage_posts', function () {
    global $typenow;
    if ($typenow !== 'reservation_request') return;

    $room = sanitize_text_field($_GET['room_filter'] ?? '');
    $date = sanitize_text_field($_GET['date_filter'] ?? '');

    echo '<select name="room_filter"><option value="">All Rooms</option>';
    for ($i = 1; $i <= 6; $i++) {
        $r = "Room $i";
        printf('<option value="%s"%s>%s</option>', esc_attr($r), selected($room, $r, false), esc_html($r));
    }
    echo '</select>';
    printf('<input type="text" name="date_filter" id="rrs-date-filter" placeholder="Filter by Date" value="%s" style="width:130px;margin-left:10px;" autocomplete="off">', esc_attr($date));
});

// Meta Query Filters
add_filter('parse_query', function ($query) {
    if (!is_admin() || $query->get('post_type') !== 'reservation_request') return;

    $meta = [];
    foreach (['room', 'time', 'date'] as $key) {
        if (!empty($_GET["{$key}_filter"])) {
            $meta[] = ['key' => $key, 'value' => sanitize_text_field($_GET["{$key}_filter"])];
        }
    }
    if ($meta) {
        $existing = $query->get('meta_query') ?: [];
        $query->set('meta_query', array_merge($existing, $meta));
    }
});

// Admin Scripts & Styles
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'edit.php' || sanitize_key($_GET['post_type'] ?? '') !== 'reservation_request') return;

    wp_enqueue_style('flatpickr-css', esc_url('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'));
    wp_enqueue_script('flatpickr-js', esc_url('https://cdn.jsdelivr.net/npm/flatpickr'), [], null, true);
    wp_add_inline_script('flatpickr-js', "document.addEventListener('DOMContentLoaded',()=>flatpickr('#rrs-date-filter',{dateFormat:'Y-m-d',allowInput:true}));");
    wp_enqueue_style('rrs-admin-style', esc_url(plugin_dir_url(__FILE__) . 'assets/css/rrs-admin.css'));
});

// Tab UI: Rooms & Times
add_filter('views_edit-reservation_request', function ($views) {
    $base = admin_url('edit.php?post_type=reservation_request');
    $room = sanitize_text_field($_GET['room_filter'] ?? '');
    $time = sanitize_text_field($_GET['time_filter'] ?? '');

    echo '<div class="room-tabs" style="margin:10px 0;display:flex;gap:10px;">';
    echo '<a class="room-tab' . ($room === '' ? ' active' : '') . '" href="' . esc_url($base) . '">All Rooms</a>';
    for ($i = 1; $i <= 6; $i++) {
        $r = "Room $i";
        $url = add_query_arg('room_filter', rawurlencode($r), $base);
        echo '<a class="room-tab' . ($room === $r ? ' active' : '') . '" href="' . esc_url($url) . '">' . esc_html($r) . '</a>';
    }
    echo '</div>';

    if ($room !== '') {
        echo '<div class="time-tabs" style="margin-bottom:10px;display:flex;gap:8px;">';
        echo '<a class="time-tab' . ($time === '' ? ' active' : '') . '" href="' . esc_url(add_query_arg('room_filter', $room, $base)) . '">All</a>';
        for ($i = 8; $i <= 16; $i++) {
            $t = "$i:00";
            $label = date('g A', strtotime($t)) . ' - ' . date('g A', strtotime(($i + 1) . ':00'));
            $url = add_query_arg(['room_filter' => $room, 'time_filter' => $t], $base);
            echo '<a class="time-tab' . ($time === $t ? ' active' : '') . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        }
        echo '</div>';
    }

    return $views;
});

// Post class based on time
add_filter('post_class', function ($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'reservation_request') {
        $time = get_post_meta($post_id, 'time', true);
        if ($time && preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            $hour = str_pad((int) explode(':', $time)[0], 2, '0', STR_PAD_LEFT);
            $classes[] = 'time-' . $hour;
        }
    }
    return $classes;
}, 10, 3);
