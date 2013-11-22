window.ANALYTICS = {
	
	// todo SITE-1049
	heiasMain : function() {
		(function(d){
			var HEIAS_PARAMS = [];
			HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
			HEIAS_PARAMS.push(['pb', '1']);
			if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
			window.HEIAS.push(HEIAS_PARAMS);
			var scr = d.createElement('script');
			scr.async = true;
			scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
			var elem = d.getElementsByTagName('script')[0];
			elem.parentNode.insertBefore(scr, elem);
		}(document)); 
	},

	heiasProduct : function() {
		var product = arguments[0];
		(function(d){
			var HEIAS_PARAMS = [];
			HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
			HEIAS_PARAMS.push(['pb', '1']);
			HEIAS_PARAMS.push(['product_id', product]);
			if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
			window.HEIAS.push(HEIAS_PARAMS);
			var scr = d.createElement('script');
			scr.async = true;
			scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
			var elem = d.getElementsByTagName('script')[0];
			elem.parentNode.insertBefore(scr, elem);
		}(document));
	},

	heiasOrder : function() {
		var orderArticle = arguments[0];

		(function(d){
			var HEIAS_PARAMS = [];
			HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
			HEIAS_PARAMS.push(['pb', '1']);
			HEIAS_PARAMS.push(['order_article', orderArticle]);
			if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
			window.HEIAS.push(HEIAS_PARAMS);
			var scr = d.createElement('script');
			scr.async = true;
			scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
			var elem = d.getElementsByTagName('script')[0];
			elem.parentNode.insertBefore(scr, elem);
		}(document));            
	},

	heiasComplete : function() {
		var a = arguments[0];

		HEIAS_T=Math.random(); HEIAS_T=HEIAS_T*10000000000000000000;
		var HEIAS_SRC='https://ads.heias.com/x/heias.cpa/count.px.v2/?PX=HT|' + HEIAS_T + '|cus|12675|pb|1|order_article|' + a.order_article + '|product_quantity|' + a.product_quantity + '|order_id|' + a.order_id + '|order_total|' + a.order_total + '';
		document.write('<img width="1" height="1" src="' + HEIAS_SRC + '" />');
		
		(function(d) {
			var HEIAS_PARAMS = [];
			HEIAS_PARAMS.push(['type', 'cpx'], ['ssl', 'force'], ['n', '12564'], ['cus', '14935']);
			HEIAS_PARAMS.push(['pb', '1']);
			HEIAS_PARAMS.push(['order_article',  a.order_article ]);
			HEIAS_PARAMS.push(['order_id', a.order_id ]);
			HEIAS_PARAMS.push(['order_total', a.order_total ]);
			HEIAS_PARAMS.push(['product_quantity', a.product_quantity ]);
			if (typeof window.HEIAS == 'undefined') window.HEIAS = []; window.HEIAS.push(HEIAS_PARAMS);
			var scr = d.createElement('script');
			scr.async = true;
			scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js'; var elem = d.getElementsByTagName('script')[0];
			elem.parentNode.insertBefore(scr, elem);
		}(document));
	},
	
	mixmarket : function() {
		document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>')
	},

	adriverCommon : function() {
		var RndNum4NoCash = Math.round(Math.random() * 1000000000);
		var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
		document.write('<img src="' + ('https:' == document.location.protocol ? 'https:' : 'http:') + '//ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
	},

	adriverProduct : function() {
		var a = arguments[0];

		var RndNum4NoCash = Math.round(Math.random() * 1000000000);
		var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
		document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&bt=21&pz=0'+
			'&custom=10='+ a.productId +';11='+ a.categoryId +
			'&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
	},

	adriverOrder : function() {
		var a = (arguments && arguments[0]) ? arguments[0] : false,
            ordNum = (a && a.order_id ) ? a.order_id : false;

        if (!ordNum) {
            return;
        }

		var RndNum4NoCash = Math.round(Math.random() * 1000000000);
		var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
		document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&sz=order&bt=55&pz=0'+
			'&custom=150='+ ordNum +
			'&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border="0" width="1" height="1" alt="" />');
	},
	
	// yandexMetrika : function() {
	//     (function (d, w, c) {
	//         (w[c] = w[c] || []).push(function() {
	//             try {
	//             w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true});
	//             } catch(e) {}
	//         });

	//         var n = d.getElementsByTagName("script")[0],
	//         s = d.createElement("script"),
	//         f = function () { n.parentNode.insertBefore(s, n); };
	//         s.type = "text/javascript";
	//         s.async = true;
	//         s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

	//         if (w.opera == "[object Opera]") {
	//             d.addEventListener("DOMContentLoaded", f);
	//         } else { f(); }
	//     })(document, window, "yandex_metrika_callbacks");
	// },

	LiveTexJS: function () {
		var LTData = $('#LiveTexJS').data('value');
		window.liveTexID = LTData.livetexID;
		window.liveTex_object = true;

		window.LiveTex = {
			onLiveTexReady: function () {
				window.LiveTex.setName(LTData.username);
			},

			invitationShowing: false,

			addToCart: function (productData) {
				var userid = ( LTData.userid ) ? LTData.userid : 0;
				if ( !productData.name || !productData.article ) {
					return false;
				}
				window.LiveTex.setManyPrechatFields({
					'Department': 'Marketing',
					'Product': productData.article,
					'Ref': window.location.href,
					'userid': userid
				});

				if ( (!window.LiveTex.invitationShowing) && (typeof(window.LiveTex.showInvitation) == 'function') ) {
					LiveTex.showInvitation('Здравствуйте! Вы добавили корзину ' + productData.name + '. Может, у вас возникли вопросы и я могу чем-то помочь?');
					LiveTex.invitationShowing = true;
				}

			} // end of addToCart function

		}; // end of LiveTex Object

		//$(document).load(function() {
		(function () {
			var lt = document.createElement('script');
			lt.type = 'text/javascript';
			lt.async = true;
			lt.src = 'http://cs15.livetex.ru/js/client.js';
			var sc = document.getElementsByTagName('script')[0];
			if ( sc ) sc.parentNode.insertBefore(lt, sc);
			else  document.documentElement.firstChild.appendChild(lt);
		})();
		//});
	},

	ActionPayJS: function () {
		var basketEvents = function ( pageType, product ) {
				var aprData = {pageType: pageType};

				if ( typeof(window.APRT_SEND) == 'undefined' || typeof(product) == 'undefined' ) {
					return false;
				}

				aprData.currentProduct = {
					id: product.id,
					name: product.name,
					price: product.price
				};
				window.APRT_SEND(aprData);

				console.log('*** basketEvents addToBasket: ');
				console.log(aprData);
			},
			addToBasket = function (event, data) {
				basketEvents(8, data.product);
				console.log('*** ActionPayJS addToBasket: ');
			},
			remFromBasket = function (event, data) {
				//basketEvents(9, data);
				console.log('*** ActionPayJS remFromBasket: ');
				console.log('data');
				console.log(data);
				console.log(event);
			}
		;

		$('body').on('addtocart', addToBasket);
		$('div.basketinfo a.whitelink:first').on('click', remFromBasket);

		(function () {
			var s = document.createElement('script'),
				x = document.getElementsByTagName('script')[0],
				vars = $('#ActionPayJS').data('vars');

			if ( typeof(vars) != 'undefined' ) {
				if ( vars.extraData ) {
					if ( true == vars.extraData.cartProducts && ENTER.config.cartProducts ) {
						vars.basketProducts = ENTER.config.cartProducts;
					}
					delete vars.extraData;
				}
				window.APRT_DATA = vars;
			}

			//s.type  = 'text/javascript';
			s.src = '//rt.actionpay.ru/code/enter/';
			s.defer = true;
			x.parentNode.insertBefore(s, x);
		})();
	},

	yaParamsJS: function () {
		var yap = $('#yaParamsJS').data('vars');
		if ( yap ) {
			window.yaParams = yap;
		}
	},

    // enterleadsJS : function() { // SITE-1911
    //     (function () {
    //         try {
    //             var script = document.createElement('script');

    //             script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') +
    //                 unescape('bn.adblender.ru%2Fpixel.js%3Fclient%3Denterleads%26cost%3D') + escape(0) +
    //                 unescape('%26order%3D') + escape(0) + unescape('%26r%3D') + Math.random();

    //             document.getElementsByTagName('head')[0].appendChild(script);

    //         } catch (e) {
    //         }
    //     })();
    // },

    sociomantic : function() {
        (function(){
            var s   = document.createElement('script');
            var x   = document.getElementsByTagName('script')[0];
            s.type  = 'text/javascript';
            s.async = true;
            s.src   = ('https:'==document.location.protocol?'https://':'http://')
                + 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
            x.parentNode.insertBefore( s, x );
        })();
    },

    sociomanticCategoryPage : function() {
        (function(){
            window.sonar_product = {
                category : $('#sociomanticCategoryPage').data('prod-cats')
            };
        })();
    },

    sociomanticProductPageStream : function() {
        (function(){
            window.sonar_product = $('#sociomanticProductPageStream').data('scr-product');
            window.sonar_product.category = $('#sociomanticProductPageStream').data('prod-cats');
        })();
    },

    sociomanticBasket : function() {
        (function(){
            window.sonar_basket = {
                products: $('#sociomanticBasket').data('cart-prods')
            };
        })();
    },

    criteoJS : function() {
        window.criteo_q = window.criteo_q || [];
        var criteo_arr =  $('#criteoJS').data('value');
        if ( typeof(criteo_q) != "undefined" && !jQuery.isEmptyObject(criteo_arr) ) {
            try{
                window.criteo_q.push(criteo_arr);
            } catch(e) {
            }
        }
    },

    flocktoryAddScript : function() {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = "//api.flocktory.com/1/hello.2.js";
        var l = document.getElementsByTagName('script')[0];
        l.parentNode.insertBefore(s, l);
    },

    jsOrderFlocktory : function() {
        console.info('foctory order complete');
        console.log($('#jsOrderFlocktory').data('value'));

        var _flocktory = window._flocktory = _flocktory || [],
            flocktoryData = $('#jsOrderFlocktory').data('value');
        // end of vars

        _flocktory.push(flocktoryData);

        this.flocktoryAddScript();
    },

    flocktoryJS : function() {
        this.flocktoryAddScript();

        window.Flocktory = {
            /**
             * Структура методов объекта:
             * popup_bind(elem)  связывает событие  subscribing_friend()  с элементом (elem)
             * subscribing_friend()  проверяет емейл/телефон и вызывает  popup_open()
             *
             * flk - сокращение от flocktory
             */
            name : '',
            mail : '',

            popup_prepare : function () {
                var flk_mail = $('.flocktory_email'); // Проверим эти элементы
                if ( !flk_mail.length ) flk_mail = $('.subscribe-form__email');
                if ( !flk_mail.length ) flk_mail = $('#recipientEmail');
                flk_mail = flk_mail.val();

                var flk_name = $('input.bFastInner__eInput').val(); // используем имя пользователя, если существует
                if (flk_name && !flk_name.length && flk_mail && flk_mail.length ) flk_name = flk_mail;

                if ( !flk_mail || !flk_mail.length ) {
                    // если нет емейла, глянем телефон и передадим его вместо мейла
                    var flk_tlf = $('#phonemask').val().replace(' ','');
                    //flk_mail = $('.flocktory_tlf').val() + '@email.tlf';
                    if ( !flk_name.length ) flk_name = flk_tlf;
                    flk_mail = flk_tlf + '@email.tlf'; // допишем суффикс к тлф, дабы получить фиктивный мейл и передать его
                }

                if ( flk_mail.search('@') !== -1 ) {
                    //if (!flk_name || !flk_name.length) flk_name = 'Покупатель';
                    if (!flk_name || !flk_name.length) flk_name = flk_mail;
                    window.Flocktory.name = flk_name;
                    window.Flocktory.mail = flk_mail;
                    return true;
                }
                return false;
            },

            subscribing_friend: function () {
                if ( Flocktory.popup_prepare() ) {
                    return Flocktory.popup_subscribe(Flocktory.mail, Flocktory.name);
                }
                return false;
            },

            popup_opder : function ( toFLK_order )  {
				try{
					if ( Flocktory.popup_prepare() ) {
						toFLK_order.email = Flocktory.mail;
						toFLK_order.name = Flocktory.name;
						return Flocktory.popup(toFLK_order);
					}
				}catch(e){};
                return false;
            },

            popup_bind : function( jq_el ) { // передаётся элемент вида — $('.jquery_elem')
                if ( jq_el && jq_el.length ) {
                    // Если элемент существует, навесим событие вызовом flocktory по клику
                    jq_el.bind('click', function () {
                        Flocktory.subscribing_friend();
                    });
                }
            },

            popup_bind_default : function( ) {
                // Свяжем действия со стандартными названиями кнопок
                Flocktory.popup_bind( $('.run_flocktory_popup') );
                Flocktory.popup_bind( $('.subscribe-form__btn') );
            },

            popup: function (toFLK) {
                var _fl = window._flocktory = _flocktory || [];
                return _fl.push(toFLK);
            },

            popup_subscribe : function ( flk_mail, flk_name ) {
                //flk_mail = 'hello@flocktory.com'; // tmp, for debug
                flk_name = flk_name || flk_mail;
                var date = new Date();

                var toFLK = {
                    "order_id": date.getFullYear() + '' + date.getMonth() + '' + date.getDay() + '' + date.getHours() + '' + date.getMinutes() + '' + date.getSeconds() + '' + date.getMilliseconds() + '' + Math.floor(Math.random() * 1000000),
                    "email": flk_mail,
                    "name": flk_name,
                    "price": 0,
                    "domain": "registration.enter.ru",
                    "items": [{
                        "id": "подписка на рассылку",
                        "title": "подписка на рассылку",
                        "price":  0,
                        "image": "",
                        "count":  1
                    }]
                };

                return Flocktory.popup(toFLK);
            }

        } // end of window.Flocktory object

        Flocktory.popup_bind_default();

    },

    RetailRocketJS : function() {
        window.rrPartnerId = "519c7f3c0d422d0fe0ee9775"; // rrPartnerId — по ТЗ должна быть глобальной
        
        window.rrApi = {};
        window.rrApiOnReady = [];

        rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};

        window.RetailRocket = {

            'product': function ( data, userData ) {

                var rcAsyncInit = function () {
                    try {
                        rcApi.view(data, userData.userId ? userData : undefined); 
                    }
                    catch ( err ) {
                        var dataToLog = {
                                event: 'RR_error',
                                type: 'ошибка в rcApi.view',
                                err: err
                            };
                        // end of vars

                        ENTER.utils.logError(dataToLog);
                    }
                };

                window.rrApiOnReady.push(rcAsyncInit);
            },

            'product.category': function ( data, userData ) {

                var rcAsyncInit = function () {
                    try {
                        rcApi.categoryView(data, userData.userId ? userData : undefined);
                    }
                    catch ( err ) {
                        var dataToLog = {
                                event: 'RR_error',
                                type:'ошибка в rcApi.categoryView',
                                err: err
                            };
                        // end of vars

                        ENTER.utils.logError(dataToLog);
                    }
                };

                window.rrApiOnReady.push(rcAsyncInit);
            },

            'order.complete': function ( data, userData ) {

                if ( userData.userId ) {
                    data.userId = userData.userId;
                    data.hasUserEmail = userData.hasUserEmail;
                }

                var rcAsyncInit = function () {
                    try {
                        rcApi.order(data);
                    }
                    catch ( err ) {
                        var dataToLog = {
                                event: 'RR_error',
                                type:'ошибка в rcApi.order',
                                err: err
                            };
                        // end of vars

                        ENTER.utils.logError(dataToLog);
                    }
                };

                window.rrApiOnReady.push(rcAsyncInit);
            },

            action: function ( e, userInfo ) {
                var rr_data = $('#RetailRocketJS').data('value'),
                    sendUserData = {
                        userId: userInfo.id || false,
                        hasUserEmail: ( userInfo && userInfo.email ) ? true : false
                    };
                // end of vars


                if ( rr_data && rr_data.routeName && rr_data.sendData && window.RetailRocket.hasOwnProperty(rr_data.routeName) ) {
                    window.RetailRocket[rr_data.routeName](rr_data.sendData, sendUserData);
                }
            },

            init: function () { // on load:
                (function (d) {
                    var ref = d.getElementsByTagName('script')[0]; var apiJs, apiJsId = 'rrApi-jssdk';
                    if (d.getElementById(apiJsId)) return;
                    apiJs = d.createElement('script');
                    apiJs.id = apiJsId;
                    apiJs.async = true;
                    apiJs.src = "//cdn.retailrocket.ru/javascript/tracking.js";
                    ref.parentNode.insertBefore(apiJs, ref);
                }(document));
            }

        }// end of window.RetailRocket object

        RetailRocket.init();

        if ( ENTER.config.userInfo ) {
            RetailRocket.action(null, ENTER.config.userInfo)
        }
        else {
            $('body').on('userLogged', RetailRocket.action);
        }
        // RetailRocket.action();
    },

    AdmitadJS : function() {
        window._retag = window._retag || [];
        var ad_data = $('#AdmitadJS').data('value');

        if (ad_data) {

            if (ad_data.ad_data) {
                /**
                 * NB! Переменные потипу var ad_category должны быть глобальными согласно задаче SITE-1670
                 */
                if (ad_data.ad_data.ad_category) {
                    window.ad_category = ad_data.ad_data.ad_category;
                }

                if (ad_data.ad_data.ad_product) {
                    window.ad_product = ad_data.ad_data.ad_product;
                }

                if (ad_data.ad_data.ad_products) {
                    window.ad_products = ad_data.ad_data.ad_products;
                }

                if (ad_data.ad_data.ad_order) {
                    window.ad_order = ad_data.ad_data.ad_order;
                }

                if (ad_data.ad_data.ad_amount) {
                    window.ad_amount = ad_data.ad_data.ad_amount;
                }

            }

            if (ad_data.pushData) {
                window._retag.push(ad_data.pushData);
            }
        }

        (function(d){
            var s=document.createElement("script");
            s.async=true;
            s.src=(d.location.protocol == "https:" ? "https:" : "http:") + "//cdn.admitad.com/static/js/retag.js";
            var a=d.getElementsByTagName("script")[0];
            a.parentNode.insertBefore(s, a);
        }(document));
    },

    marketgidProd : function() {
        var MGDate = new Date();
        document.write('<iframe src ="http://'
        +'marketgid.com/resiver.html#label1'
        +MGDate.getYear()+MGDate.getMonth()
        +MGDate.getDate()+MGDate.getHours()
        +'" width="0%" height="0" sty'
        +'le = "position:absolute;left:'
        +'-1000px" ></iframe>');
    },

	marketgidOrder : function() {
		var MGDate = new Date();
		document.write('<iframe src ="http://'
		+'marketgid.com/resiver.html#label2'
		+MGDate.getYear()+MGDate.getMonth()
		+MGDate.getDate()+MGDate.getHours()
		+'" width="0%" height="0" sty'
		+'le = "position:absolute;left:'
		+'-1000px" ></iframe>');
	},

	marketgidOrderSuccess : function() {
		var MGDate = new Date();
		document.write('<iframe src ="http://'
		+'marketgid.com/resiver.html#label3'
		+MGDate.getYear()+MGDate.getMonth()
		+MGDate.getDate()+MGDate.getHours()
		+'" width="0%" height="0" sty'
		+'le = "position:absolute;left:'
		+'-1000px" ></iframe>');
	},

	runMethod : function( fnname ) {
		if( !this. enable )
			return
		document.writeln = function(){
			$('body').append( $(arguments[0] + '') )
		}

		if( fnname+'' in this ) {
			this[fnname+'']()
		}

	},

	parseAllAnalDivs : function( nodes ) {
		if( !this. enable )
			return

		var self = this
		$.each(  nodes , function() {
//console.info( this.id, this.id+'' in self  )
			
			// document.write is overwritten in loadjs.js to document.writeln
			var anNode = $(this)
			if( anNode.is('.parsed') )
				return
			document.writeln = function(){
				anNode.html( arguments[0] )
			}

			if( this.id+'' in self )
				self[this.id]( $(this).data('vars') )
			anNode.addClass('parsed')
		})
		document.writeln = function(){
			$('body').append( $(arguments[0] + '') )
		}
	},

	myThingsTracker: function() {
		//трекинг от MyThings. Вызывается при загрузке внешнего скрипта
		window._mt_ready = function (){
			if (typeof(MyThings) != "undefined") {
				var sendData = $('#myThingsTracker').data('value')
				if (!$.isArray(sendData)) {
					sendData = [sendData];
				}

				$.each(sendData, function(i, e) {
					if (e.EventType !== "undefined") {
						e.EventType = eval(e.EventType)
					}
					MyThings.Track(e)
				})
			}
		}
		mtHost = (("https:" == document.location.protocol) ? "https" : "http") + "://rainbow-ru.mythings.com";
		mtAdvertiserToken = "1989-100-ru";
		document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok="+mtAdvertiserToken+"' type='text/javascript'%3E%3C/script%3E"));
	},
	testFreak : function() {
		document.write('<scr'+'ipt type="text/javascript" src="http://js.testfreaks.com/badge/enter.ru/head.js"></scr'+'ipt>')
	},

	marinSoftwarePageAddJS: function() {
		var mClientId ='7saq97byg0';
		var mProto = ('https:' == document.location.protocol ? 'https://' : 'http://');
		var mHost = 'tracker.marinsm.com';
		var mt = document.createElement('script'); mt.type = 'text/javascript'; mt.async = true; mt.src = mProto + mHost + '/tracker/async/' + mClientId + '.js';
		var fscr = document.getElementsByTagName('script')[0]; fscr.parentNode.insertBefore(mt, fscr);
	},

	marinLandingPageTagJS : function() {
		var _mTrack = window._mTrack || [];
		_mTrack.push(['trackPage']);
		this.marinSoftwarePageAddJS();
	},

	marinConversionTagJS : function() {
		var orders = $('#marinConversionTagJS').data('value'),
			_mTrack = window._mTrack || [];
		// end of vars

		if ( orders.length ) {
			for ( var i in orders ) {
				if ( orders[i]['id'] ) {
					_mTrack.push(['addTrans', {
						items : [
							{
								convType : 'sales',
								orderId : orders[i]['id']	// order‑id
							}
						]
					}]);

					_mTrack.push(['processOrders']);
				}
			}
			this.marinSoftwarePageAddJS();
		}
	},


	enable : true
}

ANALYTICS.parseAllAnalDivs( $('.jsanalytics') )

var ADFOX = {
	adfoxbground : function() {
		if( $(window).width() < 1000 ) // ATTENTION
			return

		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>'+
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=enlz&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);		
	},
	
	adfox400counter : function() {
	 if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);  
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>')
		AdFox_getCodeScript(1,pr1, 'http://ads.adfox.ru/171829/prepareCode?p1=biewf&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfox400 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);	
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=engb&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},
	
	adfox215 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>')
		document.write( '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emud&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);		
	},
	
	adfox683 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},
	
	adfox683sub : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);

		document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
		document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=bdto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfox980 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>'+
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emvi&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfoxWowCredit : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);

		document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
		document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?p1=bipsp&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfoxGift : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);

		document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
		document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?p1=bizeq&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfox920: function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);

		document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
		document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=epis&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfox_categoryFilterBanner: function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
			if (typeof(afReferrer) == 'undefined') {
				afReferrer = escape(document.referrer);
			}
		} else {
			afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);

		document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
		document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=espi&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	parseAllAdfoxDivs : function( nodes ) {
		 if( !this. enable )
			return

		if( window.addEventListener ) {
			var nativeEL = window.addEventListener
			window.addEventListener = function(){
//console.info('addEventListener WINDOW', arguments[0])
			  nativeEL.call(this, arguments[0], arguments[1])
			  if( arguments[0] === 'load' )
				arguments[1]()
			}
		} else if( window.attachEvent ) { //IE < 9
			var nativeEL = window.attachEvent
			window.attachEvent = function(){
//console.info('addEventListener WINDOW', arguments[0])
//console.info('addEventListener WINDOW', arguments[0])
			  //nativeEL.call(window, arguments[0], arguments[1])
			  if( arguments[0] === 'onload' )
				arguments[1]()
			}
		}        
			
		var anNode = null
		document.writeln = function() {
			if( anNode )
				anNode.innerHTML += arguments[0]
		}

		$.each( nodes , function() {
//console.info( this.id, this.id+'' in ADFOX  )
			anNode = this
			if( this.id+'' in ADFOX ) {
				ADFOX[this.id]()
			}
		})
		anNode = null
		document.writeln = function(){
			$('body').append( $(arguments[0] + '') )
		}
	},

	enable : true
}

ADFOX.parseAllAdfoxDivs( $('.adfoxWrapper') )
