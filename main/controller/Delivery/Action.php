<?php

namespace Controller\Delivery;

use Controller\Order\ResponseDataTrait;

class Action {
    use ResponseDataTrait;

    /**
     * @param bool $paypalECS
     * @param bool $lifeGift
     * @param bool $oneClick
     * @return array
     */
    public function getResponseData($paypalECS = false, $lifeGift = false, $oneClick = false) {

    }
}