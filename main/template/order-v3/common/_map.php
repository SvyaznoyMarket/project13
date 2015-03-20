<?php

return function(
    \Helper\TemplateHelper $helper,
    $id,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $pointsById */

    $region = \App::user()->getRegion();

    // для дропбоксов
    $onmouseleave = "this.style.display='none'; $(this).parent().removeClass('opn')";

    $dataValue = [
        'latitude'  => $region->getLatitude(),
        'longitude' => $region->getLongitude(),
        'zoom'      => 10,
        'points'    => [],
    ];

    $pointsCost = [];
    $nearestDays = [];

    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[]|\Model\OrderDelivery\Entity\Point\Svyaznoy[] $points */
    foreach ($order->possible_points as $token => $points) {
        foreach ($points as $point) {
            $p = $point['point'];
            $dataValue['points'][$token][] = [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $helper->noBreakSpaceAfterDot($p->address),
                'regtime' => $p->regtime,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'marker'    => $orderDelivery->points[$token]->marker,
                'token'  => $token,
                'icon'  => $orderDelivery->points[$token]->icon,
                'cost'  => (string)$point['cost'],
                'humanNearestDay'   => $helper->humanizeDate(DateTime::createFromFormat('Y-m-d', $point['nearestDay']), 'Y-m-d'),
                'nearestDay'  => $point['nearestDay'],
                'blockName'    => $orderDelivery->points[$token]->block_name,
                'orderToken' => $order->block_name
            ];
            $pointsCost[] = $point['cost'];
            $nearestDays[] = $point['nearestDay'];
        }
    }

    $uniqueCosts = array_unique($pointsCost);
    sort($uniqueCosts);
    $uniqueDays = array_unique($nearestDays);
    sort($uniqueDays);
    ?>

    <div id="<?= $id ?>" class="selShop popupFl deliv-wrap jsNewPoints" style="display: none;" data-block_name="<?= $order->block_name ?>">

        <div class="js-order-changePlace-close popupFl_clsr jsCloseFl" data-content="#<?= $id ?>"></div>

        <div class="selShop_hh">Выберите точку самовывоза</div>

        <!-- Новая верстка -->
        <div class="popup-left-container">
            <div class="deliv-ctrls">

                <div class="deliv-search">
                    <input class="deliv-search-input" type="text" placeholder="Искать по улице, метро" data-bind="value: searchInput, valueUpdate: 'afterkeydown'" />
                </div>

                <div class="deliv-sel-group fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">

                    <!-- Точка самовывоза -->
                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: choosenTokens().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx" data-bind="text: pointsText">Все точки</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                    <? foreach ($order->possible_points as $token => $points) : ?>

                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-point-<?= $token.$order->block_name ?>" name="" value="<?= $token ?>"
                                                   data-bind="checked: choosenTokens" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-point-<?= $token.$order->block_name ?>">
                                                <span class="customLabel_btx"><?= $orderDelivery->points[$token]->dropdown_name ?></span>
                                            </label>
                                        </div>

                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cтоимость -->
                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: $root.choosenCosts().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx">Стоимость</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param">
                                    <? foreach ($uniqueCosts as $cost) : ?>

                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-price-<?= $cost.$order->block_name ?>" name="" value="<?= $cost ?>"
                                                    data-bind="checked: $root.choosenCosts" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-price-<?= $cost.$order->block_name ?>">
                                                <span class="customLabel_btx"><?= $cost == 0 ? 'Бесплатно' : $cost . ' <span class="rubl">р</span>' ?></span>
                                            </label>
                                        </div>

                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дата самовывоза -->
                    <div class="deliv-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: choosenDates().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx">Дата</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param">
                                    <? foreach ($uniqueDays as $day) : ?>
                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-date-<?= $day.$order->block_name ?>" name="" value="<?= $day ?>"
                                                   data-bind="checked: choosenDates" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-date-<?= $day.$order->block_name ?>">
                                                <span class="customLabel_btx"><?= $helper->humanizeDate(DateTime::createFromFormat('Y-m-d', $day), 'Y-m-d') ?></span>
                                            </label>
                                        </div>
                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="selShop_l" data-token="shops">
                <ul class="deliv-list" data-bind="foreach: points">

                    <li class="deliv-item jsChangePoint" data-bind="attr: { 'data-id': $data.id, 'data-token': $data.token, 'data-blockname': $data.orderToken }">
                        <div class="deliv-item__logo">
                            <img src="" class="deliv-item__img" data-bind="attr: { src: icon }" />
                            <span class="deliv-item__name" data-bind="text: blockName"></span>
                        </div>
                        <div class="deliv-item__addr">
                            <!-- ko if: typeof subway !== 'undefined' -->
                            <div class="deliv-item__metro" style="background: #FBAA33;">
                               <div class="deliv-item__metro-inn">м. Ленинский проспект</div>
                            </div>
                            <!-- /ko -->
                            <div class="deliv-item__addr-name" data-bind="text: address"></div>
                            <div class="deliv-item__time" data-bind="text: regtime"></div>
                        </div>

                        <div class="deliv-item__info">
                            <div class="deliv-item__date" data-bind="text: humanNearestDay"></div>
                            <div class="deliv-item__price"><span data-bind="text: cost == 0 ? 'Бесплатно' : cost "></span> <span class="rubl" data-bind="visible: cost != 0">p</span></div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

        <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r"></div>
        <?= $helper->jsonInScriptTag($dataValue, '', 'jsMapData') ?>

    </div>

<? };