<?php
/**
 * @var $page     \View\User\ChangePasswordPage
 * @var $user     \Session\User
 * @var $error    string
 * @var $message  string
 * @var $redirect string
 */
?>

<? if ($error): ?>
    <p class="red"><?= $error ?></p>
<? elseif ($message): ?>
    <p class="green"><?= $message ?></p>
<? endif ?>

<form class="userInfoEdit clearfix" action="<?= $page->url('user.changePassword') ?>" method="post">
        <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

        <div class="pr fr">
            <div class="help helpfr">
                Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные
                буквы, цифры или символы, но не должен включать широко распространенные слова и имена.
            </div>
        </div>
            
        <label class="userInfoEdit__label" for="password_password_old">Старый пароль</label>

        <input type="password" id="password_password_old" name="password_old" class="text width418 mb15" autocomplete="off" />

        <label class="userInfoEdit__label" for="password_password_new">Новый пароль</label>

        <input type="password" id="password_password_new" name="password_new" class="text width418 mb15" autocomplete="off" />

        <input type="submit" class="btnSave button bigbutton" id="bigbutton" value="Сохранить изменения"/>

        <div class="attention">
            <?= sprintf('Внимание! После смены пароля Вам придет %s с новым паролем', implode(' и ', array(
                $user->getEntity()->getEmail() ? 'письмо' : '',
                $user->getEntity()->getMobilePhone() ? 'SMS' : '',
            ))) ?>
        </div>
</form>