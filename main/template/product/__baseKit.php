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

    <?php foreach ($products as $product) : ?>
        <div class="packageSetBodyItem">
            <a class="packageSetBodyItem_img" href="<?= $product['product']->getLink() ?>"><img src="<?= $product['product']->getImageUrl() ?>" /></a><!--/ изображение товара -->

            <div class="packageSetBodyItem_desc">
                <div class="name"><a class="" href="<?= $product['product']->getLink() ?>"><?= $product['product']->getName(); ?></a></div><!--/ название товара -->

                <!-- размеры товара -->
                <div class="column dimantion">
                    <span class="dimantion_name">Высота</span>
                    <span class="dimantion_val"><?= $product['Высота'] ?></span>
                </div>

                <div class="column separation">x</div>

                <div class="column dimantion">
                    <span class="dimantion_name">Ширина</span>
                    <span class="dimantion_val"><?= $product['Ширина'] ?></span>
                </div>

                <div class="column separation">x</div>

                <div class="column dimantion">
                    <span class="dimantion_name">Глубина</span>
                    <span class="dimantion_val"><?= $product['Глубина'] ?></span>
                </div>

                <div class="column dimantion">
                    <span class="dimantion_name">&nbsp;</span>
                    <span class="dimantion_val">см</span>
                </div>
                <!--/ размеры товара -->
            </div>

            <div class="packageSetBodyItem_delivery">
                Доставка <strong><?= $product['deliveryDate'] ?></strong>
            </div><!--/ доставка -->

            <div class="packageSetBodyItem_price">
                <?= $helper->formatPrice($product['product']->getPrice()) ?>&nbsp;<span class="rubl">p</span>
            </div><!--/ цена -->

            <div class="packageSetBodyItem_qnt"><?= $product['count'] ?> шт.</div><!--/ количество в наборе -->
        </div><!--/ элемент комплекта -->
    <?php endforeach; ?>
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
    <div class="packageSet mPackageSetEdit">
    <div class="packageSetHead cleared">
        <span class="packageSetHead_title">Уточните комплектацию</span>
    </div>

    <div class="packageSet_inner">

    <? foreach ($products as $product) : ?>

    <!-- элемент комплекта -->
    <div class="packageSetBodyItem">
        <a class="packageSetBodyItem_img" href="<?= $product['product']->getLink() ?>"><img src="<?= $product['product']->getImageUrl() ?>" /></a><!--/ изображение товара -->

        <div class="packageSetBodyItem_desc">
            <div class="name"><a class="<?= $product['product']->getLink() ?>" href=""><?= $product['product']->getName() ?></a></div><!--/ название товара -->

            <!-- размеры товара -->
            <div class="column dimantion">
                <span class="dimantion_name">Высота</span>
                <span class="dimantion_val"><?= $product['Высота'] ?></span>
            </div>

            <div class="column separation">x</div>

            <div class="column dimantion">
                <span class="dimantion_name">Ширина</span>
                <span class="dimantion_val"><?= $product['Ширина'] ?></span>
            </div>

            <div class="column separation">x</div>

            <div class="column dimantion">
                <span class="dimantion_name">Глубина</span>
                <span class="dimantion_val"><?= $product['Глубина'] ?></span>
            </div>

            <div class="column dimantion">
                <span class="dimantion_name">&nbsp;</span>
                <span class="dimantion_val">см</span>
            </div>
            <!--/ размеры товара -->

            <div class="column delivery">Доставка <strong><?= $product['deliveryDate'] ?></strong></div><!--/ доставка -->
        </div>

        <div class="bCountSection clearfix">
            <button class="bCountSection__eM">-</button>
            <input type="text" value="<?= $product['count'] ?>" class="bCountSection__eNum">
            <button class="bCountSection__eP">+</button>
            <span>шт.</span>
        </div>

        <div class="packageSetBodyItem_price">
            <?= $helper->formatPrice($product['product']->getPrice()) ?> <span class="rubl">p</span>
        </div><!--/ цена -->
    </div>
    <!--/ элемент комплекта -->

    <? endforeach ?>

    </div>
    </div>
    <!--/ Состав комплекта -->

    <div class="packageSetDefault bInputList">
        <input type="checkbox" name="" id="defaultSet" class="jsCustomRadio bCustomInput mCustomCheckBig">
        <label for="defaultSet" class="bCustomLabel mCustomLabelBig">Базовый комплект</label>
    </div>

    <div class="packageSetPrice">Итого за <?= count($products) ?> предметов: <strong><?= $helper->formatPrice($mainProduct->getPrice()) ?></strong> <span class="rubl">p</span></div>

    <div class="packageSetBuy btnBuy">
        <a class="btnBuy__eLink jsBuyButton" href="">Купить</a>
    </div>
    </div>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/knockout/3.1.0/knockout-min.js"></script>

<? }; ?>