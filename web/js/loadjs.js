(function() {
	if( typeof($LAB) === 'undefined' )
		throw new Error( "Невозможно загрузить файлы JavaScript" )
	function getWithVersion( flnm ) {
		if( typeof( filesWithVersion[''+flnm] ) !== 'undefined' ) {			
			if( !document.location.search.match(/jsdbg/) ) {
			flnm += '?' + filesWithVersion[''+flnm]			
			flnm = flnm.replace('js', 'min.js')
			}
		}	
		return flnm
	}
	
	$LAB.setGlobalDefaults({ AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/"})
	.queueScript('jquery-1.6.4.min.js')
	.queueWait( function(){
		document.write = function(){
			if( arguments[0].match('javascript') )
				$LAB.script( arguments[0].match(/src="(.*?)"/)[1])		
			else
				$('head').append( arguments[0] )
		}
	})
	.queueScript('combine.js')

	switch( document.body.getAttribute('data-template') ) {
		case 'main':
			$LAB.queueWait( function() {
				$LAB.script(getWithVersion('wellcome.js'))
			}).runQueue() 
			break
		case 'default':			
			break
		case 'cart':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script(getWithVersion('main.js'))
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )		
				//.wait()
				//.script( getWithVersion('dash.js') )//??
			}).runQueue()
			break
		case 'order':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script(getWithVersion('main.js'))
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.order.js') )
			}).runQueue()
			break
		case 'product_catalog':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				//.script( getWithVersion('app.product.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )				
			}).runQueue()	
			break
		case 'product_card':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )
				//.script( getWithVersion('app.order.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( 'watch3dv2.min.js' )
				.wait()
				.script( getWithVersion('productcard.js') )
			}).runQueue()
			break
		case 'product_comment':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )
				//.script( getWithVersion('app.order.js') )//??				
				.script( getWithVersion('mechanics.js') )
				.script( getWithVersion('app.product.comment.list.js') )
				.script( getWithVersion('app.product.comment.new.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( 'watch3dv2.min.js' )
			}).runQueue()
			break	
		case 'service':
			$LAB.queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.wait()
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )
				.script( getWithVersion('app.product.js') )				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			}).runQueue()
			break
		case 'shop':
			$LAB.queueScript('http://maps.google.com/maps/api/js?sensor=false').queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.script('google.maps.infobox.js')
				.wait()
				.script( getWithVersion('app.shop.js') ) 
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.region.js') )			
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script('tour.js')
			}).runQueue()
			break
		case 'product_stock':
			$LAB.queueScript('http://maps.google.com/maps/api/js?sensor=false').queueWait( function() {
				$LAB.script('bigjquery.min.js')
				.script('google.maps.infobox.js')
				.wait()
				.script( getWithVersion('main.js') )
				.script( getWithVersion('app.auth.js') )
				.script( getWithVersion('app.search.js') )
				.script( getWithVersion('app.product.js') )	
				.script( getWithVersion('app.region.js') )
//				.wait()
//				.script( getWithVersion('app.product.stock.js') ) 				
				.script( getWithVersion('mechanics.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			}).runQueue()
			break	
	}
}());