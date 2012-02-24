//Copyright (c) 2010 Morgan Roderick http://roderick.dk
var PubSub = {};
(function(p){
    "use strict";
    p.version = "1.0.1";
    var messages = {};
    var lastUid = -1;
    var publish = function( message, data, sync ){
        if ( !messages.hasOwnProperty( message ) ){
            return false;
        }
        
        var deliverMessage = function(){
            var subscribers = messages[message];
            var throwException = function(e){
                return function(){
                    throw e;
                };
            }; 
            for ( var i = 0, j = subscribers.length; i < j; i++ ){
                try {
                    subscribers[i].func( message, data );
                } catch( e ){
                    setTimeout( throwException(e), 0);
                }
            }
        };
        
        if ( sync === true ){
            deliverMessage();
        } else {
            setTimeout( deliverMessage, 0 );
        }
        return true;
    };
    p.publish = function( message, data ){
        return publish( message, data, false );
    };    
    p.publishSync = function( message, data ){
        return publish( message, data, true );
    };
    p.subscribe = function( message, func ){
        if ( !messages.hasOwnProperty( message ) ){
            messages[message] = [];
        }
        var token = (++lastUid).toString();
        messages[message].push( { token : token, func : func } );
        return token;
    };
    p.unsubscribe = function( token ){
        for ( var m in messages ){
            if ( messages.hasOwnProperty( m ) ){
                for ( var i = 0, j = messages[m].length; i < j; i++ ){
                    if ( messages[m][i].token === token ){
                        messages[m].splice( i, 1 );
                        return token;
                    }
                }
            }
        }
        return false;
    };
}(PubSub));

(function(){
// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
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
	/* mobile fix for Lbox position='fixed' */
	var userag    = navigator.userAgent.toLowerCase()
	var isAndroid = userag.indexOf("android") > -1
	var isOSX4    = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 ) && userag.indexOf('os 5') === -1

	if( isAndroid || isOSX4 ) {
		var isOpera = userag.indexOf("opera") > -1
		if( isOpera ) {
			$('.lightbox').hide()
		}
		$('.lightbox').css('position','absolute')
		var innerHeightM = (isOSX4) ? window.innerHeight : document.documentElement.clientHeight
		var innerWidthM  = (isOSX4) ? window.innerWidth  : document.documentElement.clientWidth
		if( isOSX4 )
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
				innerHeightM = (isOSX4) ? window.innerHeight : document.documentElement.clientHeight
				$('.lightbox').css('top', window.pageYOffset + innerHeightM -41)
			}
		}

	} // isAndroid || isOSX4

	/* Rotator */
	if($('#rotator').length) {
		$('#rotator').jshowoff({ speed:8000, controls:false })
		$('.jshowoff-slidelinks a').wrapInner('<span/>')
	}
	/* Infinity scroll */
	var ableToLoad = true
	var compact = $("div.goodslist").length
	function liveScroll( lsURL, pageid ) {
		var params = []
		if( $('.bigfilter.form').length && ( location.href.match(/_filter/) || location.href.match(/_tag/) ) )
			params = $('.bigfilter.form').parent().serializeArray()
		lsURL += '/' +pageid + '/' + (( compact ) ? 'compact' : 'expanded')
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

		if( $("#sorting").length ) {

			params.push( { name:'sort', value : $("#sorting").data('sort') }
)
		}

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
				nodd.find('div.boxhover, div.goodsboxlink').each( function(){
					var cid = $(this).data('cid') || 0
					if( cid )
						coreid.push( cid )
				})
				dlvrajax( coreid )
			}

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
			if( location.href.match(/sort=/) &&  location.href.match(/page=/) ) { // Redirect on first in sort case
				$(this).bind('click', function(){
					$.jCookies({
					name : 'infScroll',
					value : 1
					})
					location.href = location.href.replace(/page=\d+/,'')
				})
			} else
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
				next_a.attr('href', next_a.attr('href').replace(/page=\d+/,'') )

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

	//$.ajaxPrefilter(function( options ) {
	//	if( !options.url.match('search') )
	//		options.url += '?ts=' + new Date().getTime()
	//})

	//$('body').ajaxError(function(e, jqxhr, settings, exception) {
	//	$('#ajaxerror div.fl').append('<small>'+ settings.url.replace(/(.*)\?ts=/,'')+'</small>')
	//})

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

	})

	/* Rating */
	if( $('#rating').length ) {
		var iscore = $('#rating').next().html().replace(/\D/g,'')
		$('#rating span').remove()
		$('#rating').raty({
		  start: iscore,
		  showHalf: true,
		  path: '/css/skin/img/',
		  readOnly: $('#rating').data('readonly'),
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
        return false
    })

    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

    $(".goodsbar .link2").bind( 'click.css', function()   {
        //$(this).addClass("link2active")
    })

    $(".goodsbar .link3").bind( 'click.css', function()   {
        //$(this).addClass("link3active");
    })

	/* top menu */
	if( $('.topmenu').length ) {
		$.get('/category/main_menu', function(data){
			$('.bHeader').append( data )
		})
	}

	var idcm          = null // setTimeout
	var currentMenu = 0 // ref= product ID
	var corneroffsets = [152,210,270,335,395,460,535,615,690,750,810,860]//[167,222,290,362,435,515,587,662,717]
	function showList( self ) {	
		if(	$(self).data('run') ) {
			var i = $(self).find('span').attr('class').replace(/\D+/,'')
			var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
			if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
				punkt.show().find('.corner').css('left',corneroffsets[i-1])
		}
	}
	var isOSX     = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 )
	if( isAndroid || isOSX ) {
		$('.topmenu a').bind ('click', function(){
			if( $(this).data('run') )
				return true
			$('.extramenu').hide()	
			$('.topmenu a').each( function() { $(this).data('run', false) } )
			$(this).data('run', true)
			showList( this )
			return false
		})
	} else {	
		$('.topmenu a').bind( {
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

	/* CART */
	function printPrice ( val ) {
		var floatv = (val+'').split('.')
		var out = floatv[0]
		var le = floatv[0].length
		if( le > 6 ) { // billions
			out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le )
		} else if ( le > 3 ) { // thousands
			out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )
		}
		if( floatv.length == 2 )
			out += '.' + floatv[1]
		return out// + '&nbsp;'
	}

	var total = $('.allpageinner > .basketinfo .price')

	function getTotal() {
		for(var i=0, tmp=0; i < basket.length; i++ ) {
			if( ! basket[i].noview && $.contains( document.body, basket[i].hasnodes[0] ) )
				tmp += basket[i].sum * 1
		}
		if( !tmp ) {
			location.reload(true)
		}
		total.html( printPrice( tmp ) )
		total.typewriter(800)
	}

	function basketline ( nodes, clearfunction ) {
		var self = this
		this.hasnodes = $(nodes.drop)
		
		$(nodes.less).data('run',false)
		$(nodes.more).data('run',false)
			var main = $(nodes.line)
		var delurl   = $(nodes.less).parent().attr('href')
		var addurl   = $(nodes.more).parent().attr('href')
		if( delurl === '#' )
			delurl =  $(nodes.less).parent().attr('ref')
		if( typeof(delurl)==='undefined' )
			delurl = addurl + '/-1'
		var drop     = $(nodes.drop).attr('href')
		this.sum     = $(nodes.sum).html().replace(/\s/,'')
		this.quantum = $(nodes.quan).html().replace(/\D/g,'') * 1
		var price    = ( self.sum* 1 / self.quantum *1 ).toFixed(2)
		if( 'price' in nodes )
		    price    = $(nodes.price).html().replace(/\s/,'')		
		this.noview  = false
		var dropflag = false

		this.calculate = function( q ) {
			self.quantum = q
			self.sum = price * q
			$(nodes.sum).html( printPrice( self.sum ) )
			$(nodes.sum).typewriter(800, getTotal)
		}

		this.clear = function() {
			main.remove()
			self.noview = true
			if( clearfunction ) 
				clearfunction()
			
			$.getJSON( drop , function( data ) {
				$(nodes.drop).data('run',false)
				if( !data.success ) {
					location.href = location.href
				} else
					getTotal()
			})
		}

		this.update = function( minimax, delta ) {
			var tmpurl = (delta > 0) ? addurl : delurl
			self.quantum += delta
			$(nodes.quan).html( self.quantum + ' шт.' )
			self.calculate( self.quantum )
			$.getJSON( tmpurl , function( data ) {
				$(minimax).data('run',false)
				//if( data.success && data.data.quantity ) {
					//$(nodes.quan).html( data.data.quantity + ' шт.' )
					//self.calculate( data.data.quantity )
					//var liteboxJSON = ltbx.restore()
					//liteboxJSON.vitems += delta
					//liteboxJSON.sum    += delta * price
					//ltbx.update( liteboxJSON )
				//}
				if( !data.success ) {
					location.href = location.href
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
		var bline = $(this)
		var tmpline = new basketline({
						'line': bline,
						'less': bline.find('.ajaless:first'),
						'more': bline.find('.ajamore:first'),
						'quan': bline.find('.ajaquant:first'),
						'price': bline.find('.basketinfo .price:first'),
						'sum': bline.find('.basketinfo .sum:first'),
						'drop': bline.find('.basketinfo .whitelink:first')
						})
		basket.push( tmpline )
				
		if( $('div.bBacketServ.mBig', bline).length ) {
			$('div.bBacketServ.mBig tr', bline).each( function(){
				if( $('.ajaquant', $(this)).length ) {
					addLine( $(this), bline )
				}
			})
		}
		bline.find('a.link1').click( function(){
			var f1popup = $('div.bF1Block', bline)
			f1popup.show()
			       .find('.close').click( function() {
			       		f1popup.hide()
			       })
			f1popup.find('input.button').click( function() {
				   		if( $(this).hasClass('active') )
							return false
						$(this).val('В корзине').addClass('active')
						var f1item = $(this).data()
						$.getJSON( f1item.url, function(data) {
						})
						makeWide( bline, f1item )
				   		f1popup.hide()
				   })
			return false
		})
	})

	function addLine( tr, bline ) {
	
		function checkWide() {
			var buttons = $('td.bF1Block_eBuy', bline)
			var mBig = $('div.bBacketServ.mBig', bline)
			for(var i=0, l = $(buttons).length; i < l; i++) {
				if( ! $('tr[ref=' + $(buttons[i]).attr('ref') + ']', mBig).length ) {
					$(buttons[i]).find('input').val('Купить услугу').removeClass('active')
					//break
				}	
			}	
						
			if ( !$('div.bBacketServ.mBig .ajaquant', bline).length ) {	
				$('div.bBacketServ.mBig', bline).hide()							
				$('div.bBacketServ.mSmall', bline).show()
			}	
		}	
		var tmpline = new basketline({
					'line': tr,
					'less': tr.find('.ajaless'),
					'more': tr.find('.ajamore'),
					'quan': tr.find('.ajaquant'),
					//'price': '.none',
					'sum': tr.find('.price'),
					'drop': tr.find('.whitelink')
					}, checkWide)
		basket.push( tmpline )
	}		
	
	function makeWide( bline, f1item ) {
		$('div.bBacketServ.mSmall', bline).hide()
		$('div.bBacketServ.mBig', bline).show()		
		var f1lineshead = $('div.bBacketServ.mBig tr:first', bline)
		var f1linecart = tmpl('f1cartline', f1item)
		f1linecart = f1linecart.replace(/F1ID/g, f1item.fid ).replace(/PRID/g, bline.attr('ref') )
		f1lineshead.after( f1linecart )
		addLine( $('div.bBacketServ.mBig tr:eq(1)', bline) )
		getTotal()
	}

	/* tags */
	$('.fm').toggle( function(){
		$(this).parent().find('.hf').slideDown()
		$(this).html('скрыть')
	}, function(){
		$(this).parent().find('.hf').slideUp()
		$(this).html('еще...')
	})
	/* cards carousel  */

	function cardsCarousel ( nodes ) {
		var self = this
		var current = 1
		var max = $(nodes.times).html() * 1
		var wi  = nodes.width*1
		var viswi = nodes.viswidth*1
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
			var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.rubrictitle strong').html().replace(/\D/g,''),
					'wrap'  : $(this).find('~ .carousel').first(),
					'viswidth' : 3
					})
		}			
	})

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

	/* delivery ajax */
	if( $('#dlvrlinks').length ) {

		function dlvrajax( coreid ) {
			$.post( $('#dlvrlinks').data('calclink'), {ids:coreid}, function(data){
				for(var i=0; i < coreid.length; i++) {
					var raw = data[ coreid[i] ]
					if ( !raw.success )
						continue
					var self = '',
						//express = '',
						other = []
					for( var j in raw.deliveries ) {
					var dlvr = raw.deliveries[j]
						switch ( dlvr.object.token ) {
							case 'self':
								self = 'Возможен самовывоз ' + dlvr.text
								break
							case 'express':
							//	express = 'Экспресс-доставка ' + dlvr.text
								break
							default:
								other.push('Доставка ' + dlvr.text )
						}
					}
					var pnode = $( 'div[data-cid='+coreid[i]+']' ).parent()
					var tmp = $('<ul>')
					if(self)
            $('<li>').html( self ).appendTo( tmp )
//					$('<li>').html( express ).appendTo( tmp )
					for(var ii=0; ii < other.length; ii++)
						$('<li>').html( other[ii] ).appendTo( tmp )
					var uls = pnode.find( 'div.extrainfo ul' )
					uls.html( uls.html() + tmp.html() )
				}
			})
		} // dlvrajax

		var coreid = []
		$('div.boxhover, div.goodsboxlink').each( function(){
			var cid = $(this).data('cid') || 0
			if( cid )
				coreid.push( cid )
		})
		dlvrajax( coreid )
	}

	/* 
		from inline scripts
	*/
	/* agree button */
	if( $('#agree-field').length ) {
		$('#agree-field')
			.everyTime(200, function() {
			  var el = $(this)
			  if (el.next().hasClass('checked')) {
				$('#confirm-button, #pay-button').removeClass('mDisabled')
			  }
			  else {
				$('#confirm-button, #pay-button').addClass('mDisabled')
			  }
			})

		$('.form').bind('submit', function(e) {
			if ($(this).find('input.mDisabled').length) {
			  e.preventDefault()
			}
		})
	}
	/* */
	$('#watch-trigger').click(function(){
      $('#watch-cnt').toggle()
    })
    $('#watch-cnt .close').click(function(){
      $('#watch-cnt').hide()
    })
    /* some oldish ? */
    $('.point .title b').click(function(){
		$(this).parent().parent().find('.prompting').show()
	})
	$('.point .title .pr .close').click(function(){
		$(this).parent().hide()
	})

	$('#auth_forgot-link').click(function() {
		$('#auth_forgot-block').lightbox_me({
		  centered: true,
		  onLoad: function() {
			$('#auth_forgot-form').show()
			$('#auth_forgot-block').find('input:first').focus()
		  }
		})	
		return false
	})
	/* login processing */
    if( $('#order_login-url').length ) {
		var url_signin = $('#order_login-url').val(),
			url_register = $('#order_login-url').val()
		$('#radio-1').click(function(){
		  $('#old-user').show()
		  $('#old-user input').prop('disabled', null)
		  $('#new-user').hide()
		  $('#new-user input').prop('disabled', 'disabled')
		  $('#form-step-1').prop('action', url_signin)
		});
		$('#radio-2').click(function(){
		  $('#old-user').hide()
		  $('#old-user input').prop('disabled', 'disabled')
		  $('#new-user').show()
		  $('#new-user input').prop('disabled', null)
		  $('#form-step-1').prop('action', url_register)
		})
		var actionf = ( $('#module_action').length && $('#module_action').val() === 'register') ? 2 : 1
		$('#radio-'+ actionf).click()
		$('#form-step-1').submit(function(){
		  if (this.action == '') return false
		})
    }

	if( $('#user_signin-url').length ) {
		var url_signin = $('#user_signin-url').val(),
			url_register = $('#user_register-url').val()
		$('#radio-1').click(function(){
			$('#old-user').show()
			$('#old-user input').prop('disabled', null)
			$('#new-user').hide()
			$('#new-user input').prop('disabled', 'disabled')
			$('#form-step-1').prop('action', url_signin)
		})
		$('#radio-2').click(function(){
			$('#old-user').hide()
			$('#old-user input').prop('disabled', 'disabled')
			$('#new-user').show()
			$('#new-user input').prop('disabled', null)
			$('#form-step-1').prop('action', url_register)
		})
		$('#radio-1').click()
		$('#form-step-1').submit(function(){
			if (this.action == '') return false
		})
	}
	
	/* F1 */
	//if f1 block
	// add bottons bind with post
	
	// add F1:
	// add line to initial block
	// post to server 
		// flybox on return
});
