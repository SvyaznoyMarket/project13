<?php
/**
 * @var $page \View\Layout
 */

$oauthEnabled = \App::config()->oauthEnabled;
?>

<form class="authForm authForm_register js-registerForm" action="<?= $page->url('user.register') ?>" method="post" style="/*display: none*/">
    <fieldset class="authForm_fld authForm_fld-scrll">
        <!-- секция регистрации -->
        <div class="authForm_inn">
            <div class="authForm_t legend">Регистрация</div>

            <!--<input type="hidden" name="register[global]" disabled="disabled">-->

            <!-- показываем при удачной регистрации, authForm_regbox скрываем -->
            <div class="js-message authForm_regcomplt"></div>
            <!--/ показываем при удачной регистрации, authForm_regbox скрываем -->

            <div class="authForm_regbox">
                <label class="authForm_lbl">Как к вам обращаться?</label>

                <input type="text" class="authForm_it textfield" name="register[first_name]" value="" placeholder="Имя" />

                <input type="text" class="authForm_it textfield" name="register[email]" value="" placeholder="Email" />

                <input type="text" class="authForm_it textfield js-phoneField" name="register[phone]" value="" placeholder="Телефон" />

                <div class="authForm_sbscr">
                    <input class="customInput customInput-defcheck jsCustomRadio js-customInput js-registerForm-subscribe" type="checkbox" name="subscribe" id="subscribe" checked disabled/>
                    <label class="customLabel customLabel-defcheck" for="subscribe">Стать участником Enter Prize,<br/> получить скидку 300 рублей </label>
                </div>
                <div class="oferta-agreement">
                    <input class="customInput customInput-defcheck jsCustomRadio js-customInput" type="checkbox" name="agreed" id="registerForm-agreed" />
                    <label class="customLabel customLabel-defcheck" for="registerForm-agreed">Согласен <a href="/reklamnaya-akcia-enterprize">с условиями оферты</a></label>
                </div>

                <input type="submit" class="authForm_is btnsubmit" name="" value="Регистрация" />

                <p class="authForm_accept">Нажимая кнопку «Регистрация», я подтверждаю<br/> свое согласие с <a href="/terms">Условиями продажи...</a></p>

                <div class="authForm_socn">
                    Войти через

                    <ul class="authForm_socn_lst">
                        <? if ($oauthEnabled['facebook']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-fb js-registerForm-socnetLink" href="<?= $page->url('user.login.external', ['providerName' => 'facebook' ]) ?>" >Войти через FB</a>
                            </li>
                        <? endif ?>

                        <? if ($oauthEnabled['vkontakte']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-vk js-registerForm-socnetLink" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>" >Войти через VK</a>
                            </li>
                        <? endif ?>
                    </ul>
                </div>
            </div>
        </div>
        <!--/ секция регистрации -->
    </fieldset>
</form>