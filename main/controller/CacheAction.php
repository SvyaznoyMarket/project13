<?php

namespace Controller;

use EnterApplication\Action;

class CacheAction {
    /**
     * Запрашивает редирект и АБ-тесты
     *
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response|null
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $regionId = (string)\App::user()->getRegionId() ?: '14974';

        // cache
        $route = $request->attributes->get('route');
        /** @var callable[] $actionByRoute */
        $actionByRoute = [
            'product' => function(\Http\Request $httpRequest) use (&$regionId) {
                $productToken = explode('/', $httpRequest->attributes->get('productPath'));
                $productToken = end($productToken);

                if ($productToken) {
                    $action = (new Action\ProductCard\Get());
                    $request = $action->createRequest();
                    $request->urlPath = $httpRequest->getPathInfo();
                    $request->productCriteria = ['token' => $productToken];
                    $request->regionId = $regionId;

                    $action->execute($request);
                }
            },
        ];

        if ($route && isset($actionByRoute[$route])) {
            call_user_func($actionByRoute[$route], $request);
        } else {
            \App::config()->curlCache['enabled'] = false; // FIXME: динамическое переопределение конфига запрещено!
        }
    }
}
