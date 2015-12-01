/**
 * Форма подписки на уцененные товары
 * Cтраница /refurbished-sale
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var discountSubscribing = function( e ){
		e.preventDefault();

		var form = $('.js-refurbished-form'),
			wholemessage = form.serializeArray();
		// end of vars

		var authFromServer = function( response ) {
			if ( !response.success ) {
				return false;
			}

			form.find('label').hide();
			form.find('#subscribeSaleSubmit').empty().addClass('font18').html('Спасибо, уже скоро в вашей почте информация об уцененных товарах.');
		};

		wholemessage['redirect_to'] = form.find('[name="redirect_to"]:first').val();

		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		});

		return false;
	};

	$(document).ready(function(){
		if ( !$('.js-refurbished-form').length ) {
			return false;
		}

		$('.js-refurbished-form').bind('submit', discountSubscribing);

		ENTER.utils.analytics.ga.getClientId(function(gaClientId) {
			$('.js-refurbished-form-gaClientId').val(gaClientId);
		});
	});
}());