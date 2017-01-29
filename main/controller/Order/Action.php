<?php

namespace Controller\Order;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function completeAction(\Http\Request $request) {
        return new \Http\RedirectResponse(\App::router()->generateUrl('orderV3.complete'), 301);
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function newAction(\Http\Request $request) {
        return new \Http\RedirectResponse(\App::router()->generateUrl('orderV3'), 301);
    }
}
