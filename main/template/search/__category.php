<?php

return function (
    \Helper\TemplateHelper $helper,
    $searchQuery,
    array $categories,
    \Model\Product\Category\Entity $selectedCategory = null
) {
    $selectedId = $selectedCategory ? $selectedCategory->getId() : null;

    /** @var $categories \Model\Product\Category\Entity[] */
    $links = [];
    foreach ($categories as $category) {
        $links[] = [
            'name'   => $category->getName(),
            'url'    => $category->getLink(),//$helper->url('search', ['q' => $searchQuery, 'category' => $category->getId()]),
            'image'  => $category->getImageUrl(),
            'active' => $category->getId() === $selectedId,
        ];
    }

?>

    <?= $helper->renderWithMustache('product-category/_listInFilter', ['links' => $links]) ?>

<? };