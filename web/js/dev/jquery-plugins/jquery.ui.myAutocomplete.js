!function($) {
	$.widget('custom.myAutocomplete', $.ui.autocomplete, {
		options: {
			renderMenu: null
		},
		close: function() {
			var result = this._superApply(arguments);
			this.term = ''; // Чтобы повторный ввод той же строки приводил к появлению выпадающего списка
			return result;
		},
		_renderMenu: function(ul, items) {
			var result = this._superApply(arguments);

			if (this.options.renderMenu) {
				this.options.renderMenu(ul, items);
			}

			return result;
		}
	});
}(jQuery);