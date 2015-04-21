<?
/**
 * @var $dataPoints \View\PointsMap\MapView
 * @var $product    \Model\Product\Entity
 * @var $visible    bool
 * @var $class      string
 */

    $helper = \App::helper();
    $uniqId = uniqid();

    // для дропбоксов
    $onmouseleave = "this.style.display='none'; $(this).parent().removeClass('opn')";

    $mapData = [
        'latitude'  => $dataPoints->mapConfig['latitude'],
        'longitude' => $dataPoints->mapConfig['longitude'],
        'zoom'      => $dataPoints->mapConfig['zoom'],
        'points'    => $dataPoints->points
    ];

    $uniqueCosts = $dataPoints->getUniquePointCosts();
    $uniqueDays = $dataPoints->getUniquePointDays();
    $uniqueTokens = $dataPoints->getUniquePointTokens();

    if (!isset($visible)) $visible = false;
    if (!isset($class)) $class = 'jsNewPoints';

    ?>

    <div class="selShop popupFl pickup <?= $class ?>" style="display: <?= $visible ? 'block' : 'none';  ?>" data-block_name="<?= $order->block_name ?>">

        <div class="js-order-changePlace-close popupFl_clsr jsCloseFl" data-content="#<?= 'map-' . $uniqId ?>"></div>

        <div class="pickup__title">Выберите точку самовывоза</div>

        <!-- Новая верстка -->
        <div class="common-wrap clearfix">
        <div class="popup-left-container">
            <div class="pickup-ctrls">

                <div class="pickup-search">
                    <div class="pickup-search__input-wrap">
                        <input class="pickup-search-input" type="text" placeholder="Искать по улице, метро" data-bind="click: enableAutocompleteListVisible, value: searchInput, valueUpdate: 'afterkeydown'" />
                        <div class="pickup-search__clear" data-bind="click: clearSearchInput, visible: searchInput ">×</div>
                    </div>
                    <div class="pickup-suggest" style="display: none"
                         data-bind="visible: searchAutocompleteListVisible() && searchAutocompleteList().length > 0, event: { mouseleave: disableAutocompleteListVisible }">
                        <ul class="pickup-suggest__list" data-bind="foreach: searchAutocompleteList">
                            <li class="pickup-suggest__i" data-bind="text: name, attr: { 'data-bounds': bounds }, click: $parent.setMapCenter"></li>
                        </ul>
                    </div>
                </div>

                <div class="pickup-sel-group fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">

                    <!-- Точка самовывоза -->
                    <div class="pickup-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: choosenTokens().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx" data-bind="text: pointsText">Все точки</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                    <? foreach ($uniqueTokens as $token) : ?>

                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-point-<?= $token.$uniqId ?>" name="" value="<?= $token ?>"
                                                   data-bind="checked: choosenTokens" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-point-<?= $token.$uniqId ?>">
                                                <span class="customLabel_btx"><?= $dataPoints->getDropdownName($token) ?></span>
                                            </label>
                                        </div>

                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cтоимость -->
                    <div class="pickup-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: $root.choosenCosts().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx" data-bind="html: costsText">Стоимость</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param">
                                    <? foreach ($uniqueCosts as $cost) : ?>

                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-price-<?= $cost.$uniqId ?>" name="" value="<?= $cost ?>"
                                                    data-bind="checked: $root.choosenCosts" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-price-<?= $cost.$uniqId ?>">
                                                <span class="customLabel_btx"><?= $cost == 0 ? 'Бесплатно' : $cost . ' <span class="rubl">p</span>' ?></span>
                                            </label>
                                        </div>

                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дата самовывоза -->
                    <div class="pickup-sel fltrBtnBox js-category-v2-filter-dropBox jsOrderV3Dropbox">
                        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener" data-bind="css: { picked: choosenDates().length > 0 }">
                            <span class="fltrBtnBox_tggl_tx" data-bind="text: datesText">Дата</span>
                            <i class="fltrBtnBox_tggl_corner"></i>
                        </div>

                        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="fltrBtnBox_dd_inn">
                                <div class="fltrBtn_param">
                                    <? foreach ($uniqueDays as $day) : ?>
                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-date-<?= $day.$uniqId ?>" name="" value="<?= $day ?>"
                                                   data-bind="checked: choosenDates" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-date-<?= $day.$uniqId ?>">
                                                <span class="customLabel_btx"><?= $helper->humanizeDate(DateTime::createFromFormat('Y-m-d', $day)) ?></span>
                                            </label>
                                        </div>
                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
                
                <div class="selShop_l" data-token="shops" data-bind="css:{'nobefore': points().length == 0}">

                    <span class="pickup-nomatch" data-bind="visible: points().length == 0">Поиск не дал результатов</span>

                    <table class="pickup-list">
                        <tbody data-bind="foreach: points">
                            <tr class="pickup-item jsChangePoint clearfix" data-bind="attr: { 'data-id': $data.id, 'data-token': $data.token, 'data-blockname': $data.orderToken }">
                                <td class="pickup-item__logo">
                                    <img src="" class="pickup-item__img" data-bind="attr: { src: icon }" />
                                    <span class="pickup-item__name" data-bind="text: listName"></span>
                                </td>
                                <td class="pickup-item__addr">
                                    <!-- ko if: $.isArray(subway) -->
                                    <div class="pickup-item__metro" data-bind="style: { background: subway[0].line.color }">
                                       <div class="pickup-item__metro-inn" data-bind="text: subway[0].name"></div>
                                    </div>
                                    <!-- /ko -->
                                    <div class="pickup-item__addr-name" data-bind="text: address"></div>
                                    <div class="pickup-item__time" data-bind="text: regtime"></div>
                                </td>
                                <!-- если товар доступен для заказа, выводим это: -->
                                <td class="pickup-item__info">
                                    <div class="pickup-item__date" data-bind="text: humanNearestDay"></div>
                                    <div class="pickup-item__price"><span data-bind="text: cost == 0 ? 'Бесплатно' : cost "></span> <span class="rubl" data-bind="visible: cost != 0">p</span></div>
                                </div>
                                <div class="pickup-item__buy">
                                    <a
                                        href=""
                                        class="btn-type btn-type--buy jsOneClickButton-new jsOneClickButtonOnDeliveryMap"
                                        <? if ($product) : ?>data-product-ui="<?= $product->getUi() ?>"<? endif ?>
                                        data-bind="attr: { 'data-shop': id }">Купить</a>
                                </div>
                                </td>

                                <!-- конец -->
                                <!-- если товар только на витрине, выводим это: -->
                                 <!-- <td class="pickup-item__info pickup-item__info--nobtn">
                                    <span class="pickup-item__ondisplay-lbl">На витрине</span>
                                    <i class="i-product i-product--info-normal i-info__icon pickup-item__ondisplay-icon"></i>
                                    <?//попап с подсказкой, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open?>
                                    <div class="pickup-item__ondisplay-popup info-popup info-popup--ondisplay">
                                        <p>Чтобы купить товар с витрины,<br/>нужно приехать в магазин и обратиться к продавцу.</p>
                                    </div>
                                </td> -->
                                <!-- конец -->
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

            <div id="<?= 'map-' . $uniqId ?>" class="js-order-map selShop_r clearfix"></div>

        <? if (isset($product) && $product->delivery && $product->delivery->getDelivery()) : ?>

            <div class="deliv">
                <div class="deliv__title">Доставка</div>
                <div class="deliv-info">
                    <span class="deliv-info__date"><?= $helper->humanizeDate($product->delivery->getDeliveryWithMinDate()->getMinDate()->date) ?></span>
                    <span class="deliv-info__price"><?= $helper->formatPrice($product->delivery->getDeliveryWithMinDate()->price) ?> <span class="rubl">p</span></span>
                    <div class="deliv-info__buy">
                        <a href="" class="btn-type btn-type--buy">Купить с доставкой</a>
                    </div>
                    <a class="deliv-info-more-link" href="#">Подробнее об условиях и способах доставки</a>
                </div>
            </div>

        <? endif ?>

        <?= $helper->jsonInScriptTag($mapData, '', 'jsMapData') ?>

    </div>
</div>