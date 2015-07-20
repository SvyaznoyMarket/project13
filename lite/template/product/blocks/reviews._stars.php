<?php
$f = function (
    $stars = 0
){

    foreach (range(1,5) as $starIndex) : ?>

    <i class="product-card-rating__i <?= $starIndex <= $stars ? 'product-card-rating__i--fill' : '' ?>"></i>

<? endforeach; }; return $f;