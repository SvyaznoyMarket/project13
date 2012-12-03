<?php
/**
 * @var $page     \View\Layout
 * @var $request  \Http\Request
 * @var $redirect string
 */
?>

<?php
if (empty($redirect)) $redirect = $request->getRequestUri();
if (!isset($form)) $form = new \View\User\RegistrationForm();
?>

<form id="register-form" action="<?= $page->url('user.register') ?>" class="form" method="post">
    <input type="hidden" name="redirect_to" value="<?= $redirect ?>"/>

    <div class="fr width327 ml20">
        <div class="font16 pb20">Я новый пользователь</div>

        <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

        <div class="pb5">Как к вам обращаться?</div>
        <div class="pb5">
            <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>
            <input required="required" type="text" id="register_first_name" class="text width315 mb10" name="register[first_name]" value="<?= $form->getFirstName() ?>" tabindex="5"/>
        </div>
        <div class="pb5">E-mail или мобильный телефон:</div>
        <div class="pb5">
            <? if ($error = $form->getError('username')) echo $page->render('_formError', array('error' => $error)) ?>
            <input required="required" type="text" id="register_username" class="text width315 mb10" name="register[username]" value="<?= $form->getUsername() ?>" tabindex="6"/>
        </div>
        <input type="submit" class="fr button bigbutton" value="Регистрация" tabindex="10"/>

        <? if (\App::config()->user['corporateRegister']): ?>
        <p><a href="<?= $page->url('user.registerCorporate') ?>" class="orange underline">регистрация юридического лица</a></p>
        <? endif ?>

    </div>
</form>