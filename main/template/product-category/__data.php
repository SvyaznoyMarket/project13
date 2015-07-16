<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category
) {
    $data = [
        'id'    => $category->getId(),
        'ui'    => $category->getUi(),
        'token' => $category->getToken(),
        'name'  => $category->getName(),
    ];
?>
    <div id="jsProductCategory" data-value="<?= $helper->json($data) ?>"></div>
<? };