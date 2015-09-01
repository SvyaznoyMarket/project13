<?php

return function (
    \Helper\TemplateHelper $helper,
    array $compareGroups,
    $activeCompareGroupIndex
) { ?>
    <div class="js-compare js-module-require" data-module="enter.compare" data-compare-groups="<?= $helper->json($compareGroups) ?>" data-active-compare-group-index="<?= $helper->escape($activeCompareGroupIndex) ?>">
        <div class="cmprHd js-compare-header" data-bind="css: {'cmprHd-empty': !compareGroups().length}">
            <div class="cmprHd_t">
                Сравнение товаров
            </div>

            <ul class="cmprHd_lst" data-bind="visible: compareGroups().length" style="display: none;">
                <!-- ko foreach: {data: compareGroups} -->
                <li class="cmprHd_lst_i" data-bind="css: {'cmprHd_lst_i-act': $parent.activeCompareGroupIndex() == $index()}"><a href="#" class="js-compare-typeLink" data-bind="attr: {'data-index': $index}"><!-- ko text: type.name --><!-- /ko --><span class="cmprHd_lst_qn"> <!-- ko text: products().length --><!-- /ko --></span></a></li>
                <!-- /ko -->
            </ul>
        </div>

        <div class="cmprCntW js-compare-content">
            <!-- ko if: compareGroups().length -->
            <div data-bind="visible: compareGroups().length" style="display: none;">
                <!-- ko if: compareGroups()[activeCompareGroupIndex()] -->
                <table class="goods cmprCnt js-compare-table" data-bind="css: {'cmprCnt_similarOnly': similarOnly, 'cmprCnt-scroll': scrolled}">
                    <tr class="cmprCnt_head js-compare-tableHeadRow">
                        <th class="cmprCnt_modes">
                            <div class="cmprCnt_fixed cmprCnt_modeW js-compare-fixed cmprCnt_cell">
                                <menu class="cmprCnt_mode">
                                    <button class="cmprCnt_mode_btn btn1 js-compare-modeSimilarOnly" data-bind="css: {'cmprCnt_mode_btn-act': similarOnly}">Только отличия</button>
                                    <button class="cmprCnt_mode_btn btn1 js-compare-modeAll" data-bind="css: {'cmprCnt_mode_btn-act': !similarOnly()}">Все характеристики</button>
                                </menu>
                            </div>
                        </th>

                        <!-- ko foreach: {data: compareGroups()[activeCompareGroupIndex()].products} -->

                        <td class="goods__item cmprCnt_product">
                            <div class="cmprCnt_fixed cmprCnt_modeW js-compare-fixed cmprCnt_cell js-module-require"
                                 data-bind="
                                 attr: { 'data-product': JSON.stringify({ id: id, ui: ui }), 'data-module': 'enter.product' }">
                                <a href="" class="goods__delete icon-clear js-compare-deleteProductLink" data-bind="attr: {href: deleteFromCompareUrl, 'data-product-id': id}"></a>

                                <a class="goods__img" href="" data-bind="attr: {href: link}">
                                    <img src="" alt="" class="goods__img-image" data-bind="attr: {src: imageUrl}">
                                </a>

                                <div class="goods__rating rating" style="display: none">
                                    <!-- ko if: reviews.count -->
                                    <span class="rating-state">
                                        <!-- ko foreach: {data: reviews.stars.notEmpty} -->
                                        <i class="rating-state__item rating-state__item_1 icon-rating rating-state__item_fill"></i>
                                        <!-- /ko -->

                                        <!-- ko foreach: {data: reviews.stars.empty} -->
                                        <i class="rating-state__item rating-state__item_1 icon-rating"></i>
                                        <!-- /ko -->
                                    </span>

                                    <span class="rating-count">(<!-- ko text: reviews.count --><!-- /ko -->)</span>
                                    <!-- /ko -->
                                </div>

                                <div class="goods__name">
                                    <div class="goods__name-inn">
                                        <a class="link" href="" data-bind="attr: {href: link}"><!-- ko text: prefix --><!-- /ko --> <!-- ko text: webName --><!-- /ko --></a>
                                    </div>
                                </div>

                                <div class="goods__price-old"><!-- ko if: priceOld != '0' --><span class="line-through" data-bind="html: priceOld"></span>&thinsp;<span class="rubl">C</span><!-- /ko --></div>

                                <div class="goods__price-now"><span data-bind="html: price"></span>&thinsp;<span class="rubl">C</span></div>

                                <!-- ko if: isBuyable -->
                                    <a href="" class="goods__btn btn-primary "
                                       data-bind="
                                        css: {'btn-set js-buy-slot-button': isSlot, 'js-buy-button': !isSlot},
                                        text: isSlot ? 'Отправить заявку' : 'Купить',
                                        attr: {
                                            'data-product-id': id,
                                            'data-product-ui': ui,
                                            'data-product-article': article,
                                            'data-product-url': link,
                                            'data-in-shop-stock-only': inShopStockOnly ? 'true' : 'false',
                                            'data-in-shop-showroom-only': inShopShowroomOnly ? 'true' : 'false',
                                            'data-is-buyable': isBuyable ? 'true' : 'false',
                                            'data-status-id': statusId, 'data-upsale': upsale,
                                            'data-full': 1, 'data-partner-name': partnerName,
                                            'data-partner-offer-url': partnerOfferUrl,
                                            'data-is-slot': isSlot,
                                            'data-sender': typeof sender != 'undefined' ? sender : '',
                                        }
                                       ">Купить</a>
                                <!-- /ko -->
                            </div>
                        </td>
                        <!-- /ko -->
                    </tr>

                    <!-- ko foreach: {data: compareGroups()[activeCompareGroupIndex()].propertyGroups, as: 'propertyGroup'} -->
                    <tr class="cmprCnt_property cmprCnt_property_group"
                        data-bind="attr: {'data-property-group-index': $index},
                                    css: {'cmprCnt_property_similar': isSimilar, 'cmprCnt_property_group-act': propertyGroup.opened},
                                    visible: !isSimilar()">
                        <th>
                            <div class="cmprCnt_fixed js-compare-fixed cmprCnt_cell">
                                <a href="#" class="js-compare-propertyGroupLink"><span><!-- ko text: name --><!-- /ko --></span></a>
                            </div>
                        </th>

                        <!-- ko foreach: {data: $root.compareGroups()[$root.activeCompareGroupIndex()].products} -->
                        <td>
                            <div class="cmprCnt_cell"></div>
                        </td>
                        <!-- /ko -->
                    </tr>

                    <!-- ko foreach: {data: propertyGroup.properties} -->
                    <tr class="cmprCnt_property cmprCnt_property_item"
                        data-bind="css: {'cmprCnt_property_similar': isSimilar, 'cmprCnt_property_group-hide': !propertyGroup.opened()},
                                visible: !isSimilar()">
                        <th>
                            <div class="cmprCnt_fixed js-compare-fixed cmprCnt_cell">
                                <span class="cmprCnt_property_item_name"><!-- ko text: name --><!-- /ko --></span>
                            </div>
                        </th>

                        <!-- ko foreach: {data: values, as: 'value'} -->
                        <td>
                            <div class="cmprCnt_cell">
                                <!-- ko text: value.text --><!-- /ko -->
                            </div>
                        </td>
                        <!-- /ko -->
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->
                </table>
                <!-- /ko -->
            </div>
            <!-- /ko -->

            <!-- ko if: !compareGroups().length -->
            <div class="compare-empty" data-bind="visible: !compareGroups().length" style="display: none;">
                <div class="compare-empty__title">Товаров для сравнения пока нет.</div>
                <div class="compare-empty__desc">Добавляйте товары к сравнению кнопкой</div>
            </div>
            <!-- /ko -->
        </div>
    </div>
<? } ?>