<?
  $avgStarScore = $product->getAvgStarScore();
  $numReviews = $product->getNumReviews();
?>
<div class="pt10 pb10">
  <? if(!empty($avgStarScore)) { ?>
      <?= $page->render('product/_starsFive', ['score' => $avgStarScore]) ?>
      <span class="gray">(<?= $numReviews ?>)</span>
  <? } else { ?>
      <img src="" height="16" width="1">
  <? } ?>
</div>
