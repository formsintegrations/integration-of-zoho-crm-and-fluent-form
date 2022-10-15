<?php
namespace BitCode\BITFFZC\Admin;

use BitCode\BITFFZC\Core\Util\IpTool;
use BitCode\BITFFZC\Core\Util\DateTimeHelper;
use BitCode\BITFFZC\Admin\Gclid\Handler as GclidHandler;
/**
 * The admin menu and page handler class
 */

class Admin_Bar
{
    public function register()
    {
        add_action('in_admin_header', [$this, 'RemoveAdminNotices']);
        add_action('admin_menu', array( $this, 'AdminMenu' ), 9, 0);
        add_action('admin_enqueue_scripts', array( $this, 'AdminAssets' ));
    }


    /**
     * Register the admin menu
     *
     * @return void
     */
    public function AdminMenu()
    {
        global $submenu;
        $capability = apply_filters('bitffzc_form_access_capability', 'manage_options');
        add_menu_page(__('Zoho CRM integration for Fluent Form', 'bitffzc'), 'Fluent Zoho CRM', $capability, 'bitffzc', array($this, 'RootPage'), 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" width="36.34" height="36.34" data-name="Layer 1"><defs/><circle cx="18.17" cy="18.17" r="16.2" fill="none" stroke="#000" stroke-miterlimit="10" stroke-width="1.5"/><path d="M27.06 9.47v2.62H16.32a7 7 0 0 0-6.91 5.7 6.51 6.51 0 0 0-.11 1V16.5a1.46 1.46 0 0 1 0-.3 7 7 0 0 1 1.53-4.11 7.09 7.09 0 0 1 5.49-2.62Z" class="cls-2"/><path d="M12 20.71a4.89 4.89 0 0 0-.15 1.21 4.34 4.34 0 0 0 .26 1.5 4.51 4.51 0 0 0 8.54 0 4.15 4.15 0 0 0 .25-1.46h2.5a7 7 0 1 1-14.07 0 7.19 7.19 0 0 1 .3-2 6.71 6.71 0 0 1 .56-1.32 7.81 7.81 0 0 1 .69-1 7.06 7.06 0 0 1 5.49-2.62h7v2.62h-7.44a3.61 3.61 0 0 0-1.59.34 4.65 4.65 0 0 0-1.55 1.24 4.36 4.36 0 0 0-.79 1.39.38.38 0 0 1 0 .1Z" class="cls-2"/></svg>'), 30);
    }
    /**
     * Load the asset libraries
     *
     * @return void
     */
    public function AdminAssets($current_screen)
    {
        if (strpos($current_screen, 'bitffzc') === false) {
            return;
        }
        $parsed_url = parse_url(get_admin_url());
        $site_url = $parsed_url['scheme'] . "://" . $parsed_url['host'];
        $site_url .= empty($parsed_url['port']) ? null : ':' . $parsed_url['port'];
        $base_path_admin =  str_replace($site_url, '', get_admin_url());
        wp_enqueue_script(
            'bitffzc-vendors',
            BITFFZC_ASSET_JS_URI . '/vendors-main.js',
            null,
            BITFFZC_VERSION,
            true
        );
        wp_enqueue_script(
            'bitffzc-runtime',
            BITFFZC_ASSET_JS_URI . '/runtime.js',
            null,
            BITFFZC_VERSION,
            true
        );
        if (wp_script_is('wp-i18n')) {
            $deps = array('bitffzc-vendors', 'bitffzc-runtime', 'wp-i18n');
        } else {
            $deps = array('bitffzc-vendors', 'bitffzc-runtime', );
        }
        wp_enqueue_script(
            'bitffzc-admin-script',
            BITFFZC_ASSET_JS_URI . '/index.js',
            $deps,
            BITFFZC_VERSION,
            true
        );

        wp_enqueue_style(
            'bitffzc-styles',
            BITFFZC_ASSET_URI . '/css/bitffzc.css',
            null,
            BITFFZC_VERSION,
            'screen'
        );

        wp_enqueue_style(
            'bitffzc-components-styles',
            BITFFZC_ASSET_URI . '/css/components.css',
            null,
            BITFFZC_VERSION,
            'screen'
        );
        $ipTool = new IpTool();
        $user_details = $ipTool->getUserDetail();
       
        $gclidHandler = new GclidHandler();
        $gclid_enabled = $gclidHandler->get_enabled_form_lsit();
        $forms = wpFluent()->table('fluentform_forms')->select('id', 'title')->get();
        $all_forms = [];
        foreach ($forms as $form) {
            $all_forms[] = (object)[
                'id' => $form->id,
                'title' => $form->title,
                'gclid' => in_array($form->id, $gclid_enabled)
            ];
        }
        $bitffzc = apply_filters(
            'bitffzc_localized_script',
            array(
                'nonce'     => wp_create_nonce('bitffzc_nonce'),
                'assetsURL' => BITFFZC_ASSET_URI,
                'baseURL'   => $base_path_admin . 'admin.php?page=bitffzc#',
                'ajaxURL'   => admin_url('admin-ajax.php'),
                'allForms'  => is_wp_error($all_forms) ? null : $all_forms,
                'erase_all'  => get_option('bitffzc_erase_all'),
                'dateFormat'  => get_option('date_format'),
                'timeFormat'  => get_option('time_format'),
                'timeZone'  => DateTimeHelper::wp_timezone_string(),
                'redirect' => get_rest_url() . 'bitffzc/redirect',
            )
        );
        if (get_locale() !== 'en_US' && file_exists(BITFFZC_PLUGIN_DIR_PATH . '/languages/generatedString.php')) {
            include_once BITFFZC_PLUGIN_DIR_PATH . '/languages/generatedString.php';
            $bitffzc['translations'] = $bitffzc_i18n_strings;
        }
        wp_localize_script('bitffzc-admin-script', 'bitffzc', $bitffzc);

    }

    /**
     * apps-root id provider
     * @return void
     */
    public function RootPage()
    {
        require_once BITFFZC_PLUGIN_DIR_PATH . '/views/view-root.php';
    }

    public function RemoveAdminNotices()
    {
        global $plugin_page;
        if (strpos($plugin_page, 'bitffzc') === false) {
            return;
        }
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}