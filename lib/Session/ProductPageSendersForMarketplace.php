<?php

namespace Session;

/**
 * SITE-5062
 */
class ProductPageSendersForMarketplace {
    /**
     * @param string $productUid
     * @param string $sender
     */
    public static function add($productUid, $sender) {
        $sender = (string)$sender;

        if ($sender) {
            $productPageSenders = \App::session()->get(\App::config()->product['productPageSendersForMarketplaceSessionKey'], []);

            unset($productPageSenders[$productUid]);
            $productPageSenders[$productUid] = $sender;

            // Чтобы нельзя было засорить сессию
            while (strlen(serialize($productPageSenders)) > 20000) {
                unset($productPageSenders[key($productPageSenders)]);
            }

            \App::session()->set(\App::config()->product['productPageSendersForMarketplaceSessionKey'], $productPageSenders);
        }
    }

    /**
     * @param string $productUid
     * @return string
     */
    public static function get($productUid) {
        $productPageSenders = \App::session()->get(\App::config()->product['productPageSendersForMarketplaceSessionKey']);
        if (isset($productPageSenders[$productUid])) {
            return $productPageSenders[$productUid];
        }

        return '';
    }

    public static function clean() {
        \App::session()->set(\App::config()->product['productPageSendersForMarketplaceSessionKey'], []);
    }
}