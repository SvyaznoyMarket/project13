<!-- Заявка на кухни -->
<div class="popup popup-application popup_460 js-popup-application" style="display: none;">
    <div class="popup__close js-popup-close"></div>

    <div class="popup-application__order">
        <div class="popup__title">Отправить заявку</div>

        <div class="product-card-set-recall">
            <div class="product-card-set-recall__title">Вам перезвонит специалист и поможет выбрать:</div>

            <ul class="product-card-set-recall-list">
                <li class="product-card-set-recall-list__item">Состав комплекта и его изменения;</li>
                <li class="product-card-set-recall-list__item">Условия доставки и сборки.</li>
            </ul>
        </div>

        <form class="form" action="">
            <div class="form__field">
                <input id="slot-phone-input" type="text" class="form__it it js-application-phone" data-required="true" name="" value="">
                <label for="slot-phone-input" class="form__placeholder placeholder placeholder_str">Телефон</label>
            </div>

            <div class="form__field">
                <input id="slot-email-input" type="text" class="form__it it js-application-email" data-required="true" name="" value="">
                <label for="slot-email-input" class="form__placeholder placeholder placeholder_str">Email</label>
            </div>

            <div class="form__field">
                <input id="slot-name-input" type="text" class="form__it it js-application-name" name="" value="">
                <label for="slot-name-input" class="form__placeholder placeholder">Имя</label>
            </div>

            <div class="form__check-big label-strict">
                <input type="checkbox" class="custom-input custom-input_check3 js-application-agree" data-required="true" id="accept" name="" value="">

                <label class="custom-label" for="accept">Я ознакомлен и согласен с информацией <a class="dotted">о продавце и его офертой</a><br>Продавец-партнер: ООО МЕГАЭЛАТОН</label>
            </div>

            <button class="product-card-set__btn-app btn-primary btn-primary_bigger btn-primary_centred btn-set js-application-submit" >Отправить заявку</button>

            <div class="popup-application__more align-center"><a href="" class="dotted">Подробнее о кухонном гарнитуре</a></div>
        </form>
    </div>

    <!-- Успешная заявка на кухни -->
    <div class="popup-application__success">
        <div class="popup__title">Ваша заявка № 122323 отправлена</div>

        <button class="product-card-set__btn-app btn-primary btn-primary_bigger btn-primary_centred btn-set">ОК</button>
    </div>
    <!--/ Успешная заявка на кухни -->
</div>
<!--/ Заявка на кухни -->