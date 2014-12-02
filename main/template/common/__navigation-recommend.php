<?php

$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
?>

<div class="navitem">
    <div class="navitem_tl">ТОВАР ДНЯ</div>
    <a href="" class="navitem_cnt">
        <img src="<?= $product->getImageUrl(3) ?>" alt="<?= $helper->escape($product->getName()) ?>" class="navitem_img">
        <span class="navitem_n"><?= $product->getName() ?></span>
    </a>

    <div class="navitem_pr">
        <?= $helper->formatPrice($product->getPrice())?> <span class="rubl">p</span>
    </div>
</div>

<? }; return $f;