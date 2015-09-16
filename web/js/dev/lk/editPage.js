/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		mobilePhoneField = $('#user_mobile_phone'),
		bonusCardFields = $('.jsCardNumber'),
        $input = $('.js-input-control:not(always-show)');
	// end of vars

	var
		setMask = function setMask(field, mask) {
			if ( undefined == typeof(field) || undefined == typeof(mask) ) return;
			field.mask(mask, { placeholder: '*' });
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
        },
        showHideLabels = function showHideLabels() {
            var $this = $(this),
                val = $this.val(),
                $label = $this.parent().find('.js-label-control');

            if (val == ''){
                $label.hide();
            } else {
                $label.show();
            }
        };
	// end of functions

	$.mask.definitions['x'] = '[0-9]';

	// устанавливаем маску для поля "Ваш мобильный телефон"
	//mobilePhoneField.length && mobilePhoneField.mask('8xxxxxxxxxx');

	// устанавливаем маски для карт лояльности
	bonusCardFields.length && bonusCardFields.each(addCardMask);

	$.mask.definitions['x'] = '[0-9]';
	$('.js-lk-mobilePhone, .js-lk-homePhone').mask('+7 (xxx) xxx-xx-xx', {
		autoclear: 0
	});

    $.each($input, showHideLabels);
    $input.on('keyup',showHideLabels);
}());