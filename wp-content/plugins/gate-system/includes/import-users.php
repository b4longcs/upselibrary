<?php

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=gs_user', 'Import Users', 'Import Users', 'manage_options', 'import-users', function () {
        echo '<h2>Import Users</h2>
              <form method="post" enctype="multipart/form-data">
                  <input type="file" name="user_csv" required>
                  <button type="submit" name="import_csv">Import CSV</button>
              </form>';

        if (isset($_POST['import_csv']) && !empty($_FILES['user_csv']['tmp_name'])) {
            $csv = array_map('str_getcsv', file($_FILES['user_csv']['tmp_name']));
            unset($csv[0]); // skip header

            foreach ($csv as $row) {
                [$title, $barcode, $name, $college, $course, $type] = array_map('sanitize_text_field', $row);

                // Avoid duplicate by barcode
                $existing = new WP_Query([
                    'post_type' => 'gs_user',
                    'meta_query' => [[
                        'key' => 'barcode_number',
                        'value' => $barcode,
                    ]]
                ]);

                if (!$existing->have_posts()) {
                    $id = wp_insert_post([
                        'post_type' => 'gs_user',
                        'post_title' => $title,
                        'post_status' => 'publish',
                    ]);
                    update_post_meta($id, 'barcode_number', $barcode);
                    update_post_meta($id, 'name', $name);
                    update_post_meta($id, 'college', $college);
                    update_post_meta($id, 'course', $course);
                    update_post_meta($id, 'type', $type);
                }
            }


            echo '<div class="notice notice-success"><p>Users imported.</p></div>';
        }
    });
});
