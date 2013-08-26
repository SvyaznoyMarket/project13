/**
 * Обработчик страницы оффлайновых заданий
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
	var handleLinksToggle = function() {
		var toggle = $(this);
		var linksContainer = toggle.siblings('.links_response');
		var task = $(this).data('task');
    if(toggle.hasClass('expanded')) {
			linksContainer.html('');
			toggle.html('Ссылки');
      toggle.removeClass('expanded');
    } else {
			$.get('/cron/'+task+'/links', {}, function(data){
				if (data.success === true) {
					toggle.html('Скрыть ссылки');
					linksContainer.html(data.data);
				}
			});
      toggle.addClass('expanded');
    }
    return false;
	};

	var handleCronReportStart = function() {
		var toggle = $(this);
    if(toggle.hasClass('expanded')) {
			$('#report_start_response').html('');
			toggle.html('Сгенерировать');
      toggle.removeClass('expanded');
    } else {
			$.get('/cron/report', {}, function(data){
				if (data.success === true) {
					toggle.html('Скрыть информацию');
					$('#report_start_response').html(data.data);
				}
			});
      toggle.addClass('expanded');
    }
    return false;
	};


	$(document).ready(function(){
	  $('.cron_report_start').bind('click', handleCronReportStart);
	  $('.cronLinks').bind('click', handleLinksToggle);
	});
}());



 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Форма подписки на уцененные товары
 * Cтраница /refurbished-sale
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var discountSubscribing = function(e){
		e.preventDefault();

		var form = $('#subscribe-form');
		var wholemessage = form.serializeArray();

		var authFromServer = function(response) {
			if ( !response.success ) {
				return false;
			}
			form.find('label').hide();
			form.find('#subscribeSaleSubmit').empty().addClass('font18').html('Спасибо, уже скоро в вашей почте информация об уцененных товарах.');
		};

		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val();

		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		});

		return false;
	};

	$(document).ready(function(){
		if (!$('#subscribe-form').length){
			return false;
		}
		
		$('#subscribe-form').bind('submit', discountSubscribing);
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
$(document).ready(function(){

	if ( $('.subscribe-form__btn').length ) {
		var input = $('.subscribe-form__email'),
			form = $('.subscribe-form');
		// end of vars
		
		var subscribing = function subscribing() {
			var url = $(this).data('url'),
				email = input.val(),
				utm_source = document.location.search;
			// end of vars

			if ( email.search('@') !== -1 ) {
				$.post(url, {email: email}, function(res){
					if( !res.success ) {
						return false;
					}

					form.html('<div class="subscribe-form__title">Спасибо! подтверждение подписки отправлено на указанный e-mail</div>');
					window.docCookies.setItem('subscribed', 1, 157680000, '/');

					form.after('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');

					if( typeof(_gaq) !== 'undefined' ){
						_gaq.push(['_trackEvent', 'subscribe', email, utm_source]);
					}
				});
			}
			else {
				input.addClass('mError');
			}
			return false;
		};

		$('.subscribe-form__btn').bind('click', subscribing);

	}



	/**
	 * Бесконечный скролл
	 */
	$('.infiniteCarousel').infiniteCarousel();

	/**
	 * Получение продуктов
	 */
	if ( $('.getProductList').length ) {
		// console.log('yes!')
		$('.getProductList').each(function() {
			var wrapper = $(this),
				productList = wrapper.data('product'),
				url = '/products/widget/'+productList;
			// end of vars

			$.get(url, function( res ) {
				if ( !res.success ) {
					return false;
				}

				wrapper.html(res.content);
			});
		});
	}


	/**
	 * form register corporate
	 */
	if ( $('#corp_select').length ) {
        $('form[action="/corporate-register"]').bind('submit', function(){
            if ( $('#corp_select').find('option:selected').val() === 'Другая форма' ) {
                return false;
            }
        });

		$('#corp_select').change(function() {
			if ( $(this).find('option:selected').val() === 'Другая форма' ) {
				$('#corpNotice').lightbox_me({
					centered: true,
					closeSelector: ".close"
				});
			}
		});
	}

	/* iPadPromo*/
	if ( $('#oneClickPromo').length ) {
		$('.halfline .bOrangeButton.active').click( function() {
			var halfline = $(this).parent().parent(),
				ipad = {};
			// end of vars
			
			ipad.token = halfline.find('.ttl').text();
			ipad.price = halfline.find('.price').text();
			ipad.image = halfline.find('img').attr('src');
			$('#ipadwrapper').html( tmpl('ipad', ipad) );
			$('#order1click-container-new').lightbox_me({});
		});
		
		$('#oneClickPromo').submit( function(e) {
			e.preventDefault();

			return false;
		});
		
		if ( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = "[()0-9\ \-]";
			$("#phonemask").mask("8nnnnnnnnnnnnnnnnn", { placeholder: " ", maxlength: 10 } );
		}
		
		var emptyValidation = function emptyValidation( node ) {
			if ( node.val().replace(/\s/g,'') === '' ) {
				node.addClass('mEmpty');
				node.after( $('<span class="mEmpty">(!) Пожалуйста, верно заполните поле</span>') );

				return false;
			}
			else {
				if ( node.hasClass('mEmpty') ) {
					node.removeClass('mEmpty');
					node.parent().find('.mEmpty').remove();
				}
			}

			return true;
		};

		$('#oneClickPromo input[type=text]').change( function() {
			emptyValidation( $(this) );
		});
		
		var _f_success = function _f_success() { 
			$('#f_success').show();
			$('#f_init').hide();
		};
		
		var _f_error = function _f_error( button ) { 
			$('#oneClickPromo input[type=text]').removeAttr('disabled');
			$('#f_init h2').text('Произошла ошибка :( Попробуйте ещё');
			button.text('Отправить предзаказ');
		};
		
		$('.bBigOrangeButton').click( function(e) {
			var button = $(this),
				data = $('#oneClickPromo').serializeArray(),
				url = $('#oneClickPromo').attr('action');
			// end of vars
			
			e.preventDefault();

			$('#oneClickPromo input[type=text]').trigger('change');

			if ( $('.mEmpty').length ) {
				return;
			}

			button.text('Идёт отправка...');

			$('#oneClickPromo input[type=text]').attr('disabled','disabled');

			$.ajax( {
				url: url,
				data: data,
				success: function( resp ) {
					if ( !( 'success' in resp ) ) {
						_f_error(button);

						return false;
					}

					if ( resp.success !== 'ok' ) {
						_f_error(button);

						return false;
					}

					_f_success();

					return true;
				}
			});
			
			
		});
	}

	/* Credits inline */
	if ( $('.bCreditLine').length ) {
		document.getElementById("requirementsFullInfoHref").style.cursor="pointer";

		$('#requirementsFullInfoHref').bind('click', function() {
			$('.bCreditLine2').toggle();
		});

		var creditOptions = $('#creditOptions').data('value');
		var bankInfo = $('#bankInfo').data('value');
		var relations = $('#relations').data('value');

		for ( var i = 0; i < creditOptions.length; i++){
			var creditOption = creditOptions[i];
			$('<option>').val(creditOption.id).text(creditOption.name).appendTo("#productSelector");
		}

		$('#productSelector').change(function() {
			var key = $(this).val();
			var bankRelations = relations[key];

			$('#bankProductInfoContainer').empty();

			for ( var i in bankRelations ) {
				var dtmpl = {},
					programNames = '';
				// end of vars

				dtmpl.bankName = bankInfo[i].name;
				dtmpl.bankImage = bankInfo[i].image;
				

				for ( var j in bankRelations[i] ) {
					programNames += "<h4>" + bankInfo[i].programs[bankRelations[i][j]].name + "</h4>\r\n<ul>";

					for ( var k in bankInfo[i].programs[bankRelations[i][j]].params ) {
						programNames += "\t<li>" + bankInfo[i].programs[bankRelations[i][j]].params[k] + "</li>\r\n";
					}

					programNames += "</ul>";
				}

				dtmpl.programNames = programNames;

				var show_bank = tmpl('bank_program_list_tmpl', dtmpl);

				$('#bankProductInfoContainer').append(show_bank);
			}

			$('#bankProductInfoContainer').append('<p class="ac mb25"><a class="bBigOrangeButton" href="' + creditOptions[key - 1]['url'] + '">' + creditOptions[key - 1]['button_name'] + '</a></p>');
		});
	}

	/* Mobile apps inline */
	if ( $('.bMobileApps').length ) {
		var openSelector = '';

		var hideQRpopup = function hideQRpopup() {
			$(openSelector).hide();
		};

		var showQRpopup = function showQRpopup( selector ) {
			openSelector = selector;
			$(selector).show();

			return false;
		};

		$('body').bind('click.mob', hideQRpopup);
		$("div.bMobDown").click(function( e ) {
			e.stopPropagation();
		});

		$('.bMobDown__eClose').click( function() {
			hideQRpopup();

			return false;
		});

		$(".android-load").click(function () {
			showQRpopup(".android-block");

			return false;
		});

		$(".iphone-load").click(function () {
			showQRpopup(".iphone-block");

			return false;
		});

		$(".symbian-load").click(function () {
			showQRpopup(".symbian-block");

			return false;
		});
	}

	// login form toggle
	if ( $('#hideLoginform').length ) {
		$('#hideLoginform').bind('click', function () {
			var loginform = $('#login-form');

			$('#hideLoginform').hide();
			loginform.slideDown(300);
			$.scrollTo(loginform, 500);

			return false;
		});
	}

	/*paginator*/
	var EnterPaginator = function( domID,totalPages, visPages, activePage ) {
		
		var self = this;

		self.inputVars = {
			domID: domID, // id элемента для пагинатора
			totalPages:totalPages, //общее количество страниц
			visPages:visPages?visPages:10, // количество видимых сраниц
			activePage:activePage?activePage:1 // текущая активная страница
		};

		var pag = $('#'+self.inputVars.domID), // пагинатор
			pagW = pag.width(), // ширина пагинатора
			eSliderFillW = (pagW*self.inputVars.visPages)/self.inputVars.totalPages, // ширина закрашенной области слайдера
			onePageOnSlider = eSliderFillW / self.inputVars.visPages, // ширина соответствующая одной странице на слайдере
			onePage = pagW / self.inputVars.visPages, // ширина одной цифры на пагинаторе
			center = Math.round(self.inputVars.visPages/2);
		// end of vars

		var scrollingByBar = function scrollingByBar ( left ) {
			var pagLeft = Math.round(left/onePageOnSlider);

			$('.bPaginator_eWrap', pag).css('left', -(onePage * pagLeft));
		};

		var enableHandlers = function enableHandlers() {
			// биндим хандлеры
			var clicked = false,
				startX = 0,
				nowLeft = 0;
			// end of vars
			
			$('.bPaginatorSlider', pag).bind('mousedown', function(e){
				startX = e.pageX;
				nowLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10);
				clicked = true;
			});

			$('.bPaginatorSlider', pag).bind('mouseup', function(){
				clicked = false;
			});

			pag.bind('mouseout', function(){
				clicked = false;
			});

			$('.bPaginatorSlider', pag).bind('mousemove', function(e){
				if ( clicked ) {
					var newLeft = nowLeft+(e.pageX-startX);

					if ( (newLeft >= 0) && (newLeft <= pagW - eSliderFillW) ) {
						$('.bPaginatorSlider_eFill', pag).css('left', nowLeft + (e.pageX - startX));
						scrollingByBar(newLeft);
					}
				}
			});
		};

		var init = function init() {
			pag.append('<div class="bPaginator_eWrap"></div>');
			pag.append('<div class="bPaginatorSlider"><div class="bPaginatorSlider_eWrap"><div class="bPaginatorSlider_eFill" style="width:'+eSliderFillW+'px"></div></div></div>');
			for ( var i = 0; i < self.inputVars.totalPages; i++ ) {
				$('.bPaginator_eWrap', pag).append('<a class="bPaginator_eLink" href="#' + i + '">' + (i + 1) + '</a>');

				if ( (i + 1) === self.inputVars.activePage ) {
					$('.bPaginator_eLink', pag).eq(i).addClass('active');
				}
			}
			var realLinkW = $('.bPaginator_eLink', pag).width(); // реальная ширина цифр

			$('.bPaginator_eLink', pag).css({'marginLeft':(onePage - realLinkW - 2)/2, 'marginRight':(onePage - realLinkW - 2)/2}); // размазываем цифры по ширине слайдера
			$('.bPaginator_eWrap', pag).addClass('clearfix').width(onePage * self.inputVars.totalPages); // устанавливаем ширину wrap'а, добавляем ему очистку
		};

		self.setActive = function ( page ) {
			var left = parseInt($('.bPaginator_eWrap', pag).css('left'), 10), // текущее положение пагинатора
				barLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10), // текущее положение бара
				nowLeftElH = Math.round(left/onePage) * (-1), // количество скрытых элементов
				diff = -(center - (page - nowLeftElH)); // на сколько элементов необходимо подвинуть пагинатор для центрирования
			// end of vars
			
			$('.bPaginator_eLink', pag).removeClass('active');
			$('.bPaginator_eLink', pag).eq(page).addClass('active');

			if ( left - (diff * onePage) > 0 ) {
				left = 0;
				barLeft = 0;
			}
			else if ( page > self.inputVars.totalPages - center ) {
				left = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePage*(-1);
				barLeft = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePageOnSlider;
			}
			else {
				left = left - (diff * onePage);
				barLeft = barLeft + (diff * onePageOnSlider);
			}

			$('.bPaginator_eWrap').animate({'left': left});
			$('.bPaginatorSlider_eFill', pag).animate({'left': barLeft});
		};

		init();
		enableHandlers();
	};

	/* promo catalog */
	if ( $('#promoCatalog').length ) {
		var data = $('#promoCatalog').data('slides'),
		
		//первоначальная настройка
			slider_SlideCount = data.length, //количество слайдов
			catalogPaginator = new EnterPaginator('promoCatalogPaginator',slider_SlideCount, 12, 1);
		// end of vars

		var initSlider = function initSlider() {
			for ( var slide in data ) {
				var slideTmpl = tmpl("slide_tmpl",data[slide]);

				$('.bPromoCatalogSliderWrap').append(slideTmpl);

				if ( $('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).attr('href') === '' ) {
					$('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).removeAttr('href');
				}

				$('.bPromoCatalogNav').append('<a id="promoCatalogSlide' + slide + '" href="#' + slide + '" class="bPromoCatalogNav_eLink">' + ((slide * 1) + 1) + '</a>');
			}
		};

		initSlider(); //запуск слайдера

		//переменные
		var slider_SlideW = $('.bPromoCatalogSliderWrap_eSlide').width(),	// ширина одного слайда
			slider_WrapW = $('.bPromoCatalogSliderWrap').width( slider_SlideW * slider_SlideCount + (920/2 - slider_SlideW/2)),	// установка ширины обертки
			nowSlide = 0;	//текущий слайд
		// end of vars

		//листание стрелками
		$('.bPromoCatalogSlider_eArrow').bind('click', function() {
			var pos = ( $(this).hasClass('mArLeft') ) ? '-1' : '1';

			moveSlide(nowSlide + pos * 1);

			return false;
		});

		//пагинатор
		$('.bPaginator_eLink').bind('click', function() {
			if ( $(this).hasClass('active') ) {
				return false;
			}

			var link = $(this).attr('href').slice(1) * 1;

			moveSlide(link);

			return false;
		});

		//перемещение слайдов на указанный слайд
		var moveSlide = function moveSlide( slide ) {
			if ( slide === 0 ) {
				$('.bPromoCatalogSlider_eArrow.mArLeft').hide();
			}
			else{
				$('.bPromoCatalogSlider_eArrow.mArLeft').show();
			}

			if ( slide === slider_SlideCount - 1 ) {
				$('.bPromoCatalogSlider_eArrow.mArRight').hide();
			}
			else {
				$('.bPromoCatalogSlider_eArrow.mArRight').show();
			}

			$('.bPromoCatalogSliderWrap').animate({'left': -(slider_SlideW * slide)},500, function() {
				nowSlide = slide;
			});

			window.location.hash = "slide" + (slide + 1);
			catalogPaginator.setActive(slide);
		};

		var hash = window.location.hash;

		if ( hash.indexOf('slide') + 1 ) {
			var toSlide = parseInt(hash.slice(6), 10) - 1;

			moveSlide(toSlide);
		}


	}
});
