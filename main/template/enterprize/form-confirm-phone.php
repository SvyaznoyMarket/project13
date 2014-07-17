<?php
/**
 * @var $page            \View\DefaultLayout
 */
?>
<form class="confirmForm" action="<?= $page->url('enterprize.confirmPhone.check') ?>" method="post">
    <label class="labelCode">Код</label>
    <input type="text" class="text" name="code" />
    <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
</form>

<form class="confirmForm" action="<?= $page->url('enterprize.confirmPhone.create') ?>" method="post">
    <label class="labelCode">Если в течение трех минут не получили код</label>
    <input type="hidden" name="isRepeatRending" value="true" />
    <input type="submit" class="newCode mBtnGrey" value="Отправить повторно" />
</form>