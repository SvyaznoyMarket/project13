<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Slice\Entity $slice,
    \Model\Product\Category\Entity $category = null
) {
    $isFurniture = false;
    $isElectronics = false;
    $isHouseholdAppliances = false;

    $parent = $category;
    while ($parent) {
        if ('f7a2f781-c776-4342-81e8-ab2ebe24c51a' === $parent->getUi()) {
            $isFurniture = true;
        }
        if ('d91b814f-0470-4fd5-a2d0-a0449e63ab6f' === $parent->getUi()) {
            $isElectronics = true;
        }
        if ('616e6afd-fd4d-4ff4-9fe1-8f78236d9be6' === $parent->getUi()) {
            $isHouseholdAppliances = true;
        }

        $parent = $category->getParent();
    }

    $data = [
        'token'    => $slice->getToken(),
        'name'     => $slice->getName(),
        'isSale'   => 'all_labels' === $slice->getToken(),
        'category' =>
            $category
            ? [
                'name'                  => $category->name,
                'isFurniture'           => $isFurniture,
                'isElectronics'         => $isElectronics,
                'isHouseholdAppliances' => $isHouseholdAppliances,
            ]
            : null
        ,
    ];
?>
    <div id="jsSlice" data-value="<?= $helper->json($data) ?>"></div>
<? };