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
		sclubId,
		sclubEditUrl,
		data,
		sclubCookieName = 'scid',
		userNumber; // номер с пользовательских данных
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
		},

		sclub = {
			init: function () {
				// если пользователь не авторизован ничего не делаем
				if ( ENTER.config.userInfo === false ) return;

				if ( !ENTER.config.userInfo ) {
					$("body").on("userLogged", function() {sclub.action(ENTER.config.userInfo)} );
				}
				else {
					// событие "userLogged" уже произошло
					console.log(ENTER.config.userInfo);
					sclub.action(ENTER.config.userInfo);
				}

				body.on('change', '.jsBonusCard .jsCard', sclub.message);
			},

			action: function (userInfo) {
				if (
					typeof userInfo.id === "undefined" || // не пришли пользовательские данные
					!userInfo.hasOwnProperty('sclubNumber') || // не передан номер
					!window.docCookies.getItem(sclubCookieName)
				) {
					return;
				}

				userNumber = userInfo.sclubNumber;

				// выводим сообщение
				sclub.message();
			},

			/**
			 * Номер Связного в личном кабинете равен номеру переданом от sclub (через get-параметр scid)
			 */
			isNumbersEqual: function (userNumber, cookieNumber) {
				return userNumber == cookieNumber;
			},

			/**
			 * Показать сообщение для карты Связного-клуба
			 */
			message: function () {
				var message, linkYes, linkNo;

				var
					showMessage = function () {
						var
							sclubMsgBlock = $('.jsBonusCard .jsCardMessage .sclub-message'),
							cardNumberField = $('.jsActiveCard .jsCardNumber'),
							msgText;

						if ( sclubMsgBlock.length ) {
							hideMessage();
						}

						msgText = userNumber ? 'Номер карты в ЛК и пришедшим от sclub не совпадают. Заменить номер в ЛК?' : 'Сохранить номер в ЛК?';

						message = $('<div/>', {class: 'sclub-message', text: msgText});

						// linkYes
						message.append('&nbsp;');
						linkYes = $('<a/>', {href: sclubEditUrl, text: 'Да'});
						linkYes.click(function ( e ) {
							e.preventDefault();

							$.post(this.href, {number: window.docCookies.getItem(sclubCookieName)}, function ( res ) {
								if ( !res.success ) {
									if ( res.error ) {
										$('.jsBonusCard .jsCardMessage .sclub-message').html(res.error).addClass('error');
									}

									if ( res.hasOwnProperty('code') && 735 == res.code ) {// 735 - Невалидный номер карты
										window.docCookies.removeItem(sclubCookieName, '/');

										if ( cardNumberField.length ) {
											cardNumberField.val(userNumber).mask(cardNumberField.attr('placeholder'), {placeholder: '*'});
										}

										// очищаем значение с данных по умолчанию
										if ( sclubId ) {
											for ( var i = 0; i < data.length; i++ ) {
												if ( sclubId != data[i].id ) continue;
												data[i].value = userNumber;
											}
										}
									}

									return;
								}

								window.docCookies.removeItem(sclubCookieName, '/');

								hideMessage();
							});
						});
						linkYes.appendTo(message);

						// linkNo
						if ( userNumber ) {
							message.append('&nbsp;');
							linkNo = $('<a/>', {href: sclubEditUrl, text: 'Нет'});
							linkNo.click(function ( e ) {
								e.preventDefault();

								window.docCookies.removeItem(sclubCookieName, '/');

								if ( cardNumberField.length && userNumber ) {
									cardNumberField.val(userNumber).mask(cardNumberField.attr('placeholder'), {placeholder: '*'});
								}

								if ( sclubId ) {
									for ( var i = 0; i < data.length; i++ ) {
										if ( sclubId != data[i].id ) continue;
										data[i].value = userNumber;
									}
								}

								hideMessage();
							});
							linkNo.appendTo(message);
						}

						message.appendTo('.jsBonusCard .jsCardMessage');
					},

					hideMessage = function () {
						var sclubMsgBlock = $('.jsBonusCard .jsCardMessage .sclub-message');

						if ( !sclubMsgBlock.length ) return;
						sclubMsgBlock.remove();
					};
				// end of functions

				// если не выбрана карта Связного, то скрывем сообщение
				if ( sclubId != $('input[name="order[bonus_card_id]"]:checked', '.jsBonusCard').val() ) {
					hideMessage();
					return;
				}

				if ( !window.docCookies.getItem(sclubCookieName) ) {
					return;
				}

				// номера идентичны, ничего не делаем
				if (true === sclub.isNumbersEqual(userNumber, window.docCookies.getItem(sclubCookieName))) {
					return;
				}

				showMessage();
			}
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

	// sclub
	sclubId = bonusCard.data('sclub-id');
	sclubEditUrl = bonusCard.data('sclub-edit-url');
	sclub.init();

	body.on('change', '.jsBonusCard .jsCard', cardChangeHandler);
	console.groupEnd();

})(jQuery);