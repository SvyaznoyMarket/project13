<?php
/**
 * @var $page \View\Layout
 * @var $user \Session\User
 */
?>
<div class="bOrderSteps">

    <div class='bOrderPreloader' data-bind="style: { display: $root.showForm()===true ? 'none' : 'block' }">
        <span>Ваш заказ формируется...</span><img src='/images/bPreloader.gif'>
    </div>

    <div class="bOrderView" data-bind="visible: $root.showForm()" style="display:none">
        <h2 class="bOrderView__eTitle">Информация о заказе</h2>

        <div class="bBuyingLine mOrderMethod">
            <div class="bBuyingLine__eLeft">Выберите предпочтительный способ</div>
            <div class="bBuyingLine__eRight">
                <!-- ko if: dlvrCourierEnable() -->
                <label class="bBuyingLine__eLabel" for="order_delivery_type_id_1"
                       data-bind="click: pickCourier, css: {mChecked: !dlvrShopEnable()}">
                    <b></b> Доставка заказа курьером
                    <input class="bBuyingLine__eRadio" type="radio" name="order[delivery_type_id]" id="order_delivery_type_id_1"
                           value="1" autocomplete="off"/>
                </label>
                <p class="bBuyingLine__eDesc">Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.</p>
                <!-- /ko -->

                <!-- ko if: dlvrShopEnable() -->
                <label class="bBuyingLine__eLabel" class="" for="order_delivery_type_id_3"
                       data-bind="click: pickShops, css: {mChecked: !dlvrCourierEnable()}">
                    <b></b>
                    Самостоятельно заберу в магазине
                    <input class="bBuyingLine__eRadio" type="radio" name="order[delivery_type_id]" id="order_delivery_type_id_3"
                           value="3" autocomplete="off"/>
                </label>
                <p class="bBuyingLine__eDesc">Вы можете самостоятельно забрать товар из ближайшего к вам магазина Enter. Услуга бесплатная! Резерв товара сохраняется 3 дня. Пожалуйста, выберите магазин.</p>
                <!-- /ko -->

                <a href="#" style="display: none;" class="bBigOrangeButton mSelectShop selectShop" data-bind="visible: shopButtonEnable, click: showAllShops">Выберите магазин</a>
            </div>
        </div>

        <div id="orderMapPopup" class='popup'>
            <i class='close'></i>

            <div class='bMapShops__eMapWrap' id="mapPopup" style="float: right;">
            </div>
            <div class='bMapShops__eList'>
                <h3>Выберите магазин Enter для самовывоза</h3>
                <p class="pl20">Регион <?= $user->getRegion()->getName() ?>  ( <a class="jsChangeRegion" href="<?= $page->url('region.change', array('regionId' => $user->getRegion()->getId())) ?>" style="font-weight: normal">изменить</a> )</p>
                <ul id="mapPopup_shopInfo">
                    <!-- ko foreach: shopsInPopup -->
                    <li data-bind="attr: {ref: id}, click: $root.selectShop ">
                        <div class="bMapShops__eListNum"><img src="/images/shop.png" alt=""/></div>
                        <div data-bind="text: name"></div>
                        <span>Работаем</span> <span data-bind="text: regime"></span>
                    </li>
                    <!-- /ko -->
                </ul>
            </div>
        </div>

        <div data-bind="style: { display: $root.stolenItems().length > 0 ? 'block' : 'none' }" class="hf">
            <div class='bMobDownWrapAbs customalign'>
                <div class='bMobDownWrapRel'>
                    <div class='bMobDown mBR5 mW2 mW750'>
                        <div class='bMobDown__eWrap'>
                            <img class='fr pt20 mr20' src='/images/error_ajax.gif'/>

                            <h2 class="pb30">Кто-то был быстрее вас.<br/>
                                Некоторых товаров уже нет в наличии:</h2>
                            <!-- ko foreach: $root.stolenItems -->
                            <div class='bFormSave'>
                                <span data-bind="html: title"></span>

                                <h2><span data-bind="html: price"></span> <span class='rubl'>p</span></h2>
                            </div>
                            <!-- /ko -->
                            <a class='bOrangeButton mr20' id="tocontinue" href>Оформить заказ без этих товаров</a>
                            <a class='bOrangeButton'>Подобрать похожий товар</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="graying"></div>
        </div>

        <div style="display:none" data-bind="visible: step2">
            <!-- ko foreach: dlvrBoxes -->
            <div class="bBuyingLineWrap order-delivery-holder">
                <div class="delivery-message red"></div>

                <div class="bBuyingLine">
                    <div class="bBuyingLine__eLeft">
                        
                        <h2 class="bOrderView__eTitle">
                            <span data-bind="visible: type === 'self' ">Самовывоз</span>
                            <span data-bind="visible: type === 'standart' ">Доставим</span>
                            <span data-bind="text: $root.printDate( $data.chosenDate() )"></span>*
                        </h2>

                        <div class="bSelectWrap mFastInpSmall">
                            <span class="bSelectWrap_eText" data-bind="text: $data.chosenInterval()"></span>
                            <select id="order-interval_standart_rapid-holder" class='bSelect order-interval-holder' data-bind="options:currentIntervals, optionsText:$data, optionsValue:$data, value:chosenInterval"></select>
                        </div>

                        <div class="bOrderDeliveryPrice">
                            <!-- ko if: dlvrPrice() > 0 -->
                            <span class="bOrderDeliveryPrice__eItem mTextColor">Стоимость доставки
                                <span data-bind="html: printPrice( dlvrPrice() )"></span>
                                <span class="rubl">p</span>
                            </span>
                            <!-- /ko -->

                            <!-- ko if: dlvrPrice() <= 0 -->
                            Бесплатно
                            <span class="bOrderDeliveryPrice__eItem">Ожидайте смс-сообщение<br/>о приходе заказа в магазин</span>
                            <!-- /ko -->

                            <!-- ko if: supplied() -->
                            <span class="bOrderDeliveryPrice__eItem mTextColor">Оператор контакт-cEnter<br/>подтвердит точную дату<br/>доставки за 2-3 дня.</span>
                            <!-- /ko -->

                            <!-- ko if: $parent.type === 'self' -->
                            <span class="bOrderDeliveryPrice__eItem" data-bind="text: $parent.shop().name"></span>

                            <a class="bBigOrangeButton bSelectShop" href="#"
                               data-bind="click: function(data, event) { $root.showShopPopup($parent, data, event) }">Другой магазин</a>
                            <!-- /ko -->

                            <span class="bOrderDeliveryPrice__eItem mFootnote"><em class="bStar">*</em> Дату доставки уточнит специалист<br/>Контакт-сENTER</span>
                        </div>
                    </div>

                    <div class="bBuyingLine__eRight">
                        <div class="bCelendar clearfix">
                            <ul class="bBuyingDates">
                                <li data-direction="prev" class="bBuyingDates__eLeft order-delivery_date-control"
                                    data-bind="click: function(data, event) { $root.changeWeek('-1', data, event) }">
                                    <b></b><span class="dow"></span>
                                </li>
                                <!-- ko foreach: caclDates -->
                                <li class="order-delivery_date"
                                    data-bind="style: { display: ( $data.week === $parent.curWeek() ) ? $root.cssForDate : 'none' },
                                        click: function(data, event) { $root.clickDate($parent, data, event) },
                                        css: { bBuyingDates__eEnable: $data.enable(),
                                                bBuyingDates__eDisable: (!$data.enable()),
                                                bBuyingDates__eCurrent: ($data.tstamp == $parent.chosenDate()) }">
                                    <span data-bind="text: day"></span> <span class="dow" data-bind="text: dayOfWeek"></span>
                                </li>
                                <!-- /ko -->
                                <li data-direction="next" class="bBuyingDates__eRight order-delivery_date-control"
                                    data-bind="click: function(data, event) { $root.changeWeek('1', data, event) }">
                                    <b></b><span class="dow"></span>
                                </li>
                            </ul>
                        </div>

                        <!-- ko foreach: $data.itemList -->
                        <div class="bBuyingLine mProductsLine">
                            <div class="bBuyingLine__eLeft" data-bind="ifnot: $index()*1"></div>

                            <div class="bBuyingLine__eRight">
                                <div class="bOrderItems">
                                    <div class="bItemsRow mItemImg"><img data-bind="attr: {src: $data.image, alt: $data.name }"/></div>

                                    <div class="bItemsRow mItemInfo">
                                        <a href="" target="_blank" data-bind="html: name, attr: { href: $data.url }"></a>
                                        <span class="bCountItem">(<span data-bind="text: $data.quantity "></span> шт.)</span>
                                    </div>

                                    <div class="bItemsRow mItemRight"><a data-bind=" click: function(data, event) { $root.deleteItem($parent, data, event) }">удалить</a></div>

                                    <div class="bItemsRow mItemRight"> <span data-bind="html: printPrice( $data.total )"></span> <span class="rubl">p</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- /ko -->
                    </div>
                </div>

                <div class="bBuyingLineWrap__eSum" data-template="#order-delivery_total-template">Итого с доставкой:
                    <b><span data-bind="html: printPrice( $data.totalPrice() )"></span> <span class="rubl">p</span></b>
                </div>
            </div>
            <!-- /ko -->

            <div class="bF1SaleCard mOrder">
                <h3 class="bF1SaleCard_eTitle">Скидки</h3>
                <? if (\App::config()->coupon['enabled']): ?>
                    <p class="font11"><a href="<?= $page->url('cart') ?>">Введите код скидки на товары</a></p>
                <? endif ?>
            
                <? if (\App::config()->f1Certificate['enabled']): ?>
                    <p class="font11"><a href="<?= $page->url('cart') ?>">Введите номер карты «Под защитой F1» для скидки на услуги</a></p>
                <? endif ?>
            </div>

            <div class="bBuyingLine mSumm clearfix">
                <a class="bBackCart mOrdeRead" href="<?= $page->url('cart') ?>">&lt; Редактировать товары</a>

                <div class="bTotalSumm">
                    Сумма всех заказов:

                    <span class="bTotalSumm__ePrice">
                        <span data-bind="html: printPrice( totalSum() )"></span>
                        <span class="rubl">p</span>
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>