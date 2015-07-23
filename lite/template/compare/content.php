<?php

return function (
    \Helper\TemplateHelper $helper,
    array $compareGroups,
    $activeCompareGroupIndex
) { ?>
    <div class="js-compare js-module-require" data-module="enter.compare" data-compare-groups="<?= $helper->json($compareGroups) ?>" data-active-compare-group-index="<?= $helper->escape($activeCompareGroupIndex) ?>">
        <div class="cmprHd clearfix js-compare-header" data-bind="css: {'cmprHd-empty': !compareGroups().length}">
            <div class="cmprHd_l"><a href="/"><img src="/styles/compare/img/logo_cmpr.png" alt=""></a></div>

            <div class="cmprHd_r">
                <div class="cmprHd_t">
                    Сравнение товаров
                </div>

                <ul class="cmprHd_lst" data-bind="visible: compareGroups().length" style="display: none;">
                    <!-- ko foreach: {data: compareGroups} -->
                    <li class="cmprHd_lst_i" data-bind="css: {'cmprHd_lst_i-act': $parent.activeCompareGroupIndex() == $index()}"><a href="#" class="js-compare-typeLink" data-bind="attr: {'data-index': $index}"><!-- ko text: type.name --><!-- /ko --><span class="cmprHd_lst_qn"> <!-- ko text: products().length --><!-- /ko --></span></a></li>
                    <!-- /ko -->
                </ul>
            </div>
        </div>

        <div class="cmprCntW js-compare-content">
            <!-- ko if: compareGroups().length -->
            <div data-bind="visible: compareGroups().length" style="display: none;">
                <!-- ko if: compareGroups()[activeCompareGroupIndex()] -->
                <table class="cmprCnt clearfix js-compare-table" data-bind="css: {'cmprCnt_similarOnly': similarOnly, 'cmprCnt-scroll': scrolled}">
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
                        <td class="cmprCnt_product">
                            <div class="cmprCnt_fixed cmprCnt_modeW js-compare-fixed cmprCnt_cell">
                                <a href="" class="clsr js-compare-deleteProductLink" data-bind="attr: {href: deleteFromCompareUrl, 'data-product-id': id}"></a>

                                <a class="cmprCnt_img" href="" data-bind="attr: {href: link}">
                                    <img src="" class="descrImg_img" data-bind="attr: {src: imageUrl}">
                                </a>

                                <!-- ko if: reviews.count -->
                                <div class="cmprCnt_rating">
                                    <!-- ko foreach: {data: reviews.stars.notEmpty} -->
                                    <img src="/images/reviews_star.png" class="cmprCnt_rating_img">
                                    <!-- /ko -->

                                    <!-- ko foreach: {data: reviews.stars.empty} -->
                                    <img src="/images/reviews_star_empty.png" class="cmprCnt_rating_img">
                                    <!-- /ko -->

                                    <span class="cmprCnt_rating_count">(<!-- ko text: reviews.count --><!-- /ko -->)</span>
                                </div>
                                <!-- /ko -->

                                <!-- ko if: !reviews.count -->
                                <div class="cmprCnt_noRating">
                                </div>
                                <!-- /ko -->

                                <div class="cmprCnt_pt">
                                    <a href="" class="cmprCnt_cat" data-bind="attr: {href: link}"><!-- ko text: prefix --><!-- /ko --></a>
                                    <a href="" class="cmprCnt_n" data-bind="attr: {href: link}"><!-- ko text: webName --><!-- /ko --></a>
                                </div>

                                <!-- ko if: priceOld != '0' -->
                                                <span class="cmprCnt_price cmprCnt_price-l">
                                                    <span data-bind="html: priceOld"></span>
                                                    <span class="rubl">p</span>
                                                </span>
                                <!-- /ko -->

                                            <span class="cmprCnt_price">
                                                <span data-bind="html: price"></span>
                                                <span class="rubl">p</span>
                                            </span>

                                <div class="cmprCnt_buy"><a href="" class="cmprCnt_buy_lk btnBuy__eLink mBought" data-bind="attr: {
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
                                                'data-color-class': colorClass
                                            }, buyButtonBinding: $root.cart">Купить</a></div>
                            </div>
                        </td>
                        <!-- /ko -->
                    </tr>

                    <!-- ko foreach: {data: compareGroups()[activeCompareGroupIndex()].propertyGroups, as: 'propertyGroup'} -->
                    <tr class="cmprCnt_property cmprCnt_property_group" data-bind="attr: {'data-property-group-index': $index}, css: {'cmprCnt_property_similar': isSimilar, 'cmprCnt_property_group-act': propertyGroup.opened}">
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
                    <tr class="cmprCnt_property cmprCnt_property_item" data-bind="css: {'cmprCnt_property_similar': isSimilar, 'cmprCnt_property_group-hide': !propertyGroup.opened()}">
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
            <div class="cmprEmpty" data-bind="visible: !compareGroups().length" style="display: none;">
                <strong>Товаров для сравнения пока нет.</strong>
                <p><span style="display: inline-block;">Добавляйте товары к сравнению кнопкой</span> <span class="btnCmprb"></span></p>
            </div>
            <!-- /ko -->
        </div>
    </div>
<? } ?>