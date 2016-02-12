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
<div class="s-sales-grid__row s-sales-grid__row_alt">
    <?= $page->render('product-category/grid/grid-partials/item', ['category' => $categories[0], 'imageType' => Category::MEDIA_GRID_HUGE]) ?>
</div>