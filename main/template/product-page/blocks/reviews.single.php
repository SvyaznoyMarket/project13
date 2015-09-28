<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Review\ReviewEntity $review,
    $hidden
){
    ?>

    <?= $review->isEnterReview() ? '' : '<noindex>' ?>

    <div class="reviews__i jsReviewItem" style="display: <?= $hidden ? 'none' : 'block' ?>" data-review-ui="<?= $review->ui ?>">
        <div class="reviews__cpt"><div class="reviews__author"><?= $review->author ? : 'Аноним' ?></div>,
            <div class="reviews__date"><?= \Util\Date::strftimeRu('%e %B2 %Y', $review->date->format('U')) ?></div></div>

        <div class="product-card-rating">
        <span class="product-card-rating__state">
            <?= $helper->render('product-page/blocks/reviews._stars', ['stars' => $review->scoreStar]) ?>
        </span>
        </div>

        <div class="reviews__tl">Достоинства:</div>
        <p class="reviews__tx"><?= $review->pros ?></p>

        <div class="reviews__tl">Недостатки:</div>
        <p class="reviews__tx"><?= $review->cons ?></p>

        <div class="reviews__tl">Комментарий:</div>
        <p class="reviews__tx"><?= $review->extract ?></p>
        <? if ($review->isMnogoRuReview()) : ?>
            <div class="reviews-src">
                <span class="reviews-src__tl">Источник:</span>
                <img src="/styles/product/img/mnogoru-sm.png"/>
            </div>
        <? endif ?>
        <? if ($review->isYandexReview()) : ?>
            <div class="reviews-src">
                <span class="reviews-src__tl">Источник:</span>
                <img src="/styles/product/img/yandex-sm.png"/>
            </div>
        <? endif ?>
        <div class="reviews-voting jsReviewVote" data-user-vote="<?= $review->userVote ?>">
            <div class="reviews-voting__tl">Полезный отзыв?</div>
            <span class="reviews-vote reviews-vote--positive <?= $review->userVote > 0 ?  'active' : null ?>jsReviewVoteBtn" data-vote="1"><?= $review->getPositiveCount() ?></span>
            <span class="reviews-vote reviews-vote--negative <?= $review->userVote < 0 ?  'active' : null ?>jsReviewVoteBtn" data-vote="-1"><?= $review->getNegativeCount() ?></span>
        </div>
    </div>

    <?= $review->isEnterReview() ? '' : '</noindex>' ?>

<? }; return $f;