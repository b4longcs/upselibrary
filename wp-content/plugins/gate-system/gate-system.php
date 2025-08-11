<?php
/*
Plugin Name: Gate System
Description: RFID/Barcode-based library gate system with user logging and carousel.
Version: 1.0
Author: Jonathan Tubo
*/

if (!defined('ABSPATH')) exit; // Prevent direct access

// Include modules
require_once plugin_dir_path(__FILE__) . 'includes/cpt-users.php';
require_once plugin_dir_path(__FILE__) . 'includes/import-users.php';
require_once plugin_dir_path(__FILE__) . 'includes/export-logs.php';
require_once plugin_dir_path(__FILE__) . 'includes/carousel-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-log.php';

// Enqueue frontend assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('gs-css', plugin_dir_url(__FILE__) . 'assets/css/gs-css.css');
    wp_enqueue_style('tex-gyre', 'https://fonts.cdnfonts.com/css/tex-gyre-adventor');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');
    

    if (is_page_template('page-gate-scanner.php')) {
        wp_enqueue_style('gs-frontend-style', plugin_dir_url(__FILE__) . 'assets/css/scanner.css');
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        wp_enqueue_script('gs-frontend-script', plugin_dir_url(__FILE__) . 'assets/js/scanner.js', [], null, true);
        wp_localize_script('gs-frontend-script', 'gs_frontend', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('gs_frontend_nonce'),
        ]);

        // Enqueue the fullscreen helper script here
        wp_enqueue_script('gs-js', plugin_dir_url(__FILE__) . 'assets/js/gs-js.js', [], null, true);
    }
});

add_action('admin_menu', function () {
    if (!is_admin()) return;

    $user = wp_get_current_user();
    $username = $user->user_login;
    $roles = (array) $user->roles;

    // Block access for Subscriber, Author, Contributor completely
    $blocked_roles = ['subscriber', 'author', 'contributor'];

    // If user has any blocked role, remove all plugin menus including Gate System CPT
    if (array_intersect($blocked_roles, $roles)) {
        remove_menu_page('edit.php?post_type=gs_user');        // Gate System CPT menu
        remove_menu_page('room-reservation-system');            // Your other plugin menu slug (find exact slug)
        remove_menu_page('new-acquisition');                     // Your other plugin menu slug
        return;
    }

    // Specific username restrictions
    if ($username === 'ahbarboza') {
        remove_menu_page('new-acquisition');
    }
}, 999);




// Create gate log table on activation
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . 'gate_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        rfid VARCHAR(100),
        name VARCHAR(100),
        time DATETIME DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

// AJAX: Save user meta via admin
add_action('wp_ajax_gs_save_user_meta', function () {
    define('GS_AJAX_SAVE', true);

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gs_user_meta_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $post_id = intval($_POST['post_id']);
    $fields  = $_POST['fields'] ?? [];

    // Check for duplicate barcode before saving
    $barcode = $fields['barcode'] ?? '';
    if ($barcode) {
        $existing = new WP_Query([
            'post_type'     => 'gs_user',
            'meta_key'      => 'barcode',
            'meta_value'    => $barcode,
            'post__not_in'  => [$post_id],
            'fields'        => 'ids',
        ]);

        if ($existing->have_posts()) {
            wp_send_json_error([
                'duplicate_name' => get_the_title($existing->posts[0]),
                'message'        => 'Duplicate barcode detected.'
            ]);
        }
    }

    // ✅ Proceed with saving if no duplicate found
    foreach ($fields as $key => $val) {
        update_post_meta($post_id, sanitize_key($key), sanitize_text_field($val));
    }

    wp_send_json_success(['message' => 'User metadata saved.', 'post_id' => $post_id]);
});

// AJAX: Handle frontend scan request
add_action('wp_ajax_gs_scan_user', 'gs_handle_scan');
add_action('wp_ajax_nopriv_gs_scan_user', 'gs_handle_scan');

function gs_handle_scan() {
    check_ajax_referer('gs_frontend_nonce', 'nonce');

    $barcode = sanitize_text_field($_POST['barcode'] ?? '');

    $query = new WP_Query([
        'post_type'  => 'gs_user',
        'meta_query' => [[
            'key'   => 'barcode',
            'value' => $barcode,
        ]],
        'posts_per_page' => 1
    ]);

    if ($query->have_posts()) {
        $post    = $query->posts[0];
        $name    = get_post_meta($post->ID, 'name', true);
        $college = get_post_meta($post->ID, 'college', true);
        $course  = get_post_meta($post->ID, 'course', true);
        $type    = get_post_meta($post->ID, 'type', true);

        $dt      = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $datePH  = $dt->format('F d, Y');
        $timePH  = $dt->format('g:i A');

        $log = '"' . implode('","', array_map('esc_csv', [$name, $college, $course, $type, $barcode, $datePH, $timePH])) . '"';

        file_put_contents(plugin_dir_path(__FILE__) . 'gate-logs.csv', $log . PHP_EOL, FILE_APPEND | LOCK_EX);

        wp_send_json_success([
            'message' => "Welcome, $name!",
            'name'    => $name,
            'course'  => $course,
            'college' => $college,
        ]);
    } else {
        wp_send_json_error(['message' => 'User not found.']);
    }
}

// Escape values for CSV safely
function esc_csv($value) {
    $value = str_replace('"', '""', $value); // escape double quotes
    return $value;
}

// Add Dashboard Widget for Daily User Logs Graph
add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget('gs_daily_logs', 'Gate System – Daily User Logs', 'gs_render_daily_logs_widget');
});

function gs_render_daily_logs_widget() {
    $csv_file = plugin_dir_path(__FILE__) . 'gate-logs.csv';
    $daily_counts = file_exists($csv_file)
        ? array_reduce(array_map('str_getcsv', file($csv_file)), function ($carry, $row) {
            if (!empty($row[5])) $carry[trim($row[5])] = ($carry[trim($row[5])] ?? 0) + 1;
            return $carry;
        }, [])
        : [];

    uksort($daily_counts, fn($a, $b) => strtotime($a) - strtotime($b));

    $dates  = array_keys($daily_counts);
    $counts = array_values($daily_counts);

    $today       = date('F d, Y');
    $week_start  = strtotime('monday this week');
    $today_total = $daily_counts[$today] ?? 0;
    $week_total  = array_sum(array_filter($daily_counts, fn($count, $date) => strtotime($date) >= $week_start, ARRAY_FILTER_USE_BOTH));
    $total_scans = array_sum($counts);
    ?>
    <style>
    .gs-summary{display:flex;gap:8px;margin-bottom:15px;font-family:'Poppins',sans-serif}
    .gs-tile{flex:1;border:1px solid #000;padding:16px;border-radius:12px;text-align:center}
    .gs-tile h3{margin:0;font-size:22px !important;font-weight:bold !important;color:#000}
    .gs-tile span{display:block;font-size:12px;color:#000;font-weight:bold;margin-top:4px;text-transform:uppercase}
    </style>

    <div class="gs-summary">
        <?php foreach ([['Today',$today_total],['This Week',$week_total],['All Time',$total_scans]] as [$label,$val]): ?>
            <div class="gs-tile"><h3><?= $val ?></h3><span><?= $label ?></span></div>
        <?php endforeach; ?>
    </div>

    <canvas id="gs-daily-logs-chart" height="200"></canvas>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        new Chart(document.getElementById('gs-daily-logs-chart'),{
            type:'bar',
            data:{
                labels:<?= json_encode($dates) ?>,
                datasets:[{data:<?= json_encode($counts) ?>,backgroundColor:'#00573f',borderWidth:0,borderRadius:4}]
            },
            options:{
                responsive:true,
                plugins:{legend:{display:false},tooltip:{titleFont:{family:'Poppins'},bodyFont:{family:'Poppins'}}},
                scales:{
                    x:{ticks:{font:{family:'Poppins'}}},
                    y:{beginAtZero:true,ticks:{font:{family:'Poppins'},stepSize:1}}
                }
            }
        });
    });
    </script>
    <?php
}

