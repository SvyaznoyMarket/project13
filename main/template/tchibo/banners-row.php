<?php
/**
 * @var $promo \Model\Promo\Entity
 * @var $pages \Model\Promo\Page\Entity[]
 */

// показываем не больше 3 баннеров
$pages = array_slice($promo->getPages(), 0, 3);

?>

<!-- Блок скидок -->
<div class="s-sales-grid">

    <div class="s-sales-grid__row grid-3cell cell-h-220">

        <? foreach ($pages as $promoPage) : ?>

            <? $matches = []; preg_match('/\[(-?\d+)\](.*)/', $promoPage->getName(), $matches) ?>

            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="<?= $promoPage->getLink() ?>">
                    <img src="<?= $promoPage->getImageUrl() ?>" alt="" class="s-sales-grid__img">
                <span class="s-sales-grid-desc">
                    <? if ($matches) : ?>
                        <span class="s-sales-grid-desc__value"><?= $matches[1] ?></span>
                    <? endif ?>
                    <span class="s-sales-grid-desc__title">
                        <span class="s-sales-grid-desc__title-name"><?= $matches ? $matches[2] : $promoPage->getName() ?></span>
                    </span>
                </span>
                </a>
            </div>
        <? endforeach ?>

    </div>
</div>
<!-- END Блок скидок -->
