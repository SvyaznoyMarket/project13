<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Slice\Entity $slice
) {
    $data = [
        'token'  => $slice->getToken(),
        'name'   => $slice->getName(),
        'isSale' => 'all_labels' === $slice->getToken(),
    ];
    ?>
    <div id="jsSlice" data-value="<?= $helper->json($data) ?>"></div>
<? };