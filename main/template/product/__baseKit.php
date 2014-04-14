<?php
/**
 * @var $products array
 */
return function (
    \Helper\TemplateHelper $helper,
    array $products
) {

?>

<!-- Состав комплекта -->
<div class="packageSet">
    <div class="packageSetHead cleared">
        <span class="packageSetHead_title">Базовая комплектация набора</span>
        <span class="packageSetHead_change"><span class="packageSetHead_changeText">Изменить комплектацию</span></span>
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

                <div class="column separation">X</div>

                <div class="column dimantion">
                    <span class="dimantion_name">Ширина</span>
                    <span class="dimantion_val"><?= $product['Ширина'] ?></span>
                </div>

                <div class="column separation">X</div>

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

<? }; ?>