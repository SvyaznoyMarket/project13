<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $deliveryData = [],
    array $shopStates = []
) {
    /** @var $shopStates \Model\Product\ShopState\Entity[] */

    // массив данных способов доставки
    $delivery = [];
    if (isset($deliveryData['product'][0]['delivery'])) {
        foreach ($deliveryData['product'][0]['delivery'] as $item) {
            if ('self_partner_pickpoint' == $item['token']) {
                $item['token'] = 'pickpoint';
            }

            // Если есть самовывоз от Связного, то добавим его в результирующий список
            // Если еще есть и наш самовывоз, то наш потом затрет связновский
            if ($item['token'] == 'self_partner_svyaznoy') {
                $delivery['self'] = $item;
                $delivery['self']['isOnlyFromPartner'] = true;
            }

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
        $delivery['isInShopOnly'] = $product->isInShopOnly();
        $delivery['isOnlyFromPartner'] = $product->isOnlyFromPartner();

        if (in_array('pickpoint', array_keys($delivery)) && in_array('self', array_keys($delivery))) {
            unset($delivery['pickpoint']);
        }
    }

    if (isset($delivery['self']) && \Session\AbTest\AbTest::isSelfPaidDelivery()) {
        $delivery['self']['limit'] = App::config()->self_delivery['limit'];
        $delivery['self']['ab_paid_delivery'] = true;
    }

?>
    <?= $helper->renderWithMustache('product/__delivery', ['delivery' => $delivery]); // список способов доставки ?>
<? };