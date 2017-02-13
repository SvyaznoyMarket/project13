<?php
use Model\Product\Category\Entity as Category;
/**
 * @var Category $category
 * @var string $imageType
 */

if (!isset($imageType)) {
    $imageType = Category::MEDIA_GRID_BIG;
}

?>
<div class="s-sales-grid__cell">
    <a class="s-sales-grid__link jsCategoryGridLink" href="<?= $page->url('product.category', ['categoryPath' => $category->getPath() ]) ?>">
        <img src="<?= $category->getMediaSource($imageType, 'category_grid')->url ?>" alt="<?= \App::helper()->escape($category->getName()) ?>" class="s-sales-grid__img">
        <span class="s-sales-grid-desc">
            <span class="s-sales-grid-desc__title">
                <span class="s-sales-grid-desc__title-name"><?= $category->getName() ?></span>
                <span class="s-sales-grid-desc__title-product-count"><?= (new \Helper\TemplateHelper())->numberChoiceWithCount($category->getProductCount(), ['товар', 'товара', 'товаров'])?></span>
            </span>
        </span>
    </a>
</div>