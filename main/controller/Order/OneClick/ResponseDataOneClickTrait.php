<?php

namespace Controller\Order\OneClick;

trait ResponseDataOneClickTrait {
    use \Controller\Order\ResponseDataTrait {
        \Controller\Order\ResponseDataTrait::failResponseData as parentFailResponseData;
    }

    protected function failResponseData(\Exception $exception, array &$responseData) {
        $this->cart = \App::user()->getOneClickCart();
        $this->parentFailResponseData($exception, $responseData);
    }
}