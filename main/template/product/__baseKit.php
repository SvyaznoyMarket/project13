<?php
/**
 * @var $products array
 */
return function (
    \Helper\TemplateHelper $helper,
    array $products,
    \Model\Product\Entity $product,
    array $sender = [],
    $sender2 = ''
) {

?>

<!-- Состав комплекта -->
<div class="packageSet">
    <div class="packageSetHead cleared">
        <span class="packageSetHead_title">Базовая комплектация набора</span>
        <? if (!$product->getIsKitLocked() && !$product->isInShopStockOnly() && $product->getIsBuyable() && $product->getStatusId() != 5) : ?>
            <span class="packageSetHead_change"><span class="packageSetHead_changeText jsChangePackageSet">Изменить комплектацию</span></span>
        <? endif ?>
    </div>

    <? foreach ($products as $p) : ?>

        <? if ($p['lineName'] == 'baseLine') : ?>

        <div class="packageSetBodyItem">
            <a class="packageSetBodyItem_img rown" href="<?= $p['product']->getLink() ?>"><img src="<?= $p['product']->getImageUrl() ?>" /></a><!--/ изображение товара -->

            <div class="packageSetBodyItem_desc rown">
                <div class="name"><a class="" href="<?= $p['product']->getLink() ?>"><?= $p['product']->getName(); ?></a></div><!--/ название товара -->

                <? if ($p['height']!='' || $p['width']!='' || $p['depth']!='') : ?>

                <!-- размеры товара -->
                <div class="column dimantion">
                    <span class="dimantion_name">Высота</span>
                    <span class="dimantion_val"><?= $p['height'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val separation">x</span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">Ширина</span>
                    <span class="dimantion_val"><?= $p['width'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val separation">x</span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">Глубина</span>
                    <span class="dimantion_val"><?= $p['depth'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val">см</span>
                </div>
                <!--/ размеры товара -->

                <? endif; ?>
            </div>

            <div class="packageSetBodyItem_delivery rown <?= $p['deliveryDate'] == '' ? 'packageSetBodyItem_delivery-nodate' : ''?>">
                <? if ($p['deliveryDate'] == '') : ?>
                Уточните дату доставки в Контакт-сEnter
                <? else :?>
                Доставка <strong><?= $p['deliveryDate'] ?></strong>
                <? endif; ?>
            </div><!--/ доставка -->

            <div class="packageSetBodyItem_price rown">
                <?= $helper->formatPrice($p['product']->getPrice()) ?>&nbsp;<span class="rubl">p</span>
            </div><!--/ цена -->

            <div class="packageSetBodyItem_qnt rown"><?= $p['count'] ?> шт.</div><!--/ количество в наборе -->
        </div><!--/ элемент комплекта -->

        <? endif; ?>

    <? endforeach; ?>
    <!-- элемент комплекта -->

</div>
<!--/ Состав комплекта -->


<div class="popup packageSetPopup jsPackageSetPopup">
    <a href="" class="close"></a>

    <div class="bPageHead">
        <div class="bPageHead__eSubtitle"><?= $product->getPrefix() ?></div>
        <div class="bPageHead__eTitle clearfix">
            <h1 itemprop="name"><?= $product->getWebname() ?></h1>
        </div>
    </div>

    <div class="packageSetMainImg"><img src="<?= $product->getImageUrl(3) ?>" /></div>

    <!-- Состав комплекта -->
    <div class="packageSet mPackageSetEdit js-packageSetEdit" data-value="<?= $helper->json(['products' => $products, 'sender' => $sender, 'sender2' => $sender2]) ?>">

        <div class="packageSetHead cleared">
            <span class="packageSetHead_title">Уточните комплектацию</span>
        </div>

        <div class="packageSet_inner" data-bind="foreach: products">

            <div class="packageSetBodyItem" data-bind="css: { mDisabled: count() < 1 }">
                <a class="packageSetBodyItem_img" href="" data-bind="attr: { href : url }"><img src="" data-bind="attr: { src: image }" /></a><!--/ изображение товара -->

                <div class="packageSetBodyItem_desc">
                    <div class="name"><a class="" href="" data-bind="text: name, attr: { href : url }"></a></div><!--/ название товара -->

                    <div class="price"><span data-bind="html: prettyItemPrice"></span>&nbsp;<span class="rubl">p</span></div> <!-- Цена за единицу товара -->

                    <!-- размеры товара -->
                    <div class="column dimantion">
                        <span class="dimantion_name">Высота</span>
                        <span class="dimantion_val" data-bind="text: height"></span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val separation">x</span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Ширина</span>
                        <span class="dimantion_val" data-bind="text: width"></span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val separation">x</span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Глубина</span>
                        <span class="dimantion_val" data-bind="text: depth"></span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val">см</span>
                    </div>
                    <!--/ размеры товара -->

                    <div class="column delivery" data-bind="css: { 'delivery-nodate': deliveryDate() == '' } ">
                        <span class="dimantion_val" data-bind="if: deliveryDate() != '' ">Доставка <strong data-bind="text: deliveryDate()"></strong></span>
                        <span class="dimantion_val" data-bind="if: deliveryDate() == '' ">Уточните дату доставки в Контакт-сEnter</span>
                    </div><!--/ доставка -->
                </div>

                <div class="bCountSection clearfix">
                    <button class="bCountSection__eM" data-bind="click: minusClick, css: { mDisabled : count() == 0 }">-</button>
                    <input type="text" value="" class="bCountSection__eNum" data-bind="value: count, valueUpdate: 'input', event: { keydown: countKeydown, keyup: countKeyUp }">
                    <button class="bCountSection__eP" data-bind="click: plusClick, css: { mDisabled : count() == maxCount() }">+</button>
                    <span>шт.</span>
                </div>

                <div class="packageSetBodyItem_price">
                    <span data-bind="html: prettyPrice"></span>&nbsp;<span class="rubl">p</span>
                </div><!--/ цена -->
            </div>

        </div>

        <div class="packageSetFooter">
            <div class="packageSetDefault">
                <input type="checkbox" id="defaultSet" class="bInputHidden bCustomInput jsCustomRadio" data-bind="click: resetToBaseKit">
                <label for="defaultSet" class="packageSetLabel" data-bind="css: { mChecked : isBaseKit }, click: resetToBaseKit">Базовый комплект</label>
            </div>

            <div class="packageSetPrice">Итого за <span data-bind="text: totalCount"></span> предметов: <strong data-bind="html: totalPrice"></strong> <span class="rubl">p</span></div>

            <div class="packageSetBuy btnBuy">
                <a class="btnBuy__eLink jsBuyButton" href="" data-bind="css: { mDisabled: totalCount() == 0 }, attr: { href: buyLink, 'data-upsale': dataUpsale(<?= $product->getId() ?>) }">Купить</a>
            </div>
        </div>
    </div>
    <!--/ Состав комплекта -->

    
</div>

<? }; ?>