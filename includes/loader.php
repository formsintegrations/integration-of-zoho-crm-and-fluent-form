<?php
if (!defined('ABSPATH')) {
    exit;
}
$scheme = parse_url(home_url())['scheme'];
define('BITFFZC_PLUGIN_BASENAME', plugin_basename(BITFFZC_PLUGIN_MAIN_FILE));
define('BITFFZC_PLUGIN_DIR_PATH', plugin_dir_path(BITFFZC_PLUGIN_MAIN_FILE));
define('BITFFZC_ROOT_URI', set_url_scheme(plugins_url('', BITFFZC_PLUGIN_MAIN_FILE), $scheme));
define('BITFFZC_ASSET_URI', BITFFZC_ROOT_URI . '/assets');
define('BITFFZC_ASSET_JS_URI', BITFFZC_ROOT_URI . '/assets/js');
// Autoload vendor files.
require_once BITFFZC_PLUGIN_DIR_PATH . 'vendor/autoload.php';
// Initialize the plugin.
BitCode\BITFFZC\Plugin::load(BITFFZC_PLUGIN_MAIN_FILE);

