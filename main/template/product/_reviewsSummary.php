<?
  $full = 100; // longest bar, px
  $maxUser = max($reviewsDataSummary['user']);
  $maxPro = max($reviewsDataSummary['pro']);
?>
<? if(!empty($reviewsData['review_list']) || !empty($reviewsDataPro['review_list'])) { ?>

    <? if(!empty($reviewsData['review_list'])) { ?>
      <div class="bReviewsSummary__eCol">
          <strong>Пользователи</strong>
        <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
          <div class="reviewsBarCount"><?= $count ?></div>
          <div class="reviewsBar reviewsBarBg" style="width:<?= $full ?>px;">
            <div class="reviewsBar" style="width:<?= $maxUser ? $count/$maxUser*$full : 0 ?>px;"></div>
          </div>
        <? } ?>
      </div>
    <? } ?>

    <? if(!empty($reviewsDataPro['review_list'])) { ?>
      <div class="bReviewsSummary__eCol">
          <strong>Эксперты</strong>
        <? foreach ($reviewsDataSummary['pro'] as $numStars => $count) { ?>
          <div class="reviewsBarCount"><?= $count ?></div>
          <div class="reviewsBar reviewsBarBg" style="width:<?= $full ?>px;">
            <div class="reviewsBar" style="width:<?= $maxPro ? $count/$maxPro*$full : 0 ?>px;"></div>
          </div>
        <? } ?>
      </div>
    <? } ?>

    <div class="bReviewsSummary__eCol bReviewsSummary__eColStar">
      <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
        <div class="bcolStarRow">
          <? for ($i=0; $i < $numStars; $i++) { ?>
            <img src="/images/reviews_star.png">
          <? } ?>
        </div>
      <? } ?>
    </div>
<? } ?>