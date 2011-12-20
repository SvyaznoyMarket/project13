$(document).ready(function() {

    var filterlink = $('.filter .filterlink:first');
	var filterlist = $('.filter .filterlist');
	var userag    = navigator.userAgent.toLowerCase()
	var isAndroid = userag.indexOf("android") > -1
	var isOSX     = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 )
	if( isAndroid || isOSX ) {
		filterlink.click(function(){
			filterlink.hide();
			filterlist.show();
			return false
		});
	} else {
		filterlink.mouseenter(function(){
			filterlink.hide();
			filterlist.show();
		});
		filterlist.mouseleave(function(){
			filterlist.hide();
			filterlink.show();
		});
	}
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

    $('.product_filter-block')
    // change
    .bind('change', function(e) {
        var el = $(e.target)

        if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
            el.trigger('preview')
            return false
        }
    })
    // preview
    .bind('preview', function(e) {
        var el = $(e.target)
        var form = $(this)

        function disable() {
            var d = $.Deferred();
            //el.attr('disabled', true)
            return d.resolve();
        }

        function enable() {
            var d = $.Deferred();
            //el.attr('disabled', false)
            return d.promise();
        }

        function getData() {
            var d = $.Deferred();

            form.ajaxSubmit({
                url: form.data('action-count'),
                success: d.resolve,
                error: d.reject
            })

            return d.promise();
        }

        $.when(getData())
        .then(function(result) {
            if (true === result.success) {
                $('.product_count-block').remove();
                //el.parent().find('> label').first().after('<div class="product_count-block" style="position: absolute; background: #fff; padding: 4px; opacity: 0.9; border-radius: 5px; border: 1px solid #ccc; cursor: pointer;">Найдено '+result.data+'</div>')
                switch (result.data % 10) {
                  case 1:
                    ending = 'ь';
                    break
                  case 2: case 3: case 4:
                    ending = 'и';
                    break
                  default:
                    ending = 'ей';
                    break
                }
                switch (result.data % 100) {
                  case 11: case 12: case 13: case 14:
                    ending = 'ей';
                    break
                }
                var firstli = null
                if ( el.is("div") ) //triggered from filter slider !
                	firstli = el
                else
	                firstli = el.parent().find('> label').first()
                firstli.after('<div class="filterresult product_count-block" style="display:block; padding: 4px; margin-top: -30px; cursor: pointer;"><i class="corner"></i>Выбрано '+result.data+' модел'+ending+'<br /><a>Показать</a></div>')
                $('.product_count-block')
                .hover(
                    function() {
                        $(this).stopTime('hide')
                    },
                    function() {
                        $(this).oneTime(2000, 'hide', function() {
                            $(this).remove()
                        })
                    }
                    )
                .click(function() {
                    form.submit()
                })
                .trigger('mouseout')
            }
        })
        .fail(function(error) {})
    })

	function printPrice ( val ) {

		var float = (val+'').split('.')
		var out = float[0]
		var le = float[0].length
		if( le > 6 ) { // billions
			out = out.substr( 0, le - 6) + ' ' + out.substr( le - 6, le - 4) + ' ' + out.substr( le - 3, le )
		} else if ( le > 3 ) { // thousands
			out = out.substr( 0, le - 3) + ' ' + out.substr( le - 3, le )
		}
		if( float.length == 2 )
			out += '.' + float[1]
		return out// + '&nbsp;'
	}

	if( $('.order1click-link').length ) {
		var cl1loaded = false
		$('.order1click-link').bind('click', function(e) {
			e.preventDefault()

			if ( !cl1loaded ) {
				$('#ajaxgoods').lightbox_me({
					centered: true,
					closeClick: false,
					closeEsc: false
				})
				$.get( $(this).attr('href'), function( response ) {
					//$('#ajaxgoods').trigger('close')
					$('#ajaxgoods').hide()
					if( typeof(response.success) !== 'undefined' && response.success ) {
						$('#order1click-form').html(response.data.form)
						$('#order1click-container').lightbox_me({
							centered: true
						})
						cl1loaded = true
						bindCalc()
					}
				})
			} else {
				$('#order1click-container').lightbox_me({
					centered: true
				})
			}
		})

		function bindCalc() {
			var quant = 1
			var pric  = $('.b1Click__ePriceBig .price').html().replace(/\s/g,'')
			function recalc( delta ) {
				if( quant == 1 && delta < 0 )
					return
				quant += delta
				var sum = printPrice( pric * quant )
				$('.c1quant').html( quant+ ' шт.')
				$('#order_product_quantity').val( quant )
				$('.b1Click__ePriceBig .price').html( sum )
			}

			$('.c1less').bind( 'click', function(){ recalc(-1) })
			$('.c1more').bind( 'click', function(){ recalc(1) })
		}

		$('#order1click-form').bind('submit', function(e) {
			e.preventDefault()
			
			$(this).ajaxSubmit({
				beforeSubmit: function() {
					var button = $('#order1click-form').find('input:submit')
					button.attr('disabled', true)
					button.val('Оформляю заказ...')
				},
				success: function( response ) {
					if( !response.success ) {
						if( response.data ) {
							$('#order1click-form').html(response.data.form)
						}
						var button = $('#order1click-form').find('input:submit')
						button.attr('disabled', false)
						button.val('Оформить заказ')
						if( !$('#warn').length ) {
							var warn = $('<span id="warn" style="color:red">').html('Не удалось оформить заказ. Приносим свои извинения! Повторите попытку или обратитесь с заказом в контакт cENTER&nbsp;8&nbsp;(800)&nbsp;700&nbsp;00&nbsp;09')
							$('.bFormB2').before( warn )
						}
					} else {
						if( response.data ) {
							$('#order1click-container').find('h2').html(response.data.title)
							$('#order1click-form').replaceWith(response.data.content)
						}
					}
				},
				error: function() {
					var button = $('#order1click-form').find('input:submit')
					button.attr('disabled', false)
					button.val('Попробовать еще раз')
				}
			})
		})

    }

});