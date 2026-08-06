<?php
function alfa_install_seo() {
    alfa_install_plugin(
        'wordpress-seo',
        'wordpress-seo/wp-seo.php'
    );

    alfa_configure_seo();   

    echo '<div class="notice notice-success is-dismissible">
            <p>YoastSEO installed.</p>
        </div>';
}

function alfa_configure_seo() {
    if ( ! is_plugin_active('wordpress-seo/wp-seo.php') ) {
            return;
        }
    $settings = get_option('wpseo', []);

    $settings = array_merge($settings, [
        'remove_shortlinks'           => 1,
        'remove_rest_api_links'       => 1,
        'remove_rsd_wlw_links'        => 1,
        'remove_oembed_links'         => 1,
        'remove_generator'            => 1,
        'remove_pingback_header'      => 1,
        'remove_powered_by_header'    => 1,

        'remove_feed_global_comments' => 1,
        'remove_feed_authors'         => 1,
        'remove_atom_rdf_feeds'       => 1,

        'remove_emoji_scripts'        => 1,

        'deny_wp_json_crawling'      => 1,
    ]);

    update_option('wpseo', $settings);

}
