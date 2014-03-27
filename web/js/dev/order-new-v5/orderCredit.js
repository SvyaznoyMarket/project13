/**
 * Работа с кредитными брокерами
 */
;(function( window ){
	console.info('orderCredit.js init...');
	
	var
		bankWrap = $('div.bBankWrap'),
		//bankWrapInput = bankWrap.find('.bSelectInput'), // уже не используется
		//bankWrapLabel = bankWrapInput.find('.bCustomLabel'), // уже не используется
	// end of vars

		creditInit = function creditInit() {
			var
				bankField = $('#selectedBank'),
				//bankFieldInput = bankWrap.find('.bCustomInput'), // уже не используется
			// end of vars

				selectBank = function selectBank() {
					var
						checked = $('input:checked', bankWrap),
						chosenBankId = checked.val();
					// end of vars

					console.log('selectBank with ID', chosenBankId);

					if ( 'undefined' !== chosenBankId ) {
						// Навесим классы на неотмеченные блоки и уберём с отмеченного
						$('.bSelectInput', bankWrap).addClass('bUnchecked');
						checked.closest('.bSelectInput', bankWrap ).removeClass('bUnchecked');

						bankField.val(chosenBankId);
					}
				};
			// end of vars and functions

			// Если понадобится отмечать чекбокс по умолчанию, раскомментируем эти строки и связанные с ними элементы
			//$(bankFieldInput, bankWrap).eq(0).attr('checked','checked');
			//$(bankWrapLabel, bankWrap).eq(0).addClass('mChecked');
			//selectBank(); // уже не используется

			bankWrap.change(selectBank);

			if ( typeof(window.DirectCredit) && 'function' === typeof(window.DirectCredit.init) ) {
				window.DirectCredit.init( $('#jsCreditBank').data('value'), $('.credit_pay') );
			}
		}; // end of creditInit()
	// end of vars and functions
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));