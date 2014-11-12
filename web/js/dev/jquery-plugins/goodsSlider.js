/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function( $ ) {
	$.fn.goodsSlider = function( params ) {
		var slidersWithUrl = 0,
			slidersRecommendation = 0,
			body = $('body'),
			reqArray = [],
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
				return -1 != $.inArray(type, ["alsoBought", "similar", "alsoViewed"]);
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
						success: function( res ) {
							var
								i, type, callbF, data;

							try{
								for ( i in recommendArray ) {
									type = recommendArray[i].type;
									callbF = recommendArray[i].callback;

									if ( 'undefined' !== typeof(callbF) ) {
										if ( 'undefined' !== typeof(type) && 'undefined' !== typeof(res.recommend) && 'undefined' !== typeof(res.recommend[type]) ) {
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
		var SliderControl = function( mainNode ) {
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
				$self = mainNode,
				sliderParams = $self.data('slider'),
				hasCategory = $self.hasClass('mWithCategory'),

				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemSelector),
                pageTitle = $self.find(options.pageTitleSelector)

				itemW = item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10),
				elementOnSlide = parseInt(wrap.width()/itemW, 10),

				nowLeft = 0;
			// end of vars

            if (sliderParams.count) {
                //pageTitle.text('Страница ' + '1' +  ' из ' + Math.ceil(sliderParams.count / elementOnSlide));
            }

			var
				/**
				 * Переключение на следующий слайд. Проверка состояния кнопок.
				 */
				nextSlide = function nextSlide() {
					if ( $(this).hasClass('mDisabled') ) {
						return false;
					}

					leftBtn.removeClass('mDisabled');

					if ( nowLeft + elementOnSlide * itemW >= slider.width()-elementOnSlide * itemW ) {
						nowLeft = slider.width() - elementOnSlide * itemW;
						rightBtn.addClass('mDisabled');
					}
					else {
						nowLeft = nowLeft + elementOnSlide * itemW;
						rightBtn.removeClass('mDisabled');
					}

					console.info(itemW);
					console.log(elementOnSlide);
					console.log(nowLeft);
					console.log(wrap.width());

					slider.animate({'left': -nowLeft });

                    updatePageTitle(wrap.width(), nowLeft);

					return false;
				},

				/**
				 * Переключение на предыдущий слайд. Проверка состояния кнопок.
				 */
				prevSlide = function prevSlide() {
					if ( $(this).hasClass('mDisabled') ) {
						return false;
					}

					rightBtn.removeClass('mDisabled');

					if ( nowLeft - elementOnSlide * itemW <= 0 ) {
						nowLeft = 0;
						leftBtn.addClass('mDisabled');
					}
					else {
						nowLeft = nowLeft - elementOnSlide * itemW;
						leftBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

                    updatePageTitle(wrap.width(), nowLeft);

					return false;
				},

                updatePageTitle = function updatePageTitle(width, left) {
                    var pageNum = Math.floor(left / width) + 1;

                    if (!sliderParams.count || !elementOnSlide || !pageNum) return;

                    //pageTitle.text('Страница ' + pageNum +  ' из ' + Math.ceil(sliderParams.count / elementOnSlide));
                },

				/**
				 * Вычисление ширины слайдера
				 * 
				 * @param	{Object}	nowItems	Текущие элементы слайдера
				 */
				reWidthSlider = function reWidthSlider( nowItems ) {
					leftBtn.addClass('mDisabled');
					rightBtn.addClass('mDisabled');

					if ( nowItems.length > elementOnSlide ) {
						rightBtn.removeClass('mDisabled');
					}

					slider.width(nowItems.length * itemW);
					nowLeft = 0;
					leftBtn.addClass('mDisabled');
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
					var newSlider;

					if ( !res.success ){
						$self.remove();
						
						return false;
					}

					newSlider = $(res.content)[0];
					$self.before(newSlider);
					$self.remove();
					$(newSlider).goodsSlider();

					if (params.onLoad) {
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
					getSlidersData(sliderParams.url, sliderParams.type, authFromServer);
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
			var $self = $(this);

			new SliderControl($self);
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