<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    \Model\Brand\Entity $brand = null
) {

    $links = [];
    /** @var \Model\Product\Category\Entity[] $categories */
    $categories = $category->getAncestor();
    if ($brand) {
        $iCategory = clone $category;
        $iCategory->name = preg_replace('/' . $brand->name . '$/', '', $iCategory->name);
        $categories[] = $iCategory; // SITE-6369
    }

    $count = count($categories);
    $i = 1;
    foreach ($categories as $ancestor) {
        $isLast = $i == $count;

        $links[] = [
            'url'  => $ancestor->getLink(),
            'name' => $ancestor->getName(),
            'last' => $isLast,
        ];

        $i++;
    }
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };