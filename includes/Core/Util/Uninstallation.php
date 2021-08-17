<?php
namespace BitCode\BITFFZC\Core\Util;

/**
 * Class handling plugin uninstallation.
 *
 * @since 1.0.0
 * @access private
 * @ignore
 */
final class Uninstallation
{
    /**
     * Registers functionality through WordPress hooks.
     *
     * @since 1.0.0-alpha
     */
    public function register()
    {
        add_action('bitffzc_uninstall', array($this, 'uninstall'));
    }

    public function uninstall()
    {
        if (get_option('bitffzc_erase_all')) {
            global $wpdb;
            $tableArray = [
             $wpdb->prefix . "bitffzc_zoho_crm_log_details",
             $wpdb->prefix . "bitffzc_integration",
             $wpdb->prefix . "bitffzc_gclid",
            ];
            foreach ($tableArray as $tablename) {
                $wpdb->query("DROP TABLE IF EXISTS $tablename");
            }
            $columns = ["bitffzc_db_version", "bitffzc_installed", "bitffzc_version", "bitffzc_erase_all"];
            foreach ($columns as $column) {
                $wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE option_name='$column'");
            }
        }
    }
}
