/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		mobilePhoneField = $('.jsPersonPhone'),
		bonusCardFields = $('.jsCardNumber');
	// end of vars

	var
		setMask = function setMask(field, mask) {
			if ( undefined == typeof(field) || undefined == typeof(mask) ) return;
			field.mask(mask, { placeholder: '_' });
		},

		addCardMask = function addCardMask() {
			var
				self = $(this),
				mask = self.data('mask');
			// end of vars

			if ( undefined == typeof(mask) ) {
				return;
			}

			setMask(self, mask);
		};
	// end of functions

	$.mask.definitions['x'] = '[0-9]';

	// устанавливаем маску для поля "Ваш мобильный телефон"
	mobilePhoneField.length && mobilePhoneField.mask('8 (xxx) xxx-xx-xx');

	// устанавливаем маски для карт лояльности
	bonusCardFields.length && bonusCardFields.each(addCardMask);
}());