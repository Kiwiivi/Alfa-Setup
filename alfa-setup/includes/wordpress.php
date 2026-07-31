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
    echo '<div class="notice notice-success is-dismissible"><p>Default Posts & Pages Deleted!</p></div>';
}