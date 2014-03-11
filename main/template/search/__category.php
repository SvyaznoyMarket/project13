<?php

return function (
    \Helper\TemplateHelper $helper,
    $searchQuery,
    array $categories,
    array $configs,
    \Model\Product\Category\Entity $selectedCategory = null
) {
    $selectedId = $selectedCategory ? $selectedCategory->getId() : null;

    /** @var $categories \Model\Product\Category\Entity[] */
    $links = [];
    foreach ($categories as $category) {
        $config = !empty($configs[$category->getId()]) ? $configs[$category->getId()] : [];

        $links[] = array_merge([
            'name'   => $category->getName(),
            'url'    => $helper->url('search', ['q' => $searchQuery, 'category' => $category->getId()]),
            'image'  => $category->getImageUrl(),
            'active' => $category->getId() === $selectedId,
        ], $config);
    }

?>

    <?= $helper->renderWithMustache('product-category/_listInFilter', ['links' => $links]) ?>

<? };