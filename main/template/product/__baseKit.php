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
            <span class="packageSetHead_change"><span class="packageSetHead_changeText js-kitButton" data-product-ui="<?= $helper->escape($product->getUi()) ?>" data-sender="<?= $helper->json($sender) ?>" data-sender2="<?= $helper->escape($sender2) ?>">Изменить комплектацию</span></span>
        <? endif ?>
    </div>

    <? foreach ($products as $p) : ?>
        <?
        /** @var \Model\Product\Entity $product */
        $product = $p['product'];
        ?>
        <div class="packageSetBodyItem">
            <a class="packageSetBodyItem_img rown" href="<?= $product->getLink() ?>"><img src="<?= $product->getMainImageUrl('product_120') ?>" /></a><!--/ изображение товара -->

            <div class="packageSetBodyItem_desc rown">
                <div class="name"><a class="" href="<?= $product->getLink() ?>"><?= $product->getName(); ?></a></div><!--/ название товара -->

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
                <?= $helper->formatPrice($product->getPrice()) ?>&nbsp;<span class="rubl">p</span>
            </div><!--/ цена -->

            <div class="packageSetBodyItem_qnt rown"><?= $p['count'] ?> шт.</div><!--/ количество в наборе -->
        </div><!--/ элемент комплекта -->
    <? endforeach; ?>
    <!-- элемент комплекта -->

</div>
<!--/ Состав комплекта -->

<? }; ?>