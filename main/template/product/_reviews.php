<ul class="bReviewsTabs clearfix">
  <li class="bReviewsTabs__eTab bReviewsTabs__eUser user<?= !empty($reviewsData['review_list']) ? ' active' : ' hfImportant' ?>" data-container="reviewsUser" data-reviews-type="user"><span>Отзывы пользователей</span></li>
  <?
    if(!empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = '';
    } elseif(empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = ' active';
    } else {
      $tabProClass = ' hfImportant';
    }
  ?>
  <li class="bReviewsTabs__eTab bReviewsTabs__ePro pro<?= $tabProClass ?>" data-container="reviewsPro" data-reviews-type="pro"><span>Обзоры экспертов</span></li>

  <? if (\App::config()->product['pushReview']): ?>
      <li class="jsLeaveReview bReviewsTabs__eTab bReviewsTabs__eLast" data-pid="<?= $product->getId() ?>">
        <span>Оставить отзыв</span>
      </li>
  <? endif ?>
</ul>

<? if(!empty($reviewsData['review_list'])) { ?>

  <div class="bReviewsContent bReviewsContent__mUser reviewsUser">
    <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1]), 'layout' => empty($layout) ? false : $layout]) ?>
    <? } ?>
  </div>

  <div class="bReviewsContent bReviewsContent__mPro reviewsPro hf"></div>

  <? $showMore = !(empty($reviewsData['review_list']) || (!empty($reviewsData['review_list']) && $reviewsData['page_count'] == 1)); ?>
  <? $showMoreText = 'Показать другие отзывы...' ?>

<? } elseif (!empty($reviewsDataPro['review_list'])) { ?>

  <div class="bReviewsContent bReviewsContent__mUser reviewsUser hf"></div>

  <div class="bReviewsContent bReviewsContent__mPro reviewsPro">
    <? foreach ($reviewsDataPro['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsDataPro['review_list'][$key + 1]), 'layout' => empty($layout) ? false : $layout]) ?>
    <? } ?>
  </div>
  <? $showMore = !(empty($reviewsDataPro['review_list']) || (!empty($reviewsDataPro['review_list']) && $reviewsDataPro['page_count'] == 1)); ?>
  <? $showMoreText = 'Показать другие обзоры...' ?>
<? } ?>

<div class="jsGetReviews bReviewsToggle product-btn-toggle <?= $showMore ? '' : ' hfImportant' ?>"><?= $showMoreText ?></div>
