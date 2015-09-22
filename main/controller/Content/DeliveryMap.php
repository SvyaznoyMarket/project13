<?php

namespace Controller\Content;

class DeliveryMap {
    /**
     * @return \Http\Response
     */
    public function execute() {
        return new \Http\RedirectResponse(\App::router()->generate('shop'), 301);
    }
}