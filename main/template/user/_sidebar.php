<?php
/**
 * @var $page \View\User\IndexPage
 */
?>

<?= $page->render('user/_show') ?>

<div class="userMore">
	Заполните о себе больше информации - это позволит нам сделать вам интересные предложения.
</div>

<form action="<?= $page->url('user.edit') ?>" method="get">
    <input type="submit" class="btnMore button whitebutton" id="whitebutton" value="Заполнить мои данные"/>
</form>
