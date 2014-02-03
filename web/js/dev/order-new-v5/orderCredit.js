/**
 * Работа с кредитными брокерами
 */
;(function( window ){
	console.info('orderCredit.js init...');
	
	var
		bankWrap = $('.bBankWrap'),
		bankWrapInput = bankWrap.find('.bSelectInput');
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

		//$(bankFieldInput, bankWrap).eq(0).attr('checked','checked');
		
		bankWrap.change(selectBank);
		selectBank();

		window.DirectCredit.init( $('#jsCreditBank').data('value'), $('.credit_pay') );
		
	};

	var creditItemSelect = function creditItemSelect() {
		var 
			bankWrapLabel = bankWrapInput.find('.bCustomLabel');
		// end of vars
		
		bankWrapLabel.css({'opacity' : '0.4'});
		$(this).children(bankWrapLabel).css({'opacity' : '1'});
	};

	bankWrapInput.click(creditItemSelect);
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));