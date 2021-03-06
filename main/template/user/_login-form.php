<?php
/**
 * @var $page \View\Layout
 * @var $form \EnterApplication\Form\LoginForm
 */

if (!isset($form)) $form = new \EnterApplication\Form\LoginForm();
if (!isset($redirect_to)) $redirect_to = null;
if (!isset($redirectUrlUserTokenParam)) $redirectUrlUserTokenParam = null;
$oauthEnabled = \App::config()->oauthEnabled;
?>
<div class="authForm authForm_login">
    <form class="js-authForm" data-state="default" action="<?= $page->url('user.login') ?>" method="post" data-error="<?= $page->json($form->errors) ?>">
        <input type="hidden" name="redirect_to" value="<?= $page->escape($redirect_to) ?>" class="js-authForm-redirectTo">
        <input type="hidden" name="redirect-url-user-token-param" value="<?= $page->escape($redirectUrlUserTokenParam) ?>" />

        <fieldset class="authForm_fld authForm_fld-scrll">
            <!-- секция входа -->
            <div class="authForm_inn">
                <div class="authForm_t legend jsAuthFormLoginTitle">Войти</div>

                <div class="authForm_msg jsAuthFormLoginMsg"></div>

                <div class="authForm_field">
                    <input type="text" autocomplete="off" class="authForm_it textfield js-login js-register-new-field js-input-custom-placeholder" data-field="username" name="signin[username]" value="<?= $form->username->value ?>">
                    <div class="custom-placeholder js-placeholder">Email или телефон</div>
                </div>

                <div class="authForm_hint js-password-container">
                    <div class="authForm_field">
                        <input type="password" autocomplete="off" class="authForm_it textfield js-password js-register-new-field js-input-custom-placeholder" data-field="password" name="signin[password]" value="" placeholder="">
                        <div class="custom-placeholder js-placeholder">Пароль</div>
                    </div>
                    <div class="authForm_hint_tx js-resetBtn">
                        <input
                            class="js-forgotButton authForm_hint--submit"
                            type="button"
                            data-url="<?= $page->url('user.forgot') ?>"
                            data-relation="<?= $page->json([
                                'field' => '.js-authForm [data-field="username"]',
                            ]) ?>"
                        >
                        <div class="authForm_hint-popup">
                            <span>Восстановить пароль</span>
                        </div>
                    </div>
                </div>

                <input type="submit" class="authForm_is authForm_is--login btnsubmit" name="" value="Войти" data-value="Войти" data-loading-value="Вход...">

                <div class="authForm_socn">
                    <ul class="authForm_socn_lst">
                        <? if ($oauthEnabled['facebook']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-fb js-socialAuth" href="<?= $page->url('user.login.external', ['providerName' => 'facebook', 'redirect_to' => $redirect_to]) ?>" >Facebook</a>
                            </li>
                        <? endif ?>

                        <? if ($oauthEnabled['vkontakte']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-vk js-socialAuth" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte', 'redirect_to' => $redirect_to]) ?>" >ВКонтакте</a>
                            </li>
                        <? endif ?>
                    </ul>
                </div>
            </div>
            <!--/ секция входа -->
        </fieldset>
    </form>
</div>
