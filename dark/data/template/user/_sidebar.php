<?php
/**
 * @var $page \View\User\IndexPage
 */
?>

<?= $page->render('user/_show') ?>
<div class="cabinethelp">Заполни о себе больше информации это позволит нам сделать тебе интересные предложения</div>
<div class="pb15">
    <form action="<?= $page->url('user.edit') ?>" method="get">
        <input type="submit" class="button whitebutton" id="whitebutton" value="Заполнить мои данные"/>
    </form>
</div>
