/**
 * Окно смены региона
 * WARNING: необходим рефакторинг!
 *
 * @param	{Object}	global	Объект window
 */
;(function( global ) {

	$('#jscity').autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			$.ajax({
				url: $('#jscity').data('url-autocomplete'),
				dataType: 'json',
				data: {
					q: request.term
				},
				success: function( data ) {
					var res = data.data.slice(0, 15);
					response( $.map( res, function( item ) {
						return {
							label: item.name,
							value: item.name,
							url: item.url
						};
					}));
				}
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#jschangecity').data('url', ui.item.url );
			$('#jschangecity').removeClass('mDisabled');
		},
		open: function() {
			$( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
		},
		close: function() {
			$( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
		}
	});
	
	function getRegions() {
		$('.popupRegion').lightbox_me({
			autofocus: true,
			onLoad: function(){
				if ($('#jscity').val().length){
					$('#jscity').putCursorAtEnd();
					$('#jschangecity').removeClass('mDisabled');
				}
			},
			onClose: function() {			
				if( !window.docCookies.hasItem('geoshop') ) {
					var id = $('#jsregion').data('region-id');
					window.docCookies.setItem('geoshop', id, 31536e3, '/');
					// document.location.reload()
				}
			}
		});
	}

	$('.cityItem .moreCity').bind('click',function(){
		$(this).toggleClass('mExpand');
		$('.regionSlidesWrap').slideToggle(300);
	});

	$('#jsregion, .jsChangeRegion').click( function() {
		var authFromServer = function( res ) {
			if ( !res.data.length ) {
				$('.popupRegion .mAutoresolve').html('');
				return false;
			}

			var url = res.data[0].url;
			var name = res.data[0].name;
			var id = res.data[0].id;

			if ( id === 14974 || id === 108136 ) {
				return false;
			}
			
			if ( $('.popupRegion .mAutoresolve').length ) {
				$('.popupRegion .mAutoresolve').html('<a href="'+url+'">'+name+'</a>');
			}
			else {
				$('.popupRegion .cityInline').prepend('<div class="cityItem mAutoresolve"><a href="'+url+'">'+name+'</a></div>');
			}
			
		};

		var autoResolve = $(this).data('autoresolve-url');

		if ( autoResolve !=='undefined' ) {
			$.ajax({
				type: 'GET',
				url: autoResolve,
				success: authFromServer
			});
		}
		
		getRegions();
		return false;
	});
	
	$('body').delegate('#jschangecity', 'click', function(e) {
		e.preventDefault();
		if( $(this).data('url') ){
			window.location = $(this).data('url');
		}
		else{
			$('.popupRegion').trigger('close');
		}
	});

	$('body').on('keyup click', '#jscity', function(e) {
		if( $(this).val() ) {
			$('.inputClear').show();
		}
		else{
			$('.inputClear').hide();
		}
	});

	$('.inputClear').bind('click', function(e) {
		e.preventDefault();
		$('#jscity').val('');
	});

	$('.popupRegion .rightArr').bind('click', function() {
		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
		var sliderW = $('.popupRegion .regionSlides').width() *1;
		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'), 10);

		$('.popupRegion .leftArr').show();
		$('.popupRegion .regionSlides').animate({'left':sliderLeft-regionSlideW});

		if ( (sliderLeft-(regionSlideW * 2)) <= -sliderW ) {
			$('.popupRegion .rightArr').hide();
		}
	});
	$('.popupRegion .leftArr').bind('click', function() {
		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
		var sliderW = $('.popupRegion .regionSlides').width() *1;
		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'), 10);

		$('.popupRegion .rightArr').show();
		$('.popupRegion .regionSlides').animate({'left':sliderLeft+regionSlideW});

		if ( sliderLeft+(regionSlideW * 2) >= 0 ) {
			$('.popupRegion .leftArr').hide();
		}
	});

	/* GEOIP fix */
	if ( !window.docCookies.hasItem('geoshop') ) {
		getRegions();
	}
}(this));