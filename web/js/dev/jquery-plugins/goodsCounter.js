;(function($) {
	/**
	 * jQuery плагин спиннера количества товаров
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @param		{Object}	plusBtn					Элемент кнопки увеличения
	 * @param		{Object}	minusBtn				Элемент кнопки уменьшения
	 * @param		{Object}	input					Поле ввода
	 * @param		{String}	counterGroupName		Имя группы спиннеров, к которой принадлежит данный спиннер
	 * @param		{Object}	counterGroup			Все спиннеры к группе которой принадлежит данный спиннер
	 * @param		{Number}	timeout_id				Идентификатор таймаута
	 * @return		{jQuery}
	 */
	$.fn.goodsCounter = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsCounter.defaults,
							params),
				$self = $(this),

				plusBtn = $self.find(options.plusSelector),
				minusBtn = $self.find(options.minusSelector),
				input = $self.find(options.inputSelector),

				counterGroupName = $self.attr('data-spinner-for'),
				counterGroup = $('[data-spinner-for="'+counterGroupName+'"]'),

				timeout_id = 0;
			// end of vars


				/**
				 * Срабатывание функции обратного вызова onChange
				 * 
				 * @param	{Number}	count	Текущее значение в поле ввода
				 */
			var changeHandler = function changeHandler( count ) {
					clearTimeout(timeout_id);
					timeout_id = setTimeout(function() {
						counterGroup.find('input').val( count );
						options.onChange( count );
					}, 400);
				},

				/**
				 * Обработчик увеличения количества в поле ввода
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				plusHandler = function plusHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ) {
						return false;
					}

					if ( (nowCount * 1) + 1 > options.maxVal ) {
						return false;
					}

					nowCount++;
					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик уменьшения количества в поле ввода
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				minusHandler = function minusHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ){
						return false;
					}

					if ( (nowCount * 1) - 1 < 1 ){
						return false;
					}

					nowCount--;
					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик отпускания клавиши клавиатуры
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				keyupHandler = function keyupHandler( e ) {
					var nowCount = input.val();

					e.stopPropagation();

					if ( $self.hasClass('mDisabled') ){
						return false;
					}

					nowCount = input.val();

					if ( (nowCount * 1) < 1 ) {
						nowCount = 1;
					}

					if ( (nowCount * 1) > options.maxVal ) {
						nowCount = options.maxVal;
					}

					input.val( nowCount );
					changeHandler( nowCount );

					return false;
				},

				/**
				 * Обработчик нажатия клавиши клавиатуры
				 * 
				 * @param	{Event}	e	Данные события
				 * @return	{Boolean}
				 */
				keydownHandler = function keydownHandler( e ) {
					e.stopPropagation();

					if ( e.which === 38 ) { // up arrow
						plusBtn.trigger('click');
						return false;
					}
					else if ( e.which === 40 ) { // down arrow
						minusBtn.trigger('click');
						return false;
					}
					else if ( !(( (e.which >= 48) && (e.which <= 57) ) ||  //num keys
								( (e.which >= 96) && (e.which <= 105) ) || //numpad keys
								(e.which === 8) ||
								(e.which === 46)) 
							) {
						return false;
					}
				},

				/**
				 * Обновление количества в поле ввода, если товар уже лежит в корзине. Вызывается событием «updatespinner» у body
				 * 
				 * @param	{Event}		e			Данные события
				 * @param	{Array}		products	Массив продуктов
				 * @param	{Object}	spinner		Ссылка на спиннеры принадлежащие купленному товару
				 * @param	{Object}	input		Поля которые необходимо обновить
				 */
				updatespinner = function updatespinner( e, products ){
					var i = 0,
						spinner,
						input;
					// end of vars

					for ( i = products.product.length - 1; i >= 0; i-- ) {
						spinner = $('[data-spinner-for="'+products.product[i].id+'"]');
						spinner.addClass('mDisabled');
						input = spinner.find('input');
						input.val(products.product[i].quantity).attr('disabled','disabled');
					}
				};
			//end of functions

			plusBtn.bind('click', plusHandler);
			minusBtn.bind('click',minusHandler);
			input.bind('keydown', keydownHandler);
			input.bind('keyup', keyupHandler);
			$('body').bind('updatespinner', updatespinner);
		});
	};

	$.fn.goodsCounter.defaults = {
		// callbacks
		plusSelector:'.bCountSection__eP',
		minusSelector:'.bCountSection__eM',
		inputSelector:'.bCountSection__eNum',

		maxVal: 99,

		onChange: function(){}
	};

})(jQuery);