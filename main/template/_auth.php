<?php
/**
 * @var $page         \View\Layout
 * @var $oauthEnabled array
 * @var $form         \View\User\LoginForm
 */

if (!isset($form)) $form = new \View\User\LoginForm();
?>

<noindex>
    <!-- Registration -->
    <div class="popup popup-auth" id="auth-block">
        <span class="close close-auth">Закрыть</span>

        <form class="authForm js-authForm js-formContainer" action="<?= $page->url($form->getRoute(), ['redirect_to' => isset($redirect_to) ? $redirect_to : null]) ?>" method="post">
            <fieldset class="authForm_fld authForm_fld-scrll">
                <!-- секция входа -->
                <div class="authForm_inn">
                    <div class="authForm_t legend">Вход в Enter</div>

                    <input type="text" class="authForm_it textfield" name="signin[username]" value="<?= $form->getUsername() ?>" placeholder="Email или телефон">

                    <div class="authForm_hint">
                        <input type="password" class="authForm_it textfield" name="signin[password]" value="" placeholder="Пароль">
                        <span
                            class="authForm_hint_tx js-resetLink"
                            data-show-container="#auth-block .js-resetForm"
                            data-hide-container="#auth-block .js-formContainer"
                            data-show-link="#auth-block .js-authLink"
                            data-hide-link="#auth-block .js-link"
                        >забыли?</span>
                    </div>

                    <input type="submit" class="authForm_is btnsubmit" name="" data-loading-value="Вхожу..." value="Войти">

                    <div class="authForm_socn">
                        Войти через

                        <ul class="authForm_socn_lst">
                            <? if($oauthEnabled['facebook']): ?>
                                <li class="authForm_socn_i">
                                    <a class="authForm_socn_lk authForm_socn_lk-fb" href="<?= $page->url('user.login.external', ['providerName' => 'facebook' ]) ?>" >Войти через FB</a>
                                </li>
                            <? endif; ?>

                            <? if ($oauthEnabled['vkontakte']): ?>
                                <li class="authForm_socn_i">
                                    <a class="authForm_socn_lk authForm_socn_lk-vk" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>" >Войти через VK</a>
                                </li>
                            <? endif; ?>
                        </ul>
                    </div>
                </div>
                <!--/ секция входа -->
            </fieldset>
        </form>

        <form class="authForm js-registerForm js-formContainer" action="<?= $page->url('user.register') ?>" method="post" style="display: none">
            <fieldset class="authForm_fld authForm_fld-scrll">
                <!-- секция регистрации -->
                <div class="authForm_inn">
                    <div class="authForm_t legend">Регистрация</div>

                    <!-- показываем при удачной регистрации, authForm_regbox скрываем -->
                    <div class="authForm_regcomplt" style="display: none;">
                        Пароль отправлен на email<br/>и на мобильный телефон.
                    </div>
                    <!--/ показываем при удачной регистрации, authForm_regbox скрываем -->

                    <div class="authForm_regbox">
                        <label class="authForm_lbl">Как к вам обращаться?</label>
                    
                        <input type="text" class="authForm_it textfield" name="register[first_name]" value="" placeholder="Имя" />

                        <input type="text" class="authForm_it textfield" name="register[email]" value="" placeholder="Email" />

                        <input type="text" class="authForm_it textfield" name="register[phone]" value="" placeholder="Телефон" />

                        <div class="authForm_sbscr">
                            <input class="customInput customInput-defcheck jsCustomRadio" type="checkbox" name="subscribe" id="subscribe" checked="checked" />
                            <label class="customLabel" for="subscribe">Подписаться на рассылку,<br/> получить скидку 300 рублей </label>
                        </div>

                        <input type="submit" class="authForm_is btnsubmit" name="" value="Регистрация" />

                        <p class="authForm_accept">Нажимая кнопку «Регистрация», я подтверждаю<br/> свое согласие с <a href="/terms">Условиями продажи...</a></p>

                        <div class="authForm_socn">
                            Войти через

                            <ul class="authForm_socn_lst">
                                <? if ($oauthEnabled['facebook']): ?>
                                    <li class="authForm_socn_i">
                                        <a class="authForm_socn_lk authForm_socn_lk-fb" href="<?= $page->url('user.login.external', ['providerName' => 'facebook' ]) ?>" >Войти через FB</a>
                                    </li>
                                <? endif; ?>

                                <? if ($oauthEnabled['vkontakte']): ?>
                                    <li class="authForm_socn_i">
                                        <a class="authForm_socn_lk authForm_socn_lk-vk" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>" >Войти через VK</a>
                                    </li>
                                <? endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--/ секция регистрации -->
            </fieldset>
        </form>

        <form class="authForm js-resetForm js-formContainer" action="<?= $page->url('user.forgot') ?>" method="post" style="display: none">
            <!-- секция восстановления пароля -->
            <fieldset class="authForm_fld">
                <legend class="authForm_t legend">Восстановление пароля</legend>

                <!-- показываем при удачном восстановлении пароля, authForm_regbox скрываем -->
                <div class="authForm_regcomplt" style="display: none;">
                    Пароль отправлен на email.
                </div>
                <!--/ показываем при удачном восстановлении пароля, authForm_regbox скрываем -->

                <div class="authForm_regbox">
                    <input type="text" class="authForm_it textfield" name="" value="" placeholder="Email или телефон">

                    <input type="submit" class="authForm_is btnsubmit" name="" value="Отправить">
                </div>
            </fieldset>
            <!--/ секция восстановления пароля -->
        </form>

        <!-- показываем этот текст в окне входа на сайт -->
        <div class="authAct">
            <span
                class="brb-dt js-registerLink js-link"
                data-show-container="#auth-block .js-registerForm"
                data-hide-container="#auth-block .js-formContainer"
                data-show-link="#auth-block .js-authLink"
                data-hide-link="#auth-block .js-link"
            >Регистрация</span>
            <span
                class="brb-dt js-authLink js-link"
                data-show-container="#auth-block .js-authForm"
                data-hide-container="#auth-block .js-formContainer"
                data-show-link="#auth-block .js-registerLink"
                data-hide-link="#auth-block .js-link"
                style="display: none"
            >Войти</span>
        </div>

        <!-- показываем этот текст в окне регистрации -->
        <!-- div class="authAct"><span class="brb-dt">Вход в Enter</span></div-->

        <!-- показываем этот текст в окне восстановления пароля -->
        <!-- <div class="authAct">Вспомнили? <span class="brb-dt">Вход в Enter</span></div> -->
    </div>
    <!-- /Registration -->
</noindex>