<?php

namespace View\Cart;

class ProductButtonAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\BasicEntity $product
     * @param null $onClick
     * @param bool $isRetailRocket
     * @param array $sender Данные поставщика, например: {name: retailrocket, position: ProductSimilar, action: Переход в карточку товара}
     * @param bool $noUpdate
     * @param string|null $location
     * @internal param null|string $url
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\BasicEntity $product,
        $onClick = null,
        $isRetailRocket = false,
        array $sender = [],
        $noUpdate = false, // Не обновлять кнопку купить
        $location = null, // местоположение кнопки купить: userbar, product-card, ...
        $reserveAsBuy = false
    ) {
        $urlParams = [
            'productId' => $product->getId(),
        ];

        if ($helper->hasParam('sender')) {
            $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
        } else if ($isRetailRocket) {
            $urlParams['sender'] = 'retailrocket';
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

        $buyUrl = $helper->url('cart.product.set', $urlParams);

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
            'productUi'  => $product->getUi(),
            'data'       => [
                'productId' => $product->getId(),
                'upsale'    => json_encode([
                    'url'        => $helper->url('product.upsale', ['productId' => $product->getId()]),
                    'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
                ]),
                'noUpdate'  => $noUpdate,
            ],
        ];

        /** @var $region \Model\Region\Entity|null */
        $region = \App::user()->getRegion();
        $forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled jsBuyButton';
            $data['value'] = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет';
        } else if (5 == $product->getStatusId()) { // SITE-2924
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled jsBuyButton';
            $data['value'] = 'Нет';
        } else if ($slotPartnerOffer = $product->getSlotPartnerOffer()) {
            $data['isSlot'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btn btn--slot js-slotButton ' . ('product-card' !== $location ? 'btn--short' : '');
            $data['value'] = 'product-card' === $location ? 'Отправить заявку' : 'Как купить?';
            $data['full'] = 'userbar' === $location || 'product-card' === $location ? '0' : '1';
            $data['productUrl'] = $product->getLink();
            $data['productArticle'] = $product->getArticle();
            $data['productPrice'] = $product->getPrice();
            $data['partnerName'] = $slotPartnerOffer['name'];
            $data['partnerOfferUrl'] = $slotPartnerOffer['offer'];
        } else if ($product->isInShopStockOnly() && $forceDefaultBuy) {
            if ($reserveAsBuy) {
                $data['id'] = 'quickBuyButton-' . $product->getId();
                $data['url'] = $helper->url('cart.oneClick.product.set', array_merge($urlParams, ['productId' => $product->getId()]));
                $data['class'] .= ' btnBuy__eLink jsOneClickButton-new';
                $data['value'] = 'Купить';
                $data['title'] = 'Резерв товара';
            } else {
                $data['inShopOnly'] = true;
                $data['url'] = $helper->url('cart.oneClick.product.set', array_merge($urlParams, ['productId' => $product->getId()]));
                $data['class'] .= ' btnBuy__eLink mShopsOnly jsOneClickButton';
                $data['value'] = 'Резерв';
            }
		} else if (\App::user()->getCart()->hasProduct($product->getId()) && !$noUpdate) {
            $data['url'] = $helper->url('cart');
            $data['class'] .= ' btnBuy__eLink mBought';
            $data['value'] = 'В корзине';
        } else {
            $data['url'] = $buyUrl;
            $data['class'] .= ' btnBuy__eLink jsBuyButton';
            $data['value'] = 'Купить';
        }

        return $data;
    }
}