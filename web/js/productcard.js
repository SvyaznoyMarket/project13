$(document).ready(function(){
	/* Icons */
	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		var idu    = trgtimg.attr('data-url')	
		if( trgtimg[0].complete ) {
			$('#goodsphoto img').attr('src', isrc)
			$('#goodsphoto img').attr('href', idu)
		}	
	})
	
	/* Media library */
	//var lkmv = null
	var api = {
		'makeLite' : '#turnlite',
		'makeFull' : '#turnfull',
		'loadbar'  : '#percents',
		'zoomer'   : '#bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['.versioncontrol','.scrollbox']
	}
	
	lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	var mLib = new mediaLib( $('#bigpopup') )	

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
	
	/* F1 */
	var graypp = $('#f1pp')
	var F1 = new F1boxes()
	$('.close', graypp).click( F1.close )
	$(':reset', graypp).click( F1.rst )
	$(':button', graypp).click( F1.memorize )
	$('div.f1links input:checkbox').live('change',function() { 	
//	console.info('cbox clicked', $(this).attr('ref') ) 
		F1.fire( $(this).attr('ref') )
	})
	
	function Cbox( node ) {
		self = this
		var token = node.attr('ref') 
		var bavatar = node
		var savatar = $('div.f1linkslist input[ref='+ token +']')
		
		this.click = function() {
			graypp.show()
			savatar.parent().find('label').trigger('click')
			bavatar.parent().find('label').trigger('click')
		}
		
		this.bclick = function() {
			bavatar.parent().find('label').trigger('click')
		}
		
		this.getTkn = function() {
			return token
		}
	} // object Cbox
	
	function F1boxes() {
		var self = this
		var popupActive = false
		var chosen = []
		var memory = []
		collect = []
		$('input:checkbox', graypp ).each( function() {
			collect.push( new Cbox($(this)) )
		})
		
		this.findbyTkn = function( tkn ) {
			for(var i=0; i < collect.length; i++) 
			if( collect[ i ].getTkn() === tkn )
				return collect[i]
			return false	
		}
		
		this.fire = function( tkn ) {
			var fbox = self.findbyTkn( tkn )
			if( !popupActive ) {
				fbox.click()
				popupActive = true
			}	
			self.pushpop( tkn )
		}
		
		this.pushpop = function( vl ) {
			for(var i=0; i < memory.length; i++) 
				if( memory[i] === vl ) {
					memory[i] = 0 // rem
					return 
				}	
			memory.push( vl )		
		}
		
		this.memorize = function() {
			outer:for(var i=0; i < memory.length; i++) 
				if( memory[i] ) {
					for(var j=0; j < chosen.length; j++) 
						if( chosen[j] === memory[i] )
							continue outer			
					chosen.push( memory[i] )
				}
			console.info(chosen)	
			self.close()
		}
		
		this.rst = function() {
			//chosen = []
			for(var i=0; i < memory.length; i++) 
				if( memory[i] ) {
					self.findbyTkn( memory[i] ).bclick()				
				}
			memory = []	
		}
		
		this.close = function() {
			//console.info(chosen, memory)
			collect:for(var i=0; i < collect.length; i++) {
				for(var j=0; j < chosen.length; j++) {
					if( collect[i].getTkn() === chosen[j] ) 
						continue collect
				}		
				for(var j=0; j < memory.length; j++) {
					if( collect[i].getTkn() === memory[j] ) 
						collect[i].bclick()	
				}		
			}	
			memory = []
			graypp.hide()
			popupActive = false
		}
	} // object F1boxes
	
	//var tt = $('<li><label for="checkbox-small-11">Установка вытяжки плоской (2340 Р)</label><input id="checkbox-small-11" name="service[3]" type="checkbox" value="1" /></li>')
	//$('div.f1linkslist ul').append(tt)
	//$('#checkbox-small-11').prettyCheckboxes()
})	