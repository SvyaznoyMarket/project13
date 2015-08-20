<?php
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $products,
    $sender = [],
    $sender2 = ''
) { ?>

    <!-- набор-пакет -->
    <div class="product-section set-section clearfix" id="kit">
        <? if (!$product->getIsKitLocked() && !$product->isInShopStockOnly() && $product->getIsBuyable() && $product->getStatusId() != 5) : ?>
            <span class="set-section-change"><span class="set-section-change__txt js-kitButton" data-product-ui="<?= $helper->escape($product->getUi()) ?>" data-sender="<?= $helper->json($sender) ?>" data-sender2="<?= $helper->escape($sender2) ?>">Изменить комплектацию</span></span>
        <? endif ?>
        <div class="product-section__tl">Базовая комплектация набора</div>

        <!--список комплектующих-->
        <ul class="set-section-package">
            <? foreach ($products as $arrItem) : ?>

                <!-- элемент комплекта -->
                <li class="set-section-package-i">
                    <a class="set-section-package-i__img" href="<?= $arrItem['url'] ?>"><img src="<?= $arrItem['image'] ?>"></a><!--/ изображение товара -->

                    <div class="set-section-package-i__desc rown">
                        <div class="name"><a class="" href="<?= $arrItem['image'] ?>"><?= $helper->escape($arrItem['name']) ?></a></div><!--/ название товара -->

                    <? if ($arrItem['height']!='' || $arrItem['width']!='' || $arrItem['depth']!='') : ?>

                        <!-- размеры товара -->
                        <div class="column dimention">
                            <span class="dimention__name">Высота</span>
                            <span class="dimention__val"><?= $arrItem['height'] ?></span>
                        </div>

                        <div class="column dimention">
                            <span class="dimention__name">&nbsp;</span>
                            <span class="dimention__val separation">x</span>
                        </div>

                        <div class="column dimention">
                            <span class="dimention__name">Ширина</span>
                            <span class="dimention__val"><?= $arrItem['width'] ?></span>
                        </div>

                        <div class="column dimention">
                            <span class="dimention__name">&nbsp;</span>
                            <span class="dimention__val separation">x</span>
                        </div>

                        <div class="column dimention">
                            <span class="dimention__name">Глубина</span>
                            <span class="dimention__val"><?= $arrItem['depth'] ?></span>
                        </div>

                        <div class="column dimention">
                            <span class="dimention__name">&nbsp;</span>
                            <span class="dimention__val">см</span>
                        </div>
                        <!--/ размеры товара -->

                    <? endif ?>

                    </div>

                    <div class="set-section-package-i__price rown">
                        <?= $helper->formatPrice($arrItem['price']) ?>&nbsp;<span class="rubl">p</span>
                    </div><!--/ цена -->

                    <div class="set-section-package-i__qnt rown"><?= $arrItem['count'] ?> шт.</div><!--/ количество в наборе -->
                </li>
                <!-- элемент комплекта END -->
            <? endforeach ?>
        </ul>
        <!--список комплектующих END-->
    </div>

<? }; return $f;