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
$showStatus = \App::user()->getEntity() && in_array(\App::user()->getEntity()->getId(), ['1019768', '104406', '1036742', '764984', '395421', '180860', '197474', '54', '325127', '641265', '11446', '11447']);
?>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

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

</div>
