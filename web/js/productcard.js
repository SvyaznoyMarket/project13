$(document).ready(function(){
	/* Icons */
	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		var idu    = trgtimg.attr('data-url')	
		if( trgtimg[0].complete ) {
			$('#goodsphoto img').attr('src', isrc)
			$('#goodsphoto img').attr('href', idu)
		}	
	})
	
	/* Media library */
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
    
    var formatDateText = function(txt){
      txt = txt.replace('сегодня', '<b>сегодня</b>');
      txt = txt.replace('завтра', '<b>завтра</b>');
      return txt;
    }
    var formatPrice = function(price){
      if (typeof price === 'undefined' || price === null) {
        return '';
      }
      if (price > 0) {
        return ', '+price+' руб.'
      } else {
        return ', бесплатно.'
      }
    }
    var delivery_cnt = $('.delivery-info'),
        coreid = delivery_cnt.prop('id').replace('product-id-', '');
    $.post(delivery_cnt.data().calclink, {ids:[coreid]}, function(data){
      data = data[coreid].deliveries;
      var html = '<h4>Как получить заказ?</h4><ul>', i, row;
      for (i in data) {
        row = data[i];
        if (row.object.core_id == 3) {
          html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине '+formatDateText(row.text)+'</h5><div>&mdash; <a href="'+delivery_cnt.data().shoplink+'">В каких магазинах ENTER можно забрать?</a></div></li>';
          data.splice(i, 1);
        }
      }
      if (data.length > 0) {
        html += '<li><h5>Можно заказать сейчас с доставкой</h5>';
        for (i in data) {
          row = data[i];
          if (row.object.core_id == 2) {
            html += '<div>&mdash; Можем доставить '+formatDateText(row.text)+formatPrice(row.price)+'</div>';
            data.splice(i, 1);
          }
        }
        for (i in data) {
          row = data[i];
          html += '<div>&mdash; Можем доставить '+formatDateText(row.text)+formatPrice(row.price)+'</div>';
        }
        html += '</li>';
      }
      html += '</ul>';
      delivery_cnt.html(html);
    }, 'json');
})	