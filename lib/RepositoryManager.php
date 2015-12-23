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
            $instance = new \Model\Product\Filter\Repository(\App::searchClient());
        }

        return $instance;
    }

    /**
     * @return Model\Page\Repository
     */
    static public function page() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Page\Repository(\App::coreClientV2());
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
            $instance = new \Model\Shop\Repository(\App::scmsClient());
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
     * @return Model\PaymentMethod\Group\Repository
     */
    static function paymentGroup() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\PaymentMethod\Group\Repository(\App::coreClientV2());
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
            $instance = new \Model\Promo\Repository(\App::scmsClient());
        }

        return $instance;
    }

    /**
     * @return Model\Menu\Repository
     */
    static function menu() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Menu\Repository();
        }

        return $instance;
    }

    /**
     * @return Model\Subscribe\Channel\Repository
     */
    static function subscribeChannel() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Subscribe\Channel\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\Review\Repository
     */
    static function review() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Review\Repository(\App::reviewsClient());
        }

        return $instance;
    }

    /**
     * @return Model\Slice\Repository
     */
    static function slice() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Slice\Repository(\App::scmsSeoClient());
        }

        return $instance;
    }

    /**
     * @return \Model\Order\BonusCard\Repository
     */
    static function bonusCard() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\Order\BonusCard\Repository(\App::coreClientV2());
        }

        return $instance;
    }

    /**
     * @return Model\EnterprizeCoupon\Repository
     */
    static public function enterprize() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\EnterprizeCoupon\Repository(\App::scmsClientV2(), \App::coreClientV2());
        }

        return $instance;
    }
}