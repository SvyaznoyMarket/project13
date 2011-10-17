$(document).ready(function(){

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

});


/* —Î‡È‰Â ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
		var mini = $('.sliderbox .fl').html() * 1
		var maxi = $('.sliderbox .fr').html() * 1
		console.info(mini, maxi)
        if ($( "#slider-range1" ).length) $( "#slider-range1" ).slider({
            range: true,
            min: mini,
            max: maxi,
            values: [ mini , maxi ],
            slide: function( event, ui ) {
            console.info( ui ) 
                //$( "#amount1" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] )
                $('#f_price_from').val( ui.values[ 0 ] )
				$('#f_price_to').val( ui.values[ 1 ] )
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


/* –ÂÈÚËÌ„ ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
    jQuery(this).find('.ratingbox A').hover(function(){
        $("#ratingresult").html(this.innerHTML);
        return false;
    });
});

/* œÓ‚Â‰ÂÌËÂ ÍÌÓÔÓÍ ÔË Ì‡Ê‡ÚËË  ---------------------------------------------------------------------------------------*/
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

    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

    $(".goodsbar .link2").bind( 'click.css', function()   {
        //$(this).addClass("link2active")
    })

    $(".goodsbar .link3").bind( 'click.css', function()   {
        //$(this).addClass("link3active");
    })
});


$(document).ready(function(){
/* top menu */	 
	var idcm          = null // setTimeout
	var currentMenu = 0 // ref= product ID
	var corneroffsets = [167,222,290,362,435,515,587,662,717]
	
	$('.topmenu a').bind( {
		'mouseenter': function() {
			$('.extramenu').hide()
			var self = this
			
			function showList() {
				if(	$(self).data('run') ) {
					var i = $(self).attr('class').replace(/\D+/,'')
					var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )	
					if( punkt.find('dl').html().trim() != '' )
						punkt.show().find('.corner').css('left',corneroffsets[i-1])					
				}	
			}
			$(self).data('run', true)
			currentMenu = $(self).attr('id').replace(/\D+/,'')
			idcm = setTimeout( showList, 300)
		},
		'mouseleave': function() {
			var self = this
			
			if(	$(self).data('run') ) {
				clearTimeout( idcm )
				$(self).data('run',false)
			}
			//currentMenu = 0
		}		
	}) 
	
	$(document).click( function(){
		if (currentMenu)
			$( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
	})
	/* ---- */	
	
	/* cards carousel  */
	
	$('.forvard').click( function(){
		//сделать запрос, получить новые данные
		//найти блок карусели
		//скрыть старые, показать новое
		//console.info( $(this) )
		//обновить внутреннюю переменную
		//обновить состояние кнопок
		return false
	})
	/* ---- */
});	