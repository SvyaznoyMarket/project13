<?php
/**
 * @var $page     \View\Product\IndexPage
 * @var $products \Model\Product\Entity[]
 */
?>

<? if ($products && is_array($products)): ?>
    <p class="bottom-content__p">
        <span class="bottom-content__tl">Похожие товары: </span>

        <? $i = 0 ?>
        <? $count = count($products) ?>
        <? foreach ($products as $product): ?>
            <? $i++ ?>
            <a href="<?= $page->url('product', ['productPath' => $product->getPath()]) ?>" class="underline js-product-similarProducts-link"><?= $page->escape($product->getName()) ?></a><? if ($i < $count): ?>, <? endif ?>
        <? endforeach ?>
    </p>
<? endif ?>