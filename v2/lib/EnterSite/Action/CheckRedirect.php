<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Controller;

class CheckRedirect {
    use ConfigTrait, LoggerTrait, RouterTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * Временное решение для редиректа на основной домен
     * @param Http\Request $request
     * @return null
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $router = $this->getRouter();

        $route = null;
        try {
            $route = $router->getRouteByPath($request->getPathInfo(), $request->getMethod(), $request->query->all());
        } catch (\Exception $e) {}

        $hasRedirect = false
            //|| ($route instanceof Routing\Index)
            //|| ($route instanceof Routing\Search\Index)
            //|| ($route instanceof Routing\User\Login)
            || ($route instanceof Routing\User\Index)
            || ($route instanceof Routing\ShopCard\Get)
            || ($route instanceof Routing\Shop\Index)
        ;

        if (!$hasRedirect) {
            return null;
        }

        // FIXME
        //$url = str_replace('m.', '', $request->getSchemeAndHttpHost() . $request->getRequestUri());
        $url = strtr($request->getSchemeAndHttpHost(), [
            'm.'    => '',
            ':8080' => '', //FIXME: костыль для nginx-а
        ]) . $request->getRequestUri();

        return (new Controller\Redirect())->execute($url, 302);
    }
}