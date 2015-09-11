<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $channelId
 * @param \EnterModel\Error|null $error
 * @param string $location
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $channelId = '1',
    \EnterModel\Error $error = null,
    $location = 'top'
) {
?>

<form class="subscribe-form" action="<?= $helper->url('subscribe.friend.create') . ('bottom' === $location ? '#bottom' : '') ?>" method="post">
    <input type="hidden" name="channel" value="<?= $channelId ?>">

    <div class="subscribe-form-group<? if ($error): ?> error<? endif ?>">
        <label>Ваш e-mail</label>
        <input class="subscribe-email" type="text" name="email" required>
        <span class="error-txt"><?= ($error ? $error->message : null) ?></span>
        <button class="subscribe-form-btn" type="submit">Подписаться</button>
    </div>
</form>

<? }; return $f;