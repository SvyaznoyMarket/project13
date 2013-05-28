<ul>
  <li class="reviewsTab user<?= !empty($reviewsData['review_list']) ? ' active' : ' hfImportant' ?>" data-container="reviewsUser" data-reviews-type="user">Отзывы пользователей</li>
  <?
    if(!empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = '';
    } elseif(empty($reviewsData['review_list']) && !empty($reviewsDataPro['review_list'])) {
      $tabProClass = ' active';
    } else {
      $tabProClass = ' hfImportant';
    }
  ?>
  <li class="reviewsTab pro<?= $tabProClass ?>" data-container="reviewsPro" data-reviews-type="pro">Обзоры экспертов</li>
</ul>
<div class="line pb5 mb25"></div>

<? if(!empty($reviewsData['review_list'])) { ?>

  <div class="reviewsTabContent reviewsUser">
    <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1])]) ?>
    <? } ?>
  </div>
  <div class="reviewsTabContent reviewsPro hf"></div>
  <? $showMore = !(empty($reviewsData['review_list']) || (!empty($reviewsData['review_list']) && $reviewsData['page_count'] == 1)); ?>

<? } elseif (!empty($reviewsDataPro['review_list'])) { ?>

  <div class="reviewsTabContent reviewsUser hf"></div>
  <div class="reviewsTabContent reviewsPro">
    <? foreach ($reviewsDataPro['review_list'] as $key => $review) { ?>
      <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsDataPro['review_list'][$key + 1])]) ?>
    <? } ?>
  </div>
  <? $showMore = !(empty($reviewsDataPro['review_list']) || (!empty($reviewsDataPro['review_list']) && $reviewsDataPro['page_count'] == 1)); ?>

<? } ?>

<div id="getMoreReviewsButton" class="auto button getMoreReviews<?= $showMore ? '' : ' hf' ?>">Показать ещё обзоры</div>