<?php
/**
 * @var $page \View\Layout
 */

$oauthEnabled = \App::config()->oauthEnabled;
?>
<div class="authForm authForm_register">
    <form class="js-registerForm" action="<?= $page->url('user.register') ?>" method="post" data-error="">
        <fieldset class="authForm_fld authForm_fld-scrll">
            <!-- секция регистрации -->
            <div class="authForm_inn">
                <div class="authForm_t legend">Зарегистрироваться</div>

                <!--<input type="hidden" name="register[global]" disabled="disabled">-->

                <!-- показываем при удачной регистрации, authForm_regbox скрываем -->
                <div class="js-message authForm_regcomplt"></div>
                <!--/ показываем при удачной регистрации, authForm_regbox скрываем -->

                <div class="authForm_regbox">
                    <label class="authForm_lbl">Как к вам обращаться?</label>

                    <input type="text" class="authForm_it textfield js-register-new-name" name="register[first_name]" value="" placeholder="Имя" />

                    <input type="text" class="authForm_it textfield  js-register-new-email" name="register[email]" value="" placeholder="Email" />

                    <input type="text" class="authForm_it textfield js-phoneField" name="register[phone]" value="" placeholder="Телефон" />

                    <div class="authForm_sbscr">
                        <input class="customInput customInput-defcheck jsCustomRadio js-customInput js-registerForm-subscribe" type="hidden" name="subscribe" id="subscribe" checked disabled/>
                    </div>
                    <div class="oferta-agreement">
                        <input class="customInput customInput-defcheck jsCustomRadio js-customInput" type="checkbox" name="register[agreed]" id="registerForm-agreed" />
                        <label class="customLabel customLabel-defcheck" for="registerForm-agreed">Согласен <a href="/reklamnaya-akcia-enterprize" target="_blank">с условиями оферты</a></label>
                    </div>

                    <input type="submit" class="authForm_is btnsubmit" name="" value="Зарегистрироваться" />
                </div>
            </div>
            <!--/ секция регистрации -->
        </fieldset>
    </form>

    <div class= "authForm__register-good js-register-good">
        <div class="authForm_t legend">Зарегистрироваться</div>
        <div class="authForm__register-good-title">
            <span class="js-user-good-name">Dfcbkbq</span>
            <span>, вы зарегистрировались</span>
        </div>
        <span class="authForm__register-good-txt">Мы отправили Вам пароль на указанный email</span>
        <div class="authForm__register-good-error">
            <span>Не получили письмо?</span>
            <a class="js-email-repeat" href="">Отправить повторно</a>
        </div>
    </div>
</div>
