<?php
//----------------Parent Theme----------------------//
function alfa_install_parent_theme() {

    if (!wp_get_theme('Divi')->exists()) {

        $theme_path = alfa_extract_parent_theme();

        if (is_wp_error($theme_path)) {

            echo '<div class="notice notice-error"><p>' .
                esc_html($theme_path->get_error_message()) .
                '</p></div>';

            return;
        }
    }

    $result = alfa_import_divi_settings();

    if (is_wp_error($result)) {

        echo '<div class="notice notice-error"><p>' .
            esc_html($result->get_error_message()) .
            '</p></div>';

        return;
    }

    switch_theme('Divi');

    echo '<div class="notice notice-success">
            <p>Divi installed and configured.</p>
          </div>';
}
function alfa_extract_parent_theme() {

    if (is_dir(get_theme_root() . '/Divi')) {
        return get_theme_root() . '/Divi';
    }
    
    require_once ABSPATH . 'wp-admin/includes/file.php';

    WP_Filesystem();

    $zip = ALFA_SETUP_PATH . 'setup/Divi-latest.zip';

    $destination = get_theme_root();

    $result = unzip_file($zip, $destination);

    if (is_wp_error($result)) {
        return $result;
    }

    return $destination . '/Divi';

}
//Configure Divi 
function alfa_import_divi_settings() {

    $json_file = ALFA_SETUP_PATH . 'setup/divi-settings.json';

    if (!file_exists($json_file)) {
        return new WP_Error(
            'missing_json',
            'Divi settings JSON not found.'
        );
    }

    $settings = json_decode(
        file_get_contents($json_file),
        true
    );

    if (
        empty($settings['context']) ||
        $settings['context'] !== 'epanel' ||
        empty($settings['data'])
    ) {
        return new WP_Error(
            'invalid_json',
            'Invalid Divi settings export.'
        );
    }

    $current = get_option('et_divi', []);

    update_option(
        'et_divi',
        array_merge($current, $settings['data'])
    );

    return true;
}

//----------------Child Theme----------------------//
function alfa_install_child_theme($config) {


    $theme_path = alfa_install_child_theme_zip(
        ALFA_SETUP_PATH . 'setup/Child-Alfa-template.zip',
        'Child-Alfa-template',
        $config['theme_folder']
    );

    if (is_wp_error($theme_path)) {
        return;
    }

    alfa_replace_theme_placeholders($theme_path, $config);

    switch_theme($config['theme_folder']);

    echo '<div class="notice notice-success is-dismissible"><p>Child theme installed!</p></div>';
}
function alfa_install_child_theme_zip($zip_file, $template_folder, $new_folder) {

    require_once ABSPATH . 'wp-admin/includes/file.php';

    WP_Filesystem();

    $destination = get_theme_root();

    $result = unzip_file($zip_file, $destination);

    if (is_wp_error($result)) {
        return $result;
    }

    $old_path = $destination . '/' . $template_folder;
    $new_path = $destination . '/' . $new_folder;

    if (is_dir($new_path)) {
        return new WP_Error(
            'theme_exists',
            'Theme folder already exists.'
        );
    }

    if (!rename($old_path, $new_path)) {
        return new WP_Error(
            'rename_failed',
            'Unable to rename child theme.'
        );
    }

    return $new_path;

}
//Replace placeholders for child theme
function alfa_replace_placeholders($file, $config) {

    $content = file_get_contents($file);

    $replacements = [

        '{{TITLE}}' => $config['title'],
        '{{LINK}}'  => $config['link'],

    ];

    $content = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $content
    );

    file_put_contents($file, $content);

}
function alfa_replace_theme_placeholders($theme_path, $config) {

    $allowed_extensions = [
        'php',
        'css',
        'js',
        'html',
        'txt',
        'json',
        'xml',
    ];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($theme_path)
    );

    foreach ($iterator as $file) {

        if ($file->isDir()) {
            continue;
        }

        if (!in_array($file->getExtension(), $allowed_extensions, true)) {
            continue;
        }

        alfa_replace_placeholders(
            $file->getPathname(),
            $config
        );
    }
}