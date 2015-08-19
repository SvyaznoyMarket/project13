<?php
$f = function(
    \Helper\TemplateHelper $helper,
    $reviewsData = []
){
    if (!isset($reviewsData['num_reviews']) || $reviewsData['num_reviews'] == 0) return '';
?>
    <!-- Оценка и количество отзывово -->

    <div class="product-card-rating rating">
                    <span class="rating-state">
                        <?= $helper->render('product/blocks/reviews._stars', ['stars' => $reviewsData['avg_star_score']]) ?>
                    </span>

        <a href="#reviews"><span class="rating-count dotted"><?= $helper->numberChoiceWithCount($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span></a>
    </div>

    <!--/ Оценка и количество отзывово -->

<? }; return $f;