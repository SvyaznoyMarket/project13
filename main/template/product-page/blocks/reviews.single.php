<?php
$f = function(
    \Helper\TemplateHelper $helper,
    array $review,
    $hidden
){
    ?>

    <div class="reviews__i jsReviewItem" style="display: <?= $hidden ? 'none' : 'block' ?>">
        <div class="reviews__cpt"><div class="reviews__author"><?= $review['author'] ? : 'Аноним' ?></div>,
            <div class="reviews__date"><?= \Util\Date::strftimeRu('%e %B2 %G', DateTime::createFromFormat('Y-m-d', $review['date'])->format('U')) ?></div></div>

        <div class="product-card-rating">
        <span class="product-card-rating__state">
            <?= $helper->render('product-page/blocks/reviews._stars', ['stars' => $review['star_score']]) ?>
        </span>
        </div>

        <div class="reviews__tl">Достоинства:</div>
        <p class="reviews__tx"><?= $review['pros'] ?></p>

        <div class="reviews__tl">Недостатки:</div>
        <p class="reviews__tx"><?= $review['cons'] ?></p>

        <div class="reviews__tl">Комментарий:</div>
        <p class="reviews__tx"><?= $review['extract'] ?></p>

        <div class="reviews-voting">
            <div class="reviews-voting__tl">Полезный отзыв?</div>
            <span class="reviews-vote reviews-vote--positive"><?= $review['useful_count'] ?></span>
            <span class="reviews-vote reviews-vote--negative"><?= $review['not_useful_count'] ?></span>
        </div>
    </div>

<? }; return $f;