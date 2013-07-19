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

					if ( !res.success ){
						return false;
					}

					newSlider = $(res.content);
					$self.before(newSlider).remove();
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
				$self.remove();
				// $.ajax({
				// 	type: 'GET',
				// 	url: sliderParams.url,
				// 	success: authFromServer
				// });
			}

			rightBtn.bind('click', nextSlide);
			leftBtn.bind('click', prevSlide);
			catItem.bind('click', selectCategory);
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