<?php
/**
 * Plugin Name: Alfa Setup
 * Description: Automates the initial setup of a WordPress website.
 * Version: 0.1.0
 * Author: AlfaBest 
 */

if (!defined('ABSPATH')) {
    exit;
}
//Add styles
function alfa_admin_styles($hook) {

    if ($hook !== 'tools_page_alfa-setup') {
        return;
    }

    wp_enqueue_style(
        'alfa-setup-style',
        ALFA_SETUP_URL . 'assets/alfa-style.css',
        [],
        '1.0'
    );
}

add_action('admin_enqueue_scripts', 'alfa_admin_styles');

define('ALFA_SETUP_PATH', plugin_dir_path(__FILE__));
define('ALFA_SETUP_URL', plugin_dir_url(__FILE__));


require_once ALFA_SETUP_PATH . 'includes/tasks.php';
require_once ALFA_SETUP_PATH . 'includes/admin-page.php';
require_once ALFA_SETUP_PATH . 'includes/wordpress.php';
require_once ALFA_SETUP_PATH . 'includes/themes.php';
require_once ALFA_SETUP_PATH . 'includes/plugins/plugins.php';
require_once ALFA_SETUP_PATH . 'includes/plugins/woocommerce.php';
require_once ALFA_SETUP_PATH . 'includes/plugins/gdpr.php';