<?php
/**
 * @var $page    \View\Product\StockPage
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 */
?>

<?= $page->render('product/form-oneClick') ?>

<?
$json = array('jsref' => $product->getToken(), 'jstitle' => $product->getName(), 'jsprice' => $product->getPrice(), 'jssimg' => $product->getImageUrl(1), 'jsimg' => $product->getImageUrl(3), 'jsbimg' => $product->getImageUrl(2), 'jsshortcut' => $product->getArticle(), 'jsitemid' => $product->getId(), 'jsregionid' => $user->getRegion()->getId(), 'jsregion' => $user->getRegion()->getName());
?>
<div class="clear"></div>
<input type="hidden" id="stockmodel"
    data-value="<?= $page->escape(json_encode($json)) ?>"
    link-output="<?= $page->url('order.1click', array('product' => $product->getToken())) ?>"
    link-input="<?= $page->url('product.delivery_1click') ?>"
/>

<div class='bInShop' id="noDlvr" style="display:none">
    <div class='bInShop__eTop'>
        <div class='bInShopError'>
            <h2>Сейчас товара нет в вашем городе,<br>но есть возможность купить его в другом городе :)</h2>

            <div class='bInShop__eSelectorsWrap'>
                <?= $user->getRegion()->getName() ?><br/>
                <a class='bGrayButton jsChangeRegion' href="">Другой город</a>
            </div>
            <div class='bInShop__eSelectorsLong'></div>
        </div>
    </div>
</div>

<div class="pb40" id="stockCntr">
    <div class='bOrderPreloader'>
        <span>Запрос данных...</span><img src='/images/bPreloader.gif' />
    </div>
    <div class='bInShop' id="stockBlock" style="display:none">
        <div class='bInShop__eTop'>
            <table cellpadding=0 cellspacing=0>
                <td class='bInShop__eItemInfo'>
                    <div>
                        <h3>Артикул #<span data-bind="text: shortcut"></span>,
                            <span data-bind="text: priceTxt"></span> <span class="rubl">p</span>
                        </h3>
                        Выберите магазин для резервирования товара, а мы подготовим его к вашему приезду
                    </div>
                    <div class='bInShop__eItemInfo_back'>
                        &lsaquo; <a href="<?= $product->getLink() ?>">Вернуться в карточку товара</a>
                    </div>
                </td>
                <!-- ko if:!showMap() -->
                <td class='bInShop__eItemIcon' data-bind="css: { mNoArrows : todayShops.length == 0 }">
                    <div class='bInShop__eLeftArrow'>
                        <div class='bInShop__eRightArrow'>
                            <img alt="" data-bind="attr: {src: icon}"/>
                        </div>
                    </div>
                </td>
                <!-- /ko -->
                <td class='bInShop__eSelectors' data-bind="css : { mLong: showMap() }"><!-- mLong optional -->
                    <!-- ko if: showMap -->
                    <div class="bInShop__eSelectorsWrap">
                        Заберу:
                        <br/>

                        <div class="bSelectors mDates">
                            <a href="" data-bind="text: todayLabel(),
												  click: function() { toggleTerm(true) },
												  css : { mChecked: today(), mGreen: today() } ">Сегодня</a>
                            <a href="" data-bind="text: tomorrowLabel(),
												  click: function() { toggleTerm(false) },
												  css : { mChecked: !today(), mBlue: !today() } ">Завтра</a>
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class='bInShop__eSelectorsWrap'>
                        Показать магазины:<br>

                        <div class='bSelectors'>
                            <a href="" data-bind="click: function() { toggleView(false) },
												  css : { mChecked: !showMap(), mOrange: !showMap()  } ">Список</a>
                            <a href="" data-bind="click: function() { toggleView(true) },
												  css : { mChecked: showMap(), mOrange: showMap()  } ">На карте</a>
                        </div>
                    </div>
                    <div class='bInShop__eSelectorsWrap'>
                        <span data-bind="text: region"></span><br/>
                        <a class='bGrayButton jsChangeRegion' href="">Другой город</a>
                    </div>
                    <div class='bInShop__eSelectorsLong'></div>
                </td>
            </table>
        </div>
        <div class='bInShop__eBottom'>


            <table cellpadding="0" cellspacing="0" data-bind="style: { display: showMap() ? 'table' : 'none' }">
                <tr>
                    <td>
                        <div class="bMapShops__eMapWrap" id="stockmap"></div>
                    </td>
                </tr>
            </table>
            <div id="infowindowforstock" data-bind="with: pickedShop()" style="display:none">
                <div class='bMapShops__ePopupRel'>
                    <h3 data-bind="text: address"></h3>
                    <span>Работает </span><span data-bind="text: regtime"></span><br>
                    <span class="shopnum" style="display:none" data-bind="text: id"></span>
                    <a href class='bGrayButton shopchoose'>Забрать из этого магазина</a>
                </div>
            </div>
            <div id="infowindowforstockYa" style="display:none">
                <div class='bMapShops__ePopupRel'>
                    <h3>$[properties.name]</h3>
                    <span>Работает </span><span>$[properties.regtime]</span><br>
                    <span class="shopnum" style="display:none">$[properties.id]</span>
                    <a href class='bGrayButton shopchoose'>Забрать из этого магазина</a>
                </div>
            </div>

            <!-- ko if: !showMap() -->
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <!-- ko if: todayShops.length > 0 -->
                    <td>
                        <h2 data-bind="html: todayH2"></h2>

                        <div class='bInShop__eListWrap'>
                            <!-- ko foreach : todayShops -->
                            <label class='bInShop__eList'
                                   data-bind="click: function( data ) { $root.chooseShop(data, true) } , css: { mChecked : $root.selectedS() == $data }">
                                <span class='bInShop__eDescription' data-bind="text: address"></span>
                            </label><br/>
                            <!-- /ko -->
                        </div>
                        <!-- ko if: selectedS().lbl == 'td' -->
                        <a class='bBigOrangeButton' href="" data-bind="click: reserveItem">Зарезервировать</a>
                        <!-- /ko -->
                    </td>
                    <!-- /ko -->
                    <!-- ko if: tomorrowShops.length > 0 -->
                    <td>
                        <h2 data-bind="html: tomorrowH2"></h2>

                        <div class='bInShop__eListWrap'>
                            <!-- ko foreach : tomorrowShops -->
                            <label class='bInShop__eList'
                                   data-bind="click: function( data ) { $root.chooseShop(data, false) } , css: { mChecked : $root.selectedS() == $data }">
                                <span class='bInShop__eDescription' data-bind="text: address"></span>
                            </label><br/>
                            <!-- /ko -->
                        </div>
                        <!-- ko if: selectedS().lbl == 'tmr' -->
                        <a class='bBigOrangeButton' href="" data-bind="click: reserveItem">Зарезервировать</a>
                        <!-- /ko -->
                    </td>
                    <!-- /ko -->
                </tr>
            </table>
            <!-- /ko -->

        </div>
    </div>

    <div class='bInShop__eNoTime' style="display:none" data-bind="visible: activeCourier">
        Некогда приезжать? <a href="" data-bind="click: onlyCourier">Купите этот товар с доставкой</a>
    </div>

</div>
<div class="clear"></div>
