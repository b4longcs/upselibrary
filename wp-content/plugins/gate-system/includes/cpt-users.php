<?php

add_action('admin_init', function () {
    remove_post_type_support('gs_user', 'editor'); 
    remove_post_type_support('gs_user', 'title'); 
});


add_action('edit_form_after_title', function ($post) {
    if ($post->post_type === 'gs_user') {
        $name = get_post_meta($post->ID, 'name', true);
        echo '<div style="padding:10px 0">
                <label><strong>Name:</strong></label><br>
                <input type="text" name="gs_name" value="' . esc_attr($name) . '" style="width:100%; font-size:18px;">
              </div>';
    }
});

add_action('admin_head', function () {
    $screen = get_current_screen();
    if ($screen->post_type === 'gs_user') {
        echo '<style>#submitdiv { display: none !important; }</style>';
    }
});

add_action('admin_enqueue_scripts', function ($hook) {
    global $post;

    if (($hook === 'post.php' || $hook === 'post-new.php') && get_post_type() === 'gs_user') {
        wp_enqueue_script(
            'gs-user-meta-js',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/gs-user-meta.js', // ✅ now correct
            ['jquery'],
            null,
            true
        );

        wp_localize_script('gs-user-meta-js', 'gs_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('gs_user_meta_nonce'),
        ]);
    }
});


add_action('admin_menu', function () {

    remove_meta_box('commentstatusdiv', 'gs_user', 'normal');
    remove_meta_box('commentsdiv', 'gs_user', 'normal');

    // Hide "Add New" submenu
    global $submenu;
    if (isset($submenu['edit.php?post_type=gs_user'])) {
        foreach ($submenu['edit.php?post_type=gs_user'] as $index => $item) {
            if (in_array('post-new.php?post_type=gs_user', $item)) {
                unset($submenu['edit.php?post_type=gs_user'][$index]);
            }
        }
    }
});


add_action('init', function () {
    register_post_type('gs_user', [
        'labels' => [
            'name' => 'Gate System',
            'singular_name' => 'User',
            'add_new_item' => 'Add New User',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-id-alt',
        'supports' => [],
        'show_in_rest' => false,
    ]);
});

// Add meta boxes for user data
add_action('add_meta_boxes', function () {
    add_meta_box('gs_user_meta', 'User Info', function ($post) {
        $fields = ['college', 'course', 'barcode'];
        foreach ($fields as $field) {
            $val = get_post_meta($post->ID, $field, true);
            echo "<p><label>" . ucfirst($field) . ":<br>
                  <input type='text' class='gs-meta-field' data-key='{$field}' value='" . esc_attr($val) . "' style='width:100%'></label></p>";
        }

        // Type dropdown
        $type_val = get_post_meta($post->ID, 'type', true);
        $types = ['Student', 'Staff', 'Faculty', 'Visitor'];
        echo "<p><label>Type:<br><select class='gs-meta-field' data-key='type' style='width:100%'>";
        foreach ($types as $type) {
            $selected = $type === $type_val ? 'selected' : '';
            echo "<option value='{$type}' {$selected}>{$type}</option>";
        }
        echo "</select></label></p>";

        $post_id = get_the_ID();
        echo '<button type="button" class="button button-primary" id="gs-save-meta" data-id="' . esc_attr($post->ID) . '">Save Changes</button>';
        echo '<div id="gs-save-status" style="margin-top:10px;"></div>';
    }, 'gs_user');
});

// Save metadata when post is saved
add_action('save_post_gs_user', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['barcode', 'name', 'college', 'course', 'type'];
    foreach ($fields as $field) {
        $key = 'gs_' . $field;
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$key]));
        }
    }

    // Update title = name (only once, no recursion)
    if (isset($_POST['gs_name'])) {
        remove_action('save_post_gs_user', __FUNCTION__); // prevent recursion
        wp_update_post([
            'ID' => $post_id,
            'post_title' => sanitize_text_field($_POST['gs_name']),
            'post_name'  => sanitize_title($_POST['gs_name']), // Optional slug update
        ]);
        add_action('save_post_gs_user', __FUNCTION__);
    }
});

add_filter('manage_gs_user_posts_columns', function ($columns) {
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

add_action('manage_gs_user_posts_custom_column', function ($column, $post_id) {
    switch ($column) {
        case 'barcode':
        case 'name':
        case 'college':
        case 'course':
        case 'type':
            echo esc_html(get_post_meta($post_id, $column, true));
            break;
    }
}, 10, 2);

// Secure AJAX handler to save user meta
add_action('wp_ajax_gs_save_user_meta', function () {
    check_ajax_referer('gs_user_meta_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $post_id = intval($_POST['post_id']);
    $fields  = json_decode(stripslashes($_POST['fields']), true);

    if (!$post_id || empty($fields) || !is_array($fields)) {
        wp_send_json_error('Invalid data');
    }

    $allowed_fields = ['name', 'college', 'course', 'barcode', 'type'];

    foreach ($allowed_fields as $field) {
        if (isset($fields[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($fields[$field]));
        }
    }

    // Optional: Update post title to match 'name'
    if (!empty($fields['name'])) {
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => sanitize_text_field($fields['name']),
            'post_name'  => sanitize_title($fields['name']),
        ]);
    }

    wp_send_json_success('User metadata saved.');
});
