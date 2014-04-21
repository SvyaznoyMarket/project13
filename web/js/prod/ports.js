var
	body = $('body'),
	docJQ = $(document);

console.log('ports.js inited');

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
		console.group('ports.js::LiveTexJS log');

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

				if ( (!window.LiveTex.invitationShowing) && (typeof(window.LiveTex.showInvitation) === 'function') ) {
					LiveTex.showInvitation('Здравствуйте! Вы добавили корзину ' + productData.name + '. Может, у вас возникли вопросы и я могу чем-то помочь?');
					LiveTex.invitationShowing = true;
				}

			} // end of addToCart function

		}; // end of LiveTex Object

		//$(document).load(function() {
		(function () {
			console.info('LiveTexJS init');

			var lt = document.createElement('script');
			lt.type = 'text/javascript';
			lt.async = true;
			lt.src = 'http://cs15.livetex.ru/js/client.js';
			var sc = document.getElementsByTagName('script')[0];
			if ( sc ) sc.parentNode.insertBefore(lt, sc);
			else  document.documentElement.firstChild.appendChild(lt);

			console.log('LiveTexJS end');
		})();

		console.groupEnd();
		//});
	},

	ActionPayJS: function () {
		var basketEvents = function ( pageType, product ) {
				var aprData = {pageType: pageType};
				if ( typeof(window.APRT_SEND) === 'undefined' || typeof(product) === 'undefined' ) {
					return false;
				}

				aprData.currentProduct = {
					id: product.id,
					name: product.name,
					price: product.price
				};
				window.APRT_SEND(aprData);
			},
			addToBasket = function (event, data) {
				basketEvents(8, data.product);
			},
			remFromBasket = function (event, product) {
				basketEvents(9, product);
			};

		$('body').on('addtocart', addToBasket);
		$('body').on('remFromCart', remFromBasket);

		(function () {
			var s = document.createElement('script'),
				x = document.getElementsByTagName('script')[0],
				elem = $('#ActionPayJS'),
				vars = elem.data('vars');

			if ( 0 === elem.length ) {
				return;
			}

			if ( typeof(vars) === 'undefined' ) {
				vars = {};
				vars.pageType = 0;
			}
			else if ( vars.extraData ) {
				if ( true == vars.extraData.cartProducts && ENTER.config.cartProducts ) {
					vars.basketProducts = ENTER.config.cartProducts;
				}
				delete vars.extraData;
			}
			if ($('body').data('template') != 'order_new') window.APRT_DATA = vars;

			s.type  = 'text/javascript';
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


	/**
	 * CityAds counter
 	 */
	xcntmyAsync: function () {
		var
			elem = $('#xcntmyAsync'),
			data = elem ? elem.data('value') : false,
			page = data ? data.page : false,
		// end of vars

			init = function() {
				(function(){
					var xscr = document.createElement( 'script' );
					var xcntr = escape(document.referrer); xscr.async = true;
					xscr.src = ( document.location.protocol === 'https:' ? 'https:' : 'http:' )
						+ '//x.cnt.my/async/track/?r=' + Math.random();
					var x = document.getElementById( 'xcntmyAsync' );
					x.parentNode.insertBefore( xscr, x );
				}());
			},

			cart = function() {
				window.xcnt_basket_products = data.productIds; 			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
				window.xcnt_basket_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
			},

			complete = function() {
				window.xcnt_order_products = data.productIds;			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
				window.xcnt_order_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
				window.xcnt_order_id = data.orderId;					// где XXXYYY – это ID заказа (желательно, можно  шифровать значение в MD5)
				window.xcnt_order_total = data.orderTotal;				// сумма заказа (опционально)
			},

			product = function() {
				window.xcnt_product_id = data.productId;				// где ХХ – это ID товара в каталоге рекламодателя.
			}
		;// end of functions


		if ( 'cart' === page ) {
			cart();
		} else if ( 'order.complete' === page ) {
			complete();
		} else if ( 'product' === page ) {
			product();
		}
		init();

	},

	sociomanticJS: function () {
		/*(function () {
			var s = document.createElement('script'),
				x = document.getElementsByTagName('script')[0];
			s.type = 'text/javascript';
			s.async = true;
			s.src = ('https:' == document.location.protocol ? 'https://' : 'http://')
				+ 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
			x.parentNode.insertBefore(s, x);
		})();*/
	},

	smanticPageJS: function() {
		console.log('smanticPageJS');
		var
			elem = $('#smanticPageJS'),
			prod = elem.data('prod'),
			prod_cats = elem.data('prod-cats'),
			cart_prods = elem.data('cart-prods');

		window.sonar_product = window.sonar_product || {};

		if ( prod ) {
			window.sonar_product = prod;
		}

		if ( prod_cats ) {
			window.sonar_product.category = prod_cats;
		}

		if ( cart_prods ) {
			window.sonar_basket = { products: cart_prods };
		}
	},

    criteoJS : function() {
		console.log('criteoJS');
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

	/**
	 * Google Universal Analytics Tracking
	 *
	 * @requires jQuery
	 *
	 * @author	Misiukevich Juljan
	 */
	gaJS : function() {
		var
			template	= body.data('template') || '',
			templSep	= template.indexOf(' '),
			templLen	= template.length,
			route 		= template.substring(0, (templSep > 0) ? templSep : templLen),
			rType 		= (templSep > 0) ? template.substring(templSep + 1, templLen) : '',
			data		= $('#gaJS').data('vars'),
		// end of vars

			ga_init = function ga_init() {
				console.log( 'gaJS init' );

				(function (i, s, o, g, r, a, m) {
					i['GoogleAnalyticsObject'] = r;
					i[r] = i[r] || function () {
						(i[r].q = i[r].q || []).push( arguments )
					}, i[r].l = 1 * new Date();
					a = s.createElement( o ),
						m = s.getElementsByTagName( o )[0];
					a.async = 1;
					a.src = g;
					m.parentNode.insertBefore( a, m )
				})( window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga' );
			},

			gaBannerClick = function gaBannerClick( BannerId ) {
				console.log( 'GA: send', 'event', 'Internal_Promo', BannerId );
				ga( 'send', 'event', 'Internal_Promo', BannerId );
			},

			gaSubscribeClick = function gaSubscribeClick( type, email ) {
				console.log( 'GA: send', 'event', 'Subscriptions', type, email );
				ga( 'send', 'event', 'Subscriptions', type );
			}

			ga_main = function() {
				console.info( 'GoogleAnalyticsJS main page' );

				/** Событие клика на кнопку мобильного приложения */
				$('a.bMobAppLink').click(function(){
					var
						href = $(this).attr('href'),
						type = false;

					if ( 'string' !== typeof(href) ) {
						return;
					}

					if ( href.indexOf('apple.com') > 0 ) {
						type='apple';
					} else if ( href.indexOf('google.com') > 0 ) {
						type='google';
					} else if ( href.indexOf('windowsphone.com') > 0 ) {
						type='windowsphone';
					}

					if ( type ) {
						console.log('GA: Mobile App Click');
						ga('send', 'event', 'Mobile App Click', type);
					}
				});
				/**
				 * Отслеживание кликов по баннерам карусели вынесено в web/js/dev/main/welcome.js
				 */
			},

			ga_category = function ga_category() {
				console.info( 'gaJS product catalog' );
				/** Событие выбора фильтра */
				$('div.bFilterBand input:not(:checked)').click(function ga_filterBrand(){
					var
						input = $(this),
						name = input.data('name');

					if ( input.is(':checked') && 'undefined' !== name ) {
						console.log('GA: Brand clicked');
						console.log(name);
						ga('send', 'event', 'brand_selected', name);
					}
				});
			},

			ga_catalog = function ga_catalog() {
				console.info( 'gaJS catalog' );
				$('.mBannerItem').click(function() {
					var
						wrapper = $(this).find('.adfoxWrapper'),
						banner = wrapper.find('div:first')
						id = banner.attr('id') || 'adfox';
					gaBannerClick( id );
				});
			},

			ga_search = function ga_search() {
				console.info( 'gaJS search' );
			},

			ga_product = function() {
				console.info( 'gaJS product page' );
				var
					product = $('#jsProductCard').data('value'),
					gaInteractive = function gaInteractive(type) {
						console.log('GA: event Interactive: ' + type);
						ga( 'send', 'event', 'Interactive', type );
					},

					gaBannerClickPrepare = function gaBannerClickPrepare() {
						var
							img = $( this ).find( 'img' ),
							BannerId = img.attr( 'alt' ) || img.attr( 'src' );
						gaBannerClick(BannerId);
					};

				/** Событие клика на баннер */
				$( '.trustfactorRight' ).on( 'click', gaBannerClickPrepare );
				$( '.trustfactorMain' ).on( 'click', gaBannerClickPrepare );
				$( '.trustfactorContent' ).on( 'click', gaBannerClickPrepare );

				/** Событие открытия списка магазинов */
				$('span.bDeliveryNowClick').one('click', function ga_deliveryNow() {
					var
						wraper = $(this).closest('li.mDeliveryNow');

					if ( 'undefined' !== wraper && wraper.hasClass('mOpen') ) {
						console.log('GA: Available in Stores clicked!');
						ga('send', 'event', 'Available in Stores', 'Clicked');
					}
				});


				/** Событие нажатия кнопки «Купить» или «Резерв» */
				$('a.btnBuy__eLink').click(function ga_btnBuy() {
					var
						butType = $(this).hasClass('mShopsOnly') ? 'reserve' : 'add2basket';

					if ( 'undefined' !== product ) {
						console.log('GA: btn Buy');
						ga('send', 'event', butType, product.name, product.article, product.price);
					}

				});


				/** Событие нажатия кнопки миниатюры слайдера фото галереи товара */
				$('li.bPhotoSliderGallery__eItem').each(function(j){
					var
						slideItem = $(this),
						type = 'Image' + (j+1),
						link = slideItem.find('a');

					if (link) {
						console.log(link);
						link.one('click', function(){
							gaInteractive(type);
						});
					}
				});

				/** Событие нажатия кнопки 360 градусов */
				$('li.bPhotoActionOtherAction__eGrad360 a').one('click', function(){
					gaInteractive('grad360')
				});

				/** Событие нажатия кнопки «Видео» */
				$('li.mVideo a.bPhotoLink').one('click', function() {
					gaInteractive('video');
				});

				/** Событие клика по рекомендуемому товару из подборки. Не забывать, что .bSlider ajax-ом наполняется */
				$('.bProductSectionLeftCol').delegate('div.bGoodsSlider a', 'click', function() {
					var
						link = this,
						url = link.href,
						sender = url ? ENTER.utils.getURLParam('sender', url) : null,
						params = sender ? sender.split('|') : null,
						engine = params ? params[0] : null
					;

					if ( engine ) {
						//event.preventDefault(); // for debug
						console.log('GA: Recommendet link clicked, engine =', engine);
						ga('set', 'dimension1', engine);
					}
				});

				if ( data && data.afterSearch && product.article && data.upperCat ) {
					console.log('GA: Items after Search', data.upperCat, product.article);
					ga('send', 'event', 'Items after Search', data.upperCat, product.article);
				}
			},

			ga_orderComplete = function ga_orderComplete() {
				var
					ecommerce = data ? data.ecommerce : false,
					addTransaction,
					items,
					send,
					count, i,
					order, j;
				// end of vars

				if ( !ecommerce ) {
					return;
				}

				console.log( 'gaJS orderComplete (require ecommerce)' );
				ga('require', 'ecommerce', 'ecommerce.js');

				for ( j in ecommerce ) {
					order = ecommerce[j];

					addTransaction = order.addTransaction;
					items = order.items;
					send = order.send;

					if ( addTransaction ) {
						console.log('ecommerce:addTransaction', addTransaction);
						ga('ecommerce:addTransaction', addTransaction);
					}

					if ( items ) {
						count = items.length;
						for ( i = 0; i < count; i++ ) {
							console.log('ecommerce:addItem', items[i]);
							ga('ecommerce:addItem', items[i]);
						}
					}

					if ( send ) {
						console.log('ecommerce:send', send);
						ga('ecommerce:send', send);
					}
				}
			},

			ga_cart = function ga_cart() {
				console.info( 'gaJS cart page' );
				var
					cartData = data ? data.cart : false;

				if ( cartData && cartData.sum ) {
					console.log('event Cart items', cartData.SKUs, cartData.uid, cartData.sum);
					ga('send', 'event', 'Cart items', cartData.SKUs, cartData.uid, cartData.sum);
				}
			},

			ga_action = function ga_action() {
				console.log( 'gaJS action' );
				switch (route) {
					case 'main':
						ga_main(); // для главной страницы
						break;
					case 'product_card':
						ga_product(); // для карточки товара
						break;
					case 'cart':
						ga_cart(); // для корзины
						break;
					case 'product_catalog':
						if ( 'search' === rType ) {
							ga_search(); // для страницы поиска
						}
						else {
							ga_category(); // для стр. категории
						}
						ga_catalog(); // для всех страниц каталога
						break;
					case 'order_complete':
						ga_orderComplete(); // для стр «Спасибо за заказ»
						break;
				}
			}
		;// end of functions

		console.group('ports.js::gaJS');
		try{
			ga_init(); // блок инициализации аналитики для всех страниц
			if ( 'function' !== typeof(ga) ) {
				console.warn('GA: init error');
				return false; // метод ga не определён, ошибка, нечего анализировать, выходим
			}
			ga( 'create', 'UA-25485956-5', 'enter.ru' );

			ga_action();

			if( data && 'object' === typeof(data.vars) && data.vars ) {
				console.log('GA: send pageview');
				console.log(data.vars);
				ga('send', 'pageview', data.vars); // трекаем весь массив с полями {dimensionN: <*М*>}
			}

			/** Событие ошибок аджакса */
			docJQ.bind('ajaxError', function ga_ajaxError(event, request, settings, error){
				//alert(request.responseText);
				console.log('GA: ajaxError');
				console.warn('ajaxError: ', event, request, settings, error);
				ga('send', 'event', 'Error', 'ajax', error); // error - <тип ошибки>
			});

			/** Событие добавления в корзину */
			body.on('addtocart', function ga_addtocart(event, data) {
				var
					productData = data.product;
				console.log('GA: addtocart clicked', productData);
				ga('send', 'event', '<button>', productData.name, productData.article, productData.price);
			});

			/** Событие выбора города */
			$('.jsChangeRegionAnalytics' ).click(function(){
				var
					regionName = $(this).text();
				console.log('GA: dimension8 (ChangeRegion)', regionName);
				ga('send', 'dimension8', regionName);
			});

			/** Событие клика на подписку | TODO: проверить другие подписки */
			$('.bSubscribeLightboxPopup__eBtn').on('click', function(){
				/*var
					email = $( this ).siblings( '.bSubscribeLightboxPopup__eInput' ).val(); // если нужно
				*/
				gaSubscribeClick( 1 );
			});

			window.gaRun = {
				register: function register() {
					/** Метод для регистрации на сайте */
					console.log('GA: Registration');
					ga( 'send', 'event', 'Registration', 'Registered' );
				},
				login: function login() {
					/** Метод для авторизации на сайт */
					console.log('GA: login');
					ga('send', 'dimension7', 'Registered');
					ga('send', 'event', 'Logged in', 'True');
				}
			};
		}
		catch(e) {
			console.warn('GA exception');
			console.log(e);
		}
		console.groupEnd();
	},

	//SITE-3027 Установка кода TagMan на сайт
	TagManJS : function() {

		initTagMan = function initTagMan() {
        	console.info( 'TagManJS init' );

			(function( d,s ) {
			    var client = 'enterru';
    		    var siteId = 1;

			  //  do not edit
			  var a=d.createElement(s),b=d.getElementsByTagName(s)[0];
			  a.async=true;a.type='text/javascript';
			  a.src='//sec.levexis.com/clients/'+client+'/'+siteId+'.js';
			  a.tagman='st='+(+new Date())+'&c='+client+'&sid='+siteId;
			  b.parentNode.insertBefore( a,b );
			} ) (document,'script');
        };

		var
  			template = body.data('template'),
  			pageLink = location.href;

		if ( template == 'order_complete' ) {
			console.info("TagManJS Order Complete");

			var 
				data = $('#jsOrder').data('value'),
				orderData = data.orders,
				orderSum = orderData[0].sum;
				orderNum = orderData[0].numberErp;

			window.tmParam = {
				page_type : 'confirmation', // REQ 
				page_name : template, // REQ 
				page_url : pageLink, // REQ
				levrev : orderSum, // REQ when available
				levordref : orderNum, // REQ when available
				levresdes : 'confirmation' // REQ when available
			};
		}
		else {
			console.info("TagManJS Default")

			window.tmParam = {
				page_type : 'generic', // REQ 
				page_name : template, // REQ 
				page_url : pageLink // REQ
			};
		};

        initTagMan();
	},

    RetailRocketJS : function() {
    	console.group('ports.js::RetailRocketJS');

        window.rrPartnerId = "519c7f3c0d422d0fe0ee9775"; // rrPartnerId — по ТЗ должна быть глобальной
        
        window.rrApi = {};
        window.rrApiOnReady = [];

        rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};

        window.RetailRocket = {

            'product': function ( data, userData ) {
            	console.info('RetailRocketJS product');

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
            	console.info('RetailRocketJS product.category');

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
            	console.info('RetailRocketJS order.complete');

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
				try {
					console.info('RetailRocketJS action');
					console.log(userInfo);
					if ( userInfo && userInfo.id ) {
						window.rrPartnerUserId = userInfo.id; // rrPartnerUserId — по ТЗ должна быть глобальной
					}

					var
						rr_data = $('#RetailRocketJS').data('value'),
						sendUserData = {
							userId: ( userInfo ) ? ( userInfo.id || false ) : null,
							hasUserEmail: ( userInfo && userInfo.email ) ? true : false
						};
					// end of vars

					if ( rr_data && rr_data.routeName && rr_data.sendData && window.RetailRocket.hasOwnProperty(rr_data.routeName) ) {
						window.RetailRocket[rr_data.routeName](rr_data.sendData, sendUserData);
					}
				} catch (err) {
					ENTER.utils.logError({
						event: 'RR_error',
						type:'ошибка в action',
						err: err
					});
				}
            },

            init: function () { // on load:
            	console.info('RetailRocketJS init');

				(function (d) {
					var
						ref = d.getElementsByTagName( 'script' )[0],
						apiJs,
						apiJsId = 'rrApi-jssdk';

					if ( d.getElementById( apiJsId ) ) return;
					apiJs = d.createElement( 'script' );
					apiJs.id = apiJsId;
					apiJs.async = true;
					apiJs.src = "//cdn.retailrocket.ru/javascript/tracking.js";
					ref.parentNode.insertBefore( apiJs, ref );
				}( document ));
            }

        }// end of window.RetailRocket object

        RetailRocket.init();

        if ( ENTER.config.userInfo && ENTER.config.userInfo.id ) {
			// ок, берём userInfo-данные из памяти
            RetailRocket.action(null, ENTER.config.userInfo);
        }
        else {
			if (false === ENTER.config.userInfo) {
				// если === false, то данных юзера не узнаем , поэтому запустим RetailRocket.action() без параметров
				RetailRocket.action(null);
			}
			else {
				// попробуем получить данные при срабатывании события
				body.on('userLogged', RetailRocket.action);
			}
        }

        console.groupEnd();
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

	AlexaJS: function () {
		_atrk_opts = {
			atrk_acct: "mPO9i1acVE000x",
			domain: "enter.ru",
			dynamic: true
		};

		(function () {
			console.log('AlexaJS init');
			var
				as = document.createElement( 'script' ),
				s  = document.getElementsByTagName( 'script' )[0];

			as.type = 'text/javascript';
			as.async = true;
			as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js";

			s.parentNode.insertBefore( as, s );
		})();
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
		console.group('parseAllAnalDivs');
		console.info('parseAllAnalDivs');

		if ( !this.enable ) {
			console.warn('Not enabled. Return');

			return;
		}

		var
			self = this;

		$.each(  nodes , function() {
//console.info( this.id, this.id+'' in self  )
			
			// document.write is overwritten in loadjs.js to document.writeln
			var
				anNode = $(this);
			// end of vars
			
			console.log(anNode);

			if ( anNode.is('.parsed') ) {
				console.warn('Parsed. Return');

				return;
			}

			document.writeln = function() {
				anNode.html( arguments[0] );
			}

			if ( this.id+'' in self ) {
				self[this.id]( $(this).data('vars') );
			}

			anNode.addClass('parsed')
		});

		document.writeln = function() {
			$('body').append( $(arguments[0] + '') );
		}
		console.log('end parseAllAnalDivs');
		console.groupEnd();
	},

	myThingsTracker: function() {
		//трекинг от MyThings. Вызывается при загрузке внешнего скрипта
		window._mt_ready = function () {
			if ( typeof(MyThings) != "undefined" ) {
				var sendData = $('#myThingsTracker').data('value');

				if ( !$.isArray(sendData) ) {
					sendData = [sendData];
				}

				$.each(sendData, function(i, e) {

					if (e.EventType !== "undefined") {
						e.EventType = eval(e.EventType);
					}
					MyThings.Track(e)
				});
			}
		}

		mtHost = (("https:" == document.location.protocol) ? "https" : "http") + "://rainbow-ru.mythings.com";
		mtAdvertiserToken = "1989-100-ru";
		document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok="+mtAdvertiserToken+"' type='text/javascript'%3E%3C/script%3E"));
	},

	testFreak : function() {
		document.write('<scr'+'ipt type="text/javascript" src="http://js.testfreaks.com/badge/enter.ru/head.js"></scr'+'ipt>')
	},

	marinSoftwarePageAddJS: function( callback ) {
		console.info('marinSoftwarePageAddJS');

		var mClientId ='7saq97byg0';
		var mProto = ('https:' == document.location.protocol ? 'https://' : 'http://');
		var mHost = 'tracker.marinsm.com';
		var mt = document.createElement('script'); mt.type = 'text/javascript'; mt.async = true; mt.src = mProto + mHost + '/tracker/async/' + mClientId + '.js';
		// var fscr = document.getElementsByTagName('script')[0]; fscr.parentNode.insertBefore(mt, fscr);


		$LAB.script( mt.src ).script( 'three.min.js' ).wait(callback);
	},

	marinLandingPageTagJS : function() {
		var marinLandingPageTagJSHandler = function marinLandingPageTagJSHandler() {
			console.info('marinLandingPageTagJS run');

			var _mTrack = window._mTrack || [];

			_mTrack.push(['trackPage']);

			console.log('marinLandingPageTagJS complete');
		};
		// end of functions

		this.marinSoftwarePageAddJS(marinLandingPageTagJSHandler);
	},

	marinConversionTagJS : function() {
		var marinConversionTagJSHandler = function marinConversionTagJSHandler() {
			console.info('marinConversionTagJS run');

			var ordersInfo = $('#marinConversionTagJS').data('value'),
				_mTrack = window._mTrack || [];
			// end of vars

			if ( 'undefined' === typeof(ordersInfo) ) {
				return;
			}

			_mTrack.push(['addTrans', ordersInfo]);
			_mTrack.push(['processOrders']);

			console.log('marinConversionTagJS complete');
		};
		// end of functions

		this.marinSoftwarePageAddJS(marinConversionTagJSHandler);
	},

	/**
	 * Аналитика на странице подтверждения email/телефона
	 */
	enterprizeConfirmJs: function () {
		var
			enterprize = $('#enterprizeConfirmJs'),
			data = {},
			toKiss = {};
		// end of vars

		if ( !enterprize.length ) {
			return;
		}

		data = enterprize.data('value');

		// --- Kiss ---
		if (typeof _kmq !== undefined) {
			toKiss = {
				'[Ent_Req] Name': data.name,
				'[Ent_Req] Phone': data.mobile,
				'[Ent_Req] Email': data.email,
				'[Ent_Req] Token name': data.couponName,
				'[Ent_Req] Token number': data.enterprizeToken,
				'[Ent_Req] Date': data.date,// Текущая дата
				'[Ent_Req] Time': data.time,//Текущее время
				'[Ent_Req] enter_id': data.enter_id//идентификаgтор клиента в cookie сайта
			};

			_kmq.push(['record', 'Enterprize Token Request', toKiss]);
		}

		// --- GA ---
		if (typeof ga !== undefined) {
			ga('send', 'event', 'Enterprize Token Request', 'Номер фишки', data.enter_id);
		}
	},

	/**
	 * Аналитика на странице подтверждения /enterprize/complete
	 */
	enterprizeCompleteJs: function () {
		var
			enterprize = $('#enterprizeCompleteJs'),
			data = {},
			toKiss = {},
			old_identity;
		// end of vars

		if ( !enterprize.length ) {
			return;
		}

		data = enterprize.data('value');

		// --- Kiss ---
		if (typeof _kmq !== undefined) {
			toKiss = {
				'[Ent_Gr] Name': data.name,
				'[Ent_Gr] Phone': data.mobile,
				'[Ent_Gr] Email': data.email,
				'[Ent_Gr] Token name': data.couponName,
				'[Ent_Gr] Token number': data.enterprizeToken,
				'[Ent_Gr] Date': data.date,// Текущая дата
				'[Ent_Gr] Time': data.time,//Текущее время
				'[Ent_Gr] enter_id': data.enter_id//идентификатор клиента в cookie сайта
			};

			_kmq.push(['record', 'Enterprize Token Granted', toKiss]);

			// Если данные для нас новые - идентифицируем его новыми данными и мёрджим с предыдущим ID
			if (data.mobile != KM.i()) {
				old_identity = KM.i()
				_kmq.push(['identify', data.mobile]);
				_kmq.push(['set', {'enter_id': data.enter_id}]);
				_kmq.push(['set', {'user name': data.name}]);
				_kmq.push(['set', {'user email': data.email}]);
				_kmq.push(['alias', old_identity, KM.i()]);
			}
		}

		// --- GA ---
		if (typeof ga !== undefined) {
			ga('send', 'event', 'Enterprize Token Granted', 'Номер фишки', data.enter_id);
			ga('set', '&uid', data.enter_id);
		}
	},

	kissUpdateJS: function () {
		var
			kiss = $('#kissUpdateJS'),
			data = {};
		// end of vars

		if ( !kiss.length ) {
			return;
		}

		data = kiss.data('value')

		if ( undefined === data.entity_id ) {
			return;
		}

		if (typeof _kmq !== undefined) {
			_kmq.push(['alias', KM.i(), data.entity_id]);
			_kmq.push(['set', {'enter_id': data.entity_id}]);

			data.cookieName !== undefined && window.docCookies.removeItem(data.cookieName, '/');
		}
	},

	sociaPlusJs: function() {
		var _spapi = _spapi || [];
		_spapi.push(['_partner', 'enter']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') +
				'enter.api.sociaplus.com/partner.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	},

	cpaexchangeJS: function () {
		(function () {
			var
				cpaexchange = $('#cpaexchangeJS'),
				data = {},
				s, b, c;
			// end of vars

			if ( !cpaexchange.length ) {
				return;
			}

			data = cpaexchange.data('value');
			if ( !data || !$.isNumeric(data.id) ) {
				return;
			}

			s = document.createElement('script');
			b = document.getElementsByTagName('body')[0];
			c = document.createComment('HUBRUS RTB Segments Pixel V2.3');

			s.type = 'text/javascript';
			s.async = true;
			s.src = 'http://track.hubrus.com/pixel?id=' + data.id + '&type=js';
			b.appendChild(c);
			b.appendChild(s);
		})();
	},

	AdLensJS: function () {
		var
			adLens = $('#AdLensJS'),
			data = {},
			ef_event_type="transaction",
			ef_transaction_properties,
			ef_segment = "",
			ef_search_segment = "",
			ef_userid = "    ",
			ef_pixel_host="pixel.everesttech.net",
			ef_fb_is_app = 0,
			ef_allow_3rd_party_pixels = 1;
		// end of vars

		if ( !adLens.length ) {
			return;
		}

		data = adLens.data('value');

		var al = document.createElement('script'); al.type = 'text/javascript';
		al.src = 'http://www.everestjs.net/static/st.v2.js';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(al, s);

		al.onload = function() {
			if ( data.orders == undefined || data.revenue == undefined || data.margin == undefined || data.items == undefined || data.transid == undefined ) {
				return;
			}

			ef_event_type="transaction";
			ef_transaction_properties = "ev_Orders="+data.orders+"&ev_Revenue="+data.revenue+"&ev_Margin="+data.margin+"&ev_Items="+data.items+"&ev_transid="+data.transid;
			ef_segment = "";
			ef_search_segment = "";
			ef_userid="245";
			ef_pixel_host="pixel.everesttech.net";
			ef_fb_is_app = 0;
			ef_allow_3rd_party_pixels = 1;

			'function' === typeof(effp) && effp();
		}
	},

	enable : true
}

ANALYTICS.parseAllAnalDivs( $('.jsanalytics') );

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
		var addate = new Date(),
			dl = escape(document.location),
			pr1 = Math.floor(Math.random() * 1000000);
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		//AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=engb&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
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
		 if( !this.enable ) {
			 return;
		 }

// 		if( window.addEventListener ) {
// 			var nativeEL = window.addEventListener
// 			window.addEventListener = function(){
// //console.info('addEventListener WINDOW', arguments[0])
// 			  nativeEL.call(this, arguments[0], arguments[1])
// 			  if( arguments[0] === 'load' )
// 				arguments[1]()
// 			}
// 		} else if( window.attachEvent ) { //IE < 9
// 			var nativeEL = window.attachEvent
// 			window.attachEvent = function(){
// //console.info('addEventListener WINDOW', arguments[0])
// //console.info('addEventListener WINDOW', arguments[0])
// 			  //nativeEL.call(window, arguments[0], arguments[1])
// 			  if( arguments[0] === 'onload' )
// 				arguments[1]()
// 			}
// 		}        
			
		var anNode = null;
		document.writeln = function() {
			if( anNode ) {
				anNode.innerHTML += arguments[0];
			}
		}

		$.each( nodes , function() {
			anNode = this;
			if( this['id'] + '' in ADFOX ) {
				ADFOX[this['id']]();
			}
		});
		anNode = null;
		document.writeln = function(){
			$('body').append( $(arguments[0] + '') );
		}
	},

	enable : true
}

ADFOX.parseAllAdfoxDivs( $('.adfoxWrapper') );

 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function () {
	/**********************************************************************
	 Visitor split, into groups, enabling separate targeting and remarketing.
	 Groups can be compared, checking for cost-per-conversion, and remarketing effectiveness.
	 **********************************************************************/

	/**********************************************************************
	 Group settings with number prefix, e.g. GROUP_1, GROUP_2, GROUP_3
	 **********************************************************************/
	var
		GROUP_1_VENDORS = [1],
		GROUP_1_PERCENT = 33.33,   //33.33 assigns one-third visitors, 50 assigns half, 100 assigns all visitors to group, 101 is invalid

		GROUP_2_VENDORS = [2],
		GROUP_2_PERCENT = 33.33,

		GROUP_3_VENDORS = [3],
		GROUP_3_PERCENT = 33.33;

	//var GROUP_3_VENDORS = [4,5,6,7,8];
	//var GROUP_3_PERCENT = 50;

	var // CONSTANTS
		MAX_GROUPS = 3,
		MAX_VENDORS = 3;

	/**********************************************************************
	 Vendor settings with number prefix, e.g. VENDOR_1, VENDOR_2, VENDOR_3
	 **********************************************************************/
	//  VENDOR 1 - Criteo tag
	//var VENDOR_1_TAG_URL = "//r.bstatic.com/static/js/criteo_ld_min.1993.js";   //Tag URL, or function, provided by vendor
	var VENDOR_1_TAG_URL = "//static.criteo.net/js/ld/ld.js";
	var VENDOR_1_TAG_TYPE = "js";    //Only 1 of 'js, jsFunction, img, or iframe' Relevant DOM Element dynamically inserted into page

	/*
	 //  VENDOR 2 - Monetate [production] tag v6
	 var VENDOR_2_TAG_URL = "//b.monetate.net/js/1/a-f44145b4/p/www.backcountry.com/" + Math.floor((monetateT + 1118388) / 3600000) + "/g"; //  Dynamic URLs allowed
	 var VENDOR_2_TAG_TYPE = "js";
	 */

	//  VENDOR 2 - Sociomantic
	var VENDOR_2_TAG_URL = "//eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru";
	var VENDOR_2_TAG_TYPE = "js";

	//  VENDOR 3 - Google conversion tag
	//  Remove vendor if not required
	var VENDOR_3_TAG_URL = "http://www.googleadservices.com/pagead/conversion.js";
	var VENDOR_3_TAG_TYPE = "js";

	/*
	 //  VENDOR 4 - CRITEO BASKET TAG
	 var VENDOR_4_TAG_URL = "https://dis.us.criteo.com/dis/dis.aspx?p1="+escape("v=2&wi=7711869&s=0&i1=SIC0093&p1=19.95&q1=1")+"&t1=transaction&p=2168&c=2&cb="+Math.floor(Math.random()*99999999999);
	 var VENDOR_4_TAG_TYPE = "js";

	 //  VENDOR 5 - CRITEO JavaScript Function
	 var VENDOR_5_TAG_URL = "CRITEO.Load(document.location.protocol+'//dis.us.criteo.com/dis/dis.aspx?')";
	 var VENDOR_5_TAG_TYPE = "jsFunction";

	 //  VENDOR 6 - YESMAIL CART TRACKING
	 var VENDOR_6_TAG_URL = "http://link.p0.com/1x1c.dyn?p=96JT2A93";
	 var VENDOR_6_TAG_TYPE = "img";

	 //  VENDOR 7 - MERCENT tag
	 var VENDOR_7_TAG_URL = "http://cdn.mercent.com/js/tracker.js";
	 var VENDOR_7_TAG_TYPE = "js";

	 //  VENDOR 8 - DoubleClick Floodlight
	 var VENDOR_8_TAG_URL = "https://fls.doubleclick.net/activityi;src=3254838;type=visit825;cat=visit186;u=985883812;qty=1;cost=199.90;u3=NA;u2=unknown;u5=NA;u4=NA;u8=NA;u6=NA;u9=0.00;;u1=Tï¿½nis de Corrida Performance;ord=399451158";
	 var VENDOR_8_TAG_TYPE = "iframe";

	 //  VENDOR 9 - InviteMedia
	 var VENDOR_9_TAG_URL = "http://segment-pixel.invitemedia.com/pixel?pixelID=110417&partnerID=14&clientID=6125&key=segment&returnType=js";
	 var VENDOR_9_TAG_TYPE = "js";*/

	//Permanently assign visitor to group. When visitor returns, they are part of same group
	var ASSIGN_VISITOR_TO_GROUP = true;   //false would assign visitor to different group, at each page-view

	/************************ NO MODIFICATIONS BELOW LINE ************************/


	/**********************************************************************
	 CONSTANTS
	 **********************************************************************/
	//var MAX_GROUPS  = 10;  //  Limit to 10 max groups to avoid page-slow-loading
	//var MAX_VENDORS = 10;  //  Limit to 10 max vendors
	var VISITOR_ASSIGNED_TO_GROUP_FOR_DAYS = 30; //  visitor assigned to same group


	/**********************************************************************
	 Returns selected group number to tag. Possibly undefined
	 **********************************************************************/
	function getVisitorGroup() {
		var selectedGroup;
		if ( ASSIGN_VISITOR_TO_GROUP ) {  //Read from cookie
			selectedGroup = read_cookie( "visitorSplitGroup" );
		}
		if ( !selectedGroup ) {
			var marker = Math.random() * 100;
			var percentCounter = 0;
			for ( var i = 1; i <= MAX_GROUPS; i++ ) {
				var percent;
				try {
					percent = eval( "GROUP_" + i + "_PERCENT" );
				} catch ( exception ) {
					break;
				} //  Cant report error, but let's break faulty execution
				percentCounter = percentCounter + percent;
				if ( percentCounter > marker ) {          //  percentCounter crossed marker
					selectedGroup = i;
					if ( ASSIGN_VISITOR_TO_GROUP ) setVisitorGroup( selectedGroup );
					break;
				}
			}
		}
		return selectedGroup;
	}


	/**********************************************************************
	 Sets visitor to belong to specified group. Cookie stores group number
	 **********************************************************************/
	function setVisitorGroup(group) {
		// cookie domain - dynamically get top-level domain. Domain can be hardcoded too.
		var domain = window.location.host;
		var arr = domain.split( "." );
		if ( arr.length > 1 ) {
			// Use site.com instead of www.site.com, sub.site.com.  BUG: doesnt work for site.co.uk, non standard url format.
			var l = arr.length;
			if ( l > 2 ) domain = arr[l - 2] + "." + arr[l - 1];
		}
		// cookie path - set to root directory so all pages in subdirs share common site cookie
		var path = "/";
		var expireDate = (   new Date( Date.now() + VISITOR_ASSIGNED_TO_GROUP_FOR_DAYS * 24 * 60 * 60 * 1000 )  ).toUTCString();
		document.cookie = "visitorSplitGroup=" + group + "; expires=" + expireDate +
			"; domain=" + domain + "; path=" + path;
	}


	/**********************************************************************
	 read_cookie code reused
	 **********************************************************************/
	function read_cookie(key) {
		var result;
		return (result = new RegExp( '(?:^|; )' + encodeURIComponent( key ) + '=([^;]*)' ).exec( document.cookie )) ? (result[1]) : null;
	}


	/**********************************************************************
	 Inserts tag in page. Tag inserted just-before this script
	 While img and iframe tags are appended to DOM, js tags are async loaded to event 'onload'
	 **********************************************************************/
	function insertTag(type, url) {
		if ( url && type ) {
			if ( url.indexOf( "conversion.js" ) > -1 || url.indexOf( "googleadservices.com" ) > -1 ) {
				url = buildGoogleSmartPixelImgUrl();
				type = "img";
			}
			var scripts = document.getElementsByTagName( 'script' );
			var lastScript = scripts[scripts.length - 1];       //lastScript likely points to this current script
			var element;
			if ( type == 'img' || type == 'iframe' ) {
				element = document.createElement( type );
				element.src = url;
				element.width = 1;
				element.height = 1;
				lastScript.parentNode.appendChild( element );    //More reliable then document.body.appendChild()
			} else if ( type == 'js' || type == 'jsFunction' ) {
				// Add onload event for async loading
				function async_load() {
					console.log('async_load');
					element = document.createElement( 'script' );
					element.type = 'text/javascript';
					element.async = true;
					if ( type == 'js' )         element.src = url;
					else if ( type == 'jsFunction' ) element.innerHTML = url;
					lastScript.parentNode.appendChild( element );    //More reliable then document.body.appendChild()
				}

				async_load();
				//console.log('set Event');
				//window.attachEvent ? window.attachEvent( 'onload', async_load ) : window.addEventListener( 'load', async_load, false );
			}
		}
	}


	/**********************************************************************
	 Builds smart pixel url, including adding key=value pairs in query parameters. Inspects global variables for conversion id and parameters.
	 Returns empty string on error
	 **********************************************************************/
	function buildGoogleSmartPixelImgUrl() {
		var url = "";
		if ( google_conversion_id && google_conversion_label ) {
			url = "//www.googleadservices.com/pagead/conversion/" + google_conversion_id + "/?label=" + google_conversion_label + "&guid=ON&script=0";
		}
		if ( url.length > 0 && google_custom_params ) {
			var data = "";
			for ( var key in google_custom_params ) {
				if ( !google_custom_params.hasOwnProperty( key ) ) continue;
				var value = "";
				if ( (value = google_custom_params[key]) !== undefined ) {
					if ( Array.isArray( value ) ) value = value.toString().replace( /,/g, "%2C" );  // %2C is ,
					data = data + (data.length > 0 ? "%3B" : "") + key + "%3D" + encodeURIComponent( value ); // (%3B is ;) (%3D is =)
				}
			}
		}
		if ( data.length > 0 ) url = url + "&data=" + data;
		return url;
	}

	/**********************************************************************
	 Test function. All vendors are activated, and visit tagged
	 **********************************************************************
	function testInsertAllTags() {
		var
			i, url, type;

		try {
			for ( i = 1; i <= MAX_VENDORS; i++ ) {
				url = eval( "VENDOR_" + i + "_TAG_URL" );
				type = eval( "VENDOR_" + i + "_TAG_TYPE" );
				insertTag( type, url );
			}
			console.log( 'VisitorSplit testing Success!' );
		} catch ( ex ) {
			console.log( 'VisitorSplit Error, params: ', i, url, type );
			alert( ex );
		}
	}*/


	/**********************************************************************
	 main function
	 **********************************************************************/
	function main() {
		console.group('ports.js::VisitorSplit');
		var
			selectedGroup = getVisitorGroup(),
			vendors, i, url, type;

		if ( selectedGroup ) {
			console.log('selectedGroup', selectedGroup);
			try {
				vendors = eval( "GROUP_" + selectedGroup + "_VENDORS" );
				if ( vendors ) {
					console.log('vendors ', vendors, vendors.length);
					for ( i = 0; i < vendors.length; i++ ) {
						url = eval( "VENDOR_" + vendors[i] + "_TAG_URL" );
						type = eval( "VENDOR_" + vendors[i] + "_TAG_TYPE" );
						insertTag( type, url );
						console.log( 'insertTag', url, type );
					}
					console.log( 'VisitorSplit loading Success!' );
				}
				else {
					console.log( 'VisitorSplit Error: vendors is false');
				}
			} catch ( ex ) {
				console.log( 'VisitorSplit Error', ex );
				console.log( 'Params: ', i, url, type );
			}
		}
		console.groupEnd();
	}

	/**
	 * Head to head test partners
	 */
	// Очерёдность загрузки партнёров:
	// Google (before)
	// sociomantic (before)
	// Visitor Split !!! main()
	// Criteo  (after)

	if ( $('#smanticPageJS').length ) {
		window.ANALYTICS.smanticPageJS();
	}
	main(); // run Visitor Split
	if ( $('#criteoJS' ).length ) {
		window.ANALYTICS.criteoJS();
	}

	//testInsertAllTags(); // for partners pixels debug

}());
