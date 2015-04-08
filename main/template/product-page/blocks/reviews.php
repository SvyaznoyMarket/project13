<? $f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $reviewsData
){

    $reviews = (array)$reviewsData['review_list'];

    ?>

<div class="reviews clearfix">
    <div class="reviews__l">

        <? foreach ($reviews as $review) : ?>

            <?= $helper->render('product-page/blocks/reviews.single', ['review' => $review]) ?>

        <? endforeach ?>

    </div>

    <div class="reviews__r">
        <div class="btn-type btn-type--normal jsReviewAdd">+ Добавить отзыв</div>

        <? if ($reviewsData['num_reviews'] > 0) : ?>

            <span class="reviews-percentage__tl">Всего <?= $helper->numberChoiceWithCount($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
            <?= $helper->render('product-page/blocks/reviews.rating', ['scores' => (array)$reviewsData['num_users_by_score']]) ?>

        <? else : ?>

            Пока нет отзывов.

        <? endif ?>

    </div>
</div>

<!-- попап добавления отзыва -->
<div class="popup popup--add-review jsReviewForm" style="display: none">
    <i class="closer">×</i>

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
        <i class="popup-rating__i popup-rating__i--1"></i>
        <i class="popup-rating__i popup-rating__i--2"></i>
        <i class="popup-rating__i popup-rating__i--3"></i>
        <i class="popup-rating__i popup-rating__i--4"></i>
        <i class="popup-rating__i popup-rating__i--5"></i>
    </span>
    </div>

    <form class="popup-form popup-form--review form-ctrl">
        <fieldset class="form-ctrl__line">
            <div class="form-ctrl__group form-ctrl__group--inline">
                <input class="form-ctrl__input" type="text" name="name">
                <label class="form-ctrl__input-lbl" for="name">Имя</label>
            </div>

            <div class="form-ctrl__group form-ctrl__group--inline">
                <input class="form-ctrl__input form-ctrl__input--err" type="text" name="email">
                <label class="form-ctrl__input-lbl form-ctrl__input-lbl--required" for="email">E-mail</label>
            </div>
        </fieldset>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="email">Достоинства:</label>
            <textarea class="form-ctrl__textarea"></textarea>
        </div>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="email">Недостатки:</label>
            <textarea class="form-ctrl__textarea"></textarea>
        </div>

        <div class="form-ctrl__group">
            <label class="form-ctrl__textarea-lbl" for="email">Комментарий:</label>
            <textarea class="form-ctrl__textarea"></textarea>
        </div>

        <div class="form-ctrl__btn-container">
            <button class="btn-type btn-type--buy" type="submit">Отправить</button>
        </div>
    </form>
</div>
<!--/ попап добавления отзыва -->

<?}; return $f;