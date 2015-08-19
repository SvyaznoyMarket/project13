<?php

/**
 * @param \Helper\TemplateHelper $helper
 */
$f = function(
    \Helper\TemplateHelper $helper
) {

    $channelId = 1;
?>

<form class="subscribe-form" action="<?= $helper->url('subscribe.create') ?>">
    <input type="hidden" name="channel" value="<?= $channelId ?>">

    <div class="subscribe-form-group">
        <label>Ваш e-mail</label>
        <input class="subscribe-email" type="email" name="email">
        <button class="subscribe-form-btn" type="submit">Подписаться</button>
    </div>
</form>

<? }; return $f;