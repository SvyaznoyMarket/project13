(function() {
	if( typeof($LAB) === 'undefined' )
		throw new Error( "Невозможно загрузить файлы JavaScript" )
	function getWithVersion( flnm ) {
		if( typeof( filesWithVersion[''+flnm] ) !== 'undefined' ) {			
			flnm += '?' + filesWithVersion[''+flnm]
			if( !document.location.search.match(/jsdbg/) )
			flnm = flnm.replace('js', 'min.js')
		}	
		return flnm
	}
	
	$LAB.setGlobalDefaults({ AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/"})
	.script('jquery-1.6.4.min.js')
	.script('combine.js')
	.wait()
	
	switch( document.body.getAttribute('data-template') ) {
		case 'main':
			break
		case 'default':			
			break
		case 'cart':
			$LAB.script('bigjquery.min.js')
			.wait( function() {
				$LAB
				.script(getWithVersion('main.js'))
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )		
				.wait()
				.script( getWithVersion('dash.js') )//??
			})
			break
		case 'order':
			$LAB.script('bigjquery.min.js')
			.wait( function() {
				$LAB
				.script('shelf/jquery.autocomplete-dev.js')//??				
				.script(getWithVersion('main.js'))
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.order.js') )
			})
			break
		case 'product_catalog':
			$LAB.script('bigjquery.min.js')
			.wait( function() {
				$LAB
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			})
			break
		case 'product_card':
			$LAB.script('bigjquery.min.js')
			.script('shelf/jquery.autocomplete-dev.js')//??
			.wait( function() {
				$LAB
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )//??
				.script( getWithVersion('app.order.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( 'watch3dv2.min.js' )
				.wait()
				.script( getWithVersion('productcard.js') )
			})
			break
		case 'product_comment':
			$LAB.script('bigjquery.min.js')
			.script('shelf/jquery.autocomplete-dev.js')//??
			.wait( function() {
				$LAB
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )//??
				.script( getWithVersion('app.order.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.script( getWithVersion('app.product.comment.list.js') )
				.script( getWithVersion('app.product.comment.new.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( getWithVersion('productcard.js') )
				.script( 'watch3dv2.min.js' )
			})			
			break	
		case 'service':
			$LAB.script('bigjquery.min.js')
			.wait( function() {
				$LAB
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			})
			break
		case 'shop':
			$LAB.script('bigjquery.min.js')
			.wait( function() {
				$LAB
				.script('google.maps.infobox.js')
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( getWithVersion('app.shop.js') )
				.script('tour.js')
			})
			break
	}
}());