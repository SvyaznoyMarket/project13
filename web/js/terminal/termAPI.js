define('termAPI',
	['jquery', 'library'], function ($, library) {

		library.myConsole('termAPI.js loaded')

		//
		// bredcrumps
		//
		var aPathList = terminal.screen.path
		for (var i in aPathList){
			library.myConsole('-------------------')
			library.myConsole('pathType '+aPathList[i].type)
			library.myConsole('pathParam '+JSON.stringify(aPathList[i].parametrs))
			library.myConsole('pathType '+aPathList[i].screenType)
		}


		//
		// compare toggle
		//
		createElement = function(productId){
			var t = $('#compare_'+productId)
			return t
		}
		checkCompare = function(productId){
			var element = createElement(productId)
			myConsole('compare '+productId)
			if (terminal.compare.hasProduct(productId)){
				element.html('Убрать из сравнения')
				return true
			}
			else{
				element.html('К сравнению')
				return false
			}
		}
		$('.jsCompare').bind('click', function(){
			var id = $(this).attr('id')
			var productId = id.substr(8, 5)
			myConsole('id '+productId)
			if (checkCompare(productId)){
				terminal.compare.removeProduct(productId)
				checkCompare(productId)
			}
			else{
				terminal.compare.addProduct(productId)
				checkCompare(productId)
			}
		})
		terminal.compare.productRemoved.connect(checkCompare)
		terminal.compare.productAdded.connect(checkCompare)


		//
		// buy button
		//
		$('.jsBuyButton').live('click', function() {

			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var productId = $(this).data('productid')

			if ( $(this).attr('data-warrantyid') !== undefined ) {
				// buy warranty
				var warrantyId = $(this).data('warrantyid')
				library.myConsole('add to cart warranty '+warrantyId+' for '+productId)
				terminal.cart.setWarranty(productId, warrantyId)
				return false
			}

			if ( $(this).attr('data-serviceid') !== undefined ) {
				// buy service
				var serviceId = $(this).data('serviceid')
				library.myConsole('add to cart service '+serviceId+' for '+productId)
				terminal.cart.addService(productId, serviceId)
				return false
			}

			library.myConsole('add to cart product '+productId)
			terminal.cart.addProduct(productId)
		})


		//
		// where buy button
		//
		$('.jsWhereBuy').live('click', function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var id = $(this).data('productid')

			library.myConsole('other shops for '+id)
			terminal.screen.push('other_shops', { productId: id })
		})


		//
		// exports fucntion
		//
		return { 
			checkCompare: checkCompare
		}

	})