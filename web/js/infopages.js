$(document).ready(function(){
	/* iPadPromo*/
	if( $('#oneClickPromo').length ) {
		$('.halfline .bOrangeButton.active').click( function() {
			var halfline = $(this).parent().parent()
			var ipad = {}
			ipad.token = halfline.find('.ttl').text()
			ipad.price = halfline.find('.price').text()
			ipad.image = halfline.find('img').attr('src')
			$('#ipadwrapper').html( tmpl('ipad', ipad) )
			$('#order1click-container-new').lightbox_me({})
		})
		
		$('#oneClickPromo').submit( function(e) {
			e.preventDefault()
			return false
		})
		
		if( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = "[()0-9\ \-]"
			$("#phonemask").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } )
		}
		
		function emptyValidation( node ) {
			if( node.val().replace(/\s/g,'') === '' ) {
				node.addClass('mEmpty')
				node.after( $('<span class="mEmpty">(!) Пожалуйста, верно заполните поле</span>') )
				return false
			} else {
				if( node.hasClass('mEmpty') ) {
					node.removeClass('mEmpty')
					node.parent().find('.mEmpty').remove()
				}
			}
			return true	
		}

		$('#oneClickPromo input[type=text]').change( function() {
			emptyValidation( $(this) )
		})
		
		function _f_success() { 
			$('#f_success').show()
			$('#f_init').hide()
		}
		
		function _f_error() { 
			$('#oneClickPromo input[type=text]').removeAttr('disabled') 
			$('#f_init h2').text('Произошла ошибка :( Попробуйте ещё')
			button.text('Отправить предзаказ')
		}		
		
		$('.bBigOrangeButton').click( function(e) {
			e.preventDefault()
			$('#oneClickPromo input[type=text]').trigger('change')
			if( $('.mEmpty').length )
				return
				
			var button = $(this)
			button.text('Идёт отправка...')
			var data= $('#oneClickPromo').serializeArray()
			$('#oneClickPromo input[type=text]').attr('disabled','disabled') 
			var url = $('#oneClickPromo').attr('action')
			$.ajax( {
				url: url,
				data: data,
				success: function( resp ) {
				if( !( 'success' in resp ) ) {
					_f_error()
					return false
				}
				if( resp.success !== 'ok' ) {
					_f_error()
					return false
				}
				_f_success()	
				return true
			}
			})
			
			
		})
	}

	/* Credits inline */
	if( $('.bCreditLine').length ) {
		document.getElementById("requirementsFullInfoHref").style.cursor="pointer";
		$('#requirementsFullInfoHref').bind('click', function() {
		  $('.bCreditLine2').toggle();
		});

		var creditOptions = $('#creditOptions').data('value');
		var bankInfo = $('#bankInfo').data('value');
		var relations = $('#relations').data('value');

		for (var i=0; i< creditOptions.length; i++){
		  creditOption = creditOptions[i];
		  $('<option>').val(creditOption.id).text(creditOption.name).appendTo("#productSelector");
		}

		$('#productSelector').change(function() {
		  var key = $(this).val();
		  var bankRelations = relations[key];
		  $('#bankProductInfoContainer').empty();
		  for(i in bankRelations){
			var dtmpl={}
			dtmpl.bankName = bankInfo[i].name;
			dtmpl.bankImage = bankInfo[i].image;

			programNames = '';

			for(j in bankRelations[i]){
			  programNames += "<h4>" + bankInfo[i].programs[bankRelations[i][j]].name + "</h4>\r\n<ul>";
			  for(k in bankInfo[i].programs[bankRelations[i][j]].params){
				programNames += "\t<li>" + bankInfo[i].programs[bankRelations[i][j]].params[k] + "</li>\r\n";
			  }
			  programNames += "</ul>";
			}

			dtmpl.programNames = programNames;

			var show_bank = tmpl('bank_program_list_tmpl', dtmpl)
			$('#bankProductInfoContainer').append(show_bank);
		  }
		  $('#bankProductInfoContainer').append('<p class="ac mb25"><a class="bBigOrangeButton" href="'+creditOptions[key-1]['url']+'">'+creditOptions[key-1]['button_name']+'</a></p>');
		});
	}

	/* Mobile apps inline */
	if( $('.bMobileApps').length ) {
		var openSelector = ''

		function hideQRpopup() {
			$(openSelector).hide()
		}
		function showQRpopup( selector ) {
			openSelector = selector
			$(selector).show()
			return false
		}

		$('body').bind('click.mob', hideQRpopup)
		$("div.bMobDown").click(function(e){
			e.stopPropagation()
		})

		$('.bMobDown__eClose').click( function() {
			hideQRpopup()
			return false
		})

		$(".android-load").click( function(){ showQRpopup( ".android-block" ); return false; } )
		$(".iphone-load").click(  function(){ showQRpopup( ".iphone-block" );  return false; } )
		$(".symbian-load").click( function(){ showQRpopup( ".symbian-block" ); return false; } )
	}

	/* promo catalog */
	if ( $('#promoCatalog').length){
		var data = {
			"slides":[
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide1.jpg", "title":"slide1", "linkUrl":"#1"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide2.jpg", "title":"slide2", "linkUrl":"#2"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide3.jpg", "title":"slide3", "linkUrl":"#3"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide4.jpg", "title":"slide4", "linkUrl":"#4"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide1.jpg", "title":"slide1", "linkUrl":"#1"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide2.jpg", "title":"slide2", "linkUrl":"#2"},
				{"imgUrl":"http://content.enter.ru/wp-content/uploads/2013/01/slide3.jpg", "title":"slide3", "linkUrl":"#3"},
			]
		}

		//первоначальная настройка
		for (var slide in data.slides){
			var slideTmpl = tmpl("slide_tmpl",data.slides[slide])
			$('.bPromoCatalogSliderWrap').append(slideTmpl)
			$('.bPromoCatalogNavArrow.mCatalogNavRight').before('<a href="#'+slide+'" class="bPromoCatalogNav_eLink">'+((slide*1)+1)+'</a>')
		}
		$('.bPromoCatalogNav_eLink:first').addClass('active')
		var slider_SlideW = $('.bPromoCatalogSliderWrap_eSlide').width() // ширина одного слайда
		var slider_SlideCount = data.slides.length //количество слайдов
		var slider_WrapW = $('.bPromoCatalogSliderWrap').width( slider_SlideW * slider_SlideCount + (920/2 - slider_SlideW/2)) // установка ширины обертки
		var nowSlide = 0 //текущий слайд

		//листание стрелками
		$('.bPromoCatalogSlider_eArrow').bind('click', function() {
			var pos = ( $(this).hasClass('mArLeft'))?'-1':'1'
			nowSlide = nowSlide + pos*1
			moveSlide(nowSlide)
			return false
		})
		//пагинатор
		$('.bPromoCatalogNav_eLink').bind('click', function() {
			if ( $(this).hasClass('active') )
				return false
			var link = $(this).attr('href').slice(1)*1
			moveSlide(link)
			return false
		})

		//перемещение слайдов на указанный слайд
		var moveSlide = function(slide) {
			if (slide === 0){
				$('.bPromoCatalogSlider_eArrow.mArLeft').hide()
			}
			else{
				$('.bPromoCatalogSlider_eArrow.mArLeft').show()
			}
			if (slide === slider_SlideCount-1) {
				$('.bPromoCatalogSlider_eArrow.mArRight').hide()
			}
			else{
				$('.bPromoCatalogSlider_eArrow.mArRight').show()
			}
			$('.bPromoCatalogNav_eLink').removeClass('active')
			$('.bPromoCatalogSliderWrap').animate({'left':-(slider_SlideW*slide)},500)
			$('.bPromoCatalogNav_eLink').eq(slide).addClass('active')
			nowSlide = slide
		}
	}
})