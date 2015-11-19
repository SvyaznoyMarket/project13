<?php
/**
 * @var $page          \View\User\OrderPage
 * @var $subscriptions \Model\User\SubscriptionEntity[]
 * @var $flash         mixed|null
 */
?>

<?
$subscriptionsByType = [];
// сортировка каналов по типам
array_walk($subscriptions, function(\Model\User\SubscriptionEntity $subscription) use (&$subscriptionsByType) {
    $subscriptionsByType[$subscription->type][] = $subscription;
});
?>

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalPage">

    <div class="private-sections private-sections_gray grid">
        <h1 class="private-sections__head">Ваши подписки</h1>
        <div class="grid__col grid__col_2">


            <? if ($flash !== null) : ?>
                <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
            <? endif; ?>

            <div class="personalSubscr">
                <div class="personalSubscr_row">
                    <? foreach ($subscriptionsByType as $key => $subscriptionChunk): ?>
                        <? $i = 0; foreach ($subscriptionChunk as $subscription): $i++ ?>
                        <?
                            /** @var $subscription \Model\User\SubscriptionEntity */
                            if (!$subscription->channel) continue;
                            $elementId = sprintf('channel-%s_%s', $subscription->type, $subscription->channelId);
                        ?>
                            <input
                                class="js-user-subscribe-input jsCustomRadio customInput customInput-bigCheck"
                                id="<?= $elementId ?>"
                                type="checkbox"
                                name="channel[<?= $i ?>]"
                                <?= $subscription->isConfirmed ? 'checked' : '' ?>
                                data-url="<?= $page->url('user.subscriptions') ?>"
                                data-value="<?= $page->json([
                                    'subscribe' => [
                                        'channel_id' => $subscription->channelId,
                                        'type'       => $subscription->type,
                                        'email'      => $subscription->email,
                                    ]
                                ])?>"
                            />
                            <label class="private-sections__label label-for-customInput" for="<?= $elementId ?>"><?= $subscription->channel->name ?></label>
                        <? endforeach ?>
                    <? endforeach ?>
                </div>
            </div>
        </div>

        <div class="grid__col grid__col_2">
            <p class="private-sections__txt">
                Изолируя область наблюдения от посторонних шумов, мы сразу увидим, что катод представляет собой серийный фронт. Любое возмущение затухает, если интервально-прогрессийная континуальная форма ферментативно разъедает райдер, и это неудивительно, если вспомнить квантовый характер явления. Согласно учению об изотопах, эксикатор дает енамин.
                Суспензия наблюдаема. Эмиссия, несмотря на внешние воздействия, синхронно заставляет сверхпроводник. Процессуальное изменение, как неоднократно наблюдалось при постоянном воздействии ультрафиолетового облучения, пространственно неоднородно.
            </p>
        </div>

    </div>

    <div class="private-sections__modal js-modal">
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
                <label class="private-sections__modal-checkbox label-for-customInput" for="private-sections__modal-checkbox">
                    <span class="">Запомнить этот email для входа в личный кабинет <br>
                    Все подписки будут автоматически отправляться на этот адрес</span>
                </label>
                <input class="private-sections__modal-send" type="submit" value="Подписаться">
            </form>
            <a class="private-sections__modal-close js-modal-close" href="#"></a>
        </article>
    </div>

</div>