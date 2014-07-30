<script type="text/javascript">
    $(window).load(function () {
        var slotsPopup = $('#slotsPopup');
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
                        "Осталось {0} попыток",
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
        },{
            isAvailable: true,
            ledPanel: {
                defaultAnimation: [
                    {
                        type: "random",
                        speed: 5,
                        n: 1,
                        m: 1,
                        color: "58,29,200"
                    }
                ],
                spiningAnimation: [
                    {
                        type: "leftToRight",
                        speed: 10,
                        n: 3,
                        m: 1,
                        color: "158,29,20"
                    }
                ],
                stopAnimation: [
                    {
                        type: "toggle",
                        speed: 7,
                        n: 2,
                        m: 1,
                        color: "158,129,20"
                    }
                ]
            },
            textPanel: {
                defaultAnimation: {
                    animationType: "leftToRight",
                    step: 3,
                    delay: 0,
                    speed: 20
                },
                spiningAnimation: {
                    animationType: "rightToLeft",
                    step: 6,
                    delay: 0,
                    speed: 20
                },
                winAnimation: {
                    animationType: "random",
                    step: 2,
                    delay: 0,
                    speed: 300
                },
                loseAnimation: {
                    animationType: "toggle",
                    step: 2,
                    delay: 0,
                    speed: 300
                }
            },
            game: {
                maxTimeSpinning: 5000
            }
        });
    });
</script>
<div id="slotsWrapWrapper">
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