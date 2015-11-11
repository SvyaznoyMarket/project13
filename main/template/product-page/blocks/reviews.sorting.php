<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @param \Model\Review\Sorting|null $sorting
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Review\Sorting $sorting = null
) {

    if (!$sorting) {
        $sorting = new \Model\Review\Sorting();
    }
?>

<div class="reviews-sort jsReviewsSorting">
    <span class="reviews-sort__tl">Сортировать:</span>
    <? foreach ($sorting->listByToken as $item): ?>
        <a href="#" class="js-review-update reviews-sort__btn reviews-sort__btn--val <?= $item->isActive ? 'active' : '' ?> <?= $item->direction ?>" data-url="<?= $helper->url('product.reviews', ['productUi' => $product->ui, 'sort' => $item->getSwitchValue()]) ?>"><?= $item->name ?></a>
    <? endforeach ?>
</div>

<?}; return $f;