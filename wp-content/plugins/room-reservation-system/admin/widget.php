<?php
if (!defined('ABSPATH')) exit;

add_action('wp_dashboard_setup', 'rrs_add_dashboard_widget');

function rrs_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'rrs_dashboard_widget',
        '📅 Room Reservation Overview',
        'rrs_dashboard_widget_display'
    );
}
// Enqueue Chart.js for dashboard only
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'index.php') {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }
});

function rrs_dashboard_widget_display() {
    $statuses = ['pending', 'publish', 'draft'];
    $status_labels = ['Pending', 'Approved', 'Denied'];
    $status_keys = ['pending', 'approved', 'denied'];
    $status_icons = ['🕓', '✅', '❌'];
    $bg_colors = ['#fdecea', '#e8f8f0', '#f4f4f4'];
    $text_colors = ['#c0392b', '#27ae60', '#7f8c8d'];
    $now = current_time('timestamp');

    $tally = [
        'daily' => array_fill_keys($status_keys, 0),
        'monthly' => array_fill_keys($status_keys, 0),
        'yearly' => array_fill_keys($status_keys, 0),
        'total' => array_fill_keys($status_keys, 0),
    ];

    $all_posts = get_posts([
        'post_type' => 'reservation_request',
        'posts_per_page' => -1,
        'post_status' => $statuses,
        'orderby' => 'meta_value',
        'meta_key' => 'date',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'date',
                'compare' => 'EXISTS'
            ]
        ],
    ]);

    $upcoming = [];
    foreach ($all_posts as $post) {
        $key = get_post_meta($post->ID, 'status', true);
        if (!in_array($key, ['pending', 'approved', 'denied'])) {
            $key = 'pending'; // fallback
        }
        $date = get_post_meta($post->ID, 'date', true);
        if (!$date) continue;
        $timestamp = strtotime($date);
        if (!$timestamp) continue;

        if (date('Y-m-d', $timestamp) === date('Y-m-d', $now)) $tally['daily'][$key]++;
        if (date('Y-m', $timestamp) === date('Y-m', $now))   $tally['monthly'][$key]++;
        if (date('Y', $timestamp) === date('Y', $now))       $tally['yearly'][$key]++;
        $tally['total'][$key]++;

        // Collect upcoming approved reservations
        if ($key === 'approved' && $timestamp >= $now) {
            $room = get_post_meta($post->ID, 'room', true);
            $time = get_post_meta($post->ID, 'time', true);
            $upcoming[] = [
                'title' => get_the_title($post->ID),
                'date' => $date,
                'room' => $room,
                'time' => $time
            ];
        }
    }

    // Limit to next 5 upcoming reservations
    $upcoming = array_slice($upcoming, 0, 5);

    $admin_url = admin_url('edit.php?post_type=reservation_request');
    $export_url = admin_url('admin-post.php?action=export_approved_reservations_csv');
    $is_admin = current_user_can('administrator');
    ?>

    <style>
        .rrs-widget-container { font-family: "Tex Gyre Adventor", sans-serif; }
        .rrs-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }
        .rrs-card {
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 13px;
            border: 1px solid transparent;
        }
        .rrs-card h4 {
            font-size: 14px !important;
            margin: 0 0 8px !important;
            font-weight: bold !important;
            color: #000 !important;
        }
        .rrs-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .rrs-card ul li { margin-bottom: 4px; }

        .rrs-buttons {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .rrs-buttons a.button {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-weight: 500;
            display: inline-block;
        }

        .rrs-view-btn {
            background-color: #0073aa;
            color: #fff;
        }

        .rrs-view-btn:hover { background-color: #005a87; }

        .rrs-export-btn {
            background-color: #46b450;
            color: #fff;
        }

        .rrs-export-btn:hover { background-color: #39863c; }

        #rrs-chart-container {
            max-width: 100%;
            margin: 30px auto 20px;
        }

        .rrs-upcoming {
            margin-top: 20px;
        }

        .rrs-upcoming h4 {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .rrs-upcoming ul {
            list-style: disc inside;
            margin: 0;
            padding-left: 16px;
            font-size: 13px;
        }

        .rrs-upcoming ul li {
            margin-bottom: 6px;
        }

        @media (max-width: 600px) {
            .rrs-cards {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <div class="rrs-widget-container">
        <div class="rrs-cards">
            <?php foreach ($status_keys as $i => $key): ?>
                <div class="rrs-card" style="background-color: <?= $bg_colors[$i] ?>; color: <?= $text_colors[$i] ?>;">
                    <h4><?= $status_icons[$i] . ' ' . ucfirst($key) ?></h4>
                    <ul>
                        <li><strong>Today:</strong> <?= $tally['daily'][$key] ?></li>
                        <li><strong>This Month:</strong> <?= $tally['monthly'][$key] ?></li>
                        <li><strong>This Year:</strong> <?= $tally['yearly'][$key] ?></li>
                        <li><strong>Total:</strong> <?= $tally['total'][$key] ?></li>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="rrs-chart-container">
            <canvas id="rrsChart" height="180" aria-label="Reservation chart" role="img"></canvas>
        </div>

        <?php if (!empty($upcoming)): ?>
        <div class="rrs-upcoming">
            <h4>📌 Upcoming Reservations</h4>
            <ul>
                <?php foreach ($upcoming as $res): ?>
                    <li>
                        <?= esc_html(date('M d, Y', strtotime($res['date']))) ?> –
                        <?= esc_html($res['room'] ?: 'Room') ?>,
                        <?= esc_html($res['time'] ? date('g:i A', strtotime($res['time'])) : 'Time not set') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="rrs-buttons">
            <?php if ($is_admin): ?>
                <a class="button rrs-export-btn" href="<?= esc_url($export_url) ?>">Export CSV Report</a>
            <?php endif; ?>
            <a class="button rrs-view-btn" href="<?= esc_url($admin_url) ?>">View All Reservations</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('rrsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pending', 'Approved', 'Denied'],
                    datasets: [{
                        label: 'Total Reservations',
                        data: [
                            <?= $tally['total']['pending'] ?>,
                            <?= $tally['total']['approved'] ?>,
                            <?= $tally['total']['denied'] ?>
                        ],
                        backgroundColor: ['#e74c3c', '#2ecc71', '#7f8c8d'],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: context => `${context.dataset.label}: ${context.parsed.y}`
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { display: false },
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
    });
    </script>
<?php
}
