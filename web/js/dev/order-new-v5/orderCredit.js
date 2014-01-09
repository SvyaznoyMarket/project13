/**
 * Работа с кредитными брокерами
 */
;(function( window ){
	console.info('orderCredit.js init...');
	
	var
		bankWrap = $('.bBankWrap');
	// end of vars


	var creditInit = function creditInit() {
		var
			bankLink  = bankWrap.find('.bBankLink'),
			bankLinkName = bankWrap.find('.bBankLink__eName'),
			select = bankWrap.find('.bSelect'),
			bankField = $('#selectedBank'),
			bankName = bankWrap.find('.bSelectWrap_eText');
		// end of vars

		var selectBank = function selectBank() {
			var
				chosenBankLink = $('option:selected', select).attr('data-link'),
				chosenBankId = $('option:selected', select).val(),
				chosenBankName = $('option:selected', select).html();
			// end of vars

			bankName.html(chosenBankName);
			bankLinkName.html(chosenBankName);
			bankField.val(chosenBankId);
			bankLink.attr('href', chosenBankLink);
		};

		$('option', select).eq(0).attr('selected','selected');

		select.change(selectBank);
		selectBank();

		window.DirectCredit.init( $('#jsCreditBank').data('value'), $('#creditPrice') );
	};
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));