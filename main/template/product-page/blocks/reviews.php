<? $f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $reviewsData
){

    $reviews = (array)$reviewsData['review_list'];

    ?>

<div class="reviews clearfix">
    <div class="reviews__l jsReviewsList">
        <? if ($reviewsData['num_reviews'] == 0) : ?>
            Пока нет отзывов.
        <? else : ?>
            <? foreach ($reviews as $key => $review) : ?>
                <?= $helper->render('product-page/blocks/reviews.single', ['review' => $review, 'hidden' => $key > 1]) ?>
            <? endforeach ?>
        <? endif ?>
    </div>

    <div class="reviews__r">
        <div class="btn-type btn-type--normal jsReviewAdd">+ Добавить отзыв</div>

        <? if ($reviewsData['num_reviews'] > 0) : ?>

            <span class="reviews-percentage__tl">Всего <?= $helper->numberChoiceWithCount($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
            <?= $helper->render('product-page/blocks/reviews.rating', ['scores' => (array)$reviewsData['num_users_by_score']]) ?>
        <? endif ?>

    </div>
</div>

<? if ($reviewsData['num_reviews'] > 2) : ?>
    <div class="btn-type btn-type--normal jsShowMoreReviews" data-ui="<?= $product->getUi() ?>" data-total-num="<?= $reviewsData['num_reviews'] ?>">Показать больше отзывов</div>
<? endif ?>

<!-- попап добавления отзыва -->
<div class="popup popup--add-review jsReviewForm2" style="display: none">
    <i class="closer jsPopupCloser">×</i>

    <div class="popup__tl">Отзыв о товаре</div>

    <div class="popup__product-line">
        <div class="popup__product-line-img-wrap">
            <img class="popup__product-line-img" src="<?= $product->getImageUrl() ?>">
        </div>

        <div class="popup__product-line-tl">
            <?= $product->getName() ?>
        </div>
    </div>

    <div class="popup-rating">
        <span class="popup-rating__tl">Оценка:</span>
    <span class="popup-rating__state">
        <i class="popup-rating__i popup-rating__i--1 jsReviewFormRating"></i>
        <i class="popup-rating__i popup-rating__i--2 jsReviewFormRating"></i>
        <i class="popup-rating__i popup-rating__i--3 jsReviewFormRating"></i>
        <i class="popup-rating__i popup-rating__i--4 jsReviewFormRating"></i>
        <i class="popup-rating__i popup-rating__i--5 jsReviewFormRating"></i>
    </span>
    </div>

    <form id="reviewForm" class="popup-form popup-form--review form-ctrl" method="post" action="<?= $helper->url('product.review.create', ['productUi' => $product->getUi()]) ?>">

        <input id="reviewFormRating" type="hidden" name="review[score]" value="0">

        <fieldset class="form-ctrl__line">
            <div class="form-ctrl__group form-ctrl__group--inline">
                <input id="reviewFormName" class="form-ctrl__input" type="text" name="review[author_name]">
                <label class="form-ctrl__input-lbl" for="reviewFormName">Имя</label>
            </div>

            <div class="form-ctrl__group form-ctrl__group--inline">
                <input id="reviewFormEmail" class="form-ctrl__input form-ctrl__input--err" type="text" name="review[author_email]">
                <label class="form-ctrl__input-lbl form-ctrl__input-lbl--required" for="reviewFormEmail">E-mail</label>
            </div>
        </fieldset>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="reviewFormPros">Достоинства:</label>
            <textarea id="reviewFormPros" class="form-ctrl__textarea form-ctrl__textarea--err" name="review[advantage]"></textarea>
            <label class="form-ctrl__textarea-lbl--err">Не указаны достоинства</label>
        </div>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="reviewFormCons">Недостатки:</label>
            <textarea id="reviewFormCons" class="form-ctrl__textarea" name="review[disadvantage]"></textarea>
        </div>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="reviewFormExtract">Комментарий:</label>
            <textarea id="reviewFormExtract" class="form-ctrl__textarea" name="review[extract]"></textarea>
        </div>

        <div class="form-ctrl__btn-container">
            <button class="btn-type btn-type--buy" type="submit">Отправить</button>
        </div>
    </form>
    <!-- успешная отправка + подписка на рассылку -->
    <i class="closer jsPopupCloser">×</i>

    <div class="popup__tl">Отзыв о товаре</div>
    <div class="popup__product-line">
        <div class="popup__product-line-img-wrap">
            <img class="popup__product-line-img" src="<?= $product->getImageUrl() ?>">
        </div>

        <div class="popup__product-line-tl">
            <?= $product->getName() ?>
        </div>
    </div>
    <div class="popup-form-success">
        <div class="popup-form-success__txt">Спасибо! Ваш отзыв появится на сайте после проверки модератором.</div>
        <div class="popup-form-success__subscribtion">
            <form class="form-ctrl">
                <div class="form-ctrl__group">
                    <input class="customInput customInput-defcheck jsCustomRadio js-customInput js-registerForm-subscribe" type="checkbox" name="subscribe" id="subscribe">
                    <label class="customLabel customLabel-defcheck mChecked" for="subscribe">Подписаться на рассылку и получить купон со скидкой 300 <span class="rubl">p</span> на следующую покупку.</label>
                </div>
                <div class="form-ctrl__btn-container">
                    <button class="btn-type btn-type--buy" type="submit">Продолжить</button>
                </div>
            </form>
        </div>
    </div>
    <!-- конец блока подписки -->
    <!-- успешная отправка + вы уже подписаны -->
    <i class="closer jsPopupCloser">×</i>

    <div class="popup-form-success">
        <div class="popup-form-success__txt">Вы уже подписаны на нашу рассылку.<br/>Не забывайте проверять письма от Enter!</div>
        <div class="popup-form-success__txt popup-form-success__txt--small">Чтобы не пропускать наши новости и акции, добавьте <a href="mailto:info@enter.ru">info@enter.ru</a> в свою адресную книгу.</div>
        <div class="popup-form-success__subscribtion popup-form-success__subscribtion--already-done">
            <button class="btn-type btn-type--buy" type="submit">Продолжить</button>
        </div>
    </div>
    <!-- конец блока -->
    <!-- успешная отправка + спасибо -->
    <i class="closer jsPopupCloser">×</i>

    <div class="popup-form-success">
        <div class="popup-form-success__txt">Письмо с подтверждением подписки отправлено на mail@mail.ru.</div>
        <div class="popup-form-success__txt popup-form-success__txt--small">Проверьте папку «Спам», возможно, письмо попало туда.</div>
        <div class="popup-form-success__txt popup-form-success__txt--small">Чтобы не пропускать наши новости и акции, добавьте <a href="mailto:info@enter.ru">info@enter.ru</a> в свою адресную книгу.</div>
        <div class="popup-form-success__subscribtion popup-form-success__subscribtion--already-done">
            <button class="btn-type btn-type--buy" type="submit">Продолжить</button>
        </div>
    </div>
    <!-- конец блока -->

</div>
<!--/ попап добавления отзыва -->

<?}; return $f;