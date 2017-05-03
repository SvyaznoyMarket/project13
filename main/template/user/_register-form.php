<?php
/**
 * @var $page \View\Layout
 */

$oauthEnabled = \App::config()->oauthEnabled;
?>
<div class="authForm authForm_register">
    <form class="js-registerForm" action="<?= $page->url('user.register') ?>" method="post" data-error="">
        <input type="hidden" name="redirect_to" value="" class="js-registerForm-redirectTo" />
        <input type="hidden" name="loginAfterRegister" value="" class="js-registerForm-loginAfterRegister" />

        <fieldset class="authForm_fld authForm_fld-scrll">
            <!-- секция регистрации -->
            <div class="authForm_inn">
                <div class="authForm_t legend">Зарегистрироваться</div>

                <!-- показываем при удачной регистрации, authForm_regbox скрываем -->
                <div class="js-message authForm_regcomplt"></div>
                <!--/ показываем при удачной регистрации, authForm_regbox скрываем -->

                <div class="authForm_regbox">
                    <label class="authForm_lbl">Как к вам обращаться?</label>

                    <div class="authForm_field">
                        <input type="text" class="authForm_it textfield js-register-new-field js-register-new-field-email js-input-custom-placeholder" data-field="email" name="register[email]" value=""/>
                        <div class="custom-placeholder js-placeholder">Email</div>
                    </div>

                    <div class="authForm_field">
                        <input type="text" class="authForm_it textfield js-phoneField js-input-custom-placeholder" data-field="phone" name="register[phone]" value=""/>
                        <div class="custom-placeholder js-placeholder">Телефон</div>
                    </div>

                    <div class="authForm_field">
                        <input type="text" class="authForm_it textfield js-register-new-field js-register-new-field-name js-input-custom-placeholder" data-field="first_name" name="register[first_name]" value=""/>
                        <div class="custom-placeholder js-placeholder">Имя</div>
                    </div>

                    <div class="oferta-agreement">
                        <input class="customInput customInput-defcheck js-register-new-field" data-field="agreed" type="checkbox" name="register[agreed]" id="registerForm-agreed" />
                        <label class="customLabel customLabel-defcheck" for="registerForm-agreed">Согласен <a href="/terms-sordex" target="_blank">с условиями оферты</a></label>
                    </div>

                    <input type="submit" class="authForm_is btnsubmit" name="" value="Зарегистрироваться" data-value="Зарегистрироваться" data-loading-value="Регистрация..." />
                </div>
            </div>
            <!--/ секция регистрации -->
        </fieldset>
    </form>

    <div class= "authForm__register-good js-register-good js-authContainer">
        <span class="authForm_reset-close js-authForm-close">Закрыть</span>
        <div class="authForm_t legend">Зарегистрироваться</div>
        <div class="authForm__register-good-title">
            <span class="js-user-good-name"></span><span>, вы зарегистрировались</span>
        </div>
        <span class="authForm__register-good-txt js-registerTxt">Мы отправили Вам пароль на указанный email</span>
        <div class="authForm__register-good-error">
            <span>Не получили письмо?</span>
            <a
                class="js-forgotButton"
                href="#"
                data-url="<?= $page->url('user.forgot') ?>"
                data-relation="<?= $page->json([
                    'field' => '.js-registerForm [data-field="email"]',
                ]) ?>"
            >Отправить повторно</a>
        </div>
    </div>
</div>
