<?
/**
 * @var $page \View\LiteLayout
 */

if (!isset($redirect)) $redirect = null;
$oauthEnabled = \App::config()->oauthEnabled;
?>

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
        <div class="login login_auth js-auth-state">
            <!-- авторизация -->
            <form class="form form_auth js-auth-form" action="<?= $page->url('user.login') ?>" method="post">

                <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

                <div class="popup__title">Вход в Enter</div>

                <div class="form__field">
                    <!--
                        если поле заполнено символами, то добавлем класс valid
                        если ошибка - error
                    -->
                    <input id="auth-username-input" type="text" class="form__it it js-auth-username-input" name="signin[username]" value="">
                    <label for="auth-username-input" class="form__placeholder placeholder js-auth-username-label">Email или телефон</label>
                </div>

                <div class="form__field">
                    <input id="auth-password-input" type="password" class="form__it it js-auth-password-input" name="signin[password]" value="">
                    <label for="auth-password-input" class="form__placeholder placeholder js-auth-password-label">Пароль</label>

                    <a href="" class="form__it-btn js-auth-switch-state" data-state="login_hint">забыли?</a>
                </div>

                <div class="form__field">
                    <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Войти</button>
                </div>

                <div class="form__title">Войти через</div>

                <ul class="login-external">
                    <? if ($oauthEnabled['facebook']): ?>
                        <li class="login-external__item">
                            <a href="<?= $page->url('user.login.external', ['providerName' => 'facebook']) ?>"
                               class="login-external__link login-external__link_fb">

                            </a>
                        </li>
                    <? endif ?>
                    <? if ($oauthEnabled['vkontakte']): ?>
                        <li class="login-external__item">
                            <a href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte']) ?>"
                               class="login-external__link login-external__link_vk">

                            </a>
                        </li>
                    <? endif ?>
<!--                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_od"></a></li>-->
                </ul>
            </form>
            <!--/ авторизация -->

            <!-- регистрация -->
            <form class="form form_reg" action="" method="">
                <div class="popup__title">Регистрация</div>

                <fieldset class="form__content">
                    <div class="form__it-name">Как к вам обращаться?</div>

                    <div class="form__field">
                        <input id="register-name-input" type="text" class="form__it it" value="">
                        <label for="register-name-input" class="form__placeholder placeholder">Имя</label>
                    </div>

                    <div class="form__field">
                        <input id="register-email-input" type="text" class="form__it it" value="">
                        <label for="register-email-input" class="form__placeholder placeholder">Email</label>
                    </div>

                    <div class="form__field">
                        <input id="register-phone-input" type="text" class="form__it it" value="">
                        <label for="register-phone-input" class="form__placeholder placeholder">Телефон</label>
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
                        <input id="forget-email-input" type="text" class="form__it it" value="">
                        <label for="forget-email-input" class="form__placeholder placeholder">Email или телефон</label>
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

        <div class="login-switch js-auth-switch-state" data-state="login_reg"><a href="" class="dotted">Регистрация</a></div>
    </div>
</div>
<!--/ попап авторизации/регистрации -->