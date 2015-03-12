<?php
/**
 * @var $page                    \View\Order\CreatePage
 * @var $user                    \Session\User
 * @var $form                    \View\Order\Form
 * @var $subwayData              array
 * @var $creditData              array
 * @var $bankData                array
 * @var $banks                   \Model\CreditBank\Entity[]
 * @var $bank                    \Model\CreditBank\Entity
 * @var $paymentMethods          \Model\PaymentMethod\Entity[]
 * @var $selectedPaymentMethodId int
 * @var $backLink                string
 */
?>

<?
$region = $user->getRegion();
$isCorporative = $user->getEntity() && $user->getEntity()->getIsCorporative();

$jsValidator = array('order[recipient_first_name]' => 'Заполните поле', 'order[recipient_phonenumbers]' => 'Заполните поле', 'order[address_street]' => 'Укажите адрес', 'order[address_building]' => 'Укажите адрес', 'order[payment_method_id]' => 'Выберите способ оплаты', 'order[agreed]' => 'Необходимо согласие', 'order[recipient_email]' => 'Некорректный e-mail',
);
if ($form->hasSubway()) $jsValidator['order[address_metro]'] = 'Укажите ближайшее метро';
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i>Оформление заказа</i><br>
    <span>Финальный шаг :)</span>
</div>
<!-- /Header -->

<? if (\App::config()->adFox['enabled']): ?>
<div id="adfox920" class="adfoxWrapper"></div>
<? endif ?>

<input disabled="disabled" id="order-validator" type="hidden" data-value="<?= $page->json($jsValidator) ?>"/>

<script id="mapInfoBlock" type="text/html">
    <div class="bMapShops__ePopupRel">
        <h3><%=name%></h3>
        <span>Работает </span>
        <span><%=regime%></span>
        <br/>
        <span class="shopnum" style="display: none;"><%=id%></span>
        <a class="bGrayButton shopchoose" href="">Забрать из этого магазина</a>
    </div>
</script>

<div id="map-info_window-container" style="display:none"></div>

<div class="bBuyingLine"><a class="bBackCart" href="<?= $backLink ?>">&lt; Вернуться к покупкам</a></div>


<input id="order-delivery_map-data" type="hidden" data-value="<?= $page->json($deliveryMap) ?>"/>
<?= $page->render('order/_formTemplate') ?>

<form id="order-form" style="display:none" data-validator="#order-validator" method="post" action="<?= $page->url('order') ?>">
    <div class='bBuyingInfo'>
        <h2>Информация о счастливом получателе</h2>

        <div id="user-block">
            <? if ($user->getEntity()): ?>
                Привет, <a href="<?= $page->url(\App::config()->user['defaultRoute']) ?>"><?= $user->getEntity()->getName() ?></a>
            <? else: ?>
                Уже покупали у нас?
                <strong><a class="auth-link underline" data-update-url="<?//php echo url_for('order_getUser') ?>" href="<?= $page->url('user.login') ?>">Авторизуйтесь</a></strong>
                и вы сможете использовать ранее введенные данные
            <? endif ?>
        </div>

        <div class='bBuyingLine mOrderFields'>
            <label class="bBuyingLine__eLeft">Имя получателя*</label>

            <div class="bBuyingLine__eRight">
                <input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="<?= $form->getFirstName() ?>"/>
            </div>

            <label class="bBuyingLine__eLeft">Фамилия получателя</label>

            <div class="bBuyingLine__eRight">
                <input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="<?= $form->getLastName() ?>"/>
            </div>

            <label class="bBuyingLine__eLeft">E-mail</label>

            <div class="bBuyingLine__eRight">
                <? $email = $form->getEmail() ?>
                <input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong" name="order[recipient_email]" value="<?= $email ?>" />
                
                <label class="bSubscibe checked" style="visibility:<?= empty($email) ? 'hidden' : 'visible' ?>;">
                    <b></b> Хочу знать об интересных<br />предложениях
                    <input type="checkbox" name="order[subscribe]" value="1" autocomplete="off" class="subscibe" checked="checked" />
                </label>
            </div>

            <label class="bBuyingLine__eLeft">Телефон для связи*</label>

            <div class="bBuyingLine__eRight">
                <div class="phonePH">
                    <span class="placeholder">+7</span> 
                    <input id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="<?= $form->getMobilePhone() ?>"/>
                </div>
            </div>

            <label class="bBuyingLine__eLeft">Адрес доставки*</label>

            <div class="bBuyingLine__eRight" style="width: 640px;">
                <div>
                    <p></p>
                    <strong><?= $region->getName() ?></strong> ( <a class="jsChangeRegion" data-region-id="<?= $user->getRegion()->getId() ?>" data-url="<?= $page->url('region.init') ?>" href="<?= $page->url('region.change', array('regionId' => $region->getId())) ?>" style="font-weight: normal">изменить</a> )
                </div>

                <? if ($form->hasSubway()): ?>
                    <div class="bInputAddress ui-css">
                        <span class="placeholder">Метро</span>
                        <input class="bInputAddress__eField bBuyingLine__eText mInputLong ui-autocomplete-input" id="order_address_metro" type="text" title="Метро" name="order[address_metro]" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <div id="metrostations" data-name="<?= $page->json($subwayData) ?>"></div>
                        <input type="hidden" id="order_subway_id" name="order[subway_id]" value="<?= $form->getSubwayId() ?>" />
                    </div>
                <? endif ?>

                <div class="bInputAddress">
                    <span class="placeholder">Улица</span>
                    <input type="text" class="bBuyingLine__eText mInputLong mInputStreet" title="Улица" name="order[address_street]" value="<?= $form->getAddressStreet() ?>" />
                </div>

                <div class="bInputAddress placeholder-input">
                    <span class="placeholder">Дом</span>
                    <input type="text" class="bBuyingLine__eText mInputShort mInputBuild" title="Дом" name="order[address_building]" value="<?= $form->getAddressBuilding() ?>" />
                </div>

                <div class="bInputAddress placeholder-input">
                    <span class="placeholder">Корпус</span>
                    <input type="text" class="bBuyingLine__eText mInputShort mInputNumber" title="Корпус" name="order[address_number]" value="<?= $form->getAddressNumber() ?>" />
                </div>

                <div class="bInputAddress placeholder-input">
                    <span class="placeholder">Квартира</span>
                    <input type="text" class="bBuyingLine__eText mInputShort mInputApartament" title="Квартира" name="order[address_apartment]" value="<?= $form->getAddressApartment() ?>" />
                </div>

                <div class="bInputAddress placeholder-input">
                    <span class="placeholder">Этаж</span>
                    <input type="text" class="bBuyingLine__eText mInputShort mInputFloor" title="Этаж" name="order[address_floor]" value="<?= $form->getAddressFloor() ?>" />
                </div>
            </div>

            <label class="bBuyingLine__eLeft">Пожелания и дополнения</label>

            <div class="bBuyingLine__eRight">
                <textarea id="order_extra" class="bBuyingLine__eTextarea" name="order[extra]" cols="30" rows="4"></textarea>
            </div>

        </div>

        <div class='bBuyingLine<?= $isCorporative ? ' hidden' : '' ?> mOrderFields'>
            <div class="bBuyingLine__eLeft">Если у вас есть карта &laquo;Связной-Клуб&raquo;, вы можете указать ее номер</div>
            
            <div class="bBuyingLine__eRight bSClub">
                <input type="text" id="order_sclub_card_number" class="bBuyingLine__eText mInputShort mb15" name="order[bonus_card_number]" />
                <div class="mILong">Чтобы получить 1% от суммы заказа<br/>плюсами на карту, введите ее номер,<br/>расположенный на обороте под штрихкодом</div>
            </div>
        </div>

        <h2 class="bOrderView__eTitle">Оплата</h2>

        <div class='bBuyingLine mPayMethods' data-max-sum-online="<?= \App::config()->order['maxSumOnline'] ?>">
            <div class="bBuyingLine__eLeft"></div>
            <div class="bBuyingLine__eRight" id="payTypes">

                <?
                    $byPayOnReceipt = [
                        \Model\PaymentMethod\Entity::TYPE_ON_RECEIPT => [],
                        \Model\PaymentMethod\Entity::TYPE_NOW => [],
                    ];
                    foreach($paymentMethods as $paymentMethod) { 
                        $payOnReceipt = $paymentMethod->getPayOnReceipt();
                        $byPayOnReceipt[$payOnReceipt][] = $paymentMethod;
                    }
                    foreach ($byPayOnReceipt as $payOnReceipt => $paymentMethods) { ?>

                        <? if($payOnReceipt == \Model\PaymentMethod\Entity::TYPE_ON_RECEIPT) {
                            $payOnReceiptHeader = 'При получении заказа';
                        } elseif($payOnReceipt == \Model\PaymentMethod\Entity::TYPE_NOW) {
                            $payOnReceiptHeader = 'Прямо сейчас';
                        } else {
                            $payOnReceiptHeader = null;
                        } ?>

                        <h2><?= $payOnReceiptHeader ?></h2>
                        <?= $page->render('order/payment/_methods', [
                            'bankData' => $bankData,
                            'creditData' => $creditData,
                            'banks' => $banks,
                            'form' => $form,
                            'selectedPaymentMethodId' => $selectedPaymentMethodId,
                            'payOnReceipt' => $payOnReceipt,
                            'paymentMethods' => $paymentMethods,
                        ]) ?>
                    <? }
                ?>
            </div>
        </div>

        <div class='line'></div>

        <dl class='bBuyingLine'>
            <dt></dt>
            <dd>

                <div>
                    <label class='mLabelLong' for="order_agreed">
                        <b></b>
                        <? if ($isCorporative): ?>
                            <h4>Я ознакомлен и согласен с &laquo;<a href="/corp-terms" target="_blank">Условиями продажи</a>&raquo; и &laquo;<a href="/legal" target="_blank">Правовой информацией</a>&raquo;*</h4>
                        <? else: ?>
                            <h4>Я ознакомлен и согласен с &laquo;<a href="/terms" target="_blank">Условиями продажи</a>&raquo; и &laquo;<a href="/legal" target="_blank">Правовой информацией</a>&raquo;*</h4>
                        <? endif ?>
                        <input type="checkbox" id="order_agreed" class="bBuyingLine__eRadio" name="order[agreed]" />
                    </label>
                </div>
            </dd>
        </dl>
        <dl class='bBuyingLine'>
            <dt></dt>
            <dd>
                <div>
                    <p></p>
                    <i class='mILong'>* Поля обязательные для заполнения</i>
                </div>
            </dd>
        </dl>

    </div>


</form>

<dl class='bBuyingLine mConfirm'>

    <dt>&nbsp;</dt>
    <dd>
        <div><a id="order-submit" class='bBigOrangeButton disable' href="#">Завершить оформление</a></div>
    </dd>
</dl>

<div id="order-shop-popup" class="hidden"></div>

<div id="order-loader" class='bOrderPreloader hf'>
    <span>Формирую заказ...</span><img src='/images/bPreloader.gif'/>
</div>


<?php if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('order/_kissmetrics-create') ?>
    <?= $page->tryRender('order/partner-counter/_cityads-create') ?>
<?php endif ?>
