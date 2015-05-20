<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $reviewsData
) {
    $rating = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'];

    $reviewCount = !empty($reviewsData['num_reviews'])
        ? $reviewsData['num_reviews']
        : (!empty($reviewsData['review_list']) ? count($reviewsData['review_list']) : 0);
?>

<div class="bReviewSection clearfix">
    <? switch (\App::abTest()->getTest('reviews')->getChosenCase()->getKey()):
        case 'sprosikupi': ?>
            <div class="sprosikupiRating clearfix">
                <div class="spk-good-rating" shop-id="52dbdd369928f539612151" good-id="<?= $helper->escape($product->getId()) ?>"></div>
                <a href="?spkPreState=addReview">Добавить отзыв</a>
            </div>
            <? break ?>
        <? case 'shoppilot': ?>
            <div id="shoppilot-rating-container"></div>
            <? break ?>
        <? default: ?>
            <div class="bReviewSection__eStar">
                <?= empty($rating) ? '' : $helper->render('product/__rating', ['score' => $rating]) ?>
            </div>

            <? if (empty($rating) && 0 == $reviewCount): ?>
                <span style="float: left;">Отзывов нет</span>
            <? else: ?>
                <span class="jsGoToId border" data-goto="bHeadSectionReviews">
                    <?= $reviewCount ?> <?= $helper->numberChoice($reviewCount, ['отзыв', 'отзыва', 'отзывов']) ?>
                </span>
            <? endif ?>

            <? if (\App::config()->product['pushReview']): ?>
                <!--noindex--><a id="send-review" class="reviewSend jsReviewSend" href="">Добавить отзыв</a><!--/noindex-->

                <div class="popup reviewPopup jsReviewPopup" id="review-block">
                    <i title="Закрыть" class="close">Закрыть</i>

                    <div class="popupTitle">Отзыв о товаре</div>

                    <div class="productName"><span class="productName__inner"><?= $product->getPrefix() . ' ' . $product->getWebName() ?></span></div>

                    <form action="<?= $helper->url('product.review.create', ['productUi' => $product->getUi()]) ?>" id="" class="reviewForm clearfix jsReviewForm" method="post">
                        <ul class="error_list"></ul>

                        <fieldset class="reviewForm__place">
                            <div class="place2Col">
                                <label class="reviewForm__label">Достоинства</label>
                                <textarea class="reviewForm__textarea jsReviewFormField jsAdvantage" name="review[advantage]"></textarea>
                            </div>

                            <div class="place2Col mRight">
                                <label class="reviewForm__label">Недостатки</label>
                                <textarea class="reviewForm__textarea jsReviewFormField jsDisadvantage" name="review[disadvantage]"></textarea>
                            </div>

                            <div>
                                <label class="reviewForm__label">Комментарий</label>
                                <textarea class="reviewForm__textarea jsReviewFormField jsExtract" name="review[extract]"></textarea>
                            </div>
                        </fieldset>

                        <fieldset class="reviewForm__place mLeft">
                            <div class="reviewForm__stars">
                                <strong class="reviewForm__label">Оценка</strong>
                                <input class="jsReviewStarsCount" name="review[score]" value="0"/>
                                <div class="stars-list">
                                    <span class="star stars-list__item star-empty"></span>
                                    <span class="star stars-list__item star-empty"></span>
                                    <span class="star stars-list__item star-empty"></span>
                                    <span class="star stars-list__item star-empty"></span>
                                    <span class="star stars-list__item star-empty"></span>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="reviewForm__place mCol jsFormFieldset">
                            <div class="place2Col jsPlace2Col">
                                <label class="reviewForm__label">Ваше имя</label>
                                <input type="text" class="text reviewForm__text jsReviewFormField jsAuthorName" name="review[author_name]" id="" />
                            </div>

                            <div class="place2Col jsPlace2Col">
                                <label class="reviewForm__label">Ваш e-mail</label>
                                <input type="text" class="text reviewForm__text jsReviewFormField jsAuthorEmail" name="review[author_email]" id="" />
                                <span class="reviewForm__footnote">Для подтверждения требуется действующий адрес.</span>
                            </div>
                        </fieldset>

                        <fieldset class="reviewForm__place mRight">
                            <input type="submit" class="bigbutton reviewForm__button jsFormSubmit" value="Сохранить">
                        </fieldset>
                    </form>
                </div>
            <? endif ?>

            <? break ?>
    <?php endswitch ?>
</div><!--/review section -->

<? };
