<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl
) {
    if (!(bool)$productFilter->getFilterCollection()) {
        return '';
    }
?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       => $baseUrl,
        'countUrl'      => $countUrl,
        'productFilter' => $productFilter,
        'hotlinks'      => [],
    ]) // фильтры ?>

<? };