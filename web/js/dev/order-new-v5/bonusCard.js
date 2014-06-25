/**
 * Карты лояльности
 *
 * @author    Shaposhnik Vitaly
 * @requires  jQuery
 */
(function($) {
	var
		body = $('body'),
		bonusCard = $('.jsBonusCard'),
		data;
	// end of vars

	var
		cardChangeHandler = function cardChangeHandlerF() {
			var
				newCardData,
				cardIndex,
				activeCard = $('.jsActiveCard'),
				activeCardNumber = $('.jsActiveCard .jsCardNumber'),
				activeCardDescription = $('.jsActiveCard .jsDescription');
			// end of vars

			var
				changeCardImage = function changeCardImageF() {
					if ( !activeCard.length || !newCardData.hasOwnProperty('image') ) {
						return;
					}

					activeCard.css('background', 'url(' + newCardData.image + ') 260px -3px no-repeat');
				},

				changeCardMask = function changeCardMaskF() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.attr('placeholder', newCardData.mask);
					activeCardNumber.mask(newCardData.mask, {placeholder: '*'});
				},

				changeCardDescription = function changeCardDescriptionF() {
					if ( !activeCardDescription.length || !newCardData.hasOwnProperty('description') ) {
						return;
					}

					activeCardDescription.text(newCardData.description);
				},

				changeCardValue = function changeCardValueF() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('value') ) {
						return;
					}

					activeCardNumber.val(newCardData.value);
				};
			// end of function

			if ( !activeCard.length ) {
				return;
			}

			cardIndex = $('.jsBonusCard .jsCard').index(this);
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

		setDefaults = function setDefaultsF() {
			var
				activeCardNumber = $('.jsActiveCard .jsCardNumber');
			// end of vars

			var
				setMask = function setMaskF() {
					if ( !data[0].hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.mask(data[0].mask, {placeholder: '*'});
				},

				setValue = function setValueF() {
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

	if ( !bonusCard.length ) {
		return;
	}

	data = bonusCard.data('value');
	if ( !data.length ) {
		return;
	}

	console.groupCollapsed('BonusCard');

	$.mask.definitions['x'] = '[0-9]';
	setDefaults();

	body.on('change', '.jsBonusCard .jsCard', cardChangeHandler);
	console.groupEnd();

})(jQuery);