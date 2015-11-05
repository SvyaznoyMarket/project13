<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    \Model\Brand\Entity $brand = null
) {

    $links = [];
    $count = count($category->getAncestor());
    $i = 1;
    foreach ($category->getAncestor() as $ancestor) {
        $links[] = [
            'url'  => $ancestor->getLink(),
            'name' => $ancestor->getName(),
            'last' => $i == $count && !$brand,
        ];

        $i++;
    }
    /* Если Товар + Бренд, то добавляем к крошкам текущую категорию */
    if ($brand) {
        $links[] = [
            'url'  => $helper->url('product.category.brand', ['categoryPath' => $category->getPath(), 'brandToken' => $brand->getToken()]),
            'name' => $category->getName(),
            'last' => true,
        ];
    }
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };