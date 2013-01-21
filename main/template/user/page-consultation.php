<?php
/**
 * @var $page    \View\User\ChangePasswordPage
 * @var $user    \Session\User
 * @var $form    \View\User\ConsultationForm
 * @var $error   string
 * @var $message string
 */
?>

<? if ($error): ?>
    <p class="red"><?= $error ?></p>
<? elseif ($message): ?>
    <p class="green"><?= $message ?></p>
<? endif ?>


<form action="<?= $page->url('user.consultation') ?>" class="bForm" method="post">

    <div class="bInputBlock">
        <h2 class="bInputBlock__eH2">Ваше имя</h2>
        <p class="bInputBlock__eP">Введите имя, чтобы мы знали, как к вам обращаться</p>
        <input type="text" id="callback_name" value="<?= $form->getName() ?>" name="form[name]" class="bInputBlock__eInput" />
    </div>
    <div class="bInputBlock">
        <h2 class="bInputBlock__eH2">Ваша электронная почта</h2>
        <p class="bInputBlock__eP">Введите реальный адрес, на него вы получите ответ на ваше сообщение</p>
        <input type="text" id="callback_email" value="<?= $form->getEmail() ?>" name="form[email]" class="bInputBlock__eInput" />
    </div>

    <div class="bInputBlock">
        <h2 class="bInputBlock__eH2">Тема</h2>
        <p class="bInputBlock__eP">Четко сформулированная тема сообщения облегчит поиск вашего письма среди остальных</p>
        <input type="text" id="callback_subject" value="<?= $form->getSubject() ?>" name="form[subject]" class="bInputBlock__eInput" />
    </div>

    <div class="bInputBlock">
        <h2 class="bInputBlock__eH2">Сообщение</h2>
        <textarea id="callback_message" name="form[message]" value="<?= $form->getMessage() ?>" class="bInputBlock__eTextarea" cols="30" rows="4"></textarea>
    </div>

    <div class="bComment">Все поля обязательны для заполнения</div>
    <input type="submit" value="Отправить сообщение" id="bigbutton" class="bYellowButton button yellowbutton" />
</form>
