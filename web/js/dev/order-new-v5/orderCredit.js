/**
 * Работа с кредитными брокерами
 */
;(function( window ){
	console.info('orderCredit.js init...');
	
	var
		bankWrap = $('.bBankWrap'),
		bankWrapInput = bankWrap.find('.bSelectInput');
		bankWrapLabel = bankWrapInput.find('.bCustomLabel');
	// end of vars
		
	var creditInit = function creditInit() {
		var
			bankField = $('#selectedBank'),
			bankFieldInput = bankWrap.find('.bCustomInput');
		// end of vars

		var selectBank = function selectBank() {
			var
				chosenBankId = $('input:checked', bankWrap).val();
			// end of vars

			bankField.val(chosenBankId);
		};

		$(bankFieldInput, bankWrap).eq(0).attr('checked','checked');
		$(bankWrapLabel, bankWrap).eq(0).addClass('mChecked');
		
		bankWrap.change(selectBank);
		selectBank();

		window.DirectCredit.init( $('#jsCreditBank').data('value'), $('.credit_pay') );
		
	};
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));