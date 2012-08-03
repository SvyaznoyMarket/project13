$(document).ready(function() {
	/* Custom Selectors */ 
	$('#OrderView').delegate( '.bSelect', 'click', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		$(this).find('.bSelect__eDropmenu').toggle()
	})
	$('#OrderView').delegate( '.bSelect', 'mouseleave', function() {
		if( $(this).hasClass('mDisabled') )
			return false
		var options = $(this).find('.bSelect__eDropmenu')
		if( options.is(':visible') )
			options.hide()
	})

	/* KNOCKOUT STUFF */

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
		self.curWeek = ko.observable(1)

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

				// box.dlvrPrice  = ko.computed(function() {
				// 	var out = 0
				// 	var bid = box.token
				// 	for(var i=0, l=box.itemList().length; i<l; i++) {
				// 		var itemDPrice = box.itemList()[i].deliveries[bid].price
				// 		if( itemDPrice > out )
				// 			out = itemDPrice
				// 	}
				// 	return out
				// }, this)

// 				box.totalPrice = ko.computed(function() {
// console.info(this, box)					
// 					var out = 0
// 					for(var i=0, l=box.itemList().length; i<l; i++)
// 						out += box.itemList()[i].total
// 					// out += box.dlvrPrice()*1
// 					return out
// 				}, this)

				box.caclDates = []

				// There are some intervals
				var edges = []
				for(var i=0, l=box.itemList().length; i<l; i++) {
					var bid = box.token
					var dates = box.itemList()[i].deliveries[bid].dates
					edges.push( [ dates[0], dates[ dates.length - 1 ] ] )
				}

				// Build Tight Interval
				var tightInterval = edges[0]
				if( edges.length > 1 )
					for(var i=1, l=edges.length; i<l; i++) {
						if( edges[i][0] > tightInterval[0] )
							tightInterval[0] = edges[i][0]
						if( edges[i][1] < tightInterval[1] )
							tightInterval[1] = edges[i][1]
					}
// console.info(  tightInterval )					
				
				// Make Additional Dates				
				var first = new Date( tightInterval[0].timestamp )
				if( first.getDay() !== 1 ) {
					//add before
					var dbefore = (first.getDay()) ? first.getDay() - 1 : 6
					first.setTime( first.getTime()*1 - dbefore*24*60*60*1000 )
				}
console.info( 'Interval edges: ', first )
				var last = new Date( tightInterval[1].timestamp )
				if( last.getDay() !== 0 ) {
					//add after					
					last.setTime( last.getTime()*1 + (7 - last.getDay())*24*60*60*1000 )
				}
console.info( last )

				// Make Dates By T Interval
				var doweeks = ['Вс','Пн','Вт','Ср','Чт','Пт','Сб']
				var nweeks = 1
				while( first.getTime() <= last.getTime() ) {
					var linerDate = {
						dayOfWeek: doweeks[ first.getDay() ],
						day: first.getDate(),
						tstamp: first.getTime()*1,
						week: nweeks,
						enable: ko.observable( false )
					}
					if( !first.getDay() )
						nweeks ++
					box.caclDates.push( linerDate )
					linerDate = null
					first.setTime( first.getTime()*1 + 24*60*60*1000 )
				}
				box.nweeks = nweeks-1

				
				function thereIsExactPropertie( list, propertie, value ) {			
//console.info(value, new Date(value) )					
					for(var ind=0, le = list.length; ind<le; ind++) {
						if( list[ind][ propertie ] == value )
							return true
					}
					return false
				}

				function getIntervalsFromData( list, propertie, value ) {
					var out = []
					for(var ind=0, le = list.length; ind<le; ind++) {
						if( list[ind][ propertie ] == value ) {
							for( var key in list[ind].intervals )
								out.push( 'c ' + list[ind].intervals[key].start_at + ' по '+ list[ind].intervals[key].end_at )
							return out
						}
					}
					return false
				}
				

up:				for( var linedate in box.caclDates ) { // Loop for T Interval
					var dates = []
					for(var i=0, l=box.itemList().length; i<l; i++) { // Loop for all intervals
						var bid = box.token
						dates = box.itemList()[i].deliveries[bid].dates
						if( ! thereIsExactPropertie( dates, 'timestamp', box.caclDates[linedate].tstamp ) ) {
							box.caclDates[linedate].enable( false )
							continue up
						}
						box.caclDates[linedate].enable( true )
					}
					//add intervals ATTENTION : NO COMPILATION FOR INTERVALS
					box.caclDates[linedate].intervals = getIntervalsFromData( dates, 'timestamp', box.caclDates[linedate].tstamp )//= ['c 9:00 до 18:00']
					
				}
// console.info( box.caclDates )

			
				// Calc Chosen Date
				box.chosenDate = ko.observable(0)
				box.chosenInterval = ko.observable('none')
				box.currentIntervals = ko.observableArray([])

				for( var linedate in box.caclDates ) { // the first enable 
					if( box.caclDates[linedate].enable() ) {
						box.chosenDate( box.caclDates[linedate].tstamp )
						box.chosenInterval( box.caclDates[linedate].intervals[0] )
						for( var key in box.caclDates[linedate].intervals )
							box.currentIntervals.push( box.caclDates[linedate].intervals[key] )
						break
					}
				}

// console.info(box.chosenDate())
				self.dlvrBoxes.push( box )
				
			}

		for( var key in self.dlvrBoxes() ) {
			var loopBox = self.dlvrBoxes()[key]

			loopBox.dlvrPrice  = ko.computed(function() {
				var out = 0
				var bid = this.token
				for(var i=0, l=this.itemList().length; i<l; i++) {
					var itemDPrice = this.itemList()[i].deliveries[bid].price
					if( itemDPrice > out )
						out = itemDPrice
				}
				return out
			}, loopBox)

			loopBox.totalPrice = ko.computed(function() {				
				var out = 0
				for(var i=0, l=this.itemList().length; i<l; i++)
					out += this.itemList()[i].total
				out += this.dlvrPrice()*1
				return out
			}, loopBox)
		} // for

		self.changeWeek = function( direction, data, e ) {
			if( direction > 0 ) {
				if( data.nweeks == self.curWeek() )
					return	
				self.curWeek( self.curWeek() + 1 )		
			}
			if( direction < 0 ) {
				if( self.curWeek() == 1 )
					return
				self.curWeek( self.curWeek() - 1 )		
			}
		}

		self.clickDate = function( box, d, e ) {
			if( !d.enable() ) 
				return
			box.chosenDate( d.tstamp )
			box.currentIntervals.removeAll()
			for( var key in d.intervals )
				box.currentIntervals.push( d.intervals[key] )
			if( !$.inArray( box.chosenInterval(), box.currentIntervals() ) )
				box.chosenInterval( box.currentIntervals()[0] )
		}

		self.clickInterval = function( box, d, e ) {
			box.chosenInterval( d )
		}

		self.deleteItem = function( box, d, e ) {
			// ajax del 
			// $.get( d.deleteUrl )
			// drop from box
			box.itemList.remove( d )
			if( !box.itemList().length )
				self.dlvrBoxes.remove( box )
		}

		self.totalSum   = ko.computed( function() {
			var out = 0
			for(var i=0, l = self.dlvrBoxes().length; i<l; i++)
				out += self.dlvrBoxes()[i].totalPrice() * 1
			return out
		}, this)

	} // OrderModel object

	
console.info( 'MODEL ', Model )
	MVM = new OrderModel() 
	ko.applyBindings( MVM , $('#OrderView')[0] ) 

})