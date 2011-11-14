$(document).ready(function(){

	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		var idu    = trgtimg.attr('data-url')	
		if( trgtimg[0].complete ) {
			$('#goodsphoto img').attr('src', isrc)
			$('#goodsphoto img').attr('href', idu)
		}	
	})
	
	//var lkmv = null
	var api = {
		'makeLite' : '#turnlite',
		'makeFull' : '#turnfull',
		'loadbar'  : '#percents',
		'zoomer'   : '#bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['.versioncontrol','.scrollbox']
	}
	
	lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	var mLib = new mediaLib( $('#bigpopup') )	

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
    
    $('#1click-trigger').click(function(e){
        
        $.get($(this).prop('href'), {}, function(response){
            var cnt = $('<div class="popup"><i class="close" title="Закрыть">Закрыть</i>'+response+'</div>').appendTo(document.body),
                form = cnt.find('form');
            Custom.init();
            cnt.lightbox_me({
                onLoad: function(){
                    cnt.find('input[type=checkbox], input[type=radio]').prettyCheckboxes();
                    form.submit(function(){
                        $.post(form.prop('action'), form.serializeArray(), function(resp){
                            cnt.html('<i class="close" title="Закрыть">Закрыть</i>'+resp);
                            Custom.init();
                        });
                        return false;
                    });
                },
                onClose: function(){
                    cnt.remove()
                }
			});
        });
        
        return false;
    });
    
	if (location.toString().search('1click-payment-ok') !== -1) {
        alert('Заказ успешно оплачен');
    } else if (location.toString().search('1click-payment-fail') !== -1) {
        alert('При оплате заказа произошла ошибка');
    }
})	