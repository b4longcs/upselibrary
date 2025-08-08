<?php
// Adds an "Export Logs" submenu under the Gate System admin area.
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=gs_user',
        'Export Users Logs',
        'Export Users Logs',
        'edit_others_posts',
        'export-logs',
        function () {
            if (!current_user_can('edit_others_posts')) {
                wp_die(__('Unauthorized access.'));
            }

            $file_path = plugin_dir_path(dirname(__FILE__)) . 'gate-logs.csv';
            $college_options = ['' => 'All Colleges'];

            if (file_exists($file_path)) {
                $handle = fopen($file_path, 'r');
                if ($handle) {
                    $all_colleges = [];
                    while (($row = fgetcsv($handle)) !== false) {
                        if (isset($row[1])) {
                            $all_colleges[] = trim($row[1]);
                        }
                    }
                    fclose($handle);
                    $unique_colleges = array_unique($all_colleges);
                    sort($unique_colleges);
                    foreach ($unique_colleges as $college) {
                        if (!empty($college)) {
                            $college_options[$college] = $college;
                        }
                    }
                }
            }

            ?>
            <div class="wrap">
                <h2>Export Logs</h2>
                <form method="post">
                    <?php wp_nonce_field('gs_export_logs_action', 'gs_export_logs_nonce'); ?>
                    <label>Start Date:
                        <input type="date" name="start_date" required>
                    </label>
                    <label>End Date:
                        <input type="date" name="end_date" required>
                    </label>
                    <br><br>
                    <label>College:
                        <select name="filter_college">
                            <?php foreach ($college_options as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <br><br>
                    <button name="export_logs" class="button button-primary">Download CSV</button>
                </form>
            </div>
            <?php
        }
    );
});

add_action('admin_init', function () {
    if (isset($_POST['export_logs'])) {
        // Validate nonce for CSRF protection
        if (!isset($_POST['gs_export_logs_nonce']) || !wp_verify_nonce($_POST['gs_export_logs_nonce'], 'gs_export_logs_action')) {
            wp_die(__('Security check failed'));
        }

        // Check user capability
        if (!current_user_can('edit_others_posts')) {
            wp_die(__('Unauthorized export attempt.'));
        }

        // Sanitize inputs
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $filter_college = strtolower(trim(sanitize_text_field($_POST['filter_college'])));

        $start_ts = strtotime($start_date . ' 00:00:00');
        $end_ts = strtotime($end_date . ' 23:59:59');
        if (!$start_ts || !$end_ts) {
            wp_die(__('Invalid date range provided.'));
        }

        $file_path = plugin_dir_path(dirname(__FILE__)) . 'gate-logs.csv';
        if (!file_exists($file_path)) {
            wp_die(__('Log file not found.'));
        }

        $handle = fopen($file_path, 'r');
        if (!$handle) {
            wp_die(__('Unable to open log file.'));
        }

        $all_rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $all_rows[] = $row;
        }
        fclose($handle);

        if (empty($all_rows)) {
            wp_die(__('No logs found.'));
        }

        $header = $all_rows[0];
        $filtered_rows = [];

        foreach (array_slice($all_rows, 1) as $row) {
            $college = strtolower($row[1] ?? '');
            $time_in_str = $row[5] ?? '';
            $log_ts = strtotime($time_in_str);

            if (
                $log_ts >= $start_ts && $log_ts <= $end_ts &&
                ($filter_college === '' || strpos($college, $filter_college) !== false)
            ) {
                $filtered_rows[] = $row;
            }
        }

        if (empty($filtered_rows)) {
            wp_die(__('No logs match the selected filters.'));
        }

        // Output CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=gate-logs-export.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Name', 'College', 'Course', 'Type', 'Barcode', 'Time In', 'Time']);
        foreach ($filtered_rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }
});
