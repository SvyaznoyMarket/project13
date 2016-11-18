<form class="confirmForm" action="<?= \App::router()->generateUrl('enterprize.confirmPhone.check') ?>" method="post">
    <label class="labelCode">Код</label>
    <input type="text" class="text" name="code" />
    <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
</form>

<form class="confirmForm" action="<?= \App::router()->generateUrl('enterprize.confirmPhone.create') ?>" method="post">
    <label class="labelCode">Если в течение трех минут не получили код</label>
    <input type="hidden" name="isRepeatRending" value="true" />
    <input type="submit" class="confirmCode bigbutton" value="Отправить повторно" />
</form>