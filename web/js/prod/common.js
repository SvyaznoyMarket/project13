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
					var groupBtn = button.data('group'),
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
		myThingsAnalytics = function myThingsAnalytics( data ) {
			var productData = data.product;

			if ( typeof MyThings !== 'undefined' ) {
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
			var product = data.product,
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
		},

		/**
		 * Обработка покупки, парсинг данных от сервера, запуск аналитики
		 */
		buyProcessing = function buyProcessing( event, data ) {

			if ( data.redirect ) {
				console.warn('redirect');

				document.location.href = data.redirect;
			}
			else if ( blackBox ) {
				blackBox.basket().add( data );
			}
		},

		/**
		 *
		 */
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
		};
	//end of functions

	body.on('addtocart', buyProcessing);

	// analytics
	body.on('addtocart', kissAnalytics);
	body.on('addtocart', googleAnalytics);
	body.on('addtocart', myThingsAnalytics);
	body.on('addtocart', adAdriver);
	body.on('addtocart', addToRetailRocket);
	body.on('addtocart', addToVisualDNA);
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
			formRegionSubmitBtn.removeClass('mDisabled');
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
						formRegionSubmitBtn.removeClass('mDisabled');
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
			clearBtn.hide();
			
			return false;
		},

		/**
		 * Обработчик изменения в поле ввода города
		 */
		inputRegionChangeHandler = function inputRegionChangeHandler() {
			if ( $(this).val() ) {
				clearBtn.show();
			}
			else {
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
		registerMailPhoneField = $('.jsRegisterUsername'),
		body = $('body'),
		authBlock = $('#auth-block'),
		forgotPwdLogin = $('.jsForgotPwdLogin'),
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
					fieldNode: $('.jsSigninUsername'),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword'),
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
					fieldNode: $('.jsRegisterFirstName'),
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
					fieldNode: forgotPwdLogin,
					require: true,
					customErr: 'Не указан email или мобильный телефон',
					validateOnChange: true
				}
			]
		},
		forgotValidator = new FormValidator(forgotPwdValidationConfig);
	// end of vars


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
			body.on('click', '.jsEnterprizeAuthLink', $.proxy(this.enterprizeAuthLinkClick, this));
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
			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				}
			});

			return false;
		};

		/**
		 * Обработчик клика на ссылку получения купона для неавторизированного пользователя
		 *
		 * @param e
		 * @public
		 */
		Login.prototype.enterprizeAuthLinkClick = function( e ) {
			e.preventDefault();

			var
				elementClicked = $(e.target),
				authLink = elementClicked.hasClass('jsEnterprizeAuthLink') ? elementClicked : elementClicked.parents('.jsEnterprizeAuthLink')/*(elementClicked.parents('.jsEnterprizeAuthLink').length ? elementClicked.parents('.jsEnterprizeAuthLink').get(0) : null)*/,
				link = authLink.attr('href');
			// end of vars

			// устанавливаем редирект
			if ( link ) {
				this.redirect_to = link;
			}

			// показываем попап
			this.openAuth();

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
			return eval(this.getFormName() + 'Validator');
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
				urlParams = this.getUrlParams();
			// end of vars

			// устанавливаем редирект
			if ( urlParams['redirect_to'] ) {
				this.redirect_to = urlParams['redirect_to'];
			}

			var responseFromServer = function( response ) {
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

					if ( this.form.data('redirect') ) {
						if ( typeof response.data.link !== 'undefined' ) {
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
					formData.push({name: 'redirect_to', value: this.redirect_to ? this.redirect_to : window.location.href});
					$.post(this.form.attr('action'), formData, $.proxy(responseFromServer, this), 'json');
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

	//(function() {
		/*register e-mail check*/
		/*if ( !$('#register_username').length ) {
			return false;
		}

		var chEmail = true, // проверяем ли как e-mail
			register = false,
			firstNameInput = $('#register_first_name'),
			mailPhoneInput = $('#register_username'),
			subscibe = mailPhoneInput.parents('#register-form').find('.bSubscibe'),
			regBtn = mailPhoneInput.parents('#register-form').find('.bigbutton');
		// end of vars
*/
		//subscibe.show();

		/**
		 * переключение типов проверки
		 */
		/*$('.registerAnotherWayBtn').bind('click', function() {
			if ( chEmail ) {
				chEmail = false;
				$('.registerAnotherWay').html('Ваш мобильный телефон');
				$('.registerAnotherWayBtn').html('Ввести e-mail');
				mailPhoneInput.attr('maxlength', 10);
				mailPhoneInput.addClass('registerPhone');
				$('.registerPhonePH').show();
				// subscibe.hide();
			}
			else {
				chEmail = true;
				$('.registerAnotherWay').html('Ваш e-mail');
				$('.registerAnotherWayBtn').html('У меня нет e-mail');
				mailPhoneInput.removeAttr('maxlength');
				mailPhoneInput.removeClass('registerPhone');
				$('.registerPhonePH').hide();
				// subscibe.show();
			}

			mailPhoneInput.val('');
			register = false;
			regBtn.addClass('mDisabled');
		});*/

		/*regBtn.bind('click', function() {
			if ( !register ) {
				return false;
			}

			if ( typeof(_gaq) !== 'undefined' ) {
				var type = ( chEmail ) ? 'email' : 'mobile';

				_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
			}
		});*/

		/**
		 * проверка заполненности инпутов
		 * @param  {Event} e
		 */
		/*var checkInputs = function( e ) {
			if ( chEmail ) { 
				// проверяем как e-mail
				if (	( mailPhoneInput.val().search('@') !== -1 ) && 
						( firstNameInput.val().length > 0 ) ) {
					register = true;
					regBtn.removeClass('mDisabled');
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
			else { 
				// проверяем как телефон
				if (	( (e.which >= 96) && (e.which <= 105) ) ||
						( (e.which >= 48) && (e.which <= 57) ) ||
						(e.which === 8) ) {
					//если это цифра или бэкспэйс
					
				}
				else {
					//если это не цифра
					var clearVal = mailPhoneInput.val().replace(/\D/g,'');

					mailPhoneInput.val(clearVal);
				}

				if ( (mailPhoneInput.val().length === 10) && (firstNameInput.val().length > 0) ) {
					regBtn.removeClass('mDisabled');
					register = true;
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
		};

		mailPhoneInput.bind('keyup', checkInputs);
		firstNameInput.bind('keyup', checkInputs);*/
	//}());
	

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
	// $('.extramenu').find('a').on('click', 'Верхнее меню', categoriesSpy );
	// $('.bCtg').find('a').bind('click', 'Левое меню', categoriesSpy );
	// $('.rubrictitle').find('a').bind('click', 'Заголовок карусели', categoriesSpy );
	// $('a.srcoll_link').bind('click', 'Ссылка Посмотреть все', categoriesSpy );

	/* GA click counter */
	// function gaClickCounter() {
	// 	if ( typeof(_gaq) !== 'undefined' ) {
	// 		var title =  ( $(this).data('title') !== 'undefined' ) ?  $(this).data('title') : 'без названия',
	// 			nowUrl = window.location.href,
	// 			linkUrl = $(this).attr('href');
	// 		// end of vars

	// 		nowUrl.replace('http://www.enter.ru','');

	// 		if ( $(this).data('event') === 'accessorize' ) {
	// 			_gaq.push(['_trackEvent', 'AdvisedAccessorises', nowUrl, linkUrl]);
	// 		}
	// 		else if ( $(this).data('event') === 'related' ) {
	// 			_gaq.push(['_trackEvent', 'AdvisedAlsoBuy', nowUrl, linkUrl]);
	// 		}
	// 		else {
	// 			_gaq.push(['_trackEvent', $(this).data('event'), title,,,false]);
	// 		}
	// 	}

	// 	return true;
	// }

	// $('.gaEvent').bind('click', gaClickCounter );



	/* Authorization process */
	/*$('.open_auth-link').bind('click', function(e) {
		e.preventDefault();
		
		var el = $(this);
		window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus();
	});
		
	$('.bAuthLink').click(function() {
		$('#auth-block').lightbox_me({
			centered: true,
			autofocus: true,
			onLoad: function() {
				$('#auth-block').find('input:first').focus();
			}
		});
		return false;
	});*/

	/*;(function($) {
		$.fn.warnings = function() {
			var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>');

			rwn.css({
				'border': '1px solid red',
				'color': 'red',
				'border-radius': '3px',
				'background-color':'#fff',
				'position': 'absolute',
				'height': '16px',
				'padding': '1px 3px',
				'margin-top': '2px'
			});

			var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px');

			$(this).keypress(function(e) {
				var s = String.fromCharCode( e.which );

				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					if ( !$('#capslock').length ) {
						$(this).after(cln);
					}
				}
				else {
					if ( $('#capslock').length ) {
						$('#capslock').remove();
					}
				}
			});

			$(this).keyup(function(e) {
				if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
					if ( !$('#ruschars').length ) {
						if ( $('#capslock').length ) {
							rwn.css('marginLeft','-116px');
						}
						else {
							rwn.css('marginLeft','-36px');
						}
						$(this).after(rwn);
					}
				}
				else {
					if ( $('#ruschars').length ) {
						$('#ruschars').remove();
					}
				}
			});
		};
	})(jQuery);

	$('#signin_password').warnings();*/

	/*$('#bUserlogoutLink').on('click', function() {
		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['clearIdentity']);
		}
	});*/

	/*$('#login-form, #register-form').data('redirect', true).bind('submit', function(e, param) {
		e.preventDefault();

		var form = $(this); //$(e.target)
		var wholemessage = form.serializeArray();

		form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...');
		wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();

		var authFromServer = function( response ) {
			if ( !response.success ) {
				form.html( $(response.data.content).html() );
				regEmailValid();

				return false;
			}

			if ( 'login-form' == form.attr('id') ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					var type = ( (form.find('#signin_username').val().search('@')) !== -1 ) ? 'email' : 'mobile';

					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', form.find('#signin_username').val() ]);
				}
			}
			else {
				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', form.find('#register_username').val() ]);
				}
			}

			if ( form.data('redirect') ) {
				if (response.data.link) {
					window.location = response.data.link;
				}
				else {
					form.unbind('submit');
					form.submit();
				}
			}
			else {
				$('#auth-block').trigger('close');
				PubSub.publish( 'authorize', response.user );
			}

			//for order page
			if ( $('#order-form').length ) {
				$('#user-block').html('Привет, <strong><a href="'+response.data.link+'">'+response.data.user.first_name+'</a></strong>');
				$('#order_recipient_first_name').val( response.data.user.first_name );
				$('#order_recipient_last_name').val( response.data.user.last_name );
				$('#order_recipient_phonenumbers').val( response.data.user.mobile_phone.slice(1) );
				$('#qiwi_phone').val( response.data.user.mobile_phone.slice(1) );
			}
		};

		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		});
	});*/

	/*$('body').on('click', '#forgot-pwd-trigger', function() {
		$('#reset-pwd-form').show();
		$('#reset-pwd-key-form').hide();
		$('#login-form').hide();
		return false;
	});

	$('body').on('click', '#remember-pwd-trigger, #remember-pwd-trigger2', function() {
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
		return false;
	});*/

	/*$('#reset-pwd-form').submit(function() {
		var form = $(this);

		form.find('.error_list').html('Запрос отправлен. Идет обработка...');
		form.find('.whitebutton').attr('disabled', 'disabled');

		$.post(form.prop('action'), form.serializeArray(), function( resp ) {
			if (resp.success ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					var type = ( (form.find('input.text').val().search('@')) !== -1 ) ? 'email' : 'mobile';

					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
				}
				//$('#reset-pwd-form').hide();
				//$('#login-form').show();
				//alert('Новый пароль был вам выслан по почте или смс');
				var resetForm = $('#reset-pwd-form > div');

				resetForm.find('input').remove();
				resetForm.find('.pb5').remove();
				resetForm.find('.error_list').html('Новый пароль был вам выслан по почте или смс!');
			}
			else {
				var txterr = ( resp.error !== '' ) ? resp.error : 'Вы ввели неправильные данные';

				form.find('.error_list').text( txterr );
				form.find('.whitebutton').removeAttr('disabled');
			}

		}, 'json');

		return false;
	});*/

	
	/* Infinity scroll */
	// var ableToLoad = true;
	// var compact = $('div.goodslist').length;
	// var custom_jewel = $('.bGoodsList').length;

	// function liveScroll( lsURL, filters, pageid ) {
	// 	var params = [];
	// 	/* RETIRED cause data-filter
	// 	if( $('.bigfilter.form').length ) //&& ( location.href.match(/_filter/) || location.href.match(/_tag/) ) )
	// 		params = $('.bigfilter.form').parent().serializeArray()
	// 	*/
	// 	// lsURL += '/' +pageid + '/' + (( compact ) ? 'compact' : 'expanded')
	// 	var tmpnode = ( compact ) ? $('div.goodslist') : $('div.goodsline:last');

	// 	if ( custom_jewel ) {
	// 		tmpnode = $('.bGoodsList');
	// 	}

	// 	var loader =
	// 		'<div id="ajaxgoods" class="bNavLoader">' +
	// 			'<div class="bNavLoader__eIco"><img src="/images/ajar.gif"></div>' +
	// 			'<div class="bNavLoader__eM">' +
	// 				'<p class="bNavLoader__eText">Подождите немного</p>'+
	// 				'<p class="bNavLoader__eText">Идет загрузка</p>'+
	// 			'</div>' +
	// 		'</div>';

	// 	tmpnode.after( loader );

	// 	if ( lsURL.match(/\?/) ) {
	// 		lsURL += '&page=' + pageid;
	// 	}
	// 	else {
	// 		lsURL += '?page=' + pageid;
	// 	}

	// 	$.get( lsURL, params, function(data) {
	// 		if ( data != '' && !data.data ) { // JSON === error
	// 			ableToLoad = true;
	// 			if ( compact || custom_jewel ) {
	// 				tmpnode.append(data);
	// 			}
	// 			else {
	// 				tmpnode.after(data);
	// 			}
	// 		}

	// 		$('#ajaxgoods').remove();

	// 		if( $('#dlvrlinks').length ) {
	// 			var coreid = [];
	// 			var nodd = $('<div>').html( data );

	// 			nodd.find('div.boxhover, div.goodsboxlink').each( function() {
	// 				var cid = $(this).data('cid') || 0;

	// 				if( cid ) {
	// 					coreid.push( cid );
	// 				}
	// 			});

	// 			dajax.post( dlvr_node.data('calclink'), coreid );
	// 		}

	// 	});
	// }

	// if ( $('div.allpager').length ) {
	// 	$('div.allpager').each(function() {
	// 		var lsURL = $(this).data('url') ;
	// 		var filters = '';//$(this).data('filter')
	// 		var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2;
	// 		var vinit = vnext - 1;
	// 		var vlast = parseInt('0' + $(this).data('lastpage') , 10);

	// 		function checkScroll() {
	// 			if ( ableToLoad && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ) {
	// 				ableToLoad = false;

	// 				if ( vlast + vinit > vnext ){
	// 					liveScroll( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ));
	// 				}

	// 				vnext += 1;
	// 			}
	// 		}

	// 		if ( location.href.match(/sort=/) && location.href.match(/page=/) ) { // Redirect on first in sort case
	// 			$(this).bind('click', function(){
	// 				window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );
	// 				location.href = location.href.replace(/page=\d+/,'');
	// 			});
	// 		}
	// 		else {
	// 			$(this).bind('click', function() {
	// 				window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );

	// 				$('.pageslist.bPagesListBottom').hide();

	// 				var next = $('.bPagesListTop .bPagesList__eItem:first');

	// 				if ( next.hasClass('current') ) {
	// 					next = next.next();
	// 				}

	// 				var nextLnk = next.find('.bPagesList__eItemLink')
	// 								.html('<span>123</span>')
	// 								.addClass('borderedR');

	// 				nextLnk.attr('href', nextLnk.attr('href').replace(/page=\d+/,'') );
	
	// 				$('.bPagesList__eItem').remove();
	// 				$('.bPagesList').append( next )
	// 									.find('.bPagesList__eItemLink')
	// 									.bind('click', function(){
	// 										window.docCookies.setItem('infScroll', 0, 0, '/' );
	// 									});
	// 				$('div.allpager').addClass('mChecked');
	// 				checkScroll();
	// 				$(window).scroll( checkScroll );
	// 			});
	// 		}
	// 	});

	// 	if ( window.docCookies.getItem( 'infScroll' ) == 1 ) {
	// 		$('.bAllPager:first').trigger('click');
	// 	}
	// }


	/* Services Toggler */
	// if ( $('.serviceblock').length ) {
	// 	$('.info h3').css('cursor', 'pointer').click( function() {
	// 		$(this).parent().find('> div').toggle();
	// 	});

	// 	if( $('.info h3').length === 1 ) {
	// 		$('.info h3').trigger('click');
	// 	}
	// }
	
	// // /* prettyCheckboxes */ ,
	// $('.form input[type="checkbox"]').prettyCheckboxes();
	// $('.form input[type="radio"]').prettyCheckboxes();


	/* tags */
	// $('.fm').toggle( 
	// 	function(){
	// 		$(this).parent().find('.hf').slideDown();
	// 		$(this).html('скрыть');
	// 	},
	// 	function(){
	// 		$(this).parent().find('.hf').slideUp();
	// 		$(this).html('еще...');
	// 	}
	// );


	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.parent().find('li.hf').slideToggle();
		var link = el.find('a');
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...');
	});

// 	$('.product_filter-block input:submit').addClass('mDisabled');
// 	$('.product_filter-block').on('submit', function(e) {
// 		if ( $('.product_filter-block input:submit').hasClass('mDisabled') ){
// 			e.preventDefault();
// 		} else {
// 			var search = $('.product_filter-block').find('input.orangeIcon').val();
// 			if ( search ) {
// 				if ( $('.currentSearch').length ) {
// 					var newSearch = search + ' ' + $('.currentSearch').data('search-terms');
// 					$('.product_filter-block').find('input.orangeIcon').siblings('input[type="hidden"]').val(newSearch);
// 				}
// 			}
// 		}
// 	});
  
// 	/* Side Filter Block handlers */
	
// 	$('.bigfilter dd[style="display: block;"]').prev('.bigfilter dt').addClass('current');

// 	$('.bigfilter dt').click(function(){
// 		if ( $(this).hasClass('submit') ){
// 			return true;
// 		}

// 		$(this).next('.bigfilter dd').slideToggle(200);
// 		$(this).toggleClass('current');
// 		return false;
// 	});
	
// 	$('.f1list dt B').click(function(){
// 		$(this).parent('dt').next('.f1list dd').slideToggle(200);
// 		$(this).toggleClass('current');
// 		return false;
// 	});

// 	$('.tagslist dt').click(function(){
// 		$(this).next('.tagslist dd').slideToggle(200);
// 		$(this).toggleClass('current');
// 		return false;
// 	});
	
// 	var launch = false;
// 	var activateForm = function() {
// 		if ( !launch ) {
// 			$('.product_filter-block input:submit').removeClass('mDisabled');
// 			launch = true;
// 		}
// 	};

// 	$('.product_filter-block').change(function() {
// 		activateForm();
// 	});

// 	var sphinxSearchValue = null;
// 	if( $('img.orangeIcon').length && $('img.orangeIcon').siblings('input.orangeIcon').length ) {
// 		$('img.orangeIcon').on('click', function(){
// 			var search = $('.product_filter-block').find('input.orangeIcon').val();
// 			if ( search ) {
// 				$('.product_filter-block').find('input[type="submit"]').click();
// 			}
// 		});
// 	}
	
// 	/* Side Filters */
// 	var filterlink = $('.filter .filterlink:first');
// 	var filterlist = $('.filter .filterlist');
// 	var clientBrowser = new brwsr();

// 	if( clientBrowser.isTouch ) {
// 		filterlink.click(function(){
// 			filterlink.hide();
// 			filterlist.show();
// 			return false;
// 		});
// 	}
// 	else {
// 		filterlink.mouseenter(function() {
// 			filterlink.hide();
// 			filterlist.show();
// 		});
// 		filterlist.mouseleave(function() {
// 			filterlist.hide();
// 			filterlink.show();
// 		});
// 	}
	
// 	var ajaxFilterCounter = 0;
	
// 	$('.product_filter-block').bind('change', function(e) {
// 		var el = $(e.target);

// 		if ( el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox'])) ) {
// 			el.trigger('preview');
// 		}
// 	}).bind('preview', function(e) {
// 		var el = $(e.target);
// 		var form = $(this);
// 		var flRes = $('.filterresult');

// 		ajaxFilterCounter++;

// 		var getFiltersResult = function(result) {
// 			var ending = '';

// 			ajaxFilterCounter--;

// 			if ( ajaxFilterCounter > 0 ) {
// 				return;
// 			}

// 			if ( result.success ) {
// 				flRes.hide();

// 				switch ( result.data % 10 ) {
// 					case 1:
// 						ending = 'ь';
// 						break;
// 					case 2: case 3: case 4:
// 						ending = 'и';
// 						break;
// 					default:
// 						ending = 'ей';
// 						break;
// 				}

// 				switch ( result.data % 100 ) {
// 					case 11: case 12: case 13: case 14:
// 						ending = 'ей';
// 						break;
// 				}

// 				var firstli = null;

// 				if ( el.is('div') ) { //triggered from filter slider !
// 					firstli = el;
// 				}
// 				else {
// 					firstli = el.parent().find('> label').first();
// 				}
				
// 				$('.result', flRes).text(result.data);
// 				$('.ending', flRes).text(ending);
// 				flRes.css('top',firstli.offset().top-$('.product_filter-block').offset().top).show();
					
// 				var localTimeout = null;

// 				$('.product_count-block')
// 					.hover(
// 						function() {
// 							if ( localTimeout ) {
// 								clearTimeout( localTimeout );
// 							}
// 						},
// 						function() {
// 							localTimeout = setTimeout( function() {
// 								flRes.hide();
// 							}, 4000  );
// 						}
// 					)
// 					.click(function() {
// 						form.submit();
// 					})
// 					.trigger('mouseout');
// 			}
// 		};

// 		var wholemessage = form.serializeArray();

// 		wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();
// 		$.ajax({
// 			type: 'GET',
// 			url: form.data('action-count'),
// 			data: wholemessage,
// 			success: getFiltersResult
// 		});
// 	});
	
// 	/* Sliders */
// 	$('.sliderbox').each( function() {
// 		var sliderRange = $('.filter-range', this);
// 		var filterrange = $(this);
// 		var papa = filterrange.parent();
// 		var mini = $('.slider-from',  $(this).next() ).val() * 1;
// 		var maxi = $('.slider-to',  $(this).next() ).val() * 1;
// 		var informator = $('.slider-interval', $(this).next());
// 		var from = papa.find('input:first');
// 		var to   = papa.find('input:eq(1)');
// 		informator.html( printPrice( from.val() ) + ' - ' + printPrice( to.val() ) );
// 		// var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
// 		var stepf = papa.find('.slider-interval').data('step');

// 		if ( typeof(stepf) == undefined ) {
// 			var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
// 		}
		
// 		sliderRange.slider({
// 			range: true,
// 			step: stepf,
// 			min: mini,
// 			max: maxi,
// 			values: [ from.val()  ,  to.val() ],
// 			slide: function( e, ui ) {
// 				informator.html( printPrice( ui.values[ 0 ] ) + ' - ' + printPrice( ui.values[ 1 ] ) );
// 				from.val( ui.values[ 0 ] );
// 				to.val( ui.values[ 1 ] );
// 			},
// 			change: function(e, ui) {
// 				if ( parseFloat(to.val()) > 0 ){
// 					from.parent().trigger('preview');
// 					activateForm();
// 				}
// 			}
// 		});

// 	});


// 	/* ---- */
// 	if ( $('.error_list').length && $('.basketheader').length ) {
// 		$.scrollTo( $('.error_list:first'), 300 );
// 	}

// 	/* Cards Carousel  */
// 	function CardsCarousel ( nodes, noajax ) {
// 		var self = this;
// 		var current = 1;

// 		var triggerClick = false;

// 		var refresh_max_page = false;
// 		var current_accessory_category = '';
		

// 		var wi  = nodes.width * 1;
// 		var viswi = nodes.viswidth * 1;

// 		if ( !isNaN($(nodes.times).html()) ) {
// 			var max = $(nodes.times).html() * 1;
// 		}
// 		else {
// 			var max = Math.ceil(wi / viswi);
// 		}

// 		if ( (noajax !== undefined) && (noajax === true) ) {
// 			var buffer = 100;
// 		}
// 		else {
// 			var buffer = ($(nodes.times).parent().parent().hasClass('accessories')) ? 6 : 2;
// 		}

// 		var ajaxflag = false;

// 		this.notify = function() {
// 			$(nodes.crnt).html( current );

// 			if ( refresh_max_page ) {
// 				$(nodes.times).html( max );
// 			}

// 			if ( current == 1 ) {
// 				$(nodes.prev).addClass('disabled');
// 			}
// 			else {
// 				$(nodes.prev).removeClass('disabled');
// 			}

// 			if ( current == max ) {
// 				$(nodes.next).addClass('disabled');
// 			}
// 			else {
// 				$(nodes.next).removeClass('disabled');
// 			}
// 		};

// 		var shiftme = function() {
// 			var boxes = $(nodes.wrap).find('.goodsbox');
// 			$(boxes).hide();
// 			var le = boxes.length;

// 			for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
// 				boxes.eq( j ).show();
// 			}
			
// 			triggerClick = false;
// 		};

// 		$(nodes.next).bind('click', function() {
// 			if ( triggerClick ) {
// 				return false;
// 			}

// 			triggerClick = true;

// 			if ( current >= max && ajaxflag ) {
// 				return false;
// 			}

// 			if ( current + 1 === max ) {

// 				var boxes = $(nodes.wrap).find('.goodsbox');
// 				$(boxes).hide();
// 				var le = boxes.length;
// 				var rest = ( wi % viswi ) ?  wi % viswi  : viswi;

// 				for ( var j = 1; j <= rest; j++ ) {
// 					boxes.eq( le - j ).show();
// 				}
// 				current++;
// 			}
// 			else {

// 				if ( current + 1 >= buffer ) { // we have to get new pull from server
// 					$(nodes.next).css('opacity','0.4'); // addClass dont work ((
// 					ajaxflag = true;
// 					var getData = [];

// 					if( $('form.product_filter-block').length ) {
// 						getData = $('form.product_filter-block').serializeArray();
// 					}

// 					getData.push( {name: 'page', value: buffer+1 } );
// 					getData.push( {name: 'categoryToken', value: current_accessory_category } );

// 					$.get( $(nodes.prev).attr('data-url') , getData, function(data) {
// 						buffer++;
// 						$(nodes.next).css('opacity','1');
// 						ajaxflag = false;
// 						var tr = $('<div>');
// 						$(tr).html( data );
// 						$(tr).find('.goodsbox').css('display','none');
// 						$(nodes.wrap).html( $(nodes.wrap).html() + tr.html() );

// 						// if ( grouped_accessories[current_accessory_category] ) {
// 						// 	grouped_accessories[current_accessory_category]['accessories'] = $(nodes.wrap).html();
// 						// 	grouped_accessories[current_accessory_category]['buffer']++;
// 						// }

// 						tr = null;
// 						current++;
// 						shiftme();
// 					// handle_custom_items()
// 					});
// 				}
// 				else { // we have new portion as already loaded one
// 					current++;
// 					shiftme(); // TODO repair
// 				}
// 			}
// 			self.notify();

// 			return false;
// 		});

// 		$(nodes.prev).click( function() {
// 			if ( current > 1 ) {
// 				current--;
// 				shiftme();
// 				self.notify();
// 			}

// 			return false;
// 		});

// 		$('.categoriesmenuitem').click(function(){
// 			refresh_max_page = true;
// 			var menuitem = $(this);
// 			var width = null;

// 			if ( !$(this).hasClass('active') ) {
// 				$(this).siblings('.active').addClass('link');
// 				$(this).siblings('.active').removeClass('active');
// 				$(this).addClass('active');
// 				$(this).removeClass('link');

// 				current_accessory_category = $(this).attr('data-category-token');

// 				if ( current_accessory_category == undefined ) {
// 					current_accessory_category = '';
// 				}

// 				if ( grouped_accessories[current_accessory_category] ) {
// 					$(nodes.wrap).html(grouped_accessories[current_accessory_category]['accessories']);

// 					if ( !isNaN(grouped_accessories[current_accessory_category]['totalpages']) ) {
// 						max = grouped_accessories[current_accessory_category]['totalpages'];
// 					}
// 					if ( !isNaN(grouped_accessories[current_accessory_category]['quantity']) ) {
// 						width = grouped_accessories[current_accessory_category]['quantity'];
// 					}

// 					current = 1;
// 					shiftme();
// 					self.notify();
// 				}
// 				else {
// 					ajaxflag = true;
// 					var getData = [];
// 					getData.push( {name: 'page', value: 1 } );
// 					getData.push( {name: 'categoryToken', value: current_accessory_category } );
// 					$.get( $(this).attr('data-url') , getData, function(data) {
// 						buffer = 2;
// 						$(nodes.wrap).html(data);
// 						width = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-quantity'), 10);
// 						max = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-total-pages'), 10);

// 						var xhr_category = $($(nodes.wrap).find('.goodsbox')[0]).attr('data-category');

// 						grouped_accessories[xhr_category] = {
// 							'quantity':width,
// 							'totalpages':max,
// 							'accessories':data,
// 							'buffer':buffer
// 						};
// 						current = 1;
// 						shiftme();
// 						self.notify();
// 					}).done(function(data) {
// 						ajaxflag = false;
// 					});
// 				}
// 			}
// 			return false;
// 		});

// 	} // CardsCarousel object

// 	$('.carouseltitle').each( function(){
// 		var tmpline = null;

// 		if( $(this).hasClass('carbig') && !$(this).hasClass('accessories') ) {
// 			tmpline = new CardsCarousel ({
// 				'prev'  : $(this).find('.back'),
// 				'next'  : $(this).find('.forvard'),
// 				'crnt'  : $(this).find('span:first'),
// 				'times' : $(this).find('span:eq(1)'),
// 				'width' : $(this).find('.scroll').data('quantity'),
// 				'wrap'  : $(this).find('~ .bigcarousel').first(),
// 				'viswidth' : 5
// 			});
// 		}
// 		else if( $(this).hasClass('carbig') && $(this).hasClass('accessories') ) {
// 			tmpline = new CardsCarousel ({
// 				'prev'  : $(this).find('.back'),
// 				'next'  : $(this).find('.forvard'),
// 				'crnt'  : $(this).find('span:first'),
// 				'times' : $(this).find('span:eq(1)'),
// 				'width' : $(this).find('.scroll').data('quantity'),
// 				'wrap'  : $(this).find('~ .bigcarousel').first(),
// 				'viswidth' : 4
// 			});
// 		}
// 		else if( $(this).find('.jshm').length ) {
// 			tmpline = new CardsCarousel ({
// 				'prev'  : $(this).find('.back'),
// 				'next'  : $(this).find('.forvard'),
// 				'crnt'  : $(this).find('.none'),
// 				'times' : $(this).find('span:eq(1)'),
// 				'width' : $(this).find('.jshm').html().replace(/\D/g,''),
// //					'width' : $(this).find('.rubrictitle strong').html().replace(/\D/g,''),
// 				'wrap'  : $(this).find('~ .carousel').first(),
// 				'viswidth' : 3
// 			});
// 		}
// 	});


// 	var loadProductRelatedContainer = function loadProductRelatedContainer( container ) {
// 		var tID = 0;

// 		var authFromServer = function( result ) {
// 				container.html( result );
// 				// handle_custom_items();
// 				container.fadeIn();

// 				var tmpline = new CardsCarousel ({
// 					'prev': container.find('.back'),
// 					'next': container.find('.forvard'),
// 					'crnt': container.find('span:first'),
// 					'times': container.find('span:eq(1)'),
// 					'width': container.find('.scroll').data('quantity'),
// 					'wrap': container.find('.bigcarousel'),
// 					'viswidth' : 5
// 				}, true );
// 		};

// 		if ( container.length ) {
// 			tID = setTimeout(function(){
// 				$.ajax({
// 					type: 'GET',
// 					url: container.data('url'),
// 					timeout: 20000,
// 					success: authFromServer
// 				});
// 			},100);
// 		}
// 	};

// 	loadProductRelatedContainer($('#jsAlsoViewedProduct'));
// 	loadProductRelatedContainer($('#product_also_bought-container'));
// 	loadProductRelatedContainer($('#product_user-also_viewed-container'));

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

	/* Delivery Ajax */
	// function Dlvrajax() {
	// 	var that = this;
	// 	this.self = '';
	// 	this.other = [];
	// 	this.node = null;

	// 	this.formatPrice = function(price) {
	// 		if ( typeof price === 'undefined' || price === null ) {
	// 			return '';
	// 		}

	// 		if ( price > 0 ) {
	// 			return ', '+price+' <span class="rubl">p</span>';
	// 		}
	// 		else {
	// 			return ', бесплатно';
	// 		}
	// 	};

	// 	this.printError = function() {
	// 		if ( this.node ) {
	// 			$(this.node).html( 'Стоимость доставки Вы можете уточнить в Контакт-сENTER 8&nbsp;(800)&nbsp;700-00-09' );
	// 		}
	// 	};

	// 	this.post = function( url, coreid ) {
	// 		$.post( url, {ids:coreid}, function(data) {
	// 			if( !('success' in data ) ) {
	// 				that.printError();
	// 				return false;
	// 			}

	// 			if ( !data.success || data.data.length === 0 ) {
	// 				// that.printError()
	// 				if ( that.node ) {
	// 					$(that.node).html('');
	// 				}
	// 				return false;
	// 			}
					
	// 			for ( var i=0; i < coreid.length; i++ ) {
	// 				if ( !data.data[ coreid[i] ] ) {
	// 					continue;
	// 				}

	// 				for( var j in data.data[ coreid[i] ] ) {
	// 					var dlvr = data.data[ coreid[i] ][ j ];
	// 					switch ( dlvr.token ) {
	// 						case 'self':
	// 							that.self = dlvr.date;
	// 							break;
	// 						default:
	// 							that.other.push( { date: dlvr.date, price: dlvr.price, tc: ( typeof(dlvr.transportCompany) !== 'undefined') ? dlvr.transportCompany : false, days: dlvr.days, origin_date:dlvr.origin_date } );
	// 							break;
	// 					}
	// 				}

	// 				that.processHTML( coreid[i] );
	// 				that.self = '';
	// 				that.other = [];
	// 			}
	// 		});
	// 	};
	// } // dlvrajax object

	// if( $('#dlvrlinks').length ) { // Extended List
	// 	var dlvr_node = $('#dlvrlinks');

	// 	Dlvrajax.prototype.processHTML = function( id ) {
	// 		var self = this.self,
	// 			other = this.other;

	// 		var pnode = $( 'div[data-cid='+id+']' ).parent();
	// 		var ul = $('<ul>');

	// 		if ( self ) {
	// 			$('<li>').html( 'Возможен самовывоз ' + self ).appendTo( ul );
	// 		}

	// 		for ( var i = 0; i < other.length; i++ ) {
	// 			var tmp = 'Доставка ' + other[i].date;
	// 			tmp += ( other[i].price ) ? this.formatPrice( other[i].price ) : '';
	// 			$('<li>').html( tmp ).appendTo( ul );
	// 		}

	// 		var uls = pnode.find( 'div.extrainfo ul' );
	// 		uls.html( uls.html() + ul.html() );
	// 	};

	// 	var coreid = [];

	// 	$('div.boxhover, div.goodsboxlink').each( function(){
	// 		var cid = $(this).data('cid') || 0;

	// 		if ( cid ) {
	// 			coreid.push( cid );
	// 		}
	// 	});

	// 	var dajax = new Dlvrajax();

	// 	dajax.post( dlvr_node.data('calclink'), coreid );
	// }



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

	;(function() {
		$(".bGoodsList .bGoodsList__eItem").hover(
		function() {
			$(this).addClass('hover')
		},
		function() {
			$(this).removeClass('hover')
		});

		$(".bigcarousel-brand .goodsbox").hover(
		function() {
			$(this).addClass('hover');
		},
		function() {
			$(this).removeClass('hover');
		});
	}());
});
 
 
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
		subscribeCookieName = 'subscribed',
		lboxCheckSubscribe = function lboxCheckSubscribe( event ) {

		var
			notNowShield = $('.bSubscribeLightboxPopupNotNow'),
			subPopup = $('.bSubscribeLightboxPopup'),
			input = $('.bSubscribeLightboxPopup__eInput'),
			submitBtn = $('.bSubscribeLightboxPopup__eBtn' ),
			subscribe = {
				'show': !window.docCookies.hasItem(subscribeCookieName),
				'agreed': 1 === window.docCookies.getItem(subscribeCookieName)
			},
			inputValidator = new FormValidator({
				fields: [
					{
						fieldNode: input,
						customErr: 'Неправильный емейл',
						required: true,
						email: true,
						validBy: 'isEmail'
					}
				]
			} ),
			runValidation = function runValidation() {
				inputValidator.validate({
					onInvalid: function( err ) {
						console.log('Email is invalid');
						console.log(err);
					},
					onValid: function() {
						console.log('Email is valid');
					}
				});
			};
		// end of vars


		var
			subscribing = function subscribing() {
				var
					email = input.val(),
					url = $(this).data('url');
				//end of vars
				
				if ( submitBtn.hasClass('mDisabled') ) {
					return false;
				}

				$.post(url, {email: email}, function( res ) {
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
				});

				return false;
			},

			subscribeNow = function subscribeNow() {
				subPopup.slideDown(300);

				submitBtn.bind('click', subscribing);

				$('.bSubscribeLightboxPopup__eNotNow').bind('click', function() {
					var url = $(this).data('url');

					subPopup.slideUp(300, subscribeLater);
					window.docCookies.setItem('subscribed', 0, 157680000, '/');
					$.post(url);

					return false;
				});
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

		input.bind('keyup', runValidation);
	};

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
		},

		/**
		 * Скрытие юзербара
		 */
		hideUserbar = function hideUserbar() {
			userBarFixed.slideUp();
		},

		/**
		 * Проверка текущего скролла
		 */
		checkScroll = function checkScroll( e ) {
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
		 * Показ окна о совершенной покупке
		 */
		showBuyInfo = function showBuyInfo() {
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

			var
				/**
				 * Закрытие окна о совершенной покупке
				 */
				closeBuyInfo = function closeBuyInfo() {
					var
						upsaleWrap = wrap.find('.hintDd');
					// end of vars

					upsaleWrap.removeClass('mhintDdOn');
					wrapLogIn.removeClass(openClass);
					wrap.removeClass(openClass);

					buyInfo.slideUp(300, function() {
						checkScroll();
						buyInfo.remove();

						infoShowing = false;
					});

					if ( !userBar.showOverlay ) {
						return;
					}
					
					overlay.fadeOut(300, function() {
						overlay.off('click');
						overlay.remove();

						userBar.showOverlay = false;
					});

					return false;
				};
			// end of function
			

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

			buyInfo.slideDown(300);
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
				authFromServer = function authFromServer( res ) {
					if ( !res.success ) {
						console.warn('удаление не получилось :(');

						return;
					}

					utils.blackBox.basket().deleteItem(res);
				};
			// end of functions

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

				if ( !data.product.article ) {
					console.warn('Не получен article продукта');

					return;
				}

				console.log('Трекинг товара при показе блока рекомендаций');
				// google analytics
				_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.product.article]);
				// Kissmetrics
				_kmq && _kmq.push(['record', 'cart recommendation shown', {'SKU cart rec shown': data.product.article}]);
			};
			//end functions

			console.log(upsale);

			if ( !upsale.url ) {
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
	body.on('addtocart', showBuyInfo);
	body.on('getupsale', showUpsell);


	userBarFixed.on('click', '.jsCartDelete', deleteProductHandler);
	userbarStatic.on('click', '.jsCartDelete', deleteProductHandler);


	if ( userBarFixed.length ) {
		scrollTarget = $(userbarConfig.target);

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			scrollTargetOffset = scrollTarget.offset().top + userBarFixed.height();
			w.on('scroll', checkScroll);
		}
	}

}(window.ENTER));
