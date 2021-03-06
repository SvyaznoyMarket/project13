<?
use Model\Product\Category\Entity as Category;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var Category[] $categories
 */
?>
<!--
    Строка в две колонки, широкая колонка справа.
    Модификатор grid-float grid-float-right
 -->
<div class="s-sales-grid s-sales-grid--category">
    <div class="s-sales-grid__row grid-float grid-float-right">
        <div class="s-sales-grid__col">
            <?= $page->render('product-category/grid/grid-partials/item', ['category' => $categories[0], 'imageType' => Category::MEDIA_GRID_BIG]) ?>
        </div>

        <div class="s-sales-grid__col">
            <?= $page->render('product-category/grid/grid-partials/item', ['category' => $categories[1], 'imageType' => Category::MEDIA_GRID_SMALL]) ?>
            <?= $page->render('product-category/grid/grid-partials/item', ['category' => $categories[2], 'imageType' => Category::MEDIA_GRID_SMALL]) ?>
        </div>
    </div>
</div>
<!--END Конец строки -->