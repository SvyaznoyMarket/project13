<?php
/**
 * @var $page           \View\User\OrderPage
 * @var $userChannels   \Model\User\SubscriptionEntity[]|array
 * @var $flash          mixed|null
 */
?>

<?
$i = 0; $channelsByType = [];
// сортировка каналов по типам
array_walk($userChannels, function($ch) use (&$channelsByType) {
    /** @var $ch \Model\User\SubscriptionEntity */
    $channelsByType[$ch->getType()][] = $ch;
});
?>

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalPage">
    <div class="personalTitle">Подписки</div>

    <? if ($flash !== null) : ?>
        <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
    <? endif; ?>

    <form action="" method="post" class="personalSubscr">
        <fieldset class="personalSubscr_row">
            <? foreach ($channelsByType as $key => $val) : ?>
                <legend class="legend"><?= $key == 'email' ? 'Email' : 'SMS' ?></legend>

                <? foreach ($val as $channel) : ?>
                    <? /** @var $channel \Model\User\SubscriptionEntity */ ?>

                    <input class="jsCustomRadio customInput customInput-bigCheck" id="channel_<?= $channel->getType().$channel->getChannelId() ?>" type="checkbox"  name="channel[<?= $i ?>][is_confirmed]" <?= $channel->getIsConfirmed() ? 'checked' : '' ?> />
                    <label class="customLabel customLabel-bigCheck" for="channel_<?= $channel->getType().$channel->getChannelId() ?>"><?= $channel->getChannel()->getName() ?></label>
                    <input type="hidden" name="channel[<?= $i ?>][channel_id]" value="<?= $channel->getChannelId() ?>" />
                    <input type="hidden" name="channel[<?= $i ?>][type]" value="<?= $channel->getType() ?>" />
                    <input type="hidden" name="channel[<?= $i ?>][email]" value="<?= $channel->getEmail() ?>" />
                    <? $i++ ?>

                <br />

                <? endforeach; ?>
            <? endforeach; ?>

        </fieldset>

        <fieldset class="personalSubscr_clear">
            <input class="btnsubmit" type="submit" value="Сохранить" />
        </fieldset>
    </form>
</div>