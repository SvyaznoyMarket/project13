<ul class="reviewsTabs clearfix">
  <li style="width: 173px;" class="reviewsTab user<?= !empty($reviewsData['review_list']) ? ' active' : ' hfImportant' ?>" data-container="reviewsUser" data-reviews-type="user"><span>Отзывы пользователей</span></li>
  <?
    if(!empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = '';
    } elseif(empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = ' active';
    } else {
      $tabProClass = ' hfImportant';
    }
  ?>
  <li class="reviewsTab pro<?= $tabProClass ?>" data-container="reviewsPro" data-reviews-type="pro"><span>Обзоры экспертов</span></li>

  <li style="float: right;">
    <span onclick="$('.newReviewPopupLink').click()">Оставить отзыв</span>
  </li> 
</ul>

<? if(!empty($reviewsData['review_list'])) { ?>

  <div class="reviewsTabContent reviewsUser">
    <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1])]) ?>
    <? } ?>
  </div>

  <div class="reviewsTabContent reviewsPro hf"></div>

  <? $showMore = !(empty($reviewsData['review_list']) || (!empty($reviewsData['review_list']) && $reviewsData['page_count'] == 1)); ?>
  <? $showMoreText = 'Показать другие отзывы...' ?>

<? } elseif (!empty($reviewsDataPro['review_list'])) { ?>

  <div class="reviewsTabContent reviewsUser hf"></div>

  <div class="reviewsTabContent reviewsPro">
    <? foreach ($reviewsDataPro['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsDataPro['review_list'][$key + 1])]) ?>
    <? } ?>
  </div>
  <? $showMore = !(empty($reviewsDataPro['review_list']) || (!empty($reviewsDataPro['review_list']) && $reviewsDataPro['page_count'] == 1)); ?>
  <? $showMoreText = 'Показать другие обзоры...' ?>
<? } ?>

<div id="getMoreReviewsButton" class="product-btn-toggle getMoreReviews<?= $showMore ? '' : ' hf' ?>"><?= $showMoreText ?></div>