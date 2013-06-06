<?
  $avgStarScore = $product->getAvgStarScore();
  $numReviews = $product->getNumReviews();
?>
<div class="reviewSection reviewSection12 pt10 pb10 clearfix">
  <? if(!empty($avgStarScore)) { ?>
      <div class="reviewSection12__star reviewSection__star"><?= $page->render('product/_starsFive', ['score' => $avgStarScore]) ?></div>
      <div class="reviewSection__link"><span class="gray">(<?= $numReviews ?>)</span></div>
  <? } else { ?>
      <img src="" height="16" width="1">
  <? } ?>
</div>
