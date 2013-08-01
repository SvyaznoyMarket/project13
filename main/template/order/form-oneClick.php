<?php
/**
 * @var $page \View\Layout
 * @var $user \Session\User
 */
?>

<?
/** @var $userEntity \Model\User\Entity */
$userEntity = $user->getEntity() ?: null;
$formData = array(
    'recipient_first_name'   => $userEntity ? $userEntity->getFirstName() : '',
    'recipient_email'   => $userEntity ? $userEntity->getEmail() : '',
    'recipient_phonenumbers' => $userEntity ? (11 == strlen($userEntity->getMobilePhone()) ? substr($userEntity->getMobilePhone(), 1) : $userEntity->getMobilePhone()) : '',
);
?>

<div class='bMobDown mBR5 mW2 mW1000 p0' style="display:none" id="order1click-container-new">
    <div class='bMobDown__eWrap'>
        <div class='bMobDown__eClose top0 close'></div>
        <form id="oneClick" action="" data-values="<?//= $page->json($formData) ?>">
            <input type="hidden" name="order[one_click]" value="1">
            <table class='bFast' cellpadding=0 cellspacing=0>
                <tr>
                    <td class='bFast__eItem'>
                        <div class='bFast__eItemWrap'>
                            <div class='bFast__eArrow'><div></div></div>
                            <div class='bFast__eNum'>Артикул #<span data-bind="text: shortcut"></span></div>
                            <div class="bFast__eTitle"><a href="" data-bind="html: title"></a></div>
                            <div class='bFast__eCenter'><img alt="" data-bind="attr: {src: icon}"/></div>
                            <div class='bFast__eCenter' data-bind="if : !noQBar() ">
                                <div class='bCountSet'>
                                    <a class='bCountSet__eP' href="#" data-bind="click: plusItem">+</a>
                                    <a class='bCountSet__eM' href="#" data-bind="click: minusItem">-</a>
                                    <span data-bind="text: quantityTxt">1 шт.</span>
                                </div>
                            </div>
                            <div class='bFast__ePrice'>Цена:
                                <div><span data-bind="text: priceTxt"></span> <span class="rubl">p</span></div>
                            </div>
                            <div class='bFast__ePrice'>Доставка:
                                <div><span data-bind="text: chosenDlvr().price"></span> <span class="rubl">p</span></div>
                            </div>
                            <div class='bFast__ePrice'>Итого:
                                <div><span data-bind="text: total()"></span> <span class="rubl">p</span></div>
                            </div>
                        </div>
                    </td>
                    <td class='bFast__eForm'>
                        <table cellpadding=0 cellspacing=0 class='bFastInner'>
                            <tr><th colspan="2"><h2>Заполните форму быстрого заказа:</h2></th></tr>
                            <tr data-bind="visible: noDelivery()"><td colspan="2"><h2 class="red">Товар в количестве <span data-bind="text: quantity()"></span> шт. отсутствует на складе.</h2></td></tr>
                            <tr data-bind="visible: !noDelivery()"><td width="200">Способ получения заказа:</td>
                                <td>
                                    <div class="bSelectWrap mFastInpSmall mr10" data-bind="css: { mDisabled : (disabledSelectors() || stableType() ) }">
                                        <span class="bSelectWrap_eText" data-bind="text: chosenDlvr().name"></span><!-- function(form, event) { $root.doSomething($data, form, event) } -->
                                        <select class='bSelect' data-bind="options: dlvrs, optionsText:'name', optionsValue:$data, value:chosenDlvr, event:{ change:$root.changeDlvr}">
                                        </select>
                                    </div>
                                    <div class="bSelectWrap mFastInpSmall" data-bind="css: { mDisabled : disabledSelectors() }">
                                        <span class="bSelectWrap_eText" data-bind="text: chosenDate().name"></span>
                                        <select class='bSelect' data-bind="options: dates, optionsText:'name', optionsValue:$data, value:chosenDate, event:{ change:$root.pickDate}">
                                        </select>
                                    </div>
                                </td></tr>
                            <!-- ko if: chosenDlvr().type == 'self' -->
                            <tr data-bind="visible: !noDelivery()"><td>Магазин для самовывоза:</td>
                                <td>
                                    <div class="bSelectWrap mFastInpBig" data-bind="css: { mDisabled : disabledSelectors() }">
                                        <span class="bSelectWrap_eText" data-bind="text: chosenShop().address"></span>
                                        <select class='bSelect' data-bind="options: shops, optionsText:'address', optionsValue:$data, value:chosenShop, event:{ change:$root.pickShop}">
                                        </select>
                                    </div>
                                    <!-- ko if: !disabledSelectors() -->
                                    <a class='bFast__eMapLink' href="" data-bind="click: toggleMap">
                                        <!-- ko if: !showMap() -->
                                        Показать карту магазинов
                                        <!-- /ko -->
                                        <!-- ko if: showMap() -->
                                        Скрыть карту магазинов
                                        <!-- /ko -->
                                    </a>
                                    <!-- /ko -->
                                </td></tr>
                            <!-- /ko -->

                            <tr><td colspan="2" data-bind="style: { display: showMap() ? 'table-cell' : 'none' }">
                                <div class='bMapShops__eMapWrap' id="mapPopup" style="display:none"> </div>
                                <div id="map-info_window-container" data-bind="with: pickedShop" style="display:none">
                                    <div class='bMapShops__ePopupRel'>
                                        <h3 data-bind="text: address"></h3>
                                        <span data-bind="text: regtime"></span><br>
                                        <span class="shopnum" style="display:none" data-bind="text: id"></span>
                                        <a href class='bGrayButton shopchoose' data-bind="click: $root.shopChoose">Забрать из этого магазина</a>
                                    </div>
                                </div>
                                <div id="map-info_window-container-ya" style="display:none">
                                  <div class='bMapShops__ePopupRel'>
                                    <h3>$[properties.name]</h3>
                                    <span>$[properties.regtime]</span><br>
                                    <span class="shopnum" style="display:none">$[properties.id]</span>
                                    <a href class='bGrayButton shopchoose' >Забрать из этого магазина</a>
                                  </div>
                                </div>
                            </td></tr>


                            <!-- ko foreach: textfields -->
                            <tr>
                                <td><span data-bind="text: title"></span>:</td>
                                <td>
                                    <input data-bind="event: { change: $root.validateField }, value: value, attr: { name: name, id: selectorid }, css: { mEmpty: valerror }" class='bFastInner__eInput'>
                                    <!-- ko if: valerror -->
                                    <span class='mEmpty'>(!) Пожалуйста, верно заполните поле</span>
                                    <!-- /ko -->
                                </td>
                            </tr>
                            <!-- /ko -->

                            <tr><td colspan=2>Отправьте заказ и оператор подтвердит его (смс или звонком на ваш контактный номер телефона :)</td></tr>
                            <tr><td colspan=2>
                                <a class='bBigOrangeButton run_flocktory_popup' href="" data-bind="css: { disable : noDelivery() },text: formStatusTxt, click: validateForm"></a>
                            </td></tr>
                            <tr data-bind="if: newWarehouse">
                                <td class="newWarehouse" colspan="2"><img src="/css/skin/img/greenCar.png" alt="green car" />Этот товар живет на новом красивом складе. Мы доставим его отдельным заказом :)</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>