<?php
// Add Import & Export Users submenu under gs_user CPT
add_action('admin_menu', function () {
    // Import submenu
    add_submenu_page(
        'edit.php?post_type=gs_user',
        esc_html__('Import Users'),
        esc_html__('Import Users'),
        'edit_others_posts',
        'import-users',
        'gs_render_import_users_page'
    );

    // Export submenu
    add_submenu_page(
        'edit.php?post_type=gs_user',
        esc_html__('Export Users'),
        esc_html__('Export Users'),
        'edit_others_posts',
        'export-users',
        'gs_render_export_users_page'
    );
});

// Run export before any output to prevent "headers already sent"
add_action('admin_init', function () {
    if (
        isset($_POST['export_csv']) &&
        isset($_POST['gs_export_nonce']) &&
        wp_verify_nonce($_POST['gs_export_nonce'], 'gs_export_users') &&
        current_user_can('edit_others_posts')
    ) {
        gs_handle_user_csv_export();
    }
});

/**
 * Render Import Users page
 */
function gs_render_import_users_page()
{
    if (!current_user_can('edit_others_posts')) {
        wp_die(esc_html__('You do not have permission to access this page.'));
    }
    ?>
    <h2><?php esc_html_e('Import Users'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('gs_import_users', 'gs_import_nonce'); ?>
        <input type="file" name="user_csv" accept=".csv" required>
        <?php submit_button(__('Import CSV'), 'primary', 'import_csv'); ?>
    </form>
    <?php

    if (
        isset($_POST['import_csv']) &&
        isset($_FILES['user_csv']) &&
        check_admin_referer('gs_import_users', 'gs_import_nonce')
    ) {
        $result = gs_handle_user_csv_import($_FILES['user_csv']);
        printf(
            '<div class="notice notice-%1$s"><p>%2$s</p></div>',
            $result['status'],
            esc_html($result['message'])
        );
    }
}

/**
 * Handle CSV import logic
 */
function gs_handle_user_csv_import($file)
{
    if (
        empty($file['tmp_name']) ||
        $file['error'] !== UPLOAD_ERR_OK ||
        strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv'
    ) {
        return ['status' => 'error', 'message' => __('Invalid file upload.')];
    }

    // Removed file size limit

    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        return ['status' => 'error', 'message' => __('Unable to open CSV file.')];
    }

    $first_line = preg_replace('/^\xEF\xBB\xBF/', '', fgets($handle));
    $header     = str_getcsv($first_line);

    $imported = 0;
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 6) {
            continue;
        }

        list($title_ignore, $barcode, $name, $college, $course, $type) = array_map('sanitize_text_field', array_map('trim', $row));

        if (empty($barcode) || empty($name)) {
            continue;
        }

        $existing = get_posts([
            'post_type'      => 'gs_user',
            'meta_key'       => 'barcode',
            'meta_value'     => $barcode,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
        if (!empty($existing)) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type'   => 'gs_user',
            'post_title'  => $name,
            'post_status' => 'publish',
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'barcode', $barcode);
            update_post_meta($post_id, 'name', $name);
            update_post_meta($post_id, 'college', $college);
            update_post_meta($post_id, 'course', $course);
            update_post_meta($post_id, 'type', $type);
            $imported++;
        }
    }

    fclose($handle);

    return [
        'status'  => 'success',
        'message' => sprintf(__('%d users imported successfully.'), $imported),
    ];
}

/**
 * Render Export Users page
 */
function gs_render_export_users_page()
{
    if (!current_user_can('edit_others_posts')) {
        wp_die(esc_html__('You do not have permission to access this page.'));
    }
    ?>
    <h2><?php esc_html_e('Export Users'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('gs_export_users', 'gs_export_nonce'); ?>
        <?php submit_button(__('Download CSV'), 'primary', 'export_csv'); ?>
    </form>
    <?php
}

/**
 * Handle CSV export logic
 */
function gs_handle_user_csv_export()
{
    $users = get_posts([
        'post_type'      => 'gs_user',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ]);

    if (empty($users)) {
        wp_die(__('No users found to export.'));
    }

    // File name with current date
    $filename = 'GS_User_list_' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV header row (removed ID)
    fputcsv($output, ['Barcode', 'Name', 'College', 'Course', 'Type']);

    foreach ($users as $user_id) {
        $barcode = get_post_meta($user_id, 'barcode', true);
        $name    = get_post_meta($user_id, 'name', true);
        $college = get_post_meta($user_id, 'college', true);
        $course  = get_post_meta($user_id, 'course', true);
        $type    = get_post_meta($user_id, 'type', true);

        fputcsv($output, [
            $barcode,
            $name,
            $college,
            $course,
            $type
        ]);
    }

    fclose($output);
    exit;
}
