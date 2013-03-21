<?php
/**
 * @var $page    \Terminal\View\Product\IndexPage
 * @var $data[]
 * @var $products \Model\Product\TerminalEntity[]
 */
$productData = [];
?>

<div id="categoryData" data-data="<?= $page->json($data) ?>"></div>
<? foreach ($products as $product): ?>
    <? $productData[] = [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'image' => $product->getImageUrl(3),
        'article' => $product->getArticle(),
        'price' => $product->getPrice(),
        'isBuyable' => $product->getIsBuyable(\App::config()->region['shop_id']),
    ] ?>
<? endforeach ?>
<div id="productList" data-product="<?= $page->json($productData) ?>"></div>