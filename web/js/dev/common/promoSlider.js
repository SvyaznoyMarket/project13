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
			toSlide = 0,
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

			disabledBtns = false,// Активность кнопок для пролистования и пагинатора.

			// Настройки для аналитики слайдера
			analyticsConfig = typeof promoCatalog.data('analytics-config') !== "undefined" ? promoCatalog.data('analytics-config') : false,
            // Буфер, для коллекций. Пока _gaq не подгрузился, делаем запись в буфер. Затем трекаем все скопом.
			tchiboAnalyticsBuffer = [],
			categoryToken = typeof promoCatalog.data('category-token') !== "undefined" ? promoCatalog.data('category-token') : '',
			documentHidden = false;
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

					var $slide = $(slideTmpl).appendTo('.bPromoCatalogSliderWrap');
					ko.applyBindings(ENTER.UserModel, $slide[0]);

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
					time = 3000,
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

				if ( data.hasOwnProperty(slide) && data[slide].hasOwnProperty('time') ) {
					time = data[slide]['time'];
				}

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

					if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
						_gaq.push(['_trackEvent', 'slider view', 'tchibo', getSlideIndex(slide) + 1 + '']);
					}
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
				
				if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'slider view', 'tchibo', getSlideIndex(slide) + 1 + '']);
				}
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
				
				if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'slider view', 'tchibo', link + 1 + '']);
				}
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
					buff,
					slideData;
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

				slideData = data[slideId];
				if ( (slideData.hasOwnProperty('title') && slideData.hasOwnProperty('time')) && tchiboAnalytics.checkRule('collection_view') ) {
					tchiboAnalytics.collectionShow(slideData.title, (slideId*1)+1, slideData.time);
				}
			},

			getSlideIndex = function(slide) {
				var slideId = parseInt(slide);

				if (activeCarousel) {
					if ( slide > slider_SlideCount - 1 ) {
						slide = slider_SlideCount - 1;
					} else if ( slide < 0 ) {
						slide = 0;
					}

					slideId = parseInt($(".jsPromoCatalogSliderWrap .bPromoCatalogSliderWrap_eSlide").eq(slide).attr("id").replace('slide_id_', ''));
				}

				return slideId;
			},

			tchiboAnalytics = {
				init: function() {
					if ( !tchiboAnalytics.isAnalyticsEnabled ){
						return;
					}

					var
						collectionClickHandler = function() {
							var
								self = $(this),
								slide = self.parent('.bPromoCatalogSliderWrap_eSlide'),
								slideId,
								slideData;
							// end of vars

							if ( !slide.length || !slide.attr('id') ) {
								return;
							}

							slideId = slide.attr('id').replace('slide_id_', '');
							slideData = data[slideId];

							if ( slideData.hasOwnProperty('title') ) {
								tchiboAnalytics.collectionClick(slideData.title, (slideId*1)+1);
							}
						},

						productClickHandler = function() {
							var
								self = $(this),
								slide = self.parents('.bPromoCatalogSliderWrap_eSlide'),
								slideElementId,
								slideId,
								slideData,
								productIndex;
							// end of vars

							if ( !slide.length || !slide.attr('id') ) {
								return;
							}

							slideElementId = slide.attr('id');
							slideId = slideElementId.replace('slide_id_', '');
							slideData = data[slideId];

							productIndex = $('.mTchiboSlider #'+slideElementId+' .prodItem > a').index(this);
							if ( -1 == productIndex ) {
								return;
							}

							if ( slideData.hasOwnProperty('title') && undefined != typeof(slideData.products[productIndex].name) ) {
								tchiboAnalytics.productClick(slideData.title, slideData.products[productIndex].name, (productIndex*1)+1);
							}
						};
					// end of functions

					if ( tchiboAnalytics.checkRule('collection_click') ) {
						body.on('click', '.mTchiboSlider .bPromoCatalogSliderWrap_eSlideLink', collectionClickHandler);
					}

					if ( tchiboAnalytics.checkRule('product_click') ) {
						body.on('click', '.mTchiboSlider .prodItem > a', productClickHandler);
					}

					tchiboAnalytics.pageVisibility();
				},

				/**
				 * Управление аналитикой в зависимости от присутствия пользователя на вкладке текущей страницы (Page Visibility API)
				 */
				pageVisibility: function() {
					var
						hidden, visibilityChange;
					// end of vars

					var
						handleVisibilityChange = function() {
							documentHidden = document[hidden] ? true : false;
						};
					// end of functions

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						!analyticsConfig.hasOwnProperty('use_page_visibility') ||
						true != analyticsConfig.use_page_visibility
					) {
						return;
					}

					if ( typeof document.hidden !== "undefined" ) { // Opera 12.10 and Firefox 18 and later support
						hidden = "hidden";
						visibilityChange = "visibilitychange";
					} else if ( typeof document.mozHidden !== "undefined" ) {
						hidden = "mozHidden";
						visibilityChange = "mozvisibilitychange";
					} else if ( typeof document.msHidden !== "undefined" ) {
						hidden = "msHidden";
						visibilityChange = "msvisibilitychange";
					} else if ( typeof document.webkitHidden !== "undefined" ) {
						hidden = "webkitHidden";
						visibilityChange = "webkitvisibilitychange";
					}

					handleVisibilityChange();

					if ( typeof document.addEventListener === "undefined" || typeof hidden === "undefined" ) {
						// requires a browser, such as Google Chrome or Firefox, that supports the Page Visibility API.
					} else {
						// Handle page visibility change
						document.addEventListener(visibilityChange, handleVisibilityChange, false);
					}
				},

				/**
				 * @param collection_name		название коллекции
				 * @param collection_position	позиция в слайдере
				 * @param delay					текущая задержка на данном слайдере
				 */
				collectionShow: function(collection_name, collection_position, delay) {	},

				/**
				 * @param collection_name		название коллекции
				 * @param collection_position	позиция в слайдере
				 */
				collectionClick: function(collection_name, collection_position) {
					var item;

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						'undefined' == typeof(_gaq) ||
						'undefined' == typeof(collection_name) ||
						'undefined' == typeof(collection_position)
						) {
						return;
					}

					item = ['_trackEvent', 'collection_click', collection_name+'_'+collection_position]

					console.info('TchiboSliderAnalytics collection_click');
					console.log(item);
					_gaq.push(item);
				},

				/**
				 * @param collection_name	название коллекции
				 * @param item_name			название товара
				 * @param position			позиция товара на слайдере (1, 2, 3 слева направо)
				 */
				productClick: function(collection_name, item_name, position) {
					var item;

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						'undefined' == typeof(_gaq) ||
						'undefined' == typeof(collection_name) ||
						'undefined' == typeof(item_name) ||
						'undefined' == typeof(position)
						) {
						return;
					}

					item = ['_trackEvent', 'item_click', collection_name+'_'+item_name, position.toString()];

					console.info('TchiboSliderAnalytics item_click');
					console.log(item);
					_gaq.push(item);
				},

				isAnalyticsEnabled: function() {
					return analyticsConfig && analyticsConfig.hasOwnProperty('enabled') && true == analyticsConfig.enabled;
				},

				checkRule: function(rule) {
					if (
						tchiboAnalytics.isAnalyticsEnabled &&
						typeof rule !== "undefined" &&
						analyticsConfig.hasOwnProperty(rule) && true == analyticsConfig[rule].enabled &&
						((true == analyticsConfig[rule].tchiboOnly && 'tchibo' === categoryToken) || (true != analyticsConfig[rule].tchiboOnly))
					) {
						return true;
					}

					return false;
				}
			};
		// end of functions

		$(function(){
			initSlider(); //запуск слайдера

			tchiboAnalytics.init();

			body.on('click', '.bPromoCatalogSlider_eArrow', btnsClick);
			body.on('click', '.bPaginator_eLink', paginatorClick);

			if ( activeHash ) {
				hash = window.location.hash;
				if ( hash.indexOf('slide') + 1 ) {
					toSlide = parseInt(hash.slice(6), 10) - 1;
					moveSlide(toSlide);
				}
			}

			setScrollInterval(toSlide);

			// аналитика показа первого слайда
			if ( data.hasOwnProperty(toSlide) && data[toSlide].hasOwnProperty('title') && data[toSlide].hasOwnProperty('time') && tchiboAnalytics.checkRule('collection_view') ) {
				tchiboAnalytics.collectionShow(data[toSlide].title, ((toSlide*1)+1), data[toSlide].time);
			}
		});
	}
})(jQuery);