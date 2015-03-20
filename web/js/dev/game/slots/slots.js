$.fn.slots = function (slot_config, animations_config) {
    if (!slot_config) {
        slot_config = {
            labels: {
                messageBox: {
                    demo: ["!!! играйте на скидку !!!", "Это просто текст", "Внезапный текст"],
                    win: ["!!! выигрышь !!!", "ДЖЕКПОТ!!!!!", "Мама мыла раму"],
                    nowin: ["ничего не вышло {tryRemain}", "то ли еще будет {tryRemain}", "попробуйте еще {tryRemain}", "осталось {tryRemain} попыток"],
                    spinning: ["трах тиби дох", "абра кадабра", "кручу верчу"]

                },
                notAvailable: {
                    message: "Автомат временно не работает. Приходите завтра",
                    optionPrize: "Утешительный приз",
                    optionRemind: "Напомнить мне"
                }
            },
            handlers: {
                userUnauthorized: function (self, message) {
                    self.stillInGameState();
                    console.log('userUnauthorized: ' + message);
                }
            },
            api_url: {
                init: "http://vadim.ent3.ru/game/slots/init?t=" + new Date().getTime(),
                play: "http://vadim.ent3.ru/game/slots/play?t=" + new Date().getTime(),
                img_led_off: "/img/slot_led_off.png"

            },
            userAutoPlay: false
        };
    }
    String.prototype.format = function (args) {
        if (!args) return this.toString();
        var str = this;

        return str.replace(String.prototype.format.regex, function (item) {
            var intVal = item.substring(1, item.length - 1);
            return args[intVal] ? args[intVal] : "";
        });
    };
    String.prototype.format.regex = new RegExp("{-?[0-9A-z]*}", "g");


    var g = function () {
        var lastTime = 0;
        var vendors = ['ms', 'moz', 'webkit', 'o'];
        for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
            window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
            window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
                || window[vendors[x] + 'CancelRequestAnimationFrame'];
        }

        if (!window.requestAnimationFrame)
            window.requestAnimationFrame = function (callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function () {
                        callback(currTime + timeToCall);
                    },
                    timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };

        if (!window.cancelAnimationFrame)
            window.cancelAnimationFrame = function (id) {
                clearTimeout(id);
            };
    };
    g();

    window.requestAnimFrame = (function () {
        return  window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame
    });
    var $el = this;
    $el.slotMachine = {
        canvas: null,
        context: null,
        maxX: null,
        maxY: null,
        lamps: [],
        lamps2: [],
        isStopped: false,
        slot_config: slot_config,
        options: {type: "random", speed: 3, n: 0, m: 0, color: "58,209,255"},
        reels: $el.find('#reelsWrapper .chips'),
        reels2: $el.find('#reelsWrapper .chips2'),
        $notAvailableMessageText: $el.find('#notAvailable .message'),
        $notAvailablePrizeText: $el.find('#notAvailable .option.prize'),
        $notAvailableRemindText: $el.find('#notAvailable .option.remind'),
        $slotMachine: $el.find('#slotsMachine'),
        $winChipContainerChip: $el.find('#winContainer .chip'),
        $winContainer: $el.find('#winContainer'),
        $lb_overlay: $el.find('.lbOverlay'),
        initialize: function () { //инитим автомат
            var self = this;
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            self.isie = msie > 0;
            self.slot_config = slot_config;
            self.config = animations_config ? animations_config : {};//сохраняем конфиги слот машины
            self.renderMarkup();
            self.$winChipContainerChip.click(function () {
                $el.find('.winChip').removeClass('winChip');
                $el.find('.lbOverlay').hide();
                $el.find('#winContainer').hide().removeClass('winner bigwinner');
                self.setLedPanelOptions('default');
                self.messageBox.stopAnimation();
                self.messageBox.setRandomText("demo");
                self.messageBox.animateText("defaultAnimation");
            });

            if (!self.config.isAvailable) {
                self.notAvailableState(); // если автомат недоступен - стартуем режим недоступен
            } else {
                self.initLedPanel();//инициализируем лед панель
                self.messageBox.init();//инитим текстовую панель
            }

            self.send({
                type: "GET",
                url: self.slot_config.api_url.init,
                data: {},
                cb: function (response) {
                    if (response.success) {
                        self.game.init(response.reels);//иначе отрисовываем рельсы
                        self.game.bindAll();//биндимся на клики старт и стоп
                        self.demoMode = !response.user;//залогинен ли юзер

                    } else {
                        self.notAvailableState(response.error);
                    }
                },
                err: function () {
                    //fake response
                    var response = {"success": true, "isAvailable": true, "user": null, "reels": [
                        [
                            {"uid": "320df931-fb71-11e3-93f1-e4115baba630", "label": "\u0411\u044b\u0442\u043e\u0432\u0430\u044f \u0442\u0435\u0445\u043d\u0438\u043a\u0430", "url": "\/catalog\/appliances", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/bit.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "125", "is_currency": true},
                            {"uid": "48e75ecc-fb70-11e3-93f1-e4115baba630", "label": "\u0417\u043e\u043e\u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/tovari-dlya-givotnih", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/pets.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "5", "is_currency": false},
                            {"uid": "2fdb06db-f76f-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e\u043a\u0430\u043c\u0435\u0440\u044b SONY", "url": "\/products\/set\/2060701004262,2060701007775,2060701006570,2060701006372,2060701007782", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "7c9a0bc5-fb71-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "e73952ca-fb6e-11e3-93f1-e4115baba630", "label": "\u0421\u043f\u043e\u0440\u0442 \u0438 \u041e\u0442\u0434\u044b\u0445", "url": "\/catalog\/sport", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "10", "is_currency": false},
                            {"uid": "e68e98b2-fb9b-11e3-93f1-e4115baba630", "label": "\u0421\u043c\u0430\u0440\u0442\u0444\u043e\u043d\u044b", "url": "\/products\/set\/2060302008454,2060302008461,2060302008478,2060302008232,2060302008485,2060302008492,2060302008201,2060302008508,2060302008515,2060302008522,2060302008744,2060302008225,2060302007938,2060302007983,2060302006832,2060302007990,2060302007945,2060302007976,2060302008003,2060302007969,2060302007952,2060301001777,2060301003931,2060301004907,2060301004914,2060302004852,2060302006849,2060304000142,2060304000159,2060304000166,2060304000173,2060302008331,2060302007631,2060302007624,2060302006559,2060301004853,2060301004846,2060302006573,2060302006566,2060302006504,2060301004563,2060302008874,2060302008850,2060302008911,2060302008928,2060302008126,2060302008140,2060302008164,2060302008157,2060302008171,2060302006887", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "6a5e1d79-fb9b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007800,2060201009576,2060201010213,2060201010350,2060201010374,2060201010602,2060201010619,2060201010626,2060201010633,2060201010640,2060201010701,2060201010749,2060201010794,2060201010800,2060201010817,2060201010848,2060201010855,2060201010862,2060201010879,2060201010909,2060201010916,2060201010923,2060201010930,2060201010947,2060201010954,2060201010985,2060201011005,2060201011012,2060201011036,2060201011043,2060201011050,2060201011074,2060201011098,2060201011104,2060201011111,2060201011135,2060201011142,2060201011159,2060201011173,2060201011180,2060201011197,2060201011203,2060201011210,2060201011227,2060201011234,2060201011241,2060201011258,2060201011265,2060201011289,2060201011296,2060201011326,2060201011333,2060201011340,2060201011357,2060201011364,2060201011395,2060201011401,2060201011418,2060201011425,2060201011432,2060201011449,2060201011456,2060201011524", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "f77e0ec1-fb6f-11e3-93f1-e4115baba630", "label": "\u041f\u0430\u0440\u0444\u044e\u043c\u0435\u0440\u0438\u044f \u0438 \u043a\u043e\u0441\u043c\u0435\u0442\u0438\u043a\u0430", "url": "\/catalog\/parfyumeriya-i-kosmetika", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-parfum.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_blue_b2.png", "value": "10", "is_currency": false},
                            {"uid": "3efe1394-fb6d-11e3-93f1-e4115baba630", "label": "\u0421\u0430\u0434 \u0438 \u043e\u0433\u043e\u0440\u043e\u0434", "url": "\/catalog\/do_it_yourself\/tovari-dlya-sada-311", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/06\/sad_ogorod.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "7", "is_currency": false},
                            {"uid": "59a2456f-fb6f-11e3-93f1-e4115baba630", "label": "\u0423\u043a\u0440\u0430\u0448\u0435\u043d\u0438\u044f \u0438 \u0447\u0430\u0441\u044b", "url": "\/catalog\/jewel", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-jewel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "d1247d34-fb71-11e3-93f1-e4115baba630", "label": "\u0414\u0435\u0442\u0441\u043a\u0438\u0435 \u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/children", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/kids.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "5", "is_currency": false},
                            {"uid": "bea585d4-fb6d-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b \u0434\u043b\u044f \u0434\u043e\u043c\u0430", "url": "\/catalog\/household", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2013\/12\/fishka_2013-11-20.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "610e30fa-fb6c-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "7", "is_currency": false},
                            {"uid": "bff70e2c-e707-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b Tchibo \u0431\u0435\u0437 SALE", "url": "\/catalog\/tchibo", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/enterprize-icon-tchibo.jpg", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "45c26a15-fb9d-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e \u0438 \u0432\u0438\u0434\u0435\u043e", "url": "\/products\/set\/2060701007249,2060701007232,2060701007263,2060701003548,2060701007256,2060701006327,2060701004958,2060701004941,2060701007478,2060701007461,2060701007294,2060701005269,2060701007485,2060701007508,2060701007492,2060701007539,2060701007522,2060701007515,2060701007560,2060701007546,2060701007577,2060701007553,2060701007584,2060701007645,2060701007638,2060701001513,2060701003715,2060701003838,2060701004156,2060701006198,2060701004101,2060701003005,2060701002992,2060701001605,2060701001612,2060701005641,2060701006617,2060701007416,2060701007423,2060701007287,2060701007379,2060701006228,2060701005627,2060701006792,2060701007409,2060701007614,2060701007027,2060701004378,2060701006846,2060701006839,2060701007447,2060703000750,2060703001160,2060703001153,2060703001177,2060703001092,2060703000958,2060703000729,2060703001054,2060703001061,2060703000880,2060703000910,2060702000904,2060702000959,2060702000966,2060702001314,2060702001529,2060702000416,2060504000027,2060702001291,2060702000782,2060702000836,2060702001420,2060702001437,2060702001444,2060702000638,2060702000645,2060702000652,2060702001222,2060702001239,2060702000546,2060702001109,2060702001116,2060702001161,2060702001178,2060702001185,2060702000454,2060702000461,2060702000478,2060702001253,2060702001246,2060702001260,2060702001215,2060702001567,2060702001574,2060702000843,2060702000850,2060702000867,2060702000874,2060702000881,2060702000898,2060702001055,2060702001086,2060702001079,2060702001062,2060702000072,2060702000263,2060702000317,2060702000201,2060702000218,2060702000713,2060702000171,2060702000164,2060702000157,2060702000225,2060702000232,2060702000195,2060702000102,2060702000119,2060702001413,2060702001321,2060702001338,2060702001345,2060702001352,2060702001369,2060702001376,2060702001383,2060702001390,2060702001406,2060702001499,2060701007737,2060701007720,2060701007744,2060701007768,2060701007751,2060703001276,2060703001290", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "b8e11278-e70b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007527,2060201010312,2060201008234,2060201008265,2060201009743,2060201008982,2060201007534,2060201009514,2060201007794,2060201008296,2060201008319,2060201008272,2060201008968,2060201007688,2060201007329,2060201009538,2060201007633,2060201009835,2060201007923,2060201008951,2060201007657,2060201007664,2060201007466,2060201007978,2060201009859,2060201010299,2060201010305,2060201007756,2060201007947,2060201007985,2060201007473,2060201010121,2060201007886,2060201009613,2060201007954,2060201008609,2060201007961,2060201009040,2060201009606,2060201007909,2060201007763,2060201007770,2060201008258,2060201009736,2060201007848,2060201007541,2060201008036,2060201009972,2060201008081,2060201008999,2060201009576,2060201010152,2060201010169,2060201010176,2060201010213,2060201007695", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "24f86281-e72c-11e3-93f1-e4115baba630", "label": "\u0412\u0435\u043b\u043e\u0441\u0438\u043f\u0435\u0434\u044b", "url": "\/catalog\/sport\/velosipedi-3379", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "500", "is_currency": true}
                        ],
                        [
                            {"uid": "320df931-fb71-11e3-93f1-e4115baba630", "label": "\u0411\u044b\u0442\u043e\u0432\u0430\u044f \u0442\u0435\u0445\u043d\u0438\u043a\u0430", "url": "\/catalog\/appliances", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/bit.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "125", "is_currency": true},
                            {"uid": "48e75ecc-fb70-11e3-93f1-e4115baba630", "label": "\u0417\u043e\u043e\u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/tovari-dlya-givotnih", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/pets.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "5", "is_currency": false},
                            {"uid": "2fdb06db-f76f-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e\u043a\u0430\u043c\u0435\u0440\u044b SONY", "url": "\/products\/set\/2060701004262,2060701007775,2060701006570,2060701006372,2060701007782", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "7c9a0bc5-fb71-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "e73952ca-fb6e-11e3-93f1-e4115baba630", "label": "\u0421\u043f\u043e\u0440\u0442 \u0438 \u041e\u0442\u0434\u044b\u0445", "url": "\/catalog\/sport", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "10", "is_currency": false},
                            {"uid": "e68e98b2-fb9b-11e3-93f1-e4115baba630", "label": "\u0421\u043c\u0430\u0440\u0442\u0444\u043e\u043d\u044b", "url": "\/products\/set\/2060302008454,2060302008461,2060302008478,2060302008232,2060302008485,2060302008492,2060302008201,2060302008508,2060302008515,2060302008522,2060302008744,2060302008225,2060302007938,2060302007983,2060302006832,2060302007990,2060302007945,2060302007976,2060302008003,2060302007969,2060302007952,2060301001777,2060301003931,2060301004907,2060301004914,2060302004852,2060302006849,2060304000142,2060304000159,2060304000166,2060304000173,2060302008331,2060302007631,2060302007624,2060302006559,2060301004853,2060301004846,2060302006573,2060302006566,2060302006504,2060301004563,2060302008874,2060302008850,2060302008911,2060302008928,2060302008126,2060302008140,2060302008164,2060302008157,2060302008171,2060302006887", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "6a5e1d79-fb9b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007800,2060201009576,2060201010213,2060201010350,2060201010374,2060201010602,2060201010619,2060201010626,2060201010633,2060201010640,2060201010701,2060201010749,2060201010794,2060201010800,2060201010817,2060201010848,2060201010855,2060201010862,2060201010879,2060201010909,2060201010916,2060201010923,2060201010930,2060201010947,2060201010954,2060201010985,2060201011005,2060201011012,2060201011036,2060201011043,2060201011050,2060201011074,2060201011098,2060201011104,2060201011111,2060201011135,2060201011142,2060201011159,2060201011173,2060201011180,2060201011197,2060201011203,2060201011210,2060201011227,2060201011234,2060201011241,2060201011258,2060201011265,2060201011289,2060201011296,2060201011326,2060201011333,2060201011340,2060201011357,2060201011364,2060201011395,2060201011401,2060201011418,2060201011425,2060201011432,2060201011449,2060201011456,2060201011524", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "f77e0ec1-fb6f-11e3-93f1-e4115baba630", "label": "\u041f\u0430\u0440\u0444\u044e\u043c\u0435\u0440\u0438\u044f \u0438 \u043a\u043e\u0441\u043c\u0435\u0442\u0438\u043a\u0430", "url": "\/catalog\/parfyumeriya-i-kosmetika", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-parfum.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_blue_b2.png", "value": "10", "is_currency": false},
                            {"uid": "3efe1394-fb6d-11e3-93f1-e4115baba630", "label": "\u0421\u0430\u0434 \u0438 \u043e\u0433\u043e\u0440\u043e\u0434", "url": "\/catalog\/do_it_yourself\/tovari-dlya-sada-311", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/06\/sad_ogorod.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "7", "is_currency": false},
                            {"uid": "59a2456f-fb6f-11e3-93f1-e4115baba630", "label": "\u0423\u043a\u0440\u0430\u0448\u0435\u043d\u0438\u044f \u0438 \u0447\u0430\u0441\u044b", "url": "\/catalog\/jewel", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-jewel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "d1247d34-fb71-11e3-93f1-e4115baba630", "label": "\u0414\u0435\u0442\u0441\u043a\u0438\u0435 \u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/children", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/kids.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "5", "is_currency": false},
                            {"uid": "bea585d4-fb6d-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b \u0434\u043b\u044f \u0434\u043e\u043c\u0430", "url": "\/catalog\/household", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2013\/12\/fishka_2013-11-20.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "610e30fa-fb6c-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "7", "is_currency": false},
                            {"uid": "bff70e2c-e707-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b Tchibo \u0431\u0435\u0437 SALE", "url": "\/catalog\/tchibo", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/enterprize-icon-tchibo.jpg", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "45c26a15-fb9d-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e \u0438 \u0432\u0438\u0434\u0435\u043e", "url": "\/products\/set\/2060701007249,2060701007232,2060701007263,2060701003548,2060701007256,2060701006327,2060701004958,2060701004941,2060701007478,2060701007461,2060701007294,2060701005269,2060701007485,2060701007508,2060701007492,2060701007539,2060701007522,2060701007515,2060701007560,2060701007546,2060701007577,2060701007553,2060701007584,2060701007645,2060701007638,2060701001513,2060701003715,2060701003838,2060701004156,2060701006198,2060701004101,2060701003005,2060701002992,2060701001605,2060701001612,2060701005641,2060701006617,2060701007416,2060701007423,2060701007287,2060701007379,2060701006228,2060701005627,2060701006792,2060701007409,2060701007614,2060701007027,2060701004378,2060701006846,2060701006839,2060701007447,2060703000750,2060703001160,2060703001153,2060703001177,2060703001092,2060703000958,2060703000729,2060703001054,2060703001061,2060703000880,2060703000910,2060702000904,2060702000959,2060702000966,2060702001314,2060702001529,2060702000416,2060504000027,2060702001291,2060702000782,2060702000836,2060702001420,2060702001437,2060702001444,2060702000638,2060702000645,2060702000652,2060702001222,2060702001239,2060702000546,2060702001109,2060702001116,2060702001161,2060702001178,2060702001185,2060702000454,2060702000461,2060702000478,2060702001253,2060702001246,2060702001260,2060702001215,2060702001567,2060702001574,2060702000843,2060702000850,2060702000867,2060702000874,2060702000881,2060702000898,2060702001055,2060702001086,2060702001079,2060702001062,2060702000072,2060702000263,2060702000317,2060702000201,2060702000218,2060702000713,2060702000171,2060702000164,2060702000157,2060702000225,2060702000232,2060702000195,2060702000102,2060702000119,2060702001413,2060702001321,2060702001338,2060702001345,2060702001352,2060702001369,2060702001376,2060702001383,2060702001390,2060702001406,2060702001499,2060701007737,2060701007720,2060701007744,2060701007768,2060701007751,2060703001276,2060703001290", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "b8e11278-e70b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007527,2060201010312,2060201008234,2060201008265,2060201009743,2060201008982,2060201007534,2060201009514,2060201007794,2060201008296,2060201008319,2060201008272,2060201008968,2060201007688,2060201007329,2060201009538,2060201007633,2060201009835,2060201007923,2060201008951,2060201007657,2060201007664,2060201007466,2060201007978,2060201009859,2060201010299,2060201010305,2060201007756,2060201007947,2060201007985,2060201007473,2060201010121,2060201007886,2060201009613,2060201007954,2060201008609,2060201007961,2060201009040,2060201009606,2060201007909,2060201007763,2060201007770,2060201008258,2060201009736,2060201007848,2060201007541,2060201008036,2060201009972,2060201008081,2060201008999,2060201009576,2060201010152,2060201010169,2060201010176,2060201010213,2060201007695", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "24f86281-e72c-11e3-93f1-e4115baba630", "label": "\u0412\u0435\u043b\u043e\u0441\u0438\u043f\u0435\u0434\u044b", "url": "\/catalog\/sport\/velosipedi-3379", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "500", "is_currency": true}
                        ],
                        [
                            {"uid": "320df931-fb71-11e3-93f1-e4115baba630", "label": "\u0411\u044b\u0442\u043e\u0432\u0430\u044f \u0442\u0435\u0445\u043d\u0438\u043a\u0430", "url": "\/catalog\/appliances", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/bit.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "125", "is_currency": true},
                            {"uid": "48e75ecc-fb70-11e3-93f1-e4115baba630", "label": "\u0417\u043e\u043e\u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/tovari-dlya-givotnih", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/pets.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "5", "is_currency": false},
                            {"uid": "2fdb06db-f76f-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e\u043a\u0430\u043c\u0435\u0440\u044b SONY", "url": "\/products\/set\/2060701004262,2060701007775,2060701006570,2060701006372,2060701007782", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "7c9a0bc5-fb71-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "e73952ca-fb6e-11e3-93f1-e4115baba630", "label": "\u0421\u043f\u043e\u0440\u0442 \u0438 \u041e\u0442\u0434\u044b\u0445", "url": "\/catalog\/sport", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "10", "is_currency": false},
                            {"uid": "e68e98b2-fb9b-11e3-93f1-e4115baba630", "label": "\u0421\u043c\u0430\u0440\u0442\u0444\u043e\u043d\u044b", "url": "\/products\/set\/2060302008454,2060302008461,2060302008478,2060302008232,2060302008485,2060302008492,2060302008201,2060302008508,2060302008515,2060302008522,2060302008744,2060302008225,2060302007938,2060302007983,2060302006832,2060302007990,2060302007945,2060302007976,2060302008003,2060302007969,2060302007952,2060301001777,2060301003931,2060301004907,2060301004914,2060302004852,2060302006849,2060304000142,2060304000159,2060304000166,2060304000173,2060302008331,2060302007631,2060302007624,2060302006559,2060301004853,2060301004846,2060302006573,2060302006566,2060302006504,2060301004563,2060302008874,2060302008850,2060302008911,2060302008928,2060302008126,2060302008140,2060302008164,2060302008157,2060302008171,2060302006887", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "500", "is_currency": true},
                            {"uid": "6a5e1d79-fb9b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007800,2060201009576,2060201010213,2060201010350,2060201010374,2060201010602,2060201010619,2060201010626,2060201010633,2060201010640,2060201010701,2060201010749,2060201010794,2060201010800,2060201010817,2060201010848,2060201010855,2060201010862,2060201010879,2060201010909,2060201010916,2060201010923,2060201010930,2060201010947,2060201010954,2060201010985,2060201011005,2060201011012,2060201011036,2060201011043,2060201011050,2060201011074,2060201011098,2060201011104,2060201011111,2060201011135,2060201011142,2060201011159,2060201011173,2060201011180,2060201011197,2060201011203,2060201011210,2060201011227,2060201011234,2060201011241,2060201011258,2060201011265,2060201011289,2060201011296,2060201011326,2060201011333,2060201011340,2060201011357,2060201011364,2060201011395,2060201011401,2060201011418,2060201011425,2060201011432,2060201011449,2060201011456,2060201011524", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "f77e0ec1-fb6f-11e3-93f1-e4115baba630", "label": "\u041f\u0430\u0440\u0444\u044e\u043c\u0435\u0440\u0438\u044f \u0438 \u043a\u043e\u0441\u043c\u0435\u0442\u0438\u043a\u0430", "url": "\/catalog\/parfyumeriya-i-kosmetika", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-parfum.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_blue_b2.png", "value": "10", "is_currency": false},
                            {"uid": "3efe1394-fb6d-11e3-93f1-e4115baba630", "label": "\u0421\u0430\u0434 \u0438 \u043e\u0433\u043e\u0440\u043e\u0434", "url": "\/catalog\/do_it_yourself\/tovari-dlya-sada-311", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/06\/sad_ogorod.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "7", "is_currency": false},
                            {"uid": "59a2456f-fb6f-11e3-93f1-e4115baba630", "label": "\u0423\u043a\u0440\u0430\u0448\u0435\u043d\u0438\u044f \u0438 \u0447\u0430\u0441\u044b", "url": "\/catalog\/jewel", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-jewel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "d1247d34-fb71-11e3-93f1-e4115baba630", "label": "\u0414\u0435\u0442\u0441\u043a\u0438\u0435 \u0442\u043e\u0432\u0430\u0440\u044b", "url": "\/catalog\/children", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/kids.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "5", "is_currency": false},
                            {"uid": "bea585d4-fb6d-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b \u0434\u043b\u044f \u0434\u043e\u043c\u0430", "url": "\/catalog\/household", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2013\/12\/fishka_2013-11-20.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "610e30fa-fb6c-11e3-93f1-e4115baba630", "label": "\u041c\u0435\u0431\u0435\u043b\u044c", "url": "\/catalog\/furniture", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-mebel.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "7", "is_currency": false},
                            {"uid": "bff70e2c-e707-11e3-93f1-e4115baba630", "label": "\u0422\u043e\u0432\u0430\u0440\u044b Tchibo \u0431\u0435\u0437 SALE", "url": "\/catalog\/tchibo", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/enterprize-icon-tchibo.jpg", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_orange_b1.png", "value": "10", "is_currency": false},
                            {"uid": "45c26a15-fb9d-11e3-93f1-e4115baba630", "label": "\u0424\u043e\u0442\u043e \u0438 \u0432\u0438\u0434\u0435\u043e", "url": "\/products\/set\/2060701007249,2060701007232,2060701007263,2060701003548,2060701007256,2060701006327,2060701004958,2060701004941,2060701007478,2060701007461,2060701007294,2060701005269,2060701007485,2060701007508,2060701007492,2060701007539,2060701007522,2060701007515,2060701007560,2060701007546,2060701007577,2060701007553,2060701007584,2060701007645,2060701007638,2060701001513,2060701003715,2060701003838,2060701004156,2060701006198,2060701004101,2060701003005,2060701002992,2060701001605,2060701001612,2060701005641,2060701006617,2060701007416,2060701007423,2060701007287,2060701007379,2060701006228,2060701005627,2060701006792,2060701007409,2060701007614,2060701007027,2060701004378,2060701006846,2060701006839,2060701007447,2060703000750,2060703001160,2060703001153,2060703001177,2060703001092,2060703000958,2060703000729,2060703001054,2060703001061,2060703000880,2060703000910,2060702000904,2060702000959,2060702000966,2060702001314,2060702001529,2060702000416,2060504000027,2060702001291,2060702000782,2060702000836,2060702001420,2060702001437,2060702001444,2060702000638,2060702000645,2060702000652,2060702001222,2060702001239,2060702000546,2060702001109,2060702001116,2060702001161,2060702001178,2060702001185,2060702000454,2060702000461,2060702000478,2060702001253,2060702001246,2060702001260,2060702001215,2060702001567,2060702001574,2060702000843,2060702000850,2060702000867,2060702000874,2060702000881,2060702000898,2060702001055,2060702001086,2060702001079,2060702001062,2060702000072,2060702000263,2060702000317,2060702000201,2060702000218,2060702000713,2060702000171,2060702000164,2060702000157,2060702000225,2060702000232,2060702000195,2060702000102,2060702000119,2060702001413,2060702001321,2060702001338,2060702001345,2060702001352,2060702001369,2060702001376,2060702001383,2060702001390,2060702001406,2060702001499,2060701007737,2060701007720,2060701007744,2060701007768,2060701007751,2060703001276,2060703001290", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "10", "is_currency": false},
                            {"uid": "b8e11278-e70b-11e3-93f1-e4115baba630", "label": "\u0422\u0435\u043b\u0435\u0432\u0438\u0437\u043e\u0440\u044b", "url": "\/products\/set\/2060201007527,2060201010312,2060201008234,2060201008265,2060201009743,2060201008982,2060201007534,2060201009514,2060201007794,2060201008296,2060201008319,2060201008272,2060201008968,2060201007688,2060201007329,2060201009538,2060201007633,2060201009835,2060201007923,2060201008951,2060201007657,2060201007664,2060201007466,2060201007978,2060201009859,2060201010299,2060201010305,2060201007756,2060201007947,2060201007985,2060201007473,2060201010121,2060201007886,2060201009613,2060201007954,2060201008609,2060201007961,2060201009040,2060201009606,2060201007909,2060201007763,2060201007770,2060201008258,2060201009736,2060201007848,2060201007541,2060201008036,2060201009972,2060201008081,2060201008999,2060201009576,2060201010152,2060201010169,2060201010176,2060201010213,2060201007695", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/electronica.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_fuksiya_b1.png", "value": "1000", "is_currency": true},
                            {"uid": "24f86281-e72c-11e3-93f1-e4115baba630", "label": "\u0412\u0435\u043b\u043e\u0441\u0438\u043f\u0435\u0434\u044b", "url": "\/catalog\/sport\/velosipedi-3379", "icon": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/ep-picta-sport.png", "background": "http:\/\/content.enter.ru\/wp-content\/uploads\/2014\/03\/fishka_lime_b1.png", "value": "500", "is_currency": true}
                        ]
                    ], "debug": {"id": {"value": "53d763cc209c0", "type": "info"}, "git": {"value": {"version": "master", "tag": "t80.0-36-gb6f4f15", "url": "https:\/\/github.com\/SvyaznoyMarket\/project13\/tree\/master"}, "type": "info"}, "env": {"value": "local", "type": "info"}, "query": {"value": [
                        {"url": "http%3A%2F%2Fcms.enter.ru%2Fv1%2F301.json", "escapedUrl": "http:\/\/cms.enter.ru\/v1\/301.json", "data": null, "timeout": 2.4, "startAt": 1406624716.1378, "count": 1, "info": {"url": "http:\/\/cms.enter.ru\/v1\/301.json", "content_type": "application\/json; charset=utf-8", "http_code": 200, "header_size": 240, "request_size": 146, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 6, "namelookup_time": 0.000466, "connect_time": 0.000673, "pretransfer_time": 0.000678, "size_upload": 0, "size_download": 26334, "speed_download": 4769788, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.004404, "redirect_time": 0, "redirect_url": "", "primary_ip": "10.22.244.16", "certinfo": [], "primary_port": 80, "local_ip": "10.22.244.232", "local_port": 38148}, "endAt": 1406624716.1472, "spend": null, "retryCount": 2, "retryTimeout": 0.04, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Date": "Tue, 29 Jul 2014 09:05:16 GMT", "Content-Type": "application\/json; charset=utf-8", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Vary": "Accept-Encoding", "Content-Encoding": "gzip", "1": "", "2": ""}},
                        {"url": "http%3A%2F%2Fvadim.api.ent3.ru%2Fv2%2Fuser%2Fget%3Ftoken%3D39E89591-DC9B-4DA7-BE25-CED3BC1604A7%26client_id%3Dsite", "escapedUrl": "http:\/\/vadim.api.ent3.ru\/v2\/user\/get?token=39E89591-DC9B-4DA7-BE25-CED3BC1604A7\u0026amp;client_id=site", "data": null, "timeout": 90, "startAt": 1406624716.1482, "count": 1, "info": {"url": "http:\/\/vadim.api.ent3.ru\/v2\/user\/get?token=39E89591-DC9B-4DA7-BE25-CED3BC1604A7\u0026client_id=site", "content_type": "application\/json", "http_code": 200, "header_size": 202, "request_size": 209, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 82, "namelookup_time": 0.000327, "connect_time": 0.000754, "pretransfer_time": 0.000759, "size_upload": 0, "size_download": 511, "speed_download": 6195, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.082413, "redirect_time": 0, "redirect_url": "", "primary_ip": "193.232.113.142", "certinfo": [], "primary_port": 80, "local_ip": "10.22.244.232", "local_port": 49716}, "endAt": 1406624716.2315, "spend": null, "retryCount": 2, "retryTimeout": 1, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Date": "Tue, 29 Jul 2014 09:05:16 GMT", "Content-Type": "application\/json", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Vary": "Accept-Encoding", "Content-Encoding": "gzip", "1": "", "2": ""}},
                        {"url": "http%3A%2F%2Fcrm.vadim.ent3.ru%2Fgame%2Fbandit%2Finit%3Fuid%3Dd618c106-ec8e-11e3-93f1-e4115baba630%26client_id%3Dsite", "escapedUrl": "http:\/\/crm.vadim.ent3.ru\/game\/bandit\/init?uid=d618c106-ec8e-11e3-93f1-e4115baba630\u0026amp;client_id=site", "data": null, "timeout": 1, "startAt": 1406624716.2317, "count": 1, "info": {"url": "http:\/\/crm.vadim.ent3.ru\/game\/bandit\/init?uid=d618c106-ec8e-11e3-93f1-e4115baba630\u0026client_id=site", "content_type": "application\/json", "http_code": 200, "header_size": 289, "request_size": 212, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 504, "namelookup_time": 8.1e-5, "connect_time": 0.000157, "pretransfer_time": 0.000158, "size_upload": 0, "size_download": 356, "speed_download": 706, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.503839, "redirect_time": 0, "redirect_url": "", "primary_ip": "127.0.0.1", "certinfo": [], "primary_port": 80, "local_ip": "127.0.0.1", "local_port": 51068}, "endAt": 1406624716.736, "spend": 597, "retryCount": null, "retryTimeout": null, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Content-Type": "application\/json", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Keep-Alive": "timeout=5", "Cache-Control": "no-cache", "Date": "Tue, 29 Jul 2014 09:05:16 GMT", "X-Debug-Token": "4219c5", "X-Debug-Token-Link": "\/_profiler\/4219c5", "Content-Encoding": "gzip", "1": "", "2": ""}},
                        {"url": "http%3A%2F%2Fscms.vadim.ent3.ru%2Fv2%2Fcoupon%2Fget%3F", "escapedUrl": "http:\/\/scms.vadim.ent3.ru\/v2\/coupon\/get?", "data": null, "timeout": 0.36, "startAt": 1406624716.7362, "count": 1, "info": {"url": "http:\/\/scms.vadim.ent3.ru\/v2\/coupon\/get?", "content_type": "application\/json", "http_code": 200, "header_size": 316, "request_size": 155, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 267, "namelookup_time": 8.4e-5, "connect_time": 0.000181, "pretransfer_time": 0.000182, "size_upload": 0, "size_download": 2747, "speed_download": 10282, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.266982, "redirect_time": 0, "redirect_url": "", "primary_ip": "127.0.0.1", "certinfo": [], "primary_port": 80, "local_ip": "127.0.0.1", "local_port": 51077}, "endAt": 1406624717.0044, "spend": 865, "retryCount": null, "retryTimeout": null, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Content-Type": "application\/json", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Keep-Alive": "timeout=5", "Cache-Control": "no-cache", "Date": "Tue, 29 Jul 2014 09:05:16 GMT", "X-Debug-Token": "950d76", "X-Debug-Token-Link": "\/_profiler\/950d76", "X-Server-Name": "mow-03-t32", "Content-Encoding": "gzip", "1": "", "2": ""}},
                        {"url": "http%3A%2F%2Fcms.enter.ru%2Fv1%2Fpartner%2Fpaid-source.json", "escapedUrl": "http:\/\/cms.enter.ru\/v1\/partner\/paid-source.json", "data": null, "timeout": 2.4, "startAt": 1406624717.0063, "count": 1, "info": {"url": "http:\/\/cms.enter.ru\/v1\/partner\/paid-source.json", "content_type": "application\/json; charset=utf-8", "http_code": 200, "header_size": 240, "request_size": 162, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 1, "namelookup_time": 0.000498, "connect_time": 0.000721, "pretransfer_time": 0.000726, "size_upload": 0, "size_download": 274, "speed_download": 237435, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.001115, "redirect_time": 0, "redirect_url": "", "primary_ip": "10.22.244.11", "certinfo": [], "primary_port": 80, "local_ip": "10.22.244.232", "local_port": 44177}, "endAt": 1406624717.0078, "spend": null, "retryCount": 2, "retryTimeout": 0.04, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Date": "Tue, 29 Jul 2014 09:05:17 GMT", "Content-Type": "application\/json; charset=utf-8", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Vary": "Accept-Encoding", "Content-Encoding": "gzip", "1": "", "2": ""}},
                        {"url": "http%3A%2F%2Fcms.enter.ru%2Fv1%2Fpartner%2Ffree-host.json", "escapedUrl": "http:\/\/cms.enter.ru\/v1\/partner\/free-host.json", "data": null, "timeout": 2.4, "startAt": 1406624717.0081, "count": 1, "info": {"url": "http:\/\/cms.enter.ru\/v1\/partner\/free-host.json", "content_type": "application\/json; charset=utf-8", "http_code": 200, "header_size": 240, "request_size": 160, "filetime": -1, "ssl_verify_result": 0, "redirect_count": 0, "total_time": 4, "namelookup_time": 0.000371, "connect_time": 0.000593, "pretransfer_time": 0.000598, "size_upload": 0, "size_download": 203, "speed_download": 55494, "speed_upload": 0, "download_content_length": -1, "upload_content_length": 0, "starttransfer_time": 0.003614, "redirect_time": 0, "redirect_url": "", "primary_ip": "10.22.244.15", "certinfo": [], "primary_port": 80, "local_ip": "10.22.244.232", "local_port": 54287}, "endAt": 1406624717.0121, "spend": null, "retryCount": 2, "retryTimeout": 0.04, "header": {"0": "HTTP\/1.1 200 OK", "Server": "nginx", "Date": "Tue, 29 Jul 2014 09:05:17 GMT", "Content-Type": "application\/json; charset=utf-8", "Transfer-Encoding": "chunked", "Connection": "keep-alive", "Vary": "Accept-Encoding", "Content-Encoding": "gzip", "1": "", "2": ""}}
                    ], "type": "info"}, "timer": {"value": [
                        {"name": "core", "value": 588, "count": 3, "unit": "ms"},
                        {"name": "data-store", "value": 15, "count": 4, "unit": "ms"},
                        {"name": "content", "value": 0, "count": 0, "unit": "ms"},
                        {"name": "total", "value": 873, "count": 1, "unit": "ms"}
                    ], "type": "info"}, "user": {"value": "39E89591-DC9B-4DA7-BE25-CED3BC1604A7", "type": "info"}, "route": {"value": "game.slots.init", "type": "info"}, "act": {"value": "Game\\BanditAction.init", "type": "info"}, "session": {"value": {"userCart": {"productList": [], "serviceList": [], "warrantyList": [], "certificateList": [], "couponList": [], "blackcardList": [], "actionData": [], "paypalProduct": []}, "authSource": "email", "_token": "39E89591-DC9B-4DA7-BE25-CED3BC1604A7"}, "type": "info"}, "memory": {"value": {"value": 1.44, "unit": "Mb"}, "type": "info"}, "config": {"value": [
                        {"name": "debug", "value": "true"},
                        {"name": "logger", "value": "{\u0022pretty\u0022:false}"},
                        {"name": "appDir", "value": "\u0022\/opt\/wwwroot\/enter.ru\/wwwroot\u0022"},
                        {"name": "cmsDir", "value": "\u0022\/opt\/wwwroot\/enter.ru\/wwwroot\/..\/..\/cms.enter.ru\/wwwroot\u0022"},
                        {"name": "routePrefix", "value": "\u0022\u0022"},
                        {"name": "authToken", "value": "{\u0022name\u0022:\u0022_token\u0022,\u0022authorized_cookie\u0022:\u0022_authorized\u0022}"},
                        {"name": "session", "value": "{\u0022name\u0022:\u0022enter\u0022,\u0022cookie_lifetime\u0022:15552000,\u0022cookie_domain\u0022:\u0022.vadim.ent3.ru\u0022}"},
                        {"name": "cacheCookieName", "value": "\u0022enter_auth\u0022"},
                        {"name": "redirect301", "value": "{\u0022enabled\u0022:true}"},
                        {"name": "mobileRedirect", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "coreV2", "value": "{\u0022url\u0022:\u0022http:\/\/vadim.api.ent3.ru\/v2\/\u0022,\u0022client_id\u0022:\u0022site\u0022,\u0022timeout\u0022:15,\u0022hugeTimeout\u0022:90,\u0022retryTimeout\u0022:{\u0022default\u0022:1,\u0022tiny\u0022:0.6,\u0022short\u0022:1,\u0022medium\u0022:1.4,\u0022long\u0022:2,\u0022huge\u0022:3,\u0022forever\u0022:0},\u0022retryCount\u0022:2,\u0022debug\u0022:false,\u0022chunk_size\u0022:50}"},
                        {"name": "crm", "value": "{\u0022url\u0022:\u0022http:\/\/crm.vadim.ent3.ru\/\u0022,\u0022client_id\u0022:\u0022site\u0022,\u0022timeout\u0022:1,\u0022hugeTimeout\u0022:5,\u0022retryCount\u0022:2,\u0022retryTimeout\u0022:{\u0022default\u0022:0.5,\u0022tiny\u0022:0.1,\u0022short\u0022:0.3,\u0022medium\u0022:0.5,\u0022long\u0022:0.8,\u0022huge\u0022:1.5,\u0022forever\u0022:0},\u0022debug\u0022:false}"},
                        {"name": "wordpress", "value": "{\u0022url\u0022:\u0022http:\/\/content.enter.ru\/\u0022,\u0022timeout\u0022:10.8,\u0022throwException\u0022:false,\u0022retryTimeout\u0022:{\u0022default\u0022:0.3,\u0022tiny\u0022:0.1,\u0022short\u0022:0.2,\u0022medium\u0022:0.3,\u0022long\u0022:0.5,\u0022huge\u0022:1,\u0022forever\u0022:0},\u0022retryCount\u0022:2}"},
                        {"name": "dataStore", "value": "{\u0022url\u0022:\u0022http:\/\/cms.enter.ru\/v1\/\u0022,\u0022timeout\u0022:2.4,\u0022retryTimeout\u0022:{\u0022default\u0022:0.04,\u0022tiny\u0022:0.04,\u0022short\u0022:0.08,\u0022medium\u0022:0.1,\u0022long\u0022:0.5,\u0022huge\u0022:1,\u0022forever\u0022:0},\u0022retryCount\u0022:2}"},
                        {"name": "connectTerminal", "value": "true"},
                        {"name": "reviewsStore", "value": "{\u0022url\u0022:\u0022http:\/\/reviews.enter.ru\/reviews\/\u0022,\u0022timeout\u0022:1.8,\u0022retryTimeout\u0022:{\u0022default\u0022:1,\u0022tiny\u0022:0.1,\u0022short\u0022:0.4,\u0022medium\u0022:1,\u0022long\u0022:1.6,\u0022huge\u0022:3,\u0022forever\u0022:0},\u0022retryCount\u0022:3}"},
                        {"name": "company", "value": "{\u0022phone\u0022:\u00228 (800) 700-00-09\u0022,\u0022moscowPhone\u0022:\u00228 (495) 775-00-06\u0022,\u0022icq\u0022:\u0022648198963\u0022}"},
                        {"name": "analytics", "value": "{\u0022enabled\u0022:false,\u0022optimizelyEnabled\u0022:false}"},
                        {"name": "kissmentrics", "value": "{\u0022enabled\u0022:true,\u0022cookieName\u0022:{\u0022needUpdate\u0022:\u0022kissNeedUpdate\u0022}}"},
                        {"name": "pickpoint", "value": "{\u0022url\u0022:\u0022http:\/\/e-solution.pickpoint.ru\/apitest\/\u0022,\u0022timeout\u0022:60,\u0022retryTimeout\u0022:{\u0022default\u0022:0.04,\u0022tiny\u0022:0.04,\u0022short\u0022:0.08,\u0022medium\u0022:0.1,\u0022long\u0022:0.5,\u0022huge\u0022:1,\u0022forever\u0022:0},\u0022retryCount\u0022:3}"},
                        {"name": "jsonLog", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "googleAnalytics", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "yandexMetrika", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "googleTagManager", "value": "{\u0022enabled\u0022:true,\u0022containerId\u0022:\u0022GTM-P65PBR\u0022}"},
                        {"name": "partners", "value": "{\u0022livetex\u0022:{\u0022enabled\u0022:false,\u0022liveTexID\u0022:41836,\u0022login\u0022:null,\u0022password\u0022:null},\u0022criteo\u0022:{\u0022enabled\u0022:false,\u0022account\u0022:10442},\u0022RetailRocket\u0022:{\u0022account\u0022:\u0022519c7f3c0d422d0fe0ee9775\u0022,\u0022apiUrl\u0022:\u0022http:\/\/api.retailrocket.ru\/api\/\u0022,\u0022timeout\u0022:0.5,\u0022cookieLifetime\u0022:2592000,\u0022userEmail\u0022:{\u0022cookieName\u0022:\u0022user_email\u0022}},\u0022Admitad\u0022:{\u0022enabled\u0022:false},\u0022AdLens\u0022:{\u0022enabled\u0022:false},\u0022\u0421paexchange\u0022:{\u0022enabled\u0022:false},\u0022Revolvermarketing\u0022:{\u0022enabled\u0022:false},\u0022Lamoda\u0022:{\u0022enabled\u0022:false,\u0022lamodaID\u0022:\u002211640775691088171491\u0022},\u0022TagMan\u0022:{\u0022enabled\u0022:false},\u0022Myragon\u0022:{\u0022enabled\u0022:false,\u0022enterNumber\u0022:1402,\u0022secretWord\u0022:\u0022RdjJBC9FLE\u0022,\u0022subdomainNumber\u0022:49}}"},
                        {"name": "adFox", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "partner", "value": "{\u0022cookieName\u0022:\u0022last_partner\u0022,\u0022cookieLifetime\u0022:2592000}"},
                        {"name": "mainHost", "value": "\u0022vadim.ent3.ru\u0022"},
                        {"name": "mobileHost", "value": "\u0022m.enter.ru\u0022"},
                        {"name": "onlineCall", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "region", "value": "{\u0022cookieName\u0022:\u0022geoshop\u0022,\u0022cookieLifetime\u0022:31536000,\u0022defaultId\u0022:14974,\u0022autoresolve\u0022:true}"},
                        {"name": "shop", "value": "{\u0022cookieName\u0022:\u0022shopid\u0022,\u0022cookieLifetime\u0022:31536000,\u0022autoresolve\u0022:true,\u0022enabled\u0022:true}"},
                        {"name": "loadMediaHost", "value": "false"},
                        {"name": "mediaHost", "value": "[\u0022http:\/\/fs01.enter.ru\u0022,\u0022http:\/\/fs02.enter.ru\u0022,\u0022http:\/\/fs03.enter.ru\u0022,\u0022http:\/\/fs04.enter.ru\u0022,\u0022http:\/\/fs05.enter.ru\u0022,\u0022http:\/\/fs06.enter.ru\u0022,\u0022http:\/\/fs07.enter.ru\u0022,\u0022http:\/\/fs08.enter.ru\u0022,\u0022http:\/\/fs09.enter.ru\u0022,\u0022http:\/\/fs10.enter.ru\u0022]"},
                        {"name": "search", "value": "{\u0022itemLimit\u0022:1000,\u0022queryStringLimit\u0022:1,\u0022categoriesLimit\u0022:200}"},
                        {"name": "product", "value": "{\u0022itemsPerPage\u0022:20,\u0022showAccessories\u0022:true,\u0022showRelated\u0022:true,\u0022itemsInSlider\u0022:5,\u0022itemsInCategorySlider\u0022:3,\u0022minCreditPrice\u0022:3000,\u0022totalCount\u0022:30000,\u0022showAveragePrice\u0022:false,\u0022allowBuyOnlyInshop\u0022:true,\u0022reviewEnabled\u0022:true,\u0022pushReview\u0022:true,\u0022lowerPriceNotification\u0022:true,\u0022furnitureConstructor\u0022:true,\u0022recommendationPull\u0022:null,\u0022recommendationPush\u0022:null,\u0022itemsInAccessorySlider\u0022:4,\u0022recommendationSessionKey\u0022:\u0022recommendationProductIds\u0022,\u0022itemsPerPageJewel\u0022:24,\u0022itemsPerRowJewel\u0022:4,\u0022pullRecommendation\u0022:true,\u0022pushRecommendation\u0022:false}"},
                        {"name": "productPhoto", "value": "{\u0022url\u0022:[\u0022\/1\/1\/60\/\u0022,\u0022\/1\/1\/120\/\u0022,\u0022\/1\/1\/163\/\u0022,\u0022\/1\/1\/500\/\u0022,\u0022\/1\/1\/2500\/\u0022,\u0022\/1\/1\/1500\/\u0022]}"},
                        {"name": "productPhoto3d", "value": "{\u0022url\u0022:[\u0022\/1\/2\/500\/\u0022,\u0022\/1\/2\/2500\/\u0022]}"},
                        {"name": "productLabel", "value": "{\u0022url\u0022:[\u0022\/7\/1\/66x23\/\u0022,\u0022\/7\/1\/124x38\/\u0022]}"},
                        {"name": "productCategory", "value": "{\u0022url\u0022:{\u00220\u0022:\u0022\/6\/1\/163\/\u0022,\u00223\u0022:\u0022\/6\/1\/500\/\u0022,\u00225\u0022:\u0022\/6\/1\/960\/\u0022}}"},
                        {"name": "service", "value": "{\u0022url\u0022:[\u0022\/11\/1\/160\/\u0022,\u0022\/11\/1\/500\/\u0022,\u0022\/11\/1\/120\/\u0022],\u0022minPriceForDelivery\u0022:950}"},
                        {"name": "serviceCategory", "value": "{\u0022url\u0022:[\u0022\/10\/1\/160\/\u0022,\u0022\/10\/1\/500\/\u0022]}"},
                        {"name": "shopPhoto", "value": "{\u0022url\u0022:[\u0022\/8\/1\/40\/\u0022,\u0022\/8\/1\/120\/\u0022,\u0022\/8\/1\/163\/\u0022,\u0022\/8\/1\/500\/\u0022,\u0022\/8\/1\/2500\/\u0022,\u0022\/8\/1\/original\/\u0022]}"},
                        {"name": "banner", "value": "{\u0022timeout\u0022:6000,\u0022url\u0022:[\u0022\/4\/1\/230x302\/\u0022,\u0022\/4\/1\/768x302\/\u0022,\u0022\/4\/1\/920x320\/\u0022]}"},
                        {"name": "payment", "value": "{\u0022creditEnabled\u0022:true,\u0022paypalECS\u0022:false,\u0022blockedIds\u0022:[]}"},
                        {"name": "warranty", "value": "{\u0022enabled\u0022:true}"},
                        {"name": "f1Certificate", "value": "{\u0022enabled\u0022:true}"},
                        {"name": "coupon", "value": "{\u0022enabled\u0022:true}"},
                        {"name": "blackcard", "value": "{\u0022enabled\u0022:false}"},
                        {"name": "cart", "value": "{\u0022productLimit\u0022:30,\u0022sessionName\u0022:\u0022userCart\u0022}"},
                        {"name": "user", "value": "{\u0022corporateRegister\u0022:true}"},
                        {"name": "subscribe", "value": "{\u0022enabled\u0022:true,\u0022cookieName\u0022:\u0022subscribed\u0022}"},
                        {"name": "requestMainMenu", "value": "false"},
                        {"name": "order", "value": "{\u0022cookieName\u0022:\u0022last_order\u0022,\u0022sessionName\u0022:\u0022lastOrder\u0022,\u0022enableMetaTag\u0022:true,\u0022maxSumOnline\u0022:15000,\u0022maxSumOnlinePaypal\u0022:5000,\u0022excludedError\u0022:[705,708,735,759,800],\u0022addressAutocomplete\u0022:true,\u0022prepayment\u0022:{\u0022enabled\u0022:true,\u0022priceLimit\u0022:100000,\u0022labelId\u0022:15}}"},
                        {"name": "newDeliveryCalc", "value": "true"},
                        {"name": "img3d", "value": "{\u0022cmsFolder\u0022:\u0022\/opt\/wwwroot\/cms.enter.ru\/wwwroot\/v1\/video\/product\/\u0022}"},
                        {"name": "tag", "value": "{\u0022numSidebarCategoriesShown\u0022:3}"},
                        {"name": "sphinx", "value": "{\u0022showFacets\u0022:false,\u0022showListingSearchBar\u0022:false}"},
                        {"name": "lifeGift", "value": "{\u0022enabled\u0022:true,\u0022regionId\u0022:151021,\u0022labelId\u0022:17,\u0022deliveryTypeId\u0022:1077}"},
                        {"name": "enterprize", "value": "{\u0022enabled\u0022:true,\u0022formDataSessionKey\u0022:\u0022enterprizeForm\u0022,\u0022itemsInSlider\u0022:7,\u0022showSlider\u0022:true,\u0022cookieName\u0022:\u0022enterprize_coupon_sent\u0022}"},
                        {"name": "kladr", "value": "{\u0022token\u0022:\u002252b04de731608f2773000000\u0022,\u0022key\u0022:\u0022c20b52a7dc6f6b28023e3d8ef81b9dbdb51ff74b\u0022,\u0022itemLimit\u0022:1000}"},
                        {"name": "tchibo", "value": "{\u0022rowWidth\u0022:78,\u0022rowHeight\u0022:78,\u0022rowPadding\u0022:0,\u0022analyticsEnabled\u0022:null}"},
                        {"name": "tchiboSlider", "value": "{\u0022analyticsEnabled\u0022:true}"},
                        {"name": "preview", "value": "false"},
                        {"name": "svyaznoyClub", "value": "{\u0022cookieLifetime\u0022:2592000,\u0022userTicket\u0022:{\u0022cookieName\u0022:\u0022UserTicket\u0022},\u0022cardNumber\u0022:{\u0022cookieName\u0022:\u0022scid\u0022}}"},
                        {"name": "photoContest", "value": "{\u0022client\u0022:{\u0022url\u0022:\u0022http:\/\/photo.vadim.ent3.ru\/\u0022,\u0022client_id\u0022:\u0022photocontest\u0022,\u0022timeout\u0022:2,\u0022retryTimeout\u0022:1,\u0022retryCount\u0022:2,\u0022debug\u0022:false}}"},
                        {"name": "flocktoryExchange", "value": "{\u0022enabled\u0022:true}"},
                        {"name": "flocktoryCoupon", "value": "{\u0022enabled\u0022:true,\u0022paramName\u0022:\u0022utm_coupon\u0022}"},
                        {"name": "scmsV2", "value": "{\u0022url\u0022:\u0022http:\/\/scms.vadim.ent3.ru\/v2\/\u0022,\u0022timeout\u0022:0.36,\u0022retryTimeout\u0022:{\u0022default\u0022:0.18,\u0022tiny\u0022:0.18,\u0022short\u0022:0.25,\u0022medium\u0022:0.5,\u0022long\u0022:1,\u0022huge\u0022:2,\u0022forever\u0022:0},\u0022retryCount\u0022:2}"}
                    ], "type": "info"}, "abTest": {"value": {"endAt": "23-09-2013 00:00", "retailrocket": {"name": "\u0421 \u044d\u0442\u0438\u043c \u0442\u043e\u0432\u0430\u0440\u043e\u043c \u0442\u0430\u043a\u0436\u0435 \u0441\u043c\u043e\u0442\u0440\u044f\u0442 \u043e\u0442 RetailRocket", "traffic": 50, "enabled": false}, "default": {"name": "\u043f\u0443\u0441\u0442\u043e", "traffic": "*", "enabled": true}}, "type": "info"}, "abTestJson": {"value": [], "type": "info"}, "server": {"value": {"USER": "web", "HOME": "\/home\/web", "FCGI_ROLE": "RESPONDER", "QUERY_STRING": "t=1406624716213\u0026t=1406624716257", "REQUEST_METHOD": "GET", "CONTENT_TYPE": "", "CONTENT_LENGTH": "", "SCRIPT_FILENAME": "\/opt\/wwwroot\/enter.ru\/wwwroot\/web\/index.php", "SCRIPT_NAME": "\/index.php", "REQUEST_URI": "\/game\/slots\/init?t=1406624716213\u0026t=1406624716257", "DOCUMENT_URI": "\/index.php", "DOCUMENT_ROOT": "\/opt\/wwwroot\/enter.ru\/wwwroot\/web", "SERVER_PROTOCOL": "HTTP\/1.0", "GATEWAY_INTERFACE": "CGI\/1.1", "SERVER_SOFTWARE": "nginx\/1.1.19", "REMOTE_ADDR": "127.0.0.1", "REMOTE_PORT": "49489", "SERVER_ADDR": "127.0.0.1", "SERVER_PORT": "8080", "SERVER_NAME": "www.enter.ru", "HTTPS": "", "REDIRECT_STATUS": "200", "APPLICATION_ENV": "local", "APPLICATION_NAME": "main", "HTTP_HOST": "vadim.ent3.ru", "HTTP_X_FORWARDED_FOR": "89.209.124.148, 10.22.244.11", "HTTP_X_REAL_IP": "10.22.244.11", "HTTP_CONNECTION": "close", "HTTP_CLIENT_IP": "89.209.124.148", "HTTP_X_FORWARDED_PORT": "80", "HTTP_CACHE_CONTROL": "no-cache", "HTTP_AUTHORIZATION": "Basic RGV2ZWxvcGVyOmRFbDIzc1RPYXM=", "HTTP_PRAGMA": "no-cache", "HTTP_ACCEPT": "*\/*", "HTTP_USER_AGENT": "Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/36.0.1985.125 Safari\/537.36", "HTTP_REFERER": "http:\/\/127.0.0.1:7777\/", "HTTP_ACCEPT_ENCODING": "gzip,deflate,sdch", "HTTP_ACCEPT_LANGUAGE": "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4", "HTTP_COOKIE": "enter=dasatgthmua47natvqqeghe0a4; infScroll=1; optimizelyEndUserId=oeu1406107273399r0.996852494077757; _msuuid_7saq97byg0=22D7CF7F-4F19-4655-A15D-D6307BB92100; lt-pc=5; lt-on-site-time=1406107303; lt-tl=jqho; _authorized=1; kissNeedUpdate=1; geoshop=14974; __sonar=6296376004071086376; last_partner=127.0.0.1; rrpusid=53d754451e99441fb089ce14; enter_auth=b56e7f47e41ac988d96d9e7029347530; _token=39E89591-DC9B-4DA7-BE25-CED3BC1604A7; _authorized=1; kissNeedUpdate=1; optimizelySegments=%7B%7D; optimizelyBuckets=%7B%7D; visitorSplitGroup=2; rcuid=53cf7ea76636b134d8cf7b19; rrlpuid=887457", "PHP_SELF": "\/index.php", "PHP_AUTH_USER": "Developer", "PHP_AUTH_PW": "dEl23sTOas", "REQUEST_TIME_FLOAT": 1406624716.1326, "REQUEST_TIME": 1406624716}, "type": "info"}}};
                    if (response.success) {
                        self.game.init(response.reels);//иначе отрисовываем рельсы
                        self.game.bindAll();//биндимся на клики старт и стоп
                        self.demoMode = !response.user;//залогинен ли юзер

                    } else {
                        self.notAvailableState(response.error);
                    }
                }
            });
        },
        send: function (data) { //общий для всего метод отправки запроса аяксом
            var self = this;
            data.data.t = new Date().getTime();
            $.ajax({
                type: data.type,
                url: data.url,
                data: data.data,
                success: function (r) {
                    if (!r.success || r.error) {
                        self.slot_config.handlers[r.error.code] && self.slot_config.handlers[r.error.code](self, r.error);
                        return;
                    }
                    data.cb && data.cb(r);
                },
                error: function (r) {
                    //   console.log('oooopsss', r);

                    data.err && data.err();

                }
            });
        },

        notAvailableState: function (message) {//метод для вызова режима "временно недоступен"
            var self = this;
            self.ledAnimations.animationHandler.stopAnimation();//останавливаем анимацию лед панели
            clearTimeout(self.spinningTimeout);
            $el.slotMachine.demoModeSpped = false;
            self.$notAvailableMessageText.text(message ? message : self.slot_config.labels.notAvailable.message);//в  пишем сообщение что автомат недоступен
            self.$notAvailablePrizeText.text(self.slot_config.labels.notAvailable.optionPrize);//пишем тексты в ссылки
            self.$notAvailableRemindText.text(self.slot_config.labels.notAvailable.optionRemind);
            self.$slotMachine.addClass('notavailable');//вешаем класс недоступен на автомат
            self.messageBox.stopAnimation();//останавлеваем анимацию текстовой панели
            self.stopReels();
        },
        stillInGameState: function () {//метод для вызова режима "временно недоступен"
            var self = this;
            clearTimeout(self.spinningTimeout);
            $el.slotMachine.demoModeSpped = false;
            self.game.inGame = false;
            self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
            self.setLedPanelOptions('default');
            self.reels.removeClass('stop_spinning');
            if (!self.isie) {
                self.reels.addClass('spinning');
                self.game.setReelAnimation(self.reels);
            }
            self.messageBox.stopAnimation();
            self.messageBox.setRandomText("demo");
            self.messageBox.animateText("defaultAnimation");
            self.ledAnimations.animationHandler.startAnimation();
            self.$slotMachine.removeClass('notavailable');//вешаем класс недоступен на автомат

        },
        renderMarkup: function () {
            //append popup overlay
            $el.append('<div class="lbOverlay" style="display:none;height: 100%; position: fixed; width: 100%; top: 0px; left: 0px; z-index: 1001; opacity: 0.4; background: black;"></div>');
            //append slots background
            $el.append('<div id="slotsBack"></div>');
            //append slots body
            $el.append('<div id="slotsWrapper"> ' +
                '<div id="slotsMachine"> ' +
                '<div id="slotsGame"> ' +
                '<div id="slotsHeader"></div> ' +
                '<div id="notAvailable"> ' +
                '<div class="message"></div> ' +
                '<a href="#" class="option prize"></a> ' +
                '<a href="#" class="option remind"></a> ' +
                '<div class="plast br"></div> ' +
                '<div class="plast bl"></div> ' +
                '<div class="plast tr"></div> ' +
                '<div class="plast tl"></div> </div> ' +
                '<div id="slotsMessageBox"> ' +
                '<div id="slotsMessageReel"> ' +
                '<div id="messages" class="messageRow"></div> ' +
                '<div id="pixelOverlay"></div> ' +
                '</div> ' +
                '</div> ' +
                '<div id="reelsWrapper"> ' +
                '<div id="reel1" class="reel"> ' +
                '<div class="chips "> </div> ' +
                '<div class="slot-shadow-overlay overlay-top"></div> ' +
                '<div class="slot-shadow-overlay overlay-bottom"></div> ' +
                '</div> ' +
                '<div id="reel2" class="reel"> ' +
                '<div class="chips"> </div> ' +
                '<div class="slot-shadow-overlay overlay-top"></div> ' +
                '<div class="slot-shadow-overlay overlay-bottom"></div> ' +
                '</div> ' +
                '<div id="reel3" class="reel"> ' +
                '<div class="chips"> </div> ' +
                '<div class="slot-shadow-overlay overlay-top"></div> ' +
                '<div class="slot-shadow-overlay overlay-bottom"></div> ' +
                '</div> ' +
                '</div> ' +
                '<div id="winContainer" class=""> <div class="tile"> <div class="rulesText"> Фишка со скидкой <strong class="dicount">10 %</strong> на <strong class="category"><a target="_blank" style="text-decoration: underline;" href="/catalog/children">Детские товары</a></strong><br> Минимальная сумма заказа 499 руб<br> Действует c 28.05.2014 по 30.06.2014 </div> </div> <div class="confetil"></div> <div class="blue_shine"></div> <div class="shine"></div> <div class="chip "> <div class="border lime"> <div class="cuponImg__inner"> <div class="cuponIco"> <img src="http://content.enter.ru/wp-content/uploads/2014/03/kids.png"> </div> <div class="cuponDesc">Детские товары</div> <div class="cuponPrice">15% </div> </div> </div> </div> <div class=""></div> </div> <canvas height="388" width="588" id="canvas"></canvas> <div id="buttonsRow" class="toplay"> <div class="play btn_wrapper"> <div class="step_dots"> <div class="reel_dot first on"></div> <div class="reel_dot second on"></div> <div class="reel_dot third on"></div> </div> <div class="button">Играть</div> </div> <div class="stop btn_wrapper"> <div class="step_dots"> <div class="reel_dot first"></div> <div class="reel_dot second"></div> <div class="reel_dot third"></div> </div> <div class="button">Стоп</div> </div> </div> </div> </div> <div id="slotsBottomLine"></div> </div>');

            this.canvas = document.getElementById('canvas');
            this.context = canvas.getContext('2d');
            this.context.webkitImageSmoothingEnabled = false;
            this.context.mozImageSmoothingEnabled = false;
            this.context.imageSmoothingEnabled = false;
            this.reels = $el.find('#reelsWrapper .chips');
            this.reels2 = $el.find('#reelsWrapper .chips2');
            this.$notAvailableMessageText = $el.find('#notAvailable .message');
            this.$notAvailablePrizeText = $el.find('#notAvailable .option.prize');
            this.$notAvailableRemindText = $el.find('#notAvailable .option.remind');
            this.$slotMachine = $el.find('#slotsMachine');
            this.$winChipContainerChip = $el.find('#winContainer .chip');
            this.$winContainer = $el.find('#winContainer');
            this.$lb_overlay = $el.find('.lbOverlay');
        },
        game: {//логика слот машины1
            reelStoped: false,
            inGame: false,
            radius: 315,
            ie: {

                spinEm: function ($reel, height) {

                    var that = this;
                    this.loopCount = 0;

                    $reel
                        .css('top', -height + 118 * 2)
                        .animate({ 'top': '0px' }, 6000, 'linear', function () {
                            that.lowerSpeed($reel, height);
                        });

                },

                lowerSpeed: function ($reel, height) {


                    this.loopCount++;

                    if (this.loopCount < 100) {

                        this.spinEm($reel, height);

                    } else {

                        this.finish($reel, height);

                    }
                },

                // final rotation
                finish: function ($reel, height) {

                    var that = this;

                    console.log('finish');
                    $reel
                        .css('top', -height + 118 * 3)
                        .animate({'top': '0px'}, 1, 'linear', function () {
                            console.log('stop spinn');
                            that.stop($reel);
                        });

                },
                // final rotation
                stop: function ($reel) {

                    var that = this;
                    this.loopCount = 1000;
                    console.log('stop reel ie called single', $reel);
                    console.log('finish');
                    $reel.stop().clearQueue().css('top', -59);
                    this.loopCount = 1000;

                }

            },
            setReelAnimation: function ($reelEl) {
                var self = $el.slotMachine;
                console.log('self.demoModeSpped ', self.demoModeSpped);
                var cfgSpeed = self.demoModeSpped ? self.config.reelDemoSpinSpeed : self.config.reelSpinSpeed;
                //$el.slotMachine.demoModeSpped = true;
                var speedTime = cfgSpeed ? cfgSpeed : 6;
                console.log('cfgSpeed ', cfgSpeed);
                setTimeout(function () {
                    $reelEl.attr('style', '-webkit-animation: x-spin ' + speedTime + 's linear infinite; -moz-animation: x-spin ' + speedTime + 's linear infinite; -ms-animation: x-spin ' + speedTime + 's linear infinite; -o-animation: x-spin ' + speedTime + 's linear infinite; animation: x-spin ' + speedTime + 's linear infinite;');
                }, 50);


            },
            stopReelAnimation: function ($reelEl) {
                var self = $el.slotMachine;
                $reelEl.attr('style', '');
            },
            setupCips: function (chip) {
                var self = this;
                var reelLength = ($('.reel .chip').length / 3);
                var posterAngle = 360 / reelLength;


                if ($el.slotMachine.isie) {
                    console.log('chips height ', $('.reel:first .chips').height());
                    self.ie.spinEm(chip.parent(), 118 * $('.reel:first .chips .chip').length);
                } else {
                    self.radius = (18.529411764705884 * chip.length);
                    chip.each(function (index) {


                        $(this).attr('style', '-webkit-transform: ' + 'rotateX(' + (posterAngle * index) + 'deg) translateZ(' + self.radius + 'px); ' +
                            '-moz-transform: ' + 'rotateX(' + (posterAngle * index) + 'deg) translateZ(' + self.radius + 'px); ');

                    });
                }

            },
            init: function (reels) {//рисуем рельсы.
                var self = this;
                for (var i = 0; i < reels.length; i++) {
                    self.setupCips($('#reel' + (i + 1) + ' .chip'));
                    $el.find('#reel' + (i + 1) + ' .chips').html('');
                    for (var ch = 0; ch < reels[i].length; ch++) {
                        var chip = reels[i][ch];
                        if ($el.slotMachine.isie) {
                            $el.find('#reel' + (i + 1) + ' .chips').append('<div class="chip" style="position:relative; margin-left: 0px;"> <span class="border " style="background-image: url(' + chip.background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; "> <span class="cuponImg__inner"> <span class="cuponIco"> <img src="' + chip.icon + '"> </span> <span class="cuponDesc">' + chip.label + '</span> <span class="cuponPrice">' + chip.value + (chip.is_currency ? ' <span class="rubl">p</span>' : '%') + '</span> </span> </span> </div>');

                        } else {
                            $el.find('#reel' + (i + 1) + ' .chips').append('<div class="chip"> <span class="border " style="background-image: url(' + chip.background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; "> <span class="cuponImg__inner"> <span class="cuponIco"> <img src="' + chip.icon + '"> </span> <span class="cuponDesc">' + chip.label + '</span> <span class="cuponPrice">' + chip.value + (chip.is_currency ? ' <span class="rubl">p</span>' : '%') + '</span> </span> </span> </div>');

                        }
                    }
                }
                for (var j = 0; j < reels.length; j++) {
                    self.setupCips($('#reel' + (j + 1) + ' .chip'));
                }
                $el.slotMachine.reels.removeClass('stop_spinning');
                if (!$el.slotMachine.isie) {
                    $el.slotMachine.demoModeSpped = true;
                    $el.slotMachine.reels.addClass('spinning');
                    $el.slotMachine.game.setReelAnimation($el.slotMachine.reels);
                }

                if ($el.slotMachine.config.userAutoPlay) {

                    $el.slotMachine.spinningTimeout = setTimeout(function () {
                        self.start();
                    }, 30000);
                }

                $el.trigger('slotsInitialized');
            },
            bindAll: function () {//биндимся на клики по кнопкам
                var game = this;
                game.buttonsRow = $el.find('#buttonsRow');
                game.play_button = game.buttonsRow.find('.play .button');
                game.stop_button = game.buttonsRow.find('.stop .button');


                game.play_button.click(function () {
                    console.log('inGame', game.inGame);
                    if (game.inGame) {
                        return;
                    }
                    game.start();//иначе стартуем игру
                    game.buttonsRow.removeClass('toplay').addClass('stopplay');
                });
                game.stop_button.click(function () {//останавливаем игру
                    console.log('inGame', game.inGame);
                    if (!game.inGame) {
                        return;
                    }
                    game.stop(game);
                    //game.buttonsRow.removeClass('stopplay').addClass('toplay');
                });
            },
            playResultHandler: function (response) {
                var self = $el.slotMachine;
                var game = this;
                if (!response.success && !response.result) {
                    self.slot_config.handlers[r.error.code] ? self.slot_config.handlers[r.error.code](r.error) : self.notAvailableState(r.error);
                    return;
                }
                if (response.spin && !response.spin.isAvailable) {//если автомат уже недоступен режим недоступен
                    self.notAvailableState();
                    return;
                }
                game.result = response.result;//результат спина
                game.user = response.user;
                var winChinIndex = 1;
                if (self.isie) {
                    winChinIndex = 2;
                }
                var ch1 = $el.find(self.reels[0]).find('.chip:nth-child(' + winChinIndex + ')');
                var ch2 = $el.find(self.reels[1]).find('.chip:nth-child(' + winChinIndex + ')');
                var ch3 = $el.find(self.reels[2]).find('.chip:nth-child(' + winChinIndex + ')');
                var winCh = $el.find('#winContainer .chip');

                var reels = response.result.line;

                reels[0] && ch1.find('.border').attr('style', 'background-image: url(' + reels[0].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[1] && ch2.find('.border').attr('style', 'background-image: url(' + reels[1].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[2] && ch3.find('.border').attr('style', 'background-image: url(' + reels[2].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');
                reels[1] && winCh.find('.border').attr('style', 'background-image: url(' + reels[1].background + ');  background-position: center; background-repeat: no-repeat; background-size: 100%; ');

                reels[0] && ch1.find('.cuponPrice').html(reels[0].value + (reels[0].is_currency ? ' <span class="rubl">p</span>' : '%'));
                reels[1] && ch2.find('.cuponPrice').html(reels[1].value + (reels[1].is_currency ? ' <span class="rubl">p</span>' : '%'));
                reels[2] && ch3.find('.cuponPrice').html(reels[2].value + (reels[2].is_currency ? ' <span class="rubl">p</span>' : '%'));
                reels[1] && winCh.find('.cuponPrice').html(reels[1].value + (reels[1].is_currency ? ' <span class="rubl">p</span>' : '%'));

                reels[0] && ch1.find('.cuponIco img').attr('src', reels[0].icon);
                reels[1] && ch2.find('.cuponIco img').attr('src', reels[1].icon);
                reels[2] && ch3.find('.cuponIco img').attr('src', reels[2].icon);
                reels[1] && winCh.find('.cuponIco img').attr('src', reels[1].icon);


                reels[0] && ch1.find('.cuponDesc').text(reels[0].label);
                reels[1] && ch2.find('.cuponDesc').text(reels[1].label);
                reels[2] && ch3.find('.cuponDesc').text(reels[2].label);
                reels[1] && winCh.find('.cuponDesc').text(reels[1].label);

            },
            start: function () {
                var self = $el.slotMachine;
                var game = this;
                game.inGame = true;
                $el.slotMachine.demoModeSpped = false;
                game.buttonsRow.find('.stop .reel_dot').removeClass('on');
                self.setLedPanelOptions('spining');// ставим параметры лед панели
                self.ledAnimations.animationHandler.startAnimation();//стартуем акнимацию лед панели
                $el.find('.winChip').removeClass('winChip');//чистим классы фишек
                self.reels.removeClass('stop_spinning');
                if (!self.isie) {
                    self.reels.addClass('spinning');//стартуем анимацию кручения
                    self.game.stopReelAnimation(self.reels);
                    self.game.setReelAnimation(self.reels);
                } else {
                    self.game.ie.spinEm($('#reel1 .chips'), 118 * $('.reel:first .chips .chip').length);
                    self.game.ie.spinEm($('#reel2 .chips'), 118 * $('.reel:first .chips .chip').length);
                    self.game.ie.spinEm($('#reel3 .chips'), 118 * $('.reel:first .chips .chip').length);
                }
                self.messageBox.stopAnimation();
                self.messageBox.setRandomText('spinning');//пишем текст текстовой панели
                self.messageBox.animateText("spiningAnimation");//стартуем анимацию текстовой панели
                clearTimeout(self.spinningTimeout);

                self.spinningTimeout = null;//чистим таймаут

                self.spinningTimeout = setTimeout(function () {//ставим таймер на максимальное кручение спинов
                    game.stopAll();//если таймер сработал останавливаем рельсы
                    clearTimeout(self.spinningTimeout);
                    self.spinningTimeout = null;//чистим таймаут
                }, self.config.game.maxTimeSpinning);
                game.result = null;//чистим предыдущий результат
                game.response = null;
                self.send({//шлем запрос на получения результатов спина
                    type: "GET",
                    url: self.slot_config.api_url.play,
                    data: {},
                    cb: function (response) {
                        game.response = response;

                        game.playResultHandler(response);


                    },
                    err: function () {
                        self.notAvailableState();
                    }
                });
            },
            stopAll: function (isAnimationsStopped) {
                var self = $el.slotMachine;
                var game = this;

                console.log('stopAll');
                var check = function () {
                    if (game.response) {
                        self.stopReels(isAnimationsStopped);
                        self.config.game.usedUserAttempts++;
                        game.isWin(isAnimationsStopped);
                        clearTimeout(game.waitingForGameResult);
                    } else {
                        game.waitingForGameResult = setTimeout(check, 500);
                    }
                };

                check();


            },
            stop: function (game) {
                var self = $el.slotMachine;
                console.log('stop');
                if (game.reelStoped === false) {
                    game.reelStoped = 0;
                } else {
                    game.reelStoped++;
                }
                self.stopReelSpin($(self.reels[game.reelStoped]));
                console.log("real stoped index", game.reelStoped);
                $(game.buttonsRow.find('.stop .reel_dot')[game.reelStoped]).addClass('on');
                if (game.reelStoped == 2) {
                    game.stopAll(true);
                    clearTimeout(self.spinningTimeout);
                    $el.slotMachine.demoModeSpped = false;
                    game.reelStoped = false;
//                    game.buttonsRow.find('.stop .reel_dot').removeClass('on');
//                    game.buttonsRow.removeClass('stopplay').addClass('toplay');
//                    self.ledAnimations.stopAnimation(500);
//                    self.setLedPanelOptions('stop');
//                    self.ledAnimations.animationHandler.startAnimation();
//                    self.messageBox.stopAnimation();
//                    self.messageBox.animateText("loseAnimation");
//                    self.messageBox.setRandomText('nowin', null, game.user);

                }

            },
            isWin: function (isAnimationsStopped) {
                /*if win*/
                var self = $el.slotMachine;
                var game = this;
                console.log('inGame', game.inGame);
                game.inGame = false;
                if (game.result) {
                    if (game.result.prizes) {
                        setTimeout(function () {
                            self[game.result.prizes.type == "regular" ? "winChipAnimation" : "winBigChipAnimation" ](game.result.prizes.message);
                        }, 50);
                    } else {
                        setTimeout(function () {
                            self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
                            self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                            self.ledAnimations.stopAnimation(500);
                            self.setLedPanelOptions('stop');
                            self.ledAnimations.animationHandler.startAnimation();
                            self.messageBox.stopAnimation();
                            self.messageBox.animateText("loseAnimation");
                            self.messageBox.setRandomText('nowin', null, game.user);
                            game.response = null;
                        }, isAnimationsStopped ? 10 : 1500);
                        game.inGame = false;
                    }

                } else {
                    self.notAvailableState();
                }

            }
        },
        winChipAnimation: function (message) {

            var self = this;
            var chipIndex = 0;
            if (self.isie) {
                chipIndex = 1;
            }

            $.each(self.reels, function (i, reel) {
                setTimeout(function () {
                    $($(reel).find('.chip')[chipIndex]).find('.border').addClass('winChip');
                }, i * 500 + 500);

            });
            setTimeout(function () {
                self.game.response = null;
                self.$winContainer.find('.tile .rulesText').html(message);
                self.$winContainer.show();
                self.$winContainer.addClass('winner');
                self.$lb_overlay.show();
                self.game.inGame = false;
                self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                self.ledAnimations.stopAnimation(500);
                self.setLedPanelOptions('stop');
                self.ledAnimations.animationHandler.startAnimation();
                self.messageBox.stopAnimation();
                self.messageBox.animateText("winAnimation");
                self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
            }, self.reels.length * 500 + 200);
        },
        winBigChipAnimation: function (message) {
            var self = this;
            var chipIndex = 0;
            if (self.isie) {
                chipIndex = 1;
            }


            $.each(self.reels, function (i, reel) {
                setTimeout(function () {
                    $($(reel).find('.chip')[chipIndex]).find('.border').addClass('winChip');

                }, i * 500 + 500);

            });
            setTimeout(function () {
                self.game.response = null;
                self.$winContainer.find('.tile .rulesText').html(message);
                self.$winContainer.show();
                self.$winContainer.addClass('bigwinner');
                self.$lb_overlay.show();
                self.game.inGame = false;
                self.game.buttonsRow.removeClass('stopplay').addClass('toplay');
                self.ledAnimations.stopAnimation(500);
                self.setLedPanelOptions('stop');
                self.ledAnimations.animationHandler.startAnimation();
                self.messageBox.stopAnimation();
                self.messageBox.animateText("winAnimation");
                self.game.buttonsRow.find('.stop .reel_dot').removeClass('on');
            }, self.reels.length * 500 + 200);

        },
        setLedPanelOptions: function (type) {
            var self = this;
            var animationParams = self.config.ledPanel[type + 'Animation'];
            self.options.type = animationParams[0].type;
            self.options.speed = animationParams[0].speed;
            self.options.n = animationParams[0].n;
            self.options.m = animationParams[0].m;
            self.options.color = animationParams[0].color;
        },
        initLedPanel: function () {
            this.setLedPanelOptions('default');
            this.ledAnimations.initLampArrays();
        },
        ledAnimations: {
            currentAnimation: null,
            leds: null,
            params: {
            },
            initLampArrays: function () {
                var self = $el.slotMachine;
                self.$canvas = $el.find('#canvas');

                self.maxX = (parseInt(self.$canvas.width() - 48) / 10);
                self.maxY = (parseInt(self.$canvas.height() - 48) / 10);
                for (var i = 0; i < self.maxY; i++) {
                    self.lamps2[i] = [];
                    for (var j = 0; j < self.maxX; j++) {
                        var html = true;
                        if ((i == 0 && j == 0) || (i == self.maxY - 1 && j == 0) || (i == self.maxY - 1 && j == self.maxX - 1) || (i == 0 && j == self.maxX - 1)) {
                            html = false;
                        }
                        if ((i > 2 && i < self.maxY - 3) && (j > 2 && j < self.maxX - 3 )) {
                            html = false;
                        }
                        if (i >= self.maxY - 3 && (j > 15 && j < self.maxX - 16)) {
                            html = false;
                        }
                        if (html) {
                            var options = {};
                            options.y = i * 10 + 5;
                            options.x = j * 10 + 5;
                            options.isGlowing = false;
                            self.lamps.push(options);
                            self.lamps2[i].push(options);
                        }

                    }
                }
                this.step();
            },
            initAnimation: function (params) {
                var anim = this;
                $.each(params, function (i, animation) {
                    anim.setAnimation(animation.type);
                });
            },
            setAnimation: function (animationType) {
                var self = $el.slotMachine;
                if (!animationType) return;
                //animationHandler.stopAnimation();
                this.render(self.options);
                this.step();
            },
            animationHandler: {
                i: 0,
                row: 0,
                rowCol: 0,
                clean: function () {
                    var self = $el.slotMachine;
                    if (this.i >= self.lamps.length) {
                        this.i = 0;
                        this.turnOff();
                    }
                    this.row = 0;
                    this.rowCol = 0;
                },
                turnOff: function () {
                    var self = $el.slotMachine;
                    for (var j = 0; j < self.lamps.length; j++) {
                        self.lamps[j].isGlowing = false;
                    }
                },
                clearParams: function () {
                    var self = $el.slotMachine;
                    this.turnOff();
                    self.options.type = false;
                    self.options.speed = 0;
                    self.options.n = 0;
                    self.options.m = 0;
                    this.i = 0;
                    this.row = 0;
                    this.rowCol = 0;
                },
                random: function (i) {
                    var self = $el.slotMachine;
                    var isOn = parseInt(Math.random() * 2 + 1) % 2 == 0;
                    if (isOn) {
                        self.lamps[i].isGlowing = !self.lamps[i].isGlowing;
                    }
                },
                toggle: function (i) {
                    var self = $el.slotMachine;
                    self.lamps[i].isGlowing = !self.lamps[i].isGlowing;
                },
                leftToRight: function (i, n) {
                    var self = $el.slotMachine;
                    for (var a = 0; a < n; a++) {
                        if (this.i + a < self.lamps.length) {
                            self.lamps[this.i + a].isGlowing = true;
                        }
                    }
                },
                doubleRow: function (i, n, m) {
                    var temp = this.rowCol;
                    var self = $el.slotMachine;

                    for (var r = this.row; r < this.row + m; r++) {
                        //  console.log(temp)
                        for (var a = temp; a < n + temp; a++) {

                            if (self.lamps2[r] && self.lamps2[r][a]) {
                                self.lamps2[r][a].isGlowing = true;
                            }
                            if (self.lamps2[r] && this.rowCol >= self.lamps2[r].length) {
                                this.rowCol = 0;
                                this.row += m;
                            }


                        }
                    }
                },
                startAnimation: function () {
                    var self = $el.slotMachine;
                    this.turnOff();
                    this.i = 0;
                    this.row = 0;
                    this.rowCol = 0;
                    self.isStopped = false;
                    self.ledAnimations.step();
                },
                stopAnimation: function () {
                    var self = $el.slotMachine;
                    self.isStopped = true;
                    this.clearParams();
                }
            },
            render: function (type, n, m) {
                var self = $el.slotMachine;
                var anim = this;
                n = n || 1;
                m = m || 1;
                if (!self.led_off_loaded && !self.led_off_load_started) {
                    self.led_off = new Image();
                }
                var led_offLoadedCb = function () {
                    self.canvas.width = self.canvas.width;
                    self.led_off_loaded = true;
                    var lamps = self.lamps;
                    var lamps2 = self.lamps2;
                    var options = self.options;
                    var context = self.context;
                    for (var i = 0; i < lamps.length; i++) {
                        anim.animationHandler[type] && anim.animationHandler[type](i, n, m);

                        var r = 12;
                        var grd = context.createRadialGradient(lamps[i].x + r * 2, lamps[i].y + r * 2, 4, lamps[i].x + r * 2, lamps[i].y + r * 2, r * 2);
                        var blurColor = lamps[i].isGlowing ? grd : 'rgba(' + options.color + ',0)';
                        var lampColor = lamps[i].isGlowing ? 'rgba(' + options.color + ',1)' : 'rgba(' + options.color + ',0)';
                        grd.addColorStop(1, 'rgba(' + options.color + ',0)');
                        grd.addColorStop(0, 'rgba(' + options.color + ',0.2)');
                        context.save();
                        context.fillStyle = blurColor;
                        context.beginPath();
                        context.arc(lamps[i].x + r * 2, lamps[i].y + r * 2, r * 2, 0, Math.PI * 2, true);
                        context.closePath();
                        context.fill();

                        context.fillStyle = lampColor;
                        context.beginPath();
                        context.arc(lamps[i].x + r * 2, lamps[i].y + r * 2, 4, 0, Math.PI * 2, true);
                        context.closePath();
                        context.fill();
                        context.restore();

                        context.drawImage(self.led_off, lamps[i].x, lamps[i].y);
                    }
                    anim.animationHandler.rowCol += n;
                    anim.animationHandler.i += n;
                    if (type != 'doubleRow') {
                        anim.animationHandler.clean();
                    }
                    else if (anim.animationHandler.row >= lamps2.length) {
                        anim.animationHandler.clean();
                    }
                };
                if (self.led_off_loaded) {
                    led_offLoadedCb();
                } else {
                    if (!self.led_off_load_started) {
                        self.led_off.onload = led_offLoadedCb;
                        self.led_off.src = self.slot_config.api_url.img_led_off || "/styles/game/slots/img/slot_led_off.png";
                        self.led_off_load_started = true;
                    }
                }


            },

            step: function () {
                var anim = this;
                var self = $el.slotMachine;
                if (!self.isStopped) {
                    clearInterval(self.ledStepIntervalAnimation);
                    self.ledStepIntervalAnimation = setTimeout(function () {
                        self.ledAnimations.render(self.options.type, self.options.n, self.options.m);
                        requestAnimationFrame(self.ledAnimations.step);
                    }, 1000 / self.options.speed);
                }
            },
            stopAnimation: function (timer) {
                var anim = this;
                setTimeout(function () {
                    //anim.animationHandler.stopAnimation();
                }, timer || 400);

            }

        },
        stopReelSpin: function ($reel) {
            var self = this;
            if (!self.isie) {
                $reel.removeClass('spinning');
                self.game.stopReelAnimation($reel);

                $reel.addClass('stop_spinning');
            } else {
                console.log('ie stop reel ', $reel);
                self.game.ie.stop($reel);
            }
        },
        stopReels: function (isAnimationsStopped) {
            var self = this;

            $.each(self.reels, function (i, reel) {
                if (isAnimationsStopped) {
                }
                else {
                    self.stopTimeout = setTimeout(function () {
                        self.stopReelSpin($(reel));
                        if (self.game.buttonsRow) {
                            console.log("index", i);
                            $(self.game.buttonsRow.find('.stop .reel_dot')[i]).addClass('on');
                        }
                    }, i * 500 + 500);
                }

            });
        },

        stopChipAnimation: function () {
            $el.find('.winChip').removeClass('winChip');
        },
        messageBox: {
            textBox: $el.find('#slotsMessageReel #messages'),
            textInterval: {
            },
            left: 0,
            isShowing: false,
            getRandomText: function (type) {
                var self = $el.slotMachine;
                var labelArr = self.slot_config.labels.messageBox[type];
                return labelArr[parseInt(Math.random() * labelArr.length)];
            },
            setRandomText: function (type, message, inputs) {
                var self = $el.slotMachine;
                this.textBox = $el.find('#slotsMessageReel #messages');
                this.textBox.text(message ? message : this.getRandomText(type).format(inputs));
            },
            init: function () {
                this.stopAnimation();
                this.setRandomText("demo");
                this.animateText("defaultAnimation");
            },
            animateText: function (step) {
                var self = $el.slotMachine;
                var an = self.config.textPanel[step];
                this.applyAnimation(an.animationType, an.step, an.delay, an.speed);

            },
//            leftToRight1: function (step, delay, speed) {
//                console.log("leftToRight");
//                if (this.textBox.css('x') && parseInt(this.textBox.css('x').replace('px', '')) > 600) {
//                    this.textBox.css('x', '-300px');
//                }
//                this.textBox.transition({ x: '+=' + (step ? step : 3) + 'px', delay: delay ? delay : 1, duration: speed ? speed : 100 });
//            },
            leftToRight: function (step, delay, speed) {
                var self = this;
                self.isShowing = true;
                self.textBox.attr('style', '');
//                self.textBox.animate({left:600}, {duration:15000, easing: "linear"}); //$({ tempMoney: 0 })
                self.textBox.css("left", self.left + "px");
                self.textBox.animate({ left: 577 }, {
//                $({ left: self.left }).animate({ left: 577 }, {
                    duration: speed ? (speed * (Math.abs(self.left) + 577)) / 3 : 100 * 300,
                    easing: 'linear',
                    step: function () {
                        if (self.isShowing) {
                            self.left += 3;
                            self.textBox.css("left", self.left);
                        } else if (self.left > 577) {
//                            this.complete();
                        }
                    },
                    complete: function () {
                        self.textBox.css("left", -577 + "px");
                        self.left = -577;
                        if (self.isShowing) {
                            self.applyAnimation("leftToRight", step, delay, speed);
                        }
                    }
                });
            },
//            rightToLeft: function (step, delay, speed) {
//                console.log("rightToLeft");
//                if (this.textBox.css('x') && parseInt(this.textBox.css('x').replace('px', '')) < -300) {
//                    this.textBox.css('x', '900px');
//                }
//                this.textBox.transition({ x: '-=' + (step ? step : 3) + 'px', delay: delay ? delay : 1, duration: speed ? speed : 100 });
//            },
            rightToLeft: function (step, delay, speed) {
                var self = this;
                self.isShowing = true;
                self.textBox.attr('style', '');
                self.textBox.css("left", self.left + "px");
                self.textBox.animate({ left: -577 }, {
//                $({ left: self.left }).animate({ left: -577 }, {
                    duration: speed ? (speed * (Math.abs(self.left) + 577)) / 3 : 100 * 300,
                    easing: 'linear',
                    step: function () {
                        if (self.isShowing) {
                            self.left -= 3;
                            self.textBox.css("left", self.left);
                        }
                    },
                    complete: function () {
                        self.textBox.css("left", 577 + "px");
                        self.left = 577;
                        if (self.isShowing) {
                            self.applyAnimation("rightToLeft", step, delay, speed);
                        }
                    }
                });
            },
//            toggle: function (step, delay, speed) {
//                console.log("toggle");
//                this.textBox.attr('style', '');
//                this.textBox.transition({ x: '0px', delay: 1, duration: 1});
//                var opacity = this.textBox.css('opacity') != 0;
//                this.textBox.transition({ opacity: opacity ? 0 : 1, delay: delay ? delay : 0, duration: speed ? speed : 100 });
//            },
            toggle: function (step, delay, speed) {
                var self = this;
                self.isShowing = true;
                self.left = 0;
                self.textBox.css('left', 0 + "px");
                if (self.isShowing) {
                    self.textBox.attr('style', 'left: 1px; -webkit-animation: text_animation_toggle ' + ((speed ? speed : 500) / 1000) + 's linear infinite;')
                }

//                -webkit-animation: text_animation_toggle 0.5s linear infinite;
//                self.textBox.css('left', 0 + "px");
//                self.textBox.animate({opacity: 0 }, { duration: 1000, easing: "linear", complete: function () {
//                    self.textBox.css("opacity", 1);
//                    if (self.isShowing) {
//                        setTimeout(function () {
//                            self.applyAnimation("toggle", step, delay, speed)
//                        },(speed ? speed : 1000));
//
//                    }
//                }});
            },
            random: function (step, delay, speed) {
                var self = this;
                self.isShowing = true;
                self.left = 0;
                self.textBox.css('left', 0 + "px");
                if (self.isShowing) {
                    self.textBox.attr('style', 'left: 1px; -webkit-animation: text_animation_toggle ' + ((speed ? speed : 500) / 1000) + 's linear infinite;')
                }
            },
            applyAnimation: function (animationType, step, delay, speed) {
                var textBoxPane = this;
                var self = $el.slotMachine;
//                this.textBox.transition({ x: '0px', delay: 1, duration: 1});
//                this.textBox.attr('style', '');
//                setTimeout(function () {
//                    textBoxPane.textInterval[animationType] = setInterval(function () {
//                        textBoxPane[animationType] && textBoxPane[animationType](step, delay, speed);
//                    }, (speed ? speed : 300) + 50);
//                    console.log(textBoxPane.textInterval);
//                }, speed ? speed : 300);
                textBoxPane.stopAnimation();
                textBoxPane[animationType] && textBoxPane[animationType](step, delay, speed);
            },
            stopAnimation: function () {
                var self = $el.slotMachine;
                var textBoxPane = this;
//                textBoxPane.clearIntervals();
//                this.textBox.attr('style', '');
                this.isShowing = false;
                this.textBox.stop(true, false);
//                this.textBox.transition({ x: '0px', delay: 0, duration: 0});
            },
            clearIntervals: function () {
                var textBoxPane = this;
                clearInterval(textBoxPane.textInterval["toggle"]);
                clearInterval(textBoxPane.textInterval["leftToRight"]);
                clearInterval(textBoxPane.textInterval["rightToLeft"]);
                textBoxPane.textInterval = {};
            }

        }
    };

    $el.slotMachine.initialize();
    $el.data("slotMachine", $el.slotMachine);
    return $el;
};

$(function() {
	var
		slotsPopup = $('#slotsPopup'),
		slotsWrapWrapper = $('#slotsWrapWrapper'),
		config = slotsWrapWrapper.data('config');

	slotsPopup.messageBox = slotsPopup.find('.message');

	slotsWrapWrapper.slots({
		labels: config.labels,
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
			init: 'http://' + config.mainHost + '/game/slots/init',
			play: 'http://' + config.mainHost + '/game/slots/play',
			img_led_off: '/styles/game/slots/img/slot_led_off.png'
		}
	}, config.animations);
});