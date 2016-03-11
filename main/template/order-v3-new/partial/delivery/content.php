<?php
/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity|null $orderDelivery
 * @param null $error
 * @param \Model\User\Address\Entity[] $userAddresses
 * @param \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition
 * @param \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons
 * @param array $undo
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery = null,
    $error = null,
    array $userAddresses = [],
    \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition = null,
    array $userEnterprizeCoupons = [],
    $undo = []
) {
?>
    <? if ($undo): ?>
        <div class="js-order-undo-container order-message" data-redirect-url="<?= $helper->escape($undo['redirectUrl']) ?>">
            <div class="order-message__body"></div>
            <div class="order-message__overlay js-order-undo-overlay"></div>
            <div class="order-message__header" style="">
                <div class="order-message__header-wrap">
                    <div class="order-message__info-block">
                        <? if ($undo['type'] === 'stashOrder'): ?>
                            <h3 class="order-message__title">Вы отложили заказ на сумму <?= $helper->formatPrice($undo['order']['sum']) ?>&thinsp;<span class="rubl">p</span></h3>
                            <span class="order-message__product">
                                <?= $helper->escape($undo['products'][0]['name']) ?>
                                <? if (count($undo['products']) > 1): ?>
                                    <? $otherCount = count($undo['products']) - 1 ?>
                                    и ещё <?= $otherCount . ' ' . $helper->numberChoice($otherCount, ['товар', 'товара', 'товаров']) ?>
                                <? endif ?>
                            </span>
                        <? elseif ($undo['type'] === 'moveProductToFavorite'): ?>
                            <h3 class="order-message__title">Вы перенесли в избранное</h3>
                            <span class="order-message__product"><?= $helper->escape($undo['products'][0]['name']) ?></span>
                        <? elseif ($undo['type'] === 'deleteProduct'): ?>
                            <h3 class="order-message__title">Вы удалили</h3>
                            <span class="order-message__product"><?= $helper->escape($undo['products'][0]['name']) ?></span>
                        <? endif ?>
                    </div>

                    <div class="order-message__ctrl-block">
                        <a class="order-message__ctrl js-order-undo-apply" href="#">Вернуть</a>
                        <a class="order-message__ctrl order-message__ctrl_close js-order-undo-close" href="#">✕</a>
                    </div>

                    <div class="order-message__progressbar js-order-undo-progressbar"></div>
                </div>
            </div>
        </div>
    <? endif ?>

    <? if ($orderDelivery): ?>
        <?
        $orderCount = count($orderDelivery->orders);
        $region = \App::user()->getRegion();
        $firstOrder = reset($orderDelivery->orders);

        $isCoordsValid = $region && $region->getLatitude() != null && $region->getLongitude() != null;

        $initialMapCords = [
            'latitude' => $isCoordsValid ? $region->getLatitude() : 55.76,
            'longitude' => $isCoordsValid ? $region->getLongitude() : 37.64,
            'zoom' => $isCoordsValid ? 10 : 4
        ];

        /** @var \Model\OrderDelivery\Entity\Order|null $order */
        $order = reset($orderDelivery->orders) ?: null;
        ?>

        <section class="order-page orderCnt jsOrderV3PageDelivery">
            <div class="pagehead"><h1 class="orderCnt_t">Самовывоз и доставка</h1></div>

            <? if ($orderCount > 1) : ?>
                <div class="order-error order-error--success">Товары будут оформлены как <strong><?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></strong><i class="order-error__closer js-order-err-close"></i></div>

                <div class="order-error order-error--hint">Ваш регион <span><?= $helper->escape(\App::user()->getRegion()->getName()) ?> </span><a href="#" class="order-error--hint__btn jsChangeRegion" >Изменить</a></div>
            <? endif ?>

            <?= $helper->render('order-v3-new/partial/error', ['error' => $error, 'orderDelivery' => $orderDelivery]) ?>

            <?= $helper->render('order-v3-new/partial/order-list', ['orderDelivery' => $orderDelivery, 'userAddresses' => $userAddresses, 'userInfoAddressAddition' => $userInfoAddressAddition, 'userEnterprizeCoupons' => $userEnterprizeCoupons]) ?>

            <form id="js-orderForm" class="js-form" action="<?= $helper->url('orderV3.create') ?>" method="post">
                <div class="order-wishes">
                    <span class="order-wishes__lk jsOrderV3Comment <?= $firstOrder->comment != '' ? 'opened': '' ?>"><span>Дополнительные пожелания</span></span>

                    <textarea name="order[comment]" class="jsOrderV3CommentField orderComment_fld order-wishes__field" style="display: <?= $firstOrder->comment == '' ? 'none': 'block' ?>"><?= $firstOrder->comment ?></textarea>
                </div>

                <div class="order__total-row">
                    <div class="order-total">
                        <span class="order-total__txt">Итого <?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму</span> <span class="order-total__sum"><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span>
                    </div>

                    <?= $helper->render('order-v3-new/__createButton', ['orderCount' => $orderCount, 'order' => $order]) ?>
                </div>
            </form>

            <? if (\App::abTest()->isOrderMinSumRestriction() && \App::config()->minOrderSum > $orderDelivery->getProductsSum()) : ?>
                <div class="popup popup-simple deliv-free-popup jsMinOrderSumPopup" style="display: none;">
                    <a class="to-cart-lnk" href="/">
                    <div class="popup_inn">
                        <span class="info">До оформления заказа осталось</span>
                        <span class="remain-sum"><?= \App::config()->minOrderSum - $orderDelivery->getProductsSum() ?>&thinsp;<span class="rubl">p</span></span>
                        <span class="to-cart-lnk">Продолжить покупки</span>
                    </div>
                    </a>
                </div>
            <? endif ?>

        </section>

        <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json($initialMapCords)?>"></div>
        <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
        <div id="region-name" data-value=<?= json_encode($region->getName(), JSON_UNESCAPED_UNICODE); ?>></div>
        <?= App::config()->debug ? $helper->jsonInScriptTag($orderDelivery, 'initialOrderModel') : '' ?>
        <div id="jsUserAddress" data-value="<?= $helper->json($orderDelivery->user_info->address) ?>"></div>

        <div class="popup popup-simple js-order-oferta-popup">
            <a href="" class="close"></a>

            <div class="popup_inn">
                <div class="orderOferta">
                    <div class="orderOferta_tabs">
                        <div class="orderOferta_tabs_i orderOferta_tabs_i-cur js-oferta-tab" data-tab="tab-1">Условия продажи</div>
                        <div class="orderOferta_tabs_i js-oferta-tab" data-tab="tab-2">Правовая информация</div>
                    </div>

                    <div class="orderOferta_tabcnt orderOferta_tabcnt-cur js-tab-oferta-content" id="tab-1">
                        <div class="orderOferta_tl">Условия продажи</div>
                    </div>

                    <div class="orderOferta_tabcnt js-tab-oferta-content" id="tab-2">
                        <div class="orderOferta_tl">Правовая информация для пользователей сайта ООО «Энтер»</div>

                        <p>ООО «Энтер» оставляет за собой право изменять публикуемые на данном сайте материалы в любое время без предварительного уведомления.</p>

                        <div class="orderOferta_tl">Авторские права</div>
                        <p>Информация сайта ООО «Энтер» защищена авторским правом и действующим законодательством о защите интеллектуальной собственности. ООО «Энтер» предоставляет право посетителям сайта использовать опубликованные материалы в любых личных и некоммерческих целях. Любое копирование, изменение или использование данных материалов в коммерческих целях допускается с письменного согласия ООО «Энтер».</p>

                        <div class="orderOferta_tl">Содержание материалов</div>
                        <p>ООО «Энтер» приняты все разумные меры к тому, чтобы обеспечить точность и актуальность размещенной на этом сайте информации. ООО «Энтер» оставляет за собой право вносить изменение в содержание материалов этого сайта в любое время по собственному усмотрению. Продукты или услуги, не относящиеся напрямую к ООО «Энтер», упомянуты исключительно в информационных целях. Вся информация об ООО «Энтер» и третьих сторонах на этом сайте представлена в том виде, в котором она существует в распоряжении ООО «Энтер».</p>

                        <div class="orderOferta_tl">Товарные знаки</div>
                        <p>Товарные знаки, торговые марки, а также другие средства индивидуализации, помещенные на данном сайте, являются собственностью ООО «Энтер» и третьих лиц. Информация, опубликованная на сайте, не предоставляет никаких лицензионных прав на использование любых товарных знаков без получения предварительного письменного согласия владельца.</p>

                        <div class="orderOferta_tl">Ссылки</div>
                        <p>На данном сайте Вы можете найти ссылки на другие (внешние) сайты, не принадлежащие ООО «Энтер». Информация с этих сайтов не является продолжением или дополнением материалов ООО «Энтер».</p>

                        <div class="orderOferta_tl">Реквизиты</div>
                        <p>Наименование: ООО «Энтер» Место нахождения: 115419, г. Москва, улица Орджоникидзе, дом 11, строение 10. Государственный регистрационный номер записи о создании юридического лица: 1117746043381 ИНН: 7710881860 КПП: 772501001 Наименование банка: Связной Банк ЗАО г. Москва БИК: 044583139 Расчетный счет: 40702810400000002751</p>
                    </div>
                </div>
            </div>
        </div>

        <?= $helper->render('order-v3-new/__delivery-analytics', ['orderDelivery' => $orderDelivery]) ?>
    <? else: ?>
        <div class="order-message__last-order">
            <span class="order-message__last-order-message">В заказе не осталось товаров</span>
            <a class="order-message__last-order-btn" href="<?= $helper->escape(\App::router()->generate('homepage')) ?>">Вернуться на главную</a>
        </div>
    <? endif ?>
<? };