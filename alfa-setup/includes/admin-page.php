<?php
//Add menu under Tools
add_action('admin_menu', 'alfa_admin_menu');

function alfa_admin_menu() {
    add_management_page(
        'Alfa Setup',      // Page title
        'Alfa Setup',      // Menu title
        'manage_options',  // Capability
        'alfa-setup',      // Menu slug
        'alfa_setup_page'  // Callback function
    );
}
//Add admin page
function alfa_setup_page() {

        if (!current_user_can('manage_options')) {
        return;
    }

        if (
        isset($_POST['alfa_setup_run']) &&
        check_admin_referer('alfa_setup_run')
    ) {
        alfa_setup_run();
        
            echo '<div class="notice notice-success is-dismissible"><p>Setup completed successfully.</p></div>';
    }
    ?>
    <div class="alfa-header">
        <h1>AlfaBest Setup</h1>
        <p>After these steps don't forget to: 
            * Update Divi Layout & Remove search icon
            * Actiavte Wordfence
            * Configure Cyr-To-Lat
            * In case of Woocommerce, update Product catalog settings
        </p>
    </div>
    <form method="post">
        <?php wp_nonce_field('alfa_setup_run'); ?>

        <div class="alfa-wrap">
            <div class="alfa-input-wrap">
                <h2>Site Information</h2>

                <p>
                    <label for="site_title"><strong>Website Title</strong></label><br>
                    <input
                        type="text"
                        id="site_title"
                        name="site_title"
                        class="regular-text">
                </p>

                <p>
                    <label for="domain"><strong>Website Link</strong></label><br>
                    <input
                        type="text"
                        id="domain"
                        name="domain"
                        class="regular-text">
                </p>
                <p>
                    <label for="theme_folder">
                        <strong>Child Theme Folder Name</strong>
                    </label><br>

                    <input
                        type="text"
                        id="theme_folder"
                        name="theme_folder"
                        class="regular-text"
                        placeholder="alfabest">
                </p>
                <label>
                    <input type="checkbox" name="shop_theme" value="1">
                    Use Shop Child Theme
                </label><br>
                    <input
                        type="submit"
                        name="alfa_setup_run"
                        class="button button-primary"
                        value="Run Setup">
                
            </div>
            <div class="alfa-tasks-wrap">
                <h2>Tasks</h2>
                <p>
                    <a href="#" id="alfa-check-all">Check all</a> |
                    <a href="#" id="alfa-uncheck-all">Uncheck all</a>
                </p>
                
                <label>
                    <input type="checkbox" name="tasks[]" value="wordpress" checked>
                    Configure WordPress
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="homepage" checked>
                    Configure Homepage
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="defaults" checked>
                    Delete Defaults
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="plugins" checked>
                    Install Standard Plugins
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="woocommerce">
                    Install WooCommerce
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="parent_theme">
                    Install Parent Theme
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="child_theme">
                    Install Child Theme
                </label><br>
                <label>
                    <input type="checkbox" name="tasks[]" value="delete_default_themes">
                    Delete Twenty Twenty Themes
                </label><br>
               <!--<label>
                    <input type="checkbox" name="tasks[]" value="gdpr">
                    Install & Configure GDPR
                </label><br>

                <label>
                    <input type="checkbox" name="tasks[]" value="seo">
                    Install & Configure SEO
                </label>-->
            </div>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const taskCheckboxes = document.querySelectorAll(
        '.alfa-tasks-wrap input[type="checkbox"]'
        );
    
        document.getElementById('alfa-check-all').addEventListener('click', function (e) {
        e.preventDefault();

        taskCheckboxes.forEach(function (checkbox) {
            checkbox.checked = true;
        });
        });
    
        document.getElementById('alfa-uncheck-all').addEventListener('click', function (e) {
        e.preventDefault();

        taskCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });
        });
    
    });
    </script>
    <?php

}