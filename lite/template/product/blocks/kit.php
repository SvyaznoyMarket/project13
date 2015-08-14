<?php
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $products,
    $sender = [],
    $sender2 = ''
) { ?>

    <!-- набор-пакет -->
    <div class="product-section set-section" id="kit">
        <? if (!$product->getIsKitLocked() && !$product->isInShopStockOnly() && $product->getIsBuyable() && $product->getStatusId() != 5) : ?>
            <span class="set-section-change js-kitButton" data-product-ui="<?= $helper->escape($product->getUi()) ?>" data-sender="<?= $helper->json($sender) ?>" data-sender2="<?= $helper->escape($sender2) ?>"><span class="dotted">Изменить комплектацию</span></span>
        <? endif ?>
        <div class="product-section__tl">Базовая комплектация набора</div>

        <!--список комплектующих-->
        <ul class="set-list">
            <? foreach ($products as $arrItem) :
                /** @var \Model\Product\Entity $kitProduct */
                $kitProduct = $arrItem['product'];
                ?>

                <!-- элемент комплекта -->
                <li class="set-list__item">
                    <a class="set-list__left set-list__img" href="<?= $kitProduct->getLink() ?>"><img src="<?= $kitProduct->getMainImageUrl('product_120') ?>"></a><!--/ изображение товара -->

                    <div class="set-list__right">
                        <div class="set-list__name"><a class="" href="<?= $kitProduct->getMainImageUrl('product_120') ?>"><?= $kitProduct->getName(); ?></a></div><!--/ название товара -->

                        <div class="set-list__desc table">
                            <div class="table-cell">
                                <? if ($arrItem['height']!='' || $arrItem['width']!='' || $arrItem['depth']!='') : ?>

                                    <!-- размеры товара -->
                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">Высота</span>
                                        <span class="set-list__dimention-val"><?= $arrItem['height'] ?></span>
                                    </div>

                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">&nbsp;</span>
                                        <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                    </div>

                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">Ширина</span>
                                        <span class="set-list__dimention-val"><?= $arrItem['width'] ?></span>
                                    </div>

                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">&nbsp;</span>
                                        <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                    </div>

                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">Глубина</span>
                                        <span class="set-list__dimention-val"><?= $arrItem['depth'] ?></span>
                                    </div>

                                    <div class="set-list__dimention">
                                        <span class="set-list__dimention-name">&nbsp;</span>
                                        <span class="set-list__dimention-val">см</span>
                                    </div>
                                    <!--/ размеры товара -->

                                <? endif ?>
                                &nbsp;
                            </div>

                            <div class="set-list__price table-cell">
                                <?= $helper->formatPrice($kitProduct->getPrice()) ?>&thinsp;<span class="rubl">C</span>
                            </div><!--/ цена -->

                            <div class="set-list__count table-cell"><?= $arrItem['count'] ?> шт.</div><!--/ количество в наборе -->
                        </div>
                    </div>
                </li>
                <!-- элемент комплекта END -->
            <? endforeach ?>
        </ul>
        <!--список комплектующих END-->
    </div>

<? }; return $f;