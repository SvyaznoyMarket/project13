<?php

class RepositoryManager {
    static public function getRegion() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Region\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getUser() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\User\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getProduct() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getProductCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getProductFilter() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Filter\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getService() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getServiceCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getTag() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Tag\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getShop() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Shop\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static public function getCreditBank() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\CreditBank\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static function getLine() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Line\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static function getOrder() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Order\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static function getDeliveryType() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\DeliveryType\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static function getPaymentMethod() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\PaymentMethod\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    static function getBanner() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Banner\Repository(\App::coreClientV2());
        }

        return $instance;
    }
}