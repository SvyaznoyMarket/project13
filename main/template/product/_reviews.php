<?php
/**
 * @var $product \Model\Product\Entity
 * @var $page \View\DefaultLayout
 */
?>

<div class="bReviews">
    <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
        <h3 class="bHeadSection" id="bHeadSectionReviews">Отзывы</h3>
        <div class="bReviewsSummary clearfix">
            <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'product' => $product]) ?>
        </div>

        <? if (!empty($reviewsData['review_list'])): ?>
            <div class="bReviewsWrapper js-reviews-wrapper"
                 data-product-ui="<?= $product->getUi() ?>"
                 data-page-count="<?= $reviewsData['page_count'] ?>"
            >
                <ul class="bReviewsTabs clearfix">
                    <li class="bReviewsTabs__eTab bReviewsTabs__eUser user<?= !empty($reviewsData['review_list']) ? ' active' : ' hfImportant' ?>"><span>Отзывы пользователей</span></li>

                    <? if (\App::config()->product['pushReview']): ?>
                        <li class="bReviewsTabs__eTab bReviewsTabs__eLast" data-pid="<?= $product->getId() ?>">
                            <span class="jsReviewSend">Добавить отзыв</span>
                        </li>
                    <? endif ?>
                </ul>

                <div class="bReviewsContent bReviewsContent__mUser reviewsUser js-reviews-list">
                    <? foreach ($reviewsData['review_list'] as $key => $review) { ?>
                        <?= $page->render('product/_review', ['review' => $review, 'last' => empty($reviewsData['review_list'][$key + 1])]) ?>
                    <? } ?>
                </div>

                <? if ($reviewsData['page_count'] > 1): ?>
                    <div class="js-reviews-getMore bReviewsToggle product-btn-toggle">Показать другие отзывы...</div>
                <? endif ?>
            </div>
        <? endif ?>
    <? endif ?>
</div>