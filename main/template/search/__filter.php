<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl,
    array $categories,
    \Model\Product\Category\Entity $selectedCategory = null
) { ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'          => $baseUrl,
        'countUrl'         => $countUrl,
        'productFilter'    => $productFilter,
        'categories'       => $categories,
        'openFilter'    => true,
    ]) // фильтры ?>

<? };