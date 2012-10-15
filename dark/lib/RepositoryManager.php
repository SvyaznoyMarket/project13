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

    static public function getCreditBank() {
        static $instance;

        if (!$instance) {
            $instance = new \Model\CreditBank\Repository(\App::coreClientV2());
        }

        return $instance;
    }
}