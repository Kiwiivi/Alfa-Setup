<?php
function alfa_install_gdpr() {

    alfa_install_plugin(
        'gdpr-cookie-compliance',
        'gdpr-cookie-compliance/moove-gdpr.php'
    );

    alfa_configure_gdpr();

    echo '<div class="notice notice-success is-dismissible">
            <p>GDPR installed and configured.</p>
          </div>';
}
function alfa_configure_gdpr() {

    if (!is_plugin_active('gdpr-cookie-compliance/moove-gdpr.php')) {
        return;
    }

    $settings = get_option('moove_gdpr_plugin_settings', []);

    $settings = array_merge($settings, [

        'moove_gdpr_info_bar_content' => '
Ние използваме „бисквитки“, за да Ви осигурим по-добро съдържание и потребителско преживяване. Вашите предпочитания можете да отбележите {setting}тук{/setting}.


Запознайте се с нашата Политика за бисквитки.


',

        'gdpr_bs_buttons_order' => '["accept","settings","reject","close"]',

        'moove_gdpr_infobar_accept_button_label' => 'Приемам всички',

        'moove_gdpr_infobar_reject_button_label' => 'Отказвам всички',

        'gdpr_gs_buttons_order' => '{"1":"enable","2":"save","3":"reject","4":"close"}',

        'moove_gdpr_modal_allow_button_label' => 'Приемам всички',

        'moove_gdpr_modal_reject_button_label' => 'Отказвам всички',

        'moove_gdpr_modal_enabled_checkbox_label' => 'Включено',

        'moove_gdpr_modal_disabled_checkbox_label' => 'Изключено',

        'moove_gdpr_modal_powered_by_label' => 'Съгласно',

        'moove_gdpr_privacy_overview_tab_title' => 'Какво представляват бисквитките',

        'moove_gdpr_privacy_overview_tab_content' => 'Бисквитките са малки текстови файлове, които се запазват на Вашия компютър или мобилно устройство, когато посещавате нашия уебсайт. Отделните секции по-долу ще Ви дадат повече информация за различните категории бисквитки, които нашият уеб сайт използва, както и възможност за промяна на настройките в тази връзка. Моля да имате предвид, че ограничаването и спирането на бисквитките, може да доведе до спиране на функционалности, некоректна работа и ограничаване на потребителското Ви преживяване с нашия уебсайт.',

        'moove_gdpr_cookie_policy_enable' => '1',

        'moove_gdpr_cookie_policy_tab_nav_label' => 'Политика за бисквитките',

        'moove_gdpr_cookies_policy_tab_content' => '
Запознайте се с нашата политика за бисквитки.


',

        'moove_gdpr_strictly_necessary_cookies_functionality' => '2',

        'moove_gdpr_strictly_ccat_enable' => '1',

        'moove_gdpr_strictly_necessary_cookies_tab_title' => 'Строго необходими бисквитки и бисквитки за сигурност',

        'moove_gdpr_strict_necessary_cookies_tab_content' => 'Тези бисквитки са необходими за функционирането на уебсайта ни и не могат да бъдат изключени от Вас.
Използваме бисквитки от гледна точка на сигурност, чиято цел е да предотвратяват измамническо използване на идентификационни данни за вход, както и за защита информацията от неупълномощени страни.
',

        'moove_gdpr_third_party_cookies_enable_first_visit' => '0',

        'moove_gdpr_third_party_cookies_enable' => '1',

        'moove_gdpr_performance_cookies_tab_title' => 'Функционални и анализиращи бисквитки',

        'moove_gdpr_performance_cookies_tab_content' => 'Използваме бисквитки, които позволяват на уебсайта да запаметява Вашите действия и предпочитания за определен период от време, за да не се налага да ги въвеждате всеки път, когато посещавате сайта или преминавате от една страница към друга.
Използваме Анализиращи бисквитки, които ни помагат да подобрим работа и ефективността на интернет страницата си.
',

        'moove_gdpr_advanced_cookies_enable_first_visit' => '0',

        'moove_gdpr_advanced_cookies_enable' => '1',

        'moove_gdpr_advanced_cookies_tab_title' => 'Рекламни и бисквитки на трети страни',

        'moove_gdpr_advanced_cookies_tab_content' => 'Използваме бисквитки, които на база Вашето поведение в нашия уебсайт може да Ви показват реклами, които предполагаме, че отговарят на Вашите предпочитания и интереси.


Използваме бисквитки на трети страни, които позволяват харесване или споделяне на съдържание в социалните мрежи, бисквитки от Google Analytics, които помагат за проследяване трафика на уебсайта и други бисквитки свързани с външни системи и сайтове, интегрирани към уеб сайта. Тези бисквитки могат да бъдат използвани от тези трети страни.
',

    ]);

    update_option('moove_gdpr_plugin_settings', $settings);

    echo '<div class="notice notice-success is-dismissible">
            <p>GDPR configured.</p>
          </div>';
}