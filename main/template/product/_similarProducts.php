<?php
/**
 * @var $page     \View\Product\IndexPage
 * @var $products \Model\Product\Entity[]
 */
?>


<? if ($products && is_array($products)): ?>
    <div class="bTags">
        <strong>Похожие товары:</strong>

        <? $i = 0 ?>
        <? $count = count($products) ?>
        <? foreach ($products as $product): ?>
            <? $i++ ?>
            <a href="<?= $page->url('product', ['productPath' => $product->getPath()]) ?>" class="underline js-product-similarProducts-link"><?= $product->getName() ?></a><? if ($i < $count): ?>, <? endif ?>
        <? endforeach ?>
    </div>
<? endif ?>