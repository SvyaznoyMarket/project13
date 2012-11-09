$(document).ready(function(){
	/* upper */
	var upper = $('#upper');
	var trigger = false;//сработало ли появление языка
	$(window).scroll(function(){
		if (($(window).scrollTop() > 600)&&(!trigger)){
			//появление языка
			trigger = true;
			upper.animate({'marginTop':'0'},400);
		}
		else if (($(window).scrollTop() < 600)&&(trigger)){
			//исчезновение
			trigger = false;
			upper.animate({'marginTop':'-30px'},400);
		}
	});
	upper.bind('click',function(){
		$(window).scrollTo('0px',400);
		return false;
	});
	/* GA categories referrer */
	function categoriesSpy( e ) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'CategoryClick', e.data, window.location.pathname ])
		return true
	}
	$('.breadcrumbs').first().find('a').bind( 'click', 'Хлебные крошки сверху', categoriesSpy )
	$('.breadcrumbs-footer').find('a').bind( 'click', 'Хлебные крошки снизу', categoriesSpy )
	$('.extramenu').find('a').live('click', 'Верхнее меню', categoriesSpy )
	$('.bCtg').find('a').bind('click', 'Левое меню', categoriesSpy )
	$('.rubrictitle').find('a').bind('click', 'Заголовок карусели', categoriesSpy )
	$('a.srcoll_link').bind('click', 'Ссылка Посмотреть все', categoriesSpy )
    /* GA click counter */
    function gaClickCounter() {
        if( typeof(_gaq) !== 'undefined' ) {
            var title =  ($(this).data('event') !== 'undefined') ?  $(this).data('event') : 'без названия';
            _gaq.push(['_trackEvent', $(this).data('event'), title, ,,, false])
        }
        return true
    }
    $('.gaEvent').bind('click', gaClickCounter )

    /* admitad */
	if( document.location.search.match(/admitad_uid/) ) {
		var url_s = parse_url( document.location.search )
		docCookies.setItem( false, "admitad_uid", url_s.admitad_uid, 31536e3, '/') // 31536e3 == one year
	}

	/* Jira */
	$.ajax({
	    url: "https://jira.enter.ru/s/en_US-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6",
	    type: "get",
	    cache: true,
	    dataType: "script"
	});

	 window.ATL_JQ_PAGE_PROPS =  {
		"triggerFunction": function(showCollectorDialog) {
			$("#jira").click(function(e) {
				e.preventDefault()
				showCollectorDialog()
			})
		}
	}

	/* sclub card number */
	if( document.location.search.match(/scid/) ) {
		var url_s = parse_url( document.location.search )
		docCookies.setItem( false, "scId", url_s.scid, 31536e3, '/') // 31536e3 == one year
	}
	
	/* mobile fix for Lbox position='fixed' */
	var clientBrowser = new brwsr()
	if( clientBrowser.isAndroid || clientBrowser.isOSX4 ) {
		if( clientBrowser.isOpera ) {
			$('.lightbox').hide()
		}
		$('.lightbox').css('position','absolute')
		var innerHeightM = ( clientBrowser.isOSX4 ) ? window.innerHeight : document.documentElement.clientHeight
		var innerWidthM  = ( clientBrowser.isOSX4 ) ? window.innerWidth  : document.documentElement.clientWidth
		if(  clientBrowser.isOSX4  )
			$('.lightbox').css('top', window.pageYOffset + innerHeightM -41)
		if ( Math.abs(window.orientation) === 90 ) {
			var inittopv = innerHeightM - 41
			var inittoph = innerWidthM  - 41
		} else {
			var inittoph = innerHeightM - 41
			var inittopv = innerWidthM  - 41
		}

		window.addEventListener("orientationchange", setPosLbox, false)
		window.addEventListener("scroll", setPosLbox, false)
		window.onscroll = setPosLbox

		function setPosLbox() {
			if( !window.pageYOffset ){
				$('.lightbox').css('top', ( Math.abs(window.orientation) === 90 ) ? inittopv : inittoph )
			} else {
				innerHeightM = ( clientBrowser.isOSX4 ) ? window.innerHeight : document.documentElement.clientHeight
				$('.lightbox').css('top', window.pageYOffset + innerHeightM -41)
			}
		}

	} // isAndroid || isOSX4

	/* Authorization process */
	$('.open_auth-link').bind('click', function(e) {
		e.preventDefault()
		
		var el = $(this)
		window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus()
	})
		
	$('#auth-link').click(function() {
		$('#auth-block').lightbox_me({
			centered: true,
			autofocus: true,
			onLoad: function() {
				$('#auth-block').find('input:first').focus()
			}
		})
		return false
	})

	;(function($) {
		$.fn.warnings = function() {
			var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>')
			rwn.css({
				'border': '1px solid red',
				'color': 'red',
				'border-radius': '3px',
				'background-color':'#fff',
				'position': 'absolute',
				'height': '16px',
				'padding': '1px 3px',
				'margin-top': '2px'
			})
			var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px')

			$(this).keypress(function(e) {
				var s = String.fromCharCode( e.which )
				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					if( !$('#capslock').length ) $(this).after(cln)
				} else {
					if( $('#capslock').length ) $('#capslock').remove()
				}
		  })
		  $(this).keyup(function(e) {
				if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
					if( !$('#ruschars').length ) {
						if( $('#capslock').length )
							rwn.css('marginLeft','-116px')
						else
							rwn.css('marginLeft','-36px')
						$(this).after(rwn)
					}
				} else {
					if( $('#ruschars').length ) $('#ruschars').remove()
				}
		  })
		}
	})(jQuery);

  $('#signin_password').warnings()

  $('#login-form, #register-form')
	.data('redirect', true)
	.bind('submit', function(e, param) {
		e.preventDefault()
		var form = $(this) //$(e.target)
		form.find('[type="submit"]:first')
			.attr('disabled', true)
			.val('login-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...')
		var wholemessage = form.serializeArray()
		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val()
		
		function authFromServer(response) {
          if ( response.success ) {
            if ( form.data('redirect') ) {
              if (response.url) {
                window.location = response.url
              } else {
                form.unbind('submit')
                form.submit()
              }
            } else {
              $('#auth-block').trigger('close')
              PubSub.publish( 'authorize', response.user )
            }
          } else {
            form.html( $(response.data.content).html() )
          }
		}
		
		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		})
    })

	$('#forgot-pwd-trigger').live('click', function(){
		$('#reset-pwd-form').show();
		$('#reset-pwd-key-form').hide();
		$('#login-form').hide();
		return false;
	})

	$('#remember-pwd-trigger,#remember-pwd-trigger2').click(function(){
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
		return false;
	})

	$('#reset-pwd-form, #auth_forgot-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('Запрос отправлен. Идет обработка...');
		form.find('.whitebutton').attr('disabled', 'disabled')
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success === true) {
				//$('#reset-pwd-form').hide();
				//$('#login-form').show();
				//alert('Новый пароль был вам выслан по почте или смс');
				var resetForm = $('#reset-pwd-form > div')
				resetForm.find('input').remove()
				resetForm.find('.pb5').remove()
				resetForm.find('.error_list').html('Новый пароль был вам выслан по почте или смс!')
			} else {
				var txterr = ( resp.error !== '' ) ? resp.error : 'Вы ввели неправильные данные'
				form.find('.error_list').text( txterr );
			}
		}, 'json');

		return false;
	})
	/* RETIRED
	$('#reset-pwd-key-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('');
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success == true) {
				$('#reset-pwd-form').hide();
				$('#reset-pwd-key-form').hide();
				$('#login-form').show();
				alert('Новый пароль был вам выслан по почте или смс');
			} else {
				form.find('.error_list').html('Вы ввели неправильный ключ');
			}
		}, 'json');
		return false;
	})	
	*/
	
	/* Infinity scroll */
	var ableToLoad = true
	var compact = $("div.goodslist").length
	function liveScroll( lsURL, filters, pageid ) {
		var params = []
		/* RETIRED cause data-filter
		if( $('.bigfilter.form').length ) //&& ( location.href.match(/_filter/) || location.href.match(/_tag/) ) )
			params = $('.bigfilter.form').parent().serializeArray()
		*/
		// lsURL += '/' +pageid + '/' + (( compact ) ? 'compact' : 'expanded')
		var tmpnode = ( compact ) ? $('div.goodslist') : $('div.goodsline:last')
		var loader =
			"<div id='ajaxgoods' class='bNavLoader'>" +
				"<div class='bNavLoader__eIco'><img src='/images/ajar.gif'></div>" +
				"<div class='bNavLoader__eM'>" +
					"<p class='bNavLoader__eText'>Подождите немного</p>"+
					"<p class='bNavLoader__eText'>Идет загрузка</p>"+
				"</div>" +
			"</div>"
		tmpnode.after( loader )
		//'?' + filters + 
		if( lsURL.match(/\?/) )
			lsURL += '&page=' + pageid
		else
			lsURL += '?page=' + pageid
		// if( $("#sorting").length ) {
		// 	params.push( { name:'sort', value : $("#sorting").data('sort') })
		// }

		$.get( lsURL, params, function(data){
			if ( data != "" && !data.data ) { // JSON === error
				ableToLoad = true
				if( compact )
					tmpnode.append(data)
				else
					tmpnode.after(data)
			}
			$('#ajaxgoods').remove()
			if( $('#dlvrlinks').length ) {
				var coreid = []
				var nodd = $('<div>').html( data )
				nodd.find('div.boxhover, div.goodsboxlink').each( function() {
					var cid = $(this).data('cid') || 0
					if( cid )
						coreid.push( cid )
				})
				dajax.post( dlvr_node.data('calclink'), coreid )
			}
		})
	}

	if( $('div.allpager').length ) {
		$('div.allpager').each(function() {
			var lsURL = $(this).data('url') 
			var filters = ''//$(this).data('filter')
			var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2
			var vinit = vnext - 1
			var vlast = parseInt('0' + $(this).data('lastpage') , 10)
			function checkScroll(){
				if ( ableToLoad && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ){
					ableToLoad = false
					if( vlast + vinit > vnext )
						liveScroll( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ))
					vnext += 1
				}
			}
			if( location.href.match(/sort=/) &&  location.href.match(/page=/) ) { // Redirect on first in sort case
				$(this).bind('click', function(){
					docCookies.setItem( false, 'infScroll', 1, 4*7*24*60*60, '/' )
					location.href = location.href.replace(/page=\d+/,'')
				})
			} else {
				$(this).bind('click', function(){
					docCookies.setItem( false, 'infScroll', 1, 4*7*24*60*60, '/' )
					var next = $('div.pageslist:first li:first')
					if( next.hasClass('current') )
						next = next.next()
					var next_a = next.find('a')
									.html('<span>123</span>')
									.addClass('borderedR')
					next_a.attr('href', next_a.attr('href').replace(/page=\d+/,'') )
	
					$('div.pageslist li').remove()
					$('div.pageslist ul').append( next )
										 .find('a')
										 .bind('click', function(){
											docCookies.removeItem( 'infScroll' )
										  })
					$('div.allpager').addClass('mChecked')
					checkScroll()
					$(window).scroll( checkScroll )
				})
			}
		})

		if( docCookies.hasItem( 'infScroll' ) )
			$('div.allpager:first').trigger('click')
	}
	
	/* AJAX */
	$('body').append('<div style="display:none"><img src="/images/error_ajax.gif" alt=""/></div>')
	var errorpopup = function( txt ) {
	var block =	'<div id="ajaxerror" class="popup">' +
					'<i class="close" title="Закрыть">Закрыть</i>' +
					'<div class="popupbox width650 height170">' +
						'<h2 class="pouptitle">Непредвиденная ошибка</h2><div class="clear"></div>' +
						'<div class="fl"><div class="font16 pb20 width345"> Что-то произошло, но мы постараемся это починить :) Попробуйте повторить ваше последнее действие еще раз.<br/>' +
						'Причина ошибки: ' + txt + ' </div></div>' +
						'<div class="clear"></div><div style="position:absolute; right:30px; top: 20px; margin-bottom:20px;"><img src="/images/error_ajax.gif" width="" height="" alt=""/></div>' +
					'</div>' +
				'</div>	'
		$('body').append( $(block) )
		$('#ajaxerror').lightbox_me({
		  centered: true,
		  onClose: function(){
		  		$('#ajaxerror').remove()
		  	}
		})
	}
	/* RETIRED
	$.ajaxPrefilter(function( options ) {
		if( !options.url.match('search') )
			options.url += '?ts=' + new Date().getTime()
	})

	$('body').ajaxError(function(e, jqxhr, settings, exception) {
		$('#ajaxerror div.fl').append('<small>'+ settings.url.replace(/(.*)\?ts=/,'')+'</small>')
	})
	*/

	$.ajaxSetup({
		timeout: 10000,
		statusCode: {
			404: function() {
				errorpopup(' 404 ошибка, страница не найдена')
			},
			401: function() {
				if( $('#auth-block').length ) {
					$('#auth-block').lightbox_me({
						centered: true,
						onLoad: function() {
							$('#auth-block').find('input:first').focus()
						}
					})
				} else
					errorpopup(' 401 ошибка, авторизуйтесь заново')
			},
			500: function() {
				errorpopup(' сервер перегружен')
			},
			503: function() {
				errorpopup(' 503 ошибка, сервер перегружен')
			},
			504: function() {
				errorpopup(' 504 ошибка, проверьте соединение с интернетом')
			}

		  },
		error: function (jqXHR, textStatus, errorThrown) {
			if( jqXHR.statusText == 'error' )
				console.error(' неизвестная ajax ошибка')
			else if ( textStatus=='timeout' )
				;//errorpopup(' проверьте соединение с интернетом')
		}
	})
	
	$('.inputClear').bind('click', function(e) {
		e.preventDefault()
		$('#jscity').val('')
	})

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
					var res = data.data.slice(0, 15)
					response( $.map( res, function( item ) {
						return {
							label: item.name,
							value: item.name,
							url: item.url
						}
					}));
				}
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#jschangecity').data('url', ui.item.url )
			$('#jschangecity').removeClass('mDisabled')
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		}
	})

	// function paintRegions() {
	// 	$('.bCityPopupWrap').lightbox_me({ centered: true })
	// }
	
	function getRegions() {
		$('.popupRegion').lightbox_me( {
			autofocus: true,
			onClose: function() {			
				if( !docCookies.hasItem('geoshop') ) {
					docCookies.setItem( false, "geoshop", "14974", 31536e3, "/") //moscow city
					document.location.reload()
				}
			}
		} )		
	}

	$('#jsregion, .jsChangeRegion').click( function() {
		getRegions()
		return false
	})
	
	$('body').delegate('#jschangecity', 'click', function(e) {
		e.preventDefault()
		if( $(this).data('url') )
			window.location = $(this).data('url')
	})
	
	$('.inputClear').bind('click', function(e) {
		e.preventDefault()
		$('#jscity').val('')	
  	})
   
	/* GEOIP fix */
	if( !docCookies.hasItem('geoshop') ) {
		getRegions()
	}
	if( !docCookies.hasItem('geoshop_change') ) {
		docCookies.removeItem('geoshop')
		docCookies.setItem( false, "geoshop_change", "yes", 31536e3, "/")
		getRegions()
	}
	
	/* Services Toggler */
	if( $('.serviceblock').length ) {
		$('.info h3').css('cursor', 'pointer')
		.click( function() {
			$(this).parent().find('> div').toggle()
		})
		if( $('.info h3').length === 1 )
			$('.info h3').trigger('click')
	}
	
	/* prettyCheckboxes */
    $('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes()

	/* Rotator */
	if($('#rotator').length) {
		$('#rotator').jshowoff({ speed:8000, controls:false })
		$('.jshowoff-slidelinks a').wrapInner('<span/>')
	}
	
	/* tags */
	$('.fm').toggle( function(){
		$(this).parent().find('.hf').slideDown()
		$(this).html('скрыть')
	}, function(){
		$(this).parent().find('.hf').slideUp()
		$(this).html('еще...')
	})
	
	/* search tags */
	if( $('#plus10').length ) {
		if( $('#filter_product_type-form li').length < 10 )
			$('#plus10').hide()
		else
			$('#plus10').html( 'еще '+ ($('#filter_product_type-form .hf').length % 10 + 1) +' из ' + $('#filter_product_type-form li').length )
		$('#plus10').click( function(){
			$('#filter_product_type-form .hf').slice(0,10).removeClass('hf')
			if ( !$('#filter_product_type-form .hf').length )
				$(this).parent().hide()
			return false
		})
	}
	
	/* Search */
	$('input:[name="q"]').bind(
		{
			'focusin': function() {
				if ( $(this).val() == 'Поиск среди 30 000 товаров' ) $(this).val( '' );
			},
			'blur': function() {
				if ( $(this).val() == '' ) $(this).val( 'Поиск среди 30 000 товаров' );
			}
		}
	)
	
	$('.search-form').bind('submit', function(e) {
		// e.preventDefault()
		var form = $(this)
		if (form.find('input:[name="q"]').val().length < 2)
			return false
		if( form.find('input:[name="q"]').val() === 'Поиск среди 30 000 товаров' )
			return false
		// var wholemessage = form.serializeArray()
		// function getSearchResults( response ) {
		// 		if( response.success ) {
		// 			form.unbind('submit')
		// 			form.submit()
		// 		} else {
		// 			var el = $(response.data.content)
		// 			el.appendTo('body')
		// 			$('#search_popup-block').lightbox_me({
		// 				centered: true//,
		// 				//onLoad: function() { $(this).find('input:first').focus() }
		// 			})
		// 		}
		// }
		// $.ajax({
		// 	type: 'GET',
		// 	url: form.attr('action'),
		// 	data: wholemessage,
		// 	success: getSearchResults
		// })
	})

	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault()
		var el = $(this)
		el.parent().find('li.hf').slideToggle()
		var link = el.find('a')
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...')
	})
  
	/* Side Filter Block handlers */
	$(".bigfilter dt").click(function(){
		$(this).next(".bigfilter dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})
	
	$(".f1list dt B").click(function(){
		$(this).parent("dt").next(".f1list dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})

	$(".tagslist dt").click(function(){
		$(this).next(".tagslist dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})
	$('.product_filter-block input:submit').addClass('mDisabled')
	$('.product_filter-block input:submit').click( function(e) {
		if( $(this).hasClass('mDisabled') )
			e.preventDefault()
	})
	var launch = false
	$('.product_filter-block').change(function(){
		activateForm()
	})
	function activateForm() {
		if ( !launch ) {
			$('.product_filter-block input:submit').removeClass('mDisabled')
			launch = true
		}
	}
	
	/* Side Filters */
    var filterlink = $('.filter .filterlink:first')
	var filterlist = $('.filter .filterlist')
	if( clientBrowser.isTouch ) {
		filterlink.click(function(){
			filterlink.hide()
			filterlist.show()
			return false
		})
	} else {
		filterlink.mouseenter(function(){
			filterlink.hide()
			filterlist.show()
		})
		filterlist.mouseleave(function(){
			filterlist.hide()
			filterlink.show()
		})
	}	
	
	var ajaxFilterCounter = 0
	
	$('.product_filter-block')
    .bind('change', function(e) {
        var el = $(e.target)

        if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
            el.trigger('preview')
        }
    })
    .bind('preview', function(e) {
        var el = $(e.target)
        var form = $(this)
        var flRes = $('.filterresult');
        ajaxFilterCounter++
		function getFiltersResult (result) {
			ajaxFilterCounter--
			if( ajaxFilterCounter > 0 )
				return
			if( result.success ) {
                flRes.hide();
                switch (result.data % 10) {
                  case 1:
                    ending = 'ь';
                    break
                  case 2: case 3: case 4:
                    ending = 'и';
                    break
                  default:
                    ending = 'ей';
                    break
                }
                switch (result.data % 100) {
                  case 11: case 12: case 13: case 14:
                    ending = 'ей';
                    break
                }
                var firstli = null
                if ( el.is("div") ) //triggered from filter slider !
                	firstli = el
                else
	                firstli = el.parent().find('> label').first()
                	$('.result', flRes).text(result.data);
                	$('.ending', flRes).text(ending);
                	flRes.css('top',firstli.offset().top-$('.product_filter-block').offset().top).show();
                	
                var localTimeout = null
                $('.product_count-block')
					.hover(
						function() {
							if( localTimeout )
								clearTimeout( localTimeout )
						},
						function() {
							localTimeout = setTimeout( function() {
								flRes.hide();
							}, 4000  )
						}
						)
					.click(function() {
						form.submit()
					})
					.trigger('mouseout')
            }
        }

		var wholemessage = form.serializeArray()
		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val()
		$.ajax({
			type: 'GET',
			url: form.data('action-count'),
			data: wholemessage,
			success: getFiltersResult
		})
    })
    
	/* Sliders */
	$('.sliderbox').each( function(){
		var sliderRange = $('.filter-range', this)
		var filterrange = $(this)
		var papa = filterrange.parent()
		var mini = $('.slider-from',  $(this).next() ).val() * 1
		var maxi = $('.slider-to',  $(this).next() ).val() * 1
		var informator = $('.slider-interval', $(this).next())
		var from = papa.find('input:first')
		var to   = papa.find('input:eq(1)')
		informator.html( printPrice( from.val() ) + ' - ' + printPrice( to.val() ) )
		var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1
		if( maxi - mini <= 3 && stepf != 10 )
			stepf = 0.1
		sliderRange.slider({
			range: true,
			step: stepf,
			min: mini,
			max: maxi,
			values: [ from.val()  ,  to.val() ],
			slide: function( e, ui ) {
				informator.html( printPrice( ui.values[ 0 ] ) + ' - ' + printPrice( ui.values[ 1 ] ) )
				from.val( ui.values[ 0 ] )
				to.val( ui.values[ 1 ] )
			},
			change: function(e, ui) {
				if ( parseFloat(to.val()) > 0 ){
					from.parent().trigger('preview')
					activateForm()
				}
			}
		})

	})
	
    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

	/* Top Menu */
	if( $('.topmenu').length ) {
		$.get('/category/main_menu', function(data){
			$('#header').append( data )
		})
	}

	var idcm          = null // setTimeout
	var currentMenu = 0 // ref= product ID
	function showList( self ) {	
		if(	$(self).data('run') ) {
			var dmenu = $(self).position().left*1 + $(self).width()*1 / 2 + 5
			var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
			if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
				punkt.show()//.find('.corner').css('left', dmenu)
		}
	}
	if( clientBrowser.isTouch ) {
		$('.topmenu a.bToplink').bind ('click', function(){
			if( $(this).data('run') )
				return true
			$('.extramenu').hide()	
			$('.topmenu a.bToplink').each( function() { $(this).data('run', false) } )
			$(this).data('run', true)
			showList( this )
			return false
		})
	} else {	
		$('.topmenu a.bToplink').bind( {
			'mouseenter': function() {
				$('.extramenu').hide()
				var self = this				
				$(self).data('run', true)
				currentMenu = $(self).attr('id').replace(/\D+/,'')
				idcm = setTimeout( function() { showList( self ) }, 300)
			},
			'mouseleave': function() {
				var self = this
	
				if(	$(self).data('run') ) {
					clearTimeout( idcm )
					$(self).data('run',false)
				}
				//currentMenu = 0
			}
		})
	}

	$(document).click( function(e){
		if (currentMenu) {
			if( e.which == 1 )
				$( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
		}
	})

	$('.extramenu').click( function(e){
		e.stopPropagation()
	})
	
	/* ---- */
	if( $('.error_list').length && $('.basketheader').length ) {
		$.scrollTo( $('.error_list:first'), 300 )
	}

	/* Cards Carousel  */
	function cardsCarousel ( nodes, noajax ) {
		var self = this
		var current = 1

		var wi  = nodes.width*1
		var viswi = nodes.viswidth*1

		if( !isNaN($(nodes.times).html()) )
			var max = $(nodes.times).html() * 1
		else
			var max = Math.ceil(wi / viswi)			
		var buffer = (noajax) ? 100 : 2 
		var ajaxflag = false

		this.notify = function() {
			$(nodes.crnt).html( current )
			if ( current == 1 )
				$(nodes.prev).addClass('disabled')
			else
				$(nodes.prev).removeClass('disabled')
			if ( current == max )
				$(nodes.next).addClass('disabled')
			else
				$(nodes.next).removeClass('disabled')
		}

		var shiftme = function() {	
			var boxes = $(nodes.wrap).find('.goodsbox')
			$(boxes).hide()
			var le = boxes.length
			for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
				boxes.eq( j ).show()
			}
		}

		$(nodes.next).bind('click', function() {
			if( current < max && !ajaxflag ) {
				if( current + 1 == max ) { //the last pull is loaded , so special shift
					var boxes = $(nodes.wrap).find('.goodsbox')
					$(boxes).hide()
					var le = boxes.length
					var rest = ( wi % viswi ) ?  wi % viswi  : viswi
					for(var j = 1; j <= rest; j++)
						boxes.eq( le - j ).show()
					current++
				} else {
					if( current + 1 >= buffer ) { // we have to get new pull from server
						$(nodes.next).css('opacity','0.4') // addClass dont work ((
						ajaxflag = true
						var getData = []
						if( $('form.product_filter-block').length )
							getData = $('form.product_filter-block').serializeArray()
						getData.push( {name: 'page', value: buffer+1 } )	
						$.get( $(nodes.prev).attr('data-url') , getData, function(data) {
							buffer++
							$(nodes.next).css('opacity','1')
							ajaxflag = false
							var tr = $('<div>')
							$(tr).html( data )
							$(tr).find('.goodsbox').css('display','none')
							$(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
							tr = null
						})
						current++
						shiftme()
					} else { // we have new portion as already loaded one			
						current++
						shiftme() // TODO repair
					}
				}
				self.notify()
			}
			return false
		})

		$(nodes.prev).click( function() {
			if( current > 1 ) {
				current--
				shiftme()
				self.notify()
			}
			return false
		})

	} // cardsCarousel object

	$('.carouseltitle').each( function(){
		if( $(this).hasClass('carbig') ) {
			var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.scroll').data('quantity'),
					'wrap'  : $(this).find('~ .bigcarousel').first(),
					'viswidth' : 5
				})		
		} else {
			if( $(this).find('.jshm').length ) {
				var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('.none'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.jshm').html().replace(/\D/g,''),
//					'width' : $(this).find('.rubrictitle strong').html().replace(/\D/g,''),
					'wrap'  : $(this).find('~ .carousel').first(),
					'viswidth' : 3
				})
			}		
		}			
	})

	loadProductRelatedContainer($('#product_view-container'))
	loadProductRelatedContainer($('#product_also_bought-container'))
    loadProductRelatedContainer($('#product_user-also_viewed-container'))
    loadProductRelatedContainer($('#product_buy-container')); // no such element
    //loadProductRelatedContainer($('#product_user-recommendation-container'));

    function loadProductRelatedContainer(container) {
        if (container.length) {
            $.ajax({
                url: container.data('url'),
                timeout: 20000
            }).success(function(result) {
                    container.html(result)
                    container.fadeIn()      
                    var tmpline = new cardsCarousel ({
                            'prev'  : container.find('.back'),
                            'next'  : container.find('.forvard'),
                            'crnt'  : container.find('span:first'),
                            'times' : container.find('span:eq(1)'),
                            'width' : container.find('.scroll').data('quantity'),
                            'wrap'  : container.find('.bigcarousel'),
                            'viswidth' : 5
                        }, true )  // true === noajax for carousel                                       
            })
        }
    }

	/* Delivery Ajax */
	function dlvrajax() {
		var that = this
		this.self = ''
		this.other = []
		this.node = null

		this.formatPrice = function(price) {
			if (typeof price === 'undefined' || price === null)
				return ''
			if (price > 0) 
				return ', '+price+' <span class="rubl">p</span>'
			else
				return ', бесплатно'
		}

		this.printError = function() {
			if( this.node )
				$(this.node).html( 'Стоимость доставки Вы можете уточнить в Контакт-сENTER 8&nbsp;(800)&nbsp;700-00-09' )
		}

		this.post = function( url, coreid ) {
			$.post( url, {ids:coreid}, function(data) {
				if( !('success' in data ) ) {
					that.printError()
					return false
				}
				if( !data.success || data.data.length === 0 ) {
					// that.printError()
					if( that.node )
						$(that.node).html('')
					return false					
				}
					
				for(var i=0; i < coreid.length; i++) {
					if( !data.data[ coreid[i] ] )
						continue
					for( var j in data.data[ coreid[i] ] ) {
						var dlvr = data.data[ coreid[i] ][ j ]			
						switch ( dlvr.token ) {
							case 'self':
								that.self = dlvr.date
								break
							default:
								that.other.push( { date: dlvr.date, price: dlvr.price, tc: ( typeof(dlvr.transportCompany) !== 'undefined') ? dlvr.transportCompany : false } )
						}
					}
					that.processHTML( coreid[i] )
					that.self = ''
					that.other = []					
				}
			})
		}
	} // dlvrajax object

	if( $('#dlvrlinks').length ) { // Extended List
		var dlvr_node = $('#dlvrlinks')
		dlvrajax.prototype.processHTML = function( id ) {
			var self = this.self,
				other = this.other
			var pnode = $( 'div[data-cid='+id+']' ).parent()
			var ul = $('<ul>')
			if(self)
				$('<li>').html( 'Возможен самовывоз ' + self ).appendTo( ul )
			for(var i=0; i < other.length; i++) {
				var tmp = 'Доставка ' + other[i].date
				tmp += ( other[i].price ) ? this.formatPrice( other[i].price ) : ''
				$('<li>').html( tmp ).appendTo( ul )
			}
			var uls = pnode.find( 'div.extrainfo ul' )
			uls.html( uls.html() + ul.html() )		
		}
		var coreid = []
		$('div.boxhover, div.goodsboxlink').each( function(){
			var cid = $(this).data('cid') || 0
			if( cid )
				coreid.push( cid )
		})
		var dajax = new dlvrajax()
		dajax.post( dlvr_node.data('calclink'), coreid )
	}
	
    if ( $('.delivery-info').length ) { // Product Card
    	var dlvr_node = $('.delivery-info')
    	var dajax = new dlvrajax()
    	dajax.node = dlvr_node
    	dlvrajax.prototype.processHTML = function( id ) {
			var self = this.self,
				other = this.other    	
			var html = '<h4>Как получить заказ?</h4><ul>'
			if( self )
				html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине ' +
						self + '</h5><div>&mdash; <a target="blank" href="' +
						dlvr_node.data('shoplink') + '">В каких магазинах ENTER можно забрать?</a></div></li>'	
			
			if( other.length > 0 )
				html += '<li><h5>Можно заказать сейчас с доставкой</h5>'
			for(var i=0; i < other.length; i++) {
				html += '<div>&mdash; Можем доставить '+ other[i].date + this.formatPrice(other[i].price) +'</div>'
				if( other[i].tc ) {
					html += '<div>&mdash; <a href="/how_get_order">Доставка осуществляется партнерскими траспортными компаниями</a></div>'
				}
			}

			html += '</ul>'
			dlvr_node.html(html)
		}
    
		var coreid = [ dlvr_node.attr('id').replace('product-id-', '') ]
		
		dajax.post( dlvr_node.data('calclink'), coreid )
    }

});
