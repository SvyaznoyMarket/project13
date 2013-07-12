<?php

return function(
    \Model\Product\Entity $product
) {
    if (!\App::config()->adFox['enabled']) return '';

    $id = 'adfox400';
    if ($product->getLabel()) {
        switch ($product->getLabel()->getId()) {
            case \Model\Product\Label\Entity::LABEL_PROMO:
                $id = 'adfox400counter';
                break;
            case \Model\Product\Label\Entity::LABEL_CREDIT:
                $id = 'adfoxWowCredit';
                break;
            case \Model\Product\Label\Entity::LABEL_GIFT:
                $id = 'adfoxGift';
                break;
        }
    }
?>

<div id="<?= $id ?>" class="bAwardSection"></div>

<? };