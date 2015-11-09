<?php
/**
 * @var $page           \View\User\OrderPage
 * @var $userChannels   \Model\User\SubscriptionEntity[]|array
 * @var $flash          mixed|null
 */
?>

<?
$isOldView = \App::abTest()->isOldPrivate();

$i = 0; $channelsByType = [];
// сортировка каналов по типам
array_walk($userChannels, function($ch) use (&$channelsByType) {
    /** @var $ch \Model\User\SubscriptionEntity */
    $channelsByType[$ch->getType()][] = $ch;
});
?>

<?= $page->render($isOldView ? 'user/_menu' : 'user/_menu-1508', ['page' => $page]) ?>

<div class="personalPage">

    <div class="private-sections private-sections_gray grid">
        <h1 class="private-sections__head">Ваши подписки</h1>
        <div class="grid__col grid__col_2">


            <? if ($flash !== null) : ?>
                <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
            <? endif; ?>

            <form action="" method="post" class="personalSubscr">
                <fieldset class="personalSubscr_row">
                    <? foreach ($channelsByType as $key => $val) : ?>
                        <? foreach ($val as $channel) : ?>
                            <? /** @var $channel \Model\User\SubscriptionEntity */ ?>

                            <input class="jsCustomRadio customInput customInput-bigCheck js-modalShow" id="channel_<?= $channel->getType().$channel->getChannelId() ?>" type="checkbox"  name="channel[<?= $i ?>][is_confirmed]" <?= $channel->getIsConfirmed() ? 'checked' : '' ?> />
                            <label class="private-sections__label" for="channel_<?= $channel->getType().$channel->getChannelId() ?>"><?= $channel->getChannel()->getName() ?></label>
                            <input type="hidden" name="channel[<?= $i ?>][channel_id]" value="<?= $channel->getChannelId() ?>" />
                            <input type="hidden" name="channel[<?= $i ?>][type]" value="<?= $channel->getType() ?>" />
                            <input type="hidden" name="channel[<?= $i ?>][email]" value="<?= $channel->getEmail() ?>" />
                            <? $i++ ?>

                        <? endforeach; ?>
                    <? endforeach; ?>

                </fieldset>

            </form>
        </div>

        <div class="grid__col grid__col_2">
            <p class="private-sections__txt">
                Изолируя область наблюдения от посторонних шумов, мы сразу увидим, что катод представляет собой серийный фронт. Любое возмущение затухает, если интервально-прогрессийная континуальная форма ферментативно разъедает райдер, и это неудивительно, если вспомнить квантовый характер явления. Согласно учению об изотопах, эксикатор дает енамин.

                Суспензия наблюдаема. Эмиссия, несмотря на внешние воздействия, синхронно заставляет сверхпроводник. Процессуальное изменение, как неоднократно наблюдалось при постоянном воздействии ультрафиолетового облучения, пространственно неоднородно.
            </p>
        </div>

    </div>

    <div class="private-sections__modal js-modalLk">
        <article class="private-sections__modal-body">
            <header class="private-sections__modal-head">
                Подписаться на
                <span class="private-sections__modal-name-list">
                    Новые коллекции Tchibo?
                </span>

            </header>
            <form action="#">
                <fieldset class="private-sections__modal-row">
                    <input class="private-sections__modal-email" type="email" placeholder="Введите email">
                    <span class="private-sections__modal-email-desc">На этот email мы будем отправлять сообщения подписки</span>
                </fieldset>
                <input class="customInput" type="checkbox" id="private-sections__modal-checkbox">
                <label class="private-sections__modal-checkbox" for="private-sections__modal-checkbox">
                    <span>Все подписки будут автоматически отправляться на этот адрес</span>
                    <span>Запомнить этот email для входа в личный кабинет</span>
                </label>
                <input class="private-sections__modal-send" type="submit" value="Подписаться">
            </form>
            <a class="private-sections__modal-close js-modal-close" href="#"></a>
        </article>
    </div>

</div>