$(document).ready(function() {

	/* basket */
	var total = $('#total .price')
	var totalCash = 0

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
		totalCash = tmp
	}

	function basketline ( nodes, clearfunction ) {
		var self = this
		this.hasnodes = $(nodes.drop)
		
		$(nodes.less).data('run',false)
		$(nodes.more).data('run',false)
			var main = $(nodes.line)
		this.id      = main.attr('ref')	
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
		this.price   = price
		this.noview  = false
		var dropflag = false

		totalCash += this.sum * 1

		this.calculate = function( q ) {
			self.quantum = q
			self.sum = price * q
			$(nodes.sum).html( printPrice( self.sum ) )
			$(nodes.sum).typewriter(800, getTotal)
		}

		this.clear = function() {
			main.remove()
			self.noview = true
			PubSub.publish( 'quantityChange', { q : 0, id : self.id } )
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
			totalCash += self.price * delta
			PubSub.publish( 'quantityChange', { q : self.quantum, id : self.id } )
			if( $('#selectCredit').length ) {
				var sufx = ''
				if( $('#selectCredit').val()*1 )
					sufx = '/1'
				else
					sufx = '/0'
				tmpurl += sufx
			}
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
	
	/* credit */
	if( $('#selectCredit').length ) {
		var minsum = $('#creditSum').data('minsum')
		if( minsum )

		function anotherSum() {
			$('#creditSum').toggle()
		    $('#commonSum').toggle()
		}
		
		function checkFlag() {
			if( $('#selectCredit:checked').length ) {
				anotherSum()
			}
		}

		function toggleFlag() {					
			if( totalCash >= minsum ) {
				if( $('#creditFlag').is(':hidden') ) { // сумма превысила рубеж
					$('#creditFlag').show()
					checkFlag()
				}
			} else {
				if( $('#creditFlag').is(':visible') ) { // сумма стала ниже рубежа
					$('#creditFlag').hide()
					checkFlag()
				}
			}
		}

		function toggleCookie( name ) {
			if( !docCookies.hasItem( name ) ) {
				docCookies.setItem(false, name, 'true', 60*60, '/') 
				return
			}
			var curCook = docCookies.getItem( name )
			if( curCook === 'true' )
				curCook = 'false'
			else
				curCook = 'true'
			docCookies.setItem(false, name, curCook, 60*60, '/') 
		}

		toggleFlag()
		PubSub.subscribe( 'quantityChange', toggleFlag )
		//checkFlag()


		$('label.bigcheck').click( function(e) {
			var target = $(e.target)
			if (!target.is('input')) {
				return
			}
			$(this).toggleClass('checked')
			anotherSum()
			toggleCookie( 'credit_on' )
		})	
//console.info( basket )
		
		DirectCredit.init( $('#tsCreditCart').data('value'), $('#creditPrice') )
		PubSub.subscribe( 'quantityChange', DirectCredit.change )
	} // credit 
})