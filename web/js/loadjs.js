(function() {
	var _gaq = window._gaq || []
	window.onerror = function(msg, url, line) {
		var preventErrorAlert = true
		_gaq.push(['_trackEvent', 'JS Error', msg, navigator.userAgent + ' -> ' + url + " : " + line])
		return preventErrorAlert
	}
	jQuery.error = function (message) {
		_gaq.push(['_trackEvent', 'jQuery Error', message, navigator.userAgent])
	}

	if( typeof($LAB) === 'undefined' )
		throw new Error( "Невозможно загрузить файлы JavaScript" )
	function getWithVersion( flnm ) { 
		if( typeof( filesWithVersion[''+flnm] ) !== 'undefined' ){
			if( !document.location.search.match(/jsdbg/) ) {
			flnm += '?' + filesWithVersion[''+flnm]
			flnm = flnm.replace('js', 'min.js')
			}	
		} else {
			flnm = flnm.replace('js', 'min.js')
			if( typeof( filesWithVersion[''+flnm] ) !== 'undefined' ) {
				flnm += '?' + filesWithVersion[''+flnm]
			}
		}
		return flnm
	}

	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/"})
	.queueScript('combine.js')
	// .queueScript('jquery-1.6.4.min.js')
	.queueScript('/js/asyn.code.ver3.js')	
	.queueWait( function(){
		document.write = function(){
/*			if( arguments[0].match('javascript') )
				$LAB.script( arguments[0].match(/src="(.*?)"/)[1])
			else
				$('head').append( arguments[0] )
*/
			if( arguments[0].match( /<script(.?)* type=(\'|")text\/javascript(\'|")(.?)*><\/script>/ ) ) {
				$LAB.script( arguments[0].match( /src=(\'|")([^"\']?)+/ )[0].replace(/src=(\'|")/,'') )
			} else {
				document.writeln( arguments[0] )
			}
		}
	})

	switch( document.body.getAttribute('data-template') ) {
		case 'main':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('library.js') )
				.script('shelf/jquery.countdown.min.js')
				.script('shelf/jquery.countdown-ru.js?v=2')
				.wait()
				.script(getWithVersion('welcome.js'))
			}).runQueue()
			break
		case 'default':
			break
		case 'infopage':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('ports.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('main.js'))
				.wait()
				.script( getWithVersion('dash.js') )
				.script( getWithVersion('infopages.js') )
			}).runQueue()
			break
		case 'cart':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('ports.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('main.js'))
				.script(getWithVersion('app.cart.js'))
			}).runQueue()
			break
		case 'order':
            $LAB.queueScript('knockout-2.1.0.js')
            .queueScript('bigjquery.min.js')
            .queueScript('http://maps.google.com/maps/api/js?sensor=true')
			.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('app.order.v4.js'))
				.script(getWithVersion('main.js'))
			}).runQueue()
			break
		case 'order_complete':
			$LAB.queueScript('bigjquery.min.js')
				.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('app.order.js'))
				.script(getWithVersion('main.js'))
			}).runQueue()
			break
        case 'order_error':
            $LAB.queueScript('bigjquery.min.js').queueWait( function() {
                $LAB.script( getWithVersion('library.js') )
                    .wait()
                    .script(getWithVersion('app.order.js'))
                    .script(getWithVersion('main.js'))
                    .script(getWithVersion('app.order.error.js'))
            }).runQueue()
            break
		case 'product_catalog':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			}).runQueue()
			break
		case 'product_card':
			$LAB.queueScript('knockout-2.1.0.js')
			.queueScript('http://maps.google.com/maps/api/js?sensor=true')
			.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( 'watch3dv2.min.js' )
				.wait()
				.script( getWithVersion('app.product.js') )
				.script( getWithVersion('app.oneclick.js') )
			}).runQueue()
			break
		case 'product_comment':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script( 'watch3dv2.min.js' )
				.wait()
				.script( getWithVersion('app.product.js') )
				.script( getWithVersion('app.product.comment.list.js') )
				.script( getWithVersion('app.product.comment.new.js') )
			}).runQueue()
			break
		case 'service':
			$LAB.queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
			}).runQueue()
			break
		case 'shop':
			$LAB.queueScript('http://maps.google.com/maps/api/js?sensor=false').queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') ).script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('app.shop.js') )
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script('tour.js')
			}).runQueue()
			break
		case 'product_stock':
			$LAB.queueScript('knockout-2.1.0.js')
			.queueScript('http://maps.google.com/maps/api/js?sensor=false').queueWait( function() {
				$LAB.script( getWithVersion('ports.js') )
				.script( getWithVersion('bigjquery.js') )
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('main.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.wait()
				.script( getWithVersion('app.product.js') )
				.script( getWithVersion('app.oneclick.js') )
			}).runQueue()
			break
	}
}());
