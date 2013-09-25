<?
  $avgStarScore = $product->getAvgStarScore();
  $numReviews = $product->getNumReviews();
?>
<div class="bReviewSection mReviewSectionCat clearfix">
  <? if(!empty($avgStarScore)) { ?>
      <div class="bReviewSection__eStar"><?= $page->render('product/_starsFive', ['score' => $avgStarScore, 'layout' => empty($layout) ? false : $layout]) ?></div>
      <div class="bReviewSection__eLink"><span class="gray">(<?= $numReviews ?>)</span></div>
  <? } else { ?>
      <img src="" height="16" width="1">
  <? } ?>
</div>
