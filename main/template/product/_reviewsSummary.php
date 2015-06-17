<?
$full = 100; // longest bar, px
$maxUser = max($reviewsDataSummary);
?>
<? if (!empty($reviewsData['review_list'])): ?>
    <div class="bReviewsSummary__eCol">
        <strong>Пользователи</strong>
        <? foreach ($reviewsDataSummary as $numStars => $count) { ?>
            <div class="reviewsBarCount"><?= $count ?></div>
            <div class="reviewsBar reviewsBarBg" style="width:<?= $full ?>px;">
              <div class="reviewsBar" style="width:<?= $maxUser ? $count/$maxUser*$full : 0 ?>px;"></div>
            </div>
        <? } ?>
    </div>

    <div class="bReviewsSummary__eCol bReviewsSummary__eColStar">
        <? foreach ($reviewsDataSummary as $numStars => $count) { ?>
            <div class="bcolStarRow clearfix">
              <? for ($i=0; $i < $numStars; $i++) { ?>
                  <span class="star star-fill"></span>
              <? } ?>
            </div>
        <? } ?>
    </div>
<? endif ?>