<?php
$f = function(
    \Helper\TemplateHelper $helper,
    $reviewsData = []
){
    if (!isset($reviewsData['num_reviews']) || $reviewsData['num_reviews'] == 0) return '';
?>
    <!-- Оценка и количество отзывово -->

    <div class="product-card-rating">
                    <span class="product-card-rating__state">
                        <? foreach (range(1,5) as $starIndex) : ?>
                            <i class="product-card-rating__i
                            <?= isset($reviewsData['avg_star_score']) && $starIndex <= $reviewsData['avg_star_score'] ? 'product-card-rating__i--fill' : '' ?>"></i>
                        <? endforeach ?>
                    </span>

        <span class="product-card-rating__tx"><?= $helper->numberChoiceWithCount($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
    </div>

    <!--/ Оценка и количество отзывово -->

<? }; return $f;