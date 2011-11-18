// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){
  var cache = {};

  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :

      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +

        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +

        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");

    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();

$(document).ready(function(){

	var userag    = navigator.userAgent.toLowerCase()
	var isAndroid = userag.indexOf("android") > -1 
	var isPad     = userag.indexOf('ipad') > -1
	if( isAndroid ) {
		alert('isAndroid')
		$('head').append('<meta name="viewport" content="width=device-width"/>')
	}
	if( isAndroid || isPad ) {
		window.onscroll = function() {		
			$('.lightbox').css('position','absolute')
			$('.lightbox').css('top', window.pageYOffset + $(window).height() -41)
		}
	}

	/* Rotator */
	if($('#rotator').length) {
		$('#rotator').jshowoff({ speed:8000, controls:false })
		$('.jshowoff-slidelinks a').wrapInner('<span/>')
	}
	/* Infinity scroll */
	var ableToLoad = true
	var compact = $("div.goodslist").length
	function liveScroll( lsURL, pageid ) {
		lsURL += pageid + '/' + (( compact ) ? 'compact/' : 'expanded/')
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
		//tmpnode.after('<div id="ajaxgoods" style="width:100%; text-align:right;"><span style="margin-bottom: 6px;">Список товаров подгружается...</span><img src="/images/ajax-loader.gif" alt=""/></div>')
		$.get( lsURL, function(data){
			if ( data != "" && !data.data ) { // JSON === error
				ableToLoad = true
				if( compact )
					tmpnode.append(data)
				else
					tmpnode.after(data)
			}
			$('#ajaxgoods').remove()
		})
	}



	if( $('div.allpager').length ) {
			$('div.allpager').each(function(){
			var lsURL = $(this).data('url')
			var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2
			var vinit = vnext - 1
			var vlast = parseInt('0' + $(this).data('lastpage') , 10)
			function checkScroll(){
				if ( ableToLoad && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ){
					ableToLoad = false
					if( vlast + vinit > vnext )
						liveScroll( lsURL, ((vnext % vlast) ? (vnext % vlast) : vnext ))
					vnext += 1
				}
			}
			$(this).bind('click', function(){
				$.jCookies({
					name : 'infScroll',
					value : 1
				})
				var next = $('div.pageslist:first li:first')
				if( next.hasClass('current') )
					next = next.next()
				var next_a = next.find('a')
								.html('<span>123</span>')

								.addClass('borderedR')								
				next_a.attr('href', next_a.attr('href').replace(/\?page=\d/,'') )

				$('div.pageslist li').remove()
				$('div.pageslist ul').append( next )
									 .find('a')
									 .bind('click', function(){
										$.jCookies({ erase : 'infScroll' })
									  })
				$('div.allpager').addClass('mChecked')
				checkScroll()
				$(window).scroll( checkScroll )
			})
		})

		if( $.jCookies({ get : 'infScroll' }) )
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

	$.ajaxSetup({
		timeout: 7000,
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
				errorpopup(' проверьте соединение с интернетом')
		}
	})

	/* --- */
    $('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();

	$(".bigfilter dt").click(function(){
		$(this).next(".bigfilter dd").slideToggle(200)
		$(this).toggleClass("current")
		return false
	})
	//$(".bigfilter dt:first").trigger('click')
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
/*
		sliderRange.slider({
			range: true,
			step: stepf,
			min: mini,
			max: maxi,
			values: [ from.val() ? from.val() : mini ,  to.val() ? to.val() : maxi ],
			slide: function( e, ui ) {
				from.val( ui.values[ 0 ] )
				to.val( ui.values[ 1 ] )
			},
			change: function(e, ui) {
				if ( parseFloat(to.val()) > 0 );
					from.parent().trigger('preview')
			}
		})

*/		

/*		if ( from && to ) {
			from.val( sliderRange.slider( "values", 0 ) )
			to.val( sliderRange.slider( "values", 1 ) )
			from.change( function(){
				from.val( from.val().replace(/\D/g,'') )
				if( parseFloat(from.val()) > parseFloat(to.val()) ) {
					sliderRange.slider( "values", 1 , from.val()*1 + stepf )
					to.val( from.val()*1 + stepf )
				}
				sliderRange.slider( "values", 0 , from.val() )

			})
			to.change( function(){
				to.val( to.val().replace(/\D/g,'') )
				if( ! parseFloat(to.val()) )
					to.val(10)
				if( parseFloat(to.val()) < parseFloat(from.val()) ) {
					sliderRange.slider( "values", 0 , to.val()*1- stepf )
					from.val( to.val()*1 - stepf )
				}
				sliderRange.slider( "values", 1 , to.val() )
			})

		}*/

	})

	/* Rating */
	if( $('#rating').length ) {
		var iscore = $('#rating').next().html().replace(/\D/g,'')
		$('#rating span').remove()
		$('#rating').raty({
		  start: iscore,
		  showHalf: true,
		  path: '/css/skin/img/',
		  starHalf: 'star_h.png',
		  starOn: 'star_a.png',
		  starOff: 'star_p.png',
		  hintList: ['плохо', 'удовлетворительно', 'нормально', 'хорошо', 'отлично'],
		  click: function( score ) {
		  		$.getJSON( $('#rating').attr('data-url').replace('score', score ) , function(data){
		  			if( data.success === true && data.data.rating ) {
		  				$.fn.raty.start( data.data.rating ,'#rating' )
		  				$('#rating').next().html( data.data.rating )
		  			}
		  		})
		  		$.fn.raty.readOnly(true, '#rating')
		  	}
		})
	}
	/* --- */
    $(this).find('.ratingbox A').hover(function(){
        $("#ratingresult").html(this.innerHTML)
        return false;
    });
	//TODO buy bottons remake
    /*$(".yellowbutton").mousedown(function()   {
    	$(this).toggleClass("yellowbuttonactive")
    }).mouseup(function()   {
    	$(this).removeClass("yellowbuttonactive")
    })

    $(".whitebutton").mousedown(function()   {
    	$(this).toggleClass("whitebuttonactive")
    }).mouseup(function()   {
    	$(this).removeClass("whitebuttonactive")
    })

    $(".whitelink").mousedown(function()   {
    	$(this).toggleClass("whitelinkactive")
    }).mouseup(function()   {
    	$(this).removeClass("whitelinkactive")
    })*/

    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

    $(".goodsbar .link2").bind( 'click.css', function()   {
        //$(this).addClass("link2active")
    })

    $(".goodsbar .link3").bind( 'click.css', function()   {
        //$(this).addClass("link3active");
    })
	/* left menu */
	/*
	$('.bCtg__eL2').toggle( 
		function(){
			$('.bCtg__eL3').hide()
			$('.bCtg__eL2').show()
		}, function(){
			function recShow( jnode ) {
				if( jnode.next() && jnode.next().hasClass('bCtg__eL3') ) {
					jnode.next().show()				
					recShow( jnode.next() )
				}	
			}
			recShow( $(this) )
		}
	)
	*/
	/* top menu */
	if( $('.topmenu').length ) {
		$.get('/category/main_menu', function(data){
			$('.header').append( data )
		})
	}

	var idcm          = null // setTimeout
	var currentMenu = 0 // ref= product ID
	var corneroffsets = [167,222,290,362,435,515,587,662,717]

	$('.topmenu a').bind( {
		'mouseenter': function() {
			$('.extramenu').hide()
			var self = this

			function showList() {
				if(	$(self).data('run') ) {
					var i = $(self).attr('class').replace(/\D+/,'')
					var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
					if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
						punkt.show().find('.corner').css('left',corneroffsets[i-1])
				}
			}
			$(self).data('run', true)
			currentMenu = $(self).attr('id').replace(/\D+/,'')
			idcm = setTimeout( showList, 300)
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
	/* bill typewriter */

	/*if( $('.chequebottom ul').length && ! $('.error_list').length ) {
		$('.chequebottom li div').hide()
		$('.chequebottom li strong').hide()
		$('.chequebottom .total strong').hide()
		var rubl = $('<span class="rubl">p</span>')
		function recF( i ) {
			if( i == $('.chequebottom li').length ) {
				var total = $('.chequebottom .total strong')
				total.show().find('span').remove()
				total.typewriter( 700, function(){ total.append( rubl ) })
			}
			if( i < $('.chequebottom li').length )
				$('.chequebottom li').eq(i).find('div').show().typewriter( 1000, function(){
					$('.chequebottom li').eq(i).find('strong').show()
					recF(i+1)
				})
		}

		recF(0)

	}*/
	/* CART */
	function printPrice ( val ) {

		var float = (val+'').split('.')
		var out = float[0]
		var le = float[0].length
		if( le > 6 ) { // billions
			out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le )
		} else if ( le > 3 ) { // thousands
			out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )
		}
		if( float.length == 2 )
			out += '.' + float[1]
		return out// + '&nbsp;'
	}

	var total = $('.allpageinner > .basketinfo .price')

	function getTotal() {
		for(var i=0, tmp=0; i < basket.length; i++ ) {
			if( ! basket[i].noview )
				tmp += basket[i].sum * 1
		}
		if( !tmp ) {
			location.reload(true)
		}
		total.html( printPrice( tmp ) )
		total.typewriter(800)
	}

	function basketline ( nodes ) {
		var self = this

		$(nodes.less).data('run',false)
		$(nodes.more).data('run',false)
			var main = $(nodes.line)
		var deladd   = $(nodes.more).parent().attr('href')
		var drop     = $(nodes.drop).attr('href')
		var  price   = $(nodes.price).html().replace(/\s/,'')
		this.sum     = $(nodes.sum).html().replace(/\s/,'')
		this.quantum = $(nodes.quan).html().replace(/\D/g,'')
		this.noview  = false
		var dropflag = false

		this.calculate = function( q ) {
			self.quantum = q
			self.sum = price * q
			$(nodes.sum).html( printPrice( self.sum ) )
			$(nodes.sum).typewriter(800, getTotal)
//			getTotal()
		}

		this.clear = function() {
			$.getJSON( drop , function( data ) {
				$(nodes.drop).data('run',false)
				if( data.success ) {
					main.hide()
					self.noview = true
					getTotal()
				}
			})
		}

		this.update = function( minimax, delta ) {
			$.getJSON( deladd + '/'+ delta , function( data ) {
				$(minimax).data('run',false)
				if( data.success && data.data.quantity ) {
					$(nodes.quan).html( data.data.quantity + ' шт.' )
					self.calculate( data.data.quantity )
					//var liteboxJSON = ltbx.restore()
					//liteboxJSON.vitems += delta
					//liteboxJSON.sum    += delta * price
					//ltbx.update( liteboxJSON )
				}
			})
		}

		$(nodes.drop).click( function() {
			if(! $(nodes.drop).data('run') ) {
				$(nodes.drop).data('run', true)
				dropflag = self.clear()
			}
			return false
		})

		$(nodes.less).click( function() {
			var minus = this

			if( ! $(minus).data('run') ) {
				$(minus).data('run',true)
				if( self.quantum > 1 )
					self.update( minus, -1 )
				else
					self.clear()
			}
			return false
		})

		$(nodes.more).click( function() {
			var plus = this

			if( ! $(plus).data('run') ) {
				$(plus).data('run',true)
				self.update( plus, 1 )
			}
			return false
		})

	} // object basketline

	var basket = []

	$('.basketline').each( function(){
		var tmpline = new basketline({
						'line': $(this),
						'less': $(this).find('.ajaless'),
						'more': $(this).find('.ajamore'),
						'quan': $(this).find('.ajaquant'),
						'price': $(this).find('.basketinfo .price'),
						'sum': $(this).find('.basketinfo .sum'),
						'drop': $(this).find('.basketinfo .whitelink')
						})
		basket.push( tmpline )
	})

	/* ---- */
	/* tags */
	$('.fm').toggle( function(){
		$(this).parent().find('.hf').slideDown()
		$(this).html('скрыть')
	}, function(){
		$(this).parent().find('.hf').slideUp()
		$(this).html('еще...')
	})
	/* ---- */
	/* cards carousel  */

	function cardsCarousel ( nodes ) {
		var self = this
		var current = 1
		var max = $(nodes.times).html() * 1
		var wi  = $(nodes.width).html().replace(/\D/g,'')
		var buffer = 2
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
			for(var j = (current - 1) * 3 ; j < current  * 3 ; j++) {
				boxes.eq( j ).show()
			}
		}

		$(nodes.next).bind('click', function() {
			if( current < max && !ajaxflag ) {
				if( current + 1 == max ) { //the last pull is loaded , so special shift
					var boxes = $(nodes.wrap).find('.goodsbox')
					$(boxes).hide()
					var le = boxes.length
					var rest = ( wi % 3 ) ?  wi % 3  : 3
					for(var j = 1; j <= rest; j++)
						boxes.eq( le - j ).show()
					current++
				} else {
					if( current + 1 >= buffer ) { // we have to get new pull from server
						$(nodes.next).css('opacity','0.4') // addClass dont work ((
						ajaxflag = true
						$.get( $(nodes.prev).attr('data-url') + '?page=' + (buffer+1), function(data) {
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
		})

		$(nodes.prev).click( function() {
			if( current > 1 ) {
				current--
				shiftme()
				self.notify()
			}
		})

	} // cardsCarousel object


	$('.carouseltitle').each( function(){
		var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.rubrictitle strong'),
					'wrap'  : $(this).find('~ .carousel').first()
					})
	})

	/* ---- */
	/* charachteristics */
	if ( $('#toggler').length ) {
		$('#toggler').toggle( function(){
			$('.descriptionlist:first').slideUp()
			$('.descriptionlist.second').slideDown()
			$(this).html('Общие характеристики')
		},  function(){
			$('.descriptionlist.second').slideUp()
			$('.descriptionlist:first').slideDown()
			$(this).html('Все характеристики')
		})
	}
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

});
