$(document).ready(function() {
	/* Rating */
	if( $('#rating').length ) {
		var iscore = $('#rating').next().html().replace(/\D/g,'')
		$('#rating span').remove()
		$('#rating').raty({
		  start: iscore,
		  showHalf: true,
		  path: '/css/skin/img/',
		  readOnly: $('#rating').data('readonly'),
		  starHalf: 'star_h.png',
		  starOn: 'star_a.png',
		  starOff: 'star_p.png',
		  hintList: ['плохо', 'удовлетворительно', 'нормально', 'хорошо', 'отлично'],
		  click: function( score ) {
		  		$.getJSON( $('#rating').attr('data-url').replace('score', score ) , function(data){
		  			if( data.success === true && data.data.rating ) {
		  				$.fn.raty.start( data.data.rating ,'#rating' )
		  				$('#rating').next().html( data.data.rating )
		  			}
		  		})
		  		$.fn.raty.readOnly(true, '#rating')
		  	}
		})
	}
	
	/* Product Counter */
	if( $('.bCountSet').length ) {
		var np = $('.goodsbarbig .bCountSet')
		var l1 = np.parent().find('.link1')
		var l1href = l1.attr('href')
		var l1cl = $('a.order1click-link')
		var l1clhref = l1cl.attr('href')
		np.data('hm', np.first().find('span').text().replace(/\D/g,'') )
		
		np.bind('update', function() {
			var hm = $(this).data('hm')
			np.find('span').text( hm + '  шт.')
			l1.attr('href', l1href + '/' +  hm )
			l1cl.attr('href', l1clhref + '&quantity=' + hm )
		})
		
		$('.bCountSet__eP', np).click( function() {
			if( $(this).hasClass('disabled') )
				return false
			np.data('hm', np.data('hm')*1 + 1 )
			np.trigger('update')
			return false
		})
		$('.bCountSet__eM', np).click( function() {	
			if( $(this).hasClass('disabled') )
				return false		
			var hm = np.data('hm')//how many
			if( hm == 1 )
				return false
			np.data('hm', np.data('hm')*1 - 1 )
			np.trigger('update')
			return false
		})		
	}
	
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
	
	if( typeof( product_3d_small ) !== 'undefined' && typeof( product_3d_big ) !== 'undefined' )
		lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	if( $('#bigpopup').length )
		var mLib = new mediaLib( $('#bigpopup') )

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
	
	/* Delivery Block */
    var formatDateText = function(txt){
      txt = txt.replace('сегодня', '<b>сегодня</b>');
      txt = txt.replace(' завтра', ' <b>завтра</b>');
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
    var delivery_cnt = $('.delivery-info');
    if (delivery_cnt.length) {
		var coreid = delivery_cnt.prop('id').replace('product-id-', '')
		$.post( delivery_cnt.data().calclink, {ids:[coreid]}, function( data ) {
			if( !('success' in data ) )
				return false
			if( !data.success || !data.data[coreid] )
				return false		
			data = data.data[coreid]
			var html = '<h4>Как получить заказ?</h4><ul>', i, row
			for (i in data) {
				row = data[i]
				if (row.token == 'self') {
					html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине ' +
						formatDateText(row.date) + '</h5><div>&mdash; <a target="blank" href="' +
						delivery_cnt.data().shoplink + '">В каких магазинах ENTER можно забрать?</a></div></li>';
					data.splice(i, 1);
				}
			}
			if (data.length > 0) {
				html += '<li><h5>Можно заказать сейчас с доставкой</h5>';
				for (i in data) {
					row = data[i];
					//if (row.token == 'standart') {
						html += '<div>&mdash; Можем доставить '+formatDateText(row.date)+'</div>';
						data.splice(i, 1);
					//}
				}
				html += '</li>';
			}
			html += '</ul>';
			delivery_cnt.html(html);
		}, 'json');
    }
    
	/* Some handlers */
    $('.bDropMenu').each( function() {
		var jspan  = $(this).find('span:first')
		var jdiv   = $(this).find('div')
		jspan.css('display','block')
		if( jspan.width() + 60 < jdiv.width() )
			jspan.width( jdiv.width() - 70)
		else
			jdiv.width( jspan.width() + 70)
	})
	
    $('.product_rating-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            if (true == result.success) {
                $('.product_rating-form').effect('highlight', {}, 2000)
            }
        }
    })

    $('.product_comment-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            $(this).find('input:submit').attr('disabled', false)
            if (true == result.success) {
                $($(this).data('listTarget')).replaceWith(result.data.list)
                $.scrollTo('.' + result.data.element_id, 500, {
                    onAfter: function() {
                        $('.' + result.data.element_id).effect('highlight', {}, 2000);
                    }
                })
            }
        }
    })

    $('.product_comment_response-link').live({
        'content.update.prepare': function(e) {
            $('.product_comment_response-block').html('')
        },
        'content.update.success': function(e) {
            $('.product_comment_response-block').find('textarea:first').focus()
        }
    })
    

});