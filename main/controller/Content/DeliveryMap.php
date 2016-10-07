<?php

namespace Controller\Content;

class DeliveryMap {
    /**
     * @return \Http\Response
     */
    public function execute() {
        return new \Http\RedirectResponse(\App::router()->generateUrl('shop'), 301);
    }
}