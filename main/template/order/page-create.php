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

$jsValidator = array('order[recipient_first_name]' => 'Заполните поле', 'order[recipient_last_name]' => 'Заполните поле', 'order[recipient_phonenumbers]' => 'Заполните поле', 'order[address_street]' => 'Укажите адрес', 'order[address_building]' => 'Укажите адрес', 'order[payment_method_id]' => 'Выберите способ оплаты', 'order[agreed]' => 'Необходимо согласие',);
if ($form->hasSubway()) $jsValidator['order[address_metro]'] = 'Укажите ближайшее метро';
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i>Оформление заказа</i><br>
    <span>Финальный шаг :)</span>
</div>
<!-- /Header -->

<div id="adfox920" class="adfoxWrapper"></div>

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

<div class="pb15"><a class="motton font14" href="<?= $backLink ?>" style="font-weight: bold">&lt; Вернуться к покупкам</a></div>


<input id="order-delivery_map-data" type="hidden" data-value='<?= $page->json($deliveryMap) ?>'/>
<?= $page->render('order/_formTemplate') ?>

<form id="order-form" style="display:none" data-validator="#order-validator" method="post" action="<?= $page->url('order.create') ?>">
    <div class='bBuyingInfo'>
        <h2>Информация о счастливом получателе</h2>

        <div id="user-block">
            <? if ($user->getEntity()): ?>
                Привет, <a href="<?= $page->url('user') ?>"><?= $user->getEntity()->getName() ?></a>
            <? else: ?>
                Уже покупали у нас?
                <strong><a class="auth-link underline" data-update-url="<?//php echo url_for('order_getUser') ?>" href="<?= $page->url('user.login') ?>">Авторизуйтесь</a></strong>
                и вы сможете использовать ранее введенные данные
            <? endif ?>
        </div>

        <dl class='bBuyingLine'>

            <dt>Имя получателя*</dt>
            <dd>
                <div>
                    <p></p>
                    <input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="<?= $form->getFirstName() ?>"/>
                </div>
            </dd>
        </dl>

        <dl class='bBuyingLine'>

            <dt>Фамилия получателя*</dt>
            <dd>
                <div>
                    <p></p>
                    <input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="<?= $form->getLastName() ?>"/>
                </div>
            </dd>
        </dl>

        <dl class='bBuyingLine'>
            <dt>Телефон для связи*</dt>
            <dd>
                <div class="phonePH">
                    <span class="placeholder">8</span>
                    <input type="text" id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" maxlength="10" value="<?= $form->getMobilePhone() ?>"/>
                </div>

                <div>
                    <label for="order_is_receive_sms">
                        <b></b> <h5>Я хочу получать СМС уведомления об изменении статуса заказа</h5>
                        <input type="checkbox" id="order_is_receive_sms" class="bBuyingLine__eRadio" name="order[is_receive_sms]">
                    </label>
                </div>
            </dd>
        </dl>


        <dl class='bBuyingLine' id="addressField">
            <dt>Адрес доставки*</dt>
            <dd>
                <div>
                    <p></p>
                    <strong><?= $region->getName() ?></strong> ( <a id="jsregion" data-url="<?= $page->url('region.init') ?>" href="<?= $page->url('region.change', array('regionId' => $region->getId())) ?>" style="font-weight: normal">изменить</a> )
                </div>

                <? if ($form->hasSubway()): ?>
                    <div class="ui-css">
                        <span class="placeholder">Метро</span>
                        <input type="text" id="order_address_metro" title="Метро" class="placeholder-input bBuyingLine__eText mInputLong ui-autocomplete-input" name="order[address_metro]" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" />
                        <div id="metrostations" data-name="<?= $page->json($subwayData) ?>"></div>
                        <input type="hidden" id="order_subway_id" name="order[subway_id]" value="<?= $form->getSubwayId() ?>" />
                    </div>
                <? endif ?>

                <div class="street">
                    <span class="placeholder">Улица</span>
                    <input type="text" id="order_address_street" title="Улица" class="placeholder-input bBuyingLine__eText mInputLong" name="order[address_street]" value="<?= $form->getAddressStreet() ?>" />
                </div>

                <div class="number">
                    <span class="placeholder">Дом</span>
                    <input type="text" id="order_address_building" title="Дом" class="placeholder-input bBuyingLine__eText mInputShort" name="order[address_building]" value="<?= $form->getAddressBuilding() ?>" />
                </div>

                <div class="building">
                    <span class="placeholder">Корпус</span>
                    <input type="text" id="order_address_number" title="Корпус" class="placeholder-input bBuyingLine__eText mInputShort" name="order[address_number]" value="<?= $form->getAddressNumber() ?>" />
                </div>
                <div class="apartament">
                    <span class="placeholder">Квартира</span>
                    <input type="text" id="order_address_apartment" title="Квартира" class="placeholder-input bBuyingLine__eText mInputShort" name="order[address_apartment]" value="<?= $form->getAddressApartment() ?>" />
                </div>
                <div class="floor">
                    <span class="placeholder">Этаж</span>
                    <input type="text" id="order_address_floor" title="Этаж" class="placeholder-input bBuyingLine__eText mInputShort" name="order[address_floor]" value="<?= $form->getAddressFloor() ?>" />
                </div>
            </dd>
        </dl>

        <dl class='bBuyingLine'>
            <dt>Пожелания и дополнения</dt>

            <dd>
                <div>
                    <p></p>
                    <textarea id="order_extra" class="bBuyingLine__eTextarea" name="order[extra]" cols="30" rows="4"></textarea>
                </div>
            </dd>
        </dl>

        <dl class='bBuyingLine<?= $isCorporative ? ' hidden' : '' ?>'>
            <dt>Если у вас есть карта<br/>&laquo;Связной-Клуб&raquo;, вы можете указать ее номер</dt>
            <dd class="bSClub">
                <div class="bSClub__eWrap pb25">
                    <input type="text" id="order_sclub_card_number" class="bBuyingLine__eText mInputShort mb15" name="order[sclub_card_number]" />
                    <i class="mILong">Чтобы получить 1% от суммы заказа<br/>баллами на карту, введите ее номер,<br/>расположенный
                        на обороте под штрихкодом</i>
                </div>
                <!--<label><b></b> <h5>Сохранить мои данные для следующих покупок</h5> <input class='bBuyingLine__eRadio' name='r1' type='radio'></label>-->
            </dd>
        </dl>

        <h2>Об оплате</h2>

        <dl class='bBuyingLine'>
            <dt>Выберите удобный для вас способ*</dt>

            <dd id="payTypes">
                <? foreach ($paymentMethods as $paymentMethod): ?>
                <?
                    // TODO: удалить в v.33
                    if (7 == $paymentMethod->getId() || 3 == $paymentMethod->getId()) continue; // Онлайн через терминал
                ?>
                <div id="payment_method_<?= $paymentMethod->getId() ?>-field">
                    <p></p>
                    <label class="<? if ($paymentMethod->getId() == $selectedPaymentMethodId) echo 'mChecked' ?>" for="order_payment_method_id_<?= $paymentMethod->getId() ?>">
                        <b></b> <?= $paymentMethod->getName() ?>
                        <input id="order_payment_method_id_<?= $paymentMethod->getId() ?>" class='bBuyingLine__eRadio' name="order[payment_method_id]" type='radio' value="<?= $paymentMethod->getId() ?>" <? if ($paymentMethod->getId() == $selectedPaymentMethodId) echo 'checked="checked"' ?> />
                    </label>
                    <i>
                        <div><?= $paymentMethod->getDescription() // ?></div>
                        <? if ($paymentMethod->getIsCredit() && ($bank = reset($banks))) {  ?>
                        <div class="innerType" id="creditInfo" <? if ($paymentMethod->getId() != $selectedPaymentMethodId) echo 'style="display:none"' ?> >
                            <div>Выберите банк:</div>
                            <div class="bankWrap">
                                <div class="bSelectWrap mFastInpSmall fl">
                                    <span class="bSelectWrap_eText"><?= $bank->getName() ?></span>
                                    <select class='bSelect mFastInpSmall' data-value="<?= $page->json($bankData) ?>">
                                        <!-- <option class="bSelect_eItem" data-bind="click: function(data, event) { $root.clickInterval($parent, data, event) }, text: $data"></option> -->
                                    </select>
                                </div>

                                <!-- <div data-value="<?= $page->json($bankData) ?>" class="fl bSelect mFastInpSmall">
                                    <span > <?= $bank->getName() ?></span>
                                    <div class="bSelect__eArrow"></div>
                                </div> -->

                                <div class="fl creditHref"><a target="_blank" href="<?= $bank->getLink() ?>">Условия кредита <span>(<?= $bank->getName() ?>)</span></a></div>
                                <div class="clear"></div>
                            </div>
                            <input type='hidden' name='order[credit_bank_id]' value='<?= $bank->getId(); ?>' />
                            <div id="tsCreditCart" data-value="<?= $page->json($creditData) ?>" ></div>
                            <!--div>Сумма заказа: <span class="rubl">p</span></div-->
                            <div>
                                <strong style="font-size:160%; color: #000;">Ежемесячный платеж<sup>**</sup>:
                                    <span id="creditPrice"></span> <span class="rubl"> p</span>
                                </strong>
                            </div>
                            <div><sup>**</sup> Кредит не распространяется на услуги F1 и доставку. Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.</div>
                        </div>
                        <?php } else if ($paymentMethod->isCertificate()) { ?>
                        <div class="orderFinal__certificate hidden innerType">
                            <script type="text/html" id="processBlock">
                                <div class="process">
                                    <div class="img <%=typeNum%>"></div>
                                    <p><%=text%></p>
                                    <div class="clear"></div>
                                </div>
                            </script>
                            <div id="sertificateFields">
                                <input name="order[cardnumber]" type="text" class="bBuyingLine__eText cardNumber" placeholder="Номер" />
                                <input name="order[cardpin]" type="text" class="bBuyingLine__eText cardPin" placeholder="ПИН" />
                            </div>
                            <div id="processing"></div>
                        </div>
                        <?php } ?>
                    </i>
                </div>
                <?php endforeach ?>
            </dd>
        </dl>

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


<?php if (\App::config()->analytics): ?>
    <div id="marketgidOrder" class="jsanalytics"></div>
    <div id="heiasOrder" data-vars="<?= $user->getCart()->getAnalyticsData() ?>" class="jsanalytics"></div>
    <?= $page->render('order/_odinkodForCreate') ?>
<?php endif ?>
