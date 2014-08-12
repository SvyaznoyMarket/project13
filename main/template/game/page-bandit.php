<?php
/**
 * @var $page \View\Game\BanditPage
 */
?>

<?= $page->slotEnterPrizeWidget() ?>

<script type="text/javascript">
    $(window).load(function () {
        var
            slotsPopup = $('#slotsPopup'),
            animations_config = $('#slotsWrapWrapper').data('animations-config');

        slotsPopup.messageBox = slotsPopup.find('.message');

        $('#slotsWrapWrapper').slots({
            labels: {
                messageBox: {
                    demo: [
                        "Скидки до 70%!",
                        "Играй и выигрывай каждый день!"
                    ],
                    win: [
                        "Ваш выигрыш",
                        "Заберите вашу фишку",
                        "Ура! Победа!"
                    ],
                    nowin: [
                        "Осталось {tryRemain} попыток",
                        "Попробуй еще раз!",
                        "Крути барабан еще раз!",
                        "В следующий раз повезет!"
                    ],
                    spinning: [
                        "Удача все ближе",
                        "Скидки ждут тебя"
                    ]
                },
                notAvailable: {
                    message: "Автомат временно не работает. Приходите завтра",
                    optionPrize: '',//"Утешительный приз",
                    optionRemind: ''//"Напомнить мне"
                }
            },
            handlers: {
                userUnauthorized: function(self,state) {
                    self.stillInGameState();
                    window.registerAuth.init('authRegistration');
                },
                notEnterprizeMember: function(self,state){
                    self.stillInGameState();
                    window.registerAuth.init('update');
                },
                winExceeded: function(self,state) {
                    self.notAvailableState(state.message);
                },
                triesExceeded: function (self,state) {
                    self.notAvailableState(state.message);
                },
                undefinedError: function (self,state) {
                    self.notAvailableState();
                }
            },
            api_url: {
                init: "http://<?=\App::config()->mainHost?>/game/slots/init",
                play: "http://<?=\App::config()->mainHost?>/game/slots/play",
                img_led_off: "/css/game/slots/img/slot_led_off.png"
            }
        }, animations_config);
    });
</script>

<div id="slotsWrapWrapper" data-animations-config="<?= $page->json($animationsConfig) ?>">
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