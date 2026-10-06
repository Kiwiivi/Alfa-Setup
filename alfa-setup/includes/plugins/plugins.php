<?php
function alfa_install_standard_plugins() {

    $plugins = [

        'loco-translate/loco.php' => 'loco-translate',

        'cyr2lat/cyr-to-lat.php' => 'cyr2lat',

        'classic-widgets/classic-widgets.php' => 'classic-widgets',

        'wps-hide-login/wps-hide-login.php' => 'wps-hide-login',

        'wordfence/wordfence.php' => 'wordfence',

    ];

    foreach ($plugins as $file => $slug) {

        alfa_install_plugin($slug, $file);

    }

    alfa_configure_standard_plugins();

    echo '<div class="notice notice-success is-dismissible"><p>Plugins Installed /WPS Hide Login, Wordfence, Cyr-To-Lat, Classic Widgets, Loco Translate!</p></div>';
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


//Install Litespeed cache 

function alfa_install_litespeed() {

    $plugin_file = 'litespeed-cache/litespeed-cache.php';
    /*
     * Инсталираме / активираме.
     */
    alfa_install_plugin(
        'litespeed-cache',
        $plugin_file
    );
    /*
     * Проверяваме дали вече е активен.
     */
    if ( ! is_plugin_active($plugin_file) ) {
        return;
    }


    /*
     * Опит за конфигурация.
     */
    $result = alfa_configure_litespeed();

}

add_action(
    'admin_init',
    'alfa_litespeed_process_pending_import',
    20
);

function alfa_litespeed_process_pending_import() {

    if ( ! get_option('alfa_litespeed_import_pending') ) {
        return;
    }

    /*
     * LiteSpeed трябва да е активен.
     */
    if ( ! is_plugin_active('litespeed-cache/litespeed-cache.php') ) {
        return;
    }

    /*
     * Изпълняваме импорта.
     */
    $result = alfa_configure_litespeed();

    /*
     * Само ако функцията е изпълнена успешно,
     * махаме pending флага.
     */
    if ( $result === true ) {

        delete_option('alfa_litespeed_import_pending');

        add_action('admin_notices', function() {

            echo '<div class="notice notice-success is-dismissible">
                    <p><strong>Alfa Setup:</strong> LiteSpeed Cache settings imported successfully.</p>
                  </div>';

        });
    }
}
// Configure LiteSpeed Cache

function alfa_configure_litespeed() {

    /*
     * LiteSpeed трябва да е активен.
     */
    if ( ! is_plugin_active('litespeed-cache/litespeed-cache.php') ) {
        return false;
    }

    /*
     * Път към export файла.
     */
    $file = ALFA_SETUP_PATH . 'setup/litespeed-settings.data';

    if ( ! file_exists($file) ) {
        return false;
    }

    /*
     * Четем файла ред по ред.
     *
     * Формат:
     *
     * ["option", value]
     */
    $lines = file(
        $file,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    if ( ! is_array($lines) || empty($lines) ) {
        return false;
    }

    /*
     * Настройки, които НЕ трябва да се прехвърлят
     * от стария сайт.
     *
     * Те са специфични за конкретния сайт,
     * домейн, сървър или външна услуга.
     */
    $excluded_keys = [

        /*
         * LiteSpeed internal
         */
        '_version',
        'hash',

        /*
         * API / server specific
         */
        'api_key',
        'server_ip',

        /*
         * CDN - специфично за стария сайт
         */
        'cdn-ori',
        'cdn-ori_dir',

        /*
         * Cloudflare credentials / site data
         */
        'cdn-cloudflare_email',
        'cdn-cloudflare_key',
        'cdn-cloudflare_name',
        'cdn-cloudflare_zone',
        'cdn-cloudflare_clear',

        /*
         * Object Cache connection
         *
         * Host/port/user/password/database не трябва
         * да се прехвърлят автоматично от друг сървър.
         */
        'object-host',
        'object-port',
        'object-user',
        'object-pswd',
        'object-db_id',
    ];

    $settings = [];

    /*
     * Parse settings.
     */
    foreach ( $lines as $line ) {

        $line = trim($line);

        if ( $line === '' ) {
            continue;
        }

        $data = json_decode($line, true);

        /*
         * Пропускаме невалиден JSON.
         */
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            continue;
        }

        /*
         * Очакваме точно:
         *
         * [
         *     "key",
         *     value
         * ]
         */
        if (
            ! is_array($data) ||
            count($data) !== 2
        ) {
            continue;
        }

        $key   = $data[0];
        $value = $data[1];

        /*
         * Ключът трябва да е string.
         */
        if ( ! is_string($key) ) {
            continue;
        }

        /*
         * Пропускаме site/server-specific настройките.
         */
        if ( in_array($key, $excluded_keys, true) ) {
            continue;
        }

        /*
         * Добавяме настройката.
         */
        $settings[$key] = $value;
    }

    /*
     * Няма какво да записваме.
     */
    if ( empty($settings) ) {
        return false;
    }

    /*
     * При твоята версия:
     *
     * litespeed_update_confs
     * не е регистриран.
     *
     * litespeed_save_conf
     * е регистриран и приема:
     *
     * array|false $the_matrix
     *
     * LiteSpeed след това предава масива
     * към Conf::update_confs().
     */
    if ( has_action('litespeed_save_conf') ) {

        do_action(
            'litespeed_save_conf',
            $settings
        );

        return true;
    }

    /*
     * Fallback:
     *
     * Ако в друга версия на LiteSpeed
     * е наличен litespeed_update_confs,
     * използваме него.
     */
    if ( has_action('litespeed_update_confs') ) {

        do_action(
            'litespeed_update_confs',
            $settings
        );

        return true;
    }

    /*
     * Нито един от API hooks не е наличен.
     */
    return false;
}

//Install Mail Plugins

function alfa_install_mail_plugins() {

    $plugins = [

        'wp-mail-smtp' => 'wp-mail-smtp/wp_mail_smtp.php',

        'wp-mail-logging' => 'wp-mail-logging/wp-mail-logging.php',

    ];

    foreach ( $plugins as $slug => $plugin_file ) {

        alfa_install_plugin(
            $slug,
            $plugin_file
        );

    }

    echo '<div class="notice notice-success is-dismissible">
            <p>WP Mail SMTP and WP Mail Logging installed and activated.</p>
          </div>';
}


//Install MainWP Child
function alfa_install_mainwp_child() {

    alfa_install_plugin(
        'mainwp-child',
        'mainwp-child/mainwp-child.php'
    );

    echo '<div class="notice notice-success is-dismissible">
            <p>MainWP Child installed and activated.</p>
          </div>';
}