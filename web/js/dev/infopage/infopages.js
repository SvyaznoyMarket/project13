$(document).ready(function(){
	var
		subscribeBtn = $('.subscribe-form__btn');

	if ( subscribeBtn.length ) {
		var
			form = $('.subscribe-form'),
			input = $('.subscribe-form__email') || form.find('input[name="email"]'),
			channel = form.find('input[name="channel"]').val() || 1;
		// end of vars
		
		var subscribing = function subscribing() {
			var url = $(this).data('url'),
				email = input.val(),
				utm_source = document.location.search;
			// end of vars

			if ( email && email.search('@') !== -1 ) {
				$.post(url, {email: email, channel: channel}, function(res){
					if( !res.success ) {
						return false;
					}

					form.html('<div class="subscribe-form__title">Спасибо! подтверждение подписки отправлено на указанный e-mail</div>');
					window.docCookies.setItem('subscribed', channel, 157680000, '/');

					// form.after('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');

					if( typeof(_gaq) !== 'undefined' ){
						_gaq.push(['_trackEvent', 'subscribe', email, utm_source]);
					}
				});
			}
			else {
				input.addClass('mError');
			}
			return false;
		};

		subscribeBtn.bind('click', subscribing);

	}



	/**
	 * Бесконечный скролл
	 */
	$('.infiniteCarousel').infiniteCarousel();

	/**
	 * Получение продуктов
	 */
	if ( $('.getProductList').length ) {
		// console.log('yes!')
		$('.getProductList').each(function() {
			var wrapper = $(this),
				productList = wrapper.data('product'),
				url = '/products/widget/'+productList;
			// end of vars

			$.get(url, function( res ) {
				if ( !res.success ) {
					return false;
				}

				wrapper.html(res.content);
			});
		});
	}


	/**
	 * form register corporate
	 */
	if ( $('#corp_select').length ) {
        $('form[action="/b2b"]').bind('submit', function(){
            if ( $('#corp_select').find('option:selected').val() === 'Другая форма' ) {
                return false;
            }
        });

		$('#corp_select').change(function() {
			if ( $(this).find('option:selected').val() === 'Другая форма' ) {
				$('#corpNotice').lightbox_me({
					centered: true,
					closeSelector: '.close'
				});
			}
		});
	}

	/* iPadPromo*/
	if ( $('#oneClickPromo').length ) {
		$('.halfline .bOrangeButton.active').click( function() {
			var halfline = $(this).parent().parent(),
				ipad = {};
			// end of vars
			
			ipad.token = halfline.find('.ttl').text();
			ipad.price = halfline.find('.price').text();
			ipad.image = halfline.find('img').attr('src');
			$('#ipadwrapper').html( tmpl('ipad', ipad) );
			$('#order1click-container-new').lightbox_me({});
		});
		
		$('#oneClickPromo').submit( function(e) {
			e.preventDefault();

			return false;
		});
		
		if ( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = '[0-9]';
			$('#phonemask').mask('+7 (nnn) nnn-nn-nn');
		}
		
		var emptyValidation = function emptyValidation( node ) {
			if ( node.val().replace(/\s/g,'') === '' ) {
				node.addClass('mEmpty');
				node.after( $('<span class="mEmpty">(!) Пожалуйста, верно заполните поле</span>') );

				return false;
			}
			else {
				if ( node.hasClass('mEmpty') ) {
					node.removeClass('mEmpty');
					node.parent().find('.mEmpty').remove();
				}
			}

			return true;
		};

		$('#oneClickPromo input[type=text]').change( function() {
			emptyValidation( $(this) );
		});
		
		var _f_success = function _f_success() { 
			$('#f_success').show();
			$('#f_init').hide();
		};
		
		var _f_error = function _f_error( button ) { 
			$('#oneClickPromo input[type=text]').removeAttr('disabled');
			$('#f_init h2').text('Произошла ошибка :( Попробуйте ещё');
			button.text('Отправить предзаказ');
		};
		
		$('.bBigOrangeButton').click( function(e) {
			var button = $(this),
				data = $('#oneClickPromo').serializeArray(),
				url = $('#oneClickPromo').attr('action');
			// end of vars
			
			e.preventDefault();

			$('#oneClickPromo input[type=text]').trigger('change');

			if ( $('.mEmpty').length ) {
				return;
			}

			button.text('Идёт отправка...');

			$('#oneClickPromo input[type=text]').attr('disabled','disabled');

			$.ajax( {
				url: url,
				data: data,
				success: function( resp ) {
					if ( !( 'success' in resp ) ) {
						_f_error(button);

						return false;
					}

					if ( resp.success !== 'ok' ) {
						_f_error(button);

						return false;
					}

					_f_success();

					return true;
				}
			});
			
			
		});
	}

	/* Credits inline */
	if ( $('.bCreditLine').length ) {
		document.getElementById('requirementsFullInfoHref').style.cursor = 'pointer';

		$('#requirementsFullInfoHref').bind('click', function() {
			$('.bCreditLine2').toggle();
		});

		var creditOptions = $('#creditOptions').data('value');
		var bankInfo = $('#bankInfo').data('value');
		var relations = $('#relations').data('value');

		for ( var i = 0; i < creditOptions.length; i++){
			var creditOption = creditOptions[i];

			$('<option>').val(creditOption.id).text(creditOption.name).appendTo('#productSelector');
		}

		$('#productSelector').change(function() {
			var key = $(this).val();
			var bankRelations = relations[key];

			$('#bankProductInfoContainer').empty();

			for ( var i in bankRelations ) {
				var dtmpl = {},
					programNames = '';
				// end of vars

				dtmpl.bankName = bankInfo[i].name;
				dtmpl.bankImage = bankInfo[i].image;
				

				for ( var j in bankRelations[i] ) {
					programNames += '<h4>' + bankInfo[i].programs[bankRelations[i][j]].name + '</h4>\r\n<ul>';

					for ( var k in bankInfo[i].programs[bankRelations[i][j]].params ) {
						programNames += '\t<li>' + bankInfo[i].programs[bankRelations[i][j]].params[k] + '</li>\r\n';
					}

					programNames += '</ul>';
				}

				dtmpl.programNames = programNames;

				var show_bank = tmpl('bank_program_list_tmpl', dtmpl);

				$('#bankProductInfoContainer').append(show_bank);
			}

			$('#bankProductInfoContainer').append('<p class="ac mb25"><a class="bBigOrangeButton" href="' + creditOptions[key - 1]['url'] + '">' + creditOptions[key - 1]['button_name'] + '</a></p>');
		});
	}

	/* Mobile apps inline */
	if ( $('.bMobileApps').length ) {
		var openSelector = '';

		var hideQRpopup = function hideQRpopup() {
			$(openSelector).hide();
		};

		var showQRpopup = function showQRpopup( selector ) {
			openSelector = selector;
			$(selector).show();

			return false;
		};

		$('body').bind('click.mob', hideQRpopup);
		$('div.bMobDown').click(function( e ) {
			e.stopPropagation();
		});

		$('.bMobDown__eClose').click( function() {
			hideQRpopup();

			return false;
		});

		$('.android-load').click(function () {
			showQRpopup('.android-block');

			return false;
		});

		$('.iphone-load').click(function () {
			showQRpopup('.iphone-block');

			return false;
		});

		$('.symbian-load').click(function () {
			showQRpopup('.symbian-block');

			return false;
		});
	}
});
