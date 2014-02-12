<?php
/**
 * @var $page    \View\Product\IndexPage
 * @var $product \Model\Product\Entity
 */

// стр. товара
$data = [
    'page'              => 'product',
    'productId'         => $product->getId(),   // где ХХ – это ID товара в каталоге рекламодателя.
];
?>
<div id="xcntmyAsync" class="jsanalytics" data-value="<?= $page->json($data) ?>"></div>
