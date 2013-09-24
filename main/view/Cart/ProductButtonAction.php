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
        ];


        if ($product->isInShopOnly()) {
            $data['inShopOnly'] = true;
            $data['value'] = 'В магазинах';
        }

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['value'] = $product->isInShopShowroomOnly() ? 'Витринный товар' : 'Нет в наличии';
        } else {
            $urlParams = [
                'productId' => $product->getId(),
            ];
            if ($helper->hasParam('sender')) {
                $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
            }
            $data['url'] = $helper->url('cart.product.set', $urlParams);

            $data['value'] = 'Купить';
        }

        return $data;
    }
}