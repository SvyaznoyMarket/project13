$(document).ready(function(){

	(function(){
		/*register e-mail check*/
		if ( !$('#register_username').length ){
			return false;
		}

		var chEmail = true; // проверяем ли как e-mail
		var register = false;
		var firstNameInput = $('#register_first_name');
		var mailPhoneInput = $('#register_username');
		var subscibe = mailPhoneInput.parents('#register-form').find('.bSubscibe');
		var regBtn = mailPhoneInput.parents('#register-form').find('.bigbutton');

		subscibe.show();

		/**
		 * переключение типов проверки
		 */
		$('.registerAnotherWayBtn').bind('click', function() {
			if ( chEmail ) {
				chEmail = false;
				$('.registerAnotherWay').html('Ваш мобильный телефон');
				$('.registerAnotherWayBtn').html('Ввести e-mail');
				mailPhoneInput.attr('maxlength', 10);
				mailPhoneInput.addClass('registerPhone');
				$('.registerPhonePH').show();
				// subscibe.hide();
			}
			else {
				chEmail = true;
				$('.registerAnotherWay').html('Ваш e-mail');
				$('.registerAnotherWayBtn').html('У меня нет e-mail');
				mailPhoneInput.removeAttr('maxlength');
				mailPhoneInput.removeClass('registerPhone');
				$('.registerPhonePH').hide();
				// subscibe.show();
			}

			mailPhoneInput.val('');
			register = false;
			regBtn.addClass('mDisabled');
		});

		regBtn.bind('click', function() {
			if ( !register ) {
				return false;
			}

			if ( typeof(_gaq) !== 'undefined' ) {
				var type = ( chEmail ) ? 'email' : 'mobile';

				_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
			}
		});

		/**
		 * проверка заполненности инпутов
		 * @param  {Event} e
		 */
		var checkInputs = function(e){
			if (chEmail){ 
				// проверяем как e-mail
				if (	( mailPhoneInput.val().search('@') !== -1 ) && 
						( firstNameInput.val().length > 0 ) ) {
					register = true;
					regBtn.removeClass('mDisabled');
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
			else { 
				// проверяем как телефон
				subscibe.hide();
				if (	( (e.which >= 96) && (e.which <= 105) ) ||
						( (e.which >= 48) && (e.which <= 57) ) ||
						(e.which === 8) ) {
					//если это цифра или бэкспэйс
					
				}
				else {
					//если это не цифра
					var clearVal = mailPhoneInput.val().replace(/\D/g,'');

					mailPhoneInput.val(clearVal);
				}

				if ( (mailPhoneInput.val().length === 10) && (firstNameInput.val().length > 0) ) {
					regBtn.removeClass('mDisabled');
					register = true;
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
		};

		mailPhoneInput.bind('keyup',function(e){
			checkInputs(e);
		});

		firstNameInput.bind('keyup',function(){
			checkInputs();
		});
	}());
	

	/**
	 * Подписка
	 */
	$('body').on('click', '.bSubscibe', function() {
		if ( $(this).hasClass('checked') ) {
			$(this).removeClass('checked');
			$(this).find('.subscibe').removeAttr('checked');
			$(this).find('input[name="subscribe"]').val(0);
		} else {
			$(this).addClass('checked');
			$(this).find('.subscibe').attr('checked','checked');
			$(this).find('input[name="subscribe"]').val(1);
		}

		return false;
	});


	/* GA categories referrer */
	function categoriesSpy( e ) {
		if ( typeof(_gaq) !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'CategoryClick', e.data, window.location.pathname ]);
		}

		return true;
	}

	$('.bMainMenuLevel-1__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-2__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-3__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.breadcrumbs').first().find('a').bind( 'click', 'Хлебные крошки сверху', categoriesSpy );
	$('.breadcrumbs-footer').find('a').bind( 'click', 'Хлебные крошки снизу', categoriesSpy );
	$('.extramenu').find('a').on('click', 'Верхнее меню', categoriesSpy );
	$('.bCtg').find('a').bind('click', 'Левое меню', categoriesSpy );
	$('.rubrictitle').find('a').bind('click', 'Заголовок карусели', categoriesSpy );
	$('a.srcoll_link').bind('click', 'Ссылка Посмотреть все', categoriesSpy );

	/* GA click counter */
	function gaClickCounter() {
		if ( typeof(_gaq) !== 'undefined' ) {
			var title =  ( $(this).data('title') !== 'undefined' ) ?  $(this).data('title') : 'без названия',
				nowUrl = window.location.href,
				linkUrl = $(this).attr('href');
			// end of vars

			nowUrl.replace('http://www.enter.ru','');

			if ( $(this).data('event') === 'accessorize' ) {
				_gaq.push(['_trackEvent', 'AdvisedAccessorises', nowUrl, linkUrl]);
			}
			else if ( $(this).data('event') === 'related' ) {
				_gaq.push(['_trackEvent', 'AdvisedAlsoBuy', nowUrl, linkUrl]);
			}
			else {
				_gaq.push(['_trackEvent', $(this).data('event'), title,,,false]);
			}
		}

		return true;
	}

	$('.gaEvent').bind('click', gaClickCounter );



	/* Authorization process */
	$('.open_auth-link').bind('click', function(e) {
		e.preventDefault();
		
		var el = $(this);
		window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus();
	});
		
	$('#auth-link').click(function() {
		$('#auth-block').lightbox_me({
			centered: true,
			autofocus: true,
			onLoad: function() {
				$('#auth-block').find('input:first').focus();
			}
		});
		return false;
	});

	;(function($) {
		$.fn.warnings = function() {
			var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>');

			rwn.css({
				'border': '1px solid red',
				'color': 'red',
				'border-radius': '3px',
				'background-color':'#fff',
				'position': 'absolute',
				'height': '16px',
				'padding': '1px 3px',
				'margin-top': '2px'
			});

			var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px');

			$(this).keypress(function(e) {
				var s = String.fromCharCode( e.which );

				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					if ( !$('#capslock').length ) {
						$(this).after(cln);
					}
				}
				else {
					if ( $('#capslock').length ) {
						$('#capslock').remove();
					}
				}
			});

			$(this).keyup(function(e) {
				if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
					if ( !$('#ruschars').length ) {
						if ( $('#capslock').length ) {
							rwn.css('marginLeft','-116px');
						}
						else {
							rwn.css('marginLeft','-36px');
						}
						$(this).after(rwn);
					}
				}
				else {
					if ( $('#ruschars').length ) {
						$('#ruschars').remove();
					}
				}
			});
		};
	})(jQuery);

	$('#signin_password').warnings();

	$('#bUserlogoutLink').on('click', function() {
		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['clearIdentity']);
		}
	});

	$('#login-form, #register-form').data('redirect', true).bind('submit', function(e, param) {
		e.preventDefault();

		var form = $(this); //$(e.target)
		var wholemessage = form.serializeArray();

		form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...');
		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val();

		var authFromServer = function( response ) {
			if ( !response.success ) {
				form.html( $(response.data.content).html() );
				regEmailValid();

				return false;
			}

			if ( 'login-form' == form.attr('id') ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					var type = ( (form.find('#signin_username').val().search('@')) !== -1 ) ? 'email' : 'mobile';

					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', form.find('#signin_username').val() ]);
				}
			}
			else {
				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['identify', form.find('#register_username').val() ]);
				}
			}

			if ( form.data('redirect') ) {
				if (response.data.link) {
					window.location = response.data.link;
				}
				else {
					form.unbind('submit');
					form.submit();
				}
			}
			else {
				$('#auth-block').trigger('close');
				PubSub.publish( 'authorize', response.user );
			}

			//for order page
			if ( $('#order-form').length ) {
				$('#user-block').html('Привет, <strong><a href="'+response.data.link+'">'+response.data.user.first_name+'</a></strong>');
				$('#order_recipient_first_name').val( response.data.user.first_name );
				$('#order_recipient_last_name').val( response.data.user.last_name );
				$('#order_recipient_phonenumbers').val( response.data.user.mobile_phone.slice(1) );
				$('#qiwi_phone').val( response.data.user.mobile_phone.slice(1) );
			}
		};

		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		});
	});

	$('#forgot-pwd-trigger').on('click', function() {
		$('#reset-pwd-form').show();
		$('#reset-pwd-key-form').hide();
		$('#login-form').hide();
		return false;
	});

	$('#remember-pwd-trigger,#remember-pwd-trigger2').click(function() {
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
		return false;
	});

	$('#reset-pwd-form').submit(function() {
		var form = $(this);

		form.find('.error_list').html('Запрос отправлен. Идет обработка...');
		form.find('.whitebutton').attr('disabled', 'disabled');

		$.post(form.prop('action'), form.serializeArray(), function( resp ) {
			if (resp.success ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					var type = ( (form.find('input.text').val().search('@')) !== -1 ) ? 'email' : 'mobile';

					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
				}
				//$('#reset-pwd-form').hide();
				//$('#login-form').show();
				//alert('Новый пароль был вам выслан по почте или смс');
				var resetForm = $('#reset-pwd-form > div');

				resetForm.find('input').remove();
				resetForm.find('.pb5').remove();
				resetForm.find('.error_list').html('Новый пароль был вам выслан по почте или смс!');
			}
			else {
				var txterr = ( resp.error !== '' ) ? resp.error : 'Вы ввели неправильные данные';

				form.find('.error_list').text( txterr );
				form.find('.whitebutton').removeAttr('disabled');
			}

		}, 'json');

		return false;
	});

	
	/* Infinity scroll */
	var ableToLoad = true;
	var compact = $("div.goodslist").length;
	var custom_jewel = $('.items-section__list').length;

	function liveScroll( lsURL, filters, pageid ) {
		var params = [];
		/* RETIRED cause data-filter
		if( $('.bigfilter.form').length ) //&& ( location.href.match(/_filter/) || location.href.match(/_tag/) ) )
			params = $('.bigfilter.form').parent().serializeArray()
		*/
		// lsURL += '/' +pageid + '/' + (( compact ) ? 'compact' : 'expanded')
		var tmpnode = ( compact ) ? $('div.goodslist') : $('div.goodsline:last');

		if ( custom_jewel ) {
			tmpnode = $('.items-section__list');
		}

		var loader =
			"<div id='ajaxgoods' class='bNavLoader'>" +
				"<div class='bNavLoader__eIco'><img src='/images/ajar.gif'></div>" +
				"<div class='bNavLoader__eM'>" +
					"<p class='bNavLoader__eText'>Подождите немного</p>"+
					"<p class='bNavLoader__eText'>Идет загрузка</p>"+
				"</div>" +
			"</div>";

		tmpnode.after( loader );

		if ( lsURL.match(/\?/) ) {
			lsURL += '&page=' + pageid;
		}
		else {
			lsURL += '?page=' + pageid;
		}

		$.get( lsURL, params, function(data) {
			if ( data != "" && !data.data ) { // JSON === error
				ableToLoad = true;
				if ( compact || custom_jewel ) {
					tmpnode.append(data);
				}
				else {
					tmpnode.after(data);
				}
			}

			$('#ajaxgoods').remove();

			if( $('#dlvrlinks').length ) {
				var coreid = [];
				var nodd = $('<div>').html( data );

				nodd.find('div.boxhover, div.goodsboxlink').each( function() {
					var cid = $(this).data('cid') || 0;

					if( cid ) {
						coreid.push( cid );
					}
				});

				dajax.post( dlvr_node.data('calclink'), coreid );
			}

		});
	}

	if ( $('div.allpager').length ) {
		$('div.allpager').each(function() {
			var lsURL = $(this).data('url') ;
			var filters = '';//$(this).data('filter')
			var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2;
			var vinit = vnext - 1;
			var vlast = parseInt('0' + $(this).data('lastpage') , 10);

			function checkScroll() {
				if ( ableToLoad && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ) {
					ableToLoad = false;

					if ( vlast + vinit > vnext ){
						liveScroll( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ));
					}

					vnext += 1;
				}
			}

			if ( location.href.match(/sort=/) && location.href.match(/page=/) ) { // Redirect on first in sort case
				$(this).bind('click', function(){
					window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );
					location.href = location.href.replace(/page=\d+/,'');
				});
			}
			else {
				$(this).bind('click', function() {
					window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );

					$('.pageslist.bPagesListBottom').hide();

					var next = $('.bPagesListTop .bPagesList__eItem:first');

					if ( next.hasClass('current') ) {
						next = next.next();
					}

					var nextLnk = next.find('.bPagesList__eItemLink')
									.html('<span>123</span>')
									.addClass('borderedR');

					nextLnk.attr('href', nextLnk.attr('href').replace(/page=\d+/,'') );
	
					$('.bPagesList__eItem').remove();
					$('.bPagesList').append( next )
										.find('.bPagesList__eItemLink')
										.bind('click', function(){
											window.docCookies.setItem('infScroll', 0, 0, '/' );
										});
					$('div.allpager').addClass('mChecked');
					checkScroll();
					$(window).scroll( checkScroll );
				});
			}
		});

		if ( window.docCookies.getItem( 'infScroll' ) === 1 ) {	
			$('.bAllPager:first').trigger('click');
		}
	}


	$('#jscity').autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			$.ajax({
				url: $('#jscity').data('url-autocomplete'),
				dataType: "json",
				data: {
					q: request.term
				},
				success: function( data ) {
					var res = data.data.slice(0, 15);
					response( $.map( res, function( item ) {
						return {
							label: item.name,
							value: item.name,
							url: item.url
						};
					}));
				}
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#jschangecity').data('url', ui.item.url );
			$('#jschangecity').removeClass('mDisabled');
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		}
	});
	
	function getRegions() {
		$('.popupRegion').lightbox_me({
			autofocus: true,
			onLoad: function(){
				if ($('#jscity').val().length){
					$('#jscity').putCursorAtEnd();
					$('#jschangecity').removeClass('mDisabled');
				}
			},
			onClose: function() {			
				if( !window.docCookies.hasItem('geoshop') ) {
					var id = $('#jsregion').data('region-id');
					window.docCookies.setItem("geoshop", id, 31536e3, "/");
					// document.location.reload()
				}
			}
		});
	}

	$('.cityItem .moreCity').bind('click',function(){
		$(this).toggleClass('mExpand');
		$('.regionSlidesWrap').slideToggle(300);
	});

	$('#jsregion, .jsChangeRegion').click( function() {
		var authFromServer = function( res ) {
			if ( !res.data.length ) {
				$('.popupRegion .mAutoresolve').html('');
				return false;
			}

			var url = res.data[0].url;
			var name = res.data[0].name;
			var id = res.data[0].id;

			if ( id === 14974 || id === 108136 ) {
				return false;
			}
			
			if ( $('.popupRegion .mAutoresolve').length ) {
				$('.popupRegion .mAutoresolve').html('<a href="'+url+'">'+name+'</a>');
			}
			else {
				$('.popupRegion .cityInline').prepend('<div class="cityItem mAutoresolve"><a href="'+url+'">'+name+'</a></div>');
			}
			
		};

		var autoResolve = $(this).data("autoresolve-url");

		if ( autoResolve !=='undefined' ) {
			$.ajax({
				type: 'GET',
				url: autoResolve,
				success: authFromServer
			});
		}
		
		getRegions();
		return false;
	});
	
	$('body').delegate('#jschangecity', 'click', function(e) {
		e.preventDefault();
		if( $(this).data('url') ){
			window.location = $(this).data('url');
		}
		else{
			$('.popupRegion').trigger('close');
		}
	});
	
	$('.inputClear').bind('click', function(e) {
		e.preventDefault();
		$('#jscity').val('');
	});

	$('.popupRegion .rightArr').bind('click', function() {
		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
		var sliderW = $('.popupRegion .regionSlides').width() *1;
		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'), 10);

		$('.popupRegion .leftArr').show();
		$('.popupRegion .regionSlides').animate({'left':sliderLeft-regionSlideW});

		if ( (sliderLeft-(regionSlideW * 2)) <= -sliderW ) {
			$('.popupRegion .rightArr').hide();
		}
	});
	$('.popupRegion .leftArr').bind('click', function() {
		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
		var sliderW = $('.popupRegion .regionSlides').width() *1;
		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'), 10);

		$('.popupRegion .rightArr').show();
		$('.popupRegion .regionSlides').animate({'left':sliderLeft+regionSlideW});

		if ( sliderLeft+(regionSlideW * 2) >= 0 ) {
			$('.popupRegion .leftArr').hide();
		}
	});
   
	/* GEOIP fix */
	if ( !window.docCookies.hasItem('geoshop') ) {
		getRegions();
	}
	
	/* Services Toggler */
	if ( $('.serviceblock').length ) {
		$('.info h3').css('cursor', 'pointer').click( function() {
			$(this).parent().find('> div').toggle();
		});

		if( $('.info h3').length === 1 ) {
			$('.info h3').trigger('click');
		}
	}
	
	// /* prettyCheckboxes */ , 
	$('.form input[type="checkbox"]').prettyCheckboxes();
	$('.form input[type="radio"]').prettyCheckboxes();

	
	/* tags */
	$('.fm').toggle( 
		function(){
			$(this).parent().find('.hf').slideDown();
			$(this).html('скрыть');
		},
		function(){
			$(this).parent().find('.hf').slideUp();
			$(this).html('еще...');
		}
	);


	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.parent().find('li.hf').slideToggle();
		var link = el.find('a');
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...');
	});

	$('.product_filter-block input:submit').addClass('mDisabled');
	$('.product_filter-block input:submit').click( function(e) {
		if ( $(this).hasClass('mDisabled') ){
			e.preventDefault();
		}
	});
  
	/* Side Filter Block handlers */
	$(".bigfilter dt").click(function(){
		if ( $(this).hasClass('submit') ){
			return true;
		}

		$(this).next(".bigfilter dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});
	
	$(".f1list dt B").click(function(){
		$(this).parent("dt").next(".f1list dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});

	$(".tagslist dt").click(function(){
		$(this).next(".tagslist dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});
	
	var launch = false;
	var activateForm = function() {
		if ( !launch ) {
			$('.product_filter-block input:submit').removeClass('mDisabled');
			launch = true;
		}
	};

	$('.product_filter-block').change(function() {
		activateForm();
	});
	
	/* Side Filters */
	var filterlink = $('.filter .filterlink:first');
	var filterlist = $('.filter .filterlist');
	var clientBrowser = new brwsr();

	if( clientBrowser.isTouch ) {
		filterlink.click(function(){
			filterlink.hide();
			filterlist.show();
			return false;
		});
	}
	else {
		filterlink.mouseenter(function() {
			filterlink.hide();
			filterlist.show();
		});
		filterlist.mouseleave(function() {
			filterlist.hide();
			filterlink.show();
		});
	}	
	
	var ajaxFilterCounter = 0;
	
	$('.product_filter-block').bind('change', function(e) {
		var el = $(e.target);

		if ( el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox'])) ) {
			el.trigger('preview');
		}
	}).bind('preview', function(e) {
		var el = $(e.target);
		var form = $(this);
		var flRes = $('.filterresult');

		ajaxFilterCounter++;

		var getFiltersResult = function(result) {
			var ending = '';

			ajaxFilterCounter--;

			if ( ajaxFilterCounter > 0 ) {
				return;
			}

			if ( result.success ) {
				flRes.hide();

				switch ( result.data % 10 ) {
					case 1:
						ending = 'ь';
						break;
					case 2: case 3: case 4:
						ending = 'и';
						break;
					default:
						ending = 'ей';
						break;
				}

				switch ( result.data % 100 ) {
					case 11: case 12: case 13: case 14:
						ending = 'ей';
						break;
				}

				var firstli = null;

				if ( el.is("div") ) { //triggered from filter slider !
					firstli = el;
				}
				else {
					firstli = el.parent().find('> label').first();
				}
				
				$('.result', flRes).text(result.data);
				$('.ending', flRes).text(ending);
				flRes.css('top',firstli.offset().top-$('.product_filter-block').offset().top).show();
					
				var localTimeout = null;

				$('.product_count-block')
					.hover(
						function() {
							if ( localTimeout ) {
								clearTimeout( localTimeout );
							}
						},
						function() {
							localTimeout = setTimeout( function() {
								flRes.hide();
							}, 4000  );
						}
					)
					.click(function() {
						form.submit();
					})
					.trigger('mouseout');
			}
		};

		var wholemessage = form.serializeArray();

		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val();
		$.ajax({
			type: 'GET',
			url: form.data('action-count'),
			data: wholemessage,
			success: getFiltersResult
		});
	});
	
	/* Sliders */
	$('.sliderbox').each( function() {
		var sliderRange = $('.filter-range', this);
		var filterrange = $(this);
		var papa = filterrange.parent();
		var mini = $('.slider-from',  $(this).next() ).val() * 1;
		var maxi = $('.slider-to',  $(this).next() ).val() * 1;
		var informator = $('.slider-interval', $(this).next());
		var from = papa.find('input:first');
		var to   = papa.find('input:eq(1)');
		informator.html( printPrice( from.val() ) + ' - ' + printPrice( to.val() ) );
		// var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
		var stepf = papa.find('.slider-interval').data('step');

		if ( typeof(stepf) == undefined ) {
			var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
		}
		
		sliderRange.slider({
			range: true,
			step: stepf,
			min: mini,
			max: maxi,
			values: [ from.val()  ,  to.val() ],
			slide: function( e, ui ) {
				informator.html( printPrice( ui.values[ 0 ] ) + ' - ' + printPrice( ui.values[ 1 ] ) );
				from.val( ui.values[ 0 ] );
				to.val( ui.values[ 1 ] );
			},
			change: function(e, ui) {
				if ( parseFloat(to.val()) > 0 ){
					from.parent().trigger('preview');
					activateForm();
				}
			}
		});

	});


	/* ---- */
	if ( $('.error_list').length && $('.basketheader').length ) {
		$.scrollTo( $('.error_list:first'), 300 );
	}

	/* Cards Carousel  */
	function CardsCarousel ( nodes, noajax ) {
		var self = this;
		var current = 1;

		var triggerClick = false;

		var refresh_max_page = false;
		var current_accessory_category = '';
		

		var wi  = nodes.width * 1;
		var viswi = nodes.viswidth * 1;

		if ( !isNaN($(nodes.times).html()) ) {
			var max = $(nodes.times).html() * 1;
		}
		else {
			var max = Math.ceil(wi / viswi);
		}

		if ( (noajax !== undefined) && (noajax === true) ) {
			var buffer = 100;
		}
		else {
			var buffer = ($(nodes.times).parent().parent().hasClass('accessories')) ? 6 : 2;
		}

		var ajaxflag = false;

		this.notify = function() {
			$(nodes.crnt).html( current );

			if ( refresh_max_page ) {
				$(nodes.times).html( max );
			}

			if ( current == 1 ) {
				$(nodes.prev).addClass('disabled');
			}
			else {
				$(nodes.prev).removeClass('disabled');
			}

			if ( current == max ) {
				$(nodes.next).addClass('disabled');
			}
			else {
				$(nodes.next).removeClass('disabled');
			}
		};

		var shiftme = function() {	
			var boxes = $(nodes.wrap).find('.goodsbox');
			$(boxes).hide();
			var le = boxes.length;

			for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
				boxes.eq( j ).show();
			}
			
			triggerClick = false;
		};

		$(nodes.next).bind('click', function() {
			if ( triggerClick ) {
				return false;
			}

			triggerClick = true;

			if ( current >= max && ajaxflag ) {
				return false;
			}

			if ( current + 1 === max ) { 

				var boxes = $(nodes.wrap).find('.goodsbox');
				$(boxes).hide();
				var le = boxes.length;
				var rest = ( wi % viswi ) ?  wi % viswi  : viswi;

				for ( var j = 1; j <= rest; j++ ) {
					boxes.eq( le - j ).show();
				}
				current++;
			}
			else {

				if ( current + 1 >= buffer ) { // we have to get new pull from server
					$(nodes.next).css('opacity','0.4'); // addClass dont work ((
					ajaxflag = true;
					var getData = [];

					if( $('form.product_filter-block').length ) {
						getData = $('form.product_filter-block').serializeArray();
					}

					getData.push( {name: 'page', value: buffer+1 } );
					getData.push( {name: 'categoryToken', value: current_accessory_category } );

					$.get( $(nodes.prev).attr('data-url') , getData, function(data) {
						buffer++;
						$(nodes.next).css('opacity','1');
						ajaxflag = false;
						var tr = $('<div>');
						$(tr).html( data );
						$(tr).find('.goodsbox').css('display','none');
						$(nodes.wrap).html( $(nodes.wrap).html() + tr.html() );

						// if ( grouped_accessories[current_accessory_category] ) {
						// 	grouped_accessories[current_accessory_category]['accessories'] = $(nodes.wrap).html();
						// 	grouped_accessories[current_accessory_category]['buffer']++;
						// }

						tr = null;
						current++;
						shiftme();
					// handle_custom_items()
					});
				}
				else { // we have new portion as already loaded one
					current++;
					shiftme(); // TODO repair
				}
			}
			self.notify();

			return false;
		});

		$(nodes.prev).click( function() {
			if ( current > 1 ) {
				current--;
				shiftme();
				self.notify();
			}

			return false;
		});

		$('.categoriesmenuitem').click(function(){
			refresh_max_page = true;
			var menuitem = $(this);
			var width = null;

			if ( !$(this).hasClass('active') ) {
				$(this).siblings('.active').addClass('link');
				$(this).siblings('.active').removeClass('active');
				$(this).addClass('active');
				$(this).removeClass('link');

				current_accessory_category = $(this).attr('data-category-token');

				if ( current_accessory_category == undefined ) {
					current_accessory_category = '';
				}

				if ( grouped_accessories[current_accessory_category] ) {
					$(nodes.wrap).html(grouped_accessories[current_accessory_category]['accessories']);

					if ( !isNaN(grouped_accessories[current_accessory_category]['totalpages']) ) {
						max = grouped_accessories[current_accessory_category]['totalpages'];
					}
					if ( !isNaN(grouped_accessories[current_accessory_category]['quantity']) ) {
						width = grouped_accessories[current_accessory_category]['quantity'];
					}

					current = 1;
					shiftme();
					self.notify();
				}
				else {
					ajaxflag = true;
					var getData = [];
					getData.push( {name: 'page', value: 1 } );
					getData.push( {name: 'categoryToken', value: current_accessory_category } );
					$.get( $(this).attr('data-url') , getData, function(data) {
						buffer = 2;
						$(nodes.wrap).html(data);
						width = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-quantity'), 10);
						max = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-total-pages'), 10);

						var xhr_category = $($(nodes.wrap).find('.goodsbox')[0]).attr('data-category');

						grouped_accessories[xhr_category] = {
							'quantity':width,
							'totalpages':max,
							'accessories':data,
							'buffer':buffer
						};
						current = 1;
						shiftme();
						self.notify();
					}).done(function(data) {
						ajaxflag = false;
					});
				}
			}
			return false;
		});

	} // CardsCarousel object

	$('.carouseltitle').each( function(){
		var tmpline = null;

		if( $(this).hasClass('carbig') && !$(this).hasClass('accessories') ) {
			tmpline = new CardsCarousel ({
				'prev'  : $(this).find('.back'),
				'next'  : $(this).find('.forvard'),
				'crnt'  : $(this).find('span:first'),
				'times' : $(this).find('span:eq(1)'),
				'width' : $(this).find('.scroll').data('quantity'),
				'wrap'  : $(this).find('~ .bigcarousel').first(),
				'viswidth' : 5
			});
		}
		else if( $(this).hasClass('carbig') && $(this).hasClass('accessories') ) {
			tmpline = new CardsCarousel ({
				'prev'  : $(this).find('.back'),
				'next'  : $(this).find('.forvard'),
				'crnt'  : $(this).find('span:first'),
				'times' : $(this).find('span:eq(1)'),
				'width' : $(this).find('.scroll').data('quantity'),
				'wrap'  : $(this).find('~ .bigcarousel').first(),
				'viswidth' : 4
			});
		}
		else if( $(this).find('.jshm').length ) {
			tmpline = new CardsCarousel ({
				'prev'  : $(this).find('.back'),
				'next'  : $(this).find('.forvard'),
				'crnt'  : $(this).find('.none'),
				'times' : $(this).find('span:eq(1)'),
				'width' : $(this).find('.jshm').html().replace(/\D/g,''),
//					'width' : $(this).find('.rubrictitle strong').html().replace(/\D/g,''),
				'wrap'  : $(this).find('~ .carousel').first(),
				'viswidth' : 3
			});
		}
	});


	var loadProductRelatedContainer = function loadProductRelatedContainer( container ) {
		var tID = 0;

		var authFromServer = function( result ) {
				container.html( result );
				// handle_custom_items();
				container.fadeIn();

				var tmpline = new CardsCarousel ({
					'prev': container.find('.back'),
					'next': container.find('.forvard'),
					'crnt': container.find('span:first'),
					'times': container.find('span:eq(1)'),
					'width': container.find('.scroll').data('quantity'),
					'wrap': container.find('.bigcarousel'),
					'viswidth' : 5
				}, true );
		};

		if ( container.length ) {
			tID = setTimeout(function(){
				$.ajax({
					type: 'GET',
					url: container.data('url'),
					timeout: 20000,
					success: authFromServer
				});
			},100);
		}
	};

	loadProductRelatedContainer($('#jsAlsoViewedProduct'));
	loadProductRelatedContainer($('#product_also_bought-container'));
	loadProductRelatedContainer($('#product_user-also_viewed-container'));

	$('.product_buy-container').each(function() {
		var order = $(this).data('order');

		if ( typeof(order) == 'object' && !$.isEmptyObject(order) ) {
			$.ajax({
				url: ($(this).data('url')),
				data: order,
				type: 'POST',
				timeout: 20000
			});
		}
	});

	/* Delivery Ajax */
	function Dlvrajax() {
		var that = this;
		this.self = '';
		this.other = [];
		this.node = null;

		this.formatPrice = function(price) {
			if ( typeof price === 'undefined' || price === null ) {
				return '';
			}

			if ( price > 0 ) {
				return ', '+price+' <span class="rubl">p</span>';
			}
			else {
				return ', бесплатно';
			}
		};

		this.printError = function() {
			if ( this.node ) {
				$(this.node).html( 'Стоимость доставки Вы можете уточнить в Контакт-сENTER 8&nbsp;(800)&nbsp;700-00-09' );
			}
		};

		this.post = function( url, coreid ) {
			$.post( url, {ids:coreid}, function(data) {
				if( !('success' in data ) ) {
					that.printError();
					return false;
				}

				if ( !data.success || data.data.length === 0 ) {
					// that.printError()
					if ( that.node ) {
						$(that.node).html('');
					}
					return false;
				}
					
				for ( var i=0; i < coreid.length; i++ ) {
					if ( !data.data[ coreid[i] ] ) {
						continue;
					}

					for( var j in data.data[ coreid[i] ] ) {
						var dlvr = data.data[ coreid[i] ][ j ];
						switch ( dlvr.token ) {
							case 'self':
								that.self = dlvr.date;
								break;
							default:
								that.other.push( { date: dlvr.date, price: dlvr.price, tc: ( typeof(dlvr.transportCompany) !== 'undefined') ? dlvr.transportCompany : false, days: dlvr.days, origin_date:dlvr.origin_date } );
								break;
						}
					}

					that.processHTML( coreid[i] );
					that.self = '';
					that.other = [];			
				}
			});
		};
	} // dlvrajax object

	if( $('#dlvrlinks').length ) { // Extended List
		var dlvr_node = $('#dlvrlinks');

		Dlvrajax.prototype.processHTML = function( id ) {
			var self = this.self,
				other = this.other;

			var pnode = $( 'div[data-cid='+id+']' ).parent();
			var ul = $('<ul>');

			if ( self ) {
				$('<li>').html( 'Возможен самовывоз ' + self ).appendTo( ul );
			}

			for ( var i = 0; i < other.length; i++ ) {
				var tmp = 'Доставка ' + other[i].date;
				tmp += ( other[i].price ) ? this.formatPrice( other[i].price ) : '';
				$('<li>').html( tmp ).appendTo( ul );
			}

			var uls = pnode.find( 'div.extrainfo ul' );
			uls.html( uls.html() + ul.html() );
		};

		var coreid = [];

		$('div.boxhover, div.goodsboxlink').each( function(){
			var cid = $(this).data('cid') || 0;

			if ( cid ) {
				coreid.push( cid );
			}
		});

		var dajax = new Dlvrajax();

		dajax.post( dlvr_node.data('calclink'), coreid );
	}
	
// 	// if ( $('.delivery-info').length ) { // Product Card
// 	// 	var dlvr_node = $('.delivery-info')
// 	// 	var dajax = new Dlvrajax()
// 	// 	var isSupplied = false
// 	// 	if ($('#productInfo').length){
// 	// 		var prData = $('#productInfo').data('value')
// 	// 		isSupplied = prData.isSupplied
// 	// 	}
// 	// 	dajax.node = dlvr_node
// 	// 	Dlvrajax.prototype.processHTML = function( id ) {
// 	// 		var self = this.self,
// 	// 			other = this.other    	
// 	// 		var html = '<h4>Как получить заказ?</h4><ul>'
// 	// 		if( self )
// 	// 			html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине ' +
// 	// 					self + '</h5><div>&mdash; <a target="blank" href="' +
// 	// 					dlvr_node.data('shoplink') + '">В каких магазинах ENTER можно забрать?</a></div></li>'	
// 	// 		// console.log(other.length)
// 	// 		if( other.length > 0 ){
// 	// 			html += '<li><h5>Можно заказать сейчас с доставкой</h5>'
// 	// 		}
// 	// 		for(var i in other) {
// 	// 			// console.info(other[i].date)
// 	// 			// console.info(this.formatPrice(other[i].price))
// 	// 			if (other[i].date !== undefined){
// 	// 				html += '<div>&mdash; Можем доставить '+ other[i].date + this.formatPrice(other[i].price) +'</div>'
// 	// 			}
// 	// 			if( other[i].tc ) {
// 	// 				html += '<div>&mdash; <a href="/how_get_order">Доставка осуществляется партнерскими транспортными компаниями</a></div>'
// 	// 			}
// 	// 		}
// 	// 		if( other.length > 0 && isSupplied){
// 	// 			html = '<h4>Доставка</h4><p>Через ~'+other[0].days+' дней<br/>планируемая дата поставки '+other[0].origin_date+'</p><p>Оператор контакт-cENTER согласует точную дату за 2-3 дня</p>'
// 	// 			if (other[i].price === 0){
// 	// 				html += '<p class="price">Бесплатно</p>'
// 	// 			}
// 	// 			else{
// 	// 				html += '<p class="price">'+other[i].price+' <span class="rubl">p</span></p>'
// 	// 			}
// 	// 		}
// 	// 		else{
// 	// 			html += '</ul>'	
// 	// 		}
			
// 	// 		dlvr_node.html(html)
// 	// 	}
	
// 	// 	var coreid = [ dlvr_node.attr('id').replace('product-id-', '') ]
		
// 	// 	dajax.post( dlvr_node.data('calclink'), coreid )
// 	// }


	if ( $('.searchtextClear').length ){
		$('.searchtextClear').each(function() {
			if ( !$(this).val().length ) {
				$(this).addClass('vh');
			}
			else {
				$(this).removeClass('vh');
			}
		});

		$('.searchtextClear').click(function() {
			$(this).siblings('.searchtext').val('');
			$(this).addClass('vh');

			if ( $('#searchAutocomplete').length ) {
				$('#searchAutocomplete').html('');
			}
		});
	}

	;(function() {
		$(".items-section__list .item").hover(
		function() {
			$(this).addClass('hover')
		},
		function() {
			$(this).removeClass('hover')
		});

		$(".bigcarousel-brand .goodsbox").hover(
		function() {
			$(this).addClass('hover');
		},
		function() {
			$(this).removeClass('hover');
		});
	}());
});