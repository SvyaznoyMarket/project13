<?php

return function (
    \Helper\TemplateHelper $helper,
    array $compareGroups
) { ?>

    <div class="wrapw js-compare" data-compare-groups="<?= $helper->json($compareGroups) ?>">
        <div class="cmprHd clearfix js-compare-header">
            <div class="cmprHd_l"><a href="/"><img src="/styles/compare/img/logo_cmpr.png" alt=""></a></div>

            <div class="cmprHd_r">
                <div class="cmprHd_t">
                    Сравнение товаров
                </div>

                <ul class="cmprHd_lst">
                    <!-- ko foreach: {data: compareGroups} -->
                    <li class="cmprHd_lst_i" data-bind="css: {'cmprHd_lst_i-act': $parent.activeCompareGroupIndex() == $index()}"><a href="#" class="js-compare-categoryLink" data-bind="attr: {'data-index': $index}"><!-- ko text: category.name --><!-- /ko --><span class="cmprHd_lst_qn"> <!-- ko text: products().length --><!-- /ko --></span></a></li>
                    <!-- /ko -->
                </ul>
            </div>
        </div>
    
        <div class="cmprCntW">
        <!-- ko if: compareGroups()[activeCompareGroupIndex()] -->  

            <table class="cmprCnt clearfix js-compare-table" data-bind="css: {'cmprCnt_similarOnly': similarOnly}">
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

                                <div class="cmprCnt_rating">
                                    <!-- ko foreach: {data: reviews.stars.notEmpty} -->
                                        <img src="/images/reviews_star.png" class="cmprCnt_rating_img">
                                    <!-- /ko -->
                                    
                                    <!-- ko foreach: {data: reviews.stars.empty} -->
                                        <img src="/images/reviews_star_empty.png" class="cmprCnt_rating_img">
                                    <!-- /ko -->

                                    <span class="cmprCnt_rating_count">(<!-- ko text: reviews.count --><!-- /ko -->)</span>
                                </div>

                                <div class="cmprCnt_pt">
                                    <a href="" class="cmprCnt_cat" data-bind="attr: {href: link}"><!-- ko text: prefix --><!-- /ko --></a>
                                    <a href="" class="cmprCnt_n" data-bind="attr: {href: link}"><!-- ko text: webName --><!-- /ko --></a>
                                </div>

                                <!-- ko if: priceOld != '0' -->
                                    <span class="cmprCnt_price cmprCnt_price-l">
                                        <!-- ko text: priceOld --><!-- /ko -->
                                        <span class="rubl">p</span>
                                    </span>
                                <!-- /ko -->

                                <span class="cmprCnt_price">
                                    <!-- ko text: price --><!-- /ko -->
                                    <span class="rubl">p</span>
                                </span>

                                <div class="cmprCnt_buy"><a href="" class="cmprCnt_buy_lk btnBuy__eLink mBought" data-bind="attr: {'data-product-id': id, 'data-in-shop-only': inShopOnly ? 'true' : 'false', 'data-in-shop-showroom-only': inShopShowroomOnly ? 'true' : 'false', 'data-is-buyable': isBuyable ? 'true' : 'false', 'data-status-id': statusId, 'data-upsale': upsale}, buyButtonBinding: $root.cart">Купить</a></div>
                            </div>
                        </td>
                    <!-- /ko -->
                </tr>

                <!-- ko foreach: {data: compareGroups()[activeCompareGroupIndex()].propertyGroups, as: 'propertyGroup'} -->
                    <tr class="cmprCnt_property cmprCnt_property_group js-compare-propertyGroup" data-bind="css: {'cmprCnt_property_similar': isSimilar}">
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
                        <tr class="cmprCnt_property cmprCnt_property_item" data-bind="css: {'cmprCnt_property_similar': isSimilar}">
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

            <div class="cmprEmpty">
                <strong>Товаров для сравнения пока нет.</strong>
                <p><span style="display: inline-block;">Добавляйте товары к сравнению кнопкой</span> <span class="btnCmprb"></span></p>
            </div>
        </div>
    </div>
    
    <footer class="footerw js-compare-footer">
        <p class="footerw_tx clearfix">&copy; ООО «Энтер» 2011&ndash;2014. ENTER&reg; ЕНТЕР&reg; Enter&reg;. Все права защищены. <a href="javascript:void(0)" class="footer__copy__link" id="jira">Сообщить об ошибке</a></p>
    </footer>
<? } ?>