/**
 * Sertificate
 *
 * 
 * @requires	jQuery
 */
;(function( global ) {
	if ( !$('#paymentMethod-10').length ) {
		console.warn('нет метода оплаты сертификатом');

		return false;
	}

	var sertificateWrap = $('#paymentMethod-10').parent(),
		code = sertificateWrap.find('.mCardNumber'),
		pin = sertificateWrap.find('.mCardPin'),
		fieldsWrap = sertificateWrap.find('.bPayMethodAction'),
		urlCheck = fieldsWrap.attr('data-url');
	// end of vars


	var SertificateCard = (function() {

		var checked = false,
			processTmpl = 'processBlock';
		// end of vars

		var getCode = function getCode() {
				return code.val().replace(/[^0-9]/g,'');
			},

			getPIN = function getPIN() {
				return pin.val().replace(/[^0-9]/g,'');
			},

			getParams = function getParams() {
				return { code: getCode() , pin: getPIN() };
			},

			isActive = function isActive() {
				if ( checked &&
					getCode() !== '' &&
					getCode().length === 14 &&
					getPIN() !== '' &&
					getPIN().length === 4 ) {
					return true;
				}

				return false;
			},

			checkCard = function checkCard() {
				if ( pin.val().length !== 4) {
					console.warn('пин код мал');
					return false;
				}
				
				if ( !code.val().length ) {
					console.warn('номер карты не введен');
					return false;
				}

				setProcessingStatus( 'mOrange', 'Проверка по номеру карты' );
				$.post( urlCheck, getParams(), function( data ) {
					if ( !('success' in data) ) {
						return false;
					}

					if ( !data.success ) {
						var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR';

						setProcessingStatus( 'mRed', err );

						return false;
					}

					setProcessingStatus( 'mGreen', data.data );
				});
				// pin.focus()
			},

			setProcessingStatus = function setProcessingStatus( status, data ) {
				console.info('setProcessingStatus');

				var blockProcess = $('.process').first(),
					options = { typeNum: status };

				if( !blockProcess.hasClass('picked') ) {
					blockProcess.remove();
				}

				switch ( status ) {
					case 'mOrange':
						options.text = data;
						checked = false;

						break;
					case 'mRed':
						options.text = 'Произошла ошибка: ' + data;
						checked = false;

						break;
					case 'mGreen':
						if ( 'activated' in data ) {
							options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!';
						}
						else {
							options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum;
						}

						checked = true;

						break;
				}

				fieldsWrap.after( tmpl( processTmpl, options) );

				if ( typeof data['activated']  !== 'undefined' ) {
					$('.process').first().addClass('picked');
				}
			};
		// end of function

		return {
			checkCard: checkCard,
			setProcessingStatus: setProcessingStatus,
			isActive: isActive,
			getCode: getCode,
			getPIN: getPIN
		};
	})();

	code.bind('change',function() {
		SertificateCard.checkCard();
	});

	pin.bind('change',function() {
		SertificateCard.checkCard();
	});

	pin.mask('9999', { completed: SertificateCard.checkCard, placeholder: '*' } );
}(this));