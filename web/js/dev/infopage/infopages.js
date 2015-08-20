$(document).ready(function(){

    var menuItems = $('.menu-item'),
		subscribeBtn = $('.subscribe-form__btn');

    // Выделение активного пункта в боковом меню
    $.each(menuItems, function() {
        var $this = $(this);
        if ($this.find('a').attr('href') == location.pathname ) {
            $this.addClass('active');
            return false;
        }
    });

	if ( subscribeBtn.length ) {
		var
			form = $('.subscribe-form'),
			input = $('.subscribe-form__email') || form.find('input[name="email"]'),
			channel = form.find('input[name="channel"]').val() || 1;
		// end of vars
		
		var subscribing = function subscribing() {
			var url = $(this).data('url'),
				email = input.val(),
				utm_source = document.location.search,
				error_msg = $(this).data('error-msg'),
				data = {};
			// end of vars

			if ( email && email.search('@') !== -1 ) {
				data = {email: email, channel: channel};

				if ('undefined' !== typeof(error_msg) && error_msg) {
					data.error_msg = error_msg;
				}

				$.post(url, data, function(res){
                    var errorDiv = form.find('.formErrorMsg');

					if ( res.hasOwnProperty('data') && undefined != typeof(res.data) ) {
						form.html('<div class="subscribe-form__title">' + res.data + '</div>');
					}

                    if ( res.error ) {
                        if (errorDiv.length == 0) {
                            form.append($('<div class="formErrorMsg" style="margin-left: 135px; clear: both; color: red;"/>').text(res.error));
                        } else {
                            errorDiv.text(res.error).show();
                        }
                        form.find('.formErrorMsg').delay(2000).slideUp(600);
                    }

					if( !res.success ) {
						return false;
					}

					window.docCookies.setItem('subscribed', channel, 157680000, '/');

					// form.after('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');

					if (typeof(_gaq) != 'undefined') {
						if (location.pathname == '/enter-friends') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_enter_friends', email]);
						} else if (location.pathname == '/special_offers') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_special_offers', email]);
						}
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
		document.getElementById('requirementsFullInfoHref').style.cursor = 'pointer'; // TODO перенести в css в scms

		var creditOptions = $('#creditOptions').data('value');
		var bankInfo = $('#bankInfo').data('value');
		var relations = $('#relations').data('value');

		if (creditOptions) {
			for ( var i = 0; i < creditOptions.length; i++){
				var creditOption = creditOptions[i];

				$('<option>').val(creditOption.id).text(creditOption.name).appendTo('#productSelector');
			}
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
    //Попап стоимости доставки
    $('.js-tarifs-popup-show').on('click',function(){
        var popup = $('.js-tarifs-popup').clone();
        $('.js-tarifs-popup').remove();
        $('body').append('<div class="js-tarifs-overlay"></div>');
        $('body').append(popup).addClass('body-fixed');

        $('.js-tarifs-popup').show();

    });
    $('body').on('click', '.js-tarifs-popup .popup-closer', function(){
        $('.js-tarifs-popup').hide();
        $('.js-tarifs-overlay').remove();
        $('body').removeClass('body-fixed');
    });
    $('body').on('click', '.js-tarifs-overlay', function(){
        $('.js-tarifs-popup').hide();
        $(this).remove();
        $('body').removeClass('body-fixed');
    });
    $('body').on('keyup', '.js-tarifs-search', function(){
       var $this = $(this),
           value = $this.val().toLowerCase(),
           noResult = true;

        $('.tarifs-search__i').each(function(){//Пробегаем по списку букв
            var $letter = $(this).find('.tarifs-search__letter'),
                $cityList = $(this).find('.tarifs-search-city__i'),
                isLetterShown = false;

            $($cityList).each(function(){//Пробегаем по списку городов
                var name = $(this).find('.tarifs-search-city__name').text().toLowerCase();

                if (name.indexOf(value) == 0){
                    $(this).show();
                    isLetterShown = true;//Если хотя бы один город на эту букву найден - будем отображать и букву
                    noResult = false;//Если хотя бы один город найден, не будем выводить сообщение о том, что ничего не найдено
                } else {
                    $(this).hide();
                }
            });

            isLetterShown ? $letter.show() : $letter.hide();

        });

        noResult ? $('.tarifs-search__no-result').show() : $('.tarifs-search__no-result').hide();
    });

    $(document).ready(function(){
        $('.subscribe-block[data-type="background"]').each(function(){
            var $this = $(this),
                $scrolled = $this.find('.scrolled-bg'),
                $window = $(window),
                lastScrollTop = $(window).scrollTop(),
                delta = 0;


                $window.scroll(function() {

                    var st = $(window).scrollTop();
                    delta = lastScrollTop - st;

                    lastScrollTop = st;

                    if ( ($window.scrollTop() + $window.height()) >= $this.offset().top ){


                        var prevCoords = $scrolled.css('backgroundPosition').split(' '),
                            prevY = parseInt( prevCoords[1] ),
                            coords = 'center '+ (prevY + (delta / $this.data('speed')) ) + 'px';

                        $scrolled.css({ 'background-position': coords });
                    }

                });

        });
    });
});
