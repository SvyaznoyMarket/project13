<?php

namespace Controller\Order;

class NewAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        return (new \Controller\OrderV3\NewAction)->execute($request);
    }
}