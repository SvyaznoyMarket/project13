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
        });
    });
</script>
<div id="slotsWrapWrapper"></div>
<div id="slotsPopup" class="popup">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="message"></div>
</div>