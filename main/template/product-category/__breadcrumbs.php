<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    $isBrand = false
) {

    $links = [];
    $count = count($category->getAncestor());
    $i = 1;
    foreach ($category->getAncestor() as $ancestor) {
        $links[] = [
            'url'  => $ancestor->getLink(),
            'name' => $ancestor->getName(),
            'last' => $i == $count && !$isBrand,
        ];

        $i++;
    }
    /* Если Товар + Бренд, то добавляем к крошкам текущую категорию */
    if ($isBrand) {
        $links[] = [
            'url'  => $category->getLink(),
            'name' => $category->getName(),
            'last' => true,
        ];
    }
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };