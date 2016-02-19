<?php
/**
 * @var $page \View\Layout
 * @var $form \View\User\LoginForm
 */

if (!isset($form)) $form = new \View\User\LoginForm();
$oauthEnabled = \App::config()->oauthEnabled;
if (!isset($redirect_to)) $redirect_to = null;
?>
<div class="authForm authForm_login">
    <form class="js-authForm" data-state="default" action="<?= $page->url($form->getRoute()) ?>" method="post">
        <fieldset class="authForm_fld authForm_fld-scrll">
            <!-- секция входа -->
            <div class="authForm_inn">
                <div class="authForm_t legend jsAuthFormLoginTitle">Войти</div>

                <input type="text" class="authForm_it textfield js-login" data-field="username" name="signin[username]" value="<?= $form->getUsername() ?>" placeholder="Email или телефон">

                <div class="authForm_hint">
                    <input type="password" class="authForm_it textfield js-password" name="signin[password]" value="" placeholder="Пароль">
                    <div class="authForm_hint_tx">
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

                <input type="hidden" name="redirect_to" value="<?= $page->escape($redirect_to) ?>">

                <input type="submit" class="authForm_is authForm_is--login btnsubmit" name="" data-loading-value="Вхожу..." value="Войти">

                <div class="authForm_socn">
                    <ul class="authForm_socn_lst">
                        <? if ($oauthEnabled['facebook']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-fb" href="<?= $page->url('user.login.external', ['providerName' => 'facebook']) ?>" >Facebook</a>
                            </li>
                        <? endif ?>

                        <? if ($oauthEnabled['vkontakte']): ?>
                            <li class="authForm_socn_i">
                                <a class="authForm_socn_lk authForm_socn_lk-vk" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte']) ?>" >ВКонтакте</a>
                            </li>
                        <? endif ?>
                    </ul>
                </div>
            </div>
            <!--/ секция входа -->
        </fieldset>
    </form>
</div>
