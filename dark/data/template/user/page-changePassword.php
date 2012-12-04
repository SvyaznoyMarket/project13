<?php
/**
 * @var $page  \View\User\ChangePasswordPage
 * @var $user  \Session\User
 * @var $error string
 */
?>

<? if ($error): ?>
    <p class="red"><?= $error ?></p>
<? endif ?>

<form action="<?= $page->url('user.changePassword') ?>" method="post">
    <div class="fl width430">

        <div class="pr fr">
            <div class="help">
                <br/><br/><br/>
                Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные
                буквы, цифры или символы, но не должен включать широко распространенные слова и имена.
            </div>
        </div>

        <div class="pb10">
            <label for="password_password_old">Старый пароль</label>
        </div>
        <input type="password" id="password_password_old" name="password_old" class="text width418 mb15" autocomplete="off" />

        <div class="pb10">
            <label for="password_password_new">Новый пароль</label>
        </div>
        <input type="password" id="password_password_new" name="password_new" class="text width418 mb15" autocomplete="off" />

        <div class="clear pb10"></div>
        <div class="pb20">
            <input type="submit" class="button yellowbutton" id="bigbutton" value="Сохранить изменения"/>
        </div>

        <div class="attention font11">
            <?= sprintf('Внимание! После смены пароля Вам придет %s с новым паролем', implode(' и ', array(
                $user->getEntity()->getEmail() ? 'письмо' : '',
                $user->getEntity()->getMobilePhone() ? 'SMS' : '',
            ))) ?>
        </div>

    </div>
</form>