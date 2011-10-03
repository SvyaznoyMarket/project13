// JavaScript Document

$(function(){
	$('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();	
	
		$(".pricelist dt").click(function(){
		$(this).next(".pricelist dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});

});


/* Слайдер ---------------------------------------------------------------------------------------*/
$(function() {
		$( "#slider-range1" ).slider({
			range: true,
			min: 2000,
			max: 200000,
			values: [ 2000, 200000 ],
			slide: function( event, ui ) {
				$( "#amount1" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
			}
		});
		$( "#amount1" ).val( $( "#slider-range" ).slider( "values", 0 ) +
			" - " + $( "#slider-range" ).slider( "values", 1 ) );
			
			
		$( "#slider-range2" ).slider({
			range: true,
			min: 4,
			max: 8,
			values: [ 4, 6 ],
			slide: function( event, ui ) {
				$( "#amount2" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
			}
		});
		$( "#amount2" ).val( $( "#slider-range2" ).slider( "values", 0 ) +
			" - " + $( "#slider-range2" ).slider( "values", 1 ) );
			
			
		$( "#slider-range3" ).slider({
			range: true,
			min: 0,
			max: 16000,
			values: [ 200, 10000 ],
			slide: function( event, ui ) {
				$( "#amount3" ).val("1/" + ui.values[ 0 ] + " - 1/" + ui.values[ 1 ] );
			}
		});
		$( "#amount3" ).val( $( "#slider-range3" ).slider( "values", 0 ) +
			" - " + $( "#slider-range3" ).slider( "values", 1 ) );
	});




