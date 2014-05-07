;(function (window, document, $, ENTER) {
	
	/**
	 * Общие настройки AJAX
	 *
	 * @requires	jQuery, ENTER.utils.logError
	 */
	$.ajaxSetup({
		timeout: 10000,
		statusCode: {
			404: function() { 
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '404 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				ENTER.utils.logError(data);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '404 ошибка, страница не найдена']);
				}
			},
			401: function() {
				if ( $('#auth-block').length ) {
					$('#auth-block').lightbox_me({
						centered: true,
						onLoad: function() {
							$('#auth-block').find('input:first').focus();
						}
					});
				}
				else {
					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '401 ошибка, авторизуйтесь заново']);
					}
				}
					
			},
			500: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '500 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				ENTER.utils.logError(data);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '500 сервер перегружен']);
				}
			},
			503: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '503 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				ENTER.utils.logError(data);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '503 ошибка, сервер перегружен']);
				}
			},
			504: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '504 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				ENTER.utils.logError(data);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '504 ошибка, проверьте соединение с интернетом']);
				}
			}
		},
		error: function ( jqXHR, textStatus, errorThrown ) {
			var ajaxUrl = this.url,
				data = {
					event: 'ajax_error',
					type: 'неизвестная ajax ошибка',
					ajaxUrl: ajaxUrl
				};
			// end of vars
			
			if ( jqXHR.statusText === 'error' ) {
				ENTER.utils.logError(data);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', 'неизвестная ajax ошибка']);
				}
			}
			else if ( textStatus === 'timeout' ) {
				return;
			}
		}
	});
}(this, this.document, this.jQuery, this.ENTER));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Обработчик для личного кабинета
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var checkedSms = false;
  var checkedEmail = false;

  var handleSubscribeSms = function() {
    if ( checkedSms ) {
      $('#mobilePhoneWrapper').hide();
      $('#mobilePhoneWrapper').parent().find('.red').html('');
      checkedSms = false;
    } else {
      $('#mobilePhoneWrapper').show();
      checkedSms = true;
    }
  };

  var handleSubscribeEmail = function() {
    if ( checkedEmail ) {
      $('#emailWrapper').hide();
      $('#emailWrapper').parent().find('.red').html('');
      checkedEmail = false;
    } else {
      $('#emailWrapper').show();
      checkedEmail = true;
    }
  };

  $(document).ready(function(){
    checkedSms = $('.smsCheckbox').hasClass('checked');
    if ( !$('#user_mobile_phone').val() ) {
      $('.smsCheckbox').bind('click', handleSubscribeSms);
    }
    checkedEmail = $('.emailCheckbox').hasClass('checked');
    if ( !$('#user_email').val() ) {
      $('.emailCheckbox').bind('click', handleSubscribeEmail);
    }
  });
}());



 
 
/** 
 * NEW FILE!!! 
 */
 
 
// (function(){
//   $(function(){
//     if($('.bCtg__eMore').length) {
//       var expanded = false;
//       $('.bCtg__eMore').click(function(){
//         if(expanded) {
//           $(this).siblings('.more_item').hide();
//           $(this).find('a').html('еще...');
//         } else {
//           $(this).siblings('.more_item').show();
//           $(this).find('a').html('скрыть');
//         }
//         expanded = !expanded;
//         return false;
//       });
//     }

//     /* Cards Carousel  */
//     function cardsCarouselTag ( nodes, noajax ) {
//       var current = 1;

//       var wi  = nodes.width*1;
//       var viswi = nodes.viswidth*1;

//       if( !isNaN($(nodes.times).html()) )
//         var max = $(nodes.times).html() * 1;
//       else
//         var max = Math.ceil(wi / viswi);

//       if((noajax !== undefined) && (noajax === true)) {
//         var buffer = 100;
//       } else {
//         var buffer = 2;
//       }

//       var ajaxflag = false;


//       var notify = function() {
//         $(nodes.crnt).html( current );
//         if(refresh_max_page) {
//           $(nodes.times).html( max );
//         }
//         if ( current == 1 )
//           $(nodes.prev).addClass('disabled');
//         else
//           $(nodes.prev).removeClass('disabled');
//         if ( current == max )
//           $(nodes.next).addClass('disabled');
//         else
//           $(nodes.next).removeClass('disabled');
//       }

//       var shiftme = function() {  
//         var boxes = $(nodes.wrap).find('.goodsbox')
//         $(boxes).hide()
//         var le = boxes.length
//         for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
//           boxes.eq( j ).show()
//         }
//       }

//       $(nodes.next).bind('click', function() {
//         if( current < max && !ajaxflag ) {
//           if( current + 1 == max ) { //the last pull is loaded , so special shift

//             var boxes = $(nodes.wrap).find('.goodsbox')
//             $(boxes).hide()
//             var le = boxes.length
//             var rest = ( wi % viswi ) ?  wi % viswi  : viswi
//             for(var j = 1; j <= rest; j++)
//               boxes.eq( le - j ).show()
//             current++
//           } else {
//             if( current + 1 >= buffer ) { // we have to get new pull from server

//               $(nodes.next).css('opacity','0.4') // addClass dont work ((
//               ajaxflag = true
//               var getData = []
//               if( $('form.product_filter-block').length )
//                 getData = $('form.product_filter-block').serializeArray()
//               getData.push( {name: 'page', value: buffer+1 } )  
//               $.get( $(nodes.prev).attr('data-url') , getData, function(data) {
//                 buffer++
//                 $(nodes.next).css('opacity','1')
//                 ajaxflag = false
//                 var tr = $('<div>')
//                 $(tr).html( data )
//                 $(tr).find('.goodsbox').css('display','none')
//                 $(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
//                 tr = null
//               })
//               current++
//               shiftme()
//             } else { // we have new portion as already loaded one     
//               current++
//               shiftme() // TODO repair
//             }
//           }
//           notify()
//         }
//         return false
//       })

//       $(nodes.prev).click( function() {
//         if( current > 1 ) {
//           current--
//           shiftme()
//           notify()
//         }
//         return false
//       })

//       var refresh_max_page = false
//     } // cardsCarousel object

//     $('.carouseltitle').each( function(){
//       if($(this).find('.jshm').html()) {
//         var width = $(this).find('.jshm').html().replace(/\D/g,'');
//       } else {
//         var width = 3;
//       }
//       cardsCarouselTag({
//         'prev'  : $(this).find('.back'),
//         'next'  : $(this).find('.forvard'),
//         'crnt'  : $(this).find('.none'),
//         'times' : $(this).find('span:eq(1)'),
//         'width' : width,
//         'wrap'  : $(this).find('~ .carousel').first(),
//         'viswidth' : 3
//       });
//     })
//   });
// })();

 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * 
 * @requires	jQuery, ENTER.utils.BlackBox
 */
;(function( ENTER ) {
	var
		body = $('body'),
		clientCart = ENTER.config.clientCart;
	// end of vars

	
	var
		/**
		 * Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		 */
		buy = function buy() {
			var
				button = $(this),
				url = button.attr('href');
			// end of vars

			var
				addToCart = function addToCart( data ) {
					var
						groupBtn = button.data('group'),
						upsale = button.data('upsale') ? button.data('upsale') : null,
						product = button.parents('.jsSliderItem').data('product');
					//end of vars

					if ( !data.success ) {
						return false;
					}

					button.removeClass('mLoading');

					if ( data.product ) {
						data.product.isUpsale = product && product.isUpsale ? true : false;
						data.product.fromUpsale = upsale && upsale.fromUpsale ? true : false;
					}

					$('.jsBuyButton[data-group="'+groupBtn+'"]').html('В корзине').addClass('mBought').attr('href', '/cart');
					body.trigger('addtocart', [data]);
					body.trigger('getupsale', [data, upsale]);
					body.trigger('updatespinner',[groupBtn]);
				};
			// end of functions

			$.get(url, addToCart);

			return false;
		},

		/**
		 * Хандлер кнопки купить
		 */
		buyButtonHandler = function buyButtonHandler() {
			var button = $(this),
				url = button.attr('href');
			// end of vars
			

			if ( button.hasClass('mDisabled') ) {
				return false;
			}

			if ( button.hasClass('mBought') ) {
				document.location.href(url);

				return false;
			}

			button.addClass('mLoading');
			button.trigger('buy');

			return false;
		},

		/**
		 * Маркировка кнопок «Купить»
		 * см.BlackBox startAction
		 */
		markCartButton = function markCartButton() {
			var
				products = clientCart.products,
				i,
				len;
			// end of vars
			
			console.info('markCartButton');

			for ( i = 0, len = products.length; i < len; i++ ) {
				$('.'+products[i].cartButton.id).html('В корзине').addClass('mBought').attr('href','/cart');
			}
		};
	// end of functions
	

	$(document).ready(function() {
		body.bind('markcartbutton', markCartButton);
		body.on('click', '.jsBuyButton', buyButtonHandler);
		body.on('buy', '.jsBuyButton', buy);
	});
}(window.ENTER));



/**
 * Показ окна о совершенной покупке, парсинг данных от сервера, аналитика
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, printPrice, BlackBox
 * @param		{event}		event 
 * @param		{Object}	data	данные о том что кладется в корзину
 */
(function( ENTER ) {

	var
		utils = ENTER.utils,
		blackBox = utils.blackBox,
		body = $('body');
	// end of vars
	

	var
		/**
		 * Обработка покупки, парсинг данных от сервера, запуск аналитики
		 */
		buyProcessing = function buyProcessing( event, data ) {

			if ( data.redirect ) {
				console.warn('redirect');

				document.location.href = data.redirect;
			}
			else if ( blackBox ) {
				if (data.product) blackBox.basket().add( data );  // если добавляем единичный продукт
				if (data.products)  blackBox.basket().multipleAdd( data );  // если добавляем много продуктов за раз
			}
		},

		addtocartAnalytics = function addtocartAnalytics(event, data){
			var
				/**
				 * KISS Аналитика для добавления в корзину
				 */
					kissAnalytics = function kissAnalytics( event, data ) {
					var productData = data.product,
						serviceData = data.service,
						warrantyData = data.warranty,
						nowUrl = window.location.href,
						toKISS = {};
					//end of vars

					if ( typeof _kmq === 'undefined' ) {
						return;
					}

					if ( productData ) {
						toKISS = {
							'Add to Cart SKU': productData.article,
							'Add to Cart SKU Quantity': productData.quantity,
							'Add to Cart Product Name': productData.name,
							'Add to Cart Root category': productData.category[0].name,
							'Add to Cart Root ID': productData.category[0].id,
							'Add to Cart Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
							'Add to Cart Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0,
							'Add to Cart SKU Price': productData.price,
							'Add to Cart Page URL': nowUrl,
							'Add to Cart F1 Quantity': productData.serviceQuantity
						};

						_kmq.push(['record', 'Add to Cart', toKISS]);

						productData.isUpsale && _kmq.push(['record', 'cart rec added from rec', {'SKU cart added from rec': productData.article}]);
						productData.fromUpsale && _kmq.push(['record', 'cart recommendation added', {'SKU cart rec added': productData.article}]);
					}

					if ( serviceData ) {
						toKISS = {
							'Add F1 F1 Name': serviceData.name,
							'Add F1 F1 Price': serviceData.price,
							'Add F1 SKU': productData.article,
							'Add F1 Product Name': productData.name,
							'Add F1 Root category': productData.category[0].name,
							'Add F1 Root ID': productData.category[0].id,
							'Add F1 Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
							'Add F1 Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
						};

						_kmq.push(['record', 'Add F1', toKISS]);
					}

					if ( warrantyData ) {
						toKISS = {
							'Add Warranty Warranty Name': warrantyData.name,
							'Add Warranty Warranty Price': warrantyData.price,
							'Add Warranty SKU': productData.article,
							'Add Warranty Product Name': productData.name,
							'Add Warranty Root category': productData.category[0].name,
							'Add Warranty Root ID': productData.category[0].id,
							'Add Warranty Category name': ( productData.category ) ? productData.category[productData.category.length - 1].name : 0,
							'Add Warranty Category ID': ( productData.category ) ? productData.category[productData.category.length - 1].id : 0
						};

						_kmq.push(['record', 'Add Warranty', toKISS]);
					}
				},

				/**
				 * Google Analytics аналитика добавления в корзину
				 */
					googleAnalytics = function googleAnalytics( event, data ) {
					var
						productData = data.product;
					// end of vars

					if ( !productData || typeof _gaq === 'undefined' ) {
						return;
					}

					_gaq.push(['_trackEvent', 'Add2Basket', 'product', productData.article]);

					productData.isUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_from_rec', productData.article]);
					productData.fromUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_to_cart', productData.article]);
				},


				/**
				 * myThings аналитика добавления в корзину
				 */
					myThingsAnalytics = function myThingsAnalytics( event, data ) {
					var
						productData = data.product;

					if ( productData && typeof(productData.id) && typeof MyThings !== 'undefined' ) {
						MyThings.Track({
							EventType: MyThings.Event.Visit,
							Action: '1013',
							ProductId: productData.id
						});
					}
				},

				/**
				 * Soloway аналитика добавления в корзину
				 */
					adAdriver = function adAdriver( event, data ) {
					var productData = data.product,
						offer_id = productData.id,
						category_id =  ( productData.category ) ? productData.category[productData.category.length - 1].id : 0,

						s = 'http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=add_basket&custom=10='+offer_id+';11='+category_id+'&bt=55&pz=0&rnd=![rnd]',
						d = document,
						i = d.createElement('IMG'),
						b = d.body;
					// end of vars

					s = s.replace(/!\[rnd\]/, Math.round(Math.random()*9999999)) + '&tail256=' + escape(d.referrer || 'unknown');
					i.style.position = 'absolute';
					i.style.width = i.style.height = '0px';

					i.onload = i.onerror = function(){
						b.removeChild(i);
						i = b = null;
					};

					i.src = s;
					b.insertBefore(i, b.firstChild);
				},

				/**
				 * Обработчик добавления товаров в корзину. Рекомендации от RetailRocket
				 */
					addToRetailRocket = function addToRetailRocket( event, data ) {
					var
						product = data.product,
						dataToLog;
					// end of vars


					if ( typeof rcApi === 'object' ) {
						try {
							rcApi.addToBasket(product.id);
						}
						catch ( err ) {
							dataToLog = {
								event: 'rcApi.addToBasket',
								type: 'ошибка отправки данных в RetailRocket',
								err: err
							};

							utils.logError(dataToLog);
						}
					}
				}
				/*,
				addToVisualDNA = function addToVisualDNA( event, data ) {
					var
						productData 	= data.product,
						product_id 		= productData.id,
						product_price 	= productData.price,
						category_id 	= ( productData.category ) ? productData.category[productData.category.length - 1].id : 0,
						d = document,
						b = d.body,
						i = d.createElement('IMG' );
					// end of vars

					i.src = '//e.visualdna.com/conversion?api_key=enter.ru&id=added_to_basket&product_id=' + product_id + '&product_category=' + category_id + '&value=' + product_price + '&currency=RUB';
					i.width = i.height = '1';
					i.alt = '';

					b.appendChild(i);
				}*/
				;
			//end of functions

			try{
				kissAnalytics(event, data);
				googleAnalytics(event, data);
				myThingsAnalytics(event, data);
				adAdriver(event, data);
				addToRetailRocket(event, data);
				//addToVisualDNA(event, data);
			}
			catch( e ) {
				console.warn('addtocartAnalytics error');
				console.log(e);
			}
		};
	//end of functions

	body.on('addtocart', buyProcessing);

	// analytics
	body.on('addtocart', addtocartAnalytics);

}(window.ENTER));

 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Окно смены региона
 *
 * @param	{Object}	global	Объект window
 */
;(function( global ) {

	var body = $('body'),
		regionWindow = $('.popupRegion'),
		inputRegion = $('#jscity'),
		formRegionSubmitBtn = $('#jschangecity'),
		clearBtn = regionWindow.find('.inputClear'),

		changeRegionBtn = $('.jsChangeRegion'),

		changeRegionAnalyticsBtn = $('.jsChangeRegionAnalytics'),

		slidesWrap = regionWindow.find('.regionSlidesWrap'),
		moreCityBtn = regionWindow.find('.moreCity'),
		leftArrow = regionWindow.find('.leftArr'),
		rightArrow = regionWindow.find('.rightArr'),
		citySlides = regionWindow.find('.regionSlides'),
		slideWithCity = regionWindow.find('.regionSlides_slide');
	// end of vars


	/**
	 * Настройка автодополнения поля для ввода региона
	 */
	inputRegion.autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			$.ajax({
				url: inputRegion.data('url-autocomplete'),
				dataType: 'json',
				data: {
					q: request.term
				},
				success: function( data ) {
					var res = data.data.slice(0, 15);
					response( $.map( res, function( item ) {
						return {
							label: item.name,
							value: item.name,
							url: item.url
						};
					}));
				}
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			formRegionSubmitBtn.data('url', ui.item.url );
			submitBtnEnable();
		},
		open: function() {
			$( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
		},
		close: function() {
			$( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
		}
	});

	
		/**
		 * Показ окна с выбором города
		 */
	var showRegionPopup = function showRegionPopup() {
			regionWindow.lightbox_me({
				autofocus: true,
				onLoad: function(){
					if (inputRegion.val().length){
						inputRegion.putCursorAtEnd();
						submitBtnEnable();
					}
				},
				onClose: function() {
					var id = changeRegionBtn.data('region-id');

					if ( !global.docCookies.hasItem('geoshop') ) {
						global.docCookies.setItem('geoshop', id, 31536e3, '/');
						// document.location.reload()
					}
				}
			});

			// analytics only for main page
			if ( document.location.pathname === '/' ) {
				console.info( 'run analytics for main page' );

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'citySelector', 'viewed']);
				}
			}
		},

		/**
		 * Обработка кнопок для смены региона
		 */
		changeRegionHandler = function changeRegionHandler() {
			var self = $(this),
				autoResolve = self.data('autoresolve-url');
			// end of vars

			var authFromServer = function authFromServer( res ) {
				if ( !res.data.length ) {
					$('.popupRegion .mAutoresolve').html('');
					return false;
				}

				var url = res.data[0].url,
					name = res.data[0].name,
					id = res.data[0].id;
				// end of vars

				if ( id === 14974 || id === 108136 ) {
					return false;
				}
				
				if ( $('.popupRegion .mAutoresolve').length ) {
					$('.popupRegion .mAutoresolve').html('<a href="'+url+'">'+name+'</a>');
				}
				else {
					$('.popupRegion .cityInline').prepend('<div class="cityItem mAutoresolve"><a href="'+url+'">'+name+'</a></div>');
				}
				
			};

			if ( autoResolve !== 'undefined' ) {
				$.ajax({
					type: 'GET',
					url: autoResolve,
					success: authFromServer
				});
			}
			
			showRegionPopup();

			return false;
		},

		/**
		 * Следующий слайд с городами
		 */
		nextCitySlide = function nextCitySlide() {
			var regionSlideW = slideWithCity.width() * 1,
				sliderW = citySlides.width() * 1,
				sliderLeft = parseInt(citySlides.css('left'), 10);
			// end of vars

			leftArrow.show();
			citySlides.animate({'left':sliderLeft - regionSlideW});

			if ( sliderLeft - (regionSlideW * 2) <= -sliderW ) {
				rightArrow.hide();
			}

			return false;
		},

		/**
		 * Предыдущий слайд с городами
		 */
		prevCitySlide = function prevCitySlide() {
			var regionSlideW = slideWithCity.width() * 1,
				sliderW = citySlides.width() * 1,
				sliderLeft = parseInt(citySlides.css('left'), 10);
			// end of vars

			rightArrow.show();
			citySlides.animate({'left':sliderLeft + regionSlideW});

			if ( sliderLeft + (regionSlideW * 2) >= 0 ) {
				leftArrow.hide();
			}

			return false;
		},

		/**
		 * Раскрытие полного списка городов
		 */
		expandCityList = function expandCityList() {
			$(this).toggleClass('mExpand');
			slidesWrap.slideToggle(300);

			return false;
		},

		/**
		 * Очистка поля для ввода города
		 */
		clearInputHandler = function clearInputHandler() {
			inputRegion.val('');
			submitBtnDisable();
			clearBtn.hide();
			
			return false;
		},

		/**
		 * Обработчик изменения в поле ввода города
		 */
		inputRegionChangeHandler = function inputRegionChangeHandler() {
			if ( $(this).val() ) {
				submitBtnEnable();
				clearBtn.show();
			}
			else {
				submitBtnDisable();
				clearBtn.hide();
			}
		},

		changeRegionAnalytics = function changeRegionAnalytics( regionName ) {
			// analytics only for main page
			if ( document.location.pathname === '/' ) {
				console.info( 'run analytics for main page ' + regionName);

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'citySelector', 'selected', regionName]);
				}
			}
		},

		changeRegionAnalyticsHandler = function changeRegionAnalyticsHandler() {
			var regionName = $(this).text();

			changeRegionAnalytics(regionName);
		},

		/**
		 * Обработчик сохранения введенного региона
		 */
		submitCityHandler = function submitCityHandler() {
			var url = $(this).data('url'),
				regionName = inputRegion.val();
			// end of vars

			changeRegionAnalytics(regionName);

			if ( url ) {
				global.location = url;
			}
			else {
				regionWindow.trigger('close');
			}

			return false;
		},

		/**
		 * Блокировка кнопки "Сохранить"
		 */
		submitBtnDisable = function() {
			formRegionSubmitBtn.addClass('mDisabled');
			formRegionSubmitBtn.attr('disabled','disabled');
		},

		/**
		 * Разблокировка кнопки "Сохранить"
		 */
		submitBtnEnable = function() {
			formRegionSubmitBtn.removeClass('mDisabled');
			formRegionSubmitBtn.removeAttr('disabled');
		};
	// end of functions


	/**
	 * ==== Handlers ====
	 */
	formRegionSubmitBtn.on('click', submitCityHandler);
	moreCityBtn.on('click', expandCityList);
	clearBtn.on('click', clearInputHandler);
	rightArrow.on('click', nextCitySlide);
	leftArrow.on('click', prevCitySlide);
	inputRegion.on('keyup', inputRegionChangeHandler);
	body.on('click', '.jsChangeRegion', changeRegionHandler);

	changeRegionAnalyticsBtn.on('click', changeRegionAnalyticsHandler);


	/**
	 * ==== GEOIP fix ====
	 */
	if ( !global.docCookies.hasItem('geoshop') ) {
		showRegionPopup();
	}
}(this));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Custom inputs
 *
 * @requires jQuery
 *
 * @author	Zaytsev Alexandr
 */
;(function() {
	var inputs = $('input.bCustomInput'),
		body = $('body');
	// end of vars

	var updateState = function updateState() {
		if ( !$(this).is('[type=checkbox]') && !$(this).is('[type=radio]') ) {
			return;
		}

		var $self = $(this),
			id = $self.attr('id'),
			type = ( $self.is('[type=checkbox]') ) ? 'checkbox' : 'radio',
			groupName = $self.attr('name') || '',
			label = $('label[for="'+id+'"]');
		// end of vars

		if ( type === 'checkbox' ) {

			if ( $self.is(':checked') ) {
				label.addClass('mChecked');
			}
			else {
				label.removeClass('mChecked');
			}
		}


		if ( type === 'radio' && $self.is(':checked') ) {
			$('input[name="'+groupName+'"]').each(function() {
				var currElement = $(this),
					currId = currElement.attr('id');

				$('label[for="'+currId+'"]').removeClass('mChecked');
			});

			label.addClass('mChecked');
		}
	};


	body.on('updateState', '.bCustomInput', updateState);

	body.on( 'change', '.bCustomInput', function() {
		$(this).trigger('updateState');
	});

	inputs.trigger('updateState');
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
$(document).ready(function(){
	// var carturl = $('.lightboxinner .point2').attr('href')


	/* вывод слайдера со схожими товарами, если товар доступен только на витрине*/
	if ( $('#similarGoodsSlider').length ) {

		// основные элементы
		var similarSlider = $('#similarGoodsSlider'),
			similarWrap = similarSlider.find('.bSimilarGoodsSlider_eWrap'),
			similarArrow = similarSlider.find('.bSimilarGoodsSlider_eArrow'),

			slidesW = 0,

			sliderW = 0,
			slidesCount = 0,
			wrapW = 0,
			left = 0;
		// end of vars
		
		var kissSimilar = function kissSimilar() {
				var clicked = $(this),
					toKISS = {
						'Recommended Item Clicked Similar Recommendation Place':'product',
						'Recommended Item Clicked Similar Clicked SKU':clicked.data('article'),
						'Recommended Item Clicked Similar Clicked Product Name':clicked.data('name'),
						'Recommended Item Clicked Similar Product Position':clicked.data('pos')
					};
				// end of vars
				
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['record', 'Recommended Item Clicked Similar', toKISS]);
				}
			},

			// init
			init = function init( data ) {
				var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods');

				for ( var item in data ) {
					var similarGood = tmpl('similarGoodTmpl',data[item]);
					similarWrap.append(similarGood);
				}

				slidesW = similarGoods.width() + parseInt(similarGoods.css('paddingLeft'), 10) * 2;
				slidesCount = similarGoods.length;
				wrapW = slidesW * slidesCount;
				similarWrap.width(wrapW);

				if ( slidesCount > 0 ) {
					$('.bSimilarGoods').fadeIn(300, function() {
						sliderW = similarSlider.width();
					});
				}

				if ( slidesCount < 4 ){
					$('.bSimilarGoodsSlider_eArrow.mRight').hide();
				}
			};

		$.getJSON( $('#similarGoodsSlider').data('url') , function( data ) {
			if ( !($.isEmptyObject(data)) ){
				var initData = data;

				init(initData);
			}
		}).done(function() {
			var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods');

			slidesCount = similarGoods.length;
			wrapW = slidesW * slidesCount;
			similarWrap.width(wrapW);
			if ( slidesCount > 0 ) {
				$('.bSimilarGoods').fadeIn(300, function(){
					sliderW = similarSlider.width();
				});
			}
		});
		
		similarArrow.bind('click', function() {
			if ( $(this).hasClass('mLeft') ) {
				left += (slidesW * 2);
			}
			else {
				left -= (slidesW * 2);
			}
			// left *= ($(this).hasClass('mLeft'))?-1:1
			if ( (left <= sliderW-wrapW) ) {
				left = sliderW - wrapW;
				$('.bSimilarGoodsSlider_eArrow.mRight').hide();
				$('.bSimilarGoodsSlider_eArrow.mLeft').show();
			} 
			else if ( left >= 0 ) {
				left = 0;
				$('.bSimilarGoodsSlider_eArrow.mLeft').hide();
				$('.bSimilarGoodsSlider_eArrow.mRight').show();
			}
			else {
				similarArrow.show();
			}

			similarWrap.animate({'left':left});
			return false;
		});


		// KISS
		$('.bSimilarGoods.mProduct .bSimilarGoodsSlider_eGoods').on('click', kissSimilar);
	}



	// hover imitation for IE
	if ( window.navigator.userAgent.indexOf('MSIE') >= 0 ) {
		$('.allpageinner').on( 'hover', '.goodsbox__inner', function() {
			$(this).toggleClass('hover');
		});
	}

	/* ---- */
	$('body').on('click', '.goodsbox__inner', function(e) {
		if ( $(this).attr('data-url') ) {
			window.location.href = $(this).attr('data-url');
		}
	});



	/**
	 * KISS view category
	 */
	var kissForCategory = function kissForCategory() {
		var data = $('#_categoryData').data('category'),
			toKISS = {
				'Viewed Category Category Type':data.type,
				'Viewed Category Category Level':data.level,
				'Viewed Category Parent category':data.parent_category,
				'Viewed Category Category name':data.category,
				'Viewed Category Category ID':data.id
			};
		// end of vars
		
		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['record', 'Viewed Category', toKISS]);
		}
	};


    var kissForProductOfCategory = function kissForProductOfCategory(event) {
        //event.preventDefault(); // tmp
        //console.log('*** clickeD!!! '); // tmp

        var t = $(this), box, datap, toKISS = false,
            datac = $('#_categoryData').data('category');
        // end of vars

        box = t.parents('div.goodsbox__inner');

        if ( !box.length ) {
        	box = t.parents('div.goodsboxlink');
        }

        datap = box.length ? box.data('add') : false;

        if ( datap && datac ) {
            toKISS = {
                'Category Results Clicked Category Type': datac.type,
                'Category Results Clicked Category Level': datac.level,
                'Category Results Clicked Parent category': datac.parent_category,
                'Category Results Clicked Category name': datac.category,
                'Category Results Clicked Category ID': datac.id,
                'Category Results Clicked SKU': datap.article,
                'Category Results Clicked Product Name': datap.name,
                'Category Results Clicked Page Number': datap.page,
                'Category Results Clicked Product Position': datap.position
            };
        }

        /** For Debug:  **/
        /*
        console.log('*** test IN CLICK BEGIN { ');
        if (toKISS) console.log(toKISS);
        if (!datap) console.log('!!! DataP is empty!');
        if (!datac) console.log('!!! DataP is empty!');
        console.log('*** } test IN CLICK END');
        */
        /** **/

        if ( toKISS && typeof _kmq !== 'undefined' ) {
            _kmq.push(['record', 'Category Results Clicked', toKISS]);
        }

        //return false; // tmp
    };


    if ( $('#_categoryData').length ) {
		kissForCategory();
        /** Вызываем kissForProductOfCategory() для всех категорий - в том числе слайдеров, аджаксов и тп **/
        $('body').delegate('div.goodsbox a', 'click', kissForProductOfCategory);
	}

	/**
	 * KISS Search
	 */
	var kissForSearchResultPage = function kissForSearchResultPage() {
		var data = $('#_searchKiss').data('search'),
			toKISS = {
				'Search String':data.query,
				'Search Page URL':data.url,
				'Search Items Found':data.count
			};
		// end of vars

		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['record', 'Search', toKISS]);
		}

		var KISSsearchClick = function() {
			var productData = $(this).data('add'),
				prToKISS = {
					'Search Results Clicked Search String':data.query,
					'Search Results Clicked SKU':productData.article,
					'Search Results Clicked Product Name':productData.name,
					'Search Results Clicked Page Number':productData.page,
					'Search Results Clicked Product Position':productData.position
				};
			// end of vars

			if ( typeof(_kmq) !== 'undefined' ) {
				_kmq.push(['record', 'Search Results Clicked',  prToKISS]);
			}
		};

		$('.goodsbox__inner').on('click', KISSsearchClick);
		$('.goodsboxlink').on('click', KISSsearchClick);
	};

	if ( $('#_searchKiss').length ) {
		kissForSearchResultPage();
	}

});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Перемотка к Id
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
(function() {
	var goToId = function goToId() {
		var to = $(this).data('goto');

		$(document).stop().scrollTo( $('#'+to), 800 );
		
		return false;
	};
	
	$(document).ready(function() {
		$('.jsGoToId').bind('click',goToId);
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Обработчик горячих ссылок
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var handleHotLinksToggle = function() {
    var toggle = $(this);
    if(toggle.hasClass('expanded')) {
      toggle.parent().parent().find('.toHide').hide();
      toggle.html('Все метки');
      toggle.removeClass('expanded');
    } else {
      toggle.parent().parent().find('.toHide').show();
      toggle.html('Основные метки');
      toggle.addClass('expanded');
    }
    return false;
  };


  $(document).ready(function(){
    $('.hotlinksToggle').bind('click', handleHotLinksToggle);
  });
}());



 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * JIRA
 */
;(function() {
	$.ajax({
		url: 'https://jira.enter.ru/s/ru_RU-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6',
		type: 'get',
		cache: true,
		dataType: 'script'
	});
	
	window.ATL_JQ_PAGE_PROPS = {
		'triggerFunction': function( showCollectorDialog ) {
			$('#jira').click(function( e ) {
				e.preventDefault();
				showCollectorDialog();
			});
		}
	};
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function( ENTER ) {
	var constructors = ENTER.constructors,
		body = $('body'),
		authBlock = $('#auth-block'),
		registerMailPhoneField = $('.jsRegisterUsername'),
		resetPwdForm = $('.jsResetPwdForm'),
		registerForm = $('.jsRegisterForm'),
		loginForm = $('.jsLoginForm'),
		completeRegister = $('.jsRegisterFormComplete'),
		showLoginFormLink = $('.jsShowLoginForm'),

		/**
		 * Конфигурация валидатора для формы логина
		 * @type {Object}
		 */
		signinValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsSigninUsername', authBlock),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword', authBlock),
					require: true,
					customErr: 'Не указан пароль'
				}
			]
		},
		signinValidator = new FormValidator(signinValidationConfig),

		/**
		 * Конфигурация валидатора для формы регистрации
		 * @type {Object}
		 */
		registerValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsRegisterFirstName', authBlock),
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: registerMailPhoneField,
					validBy: 'isEmail',
					require: true,
					customErr: 'Некорректно введен e-mail'
				}
			]
		},
		registerValidator = new FormValidator(registerValidationConfig),

		/**
		 * Конфигурация валидатора для формы регистрации
		 * @type {Object}
		 */
		forgotPwdValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsForgotPwdLogin', authBlock),
					require: true,
					customErr: 'Не указан email или мобильный телефон',
					validateOnChange: true
				}
			]
		},
		forgotValidator = new FormValidator(forgotPwdValidationConfig);
	// end of vars

	var
		/**
		 * Задаем настройки валидаторов.
		 * Глобальные настройки позволяют навешивать кастомные валидаторы на различные авторизационные формы.
		 */
		setValidatorSettings = function() {
			ENTER.utils.signinValidationConfig = signinValidationConfig;
			ENTER.utils.signinValidator = signinValidator;
			ENTER.utils.registerValidationConfig = registerValidationConfig;
			ENTER.utils.registerValidator = registerValidator;
			ENTER.utils.forgotPwdValidationConfig = forgotPwdValidationConfig;
			ENTER.utils.forgotValidator = forgotValidator;
		};
	// end of functions

	setValidatorSettings();

	/**
	 * Класс по работе с окном входа на сайт
	 *
	 * @author  Shaposhnik Vitaly
	 *
	 * @this    {Login}
	 *
	 * @constructor
	 */
	constructors.Login = (function() {
		'use strict';

		function Login() {
			// enforces new
			if ( !(this instanceof Login) ) {
				return new Login();
			}
			// constructor body

			this.form = null; // текущая форма
			this.redirect_to = null;

			body.on('click', '.registerAnotherWayBtn', $.proxy(this.registerAnotherWay, this));
			body.on('click', '.bAuthLink', this.openAuth);
			$('.jsLoginForm, .jsRegisterForm, .jsResetPwdForm').data('redirect', true).on('submit', $.proxy(this.formSubmit, this));
			body.on('click', '.jsForgotPwdTrigger, .jsRememberPwdTrigger', this.forgotFormToggle);
			body.on('click', '#bUserlogoutLink', this.logoutLinkClickLog);

			if ( showLoginFormLink.length ) {
				loginForm.hide();
				body.on('click', '.jsShowLoginForm', this.showLoginForm);
			}
		}


		/**
		 * Показ сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.showError = function( msg, callback ) {
			var error = $('ul.error_list', this.form);
			// end of vars

			if ( callback !== undefined ) {
				callback();
			}

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', this.form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
			}

			return false;
		};

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formErrorHandler = function( formError ) {
			var validator = this.getFormValidator(),
				field = $('[name="' + this.getFormName() + '[' + formError.field + ']"]');
			// end of vars

			var clearError = function clearError() {
				validator._unmarkFieldError($(this));
			};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		};

		/**
		 * Обработка ошибок из ответа сервера
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.serverErrorHandler = {
			'default': function( res ) {
				console.log('Обработчик ошибки');

				if ( !res.redirect ) {
					res.redirect = window.location.href;
				}

				if ( res.error && res.error.message ) {
					this.showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return false;
				}

				document.location.href = res.redirect;
			},

			0: function( res ) {
				var formError = null;
				// end of vars

				console.warn('Обработка ошибок формы');

				if ( res.redirect ) {
					this.showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return;
				}

				// очищаем блок с глобальными ошибками
				if ( $('ul.error_list', this.form).length ) {
					$('ul.error_list', this.form).html('');
				}
				//this.showError(res.error.message);

				for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
					formError = res.form.error[i];
					console.warn(formError);

					if ( formError.field !== 'global' && formError.message !== null ) {
						$.proxy(this.formErrorHandler, this)(formError);
					}
					else if ( formError.field === 'global' && formError.message !== null ) {
						this.showError(formError.message);
					}
				}

				return false;
			}
		};

		/**
		 * Проверяем как e-mail
		 *
		 * @return  {Boolean}   Выбрано ли поле e-mail в качестве регистрационных данных
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.checkEmail = function() {
			return registerMailPhoneField.hasClass('jsRegisterPhone') ? false : true;
		};

		/**
		 * Переключение типов проверки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.registerAnotherWay = function() {
			var label = $('.registerAnotherWay'),
				btn = $('.registerAnotherWayBtn');
			// end of vars

			registerMailPhoneField.val('');

			if ( this.checkEmail() ) {
				label.html('Ваш мобильный телефон:');
				btn.html('Ввести e-mail');
				registerMailPhoneField.addClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isPhone', customErr: 'Некорректно введен телефон'} );

				// устанавливаем маску для поля "Ваш мобильный телефон"
				$.mask.definitions['n'] = '[0-9]';
				registerMailPhoneField.mask('+7 (nnn) nnn-nn-nn');
			}
			else {
				label.html('Ваш e-mail:');
				btn.html('У меня нет e-mail');
				registerMailPhoneField.removeClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isEmail', customErr: 'Некорректно введен e-mail'} );

				// убераем маску с поля "Ваш мобильный телефон"
				registerMailPhoneField.unmask();
			}

			return false;
		};

		/**
		 * Authorization process
		 *
		 * @public
		 */
		Login.prototype.openAuth = function() {
			var
				/**
				 * При закрытии попапа убераем ошибки с полей
				 */
				removeErrors = function() {
					var
						validators = ['signin', 'register', 'forgot'],
						validator,
						config,
						self,
						i, j;
					// end of vars

					for (j in validators) {
						validator = eval('ENTER.utils.' + validators[j] + 'Validator');
						config = eval('ENTER.utils.' + validators[j] + 'ValidationConfig');

						if ( !config || !config.fields || !validator ) {
							continue;
						}

						for (i in config.fields) {
							self = config.fields[i].fieldNode;
							self && validator._unmarkFieldError(self);
						}
					}
				};
			// end of functions

			setValidatorSettings();

			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				},
				onClose: removeErrors
			});

			return false;
		};

		/**
		 * Изменение значения кнопки сабмита при отправке ajax запроса
		 *
		 * @param btn Кнопка сабмита
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.submitBtnLoadingDisplay = function( btn ) {
			if ( btn.length ) {
				var value1 = btn.val(),
					value2 = btn.data('loading-value');
				// end of vars

				btn.attr('disabled', (btn.attr('disabled') === 'disabled' ? false : true)).val(value2).data('loading-value', value1);
			}

			return false;
		};

		/**
		 * Валидатор формы
		 *
		 * @return  {Object}   Валидатор для текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormValidator = function() {
			return eval('ENTER.utils.' + this.getFormName() + 'Validator');
		};

		/**
		 * Получить название формы
		 *
		 * @return {string} Название текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormName = function() {
			return ( this.form.hasClass('jsLoginForm') ) ? 'signin' : (this.form.hasClass('jsRegisterForm') ? 'register' : (this.form.hasClass('jsResetPwdForm') ? 'forgot' : ''));
		};

		/**
		 * Сабмит формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmit = function( e, param ) {
			e.preventDefault();
			this.form = $(e.target);

			var formData = this.form.serializeArray(),
				validator = this.getFormValidator(),
				formSubmit = $('.jsSubmit', this.form),
				forgotPwdLogin = $('.jsForgotPwdLogin', this.form),
				urlParams = this.getUrlParams(),
				timeout;
			// end of vars

			// устанавливаем редирект
			this.redirect_to = window.location.href;
			if ( urlParams['redirect_to'] ) {
				this.redirect_to = urlParams['redirect_to'];
			}

			var responseFromServer = function( response ) {
					// когда пришел ответ с сервера, очищаем timeout
					clearTimeout(timeout);

					if ( response.error ) {
						console.warn('Form has error');

						if ( Login.serverErrorHandler.hasOwnProperty(response.error.code) ) {
							console.log('Есть обработчик');
							$.proxy(Login.serverErrorHandler[response.error.code], this)(response);
						}
						else {
							console.log('Стандартный обработчик');
							Login.serverErrorHandler['default'](response);
						}

						this.submitBtnLoadingDisplay( formSubmit );

						return false;
					}

					$.proxy(this.formSubmitLog, this);

					// если форма "Восстановление пароля" то скрываем елементы и выводим сообщение
					if ( forgotPwdLogin.length && forgotPwdLogin.is(':visible') ) {
						this.submitBtnLoadingDisplay( formSubmit );
						forgotPwdLogin.hide();
						$('.jsForgotPwdLoginLabel', this.form).hide();
						formSubmit.hide();
						this.showError(response.notice.message);
					}

					console.log(this.form.data('redirect'));
					console.log(response.data.link);
					if ( typeof(gaRun) != 'undefined' && typeof(gaRun.register) === 'function' ) {
						gaRun.register();
					}

					if ( this.form.data('redirect') ) {
						if ( typeof (response.data.link) !== 'undefined' ) {
							console.info('try to redirect to2 ' + response.data.link);
							console.log(typeof response.data.link);

							document.location.href = response.data.link;

							return false;
						}
						else {
							// this.form.unbind('submit');
							// this.form.submit();

							completeRegister.html(response.message);
							completeRegister.show();
							registerForm.hide();
							this.showLoginForm();

							// Закомментил следующую строку так как изза нее возникает баг SITE-3389
							// document.location.href = window.location.href;
						}
					}
					else {
						authBlock.trigger('close');
					}

					//for order page
					if ( $('#order-form').length ) {
						$('#user-block').html('Привет, <strong><a href="' + response.data.link + '">' + response.data.user.first_name + '</a></strong>');
						$('#order_recipient_first_name').val(response.data.user.first_name);
						$('#order_recipient_last_name').val(response.data.user.last_name);
						$('#order_recipient_phonenumbers').val(response.data.user.mobile_phone.slice(1));
						$('#qiwi_phone').val(response.data.user.mobile_phone.slice(1));
					}
				},

				requestToServer = function() {
					this.submitBtnLoadingDisplay( formSubmit );
					formData.push({name: 'redirect_to', value: this.redirect_to});
					$.post(this.form.attr('action'), formData, $.proxy(responseFromServer, this), 'json');

					/*
					 SITE-3174 Ошибка авторизации.
					 Принято решение перезагружать страничку через 5 сек, после отправки запроса на логин.
					 */
					timeout = setTimeout($.proxy(function() {document.location.href = this.redirect_to;}, this), 5000);
				};
			// end of functions

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);
				},
				onValid: $.proxy(requestToServer, this)
			});

			return false;
		};

		/**
		 * Показать форму логина на странице /login
		 */
		Login.prototype.showLoginForm = function() {
			showLoginFormLink.hide();
			loginForm.slideDown(300);
			$.scrollTo(loginForm, 500);
		};


		/**
		 * Отображение формы "Забыли пароль"
		 *
		 * @public
		 */
		Login.prototype.forgotFormToggle = function() {
			if ( resetPwdForm.is(':visible') ) {
				resetPwdForm.hide();
				loginForm.show();
			}
			else {
				resetPwdForm.show();
				loginForm.hide();
			}

			return false;
		};

		/**
		 * Логирование при сабмите формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmitLog = function() {
			var type = '';
			// end of vars
			if ( typeof(gaRun) && typeof(gaRun.login) === 'function' ) {
				gaRun.login();
			}
			if ( 'signin' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsSigninUsername').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', this.form.find('.jsSigninUsername').val() ]);
				}
			}
			else if ( 'register' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( this.checkEmail() ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', this.form.find('.jsRegisterUsername').val() ]);
				}
			}
			else if ( 'forgot' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsForgotPwdLogin').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
				}
			}
		};

		/**
		 * Логирование при клике на ссылку выхода
		 *
		 * @public
		 */
		Login.prototype.logoutLinkClickLog = function() {
			if ( typeof(_kmq) !== 'undefined' ) {
				_kmq.push(['clearIdentity']);
			}
		};

		/**
		 * Получение get параметров текущей страницы
		 */
		Login.prototype.getUrlParams = function() {
			var $_GET = {},
				__GET = window.location.search.substring(1).split('&'),
				getVar,
				i;
			// end of vars

			for ( i = 0; i < __GET.length; i++ ) {
				getVar = __GET[i].split('=');
				$_GET[getVar[0]] = typeof( getVar[1] ) === 'undefined' ? '' : getVar[1];
			}

			return $_GET;
		};

		return Login;
	}());


	$(document).ready(function() {
		var login = new ENTER.constructors.Login();
	});

}(window.ENTER));
 
 
/** 
 * NEW FILE!!! 
 */
 
 
$(document).ready(function() {
	/**
	 * Подписка
	 */
	$('body').on('click', '.bSubscibe', function() {
		if ( $(this).hasClass('checked') ) {
			$(this).removeClass('checked');
			$(this).find('.subscibe').removeAttr('checked');
			$(this).find('input[name="subscribe"]').val(0);
		} else {
			$(this).addClass('checked');
			$(this).find('.subscibe').attr('checked','checked');
			$(this).find('input[name="subscribe"]').val(1);
		}

		return false;
	});


	/* GA categories referrer */
	function categoriesSpy( e ) {
		if ( typeof(_gaq) !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'CategoryClick', e.data, window.location.pathname ]);
		}

		return true;
	}

	$('.bMainMenuLevel-1__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-2__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-3__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.breadcrumbs').first().find('a').bind( 'click', 'Хлебные крошки сверху', categoriesSpy );
	$('.breadcrumbs-footer').find('a').bind( 'click', 'Хлебные крошки снизу', categoriesSpy );
	
	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.parent().find('li.hf').slideToggle();
		var link = el.find('a');
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...');
	});

	$('.product_buy-container').each(function() {
		var order = $(this).data('order');

		if ( typeof(order) == 'object' && !$.isEmptyObject(order) ) {
			$.ajax({
				url: ($(this).data('url')),
				data: order,
				type: 'POST',
				timeout: 20000
			});
		}
	});

	if ( $('.searchtextClear').length ){
		$('.searchtextClear').each(function() {
			if ( !$(this).val().length ) {
				$(this).addClass('vh');
			}
			else {
				$(this).removeClass('vh');
			}
		});

		$('.searchtextClear').click(function() {
			$(this).siblings('.searchtext').val('');
			$(this).addClass('vh');

			if ( $('#searchAutocomplete').length ) {
				$('#searchAutocomplete').html('');
			}
		});
	}

	

	$('.enterPrizeDesc').click(
		function() {
			$(this).next('.enterPrizeListWrap').toggle('fast');
	});
});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function($){	
	/*paginator*/
	var EnterPaginator = function( domID,totalPages, visPages, activePage ) {
		
		var self = this;

		self.inputVars = {
			domID: domID, // id элемента для пагинатора
			totalPages:totalPages, //общее количество страниц
			visPages:visPages?visPages:10, // количество видимых сраниц
			activePage:activePage?activePage:1 // текущая активная страница
		};

		var pag = $('#'+self.inputVars.domID), // пагинатор
			pagW = pag.width(), // ширина пагинатора
			eSliderFillW = (pagW*self.inputVars.visPages)/self.inputVars.totalPages, // ширина закрашенной области слайдера
			onePageOnSlider = eSliderFillW / self.inputVars.visPages, // ширина соответствующая одной странице на слайдере
			onePage = pagW / self.inputVars.visPages, // ширина одной цифры на пагинаторе
			center = Math.round(self.inputVars.visPages/2);
		// end of vars

		var scrollingByBar = function scrollingByBar ( left ) {
			var pagLeft = Math.round(left/onePageOnSlider);

			$('.bPaginator_eWrap', pag).css('left', -(onePage * pagLeft));
		};

		var enableHandlers = function enableHandlers() {
			// биндим хандлеры
			var clicked = false,
				startX = 0,
				nowLeft = 0;
			// end of vars
			
			$('.bPaginatorSlider', pag).bind('mousedown', function(e){
				startX = e.pageX;
				nowLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10);
				clicked = true;
			});

			$('.bPaginatorSlider', pag).bind('mouseup', function(){
				clicked = false;
			});

			pag.bind('mouseout', function(){
				clicked = false;
			});

			$('.bPaginatorSlider', pag).bind('mousemove', function(e){
				if ( clicked ) {
					var newLeft = nowLeft+(e.pageX-startX);

					if ( (newLeft >= 0) && (newLeft <= pagW - eSliderFillW) ) {
						$('.bPaginatorSlider_eFill', pag).css('left', nowLeft + (e.pageX - startX));
						scrollingByBar(newLeft);
					}
				}
			});
		};

		var init = function init() {
			pag.append('<div class="bPaginator_eWrap"></div>');
			pag.append('<div class="bPaginatorSlider"><div class="bPaginatorSlider_eWrap"><div class="bPaginatorSlider_eFill" style="width:'+eSliderFillW+'px"></div></div></div>');
			for ( var i = 0; i < self.inputVars.totalPages; i++ ) {
				$('.bPaginator_eWrap', pag).append('<a class="bPaginator_eLink" href="#' + i + '">' + (i + 1) + '</a>');

				if ( (i + 1) === self.inputVars.activePage ) {
					$('.bPaginator_eLink', pag).eq(i).addClass('active');
				}
			}
			var realLinkW = $('.bPaginator_eLink', pag).width(); // реальная ширина цифр

			$('.bPaginator_eLink', pag).css({'marginLeft':(onePage - realLinkW - 2)/2, 'marginRight':(onePage - realLinkW - 2)/2}); // размазываем цифры по ширине слайдера
			$('.bPaginator_eWrap', pag).addClass('clearfix').width(onePage * self.inputVars.totalPages); // устанавливаем ширину wrap'а, добавляем ему очистку
		};

		self.setActive = function ( page ) {
			var left = parseInt($('.bPaginator_eWrap', pag).css('left'), 10), // текущее положение пагинатора
				barLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10), // текущее положение бара
				nowLeftElH = Math.round(left/onePage) * (-1), // количество скрытых элементов
				diff = -(center - (page - nowLeftElH)); // на сколько элементов необходимо подвинуть пагинатор для центрирования
			// end of vars
			
			$('.bPaginator_eLink', pag).removeClass('active');
			$('.bPaginator_eLink', pag).eq(page).addClass('active');

			if ( left - (diff * onePage) > 0 ) {
				left = 0;
				barLeft = 0;
			}
			else if ( page > self.inputVars.totalPages - center ) {
				left = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePage*(-1);
				barLeft = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePageOnSlider;
			}
			else {
				left = left - (diff * onePage);
				barLeft = barLeft + (diff * onePageOnSlider);
			}

			$('.bPaginator_eWrap').animate({'left': left});
			$('.bPaginatorSlider_eFill', pag).animate({'left': barLeft});
		};

		init();
		enableHandlers();
	};

	/* promo catalog */
	if ( $('#promoCatalog').length ) {
		console.log('promoCatalog promoSlider');

		var
			body = $('body'),
			promoCatalog = $('#promoCatalog'),
			data = promoCatalog.data('slides'),

			//первоначальная настройка
			slider_SlideCount = data.length, //количество слайдов
			catalogPaginator = new EnterPaginator('promoCatalogPaginator',slider_SlideCount, 12, 1),

			activeInterval = promoCatalog.data('use-interval') !== undefined ? promoCatalog.data('use-interval') : false,
			interval = null,
			toSlide = null,
			nowSlide = 0,//текущий слайд

			// Флаг под которым реализована дорисовка hash к url
			activeHash = promoCatalog.data('use-hash') !== undefined ? promoCatalog.data('use-hash') : true,
			hash,
			scrollingDuration = 500,

			/**
			 * Флаг включения карусели (бесконечная листалка влево/вправо).
			 * Если флаг отключен, то когда слайдер долистался до конца, он визуально перемещается в начало
			 * @type {Boolean}
			 */
			activeCarousel = promoCatalog.data('use-carousel') !== undefined ? promoCatalog.data('use-carousel') : false,
			slideId,// id слайда
			shift = 0,// сдвиг

			slider_SlideW,// ширина одного слайда
			slider_WrapW,// ширина обертки

			disabledBtns = false;// Активность кнопок для пролистования и пагинатора.
		// end of vars

		var
			initSlider = function initSlider() {
				var
					slide,
					slideTmpl;
				// end of vars

				if ( activeCarousel ) {
					$('.bPromoCatalogSlider_eArrow.mArLeft').show();
					$('.bPromoCatalogSlider_eArrow.mArRight').show();
				}

				for ( slide = 0; slide < data.length; slide++ ) {
					slideTmpl = tmpl('slide_tmpl', data[slide]);

					if ( $(slideTmpl).length ) {
						slideTmpl = $(slideTmpl).attr("id", 'slide_id_' + slide);
					}

					$('.bPromoCatalogSliderWrap').append(slideTmpl);

					if ( $('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).attr('href') === '' ) {
						$('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).removeAttr('href');
					}

					$('.bPromoCatalogNav').append('<a id="promoCatalogSlide' + slide + '" href="#' + slide + '" class="bPromoCatalogNav_eLink">' + ((slide * 1) + 1) + '</a>');
				}

				slider_SlideW = $('.bPromoCatalogSliderWrap_eSlide').width();
				slider_WrapW = $('.bPromoCatalogSliderWrap').width( slider_SlideW * slider_SlideCount + (940/2 - slider_SlideW/2));
			},

			/**
			 * Задаем интервал для пролистывания слайдов
			 */
			setScrollInterval = function setScrollInterval( slide ) {
				var
					time,
					additionalTime = 0;
				// end of vars

				if ( !activeInterval ) {
					return;
				}

				if ( slide == undefined ) {
					slide = 0;
				}
				else {
					additionalTime = scrollingDuration;
				}

				time = data[slide]['time'] ? data[slide]['time'] : 3000;
				time = time + additionalTime;

				interval = setTimeout(function(){
					slide++;

					if ( !activeCarousel ) {
						if ( slider_SlideCount <= slide ) {
							slide = 0;
						}
					}

					moveSlide(slide);
					setScrollInterval(slide);
				}, time);
			},

			/**
			 * Убираем интервал для пролистывания слайдов
			 */
			removeScrollInterval = function removeScrollInterval() {
				if ( !interval ) {
					return;
				}

				clearTimeout(interval);
			},

			/**
			 * Click кнопки для листания
			 *
			 * @param e
			 */
			btnsClick = function( e ) {
				var
					pos = ( $(this).hasClass('mArLeft') ) ? '-1' : '1',
					slide = nowSlide + pos * 1;
				// end of vars

				e.preventDefault();

				if ( disabledBtns ) {
					return false;
				}

				removeScrollInterval();
				moveSlide(slide);
				setScrollInterval(slide);
			},

			/**
			 * Click пагинатора
			 *
			 * @param e
			 */
			paginatorClick = function( e ) {
				var
					link;
				// end of vars

				e.preventDefault();

				if ( $(this).hasClass('active') ) {
					return false;
				}

				if ( disabledBtns ) {
					return false;
				}

				link = $(this).attr('href').slice(1) * 1;
				removeScrollInterval();

				if ( activeCarousel ) {
					moveToSlideId(link);
				}
				else {
					moveSlide(link);
				}

				setScrollInterval(link);
			},

			/**
			 * Перемещение слайдов на указанный slideId.
			 * Данная функция должна использоваться только при включенном activeCarousel
			 *
			 * @param id Id слайда
			 */
			moveToSlideId = function( id ){
				var
					slidesWrap = $(".jsPromoCatalogSliderWrap"),
					slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap),
					slide;
				// end of vars

				if ( id === undefined ) {
					id = 0;
				}

				slide = slides.index($('#slide_id_' + id, slidesWrap));
				moveSlide(slide);
			},

			/**
			 * Перемещение слайдов на указанный слайд
			 *
			 * @param slide Позиция слайда
			 */
			moveSlide = function moveSlide( slide ) {
				var
					leftBtn = $('.bPromoCatalogSlider_eArrow.mArLeft'),
					rightBtn = $('.bPromoCatalogSlider_eArrow.mArRight'),
					slidesWrap = $(".jsPromoCatalogSliderWrap"),
					buff;
				// end of vars

				var
					/**
					 * Перемещение последнего слайда в начало wrapper элемента
					 */
					moveLastSlideToStart = function() {
						var
							slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap);
						// end of vars

						buff = slides.last();
						slides.last().remove();
						slidesWrap.prepend(buff);
						slidesWrap.css({left: slidesWrap.position().left - slider_SlideW});
					},

					/**
					 * Перемещение первого слайда в конец wrapper элемента
					 */
					moveFirstSlideToEnd = function() {
						var
							slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap);
						// end of vars

						buff = slides.first();
						slides.first().remove();
						slidesWrap.append(buff);
						slidesWrap.css({left: slidesWrap.position().left + slider_SlideW});
					};
				// end of functions

				slideId = slide;
				nowSlide = slide;

				if ( !activeCarousel) {
					if ( slide === 0 ) leftBtn.hide();
					else leftBtn.show();

					if ( slide === slider_SlideCount - 1 ) rightBtn.hide();
					else rightBtn.show();
				}
				else {
					if ( slide > slider_SlideCount - 1 ) {
						moveFirstSlideToEnd();
						shift++;
						slide = 0;
						nowSlide = slider_SlideCount - 1;
					}
					else if ( slide < 0 ) {
						moveLastSlideToStart();
						shift--;
						slide = slider_SlideCount - 1;
						nowSlide = 0;
					}

					slideId = $(".jsPromoCatalogSliderWrap .bPromoCatalogSliderWrap_eSlide").eq(nowSlide).attr("id").replace('slide_id_', '');
				}

				// деактивируем кнопочки для пролистывания
				disabledBtns = true;

				$('.bPromoCatalogSliderWrap').animate({'left': -(slider_SlideW * nowSlide)}, scrollingDuration, function() {
					// активируем кнопочки для пролистывания
					disabledBtns = false;
				});

				catalogPaginator.setActive(slideId);

				if ( activeHash ) {
					window.location.hash = 'slide' + ((slideId * 1) + 1);
				}
			};
		// end of functions

		initSlider(); //запуск слайдера

		body.on('click', '.bPromoCatalogSlider_eArrow', btnsClick);
		body.on('click', '.bPaginator_eLink', paginatorClick);

		if ( activeHash ) {
			hash = window.location.hash;
			if ( hash.indexOf('slide') + 1 ) {
				toSlide = parseInt(hash.slice(6), 10) - 1;
				moveSlide(toSlide);
			}
		}

		setScrollInterval( toSlide ? (toSlide) : null);
	}
})(jQuery);
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * SITE-2693
 * Показывать окно авторизации, если по аяксу был получен ответ с 403-м статусом
 *
 * @author		Shaposhnik Vitaly
 */
;(function() {
	var authBlock;// блок авторизации

	$.ajaxSetup({
		error : function(jqXHR, textStatus, errorThrown) {
			if ( 403 == jqXHR.status ) {
				authBlock = $('#auth-block');

				if ( !authBlock.length ) {
					return;
				}

				authBlock.lightbox_me({
					centered: true,
					autofocus: true,
					onLoad: function() {
						authBlock.find('input:first').focus();
					}
				});
			}
		}
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Всплывающая синяя плашка с предложением о подписке
 * Срабатывает при возникновении события showsubscribe.
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, FormValidator, docCookies
 *
 * @param		{event}		event
 * @param		{Object}	subscribe			Информация о подписке
 * @param		{Boolean}	subscribe.agreed	Было ли дано согласие на подписку в прошлый раз
 * @param		{Boolean}	subscribe.show		Показывали ли пользователю плашку с предложением о подписке
 */
;(function() {
	var
		body = $('body'),
		subscribeCookieName = 'subscribed';
	// end of vars

	var
		lboxCheckSubscribe = function lboxCheckSubscribe( event ) {
			var
				notNowShield = $('.bSubscribeLightboxPopupNotNow'),
				subPopup = $('.bSubscribeLightboxPopup'),
				input = $('.bSubscribeLightboxPopup__eInput'),
				submitBtn = $('.bSubscribeLightboxPopup__eBtn'),
				subscribe = {
					'show': !window.docCookies.hasItem(subscribeCookieName),
					'agreed': 1 === window.docCookies.getItem(subscribeCookieName)
				},
				inputValidator = new FormValidator({
					fields: [
						{
							fieldNode: input,
							customErr: 'Неправильный емейл',
							require: true,
							validBy: 'isEmail'
						}
					]
				});
			// end of vars

			var
				subscribing = function subscribing() {
					var
						email = input.val(),
						url = $(this).data('url');
					//end of vars

					var
						/**
						 * Обработчик ответа пришедшего с сервера
						 * @param res Ответ с сервера
						 */
							serverResponseHandler = function serverResponseHandler( res ) {
							if( !res.success ) {
								return false;
							}

							subPopup.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>');
							window.docCookies.setItem('subscribed', 1, 157680000, '/');

							setTimeout(function() {
								subPopup.slideUp(300);
							}, 3000);

							// analytics
							if ( typeof _gaq !== 'undefined' ) {
								_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
							}

							// subPopup.append('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'/" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');
						};
					// end of functions

					if ( submitBtn.hasClass('mDisabled') ) {
						return false;
					}

					inputValidator.validate({
						onInvalid: function( err ) {
							console.log('Email is invalid');
							console.log(err);
						},
						onValid: function() {
							console.log('Email is valid');
							$.post(url, {email: email}, serverResponseHandler);
						}
					});

					return false;
				},

				subscribeNow = function subscribeNow() {
					var
						notNow = $('.bSubscribeLightboxPopup__eNotNow');
					// end of vars

					var
						/**
						 * Обработчик клика на ссылку "Спасибо, не сейчас"
						 * @param e
						 */
							notNowClickHandler = function( e ) {
							e.preventDefault();

							var url = $(this).data('url');

							subPopup.slideUp(300, subscribeLater);
							window.docCookies.setItem('subscribed', 0, 157680000, '/');
							$.post(url);
						};
					// end of functions

					subPopup.slideDown(300);

					submitBtn.bind('click', subscribing);

					notNow.off('click');
					notNow.bind('click', notNowClickHandler);
				},

				subscribeLater = function subscribeLater() {
					notNowShield.slideDown(300);
					notNowShield.bind('click', function() {
						$(this).slideUp(300);
						subscribeNow();
					});
				};
			//end of functions

			input.placeholder();

			if ( !subscribe.show ) {
				if ( !subscribe.agreed ) {
					subscribeLater();
				}

				return false;
			}
			else {
				subscribeNow();
			}
		};
	// end of functions

	body.bind('showsubscribe', lboxCheckSubscribe);
	body.trigger('showsubscribe');
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Саджест для поля поиска
 * Нужен рефакторинг
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.placeholder
 *
 * @param	{Object}	searchInput			Поле поиска
 * @param	{Object}	suggestWrapper		Обертка для подсказок
 * @param	{Object}	suggestItem			Результаты поиска
 * 
 * @param	{Number}	nowSelectSuggest	Текущий выделенный элемент, если -1 - значит выделенных элементов нет
 * @param	{Number}	suggestLen			Количество результатов поиска
 */
;(function() {
	var
		body = $('body'),
		searchForm = $('div.searchbox form'),
        searchInput = searchForm.find('input.searchtext'),
		suggestWrapper = $('#searchAutocomplete'),
		suggestItem = $('.bSearchSuggest__eRes'),

		nowSelectSuggest = -1,
		suggestLen = 0,

		suggestCache = {},

		tID = null;
	// end of vars	


	var
		suggestAnalytics = function suggestAnalytics() {
			var
				link = suggestItem.eq(nowSelectSuggest).attr('href'),
				type = ( suggestItem.eq(nowSelectSuggest).hasClass('bSearchSuggest__eCategoryRes') ) ? 'suggest_category' : 'suggest_product';
			// end of vars
			
			if ( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Search', type, link]);
			}
		},

		/**
		 * Загрузить ответ от поиска: получить и показать его, с запоминанием (memoization)
		 *
		 * @returns {boolean}
		 */
		loadResponse = function loadResponse() {
			var
				text = searchInput.val(),

				/**
				 * Отрисовка данных с сервера
				 *
				 * @param	{String}	response	Ответ от сервера
				 */
				renderResponse = function renderResponse( response ) {
					suggestCache[text] = response; // memoization

					suggestWrapper.html(response);
					suggestItem = $('.bSearchSuggest__eRes');
					suggestLen = suggestItem.length;
					if ( suggestLen ) {
						//searchInputFocusin();
						setTimeout(searchInputFocusin, 99);
					}
				},

				/**
				 * Запрос на получение данных с сервера
				 */
				getResFromServer = function getResFromServer() {
					var
						//text = searchInput.val(),
						url = '/search/autocomplete?q=';

					if ( text.length < 3 ) {
						return false;
					}
					url += encodeURI( text );

					$.ajax({
						type: 'GET',
						url: url,
						success: renderResponse
					});
				};
			// end of functions and vars

			if ( text.length === 0 ) {
				suggestWrapper.empty();

				return false;
			}

			clearTimeout(tID);

			// memoization
			if ( suggestCache[text] ) {
				renderResponse(suggestCache[text]);

				return false;
			}

			tID = setTimeout(getResFromServer, 300);
		}, // end of loadResponse()

		/**
		 * Экранируем лишние пробелы перед отправкой на сервер
		 * вызывается по нажатию Ентера либо кнопки "Отправить"
		 */
		escapeSearchQuery = function escapeSearchQuery() {
			var s = searchInput.val().replace(/(^\s*)|(\s*$)/g,'').replace(/(\s+)/g,' ');
			searchInput.val(s);
		}

		/**
		 * Обработчик поднятия клавиши
		 * 
		 * @param	{Event}		event
		 * @param	{Number}	keyCode	Код нажатой клавиши
		 * @param	{String}	text	Текст в поле ввода
		 */
		suggestKeyUp = function suggestKeyUp( event ) {
			var
				keyCode = event.which;

			if ( (keyCode >= 37 && keyCode <= 40) ||  keyCode === 27 || keyCode === 13) { // Arrow Keys or ESC Key or ENTER Key
				return false;
			}

			loadResponse();
		},

		/**
		 * Обработчик нажатия клавиши
		 * 
		 * @param	{Event}		event
		 * @param	{Number}	keyCode	Код нажатой клавиши
		 */
		suggestKeyDown = function suggestKeyDown( event ) {
			var
				keyCode = event.which;

			var
				markSuggestItem = function markSuggestItem() {
					suggestItem.removeClass('hover').eq(nowSelectSuggest).addClass('hover');
				},

				selectUpItem = function selectUpItem() {
					if ( nowSelectSuggest - 1 >= 0 ) {
						nowSelectSuggest--;
						markSuggestItem();
					}
					else {
						nowSelectSuggest = -1;
						suggestItem.removeClass('hover');
						$(this).focus();
					}
				},

				selectDownItem = function selectDownItem() {
					if ( nowSelectSuggest + 1 <= suggestLen - 1 ) {
						nowSelectSuggest++;
						markSuggestItem();
					}
				},

				enterSelectedItem = function enterSelectedItem() {
					var link = suggestItem.eq(nowSelectSuggest).attr('href');

					suggestAnalytics();
					document.location.href = link;
				};
			// end of functions

			if ( keyCode === 38 ) { // Arrow Up
				selectUpItem();

				return false;
			}
			else if ( keyCode === 40 ) { // Arrow Down
				selectDownItem();

				return false;
			}
			else if ( keyCode === 27 ) { // ESC Key
				suggestWrapper.empty();
				
				return false;
			}
			else if ( keyCode === 13 ) {
				escapeSearchQuery();
				if ( nowSelectSuggest !== -1 ) { // Press Enter and suggest has selected item
					enterSelectedItem();

					return false;
				}
			}
		},

		searchSubmit = function searchSubmit() {
			var text = searchInput.attr('value');

			if ( text.length === 0 ) {
				return false;
			}
			escapeSearchQuery();
		},

		searchInputFocusin = function searchInputFocusin() {
			suggestWrapper.show();
		},
		
		suggestCloser = function suggestCloser( e ) {
			var
				targ = e.target.className;

			if ( !(targ.indexOf('bSearchSuggest')+1 || targ.indexOf('searchtext')+1) ) {
				suggestWrapper.hide();
			}
		},

		/**
		 * Срабатывание выделения и запоминание индекса выделенного элемента по наведению мыши
		 */
		hoverForItem = function hoverForItem() {
			var index = 0;

			suggestItem.removeClass('hover');
			index = $(this).addClass('hover').index();
			nowSelectSuggest = index - 1;
		},


		/**
		 * Подставляет поисковую подсказку в строку поиска
		 */
		searchHintSelect = function searchHintSelect() {
			var
				hintValue = $(this).text()/*,
				searchValue = searchInput.val()*/;
			//if ( searchValue ) hintValue = searchValue + ' ' + hintValue;
			searchInput.val(hintValue + ' ').focus();
			if ( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'tooltip', hintValue]);
			}
			loadResponse();
		};
	// end of functions


	/**
	 * Attach handlers
	 */
	$(document).ready(function() {
		searchInput.bind('keydown', suggestKeyDown);
		searchInput.bind('keyup', suggestKeyUp);

		searchInput.bind('focus', searchInputFocusin);
        searchForm.bind('submit', searchSubmit);

		searchInput.placeholder();

		body.bind('click', suggestCloser);
		body.on('mouseenter', '.bSearchSuggest__eRes', hoverForItem);
		body.on('click', '.bSearchSuggest__eRes', suggestAnalytics);
		body.on('click', '.sHint_value', searchHintSelect);
	});
}());

 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function(){

    // https://jira.enter.ru/browse/SITE-3508
    // SITE-3508 Закрепить товары в листинге чибы

    if ( /catalog\/tchibo/.test(document.location.href) && window.history) {

        var history = window.history;

        $(window).on('beforeunload', function () {
            history.replaceState({pageYOffset: pageYOffset}, '');
        });

        if (history && history.state.pageYOffset) {
            window.scrollTo(0, history.state.pageYOffset);
        }

    }

}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/* Top Menu */
(function(){
	var menuDelayLvl1 = 300; //ms
	var menuDelayLvl2 = 600; //ms
	var triangleOffset = 15; //px

	var lastHoverLvl1 = null;
	var checkedItemLvl1 = null;
	var hoverNowLvl1 = false;

	var lastHoverLvl2 = null;
	var checkedItemLvl2 = null;

	var currentMenuItemDimensions = null;
	var menuLevel2Dimensions = null;
	var menuLevel3Dimensions = null;
	var pointA = {x: 0,	y: 0};
	var pointB = {x: 0,	y: 0};
	var pointC = {x: 0,	y: 0};
	var cursorNow = {x: 0, y: 0};

	/**
	 * Активируем элемент меню 1-го уровня
	 *
	 * @param  {element} el
	 */
	var activateItemLvl1 = function(el){
		lastHoverLvl1 = new Date();
		checkedItemLvl1 = el;
		$('.bMainMenuLevel-2__eItem').removeClass('hover');
		el.addClass('hover');
	};

	/**
	 * Обработчик наведения на элемент меню 1-го уровня
	 */
	var menuHoverInLvl1 = function(){
		var el = $(this);

		// SITE-3041 Если в верхнем меню в категории нет child НЕ делать выпадалку
		if ( el.hasClass('jsEmptyChild') ) {
			return;
		}

		lastHoverLvl1 = new Date();
		hoverNowLvl1 = true;

		setTimeout(function(){
			if(hoverNowLvl1 && (new Date() - lastHoverLvl1 > menuDelayLvl1)) {
				activateItemLvl1(el);
			}
		}, menuDelayLvl1 + 20);
	};

	/**
	 * Обработчик ухода мыши из элемента меню 1-го уровня
	 */
	var menuMouseLeaveLvl1 = function(){
		var el = $(this);
		el.removeClass('hover');
		hoverNowLvl1 = false;
	};

	/**
	 * Непосредственно построение треугольника. Требуется предвариательно получить нужные координаты и размеры
	 */
	var createTriangle = function(){
		// левый угол - текущее положение курсора
		pointA = {
			x: cursorNow.x,
			y: cursorNow.y - $(window).scrollTop()
		};

		// верхний угол - левый верх меню 3го уровня минус triangleOffset
		pointB = {
			x: menuLevel3Dimensions.left - triangleOffset,
			y: menuLevel3Dimensions.top - $(window).scrollTop()
		};

		// нижний угол - левый низ меню 3го уровня минус triangleOffset
		pointC = {
			x: menuLevel3Dimensions.left - triangleOffset,
			y: menuLevel3Dimensions.top + menuLevel3Dimensions.height - $(window).scrollTop()
		};
	};

	/**
	 * Проверка входит ли точка в треугольник.
	 * Соединяем точку со всеми вершинами и считаем площадь маленьких треугольников.
	 * Если она равна площади большого треугольника, то точка входит в треугольник. Иначе не входит.
	 * Также точка входит в область задержки, если она попадает в прямоугольник, формируемый сдвигом треугольника
	 * 
	 * @param  {object} now    координаты точки, которую необходимо проверить
	 * 
	 * @param  {object} A      левая вершина большого треугольника
	 * @param  {object} A.x    координата по оси x левой вершины
	 * @param  {object} A.y    координата по оси y левой вершины
	 * 
	 * @param  {object} B      верхняя вершина большого треугольника
	 * @param  {object} B.x    координата по оси x верхней вершины
	 * @param  {object} B.y    координата по оси y верхней вершины
	 * 
	 * @param  {object} C      нижняя вершина большого треугольника
	 * @param  {object} C.x    координата по оси x нижней вершины
	 * @param  {object} C.y    координата по оси y нижней вершины
	 * 
	 * @return {boolean}       true - входит, false - не входит
	 */
	var menuCheckTriangle = function(){
		var res1 = (pointA.x-cursorNow.x)*(pointB.y-pointA.y)-(pointB.x-pointA.x)*(pointA.y-cursorNow.y);
		var res2 = (pointB.x-cursorNow.x)*(pointC.y-pointB.y)-(pointC.x-pointB.x)*(pointB.y-cursorNow.y);
		var res3 = (pointC.x-cursorNow.x)*(pointA.y-pointC.y)-(pointA.x-pointC.x)*(pointC.y-cursorNow.y);

		if ((res1 >= 0 && res2 >= 0 && res3 >= 0) || (res1 <= 0 && res2 <= 0 && res3 <= 0) || (cursorNow.x >= pointB.x && cursorNow.x <= (pointB.x + triangleOffset) && cursorNow.y >= pointB.y && cursorNow.y <= pointC.y)){
			// console.info('принадлежит')
			return true;
		} else {
			// console.info('не принадлежит')
			return false;
		}
	};

	/**
	 * Отслеживание перемещения мыши по меню 2-го уровня
	 *
	 * @param  {event} e
	 */
	var menuMoveLvl2 = function(e){
		cursorNow = {
			x: e.pageX,
			y: e.pageY - $(window).scrollTop()
		};
		var el = $(this);
		if(checkedItemLvl2) {
			if(el.attr('class') === checkedItemLvl2.attr('class')) {
				buildTriangle(el);
				lastHoverLvl2 = new Date();
			}
		}
		checkHoverLvl2(el);
	};

	/**
	 * Активируем элемент меню 2-го уровня, строим треугольник
	 *
	 * @param  {element} el
	 */
	var activateItemLvl2 = function(el){
		checkedItemLvl2 = el;
		el.addClass('hover');
		lastHoverLvl2 = new Date();
		buildTriangle(el);
	};

	/**
	 * Обработчик наведения на элемент меню 2-го уровня
	 */
	var menuHoverInLvl2 = function(){
		var el = $(this);
		checkHoverLvl2(el);
		el.addClass('hoverNowLvl2');

		if(lastHoverLvl2 && (new Date() - lastHoverLvl2 <= menuDelayLvl2) && menuCheckTriangle()) {
			setTimeout(function(){
				if(el.hasClass('hoverNowLvl2') && (new Date() - lastHoverLvl2 > menuDelayLvl2)) {
					checkHoverLvl2(el);
				}
			}, menuDelayLvl2 + 20);
		}
	};

	/**
	 * Обработчик ухода мыши из элемента меню 1-го уровня
	 */
	var menuMouseLeaveLvl2 = function(){
		var el = $(this);
		el.removeClass('hoverNowLvl2');
	};

	/**
	 * Меню 2-го уровня
	 * Если первое наведение - просто активируем
	 * Иначе - проверяем условия по которым активировать
	 *
	 * @param  {element} el
	 */
	var checkHoverLvl2 = function(el) {
		if (!lastHoverLvl2) {
			activateItemLvl2(el);
		} else if(!menuCheckTriangle() || (lastHoverLvl2 && (new Date() - lastHoverLvl2 > menuDelayLvl2) && menuCheckTriangle())) {
			checkedItemLvl2.removeClass('hover');
			activateItemLvl2(el);
		}
	};

	/**
	 * Получаем все нужные координаты и размеры и строим треугольник, попадание курсора в который
	 * будет определять нужна ли задержка до переключения на другой пункт меню
	 *
	 * @param  {element} el
	 */
	var buildTriangle = function(el) {
		currentMenuItemDimensions = getDimensions(el);
		menuLevel2Dimensions = getDimensions(el.find('.bMainMenuLevel-3'));
		var dropMenuWidth = el.find('.bMainMenuLevel-2__eTitle')[0].offsetWidth;
		menuLevel3Dimensions = {
			top: menuLevel2Dimensions.top,
			left: menuLevel2Dimensions.left + dropMenuWidth,
			width: menuLevel2Dimensions.width - dropMenuWidth,
			height: menuLevel2Dimensions.height
		};
		createTriangle();
	};

	/**
	 * Получение абсолютных координат элемента и его размеров
	 *
	 * @param  {element} el
	 */
	var getDimensions = function(el) {
      var width = $(el).width();
      var height = $(el).height();
      el = el[0];
      var x = 0;
      var y = 0;
      while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
          x += el.offsetLeft - el.scrollLeft;
          y += el.offsetTop - el.scrollTop;
          el = el.offsetParent;
      }
      return { top: y, left: x, width: width, height: height };
  };


	$('.bMainMenuLevel-1__eItem').mouseenter(menuHoverInLvl1);
	$('.bMainMenuLevel-1__eItem').mouseleave(menuMouseLeaveLvl1);

	$('.bMainMenuLevel-2__eItem').mouseenter(menuHoverInLvl2);
	$('.bMainMenuLevel-2__eItem').mousemove(menuMoveLvl2);
	$('.bMainMenuLevel-2__eItem').mouseleave(menuMouseLeaveLvl2);





	/* код ниже был закомментирован в main.js, перенес его сюда чтобы код, касающийся меню, был в одном месте */

	// header_v2
	// $('.bMainMenuLevel-1__eItem').bind('mouseenter', function(){
	//  var menuLeft = $(this).offset().left
	//  var cornerLeft = menuLeft - $('#header').offset().left + ($(this).find('.bMainMenuLevel-1__eTitle').width()/2) - 11
	//  $(this).find('.bCorner').css({'left':cornerLeft})
	// })

	// header_v1
	// if( $('.topmenu').length && !$('body#mainPage').length) {
	//  $.get('/category/main_menu', function(data){
	//    $('#header').append( data )
	//  })
	// }

	// var idcm          = null // setTimeout
	// var currentMenu = 0 // ref= product ID
	// function showList( self ) {  
	//  if( $(self).data('run') ) {
	//    var dmenu = $(self).position().left*1 + $(self).width()*1 / 2 + 5
	//    var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
	//    if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
	//      punkt.show()//.find('.corner').css('left', dmenu)
	//  }
	// }
	// if( clientBrowser.isTouch ) {
	//  $('#header .bToplink').bind ('click', function(){
	//    if( $(this).data('run') )
	//      return true
	//    $('.extramenu').hide()  
	//    $('.topmenu a.bToplink').each( function() { $(this).data('run', false) } )
	//    $(this).data('run', true)
	//    showList( this )
	//    return false
	//  })
	// } else { 
	//  $('#header .bToplink').bind( {
	//    'mouseenter': function() {
	//      $('.extramenu').hide()
	//      var self = this       
	//      $(self).data('run', true)
	//      currentMenu = $(self).attr('id').replace(/\D+/,'')
	//      var menuLeft = $(self).offset().left
	//      var cornerLeft = menuLeft-$('#header').offset().left+($('#topmenu-root-'+currentMenu+'').width()/2)-13
	//      $('#extramenu-root-'+currentMenu+' .corner').css({'left':cornerLeft})
	//      idcm = setTimeout( function() { showList( self ) }, 300)
	//    },
	//    'mouseleave': function() {
	//      var self = this

	//      if( $(self).data('run') ) {
	//        clearTimeout( idcm )
	//        $(self).data('run',false)
	//      }
	//      //currentMenu = 0
	//    }
	//  })
	// }

	// $(document).click( function(e){
	//  if (currentMenu) {
	//    if( e.which == 1 )
	//      $( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
	//  }
	// })

	// $('.extramenu').click( function(e){
	//  e.stopPropagation()
	// })
})();

	
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var upper = $('#upper'),
		trigger = false;	//сработало ли появление языка
	// end of vars
	
	
	var pageScrolling = function pageScrolling()  {
			if ( $(window).scrollTop() > 600 && !trigger ) {
				//появление языка
				trigger = true;
				upper.animate({'marginTop':'0'}, 400);
			}
			else if ( $(window).scrollTop() < 600 && trigger ) {
				//исчезновение
				trigger = false;
				upper.animate({'marginTop':'-55px'}, 400);
			}
		},

		goUp = function goUp() {
			$(window).scrollTo('0px',400);

			return false;
		};
	//end of functions

	$(window).scroll(pageScrolling);
	upper.bind('click',goUp);
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * White floating user bar
 *
 * 
 * @requires jQuery, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var
		config = ENTER.config,
		utils = ENTER.utils,
		clientCart = config.clientCart,

		userBar = utils.extendApp('ENTER.userBar'),

		userBarFixed = userBar.userBarFixed = $('.fixedTopBar.mFixed'),
		userbarStatic = userBar.userBarStatic = $('.fixedTopBar.mStatic'),

		topBtn = userBarFixed.find('.fixedTopBar__upLink'),
		userbarConfig = userBarFixed.data('value'),
		body = $('body'),
		w = $(window),
		infoShowing = false,
		overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),
		newOverlay = false,

		scrollTarget,
		scrollTargetOffset;
	// end of vars


	userBar.showOverlay = false;


	var
		/**
		 * Показ юзербара
		 */
		showUserbar = function showUserbar() {
			userBarFixed.slideDown();
			userbarStatic.css('visibility','hidden');
		},

		/**
		 * Скрытие юзербара
		 */
		hideUserbar = function hideUserbar() {
			userBarFixed.slideUp();
			userbarStatic.css('visibility','visible');
		},

		/**
		 * Проверка текущего скролла
		 */
		checkScroll = function checkScroll() {
			var
				nowScroll = w.scrollTop();
			// end of vars

			if ( infoShowing ) {
				return;
			}

			if ( nowScroll >= scrollTargetOffset ) {
				showUserbar();
			}
			else {
				hideUserbar();
			}
		},

		/**
		 * Прокрутка до фильтра и раскрытие фильтров
		 */
		upToFilter = function upToFilter() {
			$.scrollTo(scrollTarget, 500);
			ENTER.catalog.filter.openFilter();

			return false;
		},

		/**
		 * Обновление данных пользователя
		 *
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные пользователя
		 */
		updateUserInfo = function updateUserInfo( event, data ) {
			console.info('userbar::updateUserInfo');
			console.log(data);

			var
				userWrap = userBarFixed.find('.fixedTopBar__logIn'),
				userWrapStatic = userbarStatic.find('.fixedTopBar__logIn'),
				template = $('#userbar_user_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			if ( !( data && data.name && data.link ) ) {
				return;
			}

			html = Mustache.render(template.html(), data, partials);

			userWrapStatic.removeClass('mLogin');
			userWrap.removeClass('mLogin');
			userWrapStatic.html(html);
			userWrap.html(html);
		},

		/**
		 * Закрытие окна о совершенной покупке
		 */
		closeBuyInfo = function closeBuyInfo() {
			var
				wrap = userBarFixed.find('.fixedTopBar__cart'),
				wrapLogIn = userBarFixed.find('.fixedTopBar__logIn'),
				openClass = 'mOpenedPopup',
				upsaleWrap = wrap.find('.hintDd');
			// end of vars

			var
				/**
				 * Удаление выпадающей плашки для корзины
				 */
				removeBuyInfoBlock = function removeBuyInfoBlock() {
					var
						buyInfo = $('.fixedTopBar__cartOn');
					// end of vars

					if ( !buyInfo.length ) {
						return;
					}

					buyInfo.slideUp(300, function() {
						//checkScroll();
//						buyInfo.remove();
						infoShowing = false;
					});
				},

				/**
				 * Удаление Overlay блока
				 */
				removeOverlay = function removeOverlay() {
					overlay.fadeOut(100, function() {
						userBar.showOverlay = false;

						if ( newOverlay ) {
							newOverlay = false;

							return;
						}

						overlay.off('click');
						overlay.remove();
						userBar.showOverlay = false;
						checkScroll();
					});
				};
			// end of function

			// только BuyInfoBlock
			if ( !upsaleWrap.hasClass('mhintDdOn') ) {
				removeBuyInfoBlock();

				if ( userBar.showOverlay ) {
					removeOverlay();
				}

				return;
			}

			upsaleWrap.removeClass('mhintDdOn');
			wrapLogIn.removeClass(openClass);
			wrap.removeClass(openClass);

			if ( infoShowing ) {
				removeBuyInfoBlock();
			}

			if ( userBar.showOverlay ) {
				removeOverlay();
			}

			return false;
		},

		/**
		 * Показ окна о совершенной покупке
		 */
		showBuyInfo = function showBuyInfo( e ) {
			console.info('userbar::showBuyInfo');

			var
				wrap = userBarFixed.find('.fixedTopBar__cart'),
				wrapLogIn = userBarFixed.find('.fixedTopBar__logIn'),
				template = $('#buyinfo_tmpl'),
				partials = template.data('partial'),
				openClass = 'mOpenedPopup',
				dataToRender = {},
				buyInfo,
				html;
			// end of vars

			dataToRender.products = utils.cloneObject(clientCart.products);
			dataToRender.showTransparent = !!( dataToRender.products.length > 3 );
			dataToRender.products.reverse();
			console.log(dataToRender);

			html = Mustache.render(template.html(), dataToRender, partials);
			buyInfo = $(html).css({ left: -129 });
			
			buyInfo.find('.cartList__item').eq(0).addClass('mHover');
			wrapLogIn.addClass(openClass);
			wrap.addClass(openClass);
			wrap.append(buyInfo);

			if ( !userBar.showOverlay ) {
				body.append(overlay);
				overlay.fadeIn(300);

				userBar.showOverlay = true;
			}

			if ( e ) {
				buyInfo.slideDown(300);
			}
			else {
				buyInfo.show();
			}

			showUserbar();

			infoShowing = true;

			overlay.on('click', closeBuyInfo);
		},

		/**
		 * Удаление товара из корзины
		 */
		deleteProductHandler = function deleteProductHandler() {
			console.log('deleteProductHandler click!');

			var
				btn = $(this),
				deleteUrl = btn.attr('href');
			// end of vars
			
			var
				authFromServer = function authFromServer( res, data ) {
					console.warn( res );
					if ( !res.success ) {
						console.warn('удаление не получилось :(');

						return;
					}

					utils.blackBox.basket().deleteItem(res);

					//показываем корзину пользователя при удалении товара
					if ( clientCart.products.length !== 0 ) {
						showBuyInfo();
					}

					//скрываем оверлоу, если товаров в корзине нет
					if ( clientCart.products.length == 0 ) {
						overlay.fadeOut(300, function() {
							overlay.off('click');
							overlay.remove();

							userBar.showOverlay = false;
						});
						infoShowing = false;
						console.log('clientCart is empty');
						checkScroll();
					}

					//возвращаем кнопку - Купить
					var
						addUrl = res.product.addUrl;
						addBtnBuy = res.product.cartButton.id;
					// end of vars
					
					$('.'+addBtnBuy).html('Купить').removeClass('mBought').attr('href', addUrl);
				};

			$.ajax({
				type: 'GET',
				url: deleteUrl,
				success: authFromServer
			});

			return false;
		},

		/**
		 * Обновление данных о корзине
		 * WARNING! перевести на Mustache
		 * 
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные корзины
		 */
		updateBasketInfo = function updateBasketInfo( event, data ) {
			console.info('userbar::updateBasketInfo');
			console.log(data);
			console.log(clientCart);

			var
				cartWrap = userBarFixed.find('.fixedTopBar__cart'),
				cartWrapStatic = userbarStatic.find('.fixedTopBar__cart'),
				template = $('#userbar_cart_tmpl'),
				partials = template.data('partial'),
				html;
			// end of vars

			console.log('vars inited');

			data.hasProducts = false;
			data.showTransparent = false;

			if ( !(data && data.quantity && data.sum ) ) {
				console.warn('data and data.quantuty and data.sum not true');

				var
					template = $('#userbar_cart_empty_tmpl');
					partials = template.data('partial'),
				// end of vars

				html = Mustache.render(template.html(), data, partials);

				cartWrap.addClass('mEmpty');
				cartWrapStatic.addClass('mEmpty');
				cartWrapStatic.html(html);
				cartWrap.html(html);

				return;
			}

			if ( clientCart.products.length !== 0 ) {
				data.hasProducts = true;
				data.products = utils.cloneObject(clientCart.products);
				data.products.reverse();
			}

			if ( clientCart.products.length > 3 ) {
				data.showTransparent = true;
			}

			data.sum = printPrice( data.sum );
			html = Mustache.render(template.html(), data, partials);

			cartWrapStatic.removeClass('mEmpty');
			cartWrap.removeClass('mEmpty');
			cartWrapStatic.html(html);
			cartWrap.html(html);
			
		},

		/**
		 * Обновление блока с рекомендациями "С этим товаром также покупают"
		 *
		 * @param	{Object}	event	Данные о событии
		 * @param	{Object}	data	Данные о покупке
		 * @param	{Object}	upsale
		 */
		showUpsell = function showUpsell( event, data, upsale ) {
			console.info('userbar::showUpsell');

			var
				cartWrap = userBarFixed.find('.fixedTopBar__cart'),
				upsaleWrap = cartWrap.find('.hintDd'),
				slider;
			// end of vars

			var
				responseFromServer = function responseFromServer( response ) {
				console.log(response);

				if ( !response.success ) {
					return;
				}

				console.info('Получены рекомендации "С этим товаром также покупают" от RetailRocket');

				upsaleWrap.find('.bGoodsSlider').remove();

				slider = $(response.content)[0];
				upsaleWrap.append(slider);
				upsaleWrap.addClass('mhintDdOn');
				$(slider).goodsSlider();

				// показываем overlay для блока рекомендаций
				body.append(overlay);
				newOverlay = true;
				overlay.fadeIn(300);
				overlay.on('click', closeBuyInfo);
//				checkScroll();
				userBar.showOverlay = true;

                if ( !data.product ) return;

				if ( !data.product.article ) {
					console.warn('Не получен article продукта');

					return;
				}

				console.log('Трекинг товара при показе блока рекомендаций');

				// Retailrocket. Показ товарных рекомендаций
				if ( response.data ) {
					try {
						rrApi.recomTrack(response.data.method, response.data.id, response.data.recommendations);
					} catch( e ) {
						console.warn('showUpsell() Retailrocket error');
						console.log(e);
					}
				}

				// google analytics
				typeof _gaq == 'function' && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.product.article]);
				// Kissmetrics
				typeof _kmq == 'function' && _kmq.push(['record', 'cart recommendation shown', {'SKU cart rec shown': data.product.article}]);
			};
			//end functions

			console.log(upsale);

			if ( !upsale.url ) {
                console.log('if upsale.url');
				return;
			}

			$.ajax({
				type: 'GET',
				url: upsale.url,
				success: responseFromServer
			});
		},

		/**
		 * Обработчик клика по товару из списка рекомендаций
		 */
		upsaleProductClick = function upsaleProductClick() {
			var
				product = $(this).parents('.jsSliderItem').data('product');
			//end of vars

			if ( !product.article ) {
				console.warn('Не получен article продукта');

				return;
			}

			console.log('Трекинг при клике по товару из списка рекомендаций');
			// google analytics
			_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_clicked', product.article]);
			// Kissmetrics
			_kmq && _kmq.push(['record', 'cart recommendation clicked', {'SKU cart rec clicked': product.article}]);

			//window.docCookies.setItem('used_cart_rec', 1, 1, 4*7*24*60*60, '/');
		};
	// end of functions


	console.info('Init userbar module');
	console.log(userbarConfig);

	body.on('click', '.jsUpsaleProduct', upsaleProductClick);
	body.on('userLogged', updateUserInfo);
	body.on('basketUpdate', updateBasketInfo);
	body.on('getupsale', showUpsell);


	userbarStatic.on('click', '.jsCartDelete', deleteProductHandler);


	if ( userBarFixed.length ) {
		body.on('addtocart', showBuyInfo);
		userBarFixed.on('click', '.jsCartDelete', deleteProductHandler);
		scrollTarget = $(userbarConfig.target);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userBarFixed.height();
			w.on('scroll', checkScroll);
		}
	}
	else {
		overlay.remove();
		overlay = false;
	}

}(window.ENTER));
