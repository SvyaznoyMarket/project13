<?php

return function(
    \Helper\TemplateHelper $helper,
    $id,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $pointsById */

    $region = \App::user()->getRegion();

    $dataValue = [
        'latitude'  => $region->getLatitude(),
        'longitude' => $region->getLongitude(),
        'zoom'      => 10,
        'points'    => [],
    ];

    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[]|\Model\OrderDelivery\Entity\Point\Svyaznoy[] $points */
    foreach ($order->possible_points as $token => $points) {
        foreach ($points as $point) {
            $p = $point['point'];
            $dataValue['points'][$token][] = [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'regtime' => $p->regtime,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'marker'    => $orderDelivery->points[$token]->marker
            ];
        }
    }
    ?>

    <div id="<?= $id ?>" class="selShop popupFl deliv-wrap" style="display: none;" data-block_name="<?= $order->block_name ?>">
        <div class="js-order-changePlace-close popupFl_clsr jsCloseFl" data-content="#<?= $id ?>"></div>

        <div class="selShop_hh">Выберите точку самовывоза</div>

        <!-- Новая верстка -->
        <div class="popup-left-container">
            <div class="deliv-ctrls">
                <div class="deliv-search">
                    <input class="deliv-search-input" type="text" placeholder="Искать по улице, метро"/>
                </div>
                <div class="deliv-sel-group fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener <?//="picked"; - этот класс должен добавляться после выбора какого-либо значения?>">
                            <span class="fltrBtnBox_tggl_tx">Все точки</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-point-enter" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-point-enter">

                                            <span class="customLabel_btx">Магазин Enter</span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-point-post" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-point-post">

                                            <span class="customLabel_btx">Почта России</span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-point-hermes" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-point-hermes">

                                            <span class="customLabel_btx">HermesDPD</span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-point-podorozhnik" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-point-podorozhnik">

                                            <span class="customLabel_btx">Подорожник</span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-point-pickpoint" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-point-pickpoint">

                                            <span class="customLabel_btx">PickPoint</span>
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener <?//="picked"; - этот класс должен добавляться после выбора какого-либо значения?>">
                            <span class="fltrBtnBox_tggl_tx">Стоимость</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-price1" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-price1">

                                            <span class="customLabel_btx">50<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-price2" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-price2">

                                            <span class="customLabel_btx">500<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-price3" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-price3">

                                            <span class="customLabel_btx">120<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener <?//="picked"; - этот класс должен добавляться после выбора какого-либо значения?>">
                            <span class="fltrBtnBox_tggl_tx">Дата</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-date1" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-date1">

                                            <span class="customLabel_btx">Сегодня<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-date2" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-date2">

                                            <span class="customLabel_btx">Завтра<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                    <div class="fltrBtn_ln ">
                                        <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-delivery-date3" name="" value="">
                                        <label class="customLabel customLabel-defcheck2" for="id-delivery-date3">

                                            <span class="customLabel_btx">23.05.2015<span class="rubl">р</span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
                <div class="selShop_l" data-token="shops">
                    <ul class="deliv-list">

                        <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/enter.png" class="deliv-item__img">
                                <span class="deliv-item__name">Магазин Enter</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">Завтра</div>
                                <div class="deliv-item__price">50 <span class="rubl">p</span></div>
                            </div>
                        </li>
                        <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/post.png" class="deliv-item__img">
                                <span class="deliv-item__name">Почта России</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">23.05.2015</div>
                                <div class="deliv-item__price">50 <span class="rubl">p</span></div>
                            </div>
                        </li>
                        <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/hermes.png" class="deliv-item__img">
                                <span class="deliv-item__name">HermesDPD</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">Послезавтра</div>
                                <div class="deliv-item__price">500 <span class="rubl">p</span></div>
                            </div>
                        </li>
                        <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/pickpoint.png" class="deliv-item__img">
                                <span class="deliv-item__name">PickPoint</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">Послезавтра</div>
                                <div class="deliv-item__price">500 <span class="rubl">p</span></div>
                            </div>
                        </li>
                          <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/pickpoint.png" class="deliv-item__img">
                                <span class="deliv-item__name">PickPoint</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">Послезавтра</div>
                                <div class="deliv-item__price">500 <span class="rubl">p</span></div>
                            </div>
                        </li>
                        <li class="deliv-item">
                            <div class="deliv-item__logo">
                                <img src="/styles/order/img/deliv-logo/podorozhnik.png" class="deliv-item__img">
                                <span class="deliv-item__name">Подорожник</span>
                            </div>
                            <div class="deliv-item__addr">
                                <div class="deliv-item__metro" style="background: #FBAA33;">
                                   <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                                </div>
                                <div class="deliv-item__addr-name">ул. Орджоникидзе, д. 11, стр. 10</div>
                                <div class="deliv-item__time">с 10.00 до 21.00</div>
                            </div>

                            <div class="deliv-item__info">
                                <div class="deliv-item__date">Послезавтра</div>
                                <div class="deliv-item__price">500 <span class="rubl">p</span></div>
                            </div>
                        </li>
                    </ul>
                </div>
        </div>

        <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r" data-value="<?= $helper->json($dataValue) ?>"></div>

    </div>

<? };