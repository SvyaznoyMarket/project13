<?php

namespace Controller\OrderV3\Payment;

use EnterApplication\CurlTrait;
use Http\Response;

class FailAction {
    use CurlTrait;

    /**
     * @param $request \Http\Request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        $from = $request->get('shp_from');
        if ($from === \App::config()->mobileHost) {
            $qs = $request->getQueryString();
            if (null !== $qs) {
                $qs = '?' . $qs;
            }

            return new \Http\RedirectResponse($request->getScheme() . '://' . \App::config()->mobileHost . $request->getPathInfo() . $qs);
        }

        $page = new \View\OrderV3\ErrorPage();
        $page->setParam('showHeader', false);
        $page->setParam('error', 'Оплата не выполнена. Если Вы считаете, что произошла ошибка, обратитесь в <a href="mailto:feedback@enter.ru">службу поддержки</a>.');
        $page->setParam('type', 'warning');
        $page->setParam('step', 3);

        return new Response($page->show());
    }
}