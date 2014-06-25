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
        $delivery['isInShopOnly'] = $product->isInShopOnly();
        $delivery['isOnlyFromPartner'] = $product->isOnlyFromPartner();

        if (in_array('pickpoint', array_keys($delivery)) && in_array('self', array_keys($delivery))) {
            unset($delivery['pickpoint']);
        }
    }

?>
    <?= $helper->renderWithMustache('product/__delivery', ['delivery' => $delivery]); // список способов доставки ?>
<? };