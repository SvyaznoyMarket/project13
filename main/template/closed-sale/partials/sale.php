<?php
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var ClosedSaleEntity $sale
 * @var string $imageType
 */

if (!isset($imageType)) {
    $imageType = ClosedSaleEntity::MEDIA_FULL;
}

?>
<div class="s-sales-grid__cell">
    <a class="s-sales-grid__link" href="<?= $page->url('sale.one', ['uid' => $sale->uid ]) ?>">
        <img src="<?= $sale->getMedia()->getSource($imageType)->url ?>" alt="" class="s-sales-grid__img">
            <span class="s-sales-grid-desc">
                <span class="s-sales-grid-desc__value">-<?= $sale->discount ?>%</span>
                <span class="s-sales-grid-desc__title">
                    <span class="s-sales-grid-desc__title-name"><?= $sale->name ?></span>
                    <span class="s-sales-grid-desc__title-duration">Конец акции <?= $sale->endsAt->format('d.m.Y H:i') ?></span>
                </span>
            </span>
    </a>
</div>
