<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Slice\Entity $slice,
    \Model\Product\Category\Entity $category = null
) {
    $data = [
        'token'    => $slice->getToken(),
        'name'     => $slice->getName(),
        'isSale'   => 'all_labels' === $slice->getToken(),
        'category' =>
            $category
            ? [
                'name' => $category->name,
            ]
            : null
        ,
    ];
?>
    <div id="jsSlice" data-value="<?= $helper->json($data) ?>"></div>
<? };