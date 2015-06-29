<?php
/**
 * @var $page    \View\Product\IndexPage
 * @var $product \Model\Product\Entity
 * @var $newVersion bool
 */
?>

<?
$tags = $product->getTag();
$brand = $product->getBrand();
$category = $product->getParentCategory();
?>

<? if ((bool)$tags || ($brand && $brand->getToken() && $category)): ?>
    <p class="<? if ($newVersion): ?>bottom-content__p<? else: ?>bTags<? endif ?>">
        <? if ($newVersion): ?>
            <span class="bottom-content__tl">Теги: </span>
        <? else: ?>
            <strong>Теги: </strong>
        <? endif ?>

        <? if ($brand && $brand->getToken() && $category): ?>
            <a href="<?= $page->url('product.category.brand', ['categoryPath' => $category->getPath(), 'brandToken' => $brand->getToken()]) ?>" class="underline"><?= $category->getName() . ' ' . $brand->getName() ?></a><? if ((bool)$tags): ?>, <? endif ?>
        <? endif ?>

        <noindex>
            <? $i = 0; $count = count($product->getTag()); foreach ($tags as $tag): $i++ ?>
                <a href="<?= $page->url('tag', ['tagToken' => $tag->getToken()]) ?>" class="underline" rel="nofollow"><?= $tag->getName() ?></a><? if ($i < $count) echo ', ' ?>
            <? endforeach ?>
        </noindex>
    </p>
<? endif ?>