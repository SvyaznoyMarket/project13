<script type="text/javascript">
    $(window).load(function () {
        var slotsPopup = $('#slotsPopup');
        slotsPopup.messageBox = slotsPopup.find('.message');
        
        $('#slotsWrapWrapper').slots({
            labels: {
                messageBox: {
                    demo: [
                        "!!! играйте на скидку !!!",
                        "Это просто текст",
                        "Внезапный текст"
                    ],
                    win: [
                        "!!! выигрышь !!!",
                        "ДЖЕКПОТ!!!!!",
                        "Мама мыла раму"
                    ],
                    nowin: [
                        "ничего не вышло {0}",
                        "то ли еще будет {0}",
                        "попробуйте еще {0}",
                        "осталось {0} попыток"
                    ],
                    spinning: [
                        "трах тиби дох",
                        "абра кадабра",
                        "кручу верчу"
                    ]
                },
                notAvailable: {
                    message: "Автомат временно не работает. Приходите завтра",
                    optionPrize: "Утешительный приз",
                    optionRemind: "Напомнить мне"
                }
            },
            handlers: {
                userUnauthorized: function(state) {
                    debugger;
                    window.registerAuth.init('authRegistration');
                },
                notEnterprizeMember: function(state){
                    window.registerAuth.init('confirm');
                }
            },
            api_url: {
                init: "http://<?=\App::config()->mainHost?>/game/slots/init",
                play: "http://<?=\App::config()->mainHost?>/game/slots/play",
                img_led_off: "/css/game/slots/img/slot_led_off.png"
            }
        },{
            isAvailable: true,
            "ledPanel": {
                "defaultAnimation": [
                    {
                        "type": "random",
                        "speed": 5,
                        "n": 1,
                        "m": 1,
                        "color": "58,29,200"
                    }
                ],
                "spiningAnimation": [
                    {
                        "type": "leftToRight",
                        "speed": 10,
                        "n": 3,
                        "m": 1,
                        "color": "158,29,20"
                    }
                ],
                "stopAnimation": [
                    {
                        "type": "toggle",
                        "speed": 7,
                        "n": 2,
                        "m": 1,
                        "color": "158,129,20"
                    }
                ]
            },
            "textPanel": {
                "defaultAnimation": {
                    "animationType": "leftToRight",
                    "step": 3,
                    "delay": 0,
                    "speed": 20
                },
                "spiningAnimation": {
                    "animationType": "rightToLeft",
                    "step": 6,
                    "delay": 0,
                    "speed": 20
                },
                "winAnimation": {
                    "animationType": "random",
                    "step": 2,
                    "delay": 0,
                    "speed": 300
                },
                "loseAnimation": {
                    "animationType": "toggle",
                    "step": 2,
                    "delay": 0,
                    "speed": 300
                }
            },
            "game": {
                "maxTimeSpinning": 5000
            }
        });
    });
</script>
<div id="slotsWrapWrapper"></div>
<div id="slotsPopup" class="popup">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="message"></div>
</div>