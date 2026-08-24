<?php
/**Ако трябва нов експорт/обновяване на настройките да се използва следната команда (csv format): 
 * 
SELECT *
FROM bmv_gdpr_cc_options
WHERE option_key NOT IN (
    'moove_gdpr_nonce',
    '_wp_http_referer',
    'moove_gdpr_company_logo',
    'moove_gdpr_company_logo_id'
);
 */
function alfa_install_gdpr() {

    alfa_install_plugin(
        'gdpr-cookie-compliance',
        'gdpr-cookie-compliance/moove-gdpr.php'
    );


    // създава wp_gdpr_cc_options
    alfa_initialize_gdpr_database();


    // създава default записите на Moove
    alfa_initialize_gdpr_defaults_after_db();


    // качва CSV настройките
    alfa_configure_gdpr();


    echo '<div class="notice notice-success is-dismissible">
            <p>GDPR installed and configured.</p>
          </div>';
}
function alfa_configure_gdpr() {

    global $wpdb;

    if ( ! is_plugin_active('gdpr-cookie-compliance/moove-gdpr.php') ) {
        return;
    }

    $table = $wpdb->prefix . 'gdpr_cc_options';

    // ако таблицата още не е създадена
    if ( $wpdb->get_var(
        $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table
        )
    ) !== $table ) {
        return;
    }

    if ( function_exists( 'pll_languages_list' ) ) {
        // Polylang is active → use multilang configuration
        $file = ALFA_SETUP_PATH . 'setup/gdpr-settings-multilang.csv';
    } else {
        // No Polylang → use normal configuration
        $file = ALFA_SETUP_PATH . 'setup/gdpr-settings.csv';
    }
    
    if ( ! file_exists( $file ) ) {
        return;
    }
    if ( ( $handle = fopen( $file, 'r' ) ) === false ) {
        return;
    }

    // прескачаме header-а
    fgetcsv( $handle );

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {

        $wpdb->replace(
            $table,
            [
                'option_key'   => $row[1],
                'option_value' => $row[2],
                'site_id'      => 1,
                'extras'       => $row[4] !== '' ? $row[4] : null,
            ],
            [
                '%s',
                '%s',
                '%d',
                '%s',
            ]
        );
    }

    fclose( $handle );

}
function alfa_initialize_gdpr_database() {

    if ( ! class_exists( 'Moove_GDPR_DB_Controller' ) ) {

        require_once WP_PLUGIN_DIR .
            '/gdpr-cookie-compliance/controllers/class-moove-gdpr-db-controller.php';
    }

    new Moove_GDPR_DB_Controller();
}
function alfa_initialize_gdpr_defaults_after_db() {

    if ( ! class_exists( 'Moove_GDPR_Content' ) ) {

        require_once WP_PLUGIN_DIR .
            '/gdpr-cookie-compliance/class-moove-gdpr-content.php';
    }


    if ( ! function_exists( 'gdpr_update_field' ) ) {

        require_once WP_PLUGIN_DIR .
            '/gdpr-cookie-compliance/gdpr-functions.php';
    }


    $gdpr_default_content = new Moove_GDPR_Content();

    $option_name = $gdpr_default_content->moove_gdpr_get_option_name();

    $gdpr_options = get_option( $option_name );


    if ( ! is_array( $gdpr_options ) ) {
        return;
    }


    foreach ( $gdpr_options as $key => $value ) {

        gdpr_update_field(
            $key,
            $value
        );
    }
}
