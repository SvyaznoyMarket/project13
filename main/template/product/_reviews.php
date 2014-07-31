<?php
/**
 * @var $product \Model\Product\Entity
 */
?>

<? switch (\App::abTest()->getCase()->getKey()):
    case 'reviews_sprosikupi': ?>
        <div id="spk-widget-reviews" style="display:none; width: 100%;" shop-id="52dbdd369928f539612151" good-id="<?= $page->helper->escape($product->getId()) ?>" good-title="<?= $page->helper->escape($product->getWebName()) ?>" good-url="<?= $page->helper->escape($product->getLink()) ?>">
            <?php
            $spkCurl = curl_init('http://widget.sprosikupi.ru/seo/15898ce50992cc789cdc21/' . $product->getId());
            curl_setopt_array($spkCurl, array(
                CURLOPT_TIMEOUT => 3, //Таймаут 3 секунды
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => 0, //Не ожидаем редиректов
                CURLOPT_FAILONERROR => 1, //На ошибку тихо фейлимся, а не возвращаеем результат
                CURLOPT_RETURNTRANSFER => 0, //Сразу вывод, а не возврат строки
            ));
            curl_exec($spkCurl);
            ?>
        </div>

        <? break ?>
    <? case 'reviews_shoppilot': ?>
        <div id="shoppilot-reviews-container" data-product-id="<?=$page->helper->escape($product->getId())?>">
            <?
            $widgetUrl = curl_init('http://ugc.shoppilot.ru/html/535a852cec8d830a890000a6/product/' . $product->getId() . '/product-reviews.html');
            curl_setopt_array($widgetUrl, array(
                CURLOPT_TIMEOUT        => 2,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_FAILONERROR    => 1,
                CURLOPT_RETURNTRANSFER => 0
            ));
            curl_exec($widgetUrl);
            ?>
        </div>

        <? break ?>
    <? default: ?>
        <div class="bReviews">
            <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
                <? if (isset($layout) && $layout === 'jewel'): ?>
                    <h2 id="reviewsSectionHeader" class="bold">Отзывы</h2>
                    <div class="line pb5"></div>

                    <div id="reviewsSummary">
                        <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'product' => $product, 'layout' => 'jewel']) ?>
                    </div>
                <? else: ?>
                    <h3 class="bHeadSection" id="bHeadSectionReviews">Отзывы</h3>
                    <div class="bReviewsSummary clearfix">
                        <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'product' => $product]) ?>
                    </div>
                <? endif ?>

                <? if (!empty($reviewsData['review_list'])): ?>
                    <div <?= isset($layout) && $layout === 'jewel' ? 'id="reviewsWrapper" class="reviewsWrapper"' : 'class="bReviewsWrapper"' ?> data-product-ui="<?= $product->getUi() ?>" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsData['page_count'] ?>" data-container="reviewsUser" data-reviews-type="user">
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
                    </div>
                <? endif ?>
            <? endif ?>
        </div>
        <? break ?>
    <? endswitch ?>