<?php

$f = function (
    \Model\Product\BasicEntity $product
) {
?>

    <a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a>

<?
}; return $f;