<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    array $deliveryData = [],
    array $shopStates = []
) {
    /** @var $shopStates \Model\Product\ShopState\Entity[] */

    // массив данных способов доставки
    $delivery = [];
    if (isset($deliveryData['product'][0]['delivery'])) {
        foreach ($deliveryData['product'][0]['delivery'] as $item) {
            if (in_array($item['token'], ['self', 'standart', 'pickpoint', 'now'])) {
                $delivery[$item['token']] = $item;

                if (isset($item['price'])) {
                    $delivery[$item['token']] = array_merge($delivery[$item['token']], [
                        'isPriceEqualZero' => 0 === $item['price'] ? true : false,
                        'isPriceNaN' => is_nan($item['price']) ? true : false,
                    ]);
                }
            }
        }

        // флажек, открываем блок "Сегодня есть в магазинах" или нет
        $delivery['isInShopOnly'] = $product->isInShopOnly() ? true : false;

        if (in_array('pickpoint', array_keys($delivery)) && in_array('self', array_keys($delivery))) {
            unset($delivery['pickpoint']);
        }
    }

    // магазины, в которых товар находится на витрине
    if (!(bool)$delivery) {
        $delivery['now'] = [
            'id'    => \Model\DeliveryType\Entity::TYPE_NOW,
            'token' => 'now',
            'price' => null,
            'shop'  => [],
        ];
        foreach ($shopStates as $shopState) {
            $shop = $shopState->getShop();
            if (!$shop) continue;

            $delivery['now']['shop'][] = [
                'id'        => $shop->getId(),
                'name'      => $shop->getName(),
                'regime'    => $shop->getRegime(),
                'latitude'  => $shop->getLatitude(),
                'longitude' => $shop->getLongitude(),
                'url'       => $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]),
            ];
        }

        if (!(bool)$delivery['now']['shop']) {
            unset($delivery['now']);
        }
    }

?>

    <div id="avalibleShop" class="popup">
        <i class="close" title="Закрыть">Закрыть</i>
        <div id="ymaps-avalshops"></div>
        <a href="#" class="bOrangeButton fr mt5">Перейти к магазину</a>
    </div>

    <?= $helper->renderWithMustache('product/___delivery', ['delivery' => $delivery]); // список способов доставки ?>
<? };