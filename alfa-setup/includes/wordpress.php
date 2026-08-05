<?php
//Configure WP settings
function alfa_configure_wordpress() {
    update_option('timezone_string', 'Europe/Sofia');
    update_option('blog_public', 0);
    update_option('permalink_structure', '/%postname%/');
    flush_rewrite_rules();
    echo '<div class="notice notice-success is-dismissible"><p>WordPress default settings configured!</p></div>';
}
//Rename & Set homepage
function alfa_configure_homepage() {

    // Try to find an existing homepage first
    $page = alfa_find_page_by_slug([
        'начало',
        'nachalo',
        'sample-page',
    ]);
    //If not, create one
    if (!$page) {

        $page_id = wp_insert_post([
            'post_title'  => 'Начало',
            'post_status' => 'publish',
            'post_type'   => 'page',
            'post_name'   => 'начало',
        ]);
    //If found, update name & slug
    } else {

        wp_update_post([
            'ID'         => $page->ID,
            'post_title' => 'Начало',
            'post_name'  => 'начало',
        ]);

        $page_id = $page->ID;
    }
    //Set as homepage
    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_id);
    echo '<div class="notice notice-success is-dismissible"><p>Homepage updated!</p></div>';
}
//Helper function for alfa_configure_homepage
function alfa_find_page_by_slug(array $slugs) {

    foreach ($slugs as $slug) {

        $page = get_page_by_path($slug);

        if ($page) {
            return $page;
        }

    }

    return null;
}
//Delete default posts&pages
function alfa_configure_delete_defaults() {

    $page = get_page_by_path('privacy-policy');
    if ($page) {
        wp_delete_post($page->ID, true);
    }

    $post = get_page_by_path('hello-world', OBJECT, 'post');
    if ($post) {
        wp_delete_post($post->ID, true);
    }
    alfa_remove_default_plugins();
    echo '<div class="notice notice-success is-dismissible"><p>Defaults Deleted!</p></div>';
}
//Delete Default plugins 
function alfa_remove_default_plugins() {

    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';

    $plugins = get_plugins();

    $plugins_to_delete = [
        'AI Agent by SiteGround',
        'Security Optimizer',
        'Hello Dolly',
        'Akismet Anti-spam: Spam Protection',
    ];

    foreach ($plugins as $plugin_file => $plugin_data) {

        if (in_array($plugin_data['Name'], $plugins_to_delete, true)) {

            if (is_plugin_active($plugin_file)) {
                deactivate_plugins($plugin_file);
            }

            delete_plugins([$plugin_file]);
        }
    }

    // Speed Optimizer - deactivate only
    foreach ($plugins as $plugin_file => $plugin_data) {

        if ($plugin_data['Name'] === 'Speed Optimizer') {

            if (is_plugin_active($plugin_file)) {
                deactivate_plugins($plugin_file);
            }

            break;
        }
    }

    echo '<div class="notice notice-success is-dismissible">
            <p>Default plugins processed.</p>
          </div>';
}