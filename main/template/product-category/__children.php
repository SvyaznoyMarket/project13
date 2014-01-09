<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    array $promoStyle = [],
    array $relatedCategories = []
) {

    $links = [];
    $categories = $category->getChild();
    if (!empty($relatedCategories)) $categories = array_merge($categories, $relatedCategories);

    foreach ($categories as $child) {
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
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>

<? };