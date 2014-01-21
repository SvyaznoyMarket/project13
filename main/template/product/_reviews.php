<ul class="bReviewsTabs clearfix">
  <li class="bReviewsTabs__eTab bReviewsTabs__eUser user<?= !empty($reviewsData['review_list']) ? ' active' : ' hfImportant' ?>" data-container="reviewsUser" data-reviews-type="user"><span>Отзывы пользователей</span></li>

  <? if (\App::config()->product['pushReview']): ?>
      <li class="jsLeaveReview bReviewsTabs__eTab bReviewsTabs__eLast" data-pid="<?= $product->getId() ?>">
        <span class="jsReviewSend">Добавить отзыв</span>
      </li>
  <? endif ?>
</ul>

<? if(!empty($reviewsData['review_list'])) { ?>

  <div class="bReviewsContent bReviewsContent__mUser reviewsUser">
    <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1]), 'layout' => empty($layout) ? false : $layout]) ?>
    <? } ?>
  </div>

  <? $showMore = !(empty($reviewsData['review_list']) || (!empty($reviewsData['review_list']) && $reviewsData['page_count'] == 1)); ?>
  <? $showMoreText = 'Показать другие отзывы...' ?>

<? } ?>

<div class="jsGetReviews bReviewsToggle product-btn-toggle <?= $showMore ? '' : ' hfImportant' ?>"><?= $showMoreText ?></div>
