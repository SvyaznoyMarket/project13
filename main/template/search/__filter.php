<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl,
    array $categories,
    \Model\Product\Category\Entity $selectedCategory = null
) {
    if (!(bool)$productFilter->getFilterCollection()) {
        return '';
    }
?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'          => $baseUrl,
        'countUrl'         => $countUrl,
        'productFilter'    => $productFilter,
        'hotlinks'         => [],
        'categories'       => $categories,
        'selectedCategory' => $selectedCategory,
    ]) // фильтры ?>

<? };