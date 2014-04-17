<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    array $promoStyle = [],
    array $relatedCategories = [],
    array $categoryConfigById = []
) {

    $links = [];
    $categories = $category->getChild();
    if (!empty($relatedCategories)) $categories = array_merge($categories, $relatedCategories);

    foreach ($categories as $child) {
        $config = isset($categoryConfigById[$child->getId()]) ? $categoryConfigById[$child->getId()] : null;

        $links[] = [
            'name'   => isset($config['name']) ? $config['name'] : $child->getName(),
            'url'    => $child->getLink(),
            'image'  => (is_array($config) && array_key_exists('image', $config) && !empty($config['image'])) ? $config['image'] : $child->getImageUrl(),
            'active' => false,
            'css'    => isset($config['css']) ? $config['css'] : null,
        ];
    }
?>

    <?= $helper->renderWithMustache('product-category/_listInFilter', [
        'links' => $links,
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>

<? };