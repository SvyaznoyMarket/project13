<?php
/**
 * @var $page               \View\User\OrdersPage
 * @var $helper             \Helper\TemplateHelper
 * @var $user               \Session\User
 * @var $orderCount         int
 * @var $orders             \Model\User\Order\Entity[]
 * @var $orders_by_year     array
 * @var $current_orders     \Model\User\Order\Entity[]
 * @var $products_by_id     \Model\Product\Entity[]
 */
?>

<?
$showStatus = \App::user()->getEntity() && in_array(\App::user()->getEntity()->getId(), [
    "1019768",
    "104406",
    "1036742",
    "764984",
    "395421",
    "180860",
    "197474",
    "54",
    "325127",
    "641265",
    "11446",
    "11447",
]);

?>

<div class="personal">

    <?= $page->render('user/_menu', ['page' => $page]) ?>


    <!-- страница заказов -->

        <div class="personal__orders current">
            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status online">Оплатить онлайн</span>
                </div>
                <div class="personal-order__cell">
                        <span class="personal-order__more">Еще
                            <div class="personal-order__cancel">Отменить заказ</div>
                        </span>

                </div>
            </div>
            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status payed">Оплачен</span>
                </div>
                <div class="personal-order__cell">
                        <span class="personal-order__more">Еще
                            <div class="personal-order__cancel">Отменить заказ</div>
                        </span>

                </div>
            </div>
        </div>
        <div class="personal__orders">
            <div class="personal-order__block expanded">
                    <span class="personal-order__year-container">
                       <span class="personal-order__year"> 2015</span>
                    </span><span class="personal-order__year-total">5 заказов</span>

                <div class="personal-order__item">
                    <div class="personal-order__cell">
                        <span class="personal-order__num">COXF-767608</span>
                        <span class="personal-order__date">01.01.2015</span>
                    </div>
                    <div class="personal-order__cell">
                        <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                        <span class="personal-order__info warning">Требуется предоплата</span>
                    </div>
                    <div class="personal-order__cell">
                        <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                        <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                    </div>
                    <div class="personal-order__cell personal-order__price">
                        550 <span class="rubl">p</span>
                    </div>
                    <div class="personal-order__cell">
                        <span class="personal-order__status">Подтвержден</span>
                        <span class="personal-order__pay-status online">Оплатить онлайн</span>
                    </div>
                    <div class="personal-order__cell">
                            <span class="personal-order__more">Еще
                                <div class="personal-order__cancel">Повторить заказ</div>
                            </span>

                    </div>
                </div>
            </div>
            <div class="personal-order__block">
                    <span class="personal-order__year-container">
                       <span class="personal-order__year"> 2014</span>
                    </span><span class="personal-order__year-total">1 заказ</span>

                <div class="personal-order__item">
                    <div class="personal-order__cell">
                        <span class="personal-order__num">COXF-767608</span>
                        <span class="personal-order__date">01.01.2015</span>
                    </div>
                    <div class="personal-order__cell">
                        <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                        <span class="personal-order__info warning">Требуется предоплата</span>
                    </div>
                    <div class="personal-order__cell">
                        <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                        <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                    </div>
                    <div class="personal-order__cell personal-order__price">
                        550 <span class="rubl">p</span>
                    </div>
                    <div class="personal-order__cell">
                        <span class="personal-order__status">Подтвержден</span>
                        <span class="personal-order__pay-status online">Оплатить онлайн</span>
                    </div>
                    <div class="personal-order__cell">
                            <span class="personal-order__more">Еще
                                <div class="personal-order__cancel">Повторить заказ</div>
                            </span>

                    </div>
                </div>
            </div>
        </div>

    <!-- END страница заказов -->


    <!-- Ниже старая верстка -->
    <? if ((bool)$current_orders) : ?>

        <div class="personalTitle">Текущие заказы <span class="personalTitle_count"><?= count($current_orders) ?></span></div>

        <div class="personalTable personalTable-border personalTable-bg">
            <div class="personalTable_row personalTable_row-head">
                <div class="personalTable_cell personalTable_cell-w90">№ заказа</div>

                <div class="personalTable_cell personalTable_cell-w212">Состав</div>

                <div class="personalTable_cell personalTable_cell-w115 ta-c">Сумма</div>

                <div class="personalTable_cell personalTable_cell-w175">Получение</div>

                <? if ($showStatus): ?>
                    <div class="personalTable_cell">Статус</div>
                <? endif ?>
            </div>

            <? foreach ($current_orders as $order) : ?>

            <div class="personalTable_row">
                <div class="personalTable_cell ta-c">
                    <a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>"><?= str_replace('-','-',$order->getNumberErp()) ?></a>
                    <span class="s dblock"><?= strftime('%e %b %y', $order->getCreatedAt()->getTimestamp())?></span>
                </div>

                <div class="personalTable_cell personalTable_cell-text">
                    <ul class="orderItem">
                        <? foreach ($order->getProduct() as $i => $product) : ?>
                            <? $productEntity = isset($products_by_id[$product->getId()]) ? $products_by_id[$product->getId()] : null ?>
                            <? if (!$productEntity) continue ?>
                            <? if ($i != 2) : ?>
                                <li><a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>"><?= strlen($productEntity->getName()) > 25 ? mb_substr($productEntity->getName(), 0, 25).'...' : $productEntity->getName() ?></a> <?= $product->getQuantity()?> шт.</li>
                            <? else : ?>
                                <li><a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>">и ещё <?= $helper->numberChoiceWithCount(count($order->getProduct()) - 2, ['товар', 'товара', 'товаров']) ?></a></li>
                                <? break ?>
                            <? endif ?>
                        <? endforeach ?>
                    </ul>
                </div>

                <div class="personalTable_cell ta-r">
                    <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>
                    <!--<strong class="s dblock"><?//= $order->getPaymentStatusText($order->getPaymentStatusId()) ?></strong>-->
                </div>

                <div class="personalTable_cell">
                    <?= $order->getDeliveryTypeName() ?>
                    <span class="s dblock"><?= $order->getDeliveryDate() ?></span>
                </div>

                <? if ($showStatus): ?>
                    <div class="personalTable_cell"><?= $order->getLastLifecycleStatus() ?></div>
                <? endif ?>

                <div class="personalTable_cell"></div>
            </div>

            <? endforeach ?>

        </div>

    <? endif ?>

    <? if ((bool)$orders_by_year) : ?>

    <div class="personalTitle">История <span class="personalTitle_count"><?= count($orders) - count($current_orders) ?></span></div>

    <div class="personalTableWrap">
        <!-- таблица истории заказов -->
        <div class="personalTable personalTable-border personalTable-bg">

            <div class="personalTable_row personalTable_row-head">
                <div class="personalTable_cell personalTable_cell-w90">№ заказа</div>

                <div class="personalTable_cell personalTable_cell-w212">Состав</div>

                <div class="personalTable_cell personalTable_cell-w115 ta-c">Сумма</div>

                <div class="personalTable_cell personalTable_cell-w175">Получение</div>

                <div class="personalTable_cell">Статус</div>

                <div class="personalTable_cell"></div>
            </div>

    <? foreach ($orders_by_year as $year => $orders_in_year) : ?>

            <!-- кликаем по всему диву, что бы раскрыть блок с заказами -->
            <div class="personalTable_rowgroup personalTable_rowgroup-head">
                <div class="personalTable_cell">
                    <div class="personalTable_cell_rowspan" data-value="<?= $year ?>">
                        <strong class="textCorner textCorner-open <?= $orders_in_year == reset($orders_by_year) ? '' : 'mOldYear' ?>"><?= $year ?></strong> <span class="colorGrey"><?= count($orders_in_year). ' '. $helper->numberChoice(count($orders_in_year), ['заказ','заказа','заказов']) ?></span>
                    </div>
                </div>
            </div>
            <!--/ кликаем по всему диву, что бы раскрыть блок с заказами -->

            <div class="personalTable_rowgroup <?= $orders_in_year == reset($orders_by_year) ? '' : 'mOldYear' ?> personalTable_rowgroup_<?= $year ?>">

                <? foreach ($orders_in_year as $key => $order) : ?>

                    <? /** @var $order \Model\User\Order\Entity */?>

                    <div class="personalTable_row <?= $order->getStatusId() == 100 ? 'colorGrey' : '' ?>">
                        <div class="personalTable_cell ta-c">
                            <a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>"><?= str_replace('-','-',$order->getNumberErp()) ?></a>
                            <span class="s dblock"><?= strftime('%e %b %y', $order->getCreatedAt()->getTimestamp())?></span>
                        </div>

                        <div class="personalTable_cell personalTable_cell-text">
                            <ul class="orderItem">
                                <? foreach ($order->getProduct() as $i => $product) : ?>
                                    <? $productEntity = isset($products_by_id[$product->getId()]) ? $products_by_id[$product->getId()] : null ?>
                                    <? if (!$productEntity) continue ?>
                                    <? if ($i != 2) : ?>
                                        <li><a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>"><?= strlen($productEntity->getName()) > 25 ? mb_substr($productEntity->getName(), 0, 25).'...' : $productEntity->getName() ?> <?= $product->getQuantity()?> шт.</a></li>
                                    <? else : ?>
                                        <li><a href="<?= $page->url('user.order', ['orderId' => $order->getId() ]) ?>">и ещё <?= $helper->numberChoiceWithCount(count($order->getProduct()) - 2, ['товар', 'товара', 'товаров']) ?></a></li>
                                        <? break ?>
                                    <? endif ?>
                                <? endforeach ?>
                            </ul>
                        </div>

                        <div class="personalTable_cell ta-r">
                            <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span><br/>
                            <span class="textStatus"><?= $order->getPaymentStatusText($order->getPaymentStatusId()) ?></span>
                        </div>

                        <div class="personalTable_cell"><?= $order->getDeliveryTypeName() ?></div>

                        <div class="personalTable_cell"><?= $order->getStatusText($order->getStatusId()) ?></div>

                        <div class="personalTable_cell personalTable_cell-last ta-r">
                            <? if (!$order->isSlot()): ?>
                                <a href="<?= \App::router()->generate('cart.product.setList', ['products' => array_map(function(\Model\Order\Product\Entity $product) { return ['id' => $product->getId(), 'quantity' => '+' . $product->getQuantity(), 'up' => '1']; }, $order->getProduct())]) ?>" class="jsBuyButton"><button class="tableBtn btnLightGrey">Добавить в корзину</button></a>
                            <? endif ?>
                        </div>
                    </div>

                <? endforeach ?>

            </div>

        <!--/ таблица истории заказов -->

    <? endforeach ?>

        </div>

    </div>

    <? else: ?>
        <!-- TODO 0 заказов -->
    <? endif ?>

</div>
