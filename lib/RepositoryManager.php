<?php

class RepositoryManager {
    /**
     * @return Model\Region\Repository
     */
    static public function region() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Region\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\User\Repository
     */
    static public function user() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\User\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Repository
     */
    static public function product() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Category\Repository
     */
    static public function productCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Filter\Repository
     */
    static public function productFilter() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Filter\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Video\Repository
     */
    static public function productVideo() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Video\Repository(\App::dataStoreClient());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Service\Repository
     */
    static public function service() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Product\Service\Category\Repository
     */
    static public function serviceCategory() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Product\Service\Category\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Tag\Repository
     */
    static public function tag() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Tag\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Shop\Repository
     */
    static public function shop() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Shop\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\CreditBank\Repository
     */
    static public function creditBank() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\CreditBank\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Line\Repository
     */
    static function line() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Line\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Order\Repository
     */
    static function order() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Order\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\DeliveryType\Repository
     */
    static function deliveryType() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\DeliveryType\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\PaymentMethod\Repository
     */
    static function paymentMethod() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\PaymentMethod\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Banner\Repository
     */
    static function banner() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Banner\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Subway\Repository
     */
    static function subway() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Subway\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Promo\Repository
     */
    static function promo() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Promo\Repository(\App::dataStoreClient());
        }

        return $instance;
    }

    /**
     * @return Model\Menu\Repository
     */
    static function menu() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Menu\Repository(\App::dataStoreClient());
        }

        return $instance;
    }

    /**
     * @return Model\Subscribe\Channel\Repository
     */
    static function subscribeChannel() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Subscribe\Channel\Repository(\App::dataStoreClient());
        }

        return $instance;
    }
}