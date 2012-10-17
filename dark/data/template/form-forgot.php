<?php
/**
 * @var $page \View\Layout
 */
?>

<?php
$formId = isset($formId) ? $formId : 'reset-pwd-form';
$hasLoginLink = isset($hasLoginLink) ? $hasLoginLink : true;
$title = isset($title) ? $title : 'Восстановление пароля:'
?>

<form id="<?= $formId ?>" style="display: none;" action="<?= $page->url('user.forgot') ?>" class="form" method="post">
    <div class="fl width327 mr20">

        <?php if ($title): ?>
            <div class="font16 pb20"><?= $title ?></div>
        <?php endif ?>

        <div class="pb5">Введите e-mail или мобильный телефон, который использовали при регистрации, и мы пришлем вам пароль.</div>
        <div class="error_list"></div>
        <div class="pb5">
            <input name="login" type="text" class="text width315 mb10" value=""/>
        </div>
        <input type="submit" class="fr button whitebutton" value="Отправить запрос"/>

        <div class="clear pb10"></div>

        <?php if ($hasLoginLink): ?>
            Если вы вспомнили пароль, то вам надо лишь<br/><strong><a id="remember-pwd-trigger" href="javascript:void(0)" class="orange underline">войти в систему</a></strong>.
        <?php endif ?>

    </div>
</form>