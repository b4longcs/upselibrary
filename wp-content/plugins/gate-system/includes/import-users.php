<?php

// Add Import Users submenu under gs_user CPT
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=gs_user', 'Import Users', 'Import Users', 'manage_options', 'import-users', function () {
        echo '<h2>Import Users</h2>
              <form method="post" enctype="multipart/form-data">
                  <input type="file" name="user_csv" accept=".csv" required>
                  <button type="submit" name="import_csv" class="button button-primary">Import CSV</button>
              </form>';

        if (isset($_POST['import_csv']) && !empty($_FILES['user_csv']['tmp_name'])) {
            // Read and sanitize CSV rows
            $csv = array_map('str_getcsv', file($_FILES['user_csv']['tmp_name']));
            unset($csv[0]); // Skip header

            foreach ($csv as $row) {
                if (count($row) < 6) {
                    continue; // Skip invalid rows
                }

                [$title, $barcode, $name, $college, $course, $type] = array_map('sanitize_text_field', $row);

                // Avoid duplicate by barcode
                $existing = new WP_Query([
                    'post_type' => 'gs_user',
                    'posts_per_page' => 1,
                    'meta_query' => [[
                        'key' => 'barcode_number',
                        'value' => $barcode,
                    ]]
                ]);

                if (!$existing->have_posts()) {
                    $id = wp_insert_post([
                        'post_type' => 'gs_user',
                        'post_title' => $title ?: $name,
                        'post_status' => 'publish',
                    ]);

                    if (!is_wp_error($id)) {
                        update_post_meta($id, 'barcode_number', $barcode);
                        update_post_meta($id, 'name', $name);
                        update_post_meta($id, 'college', $college);
                        update_post_meta($id, 'course', $course);
                        update_post_meta($id, 'type', $type);
                    }
                }
            }

            echo '<div class="notice notice-success"><p>Users imported successfully.</p></div>';
        }
    });
});
