/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		mobilePhoneField = $('#user_mobile_phone'),
		cardField = $('#user_sclub_card_number');
	// end of vars

	$.mask.definitions['n'] = '[0-9]';

	// устанавливаем маску для поля "Ваш мобильный телефон"
	//mobilePhoneField.length && mobilePhoneField.mask('8nnnnnnnnnn');

	// устанавливаем маску для поля "Номер карты Связной-Клуб"
	cardField.length && cardField.mask('2 98nnnn nnnnnn', { placeholder: '*' });
}());