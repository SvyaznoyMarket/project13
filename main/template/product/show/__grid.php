<?php

$f = function (
    \Model\Product\BasicEntity $product
) {
?>

    <?= $product->getName() ?>

<?
}; return $f;