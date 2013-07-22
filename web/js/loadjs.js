(function() {
	startTime = new Date().getTime();
	// console.log('start'+startTime);

	var _gaq = window._gaq || [];

	window.onerror = function(msg, url, line) {
		var preventErrorAlert = true;
		return preventErrorAlert;
	}

	var debug = false;

	if ( document.body.getAttribute('data-debug') == 'true'){
		debug = true;
	}

	// page load log
	if ( $('#page-config').data('value').jsonLog ) {
		var pageID = document.body.getAttribute('data-id'),
			dataToLog = {
				event: 'page_load',
				pageID: pageID
			};
		// end of vars

		$.ajax({
			type: 'POST',
			global: false,
			url: '/log-json',
			data: dataToLog
		});
	}
	

	if( typeof($LAB) === 'undefined' ){
		throw new Error( "Невозможно загрузить файлы JavaScript" );
	}

	function getWithVersion( flnm ) {
		if( typeof( window.release['version']) !== 'undefined' ) {
			if( ( !document.location.search.match(/jsdbg/) )&&( !debug ) ) {
				flnm = flnm.replace('js', 'min.js');
				flnm += '?' + window.release['version'];
			}	
		} 

		return flnm;
	};

	var mapVendor = 'yandex';


	$LAB.setGlobalDefaults({ AllowDuplicates: true, AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/prod/"})
	.queueScript('/js/combine.js')
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
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
		case 'default':
			break;
		case 'tag-category':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script(getWithVersion('common.js'))
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
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
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
		case 'cart':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( 'JsHttpRequest.min.js' )
				.script( getWithVersion('library.js') )
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.wait()
				.script(getWithVersion('common.js'))
				.wait()
				.script(getWithVersion('cart.js'))
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
		case 'order':
			$LAB
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false')
			.queueScript('http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js')
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( 'JsHttpRequest.min.js' )
				.script( getWithVersion('library.js') )
				// .script('shelf/jquery.mockjax.js')	               
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.wait()
				.script(getWithVersion('common.js'))
				.script(getWithVersion('order-new.js'))
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
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
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
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
			}).runQueue();
			break;
		case 'product_catalog':
			$LAB.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( getWithVersion('common.js') )
				.script( getWithVersion('pandora.js') )
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
		case 'product_card':
			$LAB.queueScript('http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js')
			.queueScript( (mapVendor==='yandex') ? 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' : 'http://maps.google.com/maps/api/js?sensor=false' )
			.queueWait( function() {
				$LAB
				.script('jquery-plugins.min.js')
				.script( getWithVersion('library.js') )
				.wait()
				.script( 'JsHttpRequest.min.js' )			
				.script( 'http://direct-credit.ru/widget/api_script_utf.js' )
				.script( getWithVersion('common.js') )
				.wait()
				.script( getWithVersion('product.js') )
				.script( getWithVersion('oneclick.js') )
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
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
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
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
				.wait()
				.script('//cdn.optimizely.com/js/204544654.js')
			}).runQueue();
			break;
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
				.script('//cdn.optimizely.com/js/204544654.js')
				.script('adfox.asyn.code.ver3.min.js')
				.wait()
				.script( getWithVersion('ports.js') )
			}).runQueue();
			break;
	}
}());
