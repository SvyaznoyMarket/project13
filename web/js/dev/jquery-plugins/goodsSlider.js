/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function($) {
	$.fn.goodsSlider = function(params) {

		/**
		 * Обработка для каждого элемента попавшего в набор
		 *
		 * @param	{Object}	options			Расширение стандартных значений слайдера пользовательскими настройками
		 * @param	{Object}	$self			Ссылка на текущий элемент из набора
		 * @param	{Object}	sliderParams	Параметры текущего слайдера
		 * @param	{Boolean}	hasCategory		Имеет ли слайдер категории
		 * 
		 * @param	{Object}	leftBtn			Ссылка на левую стрелку
		 * @param	{Object}	rightBtn		Ссылка на правую стрелку
		 * @param	{Object}	wrap			Ссылка на обертку слайдера
		 * @param	{Object}	slider			Ссылка на контейнер с товарами
		 * @param	{Object}	item			Ссылка на карточки товаров в слайдере
		 * @param	{Object}	catItem			Ссылка на категории в слайдере
		 * 
		 * @param	{Number}	itemW			Ширина одной карточки товара в слайдере
		 * @param	{Number}	elementOnSlide	Количество помещающихся карточек на один слайд
		 * 
		 * @param	{Number}	nowLeft			Текущий отступ слева
		 */
		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsSlider.defaults,
							params ),
				$self = $(this),
				sliderParams = $self.data('slider'),
				hasCategory = $self.hasClass('mWithCategory'),

				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemselector),

				itemW = item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10),
				elementOnSlide = wrap.width()/itemW,

				nowLeft = 0;
			// end of vars

				/**
				 * Переключение на следующий слайд. Проверка состояния кнопок.
				 */
			var nextSlide = function nextSlide() {
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

					slider.animate({'left': -nowLeft });

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

					return false;
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

					res = {"success":true,"content":"\u003Cdiv class=\u0022bGoodsSlider clearfix\u0022  data-slider=\u0022{\u0026quot;count\u0026quot;:null,\u0026quot;limit\u0026quot;:null,\u0026quot;url\u0026quot;:null}\u0022\u003E\n            \u003Ch3 class=\u0022bHeadSection\u0022\u003E\u041f\u043e\u0445\u043e\u0436\u0438\u0435 \u0442\u043e\u0432\u0430\u0440\u044b\u003C\/h3\u003E\n    \n    \n    \u003Cdiv class=\u0022bSliderAction\u0022\u003E\n\n        \u003Cdiv class=\u0022bSliderAction__eInner\u0022\u003E\n            \u003Cul class=\u0022bSliderAction__eList clearfix\u0022\u003E\n                                        \u003Cli class=\u0022bSliderAction__eItem\u0022 data-category=\u0022slider-51ed166155101-category-966\u0022\u003E\n                    \u003Cdiv class=\u0022product__inner\u0022\u003E\n                        \u003Ca class=\u0022productImg\u0022 href=\u0022\/product\/jewel\/zolotoe-koltso-s-brilliantom-2030000138586\u0022\u003E\u003Cimg src=\u0022http:\/\/fs02.enter.ru\/1\/1\/120\/32\/165036.jpg\u0022 alt=\u0022\u0417\u043e\u043b\u043e\u0442\u043e\u0435 \u043a\u043e\u043b\u044c\u0446\u043e \u0441 \u0431\u0440\u0438\u043b\u043b\u0438\u0430\u043d\u0442\u043e\u043c \u0022 \/\u003E\u003C\/a\u003E\n                        \u003Cdiv class=\u0022productName\u0022\u003E\u003Ca href=\u0022\/product\/jewel\/zolotoe-koltso-s-brilliantom-2030000138586\u0022\u003E\u0417\u043e\u043b\u043e\u0442\u043e\u0435 \u043a\u043e\u043b\u044c\u0446\u043e \u0441 \u0431\u0440\u0438\u043b\u043b\u0438\u0430\u043d\u0442\u043e\u043c \u003C\/a\u003E\u003C\/div\u003E\n                        \u003Cdiv class=\u0022productPrice\u0022\u003E\u003Cspan class=\u0022price\u0022\u003E6 500 \u003Cspan class=\u0022rubl\u0022\u003Ep\u003C\/span\u003E\u003C\/span\u003E\u003C\/div\u003E\n\n                        \u003Cdiv class=\u0022bWidgetBuy__eBuy btnBuy\u0022\u003E\n    \u003Ca href=\u0022\/cart\/add-product\/86101\u0022 class=\u0022id-cartButton-product-86101 jsBuyButton btnBuy__eLink\u0022 data-group=\u002286101\u0022\u003E\u041a\u0443\u043f\u0438\u0442\u044c\u003C\/a\u003E\n\u003C\/div\u003E\n\n                    \u003C\/div\u003E\n                \u003C\/li\u003E\n                        \u003C\/ul\u003E\n        \u003C\/div\u003E\n\n        \u003Cdiv class=\u0022bSliderAction__eBtn mPrev mDisabled\u0022\u003E\u003Cspan\u003E\u003C\/span\u003E\u003C\/div\u003E\n        \u003Cdiv class=\u0022bSliderAction__eBtn mNext mDisabled\u0022\u003E\u003Cspan\u003E\u003C\/span\u003E\u003C\/div\u003E\n    \u003C\/div\u003E\n\n\u003C\/div\u003E\u003C!--\/product accessory section --\u003E\n\n"};

					if ( !res.success ){
						return false;
					}

					newSlider = $(res.content);
					$self.before(newSlider);
					newSlider.goodsSlider();
				};
			// end of function
		

			if ( hasCategory ) {
				showCategoryGoods();
			}
			else {
				reWidthSlider( item );
			}

			if (sliderParams. url) {
				// $self.remove();
				$.ajax({
					type: 'GET',
					url: sliderParams.url,
					success: authFromServer
				});
			}

			rightBtn.on('click', nextSlide);
			leftBtn.on('click', prevSlide);
			catItem.on('click', selectCategory);
		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.bSliderAction__eBtn.mPrev',
		rightArrowSelector: '.bSliderAction__eBtn.mNext',
		sliderWrapperSelector: '.bSliderAction__eInner',
		sliderSelector: '.bSliderAction__eList',
		itemSelector: '.bSliderAction__eItem',
		categoryItemselector: '.bGoodsSlider__eCatItem'
	};

})(jQuery);