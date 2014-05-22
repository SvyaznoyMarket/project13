/**
 * Карты лояльности
 *
 * @author    Shaposhnik Vitaly
 * @requires  jQuery
 */
(function($) {
	var
		body = $('body'),
		loyaltyCard = $('.jsLoyaltyCard'),
		data;
	// end of vars

	var
		cardChangeHandler = function cardChangeHandler() {
			var
				newCardData,
				cardIndex,
				activeCard = $('.jsActiveCard'),
				activeCardNumber = $('.jsActiveCard .jsCardNumber'),
				activeCardDescription = $('.jsActiveCard .jsDescription');
			// end of vars

			if ( !activeCard.length ) {
				return;
			}

			cardIndex = $('.jsLoyaltyCard input[name="loyalty_card"]').index(this);
			if ( -1 == cardIndex ) {
				return;
			}

			if ( !data.hasOwnProperty(cardIndex) ) {
				return;
			}

			newCardData = data[cardIndex];

			console.info('cardChange');
			console.log(newCardData);

			if ( newCardData.hasOwnProperty('image') ) {
				activeCard.css('background', 'url(' + newCardData.image + ') 260px -3px no-repeat');
			}

			if ( activeCardNumber.length && newCardData.hasOwnProperty('mask') ) {
				activeCardNumber.attr('placeholder', newCardData.mask);

				activeCardNumber.val('');
				activeCardNumber.mask(newCardData.mask, {placeholder: '*'});
			}

			if ( activeCardDescription.length && newCardData.hasOwnProperty('description') ) {
				activeCardDescription.text(newCardData.description);
			}
		},

		addMaskForDefaultCard = function addMaskForDefaultCard() {
			var
				activeCardNumber = $('.jsActiveCard .jsCardNumber');
			// end of vars

			if ( !activeCardNumber.length ) {
				return;
			}

			if ( !data.hasOwnProperty(0) || !data[0].hasOwnProperty('mask') ) {
				return;
			}

			console.info('addMaskForDefaultCard');
			console.log(data[0].mask);
			activeCardNumber.mask(data[0].mask, {placeholder: '*'});
		};
	// end of functions

	if ( !loyaltyCard.length ) {
		return;
	}

	data = loyaltyCard.data('value');
	if ( !data.length ) {
		return;
	}

	console.groupCollapsed('LoyaltyCard');

	$.mask.definitions['x'] = '[0-9]';
	addMaskForDefaultCard();

	body.on('change', '.jsLoyaltyCard input[name="loyalty_card"]', cardChangeHandler);
	console.groupEnd();

})(jQuery);