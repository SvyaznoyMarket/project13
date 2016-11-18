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
            'url'  => $helper->url('slice', ['sliceToken' => $slice->getToken()]),
        ];
    }

    foreach ($category->getAncestor() as $ancestor) {
        $links[] = [
            'url'  => $ancestor->getLink(),
            'name' => $ancestor->getName(),
        ];
    }

    if ($category->getName()) {
        $links[] = [
            'url'  => $category->getLink(),
            'name' => $category->getName(),
        ];
    }

    $links = array_values($links);
    $links = array_map(function($link, $key) use ($links) {
        $link['last'] = ((count($links) - 1) == $key) ? true : false;

        return $link;
    }, $links, array_keys($links));
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };