<?php

return function(
    \Model\Product\Entity $product
) {
    if (!\App::config()->adFox['enabled']) return '';

    $adfox220 = 'adfox400';
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

<? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="<?= $adfox220 ?>"></div>
<? endif ?>

<? };