;(function($){
	/**
	 * Плагин кастомных радио кнопок
	 *
	 * @author		Zaytsev Alexandr
	 * @requires	jQuery
	 * @return		{jQuery}
	 */
	$.fn.customRadio = function( params ) {

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
				deselectNode = $('.'+options.deselectClass+'[name="'+groupName+'"]');
			// end of vars
			

				/**
				 * Удаление классов с лэйблов.
				 * 
				 * @param	{Boolean}	all		Все ли пометки нужно удалить
				 */
			var removeChecked = function removeChecked( all ) {
					inputGroup.each(function(){
						var _this = $(this);

						var unmarkLabel = function unmarkLabel() {
							var thisId = _this.attr('id'),
								thisLabel = $('label[for="'+thisId+'"]');
							// end of vars

							thisLabel.removeClass(options.checkedClass);
						};

						if ( _this.attr('checked') === undefined ) {
							unmarkLabel();
						}
						else if ( all ) {
							unmarkLabel();
							_this.removeAttr('checked');
							options.onUncheckedGroup( _this );
						}
					});
				},

				/**
				 * Обработчик кнопки снимающей выделение со всех радио кнопок
				 */
				deselectHandler = function deselectHandler() {
					deselectNode.hide();
					removeChecked( true );
					return false;
				},

				/**
				 * Обработчик изменений состояний радио кнопок
				 */
				changeHandler = function(){
					if ( $self.attr('checked') === undefined ) {
						return false;
					}

					label.addClass( options.checkedClass );
					removeChecked( false );
					deselectNode.show();
					options.onChecked( $self );
				};
			// end of functions

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