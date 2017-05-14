<?php
/**
 * @var $page          \Templating\HtmlLayout
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */

$productIds = array_keys($productsById);
?>
<? foreach ($orders as $order): ?>
    <script src="https://www.gdeslon.ru/thanks.js?codes=001:<?= urlencode(call_user_func(function() use($order, $productsById) {
        $commission = '0';
        foreach ($order->product as $orderProduct) {
            /** @var $product \Model\Product\Entity */
            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;

            $productCommission = call_user_func(function() use($product) {
                // todo использовать цену товара с учётом скидки
                if ($product->getPriceOld()) {
                    return '3.9';
                }

                foreach (array_reverse($product->getCategory()) as $category) {
                    /** @var \Model\Product\Category\Entity $category */
                    switch ($category->ui) {
                        case \Model\Product\Category\Entity::UI_MEBEL:
                        case \Model\Product\Category\Entity::UI_UKRASHENIYA_I_CHASY:
                            return '15.6';
                        case \Model\Product\Category\Entity::UI_ODEZHDA_AKSESSUARY:
                        case \Model\Product\Category\Entity::UI_BYTOVAYA_TEHNIKA_AKSESSUARY:
                        case \Model\Product\Category\Entity::UI_SPORT_I_OTDYH_VELOSIPEDY_AKSESSUARY:
                        case \Model\Product\Category\Entity::UI_TOVARY_DLYA_DOMA_AKSESSUARY_DLYA_VANNOI:
                        case \Model\Product\Category\Entity::UI_ELECTRONIKA_AKSESSUARY:
                        case \Model\Product\Category\Entity::UI_DETSKIE_TOVARY_AKSESSUARY_DLYA_AVTOKRESEL:
                        case \Model\Product\Category\Entity::UI_ZOOTOVARY_AKSESSUARY_DLYA_AKVARIUMOV:
                            return '13';
                        case \Model\Product\Category\Entity::UI_SPORT_I_OTDYH:
                            return '11.7';
                        case \Model\Product\Category\Entity::UI_ODEZHDA:
                        case \Model\Product\Category\Entity::UI_DETSKIE_TOVARY:
                        case \Model\Product\Category\Entity::UI_PARFUMERIA_I_COSMETIKA:
                        case \Model\Product\Category\Entity::UI_SDELAY_SAM:
                        case \Model\Product\Category\Entity::UI_SAD_I_OGOROD:
                            return '10.4';
                        case \Model\Product\Category\Entity::UI_PODARKI_I_HOBBY:
                            return '9.1';
                        case \Model\Product\Category\Entity::UI_TOVARY_DLYA_DOMA:
                        case \Model\Product\Category\Entity::UI_AVTO:
                        case \Model\Product\Category\Entity::UI_ZOOTOVARY:
                            return '7.8';
                        case \Model\Product\Category\Entity::UI_ELECTRONIKA:
                        case \Model\Product\Category\Entity::UI_BYTOVAYA_TEHNIKA:
                            return '3.9';
                    }
                }

                return '0';
            });

            $commission = bcadd($commission, bcdiv(bcmul(bcmul($product->getPrice(), $orderProduct->getQuantity(), 10), $productCommission, 10), 100, 10), 2);
        }

        return $commission;
    })) ?>&order_id=<?= urlencode($order->numberErp) ?>&merchant_id=81901" async type="text/javascript"></script>
<? endforeach ?>