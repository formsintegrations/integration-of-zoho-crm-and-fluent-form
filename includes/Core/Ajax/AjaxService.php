<?php

namespace BitCode\BITFFZC\Core\Ajax;

use BitCode\BITFFZC\Core\Util\Request;
use BitCode\BITFFZC\Admin\AdminAjax;
use BitCode\BITFFZC\Integration\Integrations;

class AjaxService
{
    public function __construct()
    {
        if (Request::Check('ajax')) {
            $this->loadPublicAjax();
        }
        if (Request::Check('admin')) {
            $this->loadAdminAjax();
            $this->loadIntegrationsAjax();
        }
    }

    /**
     * Helps to register admin side ajax
     * 
     * @return null
     */
    public function loadAdminAjax()
    {
        (new AdminAjax())->register();
    }

    /**
     * Helps to register frontend ajax
     * 
     * @return null
     */
    protected function loadPublicAjax()
    {
        // (new FrontendAjax())->register();
    }

    /**
     * Helps to register integration ajax
     * 
     * @return null
     */
    public function loadIntegrationsAjax()
    {
        (new Integrations())->registerAjax();
    }
}
