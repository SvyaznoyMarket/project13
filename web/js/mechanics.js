/*
	Mechanics @ enter.ru 
	(c) Ivan Kotov, Enter.ru
	v 0.2

	jQuery is prohibited
							*/


function Lightbox( jn, data ){
	if(! $(jn).length ) 
		return null
	var self = this
	
	var init = data
	var plashka = jn
	var bingobox = null
	var flybox = null
	var firedbox = 0

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
	
	
	this.getBasket = function( item ) {
		flybox.clear()	
		item.price = item.price.replace(/\s/,'')		
		init.basket = item
		init.sum += item.price * 1
		item.sum = printPrice ( init.sum ) 		
		item.price = printPrice ( item.price ) 
		init.vitems++
		item.vitems = init.vitems
		flybox.updateItem( item )		
		$('#sum', plashka).html( item.sum )
		$('.point2 b', plashka).html( item.vitems )
		flybox.showBasket()
	}
	this.getWishes = function( item ) {	
		flybox.clear()
		item.price = item.price.replace(/\s/,'')
		item.price = printPrice ( item.price ) 
		init.wishes = item
		init.vwish++
		item.vwish = init.vwish
		flybox.updateItem( item )
		$('.point3 b', plashka).html(init.vwish)
		flybox.showWishes()
	}
	this.bingo = function( item ) {
		if( flybox != null)
			flybox.clear()
		item.price = printPrice ( item.price ) 
		init.bingo = item
		bingobox.updateItem( item )
		bingobox.showBingo()
	}
	this.getComparing = function() {	
		flybox.clear()
		bingobox.clear()
		flybox.showComparing()
	}
	
	this.clear = function() {
		flybox.clear()
		bingobox.clear()
	}
	
	this.getContainers = function() {
		$('.dropbox', plashka).show()
	}
	
	this.hideContainers = function() {
		$('.dropbox', plashka).hide()
	}
	
	this.toFire = function( i ) {
		if( firedbox )
			self.putOut( firedbox )
		firedbox = i
		$($('.dropbox', plashka)[i - 1]).addClass('active').find('p').html('Отпустите мышь')
	}
	
	this.putOut = function( i ) {
		$($('.dropbox', plashka)[i - 1]).removeClass('active').find('p').html('Перетащите сюда')
	}
	
	this.putOutBoxes = function() {
		if( firedbox ){
			for(var i = 1; i < 4; i++ )
				self.putOut( i )
			firedbox = 0
		}
	}
	
	this.gravitation = function( ) {
		if( firedbox ) {	
			return firedbox
		} else return false
	}
	
	// initia
	if( init  ) {
		if( init.name ) {
			$('.fl .point', plashka).removeClass('point1').addClass('point6').html('<b></b>' + init.name)
		}
		if( init.vcomp ) {
			$('.point4 b', plashka).html(init.vcomp)
		}
		if( init.vwish ) {
			$('.point3 b', plashka).html(init.vwish)
		}		
		if( init.sum ) {
			$('#sum', plashka).html( printPrice(init.sum ) )
		}		
		if( init.vitems ) {
			$('.point2 b', plashka).html(init.vitems)
		}		
		if ( init.bingo ){
			var li = $('<li>').addClass('fl').html(
				'<a class="point point5" href="">'+
				'<b></b></a>' )
			$('.lightboxmenu').prepend( li )
			li.bind('click', function(){
				self.bingo( init.bingo )
				return false		
			})
			bingobox = new Flybox( jn )			
			self.bingo( init.bingo )
		}
	}
	
	setTimeout( function () { plashka.fadeIn('slow') }, 2000)
	flybox = new Flybox( jn )
	
} // Lightbox object

function Flybox( parent ){
// TODO
//для конкретных блоков всплытия нужны гиперссылки

	if(! $(parent).length ) 
		return null
		
	var box = $('<div>').addClass('flybox').css('display','none')
	var crnr = $('<i>').addClass('corner').appendTo( box )
	var close = $('<i>').addClass('close').attr('title','Закрыть').html('Закрыть').appendTo( box )	
	box.appendTo( parent )
	
	var self = this
	var hidei = 0	
	var thestuff = null
	
	close.bind('click', function(){
		clearTimeout( hidei )
		self.jinny()
	})
	
	this.updateItem = function( item ) {
		thestuff = item
	}
	
	var basket  = ''
	var wishes  = ''
	var rcmndtn = ''

	
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
			'</div>	'
	
		box.css({'left':'400px','width':'290px'})
		crnr.css('left','132px')
		this.fillup ( wishes )
		box.fadeIn(1000)
		hidei = setTimeout( self.jinny, 5000 )
	}

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
			'<input class="button yellowbutton" type="button" value="Купить"></div>'
			
		box.css({'left':'3px','width':'250px'})
		crnr.css('left','27px')		
		this.fillup (rcmndtn)
		box.fadeIn(1000)
		hidei = setTimeout( self.jinny, 5000 )
	}

	this.showComparing = function() {
		box.css({'left':'3px','width':'874px'})
		crnr.css('left','374px')			
		this.fillup ( $('#zaglu').html() )
		box.fadeIn(1000)
		hidei = setTimeout( self.jinny, 7000 )
	}

	this.showBasket = function() {
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
			'</div>'+
			'<div class="clear pb10"></div>'+
			'<div class="line pb5"></div>'+
			'<div class="fr">Сумма: '+ thestuff.sum +' Р</div>'+
			'Всего товаров: '+ thestuff.vitems +
			'<div class="clear pb10"></div>'+
			'<div class="ar">'+ 
				'<a class="button bigbuttonlink" value="" href="">Оформить заказ</a>'+
			'</div>'	
	
		box.css({'left':'588px','width':'290px'})	
		crnr.css('left','132px')	
		this.fillup (basket)
		box.fadeIn(1000)
		hidei = setTimeout( self.jinny, 5000 )
	}
	
	this.fillup = function( nodes ) {
		var tmp = $('<div>').addClass('fillup').html( nodes )
		box.append( tmp )
	}
	
	this.jinny = function() {		
		box.fadeOut(500)
		setTimeout( function() { $('.fillup', box).remove() } , 550)
	}

	this.clear = function() {		
		clearTimeout(hidei)
		box.hide()
		$('.fillup', box).remove()
	}	
} // Flybox object

function DDforLB( outer , ltbx ) {	
	if (! outer.length ) 
		return null
		
	var self     = this
	var lightbox = ltbx
	var isactive = false
	var icon     = null
	var margin   = 25 // gravitation parameter
	var wdiv2    = 30 // draged box halfwidth
	var containers = $('.dropbox')
	if (! containers.length ) 
		return null
	var abziss 	 = []		
	var ordinat  = 0
	var itemdata = null
	
	/* initia */
	var divicon = $('<div>').addClass('dragbox').css('display','none')
	var icon = $('<div>')
	divicon.append( icon )
	$(outer).append( divicon )
	var shtorka = $('<div>').addClass('graying').css('display','none')							
	$(outer).append( shtorka )
	var shtorkaoffset = 0
	
	this.prepare = function( pageX, pageY, item ) {
		itemdata = item
		$(document).bind('mousemove', function(e) {
			if(! isactive) {
				if( Math.abs(pageX - e.pageX) > margin || Math.abs(pageY - e.pageY) > margin ) {
					self.turnon(e.pageX, e.pageY)
					isactive = true
				}
			} else {
				icon.css({'left':e.pageX - wdiv2, 'top':e.pageY - shtorkaoffset - wdiv2 })
				ordinat = $(containers[0]).offset().top

				if( e.pageY + wdiv2 > ordinat - margin &&
					e.pageX + wdiv2 > abziss[0] - margin && e.pageX - 30 < abziss[2] + 70 + margin ) { // mouse in HOT area
					var cindex = 3
					if( e.pageX  < abziss[0] + 70 + margin )
						cindex = 1
					else if( e.pageX < abziss[1]  + 70 + margin )
						cindex = 2
					
					lightbox.toFire( cindex ) // to burn the box !
				} else
					lightbox.putOutBoxes() // run checking is inside
			}
		})		
	}
	
	this.turnon = function( pageX, pageY ) {
		lightbox.clear()
		shtorka.show()
		shtorkaoffset = shtorka.offset().top
		icon.html( $('<img>').attr({'width':60, 'height':60, 'alt':'', 'src': itemdata.img }) )
		icon.css({'left':pageX - wdiv2, 'top':pageY - shtorkaoffset - wdiv2 })

		divicon.show()
		lightbox.getContainers()		
		for(var i in [0,1,2]) {
			abziss[i] = $(containers[i]).offset().left
		}	
		$(document).bind('mouseup', function() {
			if( fbox = lightbox.gravitation( ) ) {
				$(document).unbind('mousemove')
				$(document).unbind('mouseup')
				icon.animate( {
						left: abziss[ fbox - 1 ] + 5,
						top: ordinat - shtorkaoffset + 5
					} , 400, 
					function() { self.finalize( fbox ) } )
			} else 
				self.turnoff()
		})		
	}
	
	this.turnoff = function() {
		isactive = false
		shtorka.fadeOut()
		divicon.hide()
		lightbox.hideContainers()
		$(document).unbind('mousemove')
		$(document).unbind('mouseup')
	}
	
	this.finalize = function( actioncode ) {
		setTimeout(function(){
			self.turnoff()
			switch( actioncode ) {
				case 1: //comparing
					lightbox.getComparing( itemdata )
					break
				case 2: //wishes 
					lightbox.getWishes( itemdata )
					break
				case 3: //basket
					lightbox.getBasket( itemdata )
					break
			}
		}, 400)
	}
	

	
} // DDforLB object

var ltbx = null

function mediaLib( jn ) {
	if ( ! jn.length ) return
	var self = this
	var popup = jn
	var gii = null
	var running360 = false
	
	this.show = function( ntype, url ) {
		var currentfunction = function(){}
		switch ( ntype ) {
			case 'image':
				currentfunction = self.openEnormous
				this.ivn = 'en'
				break
			case '360':
				currentfunction = self.open360
				this.ivn = '36'
				break
		}
		$(popup).lightbox_me({
			centered: true, 
			onLoad: function() { currentfunction( url ) }
		})		
		return false
	}
	
	this.openEnormous = function( url ) {		
		$('.close', popup).bind( 'click', function(){
			gii.destroy()
			gii = null
			$('.photobox', popup).empty()
		})
		$('<img>').attr('src', url ).appendTo('.photobox', popup)
		gii = new gigaimage( $('.photobox img', popup), 2,  $('.scale', popup))
		gii.addZoom()
	}
	
	this.open360 = function() {
		$('.close', popup).bind( 'click', function(){
			if ( lkmv ) {	
				lkmv.hide()
			}
		})
					
		if( ! running360 ){					
			lkmv.start() 
			running360 = true
		} else
			lkmv.show()        
	}
	
} // mediaLib object

$(document).ready(function(){
	document.ondragstart = document.body.onselectstart = function() {return false}
})