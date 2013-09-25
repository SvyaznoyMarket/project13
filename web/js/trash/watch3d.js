/*
	360-degree Slideshow 
	'Watch 3d'
	Ivan Kotov
	v 2.6

	jQuery is prohibited
							*/
/*							
	2.6
	http://jira.ent3.ru/browse/SE-196
	
	new in the v.2:
	animation using canvas,	not img src
	fast preloading
	lite version and full version are sooo different
							*/							
/* 
	api = {
		'makeLite' : '#turnlite',
		'makeFull' : '#turnfull',
		'loadbar'  : '#percents',
		'zoomer'   : '#bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['.versioncontrol','.scrollbox'] // for toggle()
	}
*/
var cnvCompatible = document.createElement('canvas').getContext

function likemovie( nodename , apinodes, s, b) {
	this.mvblock = $(nodename)
	if( ! this.mvblock.length )
		return false
	if( !s || s.length != 40 || ( b && b.length != 40) )
		return false	
	var apinodes = apinodes ? apinodes : {}
	var smURLs = s
	var bURLs  = b 
	
	var self = this
	
	var iid  = null //setInterval
	var ccid = null //setInterval
	var ssid = null //setTimeout
	this.completenessIntrvl = 600
	this.rollingIntrvl      = 400	

	var vzooms     = [500, 1200, 2500]	
	this.initres   = vzooms[0]
	var initInd    = [1,11,21,31]
	var indexes    = initInd

	this.howmany = 40	
	this.zoom    = 1 /* \in {1,2,3} */ //TODO reduce
	this.imgzoom = 0 //TODO reduce
	
	this.mode    = 'slow'		
			
	var frontier    = null // 'img' or 'canvas' is our hero
	var loader      = null // loader bar
	var zoo         = null // zoomer is plus&minus buttons
	var gi          = null // gigaimage object for hero
	var jrollindex  = null // rolling stone
	var cordinates  = [    // for the rolling stone
		[110, 28], [121, 28], [132, 27], [143, 26], [154, 25], [165, 24], [176, 23], [187, 21], [198, 19], [209, 16],
		[220, 13], [209, 10], [198, 7], [187, 6], [176, 5], [165, 4], [154, 3], [143, 2], [132, 1], [121, 0], 
		[110, 0], [99, 0], [88, 1], [77, 2], [66, 3], [55, 4], [44, 5], [33, 6], [22, 7], [11, 10],
		[0, 13], [11, 16], [22, 19], [33, 21], [44, 23], [55, 24], [66, 25], [77, 26], [88, 27], [99, 28]
	]
	var initx       = 0
	var flnm        = 1
	var pointer     = 1	
	var play        = false // rolling in action
	var liteversion = false // lite version is active
	if ( !b ) 
		liteversion = true
	var abletolite  = true  // able to turn with lite version
	var evstamp     = 0     // event stamp
	var actor       = 'roll'// or 'drop'

	var mvblockW      = 0
	var mvblockH      = 0
	var cnvimg = { // init in start()
		x: 0,
		y: 0
	}
	var frontierctx = null // 2d context for canvas hero		
	var tmptgl      = true // for this.toggle()

	var manualroll = false	
	this.prefx     = 'ib' // {'is', 'ib'} image small or image big, preloader prefx
	var flags      = []
	var toload     = 0
	for(var i=0; i < self.howmany; i++)
		flags[i] = 0		
	
/* ---------------------------------------------------------------------------- */ /* API */
/* API */

	this.api = function() {
		if( apinodes.zoomer ) 
			zoo   = $(apinodes.zoomer)
		loader = new loadbar
		loader.create(  apinodes.loadbar ? apinodes.loadbar : 0)
		this.makeLite() // includes this.makeFull()		
		if( apinodes.rollindex ) 
			jrollindex = $( apinodes.rollindex )
		this.toggle()	
	}
	
	this.toggle = function() {
		if( ! apinodes.propriate ) 
			return false
		for(var i in apinodes.propriate ) 
			if ( tmptgl )
				$( apinodes.propriate[i] ).show()
			else 
				$( apinodes.propriate[i] ).hide()
		tmptgl = ! tmptgl		
	}
	
	this.hideVersions = function() {
		if( apinodes && apinodes.makeLite )
			$(apinodes.makeLite).hide()
		if( apinodes && apinodes.makeFull )
			$(apinodes.makeFull).hide()	
	}	
	
	this.makeLite = function() {
		var mlnode = null
		if( apinodes && apinodes.makeLite )
			mlnode = $(apinodes.makeLite)
		else {
			mlnode = $('<div>').attr('id','ml').html('make lite').appendTo('body')
			apinodes.makeLite = '#ml'
		}	
		mlnode.css('cursor','pointer')
			  .bind({
				  'click': function() {
				  	self.turnVersion()	 
				  	//mlnode.unbind('click')
				  	mlnode.hide()
				  	self.makeFull()
				  }
			  })
	}
	
	this.makeFull = function() {
		var mfnode = null
		if( apinodes && apinodes.makeFull )
			mfnode = $(apinodes.makeFull)
		else {
			mfnode = $('<div>').attr('id','mf').html('make full').appendTo('body')
			apinodes.makeFull = '#mf'
		}	
		mfnode.css('cursor','pointer')
			  .show()
			  .bind({
				  'click': function() {
				  	self.turnVersion()	 
				  	//mfnode.unbind('click')
				  	mfnode.hide()
				  }
			  })  
	}	

	this.rollshift = function( rollindex ) {
		rollindex = ( rollindex - 5 < 0 ) ? 34 + rollindex : rollindex - 5
		jrollindex.css('left', cordinates[rollindex][0]).css('top', cordinates[rollindex][1])
	}
	
	this.manualRollEnable = function() {
		jrollindex.bind({
			'mousedown': function(e){ // manual roll
				e.preventDefault()			
				self.stopRolling()
				manualroll = true
				var mousex = e.pageX
				var curPos =  jrollindex.offset().left
			}
		})
		
		$(document).mousemove ( function(e) {		
			if( manualroll ){
				var constoffset = 11
				var mousex = e.pageX
				var mousey = e.pageY
				var curPos =  jrollindex.offset()	
				if (Math.abs(curPos.left - mousex) > constoffset ) {
					mouseIsLeft = ( curPos.left > mousex ) ? true : false
					mouseIsTop  = ( curPos.top  > mousey ) ? true : false					
					if ( flnm - 1 - 5 < self.howmany / 4 || flnm - 1 - 5 >= self.howmany / 4 * 3 ) { // bottom of ellipse-curve
						if ( flnm - 1 - 5 == self.howmany / 4 * 3  ) {
							if( ! mouseIsLeft && ! mouseIsTop )
								self.nextSrc( 1 )
							if( ! mouseIsLeft && mouseIsTop )
								self.nextSrc( -1 )
						} else 
							( mouseIsLeft ) ? self.nextSrc( -1 ) : self.nextSrc( 1 )
					} else { // top of ellipse-curve
						if ( flnm - 1 - 5 == self.howmany / 4  ) {
							if( mouseIsLeft && mouseIsTop) 
								self.nextSrc( 1 )
							if( mouseIsLeft && ! mouseIsTop) 
								self.nextSrc( -1 )
						} else
							( ! mouseIsLeft ) ? self.nextSrc( -1 ) : self.nextSrc( 1 )						
					}
				}	
			}
			
		})
		
		$(document).mouseup (function (e) {
				manualroll = false
		})		
	}
	
/* ---------------------------------------------------------------------------- */ /* Preload */
/* Preload */	

	this.breakPreload = function() {
		clearInterval(ccid)
		if(liteversion) {
			for(var j=0; j < self.howmany; j++)
				if( flags[j] === 1 ) flags[j] = 0
		}	
		for(var i = indexes.length; i > 0; i--) {
			if (! document.getElementById( self.prefx + indexes[i-1] ).complete) {
				$('#'+ self.prefx + indexes[i-1] ).remove()
			}	
		}
		self.prefx = (liteversion) ? 'is' : 'ib'
	}
	
	this.preloadImages = function(ind) {
		if( !liteversion && $('#'+self.prefx+'1').length ) { // first call is out
			ind = []
			for(var j=0; j < self.howmany, ind.length < 5; j++)
				if( flags[j] != 2 )					
					ind.push(j+1)
		}
		var URLs = liteversion ? smURLs : bURLs
		var buffer = $('<div>')
		for(var i = 0; i < ind.length; i++) {
			$('<img>').attr('src', URLs[ ind[i] - 1 ] )
					  .attr('id', self.prefx + ind[i])
					  .appendTo(buffer)
					  .bind('load',function(){ 
					  	self.preloadOnebyone( $(this).attr('id').replace(/\D/g,'') )
					  })
		}
		(liteversion) ? $('#nvis500').append(buffer) : $('#nvis').append(buffer)
	}	
		
	this.preloadOnebyone = function( cur ) { //FULLVERSION
		if( liteversion || tmptgl ) 
			return false
		flags[cur-1] = 2
		var tmploaded = 0
		for(var j=0; j < self.howmany; j++)
			if( flags[j] == 2 )
				tmploaded++
		loader.update( tmploaded / self.howmany * 100 )
		if( tmploaded == self.howmany ) {
			for(var i=0; i < self.howmany; i++)
				initInd[i]=i+1
			//clearInterval(ccid)
			self.hideVersions()
			self.manualRollEnable()			
			self.stop4slides()	
			self.createFrontier()
			self.mode = 'medium'				
			self.startRolling( 70 )			
			return
		}
		
		toload = 99
		for(var i=cur; i < self.howmany + cur*1 ; i++) {
			if( !flags[i % self.howmany] ) { 
				toload = i % self.howmany	
				break
			}			
		}	
		if( toload < 99 ) { // :)	
			flags[toload] = 1
			$('<img>').attr('src', bURLs[ toload ] )
					  .attr('id', self.prefx + (toload*1 + 1))
					  .appendTo( $('#nvis') )
					  .bind('load',function(){ 
					  	self.preloadOnebyone( $(this).attr('id').replace(/\D/g,'') )
					  })
		} 			  				  
	}
	
	this.checkComplete = function() {
		var loaded = 0
		for(var i = 0; i < indexes.length; i++) {
			if (document.getElementById( self.prefx + indexes[i]).complete) {
				loaded++
			}	
		}
		if ( liteversion ) 
			loader.update( (initInd.length - indexes.length + loaded ) / self.howmany * 100 ) 
		if (loaded != indexes.length)
			return
		if ( liteversion ) {
			self.nextLoad() 		
		} else {
			clearInterval(ccid)
			self.show4slides()
		}
	}	
	
	this.nextLoad = function() { //LITEVERSION
		if( initInd.length == this.howmany ) {
			clearInterval(ccid)
			this.hideVersions()
			this.manualRollEnable()
			this.speedupRolling( 70 )
			return
		}
		switch( initInd.length ) {
			case 4:
				this.startRolling()
				indexes = [6,16,26,36]
				break
			case 8:				
				indexes = [3,9,13,19,23,29,33,39]
				//this.speedupRolling( 200 )		
				break
			case 16:
				indexes = [4,8,14,18,24,28,34,38]
				break
			case 24:
				indexes = [2,10,12,20,22,30,32,40]
				break				
			case 32:
				indexes = [5,7,15,17,25,27,35,37]
				break								
		}		
		this.preloadImages( indexes )
		initInd = initInd.concat( indexes )
		initInd.sort( function(a,b) { return a - b } )		
	}	
	
/* ---------------------------------------------------------------------------- */	

	this.turnVersion = function () {
		liteversion = ! liteversion
		
		loader.update(0)
		if (liteversion) {	
			this.stop4slides()	
			this.breakPreload()
			this.preloadImages(indexes) // again, but another folder
			this.createFrontier()
			gi.setDimensionProps( vzooms[0] )
			this.zoom = 1
			gi.zoom = 1
			gi.noZoom()
			gi.addDrag()
			ccid = setInterval(self.checkComplete, self.completenessIntrvl)
			if( initInd.length > 4 ) {
				this.startRolling()
			}				
		} else {
			this.stopRolling()
			frontier.hide()
			this.breakPreload()
			indexes = [1,11,21,31] // for checkComplete in fullversion
			this.preloadImages(indexes) // again, but another folder
			ccid = setInterval(self.checkComplete, self.completenessIntrvl)	
			this.getInitSize()
			gi.setDimensionProps( vzooms[this.zoom - 1] )
			gi.zoom = this.zoom
			gi.addZoom(cnvimg)
			gi.addDrag()
		}
		
	}
	
/* ---------------------------------------------------------------------------- */ /* Rolling */
/* Rolling */

	this.startRolling = function ( velocity ) {
	
		play = true
		if(velocity) self.rollingIntrvl = velocity
		iid = setInterval(function() { self.nextSrc(1) }, self.rollingIntrvl)
	}
	
	this.stopRolling = function() {
		play = false
		clearInterval(iid) 			
	}
	
	this.speedupRolling = function( velocity ) {
		self.rollingIntrvl = velocity
		if( play ) {
			clearInterval(iid) 			
			iid = setInterval(function() { self.nextSrc(1) }, self.rollingIntrvl)			
		}		
	}

	this.nextSrc = function (direction) {		
		switch(self.mode){
			
			case 'medium': {
					iind: for(var i=0; i < initInd.length; i++) {
						if( initInd[i] == flnm ) {
							if( !i && direction < 0 ) 
								flnm = initInd[initInd.length - 1]
							else 	
								flnm = initInd[(i+1*direction) % initInd.length]
							break iind							
						}
					}		
					break
				}
			case 'slow': {
					if(flnm == 31 && initInd.length > 4){
						self.mode = 'medium'
					}				
					else
						flnm = (flnm + 10 ) % this.howmany
					break
				}	
		}
		
		if( cnvCompatible && ! liteversion) {
			frontier.attr('ref',flnm)
			frontierctx.clearRect(0, 0, 2500, 2500)	
			frontierctx.drawImage( document.getElementById( self.prefx + flnm ) , 
					cnvimg.x, cnvimg.y,  vzooms[gi.zoom-1],  vzooms[gi.zoom-1])
		} else {
			var tmpURLs = (liteversion) ? smURLs : bURLs
			self.mvblock.hide()
			frontier.attr('src', tmpURLs[ flnm - 1])		
			self.mvblock.show()
		}
		
		self.rollshift( flnm - 1 )
	}
	
/* ---------------------------------------------------------------------------- */ /* 4slides gallery */	
/* 4slides gallery */

	this.show4slides = function() {	
		var tofind = true
	
		while(tofind) {
			pointer = (pointer + 10 ) % self.howmany
			if (document.getElementById( self.prefx + pointer ).complete) {
				tofind = false
			}
		}
		frontier.fadeOut(1000)
		setTimeout ( function(){
			frontier.attr('src', bURLs[ pointer - 1 ])		
		}, 1000)
		frontier.fadeIn(1000)	
		ssid = setTimeout( self.show4slides, 3000)
	}
	
	this.stop4slides = function() {
		if(ssid) clearTimeout(ssid)		
	}		
	
/* ---------------------------------------------------------------------------- */ /* main() */
/* main() */

	this.start = function() {
		mvblockW = self.mvblock.innerWidth()
		mvblockH = self.mvblock.innerHeight()
		/* preload first four images */
		$('<div>').hide()
				  .attr('id','nvis500')
				  .appendTo('body')
		$('<div>').hide()
				  .attr('id','nvis')
				  .appendTo('body')
		this.preloadImages(initInd)
		ccid = setInterval(self.checkComplete, self.completenessIntrvl) 
		
		self.api()
		
		
		this.initres = this.getInitSize()
		var tsrc = ( liteversion ) ? smURLs[0] : bURLs[0]
		frontier = $('<img>').attr({'src': tsrc,
									'width': self.initres,
									'height': self.initres })
							 .attr('id','ivn') 
							 .css({ 'position':'relative',
									'left': Math.round( (self.mvblock.innerWidth() - self.initres ) / 2 ) ,
									'top': Math.round( (self.mvblock.innerHeight() - self.initres ) / 2 ) })
		frontier.appendTo(self.mvblock)
		if( liteversion ) { // on init
			$(apinodes.makeLite).hide()
			frontier.bind ({
				'mousedown': function (e) {
					initx = e.pageX // prohibited for rollanddrop()
				},
				'click': function() {
					self.stopRolling()
				},
				'dblclick': function() {
					self.startRolling()
				}				
			})	
			gi = new gigaimage( frontier, self.zoom, zoo, self.rollanddrop )
		}		
	}
	
	this.createFrontier = function() {
	//cnvCompatible=false
		if( ! cnvCompatible || liteversion ) {
			this.mvblock.die('.cnve')
			frontier.bind ({
				'mousedown': function (e) {
					initx = e.pageX // prohibited for rollanddrop()
				},
				'click': function() {
					self.stopRolling()
				},
				'dblclick': function() {
					self.startRolling()
				}
				
			})
			//gi.setDimensionProps( ) // frontier modifying
			
			//frontier.appendTo(this.mvblock)
			this.mvblock.css('text-align','left')
			gi = new gigaimage( frontier, self.zoom, zoo, self.rollanddrop )
			gi.addZoom()		
						
			$(window).resize( function() {
				if(self.zoom == 1) { //TODO other zooms
					frontier.css('left', Math.round( (self.mvblock.innerWidth() - self.initres ) / 2 ) )
							.css('top', Math.round( (self.mvblock.innerHeight() - self.initres ) / 2 ) )							 
				}
			})
		} else {
			$('#ivn').remove()
			if(gi) gi.destroy()
			frontier    = $('<canvas>').attr({'id':'ivn','width':mvblockW,'height':mvblockH})
			frontier.appendTo(this.mvblock)
			frontierctx = document.getElementById('ivn').getContext('2d')
			frontierctx.drawImage( document.getElementById( self.prefx + '1' ) , 
				Math.round( ( mvblockW - self.initres ) / 2 ), Math.round( ( mvblockH - self.initres ) / 2 ), self.initres, self.initres)		
			
			cnvimg = { 
				x: Math.round( ( mvblockW - self.initres ) / 2 ),
				y: Math.round( ( mvblockH - self.initres ) / 2 )
			}
			
			this.mvblock.live ( {
				'mousedown.cnve': function (e) {
					initx = e.pageX // prohibited for rollanddrop()
				},
				'click.cnve': function() {
					self.stopRolling()
				},
				'dblclick.cnve': function() {
					self.startRolling()
				}
				
			})
			gi = new gigaimage( frontier, self.zoom, zoo, self.rollanddrop )
			gi.addZoom( cnvimg )
			
			$(window).resize( function() {		
				document.getElementById('ivn').width  = self.mvblock.innerWidth()
				document.getElementById('ivn').height = self.mvblock.innerHeight()	
				//TODO centrify img in canvas
			})	
		}
	}
	
	this.hide = function() {		
		$(frontier).hide()
		if( initInd.length != this.howmany )
			this.breakPreload()
		if ( liteversion || initInd.length == this.howmany ) {			
			this.stopRolling()
		} else {
			this.stop4slides()
		}	
		this.toggle()
	}
	
	this.show = function() {	
		$(frontier).show()
		if( liteversion ) gi.addZoom(cnvimg)
		gi.addDrag()
		if( initInd.length != this.howmany ) {
			this.preloadImages(indexes)
			if( liteversion ) 
				ccid = setInterval(self.checkComplete, self.completenessIntrvl) 			
		}
		if ( liteversion || initInd.length == this.howmany ) {	
			this.startRolling()
		} else 	{
			this.show4slides()
		}	
		this.toggle()		
	}
	
/* ---------------------------------------------------------------------------- */ /* Mechanics */	
/* Mechanics */	
	
	this.rollanddrop = function(e, delta, evs ) {
		if ( evstamp != evs ) {
			evstamp = evs
			if ( Math.abs(delta.x) > Math.abs(delta.y) )
				actor = 'roll'
			else
				actor = 'drop'
		}
		var tmpdir // its a direction
		if( actor == 'roll' && Math.abs (e.pageX - initx) > 20 * gi.zoom ) {
			tmpdir = (e.pageX - initx) > 0 ? 1 : -1
			initx = e.pageX
			self.nextSrc(tmpdir)
		} else if ( actor == 'drop' ) {
			if( ! cnvCompatible || liteversion ) {
				var img = {
					y: parseInt( frontier.css('top'), 10 )			
				}		
				frontier.css('top',  delta.y + img.y)	
			} else {
				cnvimg.y += delta.y
				frontierctx.clearRect(0, 0, 2500, 2500)			
				frontierctx.drawImage( document.getElementById( self.prefx + flnm ) , 
					cnvimg.x, cnvimg.y, vzooms[gi.zoom-1],  vzooms[gi.zoom-1])
			}
		}			
	}
	
	this.getInitSize = function () {
		if( liteversion )
			this.zoom = 1
		else {	
			var w = $(window).width()
			if (w < 1030 ) { //1024
				this.zoom = 1
			} else if ( w < 1590 ) { //1280
				this.zoom = 2		
			} else { // bigsize
				this.zoom = 3		
			}
		}
		this.imgzoom = vzooms[ this.zoom - 1 ]
		return this.imgzoom
	}
	
/* ---------------------------------------------------------------------------- */ /* the END */
} // likemovie Object

function gigaimage( worknode , zoom, /* zoomer node*/ zoo, overwritefn) {

	var self        = this
	var jnode       = worknode // img or canvas 
	var tagIsCanvas = ( jnode[0].tagName == 'CANVAS' )
	var active      = false // d&d
	var initx       = {} 
	var vzooms      = [500, 1200, 2500]		
	var evstamp     = 0
	var zooObj      = null
	this.zoom       = zoom
	if( zoo ) {
		zooObj      = new zoomer( zoo , self)
	}
	if ( cnvCompatible && tagIsCanvas ) 
	var frontierctx = document.getElementById( jnode.attr('id') ).getContext('2d')
	
	this.cursorHand = function(){
		jnode.css('cursor','url(/css/skin/cursor/cursor_1.png), url(/css/skin/cursor/cursor_1.gif), url(/css/skin/cursor/cursor_ie_1.cur), crosshair')
	}
	self.cursorHand()

	this.cursorDrag = function(){
		jnode.css('cursor','url(/css/skin/cursor/cursor_2.png), url(/css/skin/cursor/cursor_2.gif), url(/css/skin/cursor/cursor_ie_2.cur), move')
	}
			
	this.setDimensionProps = function( px ) {
		var resol = px ? px : vzooms[self.zoom - 1]
		jnode.attr('width', resol)
		 	  .attr('height', resol)
			  .css('left', Math.round( (jnode.parent().innerWidth() - resol ) / 2 ) )
			  .css('top', Math.round( (jnode.parent().innerHeight() - resol ) / 2 ) )							 
	
	}
	if ( ! (cnvCompatible && tagIsCanvas ) )
		this.setDimensionProps() //TODO for canvas
	//var imageObject = new Image()
	//imageObject.src = worknode.attr('src')
	//console.info(imageObject.width)

	this.zoomIn = function(){
		var outer = jnode.parent()
		var offs = outer.offset()
		this.fixzoom(1, Math.round( outer.width() / 2 ) + offs.left, Math.round( outer.height() / 2 ) + offs.top ) //center click imitation
	}

	this.zoomOut = function(){
		var outer = jnode.parent()
		var offs = outer.offset()
		this.fixzoom(-1, Math.round( outer.width() / 2 ) + offs.left, Math.round( outer.height() / 2 ) + offs.top )
	}
	var outer = jnode.parent()

	
	this.fixzoom = function(de, mX, mY) {
		var oldzoom = this.zoom
		if(self.zoom == 1 && de < 0 || self.zoom == 3 && de > 0)
			return
		de > 0 ? self.zoom++ : self.zoom--
		de = de / Math.abs(de)
		var scale = vzooms[ self.zoom - 1] / vzooms[ oldzoom - 1]
		
		if( ! ( cnvCompatible && tagIsCanvas ) ) {
			 
			var outoffsets = jnode.parent().offset()
			var img = {
				x: parseInt( jnode.css('left'), 10 ),
				y: parseInt( jnode.css('top'), 10 )			
			}
			mX -= outoffsets.left + img.x
			mY -= outoffsets.top  + img.y
			img.x -= de * Math.abs( Math.round ( mX * (1 - scale) ) )
			img.y -= de * Math.abs( Math.round ( mY * (1 - scale) ) )		
			jnode.attr('width', vzooms[self.zoom - 1])
					.attr('height', vzooms[self.zoom - 1])
					.css({'left': img.x , 'top': img.y})
			
		} else {
		// if cnvCompatible
			var outoffsets = jnode.parent().offset()
			mX -= outoffsets.left + cnvimg.x
			mY -= outoffsets.top  + cnvimg.y
			cnvimg.x -= de * Math.abs( Math.round ( mX * (1 - scale) ) )
			cnvimg.y -= de * Math.abs( Math.round ( mY * (1 - scale) ) )
			var flnm = jnode.attr('ref')
			frontierctx.clearRect(0, 0, 2500, 2500)			
			frontierctx.drawImage( document.getElementById( lkmv.prefx + flnm ) , 
					cnvimg.x, cnvimg.y, vzooms[self.zoom-1],  vzooms[self.zoom-1])
		}
		
		if( zooObj ) {
			de > 0 ? zooObj.plus() : zooObj.minus()
		}
	}
	
	this.addZoom = function( lkmvimg ) {
		cnvimg = lkmvimg
		jnode.bind({
			'mousewheel': function (e, delta) {
				e.preventDefault()
				self.fixzoom(delta, e.pageX, e.pageY)
			}
		})
		zooObj.show()
	}
	
	this.noZoom = function() {	
		jnode.unbind('mousewheel')
		zooObj.hide()		
	}
	
	this.addDrag = function() {
		jnode.bind({
			'mousedown': function (e) {
				e.preventDefault()
				init = {
					pageX: e.pageX ,
					pageY: e.pageY
				}
				evstamp = e.timeStamp
				active = true
				self.cursorDrag()
			}
		})	
	}
	this.addDrag()
	
	//document.ondragstart = document.body.onselectstart = function() {return false} /* prevent default behaviour */
	
	$(document).bind('mousemove.zoomer', function(e) {
		if( active ){
			e.preventDefault()
			var mdelta = {
				x: e.pageX - init.pageX ,
				y: e.pageY - init.pageY
			}
			self.action( e, mdelta, evstamp, self.zoom )
			init.pageX = e.pageX				
			init.pageY = e.pageY					
		}
	})
	
	$(document).bind('mouseup.zoomer', function (e) {	
			active = false
			self.cursorHand()
	})
	
	this.dropping = function( e, mdelta ) {
		var img = {
			x: parseInt( jnode.css('left'), 10 ),
			y: parseInt( jnode.css('top'), 10)			
		}		
		jnode.css('top',  mdelta.y + img.y)
			 .css('left', mdelta.x + img.x)
	}
	
	this.action = overwritefn ? overwritefn : this.dropping // FUNCTION
	
	this.destroy = function() {
		jnode.unbind('mousedown')
		jnode.unbind('mousewheel')
		jnode.remove()
		$(document).unbind('.zoomer')			
		for(var x in this)
			delete this[x]
		self = null					
	}
	
} // gigaimage Object

function loadbar () { // creates node if doesnt exist
	this.percentage = 0
	var title       = 'loadbar'
	var ref         = null
	
	this.create = function( nodename ) {
		if( nodename ) {
			ref = $(nodename)
		} else {
			ref = $('<div>').attr('id', title).html('0%')
			ref.appendTo('body')
		}
		return ref
	}
	
	this.update = function( perc ) {
		this.percentage = perc ? Math.round(perc) : 0
		ref.html( this.percentage + '%' )
		if (this.percentage == 100) {
			this.destroy()
		}
	}
	
	this.destroy = function () { //just decorative
		setTimeout( function () { ref.fadeOut('slow') } , 2000)
	}
	
} // loadbar Object

function zoomer ( jn , /* gigaimage object */ zfunctions ) { // CAUTION, object is specific!
	if (!jn) 
		return false
	var jnode     = jn
	var nodeindex = $('.zoomind', jn)
	var dragging  = false
	var topoffsets = [54, 28, -2] // three states of zoomer
	/*
	(+ ) zone 1
	 ||	 zone 1
	 ||  zone 2
	 ||  zone 3
	(||) zone 3
	 ||  zone 3
	 ||  zone 4
	 ||  zone 5
	(- ) zone 5
	*/	
	var getZone = function( Y ){
		if( Y < topoffsets[ 2 ] + 6 ) return 1
		if( Y < topoffsets[ 1 ] - 4 ) return 2
		if( Y < topoffsets[ 1 ] + 4 ) return 3		
		if( Y < topoffsets[ 0 ] - 6 ) return 4
		return 5		
	}
	var op = nodeindex.parent().offset().top
		
	var prev = 3
	var prevZ = 3
	var Zones = [5,3,1]
	
	nodeindex.css('top', topoffsets[ zfunctions.zoom - 1] ) // initia
	
	this.hide = function() {
		jn.hide()
	}

	this.show = function() {
		jn.show()
	}
	
	$('b.plus', jn).bind('click', function() {
		if( zfunctions.zoomIn )
			zfunctions.zoomIn()
		//self.plus() in zfunctions
	})

	$('b.minus', jn).bind('click', function() {
		if( zfunctions.zoomOut )
			zfunctions.zoomOut()
		//self.minus() in zfunctions
	})
	
	nodeindex.bind({
		'mousedown': function(e){
			dragging = true
			e.preventDefault()
			e.stopPropagation()
		}, 
		'mouseup': function(){
			dragging = false
		}
	})			
	
	$('b.zoomind', jn).parent().bind({
		'mousemove': function(e){			
			if ( ! dragging ) return
			e.preventDefault()
			e.stopPropagation()
			var ntop = e.pageY - op - 3
			if( ntop < 55 && ntop > -3) {

				nodeindex.css('top', ntop )
				var delta = prev - getZone( ntop )
				if( Math.abs( delta ) && getZone( ntop ) != prevZ && getZone( ntop ) % 2 ) { // small shifting
					( getZone( ntop ) - prevZ < 0) ? zfunctions.zoomIn() : zfunctions.zoomOut()
					prevZ = getZone( ntop )
				}
				if ( Math.abs( delta ) == 2 ) {
					( delta < 0 ) ? zfunctions.zoomIn() : zfunctions.zoomOut()
					prevZ = getZone( ntop )
				} else if ( Math.abs( delta ) >= 3 ) {
					if ( delta < 0 ) {
						zfunctions.zoomIn()
						zfunctions.zoomIn()						
					} else {
						zfunctions.zoomOut()
						zfunctions.zoomOut()
					}
					prevZ = getZone( ntop )
				}
				prev = getZone( ntop )					
			}
		}, 
		'mouseleave': function(){
			//dragging = false
		}
	})			
	
	this.minus = function () {	
		if ( dragging ) return
		nodeindex.css('top', topoffsets[ zfunctions.zoom - 1] )     			
		prevZ = Zones[ zfunctions.zoom - 1 ]
		prev = Zones[ zfunctions.zoom - 1 ]	
	}
	
	this.plus = function () {
		if ( dragging ) return
		nodeindex.css('top', topoffsets[ zfunctions.zoom - 1] )
		prevZ = Zones[ zfunctions.zoom - 1 ]
		prev = Zones[ zfunctions.zoom - 1 ]		
	}
} // zoomer Object
