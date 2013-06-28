function brwsr () {
	var userag      = navigator.userAgent.toLowerCase();
	this.isAndroid  = userag.indexOf("android") > -1;
	this.isOSX      = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 );
	this.isOSX4     = this.isOSX && userag.indexOf('os 5') === -1;
	this.isOpera    = userag.indexOf("opera") > -1;
	
	this.isTouch    = this.isOSX || this.isAndroid;
}

function parse_url( url ) {
	if( typeof( url ) !== 'string' ){
		return false;
	}
	if( url.indexOf('?') === -1 ){
		return false;
	}
	url = url.replace('?','');
	var url_ar = url.split('&');
	var url_hash = {};
	for (var i=0, l=url_ar.length; i<l; i++ ) {
		var pair = url_ar[i].split('=');
		url_hash[ pair[0] ] = pair[1];
	}
	return url_hash;
}

/*
	Mechanics @ enter.ru 
	(c) Ivan Kotov, Enter.ru
	v 0.5

	jQuery is prohibited
							*/
function Flybox( parent ){
// TODO
//для конкретных блоков всплытия нужны гиперссылки

	if(! $(parent).length ) {
		return null;
	}
		
	var box = $('<div>').addClass('flybox').css('display','none');
	var crnr = $('<i>').addClass('corner').appendTo( box );
	var close = $('<i>').addClass('close').attr('title','Закрыть').html('Закрыть').appendTo( box );
	box.appendTo( parent );
	
	var self = this;
	var hidei = 0;
	var thestuff = null;
	
	close.bind('click', function(){
		clearTimeout( hidei );
		self.jinny();
	});
	
	this.updateItem = function( item ) {
		thestuff = item;
	};
	
	var basket  = '';
	var wishes  = '';
	var rcmndtn = '';

	
	this.showWishes = function() {
		wishes = 
			'<div class="font16 pb20">Только что был добавлен в список желаний</div>'+
			'<div class="fl width70">'+
				'<a href="">'+
					'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
				'</a>'+
			'</div>'+
			'<div class="ml70">'+
				'<div class="pb5">'+
					'<a href="">'+ thestuff.title +'</a>'+
				'</div>'+
				'<strong>'+
					thestuff.price +
					'<span class="rubl">p</span>'+
				'</strong>'+
			'</div>'+
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="ar pb10">Всего товаров: '+ thestuff.vwish +'</div>'+
			'<div class="ar">'+
				'<a class="button bigbuttonlink" value="" href="">Перейти в список желаний</a>'+
			'</div>	';
	
		box.css({'left':'400px','width':'290px'});
		crnr.css('left','132px');
		this.fillup ( wishes );
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 5000 );
	};

	this.showBingo = function() {
		rcmndtn = 
			'<div class="font16 pb20">Этот товар может пригодиться!</div>'+
			'<div class="fl width70">'+
			'<a href="">'+
			'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
			'</a>'+
			'</div>'+
			'<div class="ml70">'+
			'<div class="pb5">'+
			'<a href="">'+ thestuff.title +'</a>'+
			'</div>'+
			'<div class="pb10">'+
			'<strong>'+
			thestuff.price +
			'<span class="rubl">p</span>'+
			'</strong>'+
			'</div>'+
			'<input class="button yellowbutton" type="button" value="Купить"></div>';
			
		box.css({'left':'3px','width':'250px'});
		crnr.css('left','27px');
		this.fillup (rcmndtn);
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 5000 );
	};

	this.showComparing = function() {
		box.css({'left':'3px','width':'874px'});
		crnr.css('left','374px');
		this.fillup ( $('#zaglu').html() );
		box.fadeIn(1000);
		hidei = setTimeout( self.jinny, 7000 );
	};

	var flyboxcloser = function(e){
		var targ = e.target.className;

		if (!(targ.indexOf('flybox')+1) || !(targ.indexOf('fillup')+1)) {
			box.hide();
			$('body').unbind('click', flyboxcloser);
		}
	};


	var hrefcart = $('.point2', parent).attr('href'); //OLD: /orders/new
	this.showBasket = function( f1 ) {
		if( typeof( thestuff.link ) !== 'undefined' ) {
			hrefcart = thestuff.link;
		}
		var f1tmpl = '';
		if ( typeof(f1) !== "undefined" ){
			f1tmpl = 
				'<br/><div class="bLiteboxF1">'+
					'<div class="fl width70 bLiteboxF1__eWrap">'+
						'<div class="bLiteboxF1__ePlus">+</div>'+
						'<a href=""><img src="/images/f1info1.png" alt="" width="60" height="60" /></a></div>'+
					'<div class="ml70">'+
						'<div class="pb5 bLiteboxF1__eG"><a href>'+ f1.f1title +'</a></div>'+
						'<strong>'+ f1.f1price +' <span class="rubl">p</span></strong>'+
					'</div>'+
				'</div>';
		}
		basket = 
			'<div class="font16 pb20">Только что был добавлен в корзину:</div>'+
			'<div class="fl width70">'+
				'<a href="">'+
					'<img width="60" height="60" alt="" src="'+ thestuff.img +'">'+
				'</a>'+
			'</div>'+
			'<div class="ml70">'+
				'<div class="pb5">'+
					'<a href="">'+ thestuff.title +'</a>'+
				'</div>'+
				'<strong>'+
					thestuff.price +
					'<span> &nbsp;</span><span class="rubl">p</span>'+
				'</strong>'+
			'</div>'+ f1tmpl +
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="fr">Сумма: '+ thestuff.sum +' Р</div>'+
			'Всего товаров: '+ thestuff.vitems +
			'<div class="clear pb10"></div>'+
			'<div class="ar">'+ 
				'<a class="button bigbuttonlink" value="" href="'+ hrefcart +'">Оформить заказ</a>'+
			'</div>';
	
		box.css({'left':'605px','width':'290px'});
		crnr.css('left','142px');
		this.fillup (basket);
		box.fadeIn(500);
		// hidei = setTimeout( self.jinny, 5000 );
		$('body').bind('click', flyboxcloser);
	};

	this.showBasketF1 = function( f1 ) {
		if ( typeof(f1) === "undefined" ){
			return false;
		}
		var f1tmpl = 
			'<div class="bLiteboxF1">'+
				'<div class="fl width70 bLiteboxF1__eWrap">'+
					'<div class="bLiteboxF1__ePlus"></div>'+
					'<a href=""><img src="/images/f1info1.png" alt="" width="60" height="60" /></a></div>'+
				'<div class="ml70">'+
					'<div class="pb5 bLiteboxF1__eG"><a href>'+ f1.f1title +'</a></div>'+
					'<strong>'+ f1.f1price +' <span class="rubl">p</span></strong>'+
				'</div>'+
			'</div>';
		basket = 
			'<div class="font16 pb20">Только что был добавлен в корзину:</div>'+
			f1tmpl +
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="fr">Сумма: '+ thestuff.sum +' Р</div>'+
			'Всего товаров: '+ thestuff.vitems +
			'<div class="clear pb10"></div>'+
			'<div class="ar">'+ 
				'<a class="button bigbuttonlink" value="" href="'+ hrefcart +'">Оформить заказ</a>'+
			'</div>';
	
		box.css({'left':'588px','width':'290px'});
		crnr.css('left','132px');
		this.fillup (basket);
		box.fadeIn(500);
		// hidei = setTimeout( self.jinny, 5000 );
		$('body').bind('click', flyboxcloser);
	};
	
	this.fillup = function( nodes ) {
		var tmp = $('<div>').addClass('fillup').html( nodes );
		box.append( tmp );
	};
	
	this.jinny = function() {		
		box.fadeOut(500);
		setTimeout( function() {
			$('.fillup', box).remove();
		} , 550);
	};

	this.clear = function() {		
		clearTimeout(hidei);
		box.hide();
		$('.fillup', box).remove();
	};
} // Flybox object

function Lightbox( jn, data ){
	if(! $(jn).length ) {
		return null;
	}
	var self = this;
	
	var init = data;
	var plashka = jn;
	var bingobox = null;
	var flybox = null;
	var firedbox = 0;
	
	this.save = function() {
		var cooka = init;
		cooka.basket={};
		docCookies.setItem( true, 'Lightbox', cooka, 20*60, '/' );
	};
	
	this.restore = function() {
		return docCookies.getItem('Lightbox', true);
	};
	
	if( ! init.name ) {
		//init = this.restore()
		if( !init ) {
			init = {
				'name':false,
				'vcomp':0, // число сравниваемых
				'vwish':0, // число товаров в вишлисте
				'vitems': 0, // число покупок
				'sum': 0, // текущая сумма покупок
				'bingo': {}
			};
		}
	}	
	
	this.getBasket = function( item ) {
		item.price +='';
		var _gafrom = ( $('.goodsbarbig').length ) ? 'product' : 'catalog';
		if ( typeof(_gaq) !== 'undefined') {
			_gaq.push(['_trackEvent', 'Add2Basket', _gafrom, item.title + ' ' + item.id, Math.round( item.price.replace(/\D/g,'') ) ]);
		}
		flybox.clear();
		item.price = item.price.replace(/\s+/,'');
		init.basket = item;
		init.sum = item.sum * 1;
		//if ( parseInt(init.sum) == parseInt(item.price) )
			$('.total').show();
		item.sum = printPrice ( init.sum );
		item.price = printPrice ( item.price );
		//init.vitems++
		//item.vitems = init.vitems
		init.vitems = item.vitems;
		flybox.updateItem( item );				
		$('#sum', plashka).html( item.sum );
		$('.point2 b', plashka).html( item.vitems );
		this.fillTopBlock();
		if( 'f1' in item ) {
			if( 'only' in item.f1  ) {
				flybox.showBasketF1( item.f1 );
			}
			else {
				flybox.showBasket( item.f1 );
			}
		}
		else {
			flybox.showBasket();
		}
		//self.save()
	};

	this.getWishes = function( item ) {	
		flybox.clear();
		item.price = item.price.replace(/\s/,'');
		item.price = printPrice ( item.price );
		init.wishes = item;
		init.vwish++;
		item.vwish = init.vwish;
		flybox.updateItem( item );
		$('.point3 b', plashka).html(init.vwish);
		flybox.showWishes();
	};

	this.bingo = function( item ) {
		if( flybox ) {
			flybox.clear();
		}
		item.price = printPrice ( item.price );
		init.bingo = item;
		bingobox.updateItem( item );
		bingobox.showBingo();
	};

	this.getComparing = function() {	
		flybox.clear();
		if (bingobox) {
			bingobox.clear();
		}
		flybox.showComparing();
	};
	
	this.clear = function() {
		flybox.clear();
		if (bingobox) {
			bingobox.clear();
		}
	};
	
	this.getContainers = function() {
		$('.dropbox', plashka).show();
	};
	
	this.hideContainers = function() {
		$('.dropbox', plashka).hide();
	};
	
	this.toFire = function( i ) {
		//if( firedbox )
		//	self.putOut( firedbox )
		firedbox = i;
		//$($('.dropbox', plashka)[i - 1]).addClass('active').find('p').html('Отпустите мышь')
		$('.dropbox', plashka).addClass('active').find('p').html('Отпустите мышь');
	};
	
	this.putOut = function( i ) {
		$($('.dropbox', plashka)[i - 1]).removeClass('active').find('p').html('Перетащите сюда');
	};
	
	this.putOutBoxes = function() {
		if( firedbox ){
			for(var i = 1; i < 4; i++ ) {
				self.putOut( i );
			}
			firedbox = 0;
		}
	};
	
	this.gravitation = function( ) {
		if( firedbox ) {	
			return firedbox;
		}
		else {
			return false;
		}
	};
	
	this.fillTopBlock = function() {
		if( $('#topBasket') ) {
			$('#topBasket').text( '('+init.vitems+')' );
		}
	};
	
	this.update = function( newinit ) {
		if ( newinit ) {
			init = newinit;
		}
		if( init  ) {
			if( init.name ) {
				$('.fl .point', plashka).removeClass('point1').addClass('point6').html('<b></b>' + init.name);
			}
			if( init.link ) {
				$('.point6', plashka).attr('href', init.link );
			}
			if( init.vcomp ) {
				$('.point4 b', plashka).html(init.vcomp);
			}
			if( init.vwish ) {
				$('.point3 b', plashka).html(init.vwish);
			}		
			if( init.sum ) {
				$('#sum', plashka).html( printPrice(init.sum ) );
				$('.total').show();
			}		
			if( init.vitems ) {
				$('.point2', plashka).addClass('orangeme');
				$('.point2 b', plashka).html(init.vitems);
				this.fillTopBlock();
			}		
			if ( init.bingo && init.bingo.id ){
				var li = $('<li>').addClass('fl').html(
					'<a class="point point5" href="">'+
					'<b></b></a>' );
				$('.lightboxmenu').prepend( li );
				li.bind('click', function(){
					self.bingo( init.bingo );
					return false;
				});
				bingobox = new Flybox( jn );
				self.bingo( init.bingo );
			}
		}
	};
	
	this.authorized = function(){
		if( init.name ) {
			return true;
		}
		else {
			return false;
		}
	};

	this.isCredit = function(){
		if( 'is_credit' in init ) {
			if( init.is_credit ) {
				return true;
			}
		}
		return false;
	};

	// initia
	this.update();
	//setTimeout( function () { plashka.fadeIn('slow') }, 2000)
	flybox = new Flybox( jn );
	
}

function mediaLib( jn ) {
	if ( ! jn.length ) {
		return;
	}
	var self = this;
	var popup = jn;
	var gii = null;
	var running360 = false;
	var vis = false;
	
	this.show = function( ntype, url ) {
		if (! vis ) {
			var currentfunction = function(){};
			switch ( ntype ) {
				case 'image':
					currentfunction = self.openEnormous;
					break;
				case '360':
					currentfunction = self.open360;
					break;
			}
			
			$(popup).lightbox_me({
				centered: true, 
				onLoad: function() {
						currentfunction( url );
					},
				onClose: function() {
						self.close();
						vis = false;
					},
				reallyBig: true	
			});
			vis = true;
		}
		else { // toggle
			self.close();
			switch ( ntype ) {
				case 'image':
					$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
					gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
					gii.addZoom();
					break;
				case '360':
					if( ! running360 ){					
						if( typeof(lkmv.start)!=='undefined' ) {
							lkmv.start();
						}
						running360 = true;
					}
					else{
						if( typeof(lkmv.show)!=='undefined' ) {
							lkmv.show();
						}
					}
					break;
			}
		}
		
		return false;
	};
	
	this.close = function() {
		if ( gii ) {
			gii.destroy();
			gii = null;		
			$('#gii').remove();
		}
		if ( running360 && lkmv ) {	
			if( typeof(lkmv.hide)!=='undefined' ) {
				lkmv.hide();
			}
		}
	};
	
	this.openEnormous = function( url ) {				
		$('<img>').attr('src', url ).attr('id','gii').appendTo($('.photobox', popup));
		gii = new gigaimage( $('#gii'), 2,  $('.scale', popup));
		gii.addZoom();
	};
	
	this.open360 = function() {	
		if( ! running360 ){					
			if( typeof(lkmv.start)!=='undefined' ) {
				lkmv.start();
			}
			running360 = true;
		} else
			if( typeof(lkmv.show)!=='undefined' ) {
				lkmv.show();
			}
	};
	
} // mediaLib object

/* Credit Brokers */
var DirectCredit = {

	basketPull : [],

	output : null,
	input  : null,

	init : function( input, output ) {
		if( !input || !output ) {
			return 'incorrect input data';
		}
		this.input  = input;
		this.output = output;
		for( var i=0, l=input.length; i < l; i++ ) {
			var tmp = {
				id : input[i].id,
				price : input[i].price,
				count : input[i].quantity,
				type : input[i].type
			};
			
			this.basketPull.push( tmp );
		}
		this.sendCredit();
	},

	change : function( message, data ) {
		var self = DirectCredit;
		if( data.q > 0 ) {
			var item = self.findProduct( self.basketPull, data.id );
			if( item < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			item.count = data.q;
		} else {
			var key = self.findProductKey( self.basketPull, data.id );
			if( key < 0 ) {
				PubSub.publish( 'bankAnswered', null ); // hack
				return;
			}
			self.basketPull.splice( key, 1 );
		}
		self.sendCredit();
	},

	findProduct : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return array[key];
			}
		}
		return -1;
	},

	findProductKey : function( array, id) {
		for( var key=0, lk=array.length; key < lk; key++ ) {
			if( array[key].id == id ) {
				return key;
			}
		}
		return -1;
	},
	
	sendCredit : function(  ) {
		var self = this;
		dc_getCreditForTheProduct(
			'4427',
			'none',
			'getPayment', 
			{ products : self.basketPull },
			function(result){                       
				//var creditPrice = 0
				// for( var i=0, l=self.basketPull.length; i < l; i++ ) {
				//  var item = self.findProduct( self.basketPull, result.products[i].id )
				//  if( item ) {
				//      var itemPrice = item.price
				//      creditPrice += result.products[i].initial_instalment * itemPrice/100 * item.count
				//  }
					
				// }               
				self.output.text( printPrice( Math.ceil( result.payment ) ) );
				PubSub.publish( 'bankAnswered', null );
			}
		);
	}
}; // DirectCredit singleton


/* Date object upgrade */
if ( !Date.prototype.toISOString ) {
	
	( function() {
	
		function pad(number) {
			var r = String(number);
			if ( r.length === 1 ) {
				r = '0' + r;
			}
			return r;
		}
 
		Date.prototype.toISOString = function() {
			return this.getUTCFullYear() +
				'-' + pad( this.getUTCMonth() + 1 ) +
				'-' + pad( this.getUTCDate() ) +
				'T' + pad( this.getUTCHours() ) +
				':' + pad( this.getUTCMinutes() ) +
				':' + pad( this.getUTCSeconds() ) +
				'.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 ) +
				'Z';
		};
  
	}() );
}

function parseISO8601(dateStringInRange) {
	var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)\s*$/,
		date = new Date(NaN), month,
		parts = isoExp.exec(dateStringInRange);

	if (parts) {
		month = +parts[2];
		date.setFullYear(parts[1], month - 1, parts[3]);
		if (month != date.getMonth() + 1) {
			date.setTime(NaN);
		}
	}
	return date.getTime();
};
