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
                /**
                 * @param Object state including .message and .code   
                 * @returns {undefined}
                 */
                userUnauthorized: function(state) {
                    ENTER.constructors.Login().openAuth();
                },
                notEnterprizeMember: function(state){
                    slotsPopup.messageBox.html(
                        'Принимать участие в розигрыше могут только участники программы Enter Prize.<br/>'
                        + '<b><a href="/enterprize">Стать участником программы</a></b>'
                    );
                    slotsPopup.lightbox_me({
                        centered: true,
                        autofocus: true,
                        onClose: function(){
                            slotsPopup.messageBox.html('');
                        }
                    });
                }
            },
            api_url: {
                init: "http://vadim.ent3.ru/game/slots/init",
                play: "http://vadim.ent3.ru/game/slots/play",
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