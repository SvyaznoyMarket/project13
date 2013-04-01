// product list
define('product_list',
	['jquery', 'ejs', 'library', 'termAPI'], function ($, EJS, library, termAPI) {

	library.myConsole('product_list.js loaded')

	library.myConsole('render template from JSON...')
	var data = $('#productList').data('product')
	for (var i = 0; i< data.length; i++){
		var template = {
			id : data[i].id,
			article : data[i].article,
			image : data[i].image,
			name : data[i].name,
			price : library.formatMoney(data[i].price),
			isBuyable : data[i].isBuyable
		}
		var html = new EJS ({url: '/js/terminal/view/listing_itemProduct.ejs'}).render(template)
		$('.bProductListWrap').append(html)
	}
	library.myConsole('render done')

})