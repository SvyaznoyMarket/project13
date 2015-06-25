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
        <div class="product-section__tl">Базовая комплектация набора</div>
        <? if (!$product->getIsKitLocked() && !$product->isInShopStockOnly() && $product->getIsBuyable() && $product->getStatusId() != 5) : ?>
            <span class="packageSetHead_change"><span class="packageSetHead_changeText js-kitButton" data-product-ui="<?= $helper->escape($product->getUi()) ?>" data-sender="<?= $helper->json($sender) ?>" data-sender2="<?= $helper->escape($sender2) ?>">Изменить комплектацию</span></span>
        <? endif ?>
        <!--список комплектующих-->
        <ul class="set-section-package">
            <? foreach ($products as $arrItem) :
                /** @var \Model\Product\Entity $kitProduct */
                $kitProduct = $arrItem['product'];
                ?>

                <!-- элемент комплекта -->
                <li class="set-section-package-i">
                    <a class="set-section-package-i__img" href="<?= $kitProduct->getLink() ?>"><img src="<?= $kitProduct->getMainImageUrl('product_120') ?>"></a><!--/ изображение товара -->

                    <div class="set-section-package-i__desc rown">
                        <div class="name"><a class="" href="<?= $kitProduct->getMainImageUrl('product_120') ?>"><?= $kitProduct->getName(); ?></a></div><!--/ название товара -->

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
                        <?= $helper->formatPrice($kitProduct->getPrice()) ?>&nbsp;<span class="rubl">p</span>
                    </div><!--/ цена -->

                    <div class="set-section-package-i__qnt rown"><?= $arrItem['count'] ?> шт.</div><!--/ количество в наборе -->
                </li>
                <!-- элемент комплекта END -->
            <? endforeach ?>
        </ul>
        <!--список комплектующих END-->
    </div>

<? }; return $f;