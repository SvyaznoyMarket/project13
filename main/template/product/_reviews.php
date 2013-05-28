<ul>
  <li class="reviewsTab user active" data-container="reviewsUser" data-reviews-type="user">Обзоры пользователей</li>
  <li class="reviewsTab pro" data-container="reviewsPro" data-reviews-type="pro">Обзоры экспертов</li>
</ul>
<div class="line pb5 mb25"></div>

<div class="reviewsTabContent reviewsUser">
  <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
    <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1])]) ?>
  <? } ?>
</div>

<div class="reviewsTabContent reviewsPro hf"></div>

<div id="getMoreReviewsButton" class="auto button getMoreReviews">Показать ещё обзоры</div>