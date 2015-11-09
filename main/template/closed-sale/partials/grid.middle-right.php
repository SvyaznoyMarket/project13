<?
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var ClosedSaleEntity[] $sales
 */
?>
<!--
    Строка в две колонки, широкая колонка справа.
    Модификатор grid-float grid-float-right
 -->
<div class="s-sales-grid__row grid-float grid-float-right">
    <div class="s-sales-grid__col">
        <?= $page->render('closed-sale/partials/sale', ['sale' => $sales[0], 'imageType' => ClosedSaleEntity::MEDIA_BIG]) ?>
    </div>

    <div class="s-sales-grid__col">
        <?= $page->render('closed-sale/partials/sale', ['sale' => $sales[1], 'imageType' => ClosedSaleEntity::MEDIA_SMALL]) ?>
        <?= $page->render('closed-sale/partials/sale', ['sale' => $sales[2], 'imageType' => ClosedSaleEntity::MEDIA_SMALL]) ?>
    </div>
</div>
<!--END Конец строки -->