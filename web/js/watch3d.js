/*
	360-degree Slideshow 
	'Watch 3d'
	Ivan Kotov
	v 0.95

	jQuery is prohibited
							*/
/* 
	api = {
		'makeLite' : 'turnlite',
		'makeFull' : 'turnfull',
		'loadbar'  : 'percents',
		'zoomer'   : 'bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['versioncontrol','scrollbox']
	}
*/
function likemovie( nodename , apinodes, s, b) {

	var self = this
	
	var iid  = null //setInterval
	var ccid = null //setInterval
	var ssid = null //setTimeout
	var apinodes = apinodes ? apinodes : {}
	var smURLs = s
	var bURLs  = b
	var URLs   = null

	this.completenessIntrvl = 2000
	this.rollingIntrvl = 400	

	var vzooms  = [500, 1200, 2500]			
	var initInd = [1,11,21,31]
	var indexes = initInd
	var bimindexes = []	

	this.howmany = 40	
	this.zoom    = 1 /* {1,2,3} */
	this.imgzoom = 0
	
	this.mode    = 'slow'		
	this.initres = vzooms[0]
	this.mvblock = $(nodename)	


	var frontier    = null
	var loader      = null
	var zoo         = null	
	var gi          = null
	var jrollindex  = null

	var initx       = 0
	var flnm        = 1
	var pointer     = 1	
	var play        = false 
	var liteversion = false
	var abletolite  = true
	var evstamp     = 0
	var actor       = 'roll'

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
	
	var tmptgl = true
	
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

	var cordinates = [
			[110, 28], [121, 28], [132, 27], [143, 26], [154, 25], [165, 24], [176, 23], [187, 21], [198, 19], [209, 16],
			[220, 13], [209, 10], [198, 7], [187, 6], [176, 5], [165, 4], [154, 3], [143, 2], [132, 1], [121, 0], 
			[110, 0], [99, 0], [88, 1], [77, 2], [66, 3], [55, 4], [44, 5], [33, 6], [22, 7], [11, 10],
			[0, 13], [11, 16], [22, 19], [33, 21], [44, 23], [55, 24], [66, 25], [77, 26], [88, 27], [99, 28]
		]
	var manualroll = false	

	this.rollshift = function( rollindex ) {
		rollindex = ( rollindex - 5 < 0 ) ? 34 + rollindex : rollindex - 5
		jrollindex.css('left', cordinates[rollindex][0]).css('top', cordinates[rollindex][1])
	}
	
	this.manualRollEnable = function() {
		jrollindex.bind({
			'mousedown': function(e){ // manual roll
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
		for(var i = indexes.length; i > 0; i--) {
			if (! document.getElementById('ivn'+indexes[i-1]).complete) {
				$('#ivn'+indexes[i-1]).remove()
			}	
		}		
	}
	
	this.preloadImages = function(ind) {
		URLs = liteversion ? smURLs : bURLs
		var buffer = $("<div>")
		for(var i = 0; i < ind.length; i++) {
			$("<img>").attr("src", URLs[ ind[i] - 1 ] )
					  .attr('id','ivn'+ind[i])
					  .appendTo(buffer)		
		}
		$('#nvis').append(buffer)
	}
	
	this.checkComplete = function() {
		var loaded = 0
		for(var i = 0; i < indexes.length; i++) {
			if (document.getElementById('ivn'+indexes[i]).complete) {
				if( ! liteversion ) bimindexes.push(indexes[i])
				loaded++
			}	
		}
		loader.update( (initInd.length - indexes.length + loaded ) / self.howmany * 100 ) 
		if (loaded != indexes.length)
			return
		self.nextLoad()	
	}	
	
	this.nextLoad = function() {
		if( initInd.length == this.howmany ) {
			clearInterval(ccid)
			this.hideVersions()
			this.manualRollEnable()
			if( ! liteversion ) {
				this.stop4slides()		
				this.mode = 'medium'				
				this.startRolling( 70 )
			} else	
				this.speedupRolling( 70 )
			return
		}
		switch( initInd.length ) {
			case 4:
				if( liteversion )
					this.startRolling()
				else 
					this.show4slides()
				indexes = [6,16,26,36]
				break
			case 8:				
				indexes = [3,9,13,19,23,29,33,39]
				this.speedupRolling( 200 )		
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
		if (liteversion) {	
			this.stop4slides()			
			this.breakPreload()
			this.preloadImages(indexes) // again, but another folder
			gi.setDimensionProps( vzooms[0] )
			this.zoom = 1
			gi.zoom = 1
			gi.noZoom()
			ccid = setInterval(self.checkComplete, self.completenessIntrvl)
			if( initInd.length > 4 ) {
				this.startRolling()
			}				
		} else {
			this.stopRolling()
			//this.show4slides()			
			this.breakPreload()
			initInd = [1,11,21,31]
			this.preloadImages(initInd) // again, but another folder
			this.getInitSize()
			frontier.hide()
			gi.setDimensionProps( vzooms[this.zoom - 1] )
			gi.zoom = this.zoom
			gi.addZoom()
			ccid = setInterval(self.checkComplete, self.completenessIntrvl)			
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
		var tmpURLs = smURLs
		for(var i=0; i < bimindexes.length; i++) {
			if (bimindexes[i] == flnm) {
				tmpURLs = bURLs
				break
			} 
		}
		self.mvblock.hide()
		frontier.attr('src', tmpURLs[ flnm - 1])		
		self.mvblock.show()
		self.rollshift( flnm - 1 )
	}
	
/* ---------------------------------------------------------------------------- */ /* 4slides gallery */	
/* 4slides gallery */

	this.show4slides = function() {	
		var tofind = true
	
		while(tofind) {
			pointer = (pointer + 10 ) % self.howmany
			if (document.getElementById('ivn'+pointer).complete) {
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
		/* preload first four images */
		$('<div>').hide()
				  .attr('id','nvis')
				  .appendTo('body')
		this.preloadImages(initInd)
		ccid = setInterval(self.checkComplete, self.completenessIntrvl) 
		
		self.api()
		
		/* create main img */
		this.initres = this.getInitSize()
		frontier = $('<img>').attr('src', bURLs[0])
							 .attr('id','ivn')
							 .css('position','relative')
		
		
		frontier.bind ( {
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
		
		frontier.appendTo(this.mvblock)
		this.mvblock.css('text-align','left')
		
		gi = new gigaimage( frontier, self.zoom, zoo, self.rollanddrop )
		gi.addZoom()		
		
		
		$(window).resize( function() {
			if(self.zoom == 1) {
				frontier.css('left', Math.round( (self.mvblock.innerWidth() - self.initres ) / 2 ) )
					    .css('top', Math.round( (self.mvblock.innerHeight() - self.initres ) / 2 ) )							 
			}
		})
		

	}
	
	this.hide = function() {
		$(frontier).hide()
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
		
		if( initInd.length != this.howmany ) {
			this.preloadImages(indexes)
			ccid = setInterval(self.checkComplete, self.completenessIntrvl) 			
		}
		if (liteversion || initInd.length == this.howmany ) {	
			this.startRolling()
		} else 	{
			this.show4slides()
		}	
		this.toggle()		
	}
	
/* ---------------------------------------------------------------------------- */ /* Mechanics */	
/* Mechanics */	

	this.rollanddrop = function(e, delta, evs) {	
		if ( evstamp != evs ) {
			evstamp = evs
			if ( Math.abs(delta.x) > Math.abs(delta.y) )
				actor = 'roll'
			else
				actor = 'drop'
		}
		var tmpdir // its a direction
		if( actor == 'roll' && Math.abs (e.pageX - initx) > 20 * self.zoom ) {
			tmpdir = (e.pageX - initx) > 0 ? 1 : -1
			initx = e.pageX
			self.nextSrc(tmpdir)
		} else if ( actor == 'drop' ) {
			var img = {
				y: parseInt( frontier.css('top'), 10 )			
			}		
			frontier.css('top',  delta.y + img.y)	
		}			
	}
	
	this.getInitSize = function () {
		var w = $(window).width()
		if (w < 1030 ) { //1024
			this.zoom = 1
		} else if ( w < 1590 ) { //1280
			this.zoom = 2		
		} else { // bigsize
			this.zoom = 3		
		}
		this.imgzoom = vzooms[ this.zoom - 1 ]
		return this.imgzoom
	}
	
/* ---------------------------------------------------------------------------- */ /* END */
} // likemovie Object

function gigaimage( worknode , zoom, zoo, overwritefn) {

	var self = this
	var jnode = worknode 
	var active = false // d&d
	var initx = {} 
	var vzooms  = [500, 1200, 2500]		
	var evstamp = 0
	var zooObj = null
	this.zoom = zoom
	if( zoo ) {
		zooObj = new zoomer( zoo , self)
	}
	
	this.cursorHand = function(){
		jnode.css('cursor','url(/css/skin/cursor/cursor_1.png), url(/css/skin/cursor/cursor_1.gif), url(/css/skin/cursor/cursor_ie_1.cur), crosshair')
	}

	this.cursorDrag = function(){
		jnode.css('cursor','url(/css/skin/cursor/cursor_2.png), url(/css/skin/cursor/cursor_2.gif), url(/css/skin/cursor/cursor_ie_2.cur), move')
	}
	
	self.cursorHand()
	
	this.setDimensionProps = function( px ) {
		var resol = px ? px : vzooms[this.zoom - 1]
		jnode.attr('width', resol)
		 	  .attr('height', resol)
			  .css('left', Math.round( (jnode.parent().innerWidth() - resol ) / 2 ) )
			  .css('top', Math.round( (jnode.parent().innerHeight() - resol ) / 2 ) )							 
	
	}
	this.setDimensionProps()
	//var imageObject = new Image()
	//imageObject.src = worknode.attr('src')
	//console.info(imageObject.width)

	this.zoomIn = function(){
		var outer = jnode.parent()
		var offs = outer.offset()
		this.fixzoom(1, Math.round( outer.width() / 2 ) + offs.left, Math.round( outer.height() / 2 ) + offs.top )
	}

	this.zoomOut = function(){
		var outer = jnode.parent()
		var offs = outer.offset()
		this.fixzoom(-1, Math.round( outer.width() / 2 ) + offs.left, Math.round( outer.height() / 2 ) + offs.top )
	}
	
	this.fixzoom = function(de, mX, mY) {
		var oldzoom = this.zoom
		if(this.zoom == 1 && de < 0 || this.zoom == 3 && de > 0)
			return
		de > 0 ? this.zoom++ : this.zoom--
		de = de / Math.abs(de)
		var scale = vzooms[ this.zoom - 1] / vzooms[ oldzoom - 1] 
		var outoffsets = jnode.parent().offset()
		var img = {
			x: parseInt( jnode.css('left'), 10 ),
			y: parseInt( jnode.css('top'), 10 )			
		}
		mX -= outoffsets.left + img.x
		mY -= outoffsets.top  + img.y
		img.x -= de * Math.abs( Math.round ( mX * (1 - scale) ) )
		img.y -= de * Math.abs( Math.round ( mY * (1 - scale) ) )		
		jnode.attr('width', vzooms[this.zoom - 1])
			    .attr('height', vzooms[this.zoom - 1])
			    .css({'left': img.x , 'top': img.y})
		if( zooObj ) {
			de > 0 ? zooObj.plus() : zooObj.minus()
		}
	}
	
	this.addZoom = function() {
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
	
	//document.ondragstart = document.body.onselectstart = function() {return false} /* prevent default behaviour */
	
	$(document).bind('mousemove.zoomer', function(e) {
		if( active ){
			e.preventDefault()
			var mdelta = {
				x: e.pageX - init.pageX ,
				y: e.pageY - init.pageY
			}
			self.action( e, mdelta, evstamp )
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
	
	this.action = overwritefn ? overwritefn : this.dropping
	
	this.destroy = function() {
		jnode.unbind('mousedown')
		jnode.unbind('mousewheel')
		jnode.remove()
		$(document).unbind('.zoomer')
		for(var x in this)
			delete this[x]
	}
	
} // gigaimage Object

function loadbar () {
	this.percentage = 0
	var title      = 'loadbar'
	var ref       = null
	
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

function zoomer ( jn , zfunctions ) {
	//var self = this
	if (!jn) 
		return false
	var jnode = jn
	var nodeindex = $('.zoomind', jn)
	var dragging = false
	
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
		}, 
		'mouseup': function(){
			dragging = false
		}
	})
		
	var topoffsets = [54, 28, -2]
	nodeindex.css('top', topoffsets[ zfunctions.zoom - 1] ) // initia
	
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
	
	
	$('b.zoomind', jn).parent().bind({
		'mousemove': function(e){			
			if ( ! dragging ) return
			e.preventDefault()
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
