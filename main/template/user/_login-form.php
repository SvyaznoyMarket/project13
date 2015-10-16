<?php
/**
 * @var $page \View\Layout
 * @var $form \View\User\LoginForm
 */

if (!isset($form)) $form = new \View\User\LoginForm();
$oauthEnabled = \App::config()->oauthEnabled;
if (!isset($redirect_to)) $redirect_to = null;
?>

<form class="authForm authForm_login js-authForm" data-state="default" action="<?= $page->url($form->getRoute()) ?>" method="post">
    <fieldset class="authForm_fld authForm_fld-scrll">
        <!-- секция входа -->
        <div class="authForm_inn">
            <div class="authForm_t legend jsAuthFormLoginTitle">Вход в Enter</div>

            <input type="text" class="authForm_it textfield" name="signin[username]" value="<?= $form->getUsername() ?>" placeholder="Email или телефон">

            <div class="authForm_hint">
                <input type="password" class="authForm_it textfield" name="signin[password]" value="" placeholder="Пароль">
                    <span
                        class="authForm_hint_tx authForm_resetLink js-link"
                        data-value="<?= $page->json(['target' => '#auth-block', 'state' => 'reset']) ?>"
                    >забыли?</span>
            </div>

            <input type="hidden" name="redirect_to" value="<?= $page->escape($redirect_to) ?>">

            <input type="submit" class="authForm_is btnsubmit" name="" data-loading-value="Вхожу..." value="Войти">

            <div class="authForm_socn">
                Войти через

                <ul class="authForm_socn_lst">
                    <? if ($oauthEnabled['facebook']): ?>
                        <li class="authForm_socn_i">
                            <a class="authForm_socn_lk authForm_socn_lk-fb" href="<?= $page->url('user.login.external', ['providerName' => 'facebook']) ?>" >Войти через FB</a>
                        </li>
                    <? endif ?>

                    <? if ($oauthEnabled['vkontakte']): ?>
                        <li class="authForm_socn_i">
                            <a class="authForm_socn_lk authForm_socn_lk-vk" href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte']) ?>" >Войти через VK</a>
                        </li>
                    <? endif ?>
                </ul>
            </div>
        </div>
        <!--/ секция входа -->
    </fieldset>
</form>