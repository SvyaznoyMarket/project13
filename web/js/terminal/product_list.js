// product list
define('product_list',
	['jquery', 'ejs', 'library', 'termAPI'], function ($, EJS, library, termAPI) {

	library.myConsole('product_list.js loaded')

	library.myConsole('EJS '+typeof(EJS))
	
	var data = $('#productList').data('product')
	for (var i = 0; i< data.length; i++){
		var html = new EJS ({url: '/js/terminal/view/listing_itemProduct.ejs'}).render(data[i])
		$('.bProductListWrap').append(html)
	}

	library.myConsole('off..')

})