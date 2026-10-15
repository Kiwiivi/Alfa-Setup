<?php
function alfa_install_woocommerce() {

    alfa_install_plugin(
        'woocommerce',
        'woocommerce/woocommerce.php'
    );

    alfa_configure_woocommerce();
    alfa_configure_woocommerce_catalog();
    echo '<div class="notice notice-success is-dismissible">
            <p>WooCommerce installed.</p>
          </div>';
}

function alfa_configure_woocommerce() {

    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        return;
    }

    // ------------------------
    // General
    // ------------------------

    // Store location
    update_option('woocommerce_default_country', 'BG:BG-22');

    // Currency
    update_option('woocommerce_currency', 'EUR');

    // Currency position
    update_option('woocommerce_currency_pos', 'right_space');

    // ------------------------
    // Inventory
    // ------------------------

    // Hold stock (minutes)
    update_option('woocommerce_hold_stock_minutes', '0');

    // ------------------------
    // Site visibility
    // ------------------------

    // Live store
    update_option('woocommerce_coming_soon', 'no');

    echo '<div class="notice notice-success is-dismissible">
            <p>WooCommerce configured.</p>
          </div>';
}
function alfa_configure_woocommerce_catalog() {

    // Проверяваме дали WooCommerce е активен.
    if ( ! class_exists( 'WooCommerce' ) ) {
        return false;
    }

    // Shop page display → Show Subcategories
    update_option(
        'woocommerce_shop_page_display',
        'subcategories'
    );

    // Category display → Show Subcategories
    update_option(
        'woocommerce_category_archive_display',
        'subcategories'
    );

    return true;
}