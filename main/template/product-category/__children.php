<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    array $promoStyle = [],
    array $relatedCategories = [],
    array $categoryConfigById = [],
    \Iterator\EntityPager $productPager = null,
    $category_class = null
) {

    $links = [];
    $categories = $category->getChild();
    if (!empty($relatedCategories))  {
        $categories = array_merge($categories, $relatedCategories);
    }

    foreach ($categories as $child) {
        $image_size = 'furniture' === $category_class ? 3 : 0;

        $link = [
            'name'   => $child->getName(),
            'url'    => $child->getLink() . ($category->isV2() && \App::request()->getQueryString() ? '?' . \App::request()->getQueryString() : ''),
            'image'  => $child->getImageUrl($image_size),
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

    if (!$category->isShowFullChildren()) {
        $templatePath = 'product-category/v2/_children';
    } else if ('furniture' === $category_class) {
        $templatePath = 'furniture/product-category/_listInFilter';
    } else {
        $templatePath = 'product-category/_listInFilter';
    }
?>

    <?= $helper->renderWithMustache($templatePath, [
        'links' => $links,
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>

<? };