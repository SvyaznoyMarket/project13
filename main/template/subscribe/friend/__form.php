<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $channelId
 * @param \EnterModel\Error|null $error
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $channelId = '1',
    \EnterModel\Error $error = null
) {
?>

<form class="subscribe-form" action="<?= $helper->url('subscribe.friend.create') ?>" method="post">
    <input type="hidden" name="channel" value="<?= $channelId ?>">

    <div class="subscribe-form-group<? if ($error): ?> error<? endif ?>">
        <label>Ваш e-mail</label>
        <input class="subscribe-email" type="email" name="email" required>
        <span class="error-txt"><!-- сообщение об ошибке --></span>
        <button class="subscribe-form-btn" type="submit">Подписаться</button>
    </div>
</form>

<? }; return $f;