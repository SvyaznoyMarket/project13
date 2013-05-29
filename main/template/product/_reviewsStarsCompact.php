<?
  $avgStarScore = $product->getAvgStarScore();
  $numReviews = $product->getNumReviews();
?>
<div class="pt10 pb10">
  <? if(!empty($avgStarScore)) { ?>
      <? for ($i=0; $i < (int)$avgStarScore; $i++) { ?>
        <img src="/images/reviews_star.png">
      <? } ?>
      <? if(ceil($avgStarScore) > $avgStarScore) { ?>
        <img src="/images/reviews_star_half.png">
      <? } ?>
      <? for ($i=5; $i > ceil($avgStarScore); $i--) { ?>
        <img src="/images/reviews_star_empty.png">
      <? } ?>
      <? if(!empty($twoLines)) { ?>
        <br>
      <? } ?>
      <span class="gray">(<?= $numReviews ?> <?= $page->helper->numberChoice($numReviews, array('отзыв', 'отзыва', 'отзывов')) ?>)</span>
  <? } else { ?>
      <? for ($i=0; $i < 5; $i++) { ?>
        <img src="/images/reviews_star_empty.png">
      <? } ?>
      <? if(!empty($twoLines)) { ?>
        <br>
      <? } ?>
      <span class="gray">(отзывов нет)</span>
  <? } ?>
</div>
