// JavaScript Document

$(function(){
	$('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();	
	
		$(".bigfilter dt").click(function(){
		$(this).next(".bigfilter dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});
		
		$(".f1list dt B").click(function(){
		$(this).parent("dt").next(".f1list dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});
		
		$(".tagslist dt").click(function(){
		$(this).next(".tagslist dd").slideToggle(200);
		$(this).toggleClass("current");
		return false;
	});
		
		$(".tearmlist dt").click(function(){
		$(this).next(".tearmlist dd").slideToggle(200);
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
			values: [ 20000, 100000 ],
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


/* Рейтинг ---------------------------------------------------------------------------------------*/
$(function() { 
        jQuery(this).find('.ratingbox A').hover(function(){
		$("#ratingresult").html(this.innerHTML); 
		return false;
	});
});

/* Поведение кнопок при нажатии  ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
	$(".yellowbutton").mousedown(function()   {
	jQuery(this).toggleClass("yellowbuttonactive"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("yellowbuttonactive");
	});
	
	$(".whitebutton").mousedown(function()   {
	jQuery(this).toggleClass("whitebuttonactive"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("whitebuttonactive");
	});
	
	$(".whitelink").mousedown(function()   {
	jQuery(this).toggleClass("whitelinkactive"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("whitelinkactive");
	});
	
	$(".goodsbar .link1").mousedown(function()   {
	jQuery(this).toggleClass("link1active"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("link1active");
	});
	
	$(".goodsbar .link2").mousedown(function()   {
	jQuery(this).toggleClass("link2active"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("link2active");
	});
	
	$(".goodsbar .link3").mousedown(function()   {
	jQuery(this).toggleClass("link3active"); 
	}).mouseup(function()   {
	jQuery(this).removeClass("link3active");
	});
	
	jQuery(this).find('.goodsbarbig A').mousedown(function(){
	jQuery(this).addClass("active");  
    }).mouseup(function()   {
	jQuery(this).removeClass("active");
	});

});
