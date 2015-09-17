<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $rowId
 * @param \Model\Product\Entity $product
 */
$f = function (
    \Helper\TemplateHelper $helper,
    $rowId,
    \Model\Product\Entity $product
) { ?>
    <div class="personal-favorit__item <?= $rowId ?>">
        <div class="personal-favorit__cell personal-favorit__choose">
            <input
                class="personal-favorit__checkbox"
                type="checkbox"
                id="<?= $rowId . '-input' ?>"
                data-type="product"
                data-product="<?= $helper->json([
                    'name'    => $product->getWebName(),
                    'barcode' => $product->barcode,
                ]) ?>"
                value="<?= $product->getUi() ?>"
            />
            <label for="<?= $rowId . '-input' ?>" class="personal-favorit__checkbox-icon"></label>
        </div>
        <div class="personal-favorit__cell personal-favorit__pic">
            <img src="<?= $product->getImageUrl(1) ?>">
        </div>
        <div class="personal-favorit__cell">
            <div class="personal-favorit__name"><?= $helper->escape($product->getName()) ?></div>
            <? if ($product->getIsBuyable()): ?>
                <div class="personal-favorit__status">В наличии</div>
            <? else: ?>
                <div class="personal-favorit__status unavailable">Нет в наличии</div>
            <? endif ?>
        </div>
        <div class="personal-favorit__cell">
            <div class="personal-favorit__price">
                <? if ($product->getPriceOld()): ?>
                    <span class="old-price"><span class="old-price__stroke"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></span>
                <? endif ?>
                <? if ($product->getPrice()): ?>
                    <?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span>
                <? endif ?>
            </div>
            <div class="personal-favorit__buy">
                <? if ($product->getIsBuyable()): ?>
                    <?= $helper->render('cart/__button-product', [
                        'product'  => $product,
                        'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                        'noUpdate'  => true,
                        'location' => 'user-favorites',
                    ]) // кнопка купить ?>
                <? endif ?>
            </div>
            <div class="personal-favorit__reminds">
            <? if (false): ?>
                <span class="remind-text">Сообщить</span>
                <a class="personal-favorit__price-change js-notification-link" href="<?= $helper->url('user.notification.addProduct', ['productId' => $product->getId(), 'channelId' => '2']) ?>">
                    <span class="personal__hint">о снижении цены</span>
                </a>
                <div class="personal-favorit__stock">
                    <a class="personal__hint">о наличии</a>
                </div>
            <? endif ?>
            </div>
        </div>
    </div>
<? }; return $f;