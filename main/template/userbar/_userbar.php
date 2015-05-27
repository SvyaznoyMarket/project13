<?php
/**
 * @var string $class
 */
?>

<ul class="<?= isset($class) ? $class : '' ?>">
    <?= $page->render('userbar/_userinfo') ?>
    <?= $page->render('userbar/_usercompare') ?>
    <?= $page->render('userbar/_usercart') ?>
</ul>