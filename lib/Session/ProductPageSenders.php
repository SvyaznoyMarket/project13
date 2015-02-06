<?php

namespace Session;

/**
 * SITE-5062
 */
class ProductPageSenders {
    /**
     * @param string $productUid
     * @param array|null $sender
     */
    public static function add($productUid, $sender) {
        if ($sender && is_array($sender)) {
            $productPageSenders = \App::session()->get(\App::config()->product['productPageSendersSessionKey'], []);

            unset($productPageSenders[$productUid]);
            $productPageSenders[$productUid] = $sender;

            // Чтобы нельзя было засорить сессию
            while (strlen(serialize($productPageSenders)) > 20000) {
                unset($productPageSenders[key($productPageSenders)]);
            }

            \App::session()->set(\App::config()->product['productPageSendersSessionKey'], $productPageSenders);
        }
    }

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