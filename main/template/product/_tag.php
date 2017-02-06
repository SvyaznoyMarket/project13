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
    <p class="bottom-content__p">
        <span class="bottom-content__tl">Теги: </span>

        <? if ($brand && $brand->getToken() && $category): ?>
            <a href="<?= $page->url('product.category', ['categoryPath' => $category->getPath(), 'brandToken' => $brand->getToken()]) ?>" class="underline"><?= $category->getName() . ' ' . $brand->getName() ?></a><? if ((bool)$tags): ?>, <? endif ?>
        <? endif ?>

        <noindex>
            <? $i = 0; $count = count($tags); foreach ($tags as $tag): $i++ ?>
                <a href="<?= $page->url('tag', ['tagToken' => $tag->token]) ?>" class="underline" rel="nofollow"><?= $tag->name ?></a><? if ($i < $count) echo ', ' ?>
            <? endforeach ?>
        </noindex>
    </p>
<? endif ?>