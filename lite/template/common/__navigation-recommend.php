<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @param array|null $sender
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $sender = null
) {
    $link = $helper->url('product', [
        'productPath' => $product->getPath(),
        'sender'      => [
            'name'     => @$sender['name'],
            'method'   => @$sender['method'],
            'position' => @$sender['position'],
        ],
    ]);

    ?>

    <div class="menu-wow">
        <div class="goods js-module-require"
             data-module="enter.product"
             data-id="<?= $product->getId() ?>"
             data-product='<?= json_encode([
                 'id'   => $product->getId(),
                 'ui'   => $product->getUi()
             ], JSON_HEX_APOS) ?>'
            >
            <div class="sticker sticker_sale">Товар дня</div>

            <a href="<?= $link ?>" class="goods__img">
                <img src="<?= $product->getMainImageUrl('product_120') ?>"
                     alt="<?= $helper->escape($product->getName()) ?>"
                     class="goods__img-image">
            </a>

            <div class="goods__name">
                <div class="goods__name-inn">
                    <a href="<?= $link ?>"><?= $helper->escape($product->getName()) ?></a>
                </div>
            </div>

            <? if ($product->getPriceOld()) : ?>
                <div class="goods__price-old">
                    <span class="line-through"><?= $helper->formatPrice($product->getPriceOld())?></span>&thinsp;<span class="rubl">C</span>
                </div>
            <? endif ?>

            <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice())?>&thinsp;<span class="rubl">C</span></div>

            <?= $helper->render('product/_button.buy', [
                'product' => $product,
            ]) ?>

        </div>
    </div>

<? }; return $f;