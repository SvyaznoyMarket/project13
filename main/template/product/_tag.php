<?php
/**
 * @var $page    \View\Product\IndexPage
 * @var $product \Model\Product\Entity
 */
?>

<?
$tags = $product->getTag();
$brand = $product->getBrand();
$category = $product->getParentCategory();
?>

<? if ((bool)$tags || ($brand && $brand->getToken() && $category)): ?>
    <div class="pb25">
        <strong>Теги:</strong>
        <? if ($brand && $brand->getToken() && $category): ?>
            <a href="<?= $page->url('product.category.brand', ['categoryPath' => $category->getPath(), 'brandToken' => $brand->getToken()]) ?>" class="underline" rel="nofollow"><?= $category->getName() . ' ' . $brand->getName() ?></a><? if ((bool)$tags): ?>, <? endif ?>
        <? endif ?>

        <noindex>
            <? $i = 0; $count = count($product->getTag()); foreach ($tags as $tag): $i++ ?>
                <a href="<?= $page->url('tag', ['tagToken' => $tag->getToken()]) ?>" class="underline" rel="nofollow"><?= $tag->getName() ?></a><? if ($i < $count) echo ', ' ?>
            <? endforeach ?>
        </noindex>
    </div>
<? endif ?>