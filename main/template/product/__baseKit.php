<?php
/**
 * @var $products array
 */
return function (
    \Helper\TemplateHelper $helper,
    array $products,
    $mainProduct
) {

?>

<!-- Состав комплекта -->
<div class="packageSet">
    <div class="packageSetHead cleared">
        <span class="packageSetHead_title">Базовая комплектация набора</span>
        <span class="packageSetHead_change"><span class="packageSetHead_changeText jsChangePackageSet">Изменить комплектацию</span></span>
    </div>

    <? foreach ($products as $product) : ?>

        <? if ($product['lineName'] == 'baseLine') : ?>

        <div class="packageSetBodyItem">
            <a class="packageSetBodyItem_img rown" href="<?= $product['product']->getLink() ?>"><img src="<?= $product['product']->getImageUrl() ?>" /></a><!--/ изображение товара -->

            <div class="packageSetBodyItem_desc rown">
                <div class="name"><a class="" href="<?= $product['product']->getLink() ?>"><?= $product['product']->getName(); ?></a></div><!--/ название товара -->

                <!-- размеры товара -->
                <div class="column dimantion">
                    <span class="dimantion_name">Высота</span>
                    <span class="dimantion_val"><?= $product['height'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val separation">x</span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">Ширина</span>
                    <span class="dimantion_val"><?= $product['width'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val separation">x</span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">Глубина</span>
                    <span class="dimantion_val"><?= $product['depth'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val">см</span>
                </div>
                <!--/ размеры товара -->
            </div>

            <div class="packageSetBodyItem_delivery rown"><!-- добавляем класс packageSetBodyItem_delivery-nodate если невозможно расчитать дату доставки -->
                Доставка <strong><?= $product['deliveryDate'] ?></strong>
                <!--Уточните дату доставки в Контакт-сEnter-->
            </div><!--/ доставка -->

            <div class="packageSetBodyItem_price rown">
                <?= $helper->formatPrice($product['product']->getPrice()) ?>&nbsp;<span class="rubl">p</span>
            </div><!--/ цена -->

            <div class="packageSetBodyItem_qnt rown"><?= $product['count'] ?> шт.</div><!--/ количество в наборе -->
        </div><!--/ элемент комплекта -->

        <? endif; ?>

    <? endforeach; ?>
    <!-- элемент комплекта -->

</div>
<!--/ Состав комплекта -->


<div class="popup packageSetPopup jsPackageSetPopup">
    <a href="" class="close"></a>

    <div class="bPageHead">
        <div class="bPageHead__eSubtitle"><?= $mainProduct->getPrefix() ?></div>
        <div class="bPageHead__eTitle clearfix">
            <h1 itemprop="name"><?= $mainProduct->getWebname() ?></h1>
        </div>
    </div>

    <div class="packageSetMainImg"><img src="<?= $mainProduct->getImageUrl(3) ?>" /></div>

    <!-- Состав комплекта -->
    <div class="packageSet mPackageSetEdit" data-value="<?= $helper->json($products) ?>">

        <div class="packageSetHead cleared">
            <span class="packageSetHead_title">Уточните комплектацию</span>
        </div>

        <div class="packageSet_inner" data-bind="foreach: products">

            <div class="packageSetBodyItem" data-bind="css: { mDisabled: count() < 1 }">
                <a class="packageSetBodyItem_img" href="" data-bind="attr: { href : url }"><img src="" data-bind="attr: { src: image }" /></a><!--/ изображение товара -->

                <div class="packageSetBodyItem_desc">
                    <div class="name"><a class="" href="" data-bind="text: name, attr: { href : url }"></a></div><!--/ название товара -->

                    <div class="price"><span data-bind="text: prettyItemPrice"></span>&nbsp;<span class="rubl">p</span></div> <!-- Цена за единицу товара -->

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

                    <div class="column delivery"><!-- добавляем класс модификатор delivery-nodate если невозможно расчитать дату доставки -->
                        <span class="dimantion_val">Доставка <strong data-bind="text: deliveryDate"></strong></span>
                        <!--span class="dimantion_val">Уточните дату доставки в Контакт-сEnter</span-->
                    </div><!--/ доставка -->
                </div>

                <div class="bCountSection clearfix">
                    <button class="bCountSection__eM" data-bind="click: minusClick, css: { mDisabled : count() == 0 }">-</button>
                    <input type="text" value="" class="bCountSection__eNum" data-bind="value: count, valueUpdate: 'input', event: { keydown: countKeydown, keyup: countKeyUp }">
                    <button class="bCountSection__eP" data-bind="click: plusClick, css: { mDisabled : count() == maxCount() }">+</button>
                    <span>шт.</span>
                </div>

                <div class="packageSetBodyItem_price">
                    <span data-bind="text: prettyPrice"></span>&nbsp;<span class="rubl">p</span>
                </div><!--/ цена -->
            </div>

        </div>

        <div class="packageSetFooter">
            <div class="packageSetDefault">
                <input type="checkbox" id="defaultSet" class="bInputHidden bCustomInput jsCustomRadio" data-bind="click: resetToBaseKit">
                <label for="defaultSet" class="packageSetLabel" data-bind="css: { mChecked : isBaseKit }, click: resetToBaseKit">Базовый комплект</label>
            </div>

            <div class="packageSetPrice">Итого за <span data-bind="text: totalCount"></span> предметов: <strong data-bind="text: totalPrice"></strong> <span class="rubl">p</span></div>

            <div class="packageSetBuy btnBuy">
                <a class="btnBuy__eLink jsBuyButton" href="" data-bind="css: { mDisabled: totalCount() == 0 }, attr: { href: buyLink, 'data-upsale': dataUpsale(<?= $mainProduct->getId() ?>) }">Купить</a>
            </div>
        </div>
    </div>
    <!--/ Состав комплекта -->

    
</div>

<? }; ?>