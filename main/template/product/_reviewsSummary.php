<?
  $full = 100; // longest bar, px
  $maxUser = max($reviewsDataSummary['user']);

  if(!empty($layout) && $layout == 'jewel') {
    $reviewsStarSrc = "/images/jewel/reviews_star.png";
  } else {
    $reviewsStarSrc = "/images/reviews_star.png";
  }
?>
<? if(!empty($reviewsData['review_list'])) { ?>

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

    <div class="bReviewsSummary__eCol bReviewsSummary__eColStar">
      <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
        <div class="bcolStarRow clearfix">
          <? for ($i=0; $i < $numStars; $i++) { ?>
            <img src="<?= $reviewsStarSrc ?>" />
          <? } ?>
        </div>
      <? } ?>
    </div>
<? } ?>