<?php
/**
 * @var $page          \Templating\HtmlLayout
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */

$productIds = array_keys($productsById);
?>

<? if (\App::config()->partners['admitad']['enabled']): ?>
    <script type="text/javascript">
        (function (d, w) {
            w._admitadPixel = {
                response_type: 'img', // 'script' or 'img'. Default: 'img'
                action_code: '1',
                campaign_code: '5000a9497f'
            };
            w._admitadPositions = w._admitadPositions || [];

            <? foreach ($orders as $order) : ?>
                <? foreach ($order->product as $orderProduct) : ?>
                    w._admitadPositions.push(<?= json_encode([
                        'uid' => \App::request()->cookies->get('admitad_uid'),
                        'tariff_code' => call_user_func(function() use($orderProduct, $productsById) {
                            /** @var $product \Model\Product\Entity */
                            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;

                            if ($product->getPriceOld()) {
                                return 8;
                            }

                            foreach (array_reverse($product->getCategory()) as $category) {
                                /** @var \Model\Product\Category\Entity $category */
                                switch ($category->ui) {
                                    case \Model\Product\Category\Entity::UI_MEBEL:
                                    case \Model\Product\Category\Entity::UI_UKRASHENIYA_I_CHASY:
                                        return 1;
                                    case \Model\Product\Category\Entity::UI_ODEZHDA_AKSESSUARY:
                                    case \Model\Product\Category\Entity::UI_BYTOVAYA_TEHNIKA_AKSESSUARY:
                                    case \Model\Product\Category\Entity::UI_SPORT_I_OTDYH_VELOSIPEDY_AKSESSUARY:
                                    case \Model\Product\Category\Entity::UI_TOVARY_DLYA_DOMA_AKSESSUARY_DLYA_VANNOI:
                                    case \Model\Product\Category\Entity::UI_ELECTRONIKA_AKSESSUARY:
                                    case \Model\Product\Category\Entity::UI_DETSKIE_TOVARY_AKSESSUARY_DLYA_AVTOKRESEL:
                                    case \Model\Product\Category\Entity::UI_ZOOTOVARY_AKSESSUARY_DLYA_AKVARIUMOV:
                                        return 2;
                                    case \Model\Product\Category\Entity::UI_SPORT_I_OTDYH:
                                        return 3;
                                    case \Model\Product\Category\Entity::UI_ODEZHDA:
                                    case \Model\Product\Category\Entity::UI_DETSKIE_TOVARY:
                                    case \Model\Product\Category\Entity::UI_PARFUMERIA_I_COSMETIKA:
                                    case \Model\Product\Category\Entity::UI_SDELAY_SAM:
                                    case \Model\Product\Category\Entity::UI_SAD_I_OGOROD:
                                        return 4;
                                    case \Model\Product\Category\Entity::UI_PODARKI_I_HOBBY:
                                        return 5;
                                    case \Model\Product\Category\Entity::UI_TOVARY_DLYA_DOMA:
                                    case \Model\Product\Category\Entity::UI_BYTOVAYA_TEHNIKA:
                                    case \Model\Product\Category\Entity::UI_AVTO:
                                    case \Model\Product\Category\Entity::UI_ZOOTOVARY:
                                        return 6;
                                    case \Model\Product\Category\Entity::UI_ELECTRONIKA:
                                        return 7;
                                }
                            }

                            return null;
                        }),
                        'order_id' => $order->numberErp,
                        'position_id' => array_search($orderProduct->getId(), $productIds, true) + 1,
                        'currency_code' => 'RUB',
                        'position_count' => count($productIds),
                        'price' => $orderProduct->getPrice(),
                        'quantity' => $orderProduct->getQuantity(),
                        'product_id' => $orderProduct->getId(),
                        'payment_type' => 'sale',
                    ], JSON_UNESCAPED_UNICODE) ?>);
                <? endforeach ?>
            <? endforeach ?>
            var id = '_admitad-pixel';
            if (d.getElementById(id)) { return; }
            var s = d.createElement('script');
            s.id = id;
            var r = (new Date).getTime();
            var protocol = (d.location.protocol === 'https:' ? 'https:' : 'http:');
            s.src = protocol + '//cdn.asbmit.com/static/js/pixel.min.js?r=' + r;
            var head = d.getElementsByTagName('head')[0];
            head.appendChild(s);
        })(document, window)
    </script>
    <noscript>
        <img src="//ad.admitad.com/r?campaign_code=5000a9497f&action_code=1&payment_type=sale&response_type=img&uid=&tariff_code=&order_id=&position_id=&currency_code=&position_count=&price=&quantity=&product_id=" width="1" height="1" alt="">
    </noscript>
<? endif ?>