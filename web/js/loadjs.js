(function() {
	startTime = new Date().getTime()
	// console.log('start'+startTime)
	var _gaq = window._gaq || []
	window.onerror = function(msg, url, line) {
		var preventErrorAlert = true
		// _gaq.push(['_trackEvent', 'Javascript Error', msg, url + " : " + line])
		return preventErrorAlert
	}
	//jQuery.error = function (message) {
	//	_gaq.push(['_trackEvent', 'jQuery Error', message, navigator.userAgent])
	//}

	var debug = false;
	if ( document.body.getAttribute('data-debug') == 'true'){
		debug = true
	}

	// page load log
	if ($('#page-config').data('value').jsonLog){
		var pageID = document.body.getAttribute('data-id')
		var dataToLog = {
			event: 'page_load',
			pageID: pageID
		};
		$.ajax({
			type: 'POST',
			global: false,
			url: '/log-json',
			data: dataToLog
		});
	}
	

	if( typeof($LAB) === 'undefined' )
		throw new Error( "Невозможно загрузить файлы JavaScript" )

	function getWithVersion( flnm ) {
		if( typeof( window.release['version']) !== 'undefined' ){
			if( (!document.location.search.match(/jsdbg/))&&(!debug) ) {
				flnm += '?' + window.release['version'];
				flnm = flnm.replace('js', 'min.js');
			}	
		} 
		else {
			flnm = flnm.replace('js', 'min.js')
			if( typeof( window.release['version'] ) !== 'undefined' ) {
				flnm += '?' + window.release['version'];
			}
		}
		return flnm;
	};

	var mapVendor = 'yandex';


	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/prod/"})
	.queueScript('/js/combine.js')
	.queueScript('adfox.asyn.code.ver3.min.js')	
	.queueWait( function(){
		document.write = function(){
			if( arguments[0].match( /<script(.?)* type=(\'|")text\/javascript(\'|")(.?)*><\/script>/ ) ) {
				$LAB.script( arguments[0].match( /src=(\'|")([^"\']?)+/ )[0].replace(/src=(\'|")/,'') );
			}
			else {
				document.writeln( arguments[0] );
			}
		}
	});

	switch( document.body.getAttribute('data-template') ) {
		case 'main':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('common.js'))
				.script(getWithVersion('main.js'))
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'default':
			break
		case 'infopage':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('common.js'))
				.wait()
				.script( getWithVersion('infopage.js') )
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'cart':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.wait()
				.script(getWithVersion('common.js'))
				.wait()
				.script(getWithVersion('cart.js'))
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'order':
			$LAB
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false')
			.queueScript('http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js')
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js').script( getWithVersion('library.js') )
				// .script('shelf/jquery.mockjax.js')	               
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.wait()
				.script(getWithVersion('order-new.js'))
				.script(getWithVersion('common.js'))
				.wait()
				.script( getWithVersion('ports.js') )			
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'order_complete':
			$LAB.queueScript('jquery-plugins.min.js')
				.queueWait( function() {
				$LAB
				.script( getWithVersion('library.js') )
				// .script('shelf/jquery.mockjax.js')	
				// .script( 'JsHttpRequest.js' )
	//             .script( 'http://direct-credit.ru/widget/api_script_utf.js' )
	//             .script( 'http://direct-credit.ru/widget/script_utf.js' )
	//             .script( 'https://kupivkredit-test-fe.tcsbank.ru:8100/widget/vkredit.js' )
				.wait()
				.script(getWithVersion('order.js'))
				.script(getWithVersion('common.js'))
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'order_error':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('order.js'))
				.script( getWithVersion('common.js') )
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'product_catalog':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('common.js') )
				.wait()
				.script( getWithVersion('ports.js') )
				.wait()
				.script( getWithVersion('pandora.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'product_card':
			$LAB.queueScript('http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js')
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false' )
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( 'JsHttpRequest.min.js' )
				//.script( 'http://direct-credit.ru/widget/dc_script_utf.js' )				
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.script( getWithVersion('common.js') )
				.wait()
				.script( 'KupeConstructorScript.min.js' ) // furniture constuctor
				.script( 'three.min.js' ) // for furniture constuctor
				.wait()
				.script( getWithVersion('product.js') )
				.script( getWithVersion('oneclick.js') )
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'service':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('common.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'shop':
			$LAB
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false' )
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js').script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('shop.js') )
				.script( getWithVersion('common.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.script('tour.js')
				// .wait()
				// .script( getWithVersion('ports.js') )
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
		case 'product_stock':
			$LAB
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false' )
			.queueScript('http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js')
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('common.js') )
				.wait()
				.script( getWithVersion('dash.js') )
				.wait()
				.script( getWithVersion('product.js') )
				.script( getWithVersion('oneclick.js') )
				.wait()
				.script( getWithVersion('ports.js') )
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue()
			break
	}
}());
