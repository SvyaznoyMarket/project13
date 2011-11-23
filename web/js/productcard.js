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
	var ajaxaddURL = '/cart/add/2050407000545'
	var ajaxdelURL = '/cart/delete/2050407000545'
	F1 = new F1boxes()
	$('.close', graypp).click( F1.close )
	$(':reset', graypp).click( F1.rst )
	$(':button', graypp).click( F1.memorize )
	$('div.f1links input:checkbox').live('change',function() { 	 
		F1.fire( $(this).attr('ref') )
	})
	
	function Cbox( node ) {
		self = this
		var token = node.attr('ref') 
		var bavatar = node
		var savatar = $('div.f1linkslist input[ref='+ token +']')
		
		this.rmSavatar = function() {
			if( savatar.length && ! savatar.parent().find('label').hasClass('checked') ) {
				savatar.parent().remove()
				savatar = $('#noobj')
				return true
			}	
			return false
		}
		
		this.click = function() {
			graypp.show()
			savatar.parent().find('label').removeClass('checked')
			var blab = bavatar.parent().find('label')
			if( blab.hasClass('checked') )
				blab.removeClass('checked')
			else	
				blab.addClass('checked')
		}
		
		this.bclick = function() {
			bavatar.parent().find('label').trigger('click')
		}
		
		this.sclick = function() {			
			if( savatar.length )
				savatar.parent().find('label').addClass('checked')
			else { //create element
				savatar = bavatar.clone().attr('id', bavatar.attr('id').replace('-','-small-2') )
				var label = bavatar.parent().find('label').clone().attr('for', bavatar.parent().find('label').attr('for').replace('-','-small-2') )
				label.text( label.text() + ' ('+ bavatar.parent().next().find('strong').text().replace(/\s/,'') +')') 
					 .find('span').remove()
				$('div.f1linkslist ul').append( $('<li>').append( label ).append( savatar ) )
				$('div.f1linkslist li:last input').prettyCheckboxes()
				self.addServer()
			}
		}
		
		this.addServer = function() {
			$.post( ajaxaddURL + '/' + token + '/1', function(data){
				console.info(data)
			})
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
		
		
		
		this.Sparta = function() { // Its Sparta, man!
			if( chosen.length < 3 ) 
				return
			for(var i=0; i < collect.length; i++)
				collect[i].rmSavatar() 
		}
		
		this.printChosen = function() {
			if( !chosen.length )	
				return ''
			var out = '{"' + chosen[0] + '": 1'
			for(var i=1; i < chosen.length; i++) {
				out += ', "' + chosen[i] + '": 1'
			}	
			out += '}'
			return out
		}
		
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
			$('.f1linkslist label.checked').removeClass('checked')	
			chosen = []	
			for(var i=0; i < memory.length; i++) 
				if( memory[i] ) {
					chosen.push( memory[i] )
					self.findbyTkn( memory[i] ).sclick()
				}		
			self.Sparta()	
			graypp.hide()
			popupActive = false
		}
		
		this.rst = function() {
			for(var i=0; i < memory.length; i++) 
				if( memory[i] ) {
					self.findbyTkn( memory[i] ).bclick()				
				}
			memory = []	
		}
		
		this.close = function() {			
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

})	