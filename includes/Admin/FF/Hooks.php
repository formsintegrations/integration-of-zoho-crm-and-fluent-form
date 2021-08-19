<?php
namespace BitCode\BITFFZC\Admin\FF;

use BitCode\BITFFZC\Core\Util\Request;

final class Hooks{
    public function __construct()
    {
        //
    }
    
    
    public function registerHooks()
    {
        if (Request::Check('frontend') && !class_exists("BitCode\\BITFFZCPRO\\Integration\\Integrations")) {
            add_action('fluentform_submission_inserted', [Handler::class, 'handle_ff_submit'], 10, 3);
            add_filter('fluentform_rendering_form', [Handler::class, 'fluentform_rendering_form'], 10, 1);
        }
    }
} 