<?php
/**
 * @var $page           \View\User\OrderPage
 * @var $userChannels   \Model\User\SubscriptionEntity[]|array
 */
?>

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalPage">
    <div class="personalTitle">Подписки</div>

    <form action="" method="post" class="personalSubscr">
        <fieldset class="personalSubscr_row">
            <legend class="legend">Email</legend>

            <? foreach ($userChannels as $channel) : ?>

            <input class="jsCustomRadio customInput customInput-bigCheck" id="channel_<?= $channel->getChannelId() ?>" type="checkbox"  name="channel_<?= $channel->getChannelId() ?>" <?= $channel->getIsConfirmed() ? 'checked' : '' ?> disabled />
            <label class="customLabel customLabel-bigCheck" for="channel_<?= $channel->getChannelId() ?>"><?= $channel->getChannel()->getName() ?></label>
            <br />

            <? endforeach; ?>

        </fieldset>

        <fieldset class="personalSubscr_clear">
            <input class="btnsubmit" type="submit" value="Сохранить"  disabled />
        </fieldset>
    </form>
</div>