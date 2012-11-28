<?php

class RepositoryManager {
    /**
     * @return Model\Region\Repository
     */
    static public function getRegion() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Region\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\User\Repository
     */
    static public function getUser() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\User\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Repository
     */
    static public function getProduct() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Category\Repository
     */
    static public function getProductCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Filter\Repository
     */
    static public function getProductFilter() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Filter\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Service\Repository
     */
    static public function getService() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Service\Category\Repository
     */
    static public function getServiceCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Tag\Repository
     */
    static public function getTag() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Tag\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Shop\Repository
     */
    static public function getShop() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Shop\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\CreditBank\Repository
     */
    static public function getCreditBank() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\CreditBank\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Line\Repository
     */
    static function getLine() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Line\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Order\Repository
     */
    static function getOrder() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Order\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\DeliveryType\Repository
     */
    static function getDeliveryType() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\DeliveryType\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\PaymentMethod\Repository
     */
    static function getPaymentMethod() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\PaymentMethod\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Banner\Repository
     */
    static function getBanner() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Banner\Repository(\App::coreClientV2());
        }

        return $instance;
    }
}