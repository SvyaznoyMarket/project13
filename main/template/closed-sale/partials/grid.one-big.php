<?
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var ClosedSaleEntity[] $sales
 */
?>
<!--
    Строка во всю ширину сетки
 -->
<div class="s-sales-grid__row">
    <?= $page->render('closed-sale/partials/sale', ['sale' => $sales[0], 'imageType' => ClosedSaleEntity::MEDIA_FULL]) ?>
</div>