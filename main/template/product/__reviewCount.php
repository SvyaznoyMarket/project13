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
    <a id="send-review" class="reviewSend" href="">Написать отзыв</a>

    <div class="popup reviewPopup" id="review-block">
        <i title="Закрыть" class="close">Закрыть</i>

       <div class="popupTitle">Отзыв о товаре</div>

       <div class="productName"><span class="productName__inner">Ноутбук Acer Aspire E1-571G 15,6" 500 ГБ i5 3230М GF 710M 1 ГБ Win 8, черный</span></div>

       <form action="" id="" class="reviewForm clearfix">
            <fieldset class="reviewForm__place">
                <div class="place2Col">
                    <label class="reviewForm__label">Достоинства</label>
                    <textarea class="reviewForm__textarea"></textarea>
                </div>

                <div class="place2Col mRight">
                    <label class="reviewForm__label">Недостатки</label>
                    <textarea class="reviewForm__textarea"></textarea>
                </div>

                <label class="reviewForm__label">Комментарий</label>
                <textarea class="reviewForm__textarea"></textarea>
            </fieldset>

            <fieldset class="reviewForm__place mLeft">
                <div class="reviewForm__stars">
                    <strong class="reviewForm__label">Оценка</strong>

                    <div class="starsList">
                        <img src="/images/reviews_star.png" alt="*">
                        <img src="/images/reviews_star.png" alt="*">
                        <img src="/images/reviews_star.png" alt="*">
                        <img src="/images/reviews_star.png" alt="*">
                        <img src="/images/reviews_star_empty.png" alt="*">
                    </div>
                </div>
            </fieldset>

            <fieldset class="reviewForm__place mCol">
                <div class="place2Col">
                    <label class="reviewForm__label">Ваше имя</label>
                    <input type="text" class="text reviewForm__text" name="" id="" />
                </div>

                <div class="place2Col">
                    <label class="reviewForm__label">Ваш e-mail</label>
                    <input type="text" class="text reviewForm__text" name="" id="" />
                    <span class="reviewForm__footnote">Для подтверждения требуется действующий адрес.</span>
                </div>
            </fieldset>

            <fieldset class="reviewForm__place mRight">
                <input type="submit" class="bigbutton reviewForm__button" value="Сохранить">
            </fieldset>
       </form>
    </div>

    <? if (\App::config()->product['pushReview']): ?>
        <span class="bReviewSection__eWrite jsLeaveReview" data-pid="<?= $product->getId() ?>"><span class="dotted">Оставить отзыв</span></span>
    <? endif ?>

    <div style="position:fixed; top:40px; left:50%; margin-left:-442px; z-index:1002; display:none; width:700px; height:480px" class="reviewPopup popup clearfix">
        <a class="close" href="#">Закрыть</a>
        <iframe id="rframe" frameborder="0" scrolling="auto" height="480" width="700"></iframe>
    </div>
</div><!--/review section -->

<? };