<?php

namespace View\Cart;

use Session\AbTest\AbTest;

class ProductButtonAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @param null $onClick
     * @param array $sender Данные поставщика, например: {name: retailrocket, position: ProductSimilar, action: Переход в карточку товара}
     * @param bool $noUpdate
     * @param string|null $location
     * @param string $sender2
     * @param bool $useNewStyles
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        $onClick = null,
        $sender = [],
        $noUpdate = false, // Не обновлять кнопку купить
        $location = null, // местоположение кнопки купить: userbar, product-card, ...
        $sender2 = '',
        $useNewStyles = false,
        $inShowroomAsButton = true
    ) {
        $data = [
            'id'         => 'buyButton-' . $product->getId() . '-'. md5(json_encode([$location, isset($sender['position']) ? $sender['position'] : null])),
            'disabled'   => false,
            'url'        => null,
            'value'      => null,
            'inShopOnly' => false,
            'class'      => \View\Id::cartButtonForProduct($product->getId()),
            'onClick'    => $onClick,
            'sender'     => $helper->json($sender),
            'sender2'    => $sender2,
            'productUi'  => $product->getUi(),
            'location'   => $location,
            'inShowroomAsLabel' => false,
            'data'       => [
                'productId' => $product->getId(),
                'upsale'    => json_encode([
                    'url'        => $helper->url('product.upsale', ['productId' => $product->getId()]),
                    'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
                ]),
                'noUpdate'  => $noUpdate,
            ],
            'divClass'  => 'btnBuy',
            'surroundDiv' => true
        ];

        if (!$product->getIsBuyable()) {
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled js-orderButton jsBuyButton';
            if ($product->isInShopShowroomOnly()) {
                if (!$inShowroomAsButton) {
                    $data['inShowroomAsLabel'] = true;
                }
                
                $data['value'] = 'На витрине';
            } else {
                $data['value'] = 'Нет';
            }
        } else if (5 == $product->getStatusId()) { // SITE-2924
            $data['disabled'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink mDisabled js-orderButton jsBuyButton';
            $data['value'] = 'Нет';
        } else if ($slotPartnerOffer = $product->getSlotPartnerOffer()) {
            $data['isSlot'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btn btn--slot js-orderButton js-slotButton';

            if ($location === 'product-card') {
                $data['class'] .= ' btn--big';
            } else if ($location !== 'userbar') {
                $data['class'] .= ' btn--short';
            }

            $data['value'] = in_array($location, ['product-card', 'userbar'], true) ? 'Отправить заявку' : 'Как купить?';
            $data['full'] = 'userbar' === $location || 'product-card' === $location ? '0' : '1';
            $data['productUrl'] = $product->getLink();
            $data['productArticle'] = $product->getArticle();
            $data['productPrice'] = $product->getPrice();
            $data['partnerName'] = $slotPartnerOffer['name'];
            $data['partnerOfferUrl'] = $slotPartnerOffer['offer'];
        } else if ($product->isGifteryCertificate()) {
            $data['isGiftery'] = true;
            $data['url'] = '#';
            $data['class'] .= ' btnBuy__eLink giftery-show-widget';
            $data['value'] = 'Купить';
        } else if ($product->isInShopStockOnly() && \App::user()->getRegion()->getForceDefaultBuy()) { // Резерв товара
            $data['id'] = 'quickBuyButton-' . $product->getId();
            $data['url'] = $this->getOneClickBuyUrl($helper, $product, $sender, $sender2);
            $data['class'] .= ' btnBuy__eLink js-orderButton jsOneClickButton-new';
            $data['value'] = 'Купить';
        } else if ($product->getKit() && !$product->getIsKitLocked()) {
            $data['isKit'] = $location === 'slider' ? false : true;
            $data['value'] = 'Купить';
            $data['class'] .= ' btnBuy__eLink js-orderButton js-kitButton';
            $data['url'] = $this->getKitBuyUrl($helper, $product, $sender, $sender2);
		} else if (\App::user()->getCart()->hasProduct($product->getId()) && !$noUpdate) {
            $data['url'] = $helper->url('cart');
            $data['class'] .= ' btnBuy__eLink mBought';
            $data['value'] = 'В корзине';
        } else {
            // Внимание!!! Генерация URL адреса для покупки также происходит в web/js/dev/common/UserCustomBindings.js
            $data['url'] = $this->getBuyUrl($helper, $product, $sender, $sender2);
            $data['class'] .= ' btnBuy__eLink js-orderButton jsBuyButton';
            $data['value'] = 'Купить';
            if (\App::abTest()->isNewProductPage() && in_array($location, ['product-card', 'userbar'])) $data['value'] = 'Купить';
        }

        /* Новая карточка товара */
        if (\App::abTest()->isNewProductPage() && $location !== null && $useNewStyles) {
            $data['class'] = str_replace('btnBuy__eLink', '', $data['class']) . ' btn-type btn-type--buy';
            if ('product-card' === $location) $data['class'] .= ' btn-type--longer btn-type--buy--bigger';
            if ('slider' === $location) $data['class'] .= ' btn-type--light';
            if ('userbar' === $location) {
                $data['class'] .= ' topbarfix_buy-btn';
                $data['surroundDiv'] = false;
            }
            $data['divClass'] = 'buy-online';
        }

        if ($location == 'user-favorites' && !$product->getIsBuyable()) $data['value'] = 'Нет в наличии';

        return $data;
    }

    private function getBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $sender, $sender2) {
        return $helper->url(
            'cart.product.set',
            array_merge(
                $this->getSenderUrlParams($sender, $sender2),
                ['productId' => $product->getId()]
            )
        );
    }

    private function getOneClickBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $sender, $sender2) {
        return $helper->url(
            'cart.oneClick.product.set',
            array_merge(
                $this->getSenderUrlParams($sender, $sender2),
                ['productId' => $product->getId()]
            )
        );
    }

    private function getKitBuyUrl(\Helper\TemplateHelper $helper, \Model\Product\Entity $product, $sender, $sender2) {
        $urlParams = $this->getSenderUrlParams($sender, $sender2);

        foreach ($product->getKit() as $kitItem) {
            $urlParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }

        return $helper->url('cart.product.setList', $urlParams);
    }

    private function getSenderUrlParams($sender, $sender2) {
        $urlParams = [];

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