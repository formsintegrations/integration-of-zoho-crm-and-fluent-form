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
            add_action('fluentform_submission_inserted', [Handler::class, 'handle_ff_submit'], 9, 3);
        }
    }
} 