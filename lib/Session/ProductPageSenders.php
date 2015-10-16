<?php

namespace Session;

/**
 * SITE-5062
 */
class ProductPageSenders {
    /**
     * @param string $productUid
     * @return array
     */
    public static function get($productUid) {
        $productPageSenders = \App::session()->get(\App::config()->product['productPageSendersSessionKey']);
        if (isset($productPageSenders[$productUid])) {
            return $productPageSenders[$productUid];
        }

        return [];
    }

    public static function clean() {
        \App::session()->set(\App::config()->product['productPageSendersSessionKey'], []);
    }
}