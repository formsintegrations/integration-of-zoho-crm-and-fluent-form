<?php

/**
 * Plugin Name: Integration of Zoho CRM and Fluent Form
 * Plugin URI:  https://www.bitapps.pro/fluent-form-zohocrm
 * Description: Sends Fluent Form entries to Zoho CRM
 * Version:     1.0.1
 * Author:      Bit Apps
 * Author URI:  bitapps.pro
 * Text Domain: bitffzc
 * Requires PHP: 5.6
 * Domain Path: /languages
 * License: GPLv2 or later
 */

/***
 * If try to direct access  plugin folder it will Exit
 **/
if (!defined('ABSPATH')) {
    exit;
}
global $bitffzc_db_version;
$bitffzc_db_version = '1.0';


// Define most essential constants.
define('BITFFZC_VERSION', '1.0.1');
define('BITFFZC_PLUGIN_MAIN_FILE', __FILE__);


require_once plugin_dir_path(__FILE__) . 'includes/loader.php';

function bitffzc_activate_plugin()
{
    if (version_compare(PHP_VERSION, '5.6.0', '<')) {
        wp_die(
            esc_html__('bitffzc requires PHP version 5.6.', 'bitffzc'),
            esc_html__('Error Activating', 'bitffzc')
        );
    }
    do_action('bitffzc_activation');
}

register_activation_hook(__FILE__, 'bitffzc_activate_plugin');

function bitffzc_uninstall_plugin()
{
    do_action('bitffzc_uninstall');
}
register_uninstall_hook(__FILE__, 'bitffzc_uninstall_plugin');
