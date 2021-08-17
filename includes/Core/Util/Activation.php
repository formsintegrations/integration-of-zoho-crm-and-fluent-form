<?php

namespace BitCode\BITFFZC\Core\Util;

use BitCode\BITFFZC\Core\Database\DB;

/**
 * Class handling plugin activation.
 *
 * @since 1.0.0
 */
final class Activation
{
    public function activate()
    {
        add_action('bitffzc_activation', array($this, 'install'));
    }

    public function install()
    {
        $installed = get_option('bitffzc_installed');
        if ($installed) {
            $oldversion = get_option('bitffzc_version');
        }
        if (!get_option('bitffzc_erase_all')) {
            update_option('bitffzc_erase_all', false);
        }
    
        if (!$installed || version_compare($oldversion, BITFFZC_VERSION, '!=')) {
            DB::migrate();
            update_option('bitffzc_installed', time());
        }
        update_option('bitffzc_version', BITFFZC_VERSION);
    }
}
