<?php
//Link Tasks
function alfa_get_tasks() {

    return [

        'wordpress' => [
            'label' => 'Configure WordPress',
            'callback' => 'alfa_configure_wordpress',
            'default' => true,
        ],

        'homepage' => [
            'label' => 'Configure Homepage',
            'callback' => 'alfa_configure_homepage',
            'default' => true,
        ],

        'defaults' => [
            'label' => 'Delete Default Content',
            'callback' => 'alfa_configure_delete_defaults',
            'default' => true,
        ],
        'plugins' => [
            'label' => 'Install Default plugins',
            'callback' => 'alfa_install_standard_plugins',
            'default' => true,
        ],
        'woocommerce' => [
            'label' => 'Install Woocommerce',
            'callback' => 'alfa_install_woocommerce',
            'default' => true,
        ],
        'parent_theme' => [
            'label' => 'Install & configure Divi',
            'callback' => 'alfa_install_parent_theme',
            'default' => true,
        ],
        'child_theme' => [
            'label' => 'Install & Configure Child Theme',
            'callback' => 'alfa_install_child_theme',
            'default' => true,
        ],
        'delete_default_themes' => [
            'label' => 'Delete Twenty Twenty Themes',
            'callback' => 'alfa_delete_default_themes',
            'default' => true,
        ],
        'gdpr' => [
            'label' => 'Install & Configure GDPR plugin',
            'callback' => 'alfa_install_gdpr',
            'default' => true,
        ],
        'seo' => [
            'label' => 'Install & Configure SEO plugin',
            'callback' => 'alfa_install_seo',
            'default' => true,
        ]
    ];
}
//Run tasks
function alfa_setup_run() {    
    //link config data
    $config = [

        'title' => sanitize_text_field($_POST['site_title'] ?? ''),
        'link' => esc_url_raw($_POST['domain'] ?? ''),
        'theme_folder' => sanitize_title($_POST['theme_folder'] ?? ''),
        'shop_theme'   => !empty($_POST['shop_theme']),

    ];
    // Execute selected tasks
    $tasks = alfa_get_tasks();
    
    foreach ($_POST['tasks'] ?? [] as $task) {
    
        if (isset($tasks[$task])) {
    
            call_user_func(
                $tasks[$task]['callback'],
                $config
            );
    
        }
    
    }

}
