<?php
// Register the Custom Post Type for Gate System Users
add_action('init', function () {
    register_post_type('gs_user', [
        'labels' => [
            'name'          => 'Gate System',
            'singular_name' => 'User',
            'add_new_item'  => 'Add New User',
        ],
        'public'        => false,
        'show_ui'       => true,
        'menu_icon'     => 'dashicons-id-alt',
        'supports'      => [],
        'show_in_rest'  => false,
    ]);
});

// Admin UI Enhancements
add_action('admin_head', function () {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'gs_user') {
        echo '<style>
            .column-barcode, .column-college, .column-type { font-weight: 600; }
            .column-name { color: #0073aa; }
        </style>';
    }
});

// Remove editor and title fields
add_action('admin_init', function () {
    remove_post_type_support('gs_user', 'editor');
    remove_post_type_support('gs_user', 'title');
});

// Custom Field for "Name"
add_action('edit_form_after_title', function ($post) {
    if ($post->post_type !== 'gs_user') return;

    $name = esc_attr(get_post_meta($post->ID, 'name', true));
    echo '<div style="padding:10px 0">
        <label><strong>Name:</strong></label><br>
        <input type="text" name="gs_name" value="' . $name . '" style="width:100%; font-size:18px;">
    </div>';
});

// Admin Notice on Successful Add
add_action('admin_notices', function () {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'gs_user' && isset($_GET['added'])) {
        echo '<div class="notice notice-success is-dismissible"><p>User successfully added.</p></div>';
    }
});

// Enqueue Custom JS for Meta Management
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
    if (get_post_type() !== 'gs_user') return;

    wp_enqueue_script(
        'gs-user-meta-js',
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/gs-user-meta.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('gs-user-meta-js', 'gs_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('gs_user_meta_nonce'),
    ]);
});

// Cleanup UI: Remove Comments, Hide "Add New"
add_action('admin_menu', function () {
    remove_meta_box('commentstatusdiv', 'gs_user', 'normal');
    remove_meta_box('commentsdiv', 'gs_user', 'normal');

    // Remove "Add New" submenu
    global $submenu;
    if (!empty($submenu['edit.php?post_type=gs_user'])) {
        foreach ($submenu['edit.php?post_type=gs_user'] as $index => $item) {
            if (in_array('post-new.php?post_type=gs_user', $item, true)) {
                unset($submenu['edit.php?post_type=gs_user'][$index]);
            }
        }
    }
});

// Meta Box for Custom Fields
add_action('add_meta_boxes', function () {
    add_meta_box('gs_user_meta', 'User Info', function ($post) {
        $meta = [
            'course'  => get_post_meta($post->ID, 'course', true),
            'barcode' => get_post_meta($post->ID, 'barcode', true),
            'type'    => get_post_meta($post->ID, 'type', true),
            'college' => get_post_meta($post->ID, 'college', true),
        ];

        $colleges = get_option('gs_colleges', []);
        $types = ['Student', 'Staff', 'Faculty', 'Visitor'];

        // College field
        echo '<p><label><strong>College:</strong><br>
            <select name="gs_college" id="gs_college" style="width:85%">';
        foreach ($colleges as $college) {
            $selected = selected($college, $meta['college'], false);
            echo "<option value='" . esc_attr($college) . "' $selected>" . esc_html($college) . '</option>';
        }
        echo '</select>
            <button type="button" class="button" id="add-college-btn" style="margin-left:5px;">Add</button>
        </label></p>';

        // Add College Form UI
        echo '<div id="add-college-form" style="display:none; margin-top:10px;">
            <input type="text" id="new-college-name" placeholder="New college name..." style="width:70%">
            <button type="button" class="button button-primary" id="save-college-btn">Save</button>
            <button type="button" class="button" id="cancel-college-btn">Cancel</button>
        </div>';

        // College List Toggle
        echo '<p><button type="button" id="toggle-college-list" class="button">See All College List</button></p>
              <div id="college-list-wrapper" style="display:none; margin-top:10px;">
                  <div id="college-list"></div>
              </div>';

        // Course Field
        echo '<p><label><strong>Course:</strong><br>
            <input type="text" name="gs_course" value="' . esc_attr($meta['course']) . '" class="widefat"></label></p>';

        // Barcode Field
        echo '<p><label><strong>Barcode:</strong><br>
            <input type="text" name="gs_barcode" value="' . esc_attr($meta['barcode']) . '" style="width:100%"></label></p>';

        // Type Field
        echo '<p><label><strong>Type:</strong><br>
            <select name="gs_type" style="width:100%">';
        foreach ($types as $type) {
            $selected = selected($type, $meta['type'], false);
            echo "<option value='" . esc_attr($type) . "' $selected>" . esc_html($type) . '</option>';
        }
        echo '</select></label></p>';
    }, 'gs_user');
});

// Save gs_user meta fields when post is saved
add_action('save_post_gs_user', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['name', 'college', 'course', 'barcode', 'type'];
    foreach ($fields as $field) {
        $key = 'gs_' . $field;
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$key]));
        }
    }

    if (!empty($_POST['gs_name'])) {
        $name = sanitize_text_field($_POST['gs_name']);
        if ($name !== get_the_title($post_id)) {
            remove_action('save_post_gs_user', __FUNCTION__);
            wp_update_post([
                'ID'         => $post_id,
                'post_title' => $name,
                'post_name'  => sanitize_title($name),
            ]);
            add_action('save_post_gs_user', __FUNCTION__);
        }
    }

    if (isset($_POST['publish'])) {
        wp_redirect(admin_url('edit.php?post_type=gs_user&added=1'));
        exit;
    }
});

// Define admin columns for gs_user post list
add_filter('manage_gs_user_posts_columns', function () {
    return [
        'cb'      => '<input type="checkbox" />',
        'barcode' => 'Barcode Number',
        'name'    => 'Name',
        'college' => 'College',
        'course'  => 'Course',
        'type'    => 'Type',
        'date'    => 'Date',
    ];
});

// Render custom column values in gs_user list
add_action('manage_gs_user_posts_custom_column', function ($column, $post_id) {
    $meta_keys = ['barcode', 'name', 'college', 'course', 'type'];
    if (in_array($column, $meta_keys, true)) {
        echo esc_html(get_post_meta($post_id, $column, true));
    }
}, 10, 2);

// AJAX: Save gs_user from modal form
add_action('wp_ajax_gs_save_user_meta', function () {
    check_ajax_referer('gs_user_meta_nonce', 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized', 403);

    $post_id = intval($_POST['post_id'] ?? 0);
    $fields  = json_decode(stripslashes($_POST['fields'] ?? ''), true);
    if (empty($fields) || !is_array($fields)) wp_send_json_error('Invalid field data.');

    $allowed_fields = ['name', 'college', 'course', 'barcode', 'type'];

    if (!$post_id) {
        if (empty($fields['name'])) wp_send_json_error('Name is required to create a new user.');
        $post_id = wp_insert_post([
            'post_type'   => 'gs_user',
            'post_status' => 'publish',
            'post_title'  => sanitize_text_field($fields['name']),
            'post_name'   => sanitize_title($fields['name']),
        ]);
        if (is_wp_error($post_id)) wp_send_json_error('Failed to create new user.');
    } else {
        if (!empty($fields['name'])) {
            wp_update_post([
                'ID'         => $post_id,
                'post_title' => sanitize_text_field($fields['name']),
                'post_name'  => sanitize_title($fields['name']),
            ]);
        }
    }

    foreach ($allowed_fields as $field) {
        if (isset($fields[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($fields[$field]));
        }
    }

    wp_send_json_success([
        'message' => 'User saved successfully.',
        'post_id' => $post_id,
    ]);
});

// AJAX: Add college to global list
add_action('wp_ajax_gs_add_college', function () {
    check_ajax_referer('gs_user_meta_nonce', 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized');

    $college = sanitize_text_field($_POST['college'] ?? '');
    if (empty($college)) wp_send_json_error('College name is required.');

    $colleges = get_option('gs_colleges', []);
    if (!in_array($college, $colleges, true)) {
        $colleges[] = $college;
        update_option('gs_colleges', $colleges);
    }

    wp_send_json_success(['college' => $college]);
});

// AJAX: Delete college from global list
add_action('wp_ajax_gs_delete_college', function () {
    check_ajax_referer('gs_user_meta_nonce', 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized');

    $college = sanitize_text_field($_POST['college'] ?? '');
    if (empty($college)) wp_send_json_error('College name is required.');

    $colleges = get_option('gs_colleges', []);
    $key = array_search($college, $colleges, true);
    if ($key !== false) {
        unset($colleges[$key]);
        update_option('gs_colleges', array_values($colleges));
        wp_send_json_success(['deleted' => $college]);
    }

    wp_send_json_error('College not found.');
});
