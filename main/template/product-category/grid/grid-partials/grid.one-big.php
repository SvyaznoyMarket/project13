<?
use Model\Product\Category\Entity as Category;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var Category[] $categories
 */
?>
<!--
    Строка во всю ширину сетки
 -->
<div class="s-sales-grid s-sales-grid--category">
    <div class="s-sales-grid__row">
        <?= $page->render('product-category/grid/grid-partials/item', ['category' => $categories[0], 'imageType' => Category::MEDIA_GRID_HUGE]) ?>
    </div>
</div>