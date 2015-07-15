<?
/**
 * @var $page \View\Main\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>
<div class="wrapper">
    <div class="header table">
        <div class="header__side header__logotype table-cell">
            <a href="" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line--top">
                <a href="" class="location dotted js-popup-show jsRegionSelection" data-popup="region"><?= \App::user()->getRegion()->getName() ?></a>

                <ul class="header-shop-info">
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Магазины и самовывоз</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Доставка</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Оплата</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Партнерам</a></li>
                </ul>

                <div class="phone">
                    <span class="phone__text">+7 495 775-00-06</span>

                    <a href="" title="" class="phone-order"><i class="phone-order__icon i-controls i-controls--phone"></i> <span class="phone-order__text dotted">Звонок с сайта</span></a>
                </div>
            </div>

            <div class="header__line header__line--bottom">

                <?= $page->render('common/_search') ?>

                <ul class="user-controls">
                    <!--li class="user-controls__item user-controls__item_compare">
                        <a href="" class="user-controls__link">
                            <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                            <span class="user-controls__text">Сравнение</span>
                        </a>
                    </li>
                    <li class="user-controls__item user-controls__item_user">
                        <a href="" class="user-controls__link js-popup-show" data-popup="login">
                            <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
                            <span class="user-controls__text">Войти</span>
                        </a>
                    </li-->

                    <li class="user-controls__item user-controls__item_compare active">
                        <a href="" class="user-controls__link">
                            <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                            <span class="user-controls__text">Сравнение</span>
                        </a>

                        <div class="notice-dd notice-dd_compare" style="display: block">
                            <div class="notice-compare">
                                <div class="notice-compare__title">Товар добавлен к сравнению</div>

                                <div class="notice-compare__img"><img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image"></div>
                                <div class="notice-compare__desc">Чехол для Apple iPhone6 XtremeMac Microshield Acc Чехол для App XtremeMac</div>
                            </div>
                        </div>
                    </li>

                    <li class="user-controls__item user-controls__item_user active">
                        <a href="" class="user-controls__link js-popup-show" data-popup="login">
                            <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
                            <span class="user-controls__text">Бурлакова Татьяна Владимировна</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="header__side header-cart table-cell">
            <div class="notice-show">
                <a href="" title="">
                    <i class="header-cart__icon i-controls i-controls--cart"><span class="header-cart__count disc-count">99+</span></i>
                    <span class="header-cart__text">Корзина</span>
                </a>

                <div class="notice-dd notice-dd_cart">
                    <div class="notice-dd__inn">
                        <div class="notice-dd__height">
                            <ul class="notice-cart">
                                <li class="notice-cart__row">
                                    <a class="notice-cart__img notice-cart__cell" href="">
                                        <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                    </a>

                                    <a class="notice-cart__name notice-cart__cell" href="">
                                        Портативная акустическая система Promate Mulotov
                                    </a>

                                    <div class="notice-cart__desc notice-cart__cell">
                                        <div class="notice-cart__price">1 344 590 p</div>
                                        <span class="notice-cart__quan">1 шт.</span>
                                        <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                    </div>
                                </li>

                                <li class="notice-cart__row">
                                    <a class="notice-cart__img notice-cart__cell" href="">
                                        <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                    </a>

                                    <a class="notice-cart__name notice-cart__cell" href="">
                                        Портативная акустическая система Promate Mulotov
                                    </a>

                                    <div class="notice-cart__desc notice-cart__cell">
                                        <div class="notice-cart__price">1 344 590 p</div>
                                        <span class="notice-cart__quan">1 шт.</span>
                                        <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                    </div>
                                </li>

                                <li class="notice-cart__row">
                                    <a class="notice-cart__img notice-cart__cell" href="">
                                        <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                    </a>

                                    <a class="notice-cart__name notice-cart__cell" href="">
                                        Портативная акустическая система Promate Mulotov
                                    </a>

                                    <div class="notice-cart__desc notice-cart__cell">
                                        <div class="notice-cart__price">44 590 p</div>
                                        <span class="notice-cart__quan">1 шт.</span>
                                        <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                    </div>
                                </li>

                                <li class="notice-cart__row">
                                    <a class="notice-cart__img notice-cart__cell" href="">
                                        <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                    </a>

                                    <a class="notice-cart__name notice-cart__cell" href="">
                                        Портативная акустическая система Promate Mulotov
                                    </a>

                                    <div class="notice-cart__desc notice-cart__cell">
                                        <div class="notice-cart__price">344 590 p</div>
                                        <span class="notice-cart__quan">1 шт.</span>
                                        <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                    </div>
                                </li>

                                <li class="notice-cart__row">
                                    <a class="notice-cart__img notice-cart__cell" href="">
                                        <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                    </a>

                                    <a class="notice-cart__name notice-cart__cell" href="">
                                        Портативная акустическая система Promate Mulotov
                                    </a>

                                    <div class="notice-cart__desc notice-cart__cell">
                                        <div class="notice-cart__price">4 590 p</div>
                                        <span class="notice-cart__quan">1 шт.</span>
                                        <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a href="" class="notice-cart__on-cart btn-simple btn-simple_width">Перейти в корзину</a>
                    <a href="" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a>
                </div>
            </div>
        </div>
    </div>

    <hr class="hr-orange">

    <aside class="left-bar">
        <?= $page->slotNavigation() ?>
    </aside>

    <main class="content">
        <div class="content__inner">

            LEAF CATEGORY

        </div>
    </main>
</div>

<hr class="hr-orange">

<div class="footer">
    <div class="footer__right">
        <ul class="footer-external">
            <li class="footer-external__item footer-external__item_title">Оставайтесь на связи</li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_fb"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_od"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_tw"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_vk"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_yt"></i></a></li>
        </ul>

        <ul class="footer-external">
            <li class="footer-external__item footer-external__item_title">Мобильные приложения</li>

            <li class="footer-external__item">
                <a href="" class="footer-external__link">
                    <span class="app-box app-box_apple">Загрузите<br/> в App Store</span>
                </a>
            </li>

            <li class="footer-external__item">
                <a href="" class="footer-external__link">
                    <span class="app-box app-box_android">Загрузите<br/> на Google Play</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="footer__left">
        <ul class="footer-list grid-4col">
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">О компании</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Работа у нас</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Правовая информация</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Уцененные товары оптом</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Способы оплаты</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Обратная связь</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Условия продажи</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Рекламные возможности</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Покупка в кредит</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">ЦСИ</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Информация о СМИ</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Партнерам</a></li>
        </ul>

        <form action="" class="subscribe-form">
            <div class="subscribe-form__title">Подписаться на рассылку и получить 300₽ на следующую покупку</div>
            <input type="text" class="subscribe-form__it it" placeholder="Ваш email">
            <button class="subscribe-form__btn btn-normal">Подписаться</button>
        </form>

        <div class="footer-hint">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</div>

        <ul class="footer-external footer-external_fl-r">
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_mnogoru"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_sb"></i></a></li>
        </ul>
    </div>
</div>

<footer class="copy">
    <div class="inner">
        <div class="copy__left">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
        <div class="copy__right"><a href="">Сообщить об ошибке</a></div>
        <div class="copy__center"><a href="">Мобильная версия сайта</a></div>
    </div>
</footer>

<!-- попап авторизации/регистрации -->
<div class="popup popup_log js-popup-login">
    <div class="popup__close js-popup-close">&#215;</div>

    <div class="popup__content">
        <!--
            по умолчанию login_auth - окно "Войти"
            если нажали "Регистрация" то login_auth - меняем на login_reg
            если нажали на "забыли?(пароль)" - меняем класс на login_hint
            если действие совершено успешно, то !добавляем! класс login_success
        -->
        <div class="login login_auth">
            <!-- авторизация -->
            <form class="form form_auth" action="" method="">
                <div class="popup__title">Вход в Enter</div>

                <div class="form__field">
                    <!--
                        если поле заполнено символами, то добавлем класс valid
                        если ошибка - error
                    -->
                    <input type="text" class="form__it it error" value="">
                    <label class="form__placeholder placeholder">Email или телефон</label>
                </div>

                <div class="form__field">
                    <input type="text" class="form__it it" value="">
                    <label class="form__placeholder placeholder">Пароль</label>

                    <a href="" class="form__it-btn">забыли?</a>
                </div>

                <div class="form__field">
                    <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Войти</button>
                </div>

                <div class="form__title">Войти через</div>

                <ul class="login-external">
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_fb"></a></li>
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_vk"></a></li>
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_od"></a></li>
                </ul>
            </form>
            <!--/ авторизация -->

            <!-- регистрация -->
            <form class="form form_reg" action="" method="">
                <div class="popup__title">Регистрация</div>

                <fieldset class="form__content">
                    <div class="form__it-name">Как к вам обращаться?</div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Имя</label>
                    </div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Email</label>
                    </div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Телефон</label>
                    </div>

                    <div class="login-subscribe">
                        <input class="custom-input custom-input_check" type="checkbox" name="subscribe" id="subscribe" checked="checked">
                        <label class="custom-label" for="subscribe">Подписаться на email-рассылку,<br> получить скидку 300 рублей</label>
                    </div>

                    <div class="form__field">
                        <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Регистрация</button>
                    </div>

                    <div class="login-hint">
                        Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="" class="underline">Условиями продажи</a>.
                    </div>

                    <div class="form__title">Войти через</div>

                    <ul class="login-external">
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_fb"></a></li>
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_vk"></a></li>
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_od"></a></li>
                    </ul>
                </fieldset>

                <!-- сообщение об успешной регистрации -->
                <div class="form__success-text">Пароль отправлен на email<br>и на мобильный телефон.</div>
                <!--/ сообщение об успешной регистрации -->
            </form>
            <!--/ регистрация -->

            <!-- восстановление пароля -->
            <form class="form form_hint" action="" method="">
                <div class="popup__title">Восстановление пароля</div>

                <fieldset class="form__content">
                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Email или телефон</label>
                    </div>

                    <div class="form__field">
                        <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Отправить</button>
                    </div>
                </fieldset>

                <!-- сообщение об успешной отправке пароля -->
                <div class="form__success-text">Новый пароль отправлен<br>на мобильный телефон.</div>
                <!--/ сообщение об успешной отправке пароля -->
            </form>
            <!-- восстановление пароля -->
        </div>

        <div class="login-switch"><a href="" class="dotted">Регистрация</a></div>
    </div>
</div>
<!--/ попап авторизации/регистрации -->

<?= $page->slotBodyJavascript() ?>

</body>
</html>
