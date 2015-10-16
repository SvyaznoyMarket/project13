<?php

namespace Controller\Order;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function complete(\Http\Request $request) {
        return new \Http\RedirectResponse(\App::router()->generate('cart'));
    }
}
