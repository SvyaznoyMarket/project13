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

			var
				changeCardImage = function changeCardImage() {
					if ( !activeCard.length || !newCardData.hasOwnProperty('image') ) {
						return;
					}

					activeCard.css('background', 'url(' + newCardData.image + ') 260px -3px no-repeat');
				},

				changeCardMask = function changeCardMask() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.attr('placeholder', newCardData.mask);
					activeCardNumber.mask(newCardData.mask, {placeholder: '*'});
				},

				changeCardDescription = function changeCardDescription() {
					if ( !activeCardDescription.length || !newCardData.hasOwnProperty('description') ) {
						return;
					}

					activeCardDescription.text(newCardData.description);
				},

				changeCardValue = function changeCardValue() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('value') || '' == newCardData.value ) {
						return;
					}

					activeCardNumber.val(newCardData.value);
				};
			// end of function

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

			changeCardValue();
			changeCardImage();
			changeCardMask();
			changeCardDescription();
		},

		setDefaults = function setDefaults() {
			var
				activeCardNumber = $('.jsActiveCard .jsCardNumber');
			// end of vars

			var
				setMask = function setMask() {
					if ( !data[0].hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.mask(data[0].mask, {placeholder: '*'});
				},

				setValue = function setValue() {
					if ( !data[0].hasOwnProperty('value') ) {
						return;
					}

					activeCardNumber.val(data[0].value);
				};
			// end of functions

			if ( !activeCardNumber.length || !data || !data.hasOwnProperty(0) ) {
				return;
			}

			console.info('setDefaults');
			console.log(data[0]);

			setValue();
			setMask();
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
	setDefaults();

	body.on('change', '.jsLoyaltyCard input[name="loyalty_card"]', cardChangeHandler);
	console.groupEnd();

})(jQuery);