$(document).ready(function() {
console.info('v.4')

	var Model = $('#order-delivery_map-data').data('value')
	// Check Consistency TODO

	function OrderModel() {
		var self = this	
		
		// Unavailable TODO layout scheme
		self.unavailable = ko.observable( false )
		if( Model.unavailable.length )
			self.unavailable( true )

		// Parse Shops
		self.allShops = []

		// Boxes
		self.dlvrBoxes = ko.observableArray([])
		for( var tkn in Model.deliveryTypes )
			if( Model.deliveryTypes[tkn].items.length ) {
				var box = Model.deliveryTypes[tkn]
				box.itemList = ko.observableArray([])
				for( var prdct in Model.deliveryTypes[tkn].items ) {
					box.itemList.push( Model.items[ Model.deliveryTypes[tkn].items[prdct] ] )
				}

				box.displayDate = ko.observable( box.displayDate )
				box.displayInterval = ko.observable( box.displayInterval )

				box.dlvrPrice  = ko.computed(function() {
					var out = 0
					var bid = box.token
					for(var i=0, l=box.itemList().length; i<l; i++) {
						var itemDPrice = box.itemList()[i].deliveries[bid].price
						if( itemDPrice > out )
							out = itemDPrice
					}
					return out
				}, this)

				box.totalPrice = ko.computed(function() {
					var out = 0
					for(var i=0, l=box.itemList().length; i<l; i++)
						out += box.itemList()[i].total
					out += box.dlvrPrice()
					return out
				}, this)

				box.caclDates = []

				for(var i=0, l=box.itemList().length; i<l; i++) {
					// Init add
					var bid = box.token
					var dates = box.itemList()[i].deliveries[bid].dates
					console.info( dates )
					for( var j in dates ) {
						var date = { day: dates[j].day, dow: dates[j].dayOfWeek, value: dates[j].value  }
						for(var k=0, lk = box.caclDates.length; k<lk; k++ ) {
							if()box.caclDates[k]
						}
						box.caclDates.push( date )
					}
					//Fin add
				}

				self.dlvrBoxes.push( box )
				
			}

// console.info( self.dlvrBoxes()[0].itemList() )
		self.totalSum   = ko.computed( function() {
			var out = 0
			for(var i=0, l = self.dlvrBoxes().length; i<l; i++)
				out += self.dlvrBoxes()[i].totalPrice() * 1
			return out
		}, this)
	} // OrderModel object

	
console.info( Model )
	MVM = new OrderModel() 
	ko.applyBindings( MVM , $('#OrderView')[0] ) // this way, Lukas!
})