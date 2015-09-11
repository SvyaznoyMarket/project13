<!-- Добавить отзыв -->
<div class="popup-add-review popup popup_640" style="display: none">
    <div class="popup__close"></div>

    <div class="jsReviewFormInner">
        <div class="popup__title">Отзыв о товаре</div>

        <div class="add-review-product">
            <div class="add-review-product__img">
                <img class="image" src="http://0.imgenter.ru/uploads/media/69/93/37/thumb_3ddc_product_120.jpeg">
            </div>

            <div class="add-review-product__name">
                <span>Мягкая игрушка Toivy «Львенок», 23 см</span>
            </div>
        </div>

        <div class="add-review-rating">
            <span class="add-review-rating__title">Оценка:</span>

            <div class="add-review-rating__value rating rating_bigger rating_add">
                <span class="rating-state">
                    <i class="rating-state__item icon-rating rating-state__item_fill"></i>
                    <i class="rating-state__item icon-rating rating-state__item_fill"></i>
                    <i class="rating-state__item icon-rating rating-state__item_fill"></i>
                    <i class="rating-state__item icon-rating rating-state__item_fill"></i>
                    <i class="rating-state__item icon-rating rating-state__item_fill"></i>
                </span>
            </div>
        </div>

        <form id="" class="form" method="" action="">
            <div class="form__field-2col">
                <div class="form__field">
                    <input id="reviewFormName" class="form__it it" type="text" name="" maxlength="20">
                    <label class="form__placeholder placeholder" for="reviewFormName">Имя</label>
                </div>

                <div class="form__field">
                    <input id="reviewFormEmail" class="form__it it" type="text" name="">
                    <label class="form__placeholder placeholder placeholder_str" for="reviewFormEmail">E-mail</label>
                </div>
            </div>

            <div class="form__field form__field_no-placeholder">
                <label class="label" for="reviewFormPros">Достоинства:</label>
                <textarea id="reviewFormPros" class="form__textarea textarea" name=""></textarea>
            </div>

            <div class="form__field form__field_no-placeholder">
                <label class="label" for="reviewFormCons">Недостатки:</label>
                <textarea id="reviewFormCons" class="form__textarea textarea" name=""></textarea>
            </div>

            <div class="form__field form__field_no-placeholder">
                <label class="label" for="reviewFormExtract">Комментарий:</label>
                <textarea id="reviewFormExtract" class="form__textarea textarea" name=""></textarea>
            </div>

            <div class="form__btn-container">
                <button class="btn-primary btn-primary_bigger" type="submit">Отправить</button>
            </div>
        </form>
    </div>

    <!-- успешная отправка + подписка на рассылку -->
    <div style="display: none">
        <div class="popup__title">Отзыв о товаре</div>

        <div class="add-review-product">
            <div class="add-review-product__img">
                <img class="image" src="http://0.imgenter.ru/uploads/media/69/93/37/thumb_3ddc_product_120.jpeg">
            </div>

            <div class="add-review-product__name">
                <span>Мягкая игрушка Toivy «Львенок», 23 см</span>
            </div>
        </div>

        <div class="add-review-success">
            <div class="add-review-success__text">Спасибо! Ваш отзыв появится на сайте после проверки модератором.</div>

            <form class="add-review-success__form form">
                <input class="custom-input custom-input_check3" type="checkbox" name="subscribe" id="subscribe2">
                <label class="custom-label" for="subscribe2">Подписаться на рассылку и получить купон со скидкой 300&thinsp;<span class="rubl">C</span> на следующую покупку.</label>

                <div class="form__btn-container">
                    <button class="btn-normal btn-normal_middle" type="submit">Продолжить</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ успешная отправка + подписка на рассылку -->

    <!-- успешная отправка + вы уже подписаны -->
    <div class="" style="display: none">
        <div class="add-review-success">
            <div class="add-review-success__text">Вы уже подписаны на нашу рассылку.</div>
            <div class="add-review-success__text">Не забывайте проверять письма от Enter!</div>
            <div class="add-review-success__text add-review-success__text_small">Чтобы не пропускать наши новости и акции, добавьте <a class="link" href="mailto:info@enter.ru">info@enter.ru</a> в свою адресную книгу.</div>

            <div class="form__btn-container">
                <button class="btn-normal btn-normal_middle" type="submit">Продолжить</button>
            </div>
        </div>
    </div>
    <!--/ успешная отправка + вы уже подписаны -->

    <!-- успешная отправка + спасибо -->
    <div class="" style="display: none">
        <div class="add-review-success">
            <div class="add-review-success__text add-review-success__text_mark">Спасибо!</div>
            <div class="add-review-success__text">Письмо с подтверждением подписки отправлено на mail@mail.ru.</div>
            <div class="add-review-success__text add-review-success__text_small">Проверьте папку «Спам», возможно, письмо попало туда.</div>
            <div class="add-review-success__text add-review-success__text_small">Чтобы не пропускать наши новости и акции, добавьте <a class="link" href="mailto:info@enter.ru">info@enter.ru</a> в свою адресную книгу.</div>

            <div class="form__btn-container">
                <button class="btn-normal btn-normal_middle" type="submit">Продолжить</button>
            </div>
        </div>
    </div>
    <!--/ успешная отправка + спасибо -->
</div>
<!--/ Добавить отзыв -->