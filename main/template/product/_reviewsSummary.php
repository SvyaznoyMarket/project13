<?
  $full = 100; // longest bar, px
  $maxUser = max($reviewsDataSummary['user']);
  $maxPro = max($reviewsDataSummary['pro']);
?>
<div id="reviewsRatingWrapper" class="fr width500">
  <div class="width181 fl">
    <div>
      <div class="reviewsBarCount width50 fl">&nbsp;</div>
      <div class="bold fl">Пользователи</div>
      <div class="clear pb5"></div>
    </div>
    <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
      <div class="reviewsBarCount width50 fl"><?= $count ?></div>
      <div class="reviewsBar fl" style="width:<?= $count/$maxUser*$full ?>px;"></div>
      <div class="clear pb5"></div>
    <? } ?>
  </div>

  <div class="width181 fl">
    <div>
      <div class="reviewsBarCount width50 fl">&nbsp;</div>
      <div class="bold fl">Эксперты</div>
      <div class="clear pb5"></div>
    </div>
    <? foreach ($reviewsDataSummary['pro'] as $numStars => $count) { ?>
      <div class="reviewsBarCount width50 fl"><?= $count ?></div>
      <div class="reviewsBar fl" style="width:<?= $count/$maxPro*$full ?>px;"></div>
      <div class="clear pb5"></div>
    <? } ?>
  </div>

  <div class="width117 fl">
    <div class="pb5">&nbsp;</div>
    <? foreach ($reviewsDataSummary['user'] as $numStars => $count) { ?>
      <div class="pb5">
        <? for ($i=0; $i < $numStars; $i++) { ?>
          <img src="/images/reviews_star.png">
        <? } ?>
      </div>
    <? } ?>
  </div>

</div>
<div class="clear mb25"></div>