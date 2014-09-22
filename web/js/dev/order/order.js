;(function( global ){

	/**
	 * Новая аналитика для оформления заказа
	 */
	var newOrderAnalytics = function newOrderAnalytics() {
		var
			data,
			orderData,
			isUsedCartRecommendation,

			toKISS_orderInfo = {},
			toKISS_productInfo = {},

			i, j,

			sociomanticUrl = ( 'https:' === document.location.protocol ? 'https://' : 'http://' )+'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
		// end of vars

		if ( !$('#jsOrder').length ) return;

		data = $('#jsOrder').data('value');
		if ( !data || !data.hasOwnProperty('orders') || !data.hasOwnProperty('isUsedCartRecommendation') ) {
			return;
		}

		orderData = data.orders;
		isUsedCartRecommendation = data.isUsedCartRecommendation;

		console.info('newOrderAnalytics');
		console.log(orderData);

		/**
		 * Sociomantic
		 *
		 * https://jira.enter.ru/browse/SITE-1475
		 */
		global.sonar_basket = {
			products: [],
			transaction: orderData[0].number,
			amount: 0,
			currency:'RUB'
		};

		for ( i = orderData.length - 1; i >= 0; i-- ) {
			for ( j = orderData[i].products.length - 1; j >= 0; j-- ) {
				global.sonar_basket.products.push({
					identifier: orderData[i].products[j].article + '_' + window.docCookies.getItem('geoshop'),
					amount: parseInt(orderData[i].products[j].price, 10),
					currency: 'RUB',
					quantity: orderData[i].products[j].quantity
				});

				global.sonar_basket.amount += parseInt(orderData[i].products[j].price, 10);
			}
		}

		$LAB.script( sociomanticUrl );


		/**
		 * KISS
		 *
		 * https://wiki.enter.ru/display/PRODUCT/KISSmetrics+tracking
		 */
		for ( i = orderData.length - 1; i >= 0; i-- ) {
			toKISS_orderInfo = {
				'Checkout Complete Order ID': orderData[i].number,
				'Checkout Complete Order ERP ID': orderData[i].numberErp,
				'Checkout Complete SKU Quantity': orderData[i].products.length,
				'Checkout Complete SKU Total': orderData[i].sum,
				'Checkout Complete Delivery Total': orderData[i].delivery[0].price,
				'Checkout Complete Order Total': orderData[i].sum,
				'Checkout Complete Order Type': 'cart order',
				'Checkout Complete Delivery': orderData[i].delivery[0].typeId,
				'Checkout Complete Payment': orderData[i].paymentMethod.id
			};

			if ( orderData[i].coupon_number ) {
				console.log('Checkout Complete Coupon', orderData[i].coupon_number);
				toKISS_orderInfo['Checkout Complete Coupon'] = orderData[i].coupon_number;
			}

			for ( j = orderData[i].products.length - 1; j >= 0; j-- ) {
				if ( (typeof _kmq === 'undefined') || (typeof KM === 'undefined') ) {
					continue;
				}

				toKISS_productInfo = {};

				toKISS_productInfo =  {
					'Checkout Complete SKU': orderData[i].products[j].article,
					'Checkout Complete SKU Quantity': orderData[i].products[j].quantity,
					'Checkout Complete SKU Price': parseInt(orderData[i].products[j].price, 10),
					'Checkout Complete Parent category': orderData[i].products[j].category[0].name,
					'Checkout Complete Category name': orderData[i].products[j].category[orderData[i].products[j].category.length - 1].name,
					'_t':KM.ts() + j + i,
					'_d':1
				};

				console.log(toKISS_productInfo);

				_kmq.push(['set', toKISS_productInfo]);
			}

			console.log(toKISS_orderInfo);

			if ( (typeof _kmq !== 'undefined') && (KM !== 'undefined') ) {
				_kmq.push(['alias', orderData[0].phonenumber, KM.i()]);
				// _kmq.push(['alias', emailVal, KM.i()]);
				_kmq.push(['identify', orderData[0].phonenumber]);
				_kmq.push(['record', 'Checkout Complete', toKISS_orderInfo]);
			}
		}

		if ( 'undefined' !== typeof gaq ) {
			/**
			 * Отслеживание рекомендаций
			 */
			console.info('Отслеживание рекомендаций');
			_gaq.push(['_setCustomVar', 5, 'Used_cart_rec', (isUsedCartRecommendation ? 'YES' : 'NO'), 2]);

			/**
			 * Отслеживание кода купона
			 */
			if ( orderData[0].coupon_number ) {
				console.info( 'Отслеживание кода купона: ' + orderData[0].coupon_number );
				_gaq.push( ['_trackEvent', 'coupon', orderData[0].coupon_number] );
			}
		}
	};

	$(document).ready(function () {
		if ( $('.socnet-ico-list-link').length ) {
			$('.socnet-ico-list-link').bind('click', function() {
				var type = $(this).data('type');

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'SMM', 'Complete order', type]);
				}
			});
		}

		/* sertificate */
		if ( $('.orderFinal__certificate').length ) {
			var code = $(".cardNumber"),
				pin = $(".cardPin"),
				form = $(".orderFinal__certificate form"),
				button = $('#sendCard'),

				urlCheck = '/certificate-check',
				urlActivate = '/certificate-activate';
			// end of vars

			var SertificateCard = (function() {

				var paymentWithCard = $('#paymentWithCard').text() * 1,
					checked = false,
					processTmpl = 'processBlock';
				// end of vars

				function setPaymentSum( delta ) {
					if( delta > paymentWithCard ) {
						paymentWithCard = 0;
					}
					else {
						paymentWithCard -= delta;
					}

					$('#paymentWithCard').text( paymentWithCard );
				}

				function prepareNewCard() {
					code.val('');
					pin.val('');
					button.addClass('mDisabled');
					checked = false;
				}

				function getCode() {
					return code.val().replace(/[^0-9]/g,'');
				}

				function getPIN() {
					return pin.val().replace(/[^0-9]/g,'');
				}

				function getParams() {
					return {
						code: getCode(),
						pin: getPIN()
					};
				}

				function activateButton() {
					// console.info('activateButton', getCode(), getPIN())

					if ( checked && ( getCode() !== '' ) && getCode().length === 14 && ( getPIN() !== '' ) && getPIN().length === 4 ) {
						button.removeClass('mDisabled');
					}
				}

				function checkForStars( v ) {
					if ( v.match(/\*/) ) {
						button.addClass('mDisabled');
					}
				}

				function checkCard() {
					setProcessingStatus( 'orange', 'Проверка по номеру карты' );

					$.post( urlCheck, { code: '23846829634' }, function( data ) {
						if ( ! 'success' in data ) {
							return false;
						}

						if( !data.success ) {
							var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR';

							setProcessingStatus( 'red', err );

							return false;
						}

						setProcessingStatus( 'green', data.data );
					});

					activateButton();
					pin.focus();
				}

				function setProcessingStatus( status, data ) {
					var blockProcess = $('.process').first();

					if ( !blockProcess.hasClass('picked') ) {
						blockProcess.remove();
					}

					var options = {
						typeNum: status
					};

					switch ( status ) {
						case 'orange':
							options.text = data;
							checked = false;
							break;
						case 'red':
							options.text = 'Произошла ошибка: ' + data;
							checked = false;
							break;
						case 'green':
							if( 'activated' in data ) {
								options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!';
							}
							else {
								options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum;
							}

							checked = true;
							break;
					}

					form.after( tmpl( processTmpl, options) );

					if ( typeof( data['activated'] ) !== 'undefined' ) {
						$('.process').first().addClass('picked');
					}
					activateButton();
				}

				return {
					activateButton: activateButton,
					checkCard: checkCard,
					setProcessingStatus: setProcessingStatus,
					setPaymentSum: setPaymentSum,
					prepareNewCard: prepareNewCard,
					getParams: getParams,
					checkForStars: checkForStars
				};
			})(); // object SertificateCard , singleton

			$.mask.definitions['n'] = '[0-9]';
			code.mask("nnn nnn nnn nnnn n", { completed: SertificateCard.checkCard, placeholder: "*" } );
			pin.mask("nnnn", { completed: SertificateCard.activateButton, placeholder: "*" } );
			code.bind('keyup', function() {
				SertificateCard.checkForStars( $(this).val() );
			});
			pin.bind('keyup', function() {
				SertificateCard.checkForStars( $(this).val() );
			});

			button.bind('click', function(e) {
				e.preventDefault();

				if( $(this).hasClass('mDisabled') ) {
					return false;
				}

				SertificateCard.setProcessingStatus( 'orange', 'Минутку, активация карты...' );

				$.get( urlActivate, SertificateCard.getParams(), function( data ) {
					if( ! 'success' in data ) {
						return false;
					}

					if( !data.success ) {
						SertificateCard.setProcessingStatus( 'red', data.error );

						return false;
					}

					data.data.activated = true;
					SertificateCard.setProcessingStatus( 'green', data.data );
					SertificateCard.setPaymentSum( data.data.sum * 1 );
					SertificateCard.prepareNewCard();
				});

				return false;
			});

			// $.mockjax({
			//   url: '/certificate-check',
			//   responseTime: 1000,
			//   responseText: {
			//     success: true,
			//     data: { sum: 1000, code: '3432432' }
			//   }
			// })
		}

		/* */

		$('.auth-link').bind('click', function (e) {
			e.preventDefault();

			var link = $(this);

			$('#login-form, #register-form').data('redirect', false);
			$('#auth-block').lightbox_me({
				centered:true,
				onLoad:function () {
					$('#auth-block').find('input:first').focus();
				},
				onClose:function () {
					$.get(link.data('updateUrl'), function (response) {
						if (true === response.success) {
							var form = $('.order-form');

							$('#user-block').replaceWith(response.data.content);

							$.each(response.data.fields, function (name, value) {
								var field = form.find('[name="' + name + '"]');

								if (field.val().length < 2) {
									field.val(value);
								}
							});
						}
					});
				}
			});
		});

		(function () {
			if ( !$('#product_errors').length ) {
				return;
			}
			// var dfd = $.Deferred()
			var orderErrPopup = function( txt, delUrl, addUrl ) {
				var id = 'tmpErrPopup'+Math.floor(Math.random() * 22),
					block = '<div id="'+id+'" class="popup">' +
								'<div class="popupbox width290">' +
									'<div class="font18 pb18"> Непредвиденная ошибка</div>'+
								'</div>' +
								'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>'+
							'</div> ';
				// end of vars

				$('body').append( $(block) );
				$.each(txt, function(i, item) {
					$('#'+id).find('.popupbox').append('<div class="font18 pb18"> ' +item+ '</div>');
				});

				$('#'+id).lightbox_me({
				  centered: true,
				  closeSelector: ".closePopup",
				  onClose: function(){
						var sendData = function(item, i) {
							if ( item[i] ) {
								var url = item[i]+'';

								$.ajax({
									url: url
								}).then(function( res ) {
									i++;
									sendData(item, i);
								});
							}
							else {
								window.location.reload();
							}

						};

						$.merge(delUrl, addUrl);
						sendData(delUrl, 0);
					}
				});
			};

			var txt = [],
				delUrl = [],
				addUrl = [],
				length = $('#product_errors').data('value').length;
			// end of vars

			if ( length ) {
				var checkItemQuantity = function() {
					$.each($('#product_errors').data('value'), function(i, item) {
						if ( item.product.deleteUrl ) {
							delUrl.push(item.product.deleteUrl);
						}

						if ( item.product.addUrl ) {
							addUrl.push(item.product.addUrl);
						}

						if ( 708 == item.code ) {
							if (item.quantity_available > 0) {
								if ( typeof(_gaq) !== 'undefined' ) {
									_gaq.push(['_trackEvent', 'Errors', 'User error', 'Нет нужного количества товаров']);
								}

								txt.push('Вы заказали товар '+item.product.name+' в количестве '+item.product.quantity+' шт. <br/ >Доступно только '+item.quantity_available+' шт.<br/ >Будет заказано '+item.quantity_available+'шт');
								// delUrl.push(item.product.deleteUrl)
								addUrl.push(item.product.addUrl);

							}
							else {
								if ( typeof(_gaq) !== 'undefined' ) {
									_gaq.push(['_trackEvent', 'Errors', 'User error', 'Нет товара для выбранного способа доставки']);
								}

								txt.push('Товара ' + item.product.name + ' нет в наличии для выбранного способа доставки.<br/>Товар будет удален из корзины.');
								// delUrl.push(item.product.deleteUrl)
							}
						}
						else {
							if (typeof(_gaq) !== 'undefined') {
								_gaq.push(['_trackEvent', 'Errors', 'User error', 'Товар недоступен для продажи']);
							}

							txt.push('Товар ' + item.product.name + ' недоступен для продажи.<br/>Товар будет удален из корзины.');
							// delUrl.push(item.product.deleteUrl)
						}
					});

					orderErrPopup(txt, delUrl, addUrl);
				};

				checkItemQuantity();
			}

		 }());


		;(function () {
			var j_count = $('.timer');

			if ( !j_count.length ) {
				return false;
			}

			var clearPaymentUrl = function(form) {
				$.ajax({
					type: 'POST',
					url: form.data('clear-payment-url'),
					async: false,
					data: {},
					success: function() {}
				});
			};

			var sec5run = function() {
				if (secs === 1) {
					clearInterval(interval);
					if ( $('form.paymentUrl').length ) {
						clearPaymentUrl($('form.paymentUrl'));
						window.location = $('form.paymentUrl').attr('action');
					}
					else {
						$('.form').submit();
					}
				}

				secs -= 1;
				j_count.html(secs);
			};

			var interval = window.setInterval(sec5run, 1000);
			var secs = j_count.html().replace(/\D/g, '') * 1;
		})();

		/* Credit Widget */
		;(function () {
			var
				creditWidget,
				vkredit,
				creditBtn = $('.jsCreditBtn');
			// end of vars

			//window.onbeforeunload = function (){ return false }    // DEBUG
			if ( ! $('#credit-widget').length ) {
				console.warn('кредитный виджет не найден');

				return;
			}

			creditWidget = $('#credit-widget').data('value');

			if ( ! 'widget' in creditWidget ) {
				console.warn('тип виджета не найден в данных');
				console.log(creditWidget);

				return;
			}

			console.info('обрабатываем как '+creditWidget.widget);

			var isScriptsLoaded = false;
			if ( creditWidget.widget == 'direct-credit' ) {
				var products = [];
				$.each(creditWidget.vars.items, function(index, elem){
					products.push({
						id: elem.articul,
						name: elem.name,
						price: elem.price,
						type: elem.type,
						count: elem.quantity
					})
				});

				function openDirectCreditWidget() {
					// Иначе иногда при обновлении страницы она прокручивается вниз
					location.hash = '#/';
					location.hash = '#';

					DCLoans(creditWidget.vars.partnerID, 'getCredit', { products: products, order: creditWidget.vars.number, codeTT: creditWidget.vars.region }, function(result){
						console.log(result);
					}, false);
				}

				$LAB.script( '///api.direct-credit.ru/JsHttpRequest.js' )
				.script( '//api.direct-credit.ru/dc.js' )
				.wait( function() {
					console.info('скрипты загружены для кредитного виджета. начинаем обработку');
					isScriptsLoaded = true;
					openDirectCreditWidget();
				});
			} else if ( creditWidget.widget == 'kupivkredit' ) {
				//console.info('kupivkredit')

				$LAB.script('https://www.kupivkredit.ru/widget/vkredit.js')
				.wait( function() {
					isScriptsLoaded = true;
					vkredit = new VkreditWidget(1, creditWidget.vars.sum,  {
						order: creditWidget.vars.order,
						sig: creditWidget.vars.sig,
						callbackUrl: window.location.href,
						onClose: function(decision) {},
						onDecision: function(decision) {
							//console.info( 'Пришел статус: ' + decision )
						}
					});

					vkredit.openWidget();
				});
			}

			if ( creditBtn.length ) {
				$('body').on('click', '.jsCreditBtn', function( e ) {
					e.preventDefault();
					if (!isScriptsLoaded) {
						return;
					}

					if ( creditWidget.widget == 'direct-credit' ) {
						if ( !$('#dc_frame_block').length ) {
							openDirectCreditWidget();
						}
					} else if ( creditWidget.widget == 'kupivkredit' ) {
						vkredit.openWidget();
					}
				});
			}
		})();


		/* order final analytics*/
		newOrderAnalytics();

		if ( ($('body').attr('data-template') === 'order_complete') && (typeof orderAnalyticsRun !== 'undefined') ) {
			console.info('запуск старой аналитики зашитой в шаблоне php');
			orderAnalyticsRun();
		}
		console.log('аналитика завершена');
	});

}(this));
