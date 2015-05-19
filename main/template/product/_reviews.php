<?php
/**
 * @var $product \Model\Product\Entity
 * @var $page \View\DefaultLayout
 */
?>

<? switch (\App::abTest()->getTest('reviews')->getChosenCase()->getKey()):
    case 'sprosikupi': ?>
        <div id="spk-widget-reviews" style="display:none; width: 100%;" shop-id="52dbdd369928f539612151" good-id="<?= $page->helper->escape($product->getId()) ?>" good-title="<?= $page->helper->escape($product->getName()) ?>" good-url="<?= $page->helper->escape($page->url('product', ['productPath' => $product->getPath()], true)) ?>">
            <?=$sprosikupiReviews?>
        </div>

        <? break ?>
    <? case 'shoppilot': ?>
        <div id="shoppilot-reviews-container" data-product-id="<?=$page->helper->escape($product->getId())?>">
            <?=$shoppilotReviews?>
        </div>

        <? break ?>
    <? default: ?>
        <div class="bReviews">
        <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
                <h3 class="bHeadSection" id="bHeadSectionReviews">Отзывы</h3>
                <div class="bReviewsSummary clearfix">
                    <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'product' => $product]) ?>
                </div>

                <? if (!empty($reviewsData['review_list'])): ?>
                    <div class="bReviewsWrapper js-reviews-wrapper" data-product-ui="<?= $product->getUi() ?>" data-page-count="<?= $reviewsData['page_count'] ?>" data-avg-score="<?= empty($reviewsData['avg_score']) ? 0 : $page->escape($reviewsData['avg_score']) ?>" data-first-page-avg-score="<?= empty($reviewsData['current_page_avg_score']) ? 0 : $page->escape($reviewsData['current_page_avg_score']) ?>" data-category-name="<?= $page->escape($product->getLastCategory() ? $product->getLastCategory()->getName() : '') ?>">
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
        <? break ?>
    <? endswitch ?>