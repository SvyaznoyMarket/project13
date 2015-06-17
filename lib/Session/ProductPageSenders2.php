<?php

namespace Session;

/**
 * SITE-5072
 */
class ProductPageSenders2 {
    /**
     * @param string $productUid
     * @return string
     */
    public static function get($productUid) {
        $productPageSenders = \App::session()->get(\App::config()->product['productPageSenders2SessionKey']);
        if (isset($productPageSenders[$productUid])) {
            return $productPageSenders[$productUid];
        }

        return '';
    }

    public static function clean() {
        \App::session()->set(\App::config()->product['productPageSenders2SessionKey'], []);
    }
}