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
    <a class="s-sales-grid__link" href="<?= $page->url('product.category', ['categoryPath' => $category->getPath() ]) ?>">
        <img src="<?= $category->getMediaSource($imageType, 'category_grid')->url ?>" alt="" class="s-sales-grid__img">
        <span class="s-sales-grid-desc">
            <span class="s-sales-grid-desc__title">
                <span class="s-sales-grid-desc__title-name"><?= $category->getName() ?></span>
            </span>
        </span>
    </a>
</div>