<?php
$f = function (
    $stars = 0
){

    foreach (range(1,5) as $starIndex) : ?>

    <i class="rating-state__item <?= $starIndex <= $stars ? 'icon-rating rating-state__item_fill' : '' ?>"></i>

<? endforeach; }; return $f;