<?php

return function(
    $score,
    $emptyText = null,
    $layout = null
) {
    if (!empty($layout) && $layout == 'jewel') {
        $reviewsStarSrc = "/images/jewel/reviews_star.png";
        $reviewsStarHalfSrc = "/images/jewel/reviews_star_half.png";
        $reviewsStarEmptySrc = "/images/jewel/reviews_star_empty.png";
    } else {
        $reviewsStarSrc = "/images/reviews_star.png";
        $reviewsStarHalfSrc = "/images/reviews_star_half.png";
        $reviewsStarEmptySrc = "/images/reviews_star_empty.png";
    }
?>

    <? if (empty($score) && empty($emptyText)) { ?>
        <? for ($i=5; $i > ceil($score); $i--) { ?>
            <img src="<?= $reviewsStarEmptySrc ?>" alt="*" />
        <? } ?>
    <? } elseif(empty($score) && !empty($emptyText)) { ?>
        <span class="gray"><?= $emptyText ?></span>
    <? } else { ?>
        <? for ($i=0; $i < (int)$score; $i++) { ?>
            <img src="<?= $reviewsStarSrc ?>" alt="*" />
        <? } ?>
        <? if(ceil($score) > $score) { ?>
            <img src="<?= $reviewsStarHalfSrc ?>" alt="*" />
        <? } ?>
        <? for ($i=5; $i > ceil($score); $i--) { ?>
            <img src="<?= $reviewsStarEmptySrc ?>" alt="*" />
        <? } ?>
    <? } ?>


<? };