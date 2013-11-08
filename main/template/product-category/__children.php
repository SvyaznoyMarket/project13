<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    array $promoStyle = []
) {

    $links = [];
    foreach ($category->getChild() as $child) {
        $links[] = [
            'name'   => $child->getName(),
            'url'    => $child->getLink(),
            'image'  => $child->getImageUrl(),
            'active' => false,
        ];
    }
?>

    <?= $helper->renderWithMustache('product-category/_listInFilter', [
        'links' => $links,
        'bCatalogListStyle' => !empty($promoStyle['bCatalogList']) ? $promoStyle['bCatalogList'] : '',
        'bCatalogList__eItemStyle' => !empty($promoStyle['bCatalogList__eItem']) ? $promoStyle['bCatalogList__eItem'] : '',
    ]) ?>

<? };