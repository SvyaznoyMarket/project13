define('filter',
	['jquery', 'library', 'bigjquery'], function ($, library) {


			$('.bFilter__eClearBtn').live('click', function(){
				terminal.filterObject.clear()
				return false
			})

			$('.bFilter').submit(function(){
				var filterString = $(this).serialize().toString()
				// console.log(filterString)
				// terminal.currentScreen.applyFilters(filterString)
				// terminal.pageStack.currentScreen.applyFilters(filterString)
				terminal.filterObject.apply(filterString)
				return false
			})

			$(".bFilter__eName").click(function(){
				$(this).next(".bFilter dd").slideToggle(200)
				$(this).toggleClass("mCurrent")
				return false
			})


			$('.sliderbox').each( function(){
				var sliderRange = $('.filter-range', this)
				var filterrange = $(this)
				var papa = filterrange.parent()
				var mini = $(this).parent().find('.slider-from').val() * 1
				var maxi = $(this).parent().find('.slider-to').val() * 1
				var informator = $(this).parent().find('.slider-interval')
				var from = papa.find('input:first')
				var to   = papa.find('input:eq(1)')
				informator.html( library.formatMoney( from.val() ) + ' - ' + library.formatMoney( to.val() ) )
				var stepf = (/price/.test( from.attr('id') ) ) ?  100 : 1
				if( maxi - mini <= 3 && stepf != 100 )
					stepf = 0.1

				sliderRange.slider({
					range: true,
					step: stepf,
					min: mini,
					max: maxi,
					values: [ from.val()  ,  to.val() ],
					slide: function( e, ui ) {
						informator.html( library.formatMoney( ui.values[ 0 ] ) + ' - ' + library.formatMoney( ui.values[ 1 ] ) )
						from.val( ui.values[ 0 ] )
						to.val( ui.values[ 1 ] )
					},
					change: function(e, ui) {
						if ( parseFloat(to.val()) > 0 ){
							// from.parent().trigger('preview')
							// activateForm()
						}
					}
				})

			})
	})