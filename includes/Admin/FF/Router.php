<?php
namespace BitCode\BITFFZC\Admin\FF;

use BitCode\BITFFZC\Core\Util\Route;

final class Router{
    public function __construct()
    {
        //
    }
    
    
    public static function registerAjax()
    {
        Route::post('ff/get/form', [Handler::class, 'get_a_form']);
    }
} 