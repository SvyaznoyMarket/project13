<?php
$f = function(
    \Helper\TemplateHelper $helper,
    $scores
){
    // Массив, который заполним правильными значениями ['0' => 0 ... '5' => 0]
    $scoreCounts = array_fill_keys(range(1,5), 0);

    foreach ($scores as $score) {
        $s = (int)($score['score']/2);
        if (isset($scoreCounts[$s])) $scoreCounts[$s] = $score['count'];
    }

    // Всего отзывов
    $totalScoresCount = array_sum(array_values($scoreCounts));

    ?>

<ul class="reviews-percentage-list">

    <? foreach (array_reverse($scoreCounts, true) as $key => $val) : ?>

        <li class="reviews-percentage-item">
            <div class="product-card-rating">
                    <span class="product-card-rating__state">
                        <?= $helper->render('product-page/blocks/reviews._stars', ['stars' => $key]) ?>
                    </span>

                <div class="product-card-rating-chart">
                    <span class="product-card-rating-chart__val" style="width: <?= 100 * $val / $totalScoresCount ?>px;"></span>
                </div>

                <span class="product-card-rating__val"><?= $val ?></span>
            </div>
        </li>

    <? endforeach ?>

</ul>

<? }; return $f;