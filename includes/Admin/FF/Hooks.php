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
        if (Request::Check('frontend')) {
            add_action('wpcf7_submit', [Handler::class, 'handle_wpcf7_submit'], 10, 2);
            add_filter('wpcf7_form_hidden_fields', [Handler::class, 'filter_wpcf7_form_hidden_fields'], 10, 1 ); 
        }
    }
} 