<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    array $deliveryData = []
) {
    // массив данных способов доставки
    $delivery = [];
    if (isset($deliveryData['product'][0]['delivery'])) {
        foreach ($deliveryData['product'][0]['delivery'] as $item) {
            if (in_array($item['token'], ['self', 'standart', /*'pickpoint',*/ 'now'])) {
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
    } ?>

    <div id="avalibleShop" class="popup">
        <i class="close" title="Закрыть">Закрыть</i>
        <div id="ymaps-avalshops"></div>
        <a href="#" class="bOrangeButton fr mt5">Перейти к магазину</a>
    </div>

    <?= $helper->renderWithMustache('product/___delivery', ['delivery' => $delivery]); // список способов доставки ?>
<? };