/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function( $ ) {
	$.fn.goodsSlider = function( params ) {
		params = params || {};

		var
			slidersWithUrl = 0,
			slidersRecommendation = 0,
			body = $('body'),
			reqArray = [],
            urlData = {senders: []},
			recommendArray = [];
		// end of vars

		var
			/**
			 * Слайдер для рекомендаций
			 *
			 * @param type Тип слайдера
			 * @return bool
			 */
			isRecommendation = function isRecommendation( type ) {
				return -1 != $.inArray(type, ['alsoBought', 'similar', 'alsoViewed', 'main', 'search', 'popular', 'personal'/*, 'viewed'*/]);
			};
		// end of functions

		this.each(function() {
			var $self = $(this),
				sliderParams = $self.data('slider');
			// end of vars

			if ( sliderParams.url !== null ) {
				slidersWithUrl++;
			}

			if ( sliderParams.type !== null && isRecommendation(sliderParams.type) ) {
				slidersRecommendation++;
			}

			if (sliderParams.url && sliderParams.sender) {
				sliderParams.sender.type = sliderParams.type;
				urlData.senders.push(sliderParams.sender);
			}

			if (sliderParams.sender2) {
				urlData.sender2 = sliderParams.sender2;
			}

            if (sliderParams.rrviewed) {
                //urlData.rrviewed = sliderParams.rrviewed;
            }
		});

		var getSlidersData = function getSlidersData( url, type, callback ) {
			if ( isRecommendation(type) ) {
				recommendArray.push({
					type: type,
					callback: callback
				});

				if ( recommendArray.length === slidersRecommendation ) {
					$.ajax({
						type: 'GET',
						url: url,
                        data: urlData,
						success: function( res ) {

							var i, type, callbF, data;

							try {
								for ( i in recommendArray ) {
									type = recommendArray[i].type;
									callbF = recommendArray[i].callback;

									if ( 'undefined' !== typeof(callbF) ) {
										if ( typeof type != 'undefined' && typeof res.recommend != 'undefined' && 'undefined' !== typeof(res.recommend[type]) ) {
											callbF(res.recommend[type]);
											data = res.recommend[type].data;
											if ( data ) {
												console.log('Показ товарных рекомендаций от Retailrocket для блока ' + type);
												try {
													rrApi.recomTrack(data.method, data.id, data.recommendations);
												} catch( e ) {
													console.warn('Retailrocket error');
													console.log(e.message);
												}
											}
										}
										else {
											callbF(res);
										}
									}
								}
							}
							catch(e) {
								console.warn('Error in RR recomendations');
								console.log(e);
								callback({'success': false});
							}
						},
						error: function(e) {
							console.warn('Error in RR ajax response');
							console.log(e);
							callback({'success': false});
						}
					});
				}
			}
			else {
				reqArray.push({
					type: 'GET',
					url: url,
					callback: callback
				});

				if ( reqArray.length === (slidersWithUrl - slidersRecommendation) ) {
					window.ENTER.utils.packageReq(reqArray);
				}
			}
		};

		/**
		 * Обработка для каждого элемента попавшего в набор
		 */
		var SliderControl = function() {
			/**
			 * Обработка для каждого элемента попавшего в набор
			 *
			 * @var		{Object}	options			Расширение стандартных значений слайдера пользовательскими настройками
			 * @var	{Object}	$self			Ссылка на текущий элемент из набора
			 * @var	{Object}	sliderParams	Параметры текущего слайдера
			 * @var	{Boolean}	hasCategory		Имеет ли слайдер категории
			 *
			 * @var	{Object}	leftBtn			Ссылка на левую стрелку
			 * @var	{Object}	rightBtn		Ссылка на правую стрелку
			 * @var	{Object}	wrap			Ссылка на обертку слайдера
			 * @var	{Object}	slider			Ссылка на контейнер с товарами
			 * @var	{Object}	item			Ссылка на карточки товаров в слайдере
			 * @var	{Object}	catItem			Ссылка на категории в слайдере
			 * @var	{Object}	pageTitle   	Ссылка на заголовоклисталки в слайдере
			 *
			 * @var	{Number}	itemW			Ширина одной карточки товара в слайдере
			 * @var	{Number}	elementOnSlide	Количество помещающихся карточек на один слайд
			 *
			 * @var	{Number}	nowLeft			Текущий отступ слева
			 */
			var
				options = $.extend(
							{},
							$.fn.goodsSlider.defaults,
							params ),
				$self = this,
				sliderParams = $self.data('slider'),
				hasCategory = $self.hasClass('mWithCategory'),

				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemSelector),
				classDisabled = 'mDisabled disabled',

				nowLeft = 0;
			// end of vars

			var
				calculateItemWidth = function(i) {
					i = i || item;
					return i.width() + parseInt(i.css('marginLeft'),10) + parseInt(i.css('marginRight'),10);
				},
				calculateElementOnSlideCount = function(itemW, wrapEl) {
					wrapEl = wrapEl || wrap;
					return Math.ceil(wrapEl.width()/itemW);
				},
				/**
				 * Переключение на следующий слайд. Проверка состояния кнопок.
				 */
				nextSlide = function nextSlide(e) {
					if ( $(this).hasClass(classDisabled) ) {
						return false;
					}

					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					leftBtn.removeClass(classDisabled);

					if ( nowLeft + elementOnSlide * itemW >= slider.width()-elementOnSlide * itemW ) {
						nowLeft = slider.width() - elementOnSlide * itemW;
						rightBtn.addClass(classDisabled);
					}
					else {
						nowLeft += elementOnSlide * itemW;
						rightBtn.removeClass(classDisabled);
					}

					slider.animate({'left': -nowLeft }, {
						complete: function(){
							sendAnalytic.apply($self)
						}
					});

                    e.preventDefault();
				},

				/**
				 * Переключение на предыдущий слайд. Проверка состояния кнопок.
				 */
				prevSlide = function prevSlide(e) {
					if ( $(this).hasClass(classDisabled) ) {
						return false;
					}

					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					rightBtn.removeClass(classDisabled);

					if ( nowLeft - elementOnSlide * itemW <= 0 ) {
						nowLeft = 0;
						leftBtn.addClass(classDisabled);
					}
					else {
						nowLeft -= elementOnSlide * itemW;
						leftBtn.removeClass(classDisabled);
					}

					slider.animate({'left': -nowLeft }, {
						complete: function(){
							sendAnalytic.apply($self)
						}
					});

                    e.preventDefault();
				},

				/**
				 * Отправка e-comm аналитики при загрузке или прокрутке слайдера
				 * @param action
				 */
				sendAnalytic = function(action) {
					var $slider = $(this),
						itemW = calculateItemWidth($slider.find(options.itemSelector)),
						elementOnSlide = calculateElementOnSlideCount(itemW, $slider.find(options.sliderWrapperSelector)),
						firstIndex = parseInt(nowLeft/itemW, 10),
						lastIndex = firstIndex + elementOnSlide,
						sender = $slider.data('slider').sender,
						position = '';

					if (sender) position = sender.position + '_' + sender.method;

					$slider.find('.jsBuyButton').slice(firstIndex, lastIndex).each(function(i,el){
						ENTER.utils.analytics.addImpression(el, {
							position: firstIndex + i,
							list: position
						})
					});

					body.trigger('trackGoogleEvent', ['Recommendations', action == 'load' ? 'load' : 'scroll', position])
				},

				/**
				 * Отправка аналитики про клике на товаре
				 */
				bindAnalyticOnProductClick = function(){

					var $slider = $(this);

					$slider.find('a:not(.jsBuyButton)').on('click', function(e){
						e.preventDefault();

						var link = $(this).attr('href'),
							sender = $slider.data('slider') ? $slider.data('slider').sender : {},
							productIndex = $(this).closest('li').index(),
							position = sender.position + '_' + sender.method,
							data = $(this).closest('li').find('.jsBuyButton').data('ecommerce');

						ENTER.utils.analytics.addProduct(data, {
							position: productIndex
						});
						ENTER.utils.analytics.setAction('click', {
							list: position
						});
						body.trigger('trackGoogleEvent', {
							category: 'Recommendations',
							action: 'click',
							label: position,
							value: productIndex,
							hitCallback: link
						})
					});
				},

				/**
				 * Вычисление ширины слайдера
				 * 
				 * @param	{Object}	nowItems	Текущие элементы слайдера
				 */
				reWidthSlider = function reWidthSlider( nowItems ) {
					var
						itemW = calculateItemWidth(),
						elementOnSlide = calculateElementOnSlideCount(itemW);

					leftBtn.addClass(classDisabled);
					rightBtn.addClass(classDisabled);

					if ( nowItems.length > elementOnSlide ) {
						rightBtn.removeClass(classDisabled);
					}

					slider.width(nowItems.length * itemW);
					nowLeft = 0;
					leftBtn.addClass(classDisabled);
					slider.css({'left':nowLeft});
					wrap.removeClass('mLoader');
					nowItems.show();
				},

				/**
				 * Показ товаров определенной категории
				 */
				showCategoryGoods = function showCategoryGoods() {
					var nowCategoryId = catItem.filter('.mActive').attr('id'),
						showAll = ( catItem.filter('.mActive').data('product') === 'all' ),
						nowShowItem = ( showAll ) ? item : item.filter('[data-category="'+nowCategoryId+'"]');
					//end of vars
					
					item.hide();
					reWidthSlider( nowShowItem );
				},

				/**
				 * Хандлер выбора категории
				 */
				selectCategory = function selectCategory() {
					catItem.removeClass('mActive');
					$(this).addClass('mActive');
					showCategoryGoods();
				},

				/**
				 * Обработка ответа от сервера
				 * 
				 * @param	{Object}	res	Ответ от сервера
				 */
				authFromServer = function authFromServer( res ) {
					var newSlider, $n;

					if ( !res.success ){
						$self.remove();
						return false;
					}

					newSlider = $(res.content)[0];
					$self.before(newSlider).remove();
					$n = $(newSlider).goodsSlider(options);

					sendAnalytic.call($n, 'load');
					bindAnalyticOnProductClick.call($n);
					if (typeof params.onLoad == 'function') {
						params.onLoad(newSlider);
					}

				},

				/**
				 * Неудача при получении данных с сервера
				 */
				errorStatusCode = function errorStatusCode() {
					console.warn('Слайдер товаров: Неудача при получении данных с сервера');
					
					$self.remove();
				};
			// end of function
			

			if ( sliderParams.url !== null ) {
				if ( typeof window.ENTER.utils.packageReq === 'function' ) {
                    try {
                        if ('viewed' == sliderParams.type) {
                            sliderParams.url += ((-1 != sliderParams.url.indexOf('?')) ? '&' : '?') +
								(sliderParams.rrviewed ? 'rrviewed=' + sliderParams.rrviewed + '&' : '') +
								$.param({senders: [sliderParams.sender]}) +
								(sliderParams.sender2 ? '&' +
								$.param({sender2: sliderParams.sender2}) : '')
							;

                            getSlidersData(sliderParams.url, sliderParams.type, function(res) {
                                res.recommend && res.recommend.viewed && authFromServer(res.recommend.viewed);
                                $('body').trigger('sliderLoaded', {type: 'viewed'});
                            });
                        } else {
                            getSlidersData(sliderParams.url, sliderParams.type, authFromServer);
                        }
                    } catch (e) { console.error(e); }
				}
				else {
					$.ajax({
						type: 'GET',
						url: sliderParams.url,
						success: authFromServer,
						statusCode: {
							500: errorStatusCode,
							503: errorStatusCode,
							504: errorStatusCode
						}
					});
				}
			}
			else {
				if ( hasCategory ) {
					showCategoryGoods();
				}
				else {
					reWidthSlider( item );
				}
			}

			rightBtn.on('click', nextSlide);
			leftBtn.on('click', prevSlide);
			catItem.on('click', selectCategory);
		};

		return this.each(function() {
			SliderControl.apply($(this));
		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.slideItem_btn-prv',
		rightArrowSelector: '.slideItem_btn-nxt',
		sliderWrapperSelector: '.slideItem_inn',
		sliderSelector: '.slideItem_lst',
		itemSelector: '.slideItem_i',
		categoryItemSelector: '.bGoodsSlider__eCatItem',
        pageTitleSelector: '.slideItem_cntr'
	};

})(jQuery);