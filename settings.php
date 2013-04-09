<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Logo file setting.
    $name = 'theme_simple/logo';
    $title = get_string('logo','theme_simple');
    $description = get_string('logodesc', 'theme_simple');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $settings->add($setting);

    // Footnote setting.
    $name = 'theme_simple/footnote';
    $title = get_string('footnote','theme_simple');
    $description = get_string('footnotedesc', 'theme_simple');
    $default = get_string('footnotetxt', 'theme_simple');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);


    // Custom CSS file.
    $name = 'theme_simple/customcss';
    $title = get_string('customcss','theme_simple');
    $description = get_string('customcssdesc', 'theme_simple');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

}