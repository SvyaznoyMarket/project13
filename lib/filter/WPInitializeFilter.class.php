<?php
class WPInitializeFilter extends sfFilter
{
    public function execute ($filterChain)
    {
        $module = $this->getContext()->getRequest()->getParameter('module');
        $action = $this->getContext()->getRequest()->getParameter('action');

        if(in_array($module, array('wordpress', 'callback')))
        {
            chdir( dirname(__FILE__) . '/..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'sfWordpressPlugin' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'wordpress' );
            require_once('wp-symfony.php');

            SF_WP_Proxy::getInstance()->init();
        }

        $filterChain->execute();
    }
}
