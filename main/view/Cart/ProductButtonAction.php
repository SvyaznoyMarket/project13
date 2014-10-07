<?php

namespace View\Cart;

class ProductButtonAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\BasicEntity $product
     * @param string|null $url
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\BasicEntity $product,
        $url = null
    ) {
        $data = [
            'disabled'   => null,
            'url'        => null,
            'value'      => null,
            'inShopOnly' => null,
            'data'       => [
                'group'  => $product->getId(),
                'upsale' => json_encode([
                    'url' => $helper->url('product.upsale', ['productId' => $product->getId()]),
                    'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
                ]),
            ],
            'class'      => \View\Id::cartButtonForProduct($product->getId()),
        ];

        $data['value'] = 'Купить';

        /** @var $region \Model\Region\Entity|null */
        $region = \App::user()->getRegion();
        $forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

        if ($product->isInShopOnly() && $forceDefaultBuy) {
            $data['inShopOnly'] = true;
            $data['value'] = 'Резерв';
            $data['url'] = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
            $data['class'] .= ' jsOneClickButton';
        }

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' mDisabled jsBuyButton';
            $data['value'] = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет';
        } else if (\App::user()->getCart()->hasProduct($product->getId())) {
            $data['url'] = $helper->url('cart');
            $data['class'] .= ' mBought';
            $data['value'] = 'В корзине';
        } else if (!isset($data['url'])) {
            $urlParams = [
                'productId' => $product->getId(),
            ];
            if ($helper->hasParam('sender')) {
                $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
            }
            $data['url'] = $helper->url('cart.product.set', $urlParams);
            $data['class'] .= ' jsBuyButton';
        }

        return $data;
    }
}