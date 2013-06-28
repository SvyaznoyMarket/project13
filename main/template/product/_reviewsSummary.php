<?
  $full = 100; // longest bar, px
  $maxUser = max($reviewsDataSummary['user']);
  $maxPro = max($reviewsDataSummary['pro']);

  if(!empty($layout) && $layout == 'jewel') {
    $reviewsStarSrc = "/images/jewel/reviews_star.png";
  } else {
    $reviewsStarSrc = "/images/reviews_star.png";
  }
?>
<? if(!empty($reviewsData['review_list']) || !empty($reviewsDataPro['review_list'])) { ?>
  <div id="reviewsRatingWrapper" class="reviewsRatingWrapper clearfix">
  <?php /*div id="reviewsRatingWrapper" class="<?= empty($alignRight) ? 'fl' : 'fr' ?> clearfix" */?>

    <? if(!empty($alignRight) && (empty($reviewsData['review_list']) || empty($reviewsDataPro['review_list']))) { ?>
      <div class="width181 fl">&nbsp;</div>
    <? } ?>

    <? if(!empty($reviewsData['review_list'])) { ?>
      <div class="reviewsRatingCol fl">
          <div class="reviewsRatingCol__title bold">Пользователи</div>
        <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
          <div class="reviewsBarCount fl"><?= $count ?></div>
          <div class="reviewsBar reviewsBarBg fl" style="width:<?= $full ?>px;">
            <div class="reviewsBar fl" style="width:<?= $maxUser ? $count/$maxUser*$full : 0 ?>px;"></div>
          </div>
        <? } ?>
      </div>
    <? } ?>

    <? if(!empty($reviewsDataPro['review_list'])) { ?>
      <div class="reviewsRatingCol fl">
          <div class="reviewsRatingCol__title bold">Эксперты</div>
        <? foreach ($reviewsDataSummary['pro'] as $numStars => $count) { ?>
          <div class="reviewsBarCount fl"><?= $count ?></div>
          <div class="reviewsBar reviewsBarBg fl" style="width:<?= $full ?>px;">
            <div class="reviewsBar fl" style="width:<?= $maxPro ? $count/$maxPro*$full : 0 ?>px;"></div>
          </div>
        <? } ?>
      </div>
    <? } ?>

    <div class="reviewsRatingWrapper__colstar fl">
      <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
        <div class="reviewsRatingWrapper__colstar__row">
          <? for ($i=0; $i < $numStars; $i++) { ?>
            <img src="<?= $reviewsStarSrc ?>">
          <? } ?>
        </div>
      <? } ?>
    </div>
  </div>
<? } ?>