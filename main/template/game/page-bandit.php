<?php
/**
 * @var $page \View\Game\BanditPage
 * @var array $config
 */
?>

<?= $page->slotEnterPrizeWidget() ?>

<div id="slotsWrapWrapper" data-config="<?= $page->json($config) ?>">
    <div class="hello">
        <div class="rulesText">
            <h2><i>Enter Prize Jackpot</i></h2>
            <em>Enter Prize Jackpot</em> - это игровой автомат. Вы можете выиграть уникальные фишки, которых нет среди обычных
            предложений программы лояльности Enter Prize. Выигранную фишку можно сразу же потратить в нашем магазине.
            <br/>Фишка придет на Ваш телефон или e-mail.
        </div>
        <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
        <div class="yashare-auto-init pc_buttons" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,facebook,twitter"></div>
    </div>
</div>
<div id="slotsPopup" class="popup">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="message"></div>
</div>