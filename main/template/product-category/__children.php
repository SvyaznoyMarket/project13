<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    array $relatedCategories = [],
    array $categoryConfigById = [],
    $showFullChildren = true
) {

    $links = [];
    $categories = $category->getChild();
    if (!empty($relatedCategories))  {
        $categories = array_merge($categories, $relatedCategories);
    }

    foreach ($categories as $child) {
        $link = [
            'name'   => $child->getName(),
            'url'    => $child->getLink() . ($category->isV2() && \App::request()->getQueryString() ? '?' . \App::request()->getQueryString() : ''),
            'image'  => $child->getImageUrl(),
            'active' => false,
            'css'    => null,
        ];

        // подставляем данные с json-a
        if (is_array($categoryConfigById) && array_key_exists($child->getId(), $categoryConfigById)) {
            $config = $categoryConfigById[$child->getId()];

            $linkNew = [];
            if (array_key_exists('name', $config) && !empty($config['name'])) $linkNew['name'] = $config['name'];
            if (array_key_exists('css', $config) && !empty($config['css'])) {
                $linkNew['css'] = $config['css'];

                // если в json-е задана css-настройка с background-ом, то затираем изображение по умолчанию
                if (array_key_exists('link', $config['css']) && false !== strpos($config['css']['link'], 'background')) {
                    $linkNew['image'] = "";
                }
            }
            if (array_key_exists('image', $config) && !empty($config['image'])) $linkNew['image'] = $config['image'];

            $link = array_merge($link, $linkNew);
        }

        $links[] = $link;
    }

    if (!$showFullChildren) {
        $templatePath = 'product-category/v2/_children';
    } else {
        $templatePath = 'product-category/_listInFilter';
    }
?>

    <?= $helper->renderWithMustache($templatePath, [
        'links' => $links,
    ]) ?>

<? };