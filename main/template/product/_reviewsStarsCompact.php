<?
  $avgStarScore = $product->getAvgStarScore();
  $numReviews = $product->getNumReviews();
?>
<div class="pt10 pb10">
  <?= $page->render('product/_starsFive', ['score' => $avgStarScore]) ?>
  <? if(!empty($avgStarScore)) { ?>
      <? if(!empty($twoLines)) { ?>
        <br>
      <? } ?>
      <span class="gray">(<?= $numReviews ?> <?= $page->helper->numberChoice($numReviews, array('отзыв', 'отзыва', 'отзывов')) ?>)</span>
  <? } elseif(!empty($showNoReviews)) { ?>
      <? if(!empty($twoLines)) { ?>
        <br>
      <? } ?>
      <span class="gray">(отзывов нет)</span>
  <? } ?>
</div>
