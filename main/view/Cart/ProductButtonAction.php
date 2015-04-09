<?php

namespace View\Cart;

use Session\AbTest\AbTest;

class ProductButtonAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @param null $onClick
     * @param bool $isRetailRocket
     * @param array $sender Данные поставщика, например: {name: retailrocket, position: ProductSimilar, action: Переход в карточку товара}
     * @param bool $noUpdate
     * @param string|null $location
     * @param \Model\Product\Entity[] $kitProducts
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        $onClick = null,
        $isRetailRocket = false,
        array $sender = [],
        $noUpdate = false, // Не обновлять кнопку купить
        $location = null, // местоположение кнопки купить: userbar, product-card, ...
        $sender2 = ''
    ) {
        $buyUrl = $this->getBuyUrl($helper, $product, $isRetailRocket, $sender, $sender2);

        $colorClass = AbTest::getColorClass($product, $location);

        $data = [
            'id'         => 'buyButton-' . $product->getId() . '-'. md5(json_encode([$location, isset($sender['position']) ? $sender['position'] : null])),
            'disabled'   => false,
            'url'        => null,
            'buyUrl'     => $buyUrl,
            'value'      => null,
            'inShopOnly' => false,
            'class'      => \View\Id::cartButtonForProduct($product->getId()),
            'onClick'    => $onClick,
            'sender'     => $helper->json($sender),
            'sender2'    => $sender2,
            'productUi'  => $product->getUi(),
            'colorClass' => $colorClass,
            'data'       => [
                'productId' => $product->getId(),
                'upsale'    => json_encode([
                    'url'        => $helper->url('product.upsale', ['productId' => $product->getId()]),
                    'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
                ]),
                'noUpdate'  => $noUpdate,
            ],
        ];

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled js-orderButton jsBuyButton';
            $data['value'] = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет';
        } else if (5 == $product->getStatusId()) { // SITE-2924
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled js-orderButton jsBuyButton';
            $data['value'] = 'Нет';
        } else if (\App::config()->wikimart['enabled'] && $product->isOnlyWikimartPartnerOffer()) {
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink jsWmBuyButton' . $colorClass;
            $data['value'] = 'Купить*';
            $data['wikimartId'] = $product->getWikimartId();
        } else if (\App::config()->wikimart['enabled']) {
            $data['id'] = 'quickBuyButton-' . $product->getId();
            $data['url'] = $this->getOneClickBuyUrl($helper, $product, $isRetailRocket, $sender, $sender2);
            $data['class'] .= ' btnBuy__eLink js-orderButton jsOneClickButton-new' . $colorClass;
            $data['value'] = 'Купить';
        } else if ($slotPartnerOffer = $product->getSlotPartnerOffer()) {
            $data['isSlot'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btn btn--slot js-orderButton js-slotButton ' . ('product-card' !== $location ? 'btn--short' : 'btn--big');
            $data['value'] = 'product-card' === $location ? 'Отправить заявку' : 'Как купить?';
            $data['full'] = 'userbar' === $location || 'product-card' === $location ? '0' : '1';
            $data['productUrl'] = $product->getLink();
            $data['productArticle'] = $product->getArticle();
            $data['productPrice'] = $product->getPrice();
            $data['partnerName'] = $slotPartnerOffer['name'];
            $data['partnerOfferUrl'] = $slotPartnerOffer['offer'];
        } else if ($product->isGifteryCertificate()) {
            $data['isGiftery'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink giftery-show-widget ';
            $data['value'] = 'Купить';
        } else if ($product->isInShopStockOnly() && \App::user()->getRegion()->getForceDefaultBuy()) { // Резерв товара
            $data['id'] = 'quickBuyButton-' . $product->getId();
            $data['url'] = $this->getOneClickBuyUrl($helper, $product, $isRetailRocket, $sender, $sender2);
            $data['class'] .= ' btnBuy__eLink js-orderButton jsOneClickButton-new' . $colorClass;
            $data['value'] = 'Купить';
        } else if ($product->getKit() && !$product->getIsKitLocked()) {
            $data['isKit'] = $location === 'slider' ? false : true;
            $data['value'] = 'Купить';
            $data['class'] .= ' btnBuy__eLink js-orderButton js-kitButton' . $colorClass;
            $data['url'] = $this->getKitBuyUrl($helper, $product, $isRetailRocket, $sender, $sender2);
		} else if (\App::user()->getCart()->hasProduct($product->getId()) && !$noUpdate) {
            $data['url'] = $helper->url('cart');
            $data['class'] .= ' btnBuy__eLink mBought';
            $data['value'] = 'В корзине';
        } else {
            $data['url'] = $buyUrl;
            $data['class'] .= ' btnBuy__eLink js-orderButton jsBuyButton' . $colorClass;
            $data['value'] = 'Купить';
        }

        return $data;
    }

    private function getBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $isRetailRocket, $sender, $sender2) {
        return $helper->url(
            'cart.product.set',
            array_merge(
                $this->getSenderUrlParams($helper, $product, $isRetailRocket, $sender, $sender2),
                ['productId' => $product->getId()]
            )
        );
    }

    private function getOneClickBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $isRetailRocket, $sender, $sender2) {
        return $helper->url(
            'cart.oneClick.product.set',
            array_merge(
                $this->getSenderUrlParams($helper, $product, $isRetailRocket, $sender, $sender2),
                ['productId' => $product->getId()]
            )
        );
    }

    private function getKitBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $isRetailRocket, $sender, $sender2) {
        $urlParams = $this->getSenderUrlParams($helper, $product, $isRetailRocket, $sender, $sender2);

        foreach ($product->getKit() as $kitItem) {
            $urlParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }

        return $helper->url('cart.product.setList', $urlParams);
    }

    private function getSenderUrlParams(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $isRetailRocket, $sender, $sender2) {
        $urlParams = [];

        if (!$product->getKit() || $product->getIsKitLocked()) {
            if ($helper->hasParam('sender')) {
                $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
            } else if ($isRetailRocket) {
                $urlParams['sender'] = 'retailrocket';
            }
        }

        if ($sender) {
            $urlParams = array_merge($urlParams, [
                'sender' => [
                    'name'      => isset($sender['name']) ? $sender['name'] : null,
                    'position'  => isset($sender['position']) ? $sender['position'] : null,
                    'method'    => isset($sender['method']) ? $sender['method'] : null,
                    'from'      => isset($sender['from']) ? $sender['from'] : null,
                ],
            ]);
        }

        if ($sender2) {
            $urlParams['sender2'] = $sender2;
        }

        return $urlParams;
    }
}