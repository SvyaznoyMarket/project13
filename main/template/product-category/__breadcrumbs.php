<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    \Model\Brand\Entity $brand = null
) {

    $links = [];
    /** @var \Model\Product\Category\Entity[] $categories */
    $categories = array_merge($category->getAncestor(), [$category]);
    $count = count($categories);
    $i = 1;
    foreach ($categories as $ancestor) {
        $isLast = $i == $count;

        $links[] = [
            'url'  => $isLast ? null : $ancestor->getLink(),
            'name' => $ancestor->getName(),
            'last' => $isLast,
        ];

        $i++;
    }
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };