<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $reviewsData
) {
?>

<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="bReviewSection clearfix">
    <div class="bReviewSection__eStar">
        <? $rating = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'] ?>
        <?= empty($rating) ? '' : $helper->render('product/__rating', ['score' => $rating]) ?>
    </div>
    <? if (!empty($rating)) { ?>
        <span itemprop="ratingCount" class="jsGoToId border" data-goto="bHeadSectionReviews"><?= $reviewsData['num_reviews'] ?> <?= $helper->numberChoice($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
    <? } else { ?>
        <span>Отзывов нет</span>
    <? } ?>

    <? if (\App::config()->product['pushReview']): ?>
        <a id="send-review" class="reviewSend jsReviewSend" href="">Написать отзыв</a>

        <div class="popup reviewPopup jsReviewPopup" id="review-block">
            <i title="Закрыть" class="close">Закрыть</i>

            <div class="popupTitle">Отзыв о товаре</div>

            <div class="productName"><span class="productName__inner"><?= $product->getPrefix() . ' ' . $product->getWebName() ?></span></div>

            <form action="<?= $helper->url('product.reviews', ['productId' => $product->getId()]) ?>" id="" class="reviewForm clearfix jsReviewForm" method="post">
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
                        <div class="starsList">
                            <span class="starsList__item mEmpty"></span>
                            <span class="starsList__item mEmpty"></span>
                            <span class="starsList__item mEmpty"></span>
                            <span class="starsList__item mEmpty"></span>
                            <span class="starsList__item mEmpty"></span>

<!--                             <img class="mEmpty" src="/images/reviews_star_empty.png" alt="*">
                            <img class="mEmpty" src="/images/reviews_star_empty.png" alt="*">
                            <img class="mEmpty" src="/images/reviews_star_empty.png" alt="*">
                            <img class="mEmpty" src="/images/reviews_star_empty.png" alt="*">
                            <img class="mEmpty" src="/images/reviews_star_empty.png" alt="*"> -->
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
</div><!--/review section -->

<? };