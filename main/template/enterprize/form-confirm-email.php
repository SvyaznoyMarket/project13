<form class="confirmForm" action="<?=\App::router()->generate('enterprize.confirmEmail.check') ?>" method="post">
    <label class="labelCode">Код</label>
    <input type="text" class="text" name="code" />
    <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
</form>

<form class="confirmForm" action="<?= \App::router()->generate('enterprize.confirmEmail.create') ?>" method="post">
    <input type="hidden" name="isRepeatRending" value="true" />
    <input style="margin-left: 0;" class="confirmCode bigbutton" type="submit" value="Отправить повторно" />
</form>

<p style="margin: 30px 0 0 0; font-size: 12px;" class="textConfirm">
    Если письмо затерялось или обнаружили ошибку, пожалуйста, напишите нам на <a style="text-decoration: underline;" href="http://my.enter.ru/feedback/">my.enter.ru/feedback/</a>.
</p>