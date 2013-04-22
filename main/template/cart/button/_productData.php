<?php
/**
 * @var $page    \View\Layout
 * @var $product \Model\Product\BasicEntity
 */

$data = [];
try {
    $data['article'] = $product->getArticle();
    $data['name'] = $product->getName();
    $data['price'] = $product->getPrice();
    $data['parentCategory'] = $product->getParentCategory() ? ['id' => $product->getParentCategory()->getId(), 'name' => $product->getParentCategory()->getName() ] : null;
    $data['rootCategory'] = $product->getMainCategory() ? ['id' => $product->getMainCategory()->getId(), 'name' => $product->getMainCategory()->getName()] : null;
} catch (\Exception $e) {
    \App::logger()->error($e);
}
?>data-product-data="<?= $page->json($data) ?>"