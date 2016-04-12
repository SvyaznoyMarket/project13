<?php
/**
 * @var $page \View\Subscribe\DeletePage
 * @var $error \Exception|null
 * @var $message string|null
 * @var $success bool|null
 */
?>

<div class="entry-content">
    <h1>Управление подпиской</h1>

    <? if ($message): ?>
        <p><?= $message ?></p>
    <? endif ?>

    <? if ($error): ?>
        <p><?= $error->getMessage() ?></p>
    <? endif ?>
</div>