<?php

return function(
    \Model\Product\Entity $product,
    array $reviewsData,
    \Helper\TemplateHelper $helper
) {
?>

<div class="bReviewSection clearfix">
    <div class="bReviewSection__eStar">
        <? $rating = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'] ?>
        <?= empty($rating) ? '' : $helper->render('product/__rating', ['score' => $rating]) ?>
    </div>
    <? if (!empty($rating)) { ?>
        <span class="jsGoToId border" data-goto="bHeadSectionReviews"><?= $reviewsData['num_reviews'] ?> <?= $helper->numberChoice($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
    <? } else { ?>
        <span>Отзывов нет</span>
    <? } ?>

    <span class="bReviewSection__eWrite jsLeaveReview" data-pid="<?= $product->getId() ?>">Оставить отзыв</span>

    <div style="position:fixed; top:40px; left:50%; margin-left:-442px; z-index:1002; display:none; width:700px; height:480px" class="reviewPopup popup clearfix">
        <a class="close" href="#">Закрыть</a>
        <iframe id="rframe" frameborder="0" scrolling="auto" height="480" width="700"></iframe>
    </div>
</div><!--/review section -->

<? };