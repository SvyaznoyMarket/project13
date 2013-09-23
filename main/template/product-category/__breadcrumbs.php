<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category
) {

    $links = [];
    $count = count($category->getAncestor());
    $i = 1;
    foreach ($category->getAncestor() as $ancestor) {
        $links[] = [
            'url'  => $ancestor->getLink(),
            'name' => $ancestor->getName(),
            'last' => $i == $count,
        ];

        $i++;
    }
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };