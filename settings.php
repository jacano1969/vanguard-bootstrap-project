<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Logo file setting.
    $name = 'theme_vanguard/logo';
    $title = get_string('logo','theme_vanguard');
    $description = get_string('logodesc', 'theme_vanguard');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $settings->add($setting);

    // Footnote setting.
    $name = 'theme_vanguard/footnote';
    $title = get_string('footnote','theme_vanguard');
    $description = get_string('footnotedesc', 'theme_vanguard');
    $default = get_string('footnotetxt', 'theme_vanguard');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);


    // Custom CSS file.
    $name = 'theme_vanguard/customcss';
    $title = get_string('customcss','theme_vanguard');
    $description = get_string('customcssdesc', 'theme_vanguard');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

}