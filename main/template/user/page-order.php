<?php
/**
 * @var $page                   \View\User\OrderPage
 * @var $user                   \Session\User
 * @var $order                  \Model\User\Order\Entity
 * @var $products               \Model\Product\Entity[]
 * @var $delivery               \Model\DeliveryType\Entity
 * @var $current_orders_count   int
 * @var $shop                   \Model\Shop\Entity|null
 */
?>

<div class="personalPage">

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalTitle">
    <a class="td-underl" href="<?= $page->url('user.orders') ?>">Текущие заказы</a> <span class="personalTitle_count"><?= $current_orders_count ?></span>
</div>

<div class="personalPage_left">
    <div class="personalPage_head clearfix">
        <h2 class="personalPage_head_left">Заказ <?= $order->getNumberErp() ?> от <?= $order->getCreatedAt()->format('d.m.y')?></h2>
 <!--       <div class="personalPage_head_right">
            Получить номер заказа:
            <button class="personalPage_head_btn btnLightGrey va-m">SMS</button>
            <button class="personalPage_head_btn btnLightGrey va-m">e-mail</button>
        </div>-->
    </div>

    <? if (false && (bool)$order->getLifecycle()) : ?>
    <!-- статусы заказа -->
    <ul class="personalControl personalControl-arrow">

        <? foreach ($order->getLifecycle() as $key => $cycle) : ?>

            <?  $classList = '';

                // стрелка для всех, кроме последнего
                if ($key == count($order->getLifecycle()) - 1) {
                    $classList .= ' personalControl_item-last';
                } else {
                    $classList .= ' personalControl_item-arrow';
                }

                // галочка для выполненных
                if ($cycle->getCompleted()) {
                    if (isset($order->getLifecycle()[$key + 1]) && $order->getLifecycle()[$key + 1]->getCompleted()) {
                        $classList .= ' personalControl_item-past';
                    } else {
                        $classList .= ' personalControl_item-active';
                    }
                }

                // последний выполнен
                if ($key == count($order->getLifecycle()) - 1 && $cycle->getCompleted()) $classList .= ' personalControl_item-active'
            ?>

            <li class="personalControl_item <?= $classList ?>">
                <?= $cycle->getTitle() ?>
            </li>

        <? endforeach; ?>

    </ul>
    <!--/ статусы заказа -->
    <? endif ?>

    <!-- информация о заказе -->
    <div class="personalInfo">
        <p><strong><?= $delivery ? $delivery->getShortName() : '' ?></strong> <span class="colorBlack"><?= $order->getDeliveryDate() ?></span></p>

        <? if ($delivery && in_array($delivery->getToken(), ['now', 'self']) && $shop) : ?>

        <div class="personalTable">
            <div class="personalTable_row">
                <div class="personalTable_cell w90">из магазина</div>

                <div class="personalTable_cell">
                    <? if ((bool)$shop->getSubway()) : ?>
                    <span class="decorColor-bullet" style="color: <?= $shop->getSubway()[0]->getLine()->getColor() ?>"><span class="colorBlack"><?= $shop->getSubway()[0]->getName() ?></span></span>
                    <? endif; ?>
                    <div class="shopsInfo">

                        <span class="colorBrightGrey"><?= $shop->getAddress() ?></span>

                        <div class="shopsInfo_time">
                            <span class="colorBrightGrey">Режим работы:</span> с <?= $shop->getWorkingTimeToday()['start_time'] /* TODO день работы на день вывоза */?> до <?= $shop->getWorkingTimeToday()['end_time'] /* TODO день работы на день вывоза */?> &nbsp;
                            <span class="colorBrightGrey">Оплата при получении: </span>
                            <img src="/styles/personal-page/img/nal.png" alt=""/>
                            <img src="/styles/personal-page/img/card.png" alt=""/>
                        </div>
                    </div>
                </div>

                <div class="personalTable_cell va-m">
                    <a href="<?= $page->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>" title="">Как добраться?</a>
                </div>
            </div>
        </div>

        <? endif; ?>
    </div>
    <!--/ информация о заказе -->

    <div class="personalTable personalTable-border nomargin">

        <? foreach ($products as $product) : ?>

        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-mini">
                <img class="imgProd" src="<?= $product->getImageUrl(0) ?>" alt="" />
            </div>

            <div class="personalTable_cell personalTable_cell-text">
                <? $categories = $product->getCategory(); $category = end($categories) ?>
                <?= $category ? $category->getName() : '' ; ?><br/>
                <?= $product->getName() ?>
            </div>

            <div class="personalTable_cell l colorGrey ta-r">
                <?= $page->helper->formatPrice($order->getProductById($product->getId())->getPrice()) ?> <span class="rubl">p</span><br/>
            </div>

            <div class="personalTable_cell l colorGrey ta-r"><?= $order->getProductById($product->getId())->getQuantity() ?> шт.</div>

            <div class="personalTable_cell personalTable_cell-l ta-r">
                <?=  $page->helper->formatPrice($order->getProductById($product->getId())->getSum()) ?> <span class="rubl">p</span><br/>
            </div>
        </div>

        <? endforeach; ?>

    </div>

    <div class="personalTable personalTable-border">

        <? if ($order->getCertificateNumber()) : ?>
        <div class="personalTable_caption">
            Скидки
        </div>

        <? if ($order->getCertificateNumber()) : ?>

            <div class="personalTable_row">
                <div class="personalTable_cell personalTable_cell-mini">
                    <img class="imgProd" src="/styles/personal-page/img/enterLogo.png" alt="" />
                </div>

                <div class="personalTable_cell">
                    Подарочный сертификат <?= $page->helper->formatPrice($order->getCertificatePrice()) ?> руб
                </div>

                <div class="personalTable_cell personalTable_cell-right l colorRed ta-r">
                    - <?= $page->helper->formatPrice($order->getCertificatePrice()) ?> <span class="rubl">p</span><br/>
                </div>
            </div>

        <? endif; ?>

        <? endif; ?>

        <div class="personalTable_rowgroup">

            <? if ( (bool)$order->getDelivery() ) : ?>
            <div class="personalTable_row personalTable_row-total ta-r">
                <div class="personalTable_cell">
                </div>

                <div class="personalTable_cell personalTable_cell-long">
                    <?= $delivery ? $delivery->getShortName() : '' ?>:
                </div>

                <div class="personalTable_cell">
                    <? if ($order->getDelivery()->getPrice()): ?>
                        <?= $order->getDelivery()->getPrice() ?> <span class="rubl">p</span>
                    <? else: ?>
                        Бесплатно
                    <? endif ?>
                </div>
            </div>
            <? endif; ?>

            <div class="personalTable_row personalTable_row-total ta-r">
                <div class="personalTable_cell">
                </div>

                <div class="personalTable_cell personalTable_cell-long">
                    Итого:
                </div>

                <div class="personalTable_cell l">
                    <? if ($order->getPaySum() != $order->getSum() ) : ?>
                        <span class="colorGrey td-lineth"><?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span><br/>
                    <? endif ?>
                    <?= $page->helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- сайдбар онлайн оплаты -->
<aside class="personalPage_right">

    <? if ($order->getPaymentStatusId() == 2) : // Оплачено ?>

        <ul class="paySumm">
            <li>Сумма заказа: <span class="paySumm_val"><?= $page->helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></span></li>
        </ul>

        <div class="payComplete"></div>

    <? else : // Не оплачено ?>

        <ul class="paySumm">
            <li>Сумма заказа: <span class="paySumm_val"><?= $page->helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></span></li>
            <!--<li>Оплачено: <span class="paySumm_val"> <span class="rubl">p</span></span></li>-->
            <li>К оплате: <span class="paySumm_val"><?= $page->helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></span></li>
        </ul>

        <? if ($order->getPaymentStatusId() == 3 || $order->getPaymentStatusId() == 6) : // Заявка на кредит? ?>

            <!--<menu class="payCommands">
                <ul class="payCommandsList payCommandsList-mark">
                    <li class="payCommandsList_item">
                        <div class="titlePay">Кредит</div>

                        <button class="btnPay btnLightGrey">Заполнить заявку</button>

                        <span class="descPay">
                            <img src="/styles/personal-page/img/cards/renesans.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/tinkoff.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/otpBank.png" alt="" class="descPay_img" />
                        </span>
                    </li>
                </ul>
            </menu>-->

        <? else : ?>

            <!--<menu class="payCommands">
                <ul class="payCommandsList">
                    <li class="payCommandsList_item mb20">
                        <button class="btnPay btnLightGrey">Оплатить баллами</button>

                        <span class="descPay">
                            <img src="/styles/personal-page/img/cards/sclub.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/sber.png" alt="" class="descPay_img" />
                        </span>
                    </li>

                    <li class="payCommandsList_item">
                        <button class="btnPay btnLightGrey">Оплатить онлайн</button>

                        <span class="descPay">
                            <img src="/styles/personal-page/img/cards/MasterCard.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/Visa.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/Maestro.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/paypal.png" alt="" class="descPay_img" />
                            <img src="/styles/personal-page/img/cards/psb.png" alt="" class="descPay_img" />
                        </span>
                    </li>
                </ul>
            </menu>-->

        <? endif; ?>

    <? endif; ?>


</aside>
<!--/ сайдбар онлайн оплаты -->
</div>