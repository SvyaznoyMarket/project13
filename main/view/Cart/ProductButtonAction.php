<?php

namespace View\Cart;

class ProductButtonAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\BasicEntity $product
     * @param null $onClick
     * @param bool $isRetailRocket
     * @internal param null|string $url
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\BasicEntity $product,
        $onClick = null,
        $isRetailRocket = false
    ) {
        $data = [
            'disabled'   => false,
            'url'        => null,
            'value'      => null,
            'inShopOnly' => false,
            'class'      => \View\Id::cartButtonForProduct($product->getId()),
            'onClick'    => $onClick,
            'data'       => [
                'productId' => $product->getId(),
                'upsale' => json_encode([
                    'url' => $helper->url('product.upsale', ['productId' => $product->getId()]),
                    'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
                ]),
            ],
        ];

        /** @var $region \Model\Region\Entity|null */
        $region = \App::user()->getRegion();
        $forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' mDisabled jsBuyButton';
            $data['value'] = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет';
        } else if (5 == $product->getStatusId()) { // SITE-2924
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' mDisabled jsBuyButton';
            $data['value'] = 'Нет';
        } else if ($product->isInShopStockOnly() && $forceDefaultBuy) {
            $data['inShopOnly'] = true;
            $data['url'] = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
            $data['class'] .= ' mShopsOnly jsOneClickButton';
            $data['value'] = 'Резерв';
		} else if (\App::user()->getCart()->hasProduct($product->getId())) {
            $data['url'] = $helper->url('cart');
            $data['class'] .= ' mBought';
            $data['value'] = 'В корзине';
        } else {
            $urlParams = [
                'productId' => $product->getId(),
            ];

            if ($helper->hasParam('sender')) {
                $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
            } else if ($isRetailRocket) {
                $urlParams['sender'] = 'retailrocket';
            }

            $data['url'] = $helper->url('cart.product.set', $urlParams);
            $data['class'] .= ' jsBuyButton';
            $data['value'] = 'Купить';
        }

        return $data;
    }
}