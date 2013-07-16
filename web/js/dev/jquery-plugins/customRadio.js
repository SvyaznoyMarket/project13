;(function($){
	/**
	 * Плагин кастомных радио кнопок
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @return		{jQuery}
	 */
	$.fn.customRadio = function(params) {

		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.customRadio.defaults,
							params),
				$self = $(this),
				id = $self.attr('id'),
				label = $('label[for="'+id+'"]'),
				groupName = $self.attr('name'),
				inputGroup = $('input[name="'+groupName+'"]'),
				deselectNode = $('.'+options.deselectClass+'[name="'+groupName+'"]'),

				/**
				 * Удаление классов с лэйблов.
				 * 
				 * @param	{Boolean}	all		Все ли пометки нужно удалить
				 */
				removeChecked = function(all){
					inputGroup.each(function(){
						var _this = $(this);

						var unmarkLabel = function(){
							var thisId = _this.attr('id');
							var thisLabel = $('label[for="'+thisId+'"]');
							thisLabel.removeClass(options.checkedClass);
						};

						if (_this.attr('checked') === undefined){
							unmarkLabel();
						}
						else if(all){
							unmarkLabel();
							_this.removeAttr('checked');
							options.onUncheckedGroup(_this);
						}
					});
				},

				/**
				 * Обработчик кнопки снимающей выделение со всех радио кнопок
				 */
				deselectHandler = function(){
					deselectNode.hide();
					removeChecked(true);
					return false;
				},

				/**
				 * Обработчик изменений состояний радио кнопок
				 */
				changeHandler = function(){
					if ($self.attr('checked') === undefined){
						return false;
					}
					label.addClass(options.checkedClass);
					removeChecked(false);
					deselectNode.show();
					options.onChecked($self);
				};
			// end of vars

			$self.bind('change', changeHandler);
			deselectNode.bind('click', deselectHandler);
		});
	};

	$.fn.customRadio.defaults = {
		checkedClass: 'mChecked',
		deselectClass: 'bDeSelect',
		// callbacks
		onChecked: function(){},
		onUncheckedGroup: function(){}
	};
})(jQuery);