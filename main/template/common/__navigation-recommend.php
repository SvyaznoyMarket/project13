<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @param array|null $sender
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $sender = null
) {
    $link = $helper->url('product', [
        'productPath' => $product->getPath(),
        'sender'      => [
            'name'     => @$sender['name'],
            'method'   => @$sender['method'],
            'position' => @$sender['position'],
        ],
    ]);

?>

<div class="navitem">
    <div class="navitem_tl">ТОВАР ДНЯ</div>
    <a href="<?= $link ?>" class="navitem_cnt">
        <img src="<?= $product->getImageUrl(3) ?>" alt="<?= $helper->escape($product->getName()) ?>" class="navitem_img">
        <span class="navitem_n"><?= $product->getName() ?></span>
    </a>

    <div class="navitem_pr">
        <?= $helper->formatPrice($product->getPrice())?> <span class="rubl">p</span>
    </div>
</div>

<? }; return $f;