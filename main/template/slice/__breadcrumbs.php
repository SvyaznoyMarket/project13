<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    \Model\Slice\Entity $slice
) {
    if (!$category->getId()) {
        return;
    }

    $links = [];
    if (!$slice->getCategoryId()) {
        $links[] = [
            'name' => $slice->getName(),
            'url'  => $helper->url('slice.show', ['sliceToken' => $slice->getToken()]),
            'last' => false,
        ];
    }

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