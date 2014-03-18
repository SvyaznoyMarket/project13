<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $errors array|null
 */
?>

<? if (is_array($errors) && !empty($errors)): ?>
    <? foreach ($errors as $error): ?>
        <p class="red enterprizeWar"><?= $error ?></p>
    <? endforeach ?>
<? endif ?>


﻿



