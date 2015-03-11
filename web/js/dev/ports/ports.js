var
	body = $('body'),
	docJQ = $(document);

console.log('ports.js inited');

window.ANALYTICS = {
	
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
	
	yandexOrderComplete: function() {
        try {
            var orderData = $('#jsOrder').data('value');
            if (typeof window.yandexCounter == 'undefined' || orderData == undefined) return;
            $.each(orderData.orders, function (index, order) {
                window.yandexCounter.reachGoal('ORDERCOMPLETE', {
                    order_id: order.number,
                    order_price: order.sum,
                    currency: "RUR",
                    goods: $.map(order.products, function (product) {
                        return {
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            quantity: product.quantity
                        }
                    })
                })
            });
            console.info('yandexOrderComplete reachGoal successfully sended');
        } catch (e) {
            console.error('yandexOrderComplete error', e);
        }
    },

	LiveTexJS: function () {
		console.group('ports.js::LiveTexJS log');

		function loadLiveTex() {
			console.info('LiveTexJS init');

			var lt = document.createElement('script');
			lt.type = 'text/javascript';
			lt.async = true;
			lt.src = 'http://cs15.livetex.ru/js/client.js';
			var sc = document.getElementsByTagName('script')[0];
			if ( sc ) sc.parentNode.insertBefore(lt, sc);
			else  document.documentElement.firstChild.appendChild(lt);

			console.log('LiveTexJS end');
		}

		var
			LTData = $('#LiveTexJS').data('value');
		// end of vars

		var
			liveTexAction = function() {
				if ( !LTData ) {
					return;
				}

				console.info('liveTex action');
				console.log(LTData);

				window.liveTexID = LTData.livetexID;
				window.liveTex_object = true;

				window.LiveTex = {
					onLiveTexReady: function () {
						window.LiveTex.setName(LTData.username);
						LiveTex.on('chat_open', function(){
							if ('undefined' != typeof(_gaq)) {
								_gaq.push(['_trackEvent', 'webchat', 'chat_started']);
							}
						});
					},

					invitationShowing: true,

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
			},

			/**
			 * @param {Object}	userInfo	Данные пользователя
			 */
			liveTexUserInfo = function( userInfo ) {
				try {
					LTData.username = 'undefined' != typeof(userInfo.name) ? userInfo.name : null;
					LTData.userid = 'undefined' != typeof(userInfo.id) ? userInfo.id : null;

					liveTexAction();

				} catch ( err ) {
					ENTER.utils.logError({
						event: 'liveTex_error',
						type:'ошибка в action',
						err: err
					});
				}
			};
		// end of functions

		if (ENTER.config.userInfo === false) {
			liveTexAction();
			loadLiveTex();
		} else if (ENTER.config.userInfo) {
			// SITE-4382
			liveTexUserInfo(ENTER.config.userInfo);
			loadLiveTex();
		} else {
			$('body').on('userLogged', function(event, userInfo){
				liveTexUserInfo(userInfo);
				loadLiveTex();
			});
		}

		console.groupEnd();
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
		$('body').on('removeFromCart', remFromBasket);

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

			if ($('body').data('template') != 'order_new') window.APRT_DATA = vars;

			s.type  = 'text/javascript';
			s.src = '//aprtx.com/code/enter/';
			s.defer = true;
			x.parentNode.insertBefore(s, x);
		})();
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
		(function () {
			var s = document.createElement('script'),
				x = document.getElementsByTagName('script')[0];
			s.type = 'text/javascript';
			s.async = true;
			s.src = ('https:' == document.location.protocol ? 'https://' : 'http://')
				+ 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru' + (ENTER.config.pageConfig.isMobile ? '-m' : '');
			x.parentNode.insertBefore(s, x);
		})();
	},

	// финальная страница оформления заказа
	sociomanticOrderCompleteJS: function() {
		var basket = {products: [], transaction: '', amount: 0.0, currency: 'RUB'},
			ordersData = $('#jsOrder').data('value');

		if (!ordersData) return;

		// пройдем по заказам
		$.each(ordersData.orders, function(i,order){
			// пройдем по продуктам
			$.each(order.products, function(ii,pr) {
				basket.products.push({identifier: pr.article + '_' + docCookies.getItem('geoshop'), amount: pr.price, currency: 'RUB', quantity: pr.quantity})
			});
			// если несколько заказов, то пишем их через дефис
			basket.transaction += i == 0 ? order.numberErp : ' - ' + order.numberErp;
			// если несколько заказов, то суммируем сумму
			basket.amount += parseInt(order.sum, 10);
		});
		window.sonar_basket = basket;
//		$LAB.script('//eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru');
	},

	smanticPageJS: function() {
		(function(){
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
		})();
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
			useTchiboAnalytics = Boolean($('#gaJS').data('use-tchibo-analytics')),
		// end of vars

			gaBannerClick = function gaBannerClick( BannerId ) {
				console.log( 'GA: send', 'event', 'Internal_Promo', BannerId );
				ga( 'send', 'event', 'Internal_Promo', BannerId );
			},

			gaSubscribeClick = function gaSubscribeClick( type, email ) {
				console.log( 'GA: send', 'event', 'Subscriptions', type, email );
				ga( 'send', 'event', 'Subscriptions', type );
            },

            /**
             * Tracking event for ga.js and analytics.js
             * @param {string} category (required) The name you supply for the group of objects you want to track.
             * @param {string} action (required) A string that is uniquely paired with each category, and commonly used to define the type of user interaction for the web object.
             * @param {string} [label] (optional) An optional string to provide additional dimensions to the event data.
             * @param {int|object} [value] (optional) An integer that you can use to provide numerical data about the user event (or {'hitcallback':func} object).
             * @param {bool} [nonInteraction] (optional) A boolean that when set to true, indicates that the event hit will not be used in bounce-rate calculation.
             */
            trackEvent = function trackEventF(category, action, label, value, nonInteraction) {
                var // variables
                    w = window,
                    _gaq = w._gaq || [],
                    ga = w[w['GoogleAnalyticsObject']];

                /* Checking values */
                if (category == '' || action == '') return;
                if (typeof value == 'string') value = parseInt(value, 10);

                /* Sending */
                if (typeof _gaq === 'object') _gaq.push(['_trackEvent', category, action, label, value, nonInteraction]);
                if (typeof ga === 'function') ga('send', 'event', category, action, label, value);

                console.log('[Google trackEvent] category: %s, action: %s, label: %s, value: %s, nonInteraction: %s', category, action, label, value, nonInteraction)
            },

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
				$('.js-category-filter-brand:not(:checked)').click(function ga_filterBrand(){
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
						banner = wrapper.find('div:first'),
						id = banner.attr('id') || 'adfox';
					gaBannerClick( id );
				});
                // отслеживание кликов на блоках smartchoice
                $('.specialPriceItem').on('click', '.specialPriceItemCont_imgLink, .specialPriceItemCont_name', function (e) {
                    var $parent = $(this).closest('.specialPriceItem'),
                        article = $parent.data('article'),
                        title = $parent.find('.specialPriceItemTitle').text(),
                        url = $(this).attr('href');
                    e.preventDefault();
                    trackEvent('smartchoice', title, article, { 'hitCallback': function () { document.location = url; }});
                });
			},

			ga_search = function ga_search() {
				console.info( 'gaJS search' );
			},

			ga_product = function() {
				console.info( 'gaJS product page' );
				var
					product = $('#jsProductCard').data('value'),
                    ref = document.referrer,
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
				$( '.trustfactor-right' ).on( 'click', gaBannerClickPrepare );
				$( '.trustfactor-main' ).on( 'click', gaBannerClickPrepare );
				$( '.trustfactor-content' ).on( 'click', gaBannerClickPrepare );

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
				$('body').on('click', 'a.btnBuy__eLink', function ga_btnBuy() {
					if ( 'undefined' !== product ) {

                        /* На наборах выполняется другой трекинговый код */
                        if ($(this).hasClass('js-kitButton')) {
                            console.log('GA: send event addedCollection collection %s', product.article);
                            ga('send', 'event', 'addedCollection', 'collection', product.article);
                            return ;
                        }

						console.log('GA: btn Buy');
						if ($(this).hasClass('mShopsOnly')) {
							ga('send', 'event', 'reserve', product.name, product.article, product.price);
						}
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
				$('.bProductSectionLeftCol').delegate('.js-slider a', 'click', function() {
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

                if (/product\/tchibo/.test(document.location.href)) {
                    if (/catalog\/tchibo/.test(ref)) {
                        trackEvent('tchibo_item_visit', 'From_Tchibo', ref, null, true);
                        window.docCookies.setItem('tchibo_track', 1, 0, '/');
                    }
                    if (/catalog/.test(ref) && !/tchibo/.test(ref)) trackEvent('tchibo_item_visit', 'From_Enter', ref, null, true);
                    if (!/catalog/.test(ref)) trackEvent('tchibo_item_visit', 'Other', ref, null, true);
                    if (ref=='') trackEvent('tchibo_item_visit', 'From Ads', ref, null, true);
                }
			},

			ga_orderComplete = function ga_orderComplete() {
				var
					ecommerce = data ? data.ecommerce : false,
					addTransaction,
					items,
					send,
					count, i,
					order, j,
                    tchiboItems = [], tchiboItemsPrice;
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
                            if (/Tchibo/.test(items[i].category) || /Tchibo/.test(items[i].name)) tchiboItems.push(items[i]);
							if (items[i].rr_viewed) body.trigger('trackGoogleEvent',['RR_покупка','Купил просмотренные', items[i].rr_block]);
							if (items[i].rr_added) body.trigger('trackGoogleEvent',['RR_покупка','Купил добавленные', items[i].rr_block]);
						}
					}

					if ( send ) {
						console.log('ecommerce:send', send);
						ga('ecommerce:send', send);
					}
				}

                if (docCookies.hasItem('tchibo_track') && tchiboItems) {
                    tchiboItemsPrice = tchiboItems.reduce(function(pv,cv) { return pv + cv.price; }, 0);
                    if (tchiboItemsPrice) trackEvent('tchibo_item_purchase', 'purchase', '', tchiboItemsPrice, true);
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
			if ( 'function' !== typeof(ga) ) {
				console.error('GA: init error');
				console.groupEnd();
				return false; // метод ga не определён, ошибка, нечего анализировать, выходим
			}
			ga('create', 'UA-25485956-5', 'enter.ru');
			ga('require', 'displayfeatures');

			if ( true === useTchiboAnalytics ) {
				ga('create', 'UA-12345-6', 'auto', {'name': 'tchiboTracker'});

				if( data && 'object' === typeof(data.vars) && data.vars ) {
					console.log('TchiboGA: tchiboTracker.send pageview');
					console.log(data.vars);
					ga('tchiboTracker.send', 'pageview');
				}
			}

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
                // TODO-zra productData = data.products
                if (productData) {
                    console.log('GA: addtocart clicked', productData);
                    ga('send', 'event', '<button>', productData.name, productData.article, productData.price);
                }
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
    //SITE-3661 Удаление кода TagMan
	/*TagManJS : function() {

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
	},*/

    RetailRocketJS : function() {
    	console.groupCollapsed('ports.js::RetailRocketJS');

        rrPartnerId = "519c7f3c0d422d0fe0ee9775"; // rrPartnerId — по ТЗ должна быть глобальной
        rrApi = {};
        rrApiOnReady = [];
        rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};

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
			apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
			ref.parentNode.insertBefore( apiJs, ref );
		}( document ));

		// SITE-3672. Передаем email пользователя для RetailRocket
		(function() {
			var
				rr_data = $('#RetailRocketJS').data('value'),
				email,
				cookieName;
			// end of vars

			if ( 'object' != typeof(rr_data) || !rr_data.hasOwnProperty('emailCookieName') ) {
				return;
			}

			cookieName = rr_data.emailCookieName;

			email = window.docCookies.getItem(cookieName);
			if ( !email ) {
				return;
			}

			console.info('RetailRocketJS userEmailSend');
			console.log(email);

			rrApiOnReady.push(function () {
				rrApi.setEmail(email);
			});

			window.docCookies.removeItem(cookieName, '/');
		})();

		// Вызываем счётчик для заданных в HTML коде параметров
		(function() {
			try {
				var rr_data = $('#RetailRocketJS').data('value');

				if (rr_data && rr_data.routeName && rr_data.sendData) {
					$.each(rr_data.sendData, function(index, data) {
						ENTER.counters.callRetailRocketCounter(rr_data.routeName, data);
					});
				}
			} catch (err) {}
		})();

		console.groupEnd();
    },

//    AdmitadJS : function() {
//        window._retag = window._retag || [];
//        var ad_data = $('#AdmitadJS').data('value');
//
//        if (ad_data) {
//
//            if (ad_data.ad_data) {
//                /**
//                 * NB! Переменные потипу var ad_category должны быть глобальными согласно задаче SITE-1670
//                 */
//                if (ad_data.ad_data.ad_category) {
//                    window.ad_category = ad_data.ad_data.ad_category;
//                }
//
//                if (ad_data.ad_data.ad_product) {
//                    window.ad_product = ad_data.ad_data.ad_product;
//                }
//
//                if (ad_data.ad_data.ad_products) {
//                    window.ad_products = ad_data.ad_data.ad_products;
//                }
//
//                if (ad_data.ad_data.ad_order) {
//                    window.ad_order = ad_data.ad_data.ad_order;
//                }
//
//                if (ad_data.ad_data.ad_amount) {
//                    window.ad_amount = ad_data.ad_data.ad_amount;
//                }
//
//            }
//
//            if (ad_data.pushData) {
//                window._retag.push(ad_data.pushData);
//            }
//        }
//
//        (function(d){
//            var s=document.createElement("script");
//            s.async=true;
//            s.src=(d.location.protocol == "https:" ? "https:" : "http:") + "//cdn.admitad.com/static/js/retag.js";
//            var a=d.getElementsByTagName("script")[0];
//            a.parentNode.insertBefore(s, a);
//        }(document));
//    },

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

    adblenderCommon: function(){

        var layout = '';

        if (arguments[0].layout) layout = arguments[0].layout;

        function addAdblenderCode(scriptName) {
            var ra = document.createElement('script');
            ra.type = 'text/javascript';
            ra.async = true;
            ra.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/c/enter/' + scriptName + '.js?' + Math.random();
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ra, s);
        }

        // For all pages
        addAdblenderCode('all');

        // For order page and complete order page
        if (layout == 'layout-order') addAdblenderCode('basket');
        if (layout == 'layout-order-complete') addAdblenderCode('success');
    },

	parseAllAnalDivs : function( nodes ) {
		console.groupCollapsed('parseAllAnalDivs');

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
			};

			if ( this.id+'' in self ) {
				self[this.id]( $(this).data('vars') );
			}

			anNode.addClass('parsed')
		});

		document.writeln = function() {
			$('body').append( $(arguments[0] + '') );
		};
		console.groupEnd();
	},

	marinSoftwarePageAddJS: function( callback ) {
		console.info('marinSoftwarePageAddJS');

		var mClientId ='7saq97byg0';
		var mProto = ('https:' == document.location.protocol ? 'https://' : 'http://');
		var mHost = 'tracker.marinsm.com';
		var mt = document.createElement('script'); mt.type = 'text/javascript'; mt.async = true; mt.src = mProto + mHost + '/tracker/async/' + mClientId + '.js';
		// var fscr = document.getElementsByTagName('script')[0]; fscr.parentNode.insertBefore(mt, fscr);


		$LAB.script( mt.src ).wait(callback);
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
		if (typeof _kmq != 'undefined') {
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
		if (typeof ga != 'undefined') {
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
		if (typeof _kmq != 'undefined') {
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
		if (typeof ga != 'undefined') {
			ga('send', 'event', 'Enterprize Token Granted', 'Номер фишки', data.enter_id);
			ga('set', '&uid', data.enter_id);
		}
	},

	/**
	 * Аналитика при регистрации в EnterPrize
	 */
	enterprizeRegAnalyticsJS: function() {
		typeof _gaq !== "undefined" && _gaq.push(['_trackEvent', 'Enterprize Registration', 'true']);
		typeof ga !== "undefined" && ga('send', 'event', 'Enterprize Registration', 'true');
	},

	kissUpdateJS: function () {
		var
			kiss = $('#kissUpdateJS'),
			data = {};
		// end of vars

		if ( !kiss.length ) {
			return;
		}

		data = kiss.data('value');

		if (
			'object' != typeof(data) ||
			!data.hasOwnProperty('entity_id') ||
			!data.hasOwnProperty('cookieName') ||
			'undefined' == typeof(_kmq)
			) {
			return;
		}

		_kmq.push(['alias', KM.i(), data.entity_id]);
		_kmq.push(['set', {'enter_id': data.entity_id}]);

		window.docCookies.removeItem(data.cookieName, '/');
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

	LamodaJS: function () {
		(function() {
			var
				lamoda = $('#LamodaJS'),
				data = lamoda.data('value');
			// end of vars

			if ( 'undefined' == typeof(data) || !data.hasOwnProperty('lamodaID') ) {
				return;
			}

			console.log('LamodaJS');

			window.JSREObject = window.JSREObject || function() { window.JSREObject.q.push(arguments) };
			window.JSREObject.q = window.JSREObject.q || [];
			window.JSREObject.l = +new Date;
			JSREObject('create', data.lamodaID, 'r24-tech.com');
			$.getScript("//jsre.r24-tech.com/static/dsp/min/js/jsre-min.js");
		})();
	},

	LamodaCategoryJS: function () {
		(function() {
			var
				lamoda = $('#LamodaCategoryJS'),
				data = lamoda.data('value');
			// end of vars

			if ( 'undefined' == typeof(data) || !data.hasOwnProperty('id') || 'undefined' == typeof(JSREObject) ) {
				return;
			}

			console.info('LamodaCategoryJS');
			console.log('category_id=' + data.id);
			JSREObject('pageview_catalog', 'category', data.id);
		})();
	},

	LamodaSearchJS: function () {
		(function() {
			var
				lamoda = $('#LamodaSearchJS'),
				data = lamoda.data('value');
			// end of vars

			if ( 'undefined' == typeof(data) || !data.hasOwnProperty('query') || 'undefined' == typeof(JSREObject) ) {
				return;
			}

			console.info('LamodaSearchJS');
			console.log('search_query=' + data.query);
			JSREObject('pageview_catalog', 'category', data.query);
		})();
	},

	LamodaProductJS: function () {
		(function() {
			var
				lamoda = $('#LamodaProductJS'),
				data = lamoda.data('value');
			// end of vars

			if ( 'undefined' == typeof(data) || !data.hasOwnProperty('id') || 'undefined' == typeof(JSREObject) ) {
				return;
			}

			console.info('LamodaProductJS');
			console.log('product_id=' + data.id);
			JSREObject('pageview_product', data.id);
		})();
	},

	LamodaOtherPageJS: function () {
		(function() {
			if ( 'undefined' == typeof(JSREObject) ) return;

			console.log('LamodaOtherPageJS');
			JSREObject('pageview');
		})();
	},

	LamodaCompleteJS: function () {
		(function() {
			if ( 'undefined' == typeof(JSREObject) ) return;

			console.log('LamodaCompleteJS');
			JSREObject('cart_checkout');
			JSREObject('conversion');
		})();
	},

	googleTagManagerJS: function () {
		var
			manager = $('#googleTagManagerJS'),
			data = manager.data('value');
		// end of vars

		console.groupCollapsed('ports.js::googleTagManagerJS');

		if ("undefined" == typeof(data) || !data.hasOwnProperty('containerId')) {
			console.warn('Не переданы данные для googleTagManager (containerId, ...) ');
			return;
		}

		(function(w,d,s,l,i){
			w[l]=w[l]||[];
			w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
			var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
			j.async=true;
			j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;
			f.parentNode.insertBefore(j,f);

			console.log('googleTagManagerJS init');
			console.groupEnd();
		})(window,document,'script','dataLayer', data.containerId);
	},

	flocktoryExchangeJS: function () {
		var
			flocktoryExchange = $('#flocktoryExchangeJS'),
			data = flocktoryExchange.data('value'),
			_flocktory = window._flocktory || [];
		// end of vars

		if ( !flocktoryExchange.length || 'undefined' == typeof(data) ) {
			return;
		}

		console.info('flocktoryExchange');
		console.log(['exchange', data]);

		_flocktory.push(['exchange', data]);
//		(function() {
//			var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
//			s.src = "//api.flocktory.com/1/hello.js";
//			var l = document.getElementsByTagName('script')[0]; l.parentNode.insertBefore(s, l);
//		})();
	},

	flocktoryEnterprizeJS: function() {
		console.groupCollapsed('ports.js::flocktoryEnterprizeJS');

//		this.flocktoryAddScript();

		var
			data = {
				name: "",
				email: "",
				sex: "",
				action: "precheckout",
				spot: "popup_enterprize"
			},
			flocktoryEnterprize = {
				/**
				 * подставляем данные с get-параметров
				 */
				fillDataFromParams: function() {
					var urlParams = getUrlParams();

					if ( typeof urlParams === "undefined" ) {
						return;
					}

					if ( typeof urlParams['name'] !== "undefined" )		data.name = urlParams['name'];
					if ( typeof urlParams['email'] !== "undefined" )	data.email = urlParams['email'];
					if ( typeof urlParams['sex'] !== "undefined" )		data.sex = urlParams['sex'];

					return;
				},

				init: function() {
					var needUserInfoData = false;

					flocktoryEnterprize.fillDataFromParams();

					if ( data.name == "" || data.email == "" || data.sex == "" ) {
						needUserInfoData = true;
					}

					if ( ENTER.config.userInfo === false || needUserInfoData === false ) {
						flocktoryEnterprize.action();
					}
					else if ( !ENTER.config.userInfo ) {
						$("body").on("userLogged", function() {flocktoryEnterprize.action(ENTER.config.userInfo)} );
					}
					else {
						// событие уже прошло
						console.warn(ENTER.config.userInfo);
						flocktoryEnterprize.action(ENTER.config.userInfo);
					}
				},

				action: function(userInfo) {
					if ( userInfo && userInfo.id ) {
						if ( data.name == "" )	data.name = userInfo.name;
						if ( data.email == "" )	data.email = userInfo.email;
						if ( data.sex == "" )	data.sex = (1 == userInfo.sex) ? "m" : ( 2 == userInfo.sex ? "f" : "" );
					}

					// первый блок
					$('<div/>', {
						"class": "i-flocktory",
						"data-fl-user-name": data.name,
						"data-fl-user-email": data.email,
						"data-fl-user-sex": data.sex
					}).appendTo('#flocktoryEnterprizeJS');

					// второй блок
					$('<div/>', {
						"class": "i-flocktory",
						"data-fl-action": data.action,
						"data-fl-spot": data.spot
					}).appendTo('#flocktoryEnterprizeJS');
				}
			};
		// end of vars

		var
			/**
			 * Получение get параметров текущей страницы
			 */
			getUrlParams = function () {
				var $_GET = {},
					__GET = window.location.search.substring(1).split('&'),
					getVar,
					i;
				// end of vars

				for ( i = 0; i < __GET.length; i++ ) {
					getVar = __GET[i].split('=');
					$_GET[getVar[0]] = typeof(getVar[1]) == 'undefined' ? '' : getVar[1];
				}

				return $_GET;
			};
		// end of functions

		flocktoryEnterprize.init();

		console.groupEnd();
	},

	flocktoryEnterprizeFormJS: function() {
//		this.flocktoryAddScript();

		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.async = true;
		s.src = "//api.flocktory.com/v2/loader.js?site_id=1401";
		var l = document.getElementsByTagName('script')[0];
		l.parentNode.insertBefore(s, l);
	},

	flocktoryEnterprizeRegJS: function() {
		var
			data = $("#flocktoryEnterprizeRegJS").data('value');
		// end of vars

		var
			action = function( userInfo ) {
				var result;

				if (
					!data ||
					!userInfo ||
					!userInfo.hasOwnProperty('email') || !userInfo.email ||
					!userInfo.hasOwnProperty('name') || !userInfo.name
				) {
					return;
				}

				data.user.email = userInfo.email;
				data.user.name = userInfo.name;

				if ( userInfo.hasOwnProperty('sex') ) {
					data.user.sex = 1 == userInfo.sex ? 'm' : (2 == userInfo.sex ? 'f' : null);
				}

				result = ['postcheckout', data];

				console.info("Analytics flocktoryEnterprizeReg");
				console.log(result);

				window.flocktory.push(result);
			};
		// end of functions

		if ( !data ) {
			return;
		}

		window.flocktory = window.flocktory || [];

		if ( ENTER.config.userInfo === false ) {
			// пользователь должен быть авторизован
			return;
		}
		else if ( !ENTER.config.userInfo ) {
			$("body").on("userLogged", function() {action(ENTER.config.userInfo)} );
		}
		else {
			action(ENTER.config.userInfo);
		}
	},

	insiderJS: function(){

		var InsiderProduct, fillProducts, products = [];

		InsiderProduct = function (data) {
			this.category = [];
			if (data.category && $.isArray(data.category)) this.category = data.category;
			if (data.name) this.name = data.name;
			if (data.img) this.img = data.img;
			if (data.link) this.url = data.link;
			if (data.price) this.price = '' + data.price;
			return this;
		};

		fillProducts = function(data) {
			$.each(data, function(i,val){
				products.push(new InsiderProduct(val))
			});
			window.spApiPaidProducts = products;
		};

		if (ENTER.UserModel && ENTER.UserModel.cart()) fillProducts(ENTER.UserModel.cart());

		body.on('userLogged', function(e,data){
			if (data && data.cartProducts && $.isArray(data.cartProducts)) {
				products = [];
				fillProducts(data.cartProducts);
			}
		});

		body.on('addtocart', function(e,data){
			if (data.product) {
				window.spApiPaidProducts = window.spApiPaidProducts || [];
				data.product.category = null; // TODO временно, пока не отдаются категории в едином виде
				window.spApiPaidProducts.push(new InsiderProduct(data.product));
			}
		})
	},

	GetIntentJS: function(){
		var data = $('#GetIntentJS').data('value');

		ENTER.counters.callGetIntentCounter({
			type: "VIEW",
			productId: data.productId,
			productPrice: data.productPrice,
			categoryId: data.categoryId
		});

		if (data.orders) {
			$.each(data.orders, function(index, order) {
				ENTER.counters.callGetIntentCounter({
					type: "CONVERSION",
					orderId: order.id,
					orderProducts: order.products,
					orderRevenue: order.revenue
				});
			});
		}
	},

	hubrusJS: function() {
		var productData = $('.hubrusProductData').data('value'),
			hubrusDataDiv = $('.hubrusData'),
			hubrusProperty = hubrusDataDiv.data('property'),
			hubrusValue = hubrusDataDiv.data('value'),
			lsCacheKey = 'hubrus_viewed_items',
			viewedItems, hubrusVars = {};

		// Если есть данные по продукту на странице (пользователь открыл страницу продукта)
		if (productData) {
			viewedItems = lscache.get(lsCacheKey) ? lscache.get(lsCacheKey) : [];
			// проверка на уникальность
			if ($.grep(viewedItems, function(p){ return productData.id == p.id }).length == 0) viewedItems.unshift(productData);
			hubrusVars.viewed_items = viewedItems.splice(0,10);
			lscache.set(lsCacheKey, hubrusVars.viewed_items);
		}

		if (hubrusProperty && hubrusValue) {
			hubrusVars[hubrusProperty] = hubrusValue
		}

		/** Событие добавления в корзину */
		body.on('addtocart removeFromCart', function hubrusAddToCart(event) {
			var	smpix = window.smartPixel1,
				type = event.type;

			if (!smpix || typeof smpix['trackState'] !== 'function') return;

			smpix.trackState(type == 'addtocart' ? 'add_to_cart' : 'remove_from_cart',
				{ cart_items: $.map(ENTER.UserModel.cart(), function(e){
					return {
						id: e.id,
						price: e.price,
						category: e.rootCategory ? e.rootCategory.id : 0
					}
				})
			});
		});

		body.on('click', '.jsOneClickButton-new', function(){
			var smpix = window.smartPixel1,
				product = $('#jsProductCard').data('value'),
				categoryId = 0;

			if (!smpix || typeof smpix['trackState'] !== 'function' || !product) return;
			if ($.isArray(product.category) && product.category.length > 0) categoryId = product.category[product.category.length - 1].id;

			smpix.trackState('oneclick',
				{ oneclick_item: [{
					id: product.id,
					price: product.price,
					category: categoryId
				}]
				});

		});

		window.smCustomVars = hubrusVars;

		$LAB.script('http://pixel.hubrus.com/containers/enter/dist/smartPixel.min.js');
	},

	enable : true
};

$(function(){
	ANALYTICS.parseAllAnalDivs( $('.jsanalytics') );
});


