<?php
function alfa_install_standard_plugins() {

    $plugins = [

        'loco-translate/loco.php' => 'loco-translate',

        'cyr2lat/cyr-to-lat.php' => 'cyr2lat',

        'classic-widgets/classic-widgets.php' => 'classic-widgets',

        'wps-hide-login/wps-hide-login.php' => 'wps-hide-login',

    ];

    foreach ($plugins as $file => $slug) {

        alfa_install_plugin($slug, $file);

    }

    alfa_configure_standard_plugins();

    echo '<div class="notice notice-success is-dismissible"><p>Plugins Installed!</p></div>';
}
//Helper function for alfa_install_standard_plugins
function alfa_install_plugin($slug, $plugin_file) {

    // Load required WordPress files
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';

    // Already active
    if (is_plugin_active($plugin_file)) {
        return;
    }

    // Already installed, just activate it
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
        activate_plugin($plugin_file);
        return;
    }

    // Get plugin information
    $api = plugins_api('plugin_information', [
        'slug' => $slug,
        'fields' => [
            'sections' => false,
        ],
    ]);

    if (is_wp_error($api)) {
        return;
    }

    // Install
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
    $installed = $upgrader->install($api->download_link);

    if ($installed && !is_wp_error($installed)) {
        activate_plugin($plugin_file);
    }
}

function alfa_configure_standard_plugins() {

    alfa_configure_wps_hide_login();
    alfa_configure_classic_widgets();

}

//Configure WPS Hide Login 
function alfa_configure_wps_hide_login() {

    if (!is_plugin_active('wps-hide-login/wps-hide-login.php')) {
        return;
    }

    update_option('whl_page', 'wa-ccess');

    echo '<div class="notice notice-success is-dismissible">
            <p>WPS Hide Login configured.</p>
          </div>';
}

//Configure classic widgets (delete default blocks)
function alfa_configure_classic_widgets() {

    if (!is_plugin_active('classic-widgets/classic-widgets.php')) {
        return;
    }

    $sidebars = get_option('sidebars_widgets');

    foreach ($sidebars as $sidebar => $widgets) {

        if ($sidebar === 'wp_inactive_widgets' || $sidebar === 'array_version') {
            continue;
        }

        $sidebars[$sidebar] = [];
    }

    update_option('sidebars_widgets', $sidebars);

    echo '<div class="notice notice-success is-dismissible">
            <p>Default widgets removed.</p>
          </div>';
}
