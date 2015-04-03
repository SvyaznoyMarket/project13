<?php

namespace Controller\Order\OneClick;

class NewAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        return new \Http\RedirectResponse(\App::router()->generate('orderV3.one-click'));
    }
}