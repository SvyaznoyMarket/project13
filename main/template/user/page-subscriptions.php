<?php
/**
 * @var $page                          \View\User\OrderPage
 * @var $channelsById                  \Model\Subscribe\Channel\Entity[]
 * @var $subscriptions                 \Model\User\SubscriptionEntity[]
 * @var $subscription                  \Model\User\SubscriptionEntity
 * @var $subscriptionsGroupedByChannel array
 * @var $flash                         mixed|null
 */
?>

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalPage">

    <div class="private-sections private-sections_gray grid">
        <h1 class="private-sections__head">Ваши подписки</h1>
        <div class="grid__col grid__col_2">


            <? if (false && $flash !== null) : ?>
                <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
            <? endif; ?>

            <div class="personalSubscr">
                <div class="personalSubscr_row">
                    <? $i = 0; foreach ($channelsById as $channel): $i++ ?>
                    <?
                        $subscription = isset($subscriptionsGroupedByChannel[$channel->id]) ? (reset($subscriptionsGroupedByChannel[$channel->id]) ?: null) : null;
                        if (!$channel->isActive && !$subscription) continue;

                        $elementId = sprintf('channel-%s', md5(json_encode($channel, JSON_UNESCAPED_UNICODE)));
                    ?>
                        <input
                            class="js-user-subscribe-input jsCustomRadio customInput customInput-bigCheck"
                            id="<?= $elementId ?>"
                            type="checkbox"
                            name="channel[<?= $i ?>]"
                            <?= $subscription ? 'checked' : '' ?>
                            data-set-url="<?= $page->url('user.subscriptions') ?>"
                            data-delete-url="<?= $page->url('user.subscriptions', ['delete' => true]) ?>"
                            data-value="<?= $page->json([
                                'subscribe' => [
                                    'channel_id' => $channel->id,
                                    'type'       => 'email',
                                    'email'      => $user->getEntity()->getEmail(),
                                ]
                            ])?>"
                        />
                        <label class="private-sections__label label-for-customInput" for="<?= $elementId ?>"><?= $channel->name ?></label>
                    <? endforeach ?>
                </div>
            </div>
        </div>

        <div class="grid__col grid__col_2">
            <p class="private-sections__txt"></p>
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