<?php
/**
 * @var $page     \View\Product\IndexPage
 * @var $products \Model\Product\Entity[]
 * @var $newVersion bool
 */
?>

<?
if (!isset($newVersion)) $newVersion = false;
?>


<? if ($products && is_array($products)): ?>
    <p class="<? if ($newVersion): ?>bottom-content__p<? else: ?>bTags<? endif ?>">
        <? if ($newVersion): ?>
            <span class="bottom-content__tl">Похожие товары: </span>
        <? else: ?>
            <strong>Похожие товары: </strong>
        <? endif ?>

        <? $i = 0 ?>
        <? $count = count($products) ?>
        <? foreach ($products as $product): ?>
            <? $i++ ?>
            <a href="<?= $page->url('product', ['productPath' => $product->getPath()]) ?>" class="underline js-product-similarProducts-link"><?= $page->escape($product->getName()) ?></a><? if ($i < $count): ?>, <? endif ?>
        <? endforeach ?>
    </p>
<? endif ?>