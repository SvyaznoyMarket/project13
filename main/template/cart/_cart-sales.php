<?
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var [] $sales
 */
if (!\App::user()->getEntity() || !$sales) return '';
?>

<div class="s-sales jsKnockoutCart" style="display: none" data-bind="visible: cart().hasAvailableProducts().length == 0">

    <!-- Сетка скидочных категорий -->
    <div class="s-sales-grid">
        <!--
            Строка с тремя ячейками, выста каждой ячейки 220 пиксел
            Модификатор grid-3cell cell-h-220
         -->
        <div class="s-sales-grid__row grid-3cell cell-h-220">

            <? foreach ($sales as $sale) : ?>
                <?= $page->render('closed-sale/partials/sale', ['sale' => $sale, 'imageType' => ClosedSaleEntity::MEDIA_SMALL]) ?>
            <? endforeach ?>

        </div>
        <!--END Конец строки -->
    </div>

    <div class="button-container">
        <a href="<?= $page->url('sale.all') ?>" class="button button_action button_size-l">Посмотреть текущие акции</a>
    </div>
</div>