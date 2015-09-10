<?
/**
 * @var $dataPoints \View\PointsMap\MapView
 * @var $product    \Model\Product\Entity
 * @var $visible    bool
 * @var $class      string
 * @var $page       string (order|product)
 */

    $helper = \App::helper();
    $uniqId = uniqid();

    // для дропбоксов
    $onmouseleave = "this.style.display='none'; $(this).parent().removeClass('opn')";


    array_walk($dataPoints->points, function( \Model\Point\MapPoint $point) use ($page) {
        if ($page == 'product') $point->showBaloonBuyButton = false;
    });

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

    <div class="selShop popupFl pick-point <?= $class ?>" style="display: <?= $visible ? 'block' : 'none';  ?>" data-block_name="<?= isset($order) ? $order->block_name : '' ?>">

        <div class="js-order-changePlace-close popupFl_clsr jsCloseFl" data-content="#<?= 'map-' . $uniqId ?>"></div>

        <div class="pick-point__title"><?= $page == 'order' ? 'Выберите точку самовывоза' : 'Точки самовывоза' ?></div>

        <!-- Новая верстка -->
        <div class="common-wrap clearfix">
        <div class="popup-left-container">
            <div class="pick-point-ctrls">

                <div class="pick-point-search">
                    <div class="pick-point-search__input-wrap">
                        <input class="pick-point-search-input" type="text" placeholder="Искать по улице, метро" data-bind="click: enableAutocompleteListVisible, value: searchInput, valueUpdate: 'afterkeydown'" />
                        <div class="pick-point-search__clear" data-bind="click: clearSearchInput, visible: searchInput ">×</div>
                    </div>
                    <div class="pick-point-suggest" style="display: none"
                         data-bind="visible: searchAutocompleteListVisible() && searchAutocompleteList().length > 0, event: { mouseleave: disableAutocompleteListVisible }">
                        <ul class="pick-point-suggest__list" data-bind="foreach: searchAutocompleteList">
                            <li class="pick-point-suggest__i" data-bind="text: name, attr: { 'data-bounds': bounds }, click: $parent.setMapCenter"></li>
                        </ul>
                    </div>
                </div>

                <div class="drop-filter-kit drop-filter-kit-box js-category-v2-filter-otherGroups">

                    <!-- Точка самовывоза -->
                    <div class="drop-filter-box js-category-v2-filter-dropBox jsOrderV3Dropbox" data-bind="css: { picked: choosenTokens().length > 0 }">
                        <div class="drop-filter-box__tggl js-category-v2-filter-dropBox-opener">
                            <span class="drop-filter-box__tggl-tx" data-bind="text: pointsText">Все точки</span>
                            <i class="drop-filter-box__tggl-corner"></i>
                        </div>

                        <div class="drop-filter-box__dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="drop-filter-box__dd-inn">
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
                    <div class="drop-filter-box js-category-v2-filter-dropBox jsOrderV3Dropbox" data-bind="css: { picked: $root.choosenCosts().length > 0 }">
                        <div class="drop-filter-box__tggl js-category-v2-filter-dropBox-opener">
                            <span class="drop-filter-box__tggl-tx" data-bind="html: costsText">Стоимость</span>
                            <i class="drop-filter-box__tggl-corner"></i>
                        </div>

                        <div class="drop-filter-box__dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="drop-filter-box__dd-inn">
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
                    <div class="drop-filter-box js-category-v2-filter-dropBox jsOrderV3Dropbox" data-bind="css: { picked: choosenDates().length > 0 }">
                        <div class="drop-filter-box__tggl js-category-v2-filter-dropBox-opener">
                            <span class="drop-filter-box__tggl-tx" data-bind="text: datesText">Дата</span>
                            <i class="drop-filter-box__tggl-corner"></i>
                        </div>

                        <div class="drop-filter-box__dd js-category-v2-filter-dropBox-content jsOrderV3DropboxInner" onmouseleave="<?= $onmouseleave ?>">
                            <div class="drop-filter-box__dd-inn">
                                <div class="fltrBtn_param">
                                    <? foreach ($uniqueDays as $day) : ?>
                                        <div class="fltrBtn_ln ">
                                            <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput"
                                                   type="checkbox" id="id-delivery-date-<?= $day.$uniqId ?>" name="" value="<?= $day ?>"
                                                   data-bind="checked: choosenDates" />
                                            <label class="customLabel customLabel-defcheck2" for="id-delivery-date-<?= $day.$uniqId ?>">
                                                <span class="customLabel_btx"><?= $helper->escape($day) ?></span>
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

                    <span class="pick-point-nomatch" data-bind="visible: points().length == 0">Поиск не дал результатов</span>

                    <div class="pick-point-list-wrap">
                        <table class="pick-point-list">
                            <tbody data-bind="foreach: points">

                                <tr class="pick-point-item clearfix jsChangePoint"
                                    data-bind="attr: { 'data-id': $data.id, 'data-token': $data.token, 'data-blockname': $data.orderToken }
                                    <? if ($page == 'product') : ?>, click: $root.setMapCenter <? endif ?>">

                                    <td class="pick-point-item__logo">
                                        <img src="" class="pick-point-item__img" data-bind="attr: { src: icon }" />
                                        <span class="pick-point-item__name" data-bind="text: listName"></span>
                                    </td>

                                    <td class="pick-point-item__addr">
                                        <!-- ko if: $.isArray(subway) -->
                                        <div class="pick-point-item__metro" data-bind="style: { background: subway[0].line.color }">
                                           <div class="pick-point-item__metro-inn" data-bind="text: subway[0].name"></div>
                                        </div>
                                        <!-- /ko -->
                                        <div class="pick-point-item__addr-name" data-bind="text: address"></div>
                                        <div class="pick-point-item__time" data-bind="text: regtime"></div>
                                    </td>


                                    <td class="pick-point-item__info <?= $page == 'product' ? 'no-hide-info' : 'no-hide-info' ?>">

                                    <!-- ko if: !productInShowroom -->
                                        <div class="pick-point-item__date" data-bind="text: humanNearestDay"></div>
                                        <div class="pick-point-item__price"><span data-bind="text: cost == 0 ? 'Бесплатно' : cost "></span> <span class="rubl" data-bind="visible: cost != 0">p</span></div></div>
                                    <!-- /ko -->

                                    <!-- ko if: showBuyButton -->
                                    <div class="pick-point-item__buy">
                                        <button
                                            href=""
                                            class="btn-type btn-type--buy <? if ($page == 'page') : ?>jsOneClickButton<? endif ?>"
                                            <? if (isset($productUi)) : ?>data-product-ui="<?= $productUi ?>"<? endif ?>
                                            data-bind="attr: { 'data-shop': id }">Купить</button>
                                    </div>
                                    <!-- /ko -->

                                    <!-- ko if: productInShowroom -->
                                        <span class="pick-point-item__ondisplay-lbl">На витрине</span>
                                        <i class="i-product i-product--info-normal i-info__icon pick-point-item__ondisplay-icon" onclick="$(this).next().toggleClass('info-popup--open')"></i>
                                        <?//попап с подсказкой, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open?>
                                        <div class="pick-point-item__ondisplay-popup info-popup info-popup--ondisplay ">
                                            <p>Чтобы купить товар с витрины,<br/>нужно приехать в магазин и обратиться к продавцу.</p>
                                        </div>
                                    <!-- /ko -->

                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
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