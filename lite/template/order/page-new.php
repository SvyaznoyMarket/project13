<div class="">

	<?= $page->render('order/common/order-head') ?>

    <section class="checkout jsOrderV3PageNew">
        <h1 class="checkout__title">Получатель</h1>


    	<div id="OrderV3ErrorBlock" class="errtx" style="display: none"></div>


        <form class="form form-order-login clearfix" action="" method="POST" accept-charset="utf-8">
            <input type="hidden" value="changeUserInfo" name="action">

            <fieldset class="form-order-login__left">
                <div>
                    <div class="form__field">
                        <input class="jsOrderV3PhoneField form-order-login__it it" type="text" name="user_info[phone]" value="" data-mask="+7 (xxx) xxx-xx-xx" data-event="true">
                        <label class="form-order-login__label form-order-login__label_str placeholder" for="">Телефон</label>
                        <span class="form-order-login__hint">Для смс о состоянии заказа</span>
                    </div>

                    <div class="form__field">
                        <input class="jsOrderV3EmailField jsOrderV3EmailRequired form-order-login__it it" type="text" name="user_info[email]" value="">
                        <label class="form-order-login__label form-order-login__label_str placeholder" for="">E-mail</label>

                        <span class="form-order-login__hint form-order-login__hint_check">
                           <input class="custom-input custom-input_check jsCustomRadio js-customInput jsOrderV3SubscribeCheckbox" type="checkbox" name="subscribe" value="" id="orderV3Subscribe" checked="">
                           <label class="custom-label jsOrderV3SubscribeLabel" for="orderV3Subscribe">Подписаться на рассылку,<br/>получить скидку 300 рублей</label>
                        </span>
                    </div>

                    <div class="form__field">
                        <input class="jsOrderV3NameField form-order-login__it it" type="text" name="user_info[first_name]" value="">
                        <label class="form-order-login__label placeholder" for="">Имя</label>
                        <span class="form-order-login__hint">Как к вам обращаться?</span>
                    </div>
                </div>

                <div>
                    <div class="order-bonus">
                        <!-- Карта Много.ру -->
                        <div class="order-bonus__item" data-eq="0">
                            <img class="order-bonus__img" src="/public/images/order/mnogoru-mini.png" alt="mnogo.ru">
                            <span class="order-bonus__text">
                                <span id="" class="dotted">Карта Много.ру</span> <!-- что бы убрать бордер можно удалить класс dotted -->
                                <span id="" class="order-bonus__text-code"><span class="dotted jsMnogoRuSpan"></span></span>
                            </span>
                        </div>

                        <div class="order-bonus_it form__field" style="display: block">
                            <input class="form-order-login__it it jsOrderV3MnogoRuCardField" type="text" name="user_info[mnogo_ru_number]" value="" data-mask="xxxx xxxx">
                            <label class="form-order-login__label placeholder" for="">Номер</label>
                            <span class="order-bonus__info jsShowBonusCardHint"></span>

                            <div class="order-bonus__popup" style="display: block">
                                <div class="order-bonus__popup-desc">Получайте бонусы Много.ру за покупки в Enter (1 бонус за 33 руб.).<br>
                                    Для этого введите восьмизначный номер, указанный на лицевой стороне карты и в письмах от Клуба Много.ру.</div>
                                <img src="/css/skin/img/mnogo_ru.png" alt="mnogo.ru">
                            </div>
                        </div>
                        <!-- Карта Много.ру -->
                    </div>
                </div>
            </fieldset>

            <div class="form-order-login__right">
                <div class="form-order-login__log-title">Уже заказывали у нас?</div>
                <a class="form-order-login__btn btn-normal jsOrderV3AuthLink" href="/login">Войти с паролем</a>
            </div>


            <div class="orderCompl">
                <button class="orderCompl__btn btn-primary btn-primary_bigger" type="submit">Далее</button>
            </div>

        </form>

    </section>

    </div>