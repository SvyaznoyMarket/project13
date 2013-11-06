/**
 * Окно смены региона
 *
 * @param	{Object}	global	Объект window
 */
;(function( global ) {

	var body = $('body'),
		regionWindow = $('.popupRegion'),
		inputRegion = $('#jscity'),
		formRegionSubmitBtn = $('#jschangecity'),
		clearBtn = regionWindow.find('.inputClear'),

		changeRegionBtn = $('.jsChangeRegion'),

		slidesWrap = regionWindow.find('.regionSlidesWrap'),
		moreCityBtn = regionWindow.find('.moreCity'),
		leftArrow = regionWindow.find('.leftArr'),
		rightArrow = regionWindow.find('.rightArr'),
		rightArrow = regionWindow.find('.rightArr'),
		citySlides = regionWindow.find('.regionSlides'),
		slideWithCity = regionWindow.find('.regionSlides_slide');
	// end of vars


	/**
	 * Настройка автодополнения поля для ввода региона
	 */
	inputRegion.autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			$.ajax({
				url: inputRegion.data('url-autocomplete'),
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
			formRegionSubmitBtn.data('url', ui.item.url );
			formRegionSubmitBtn.removeClass('mDisabled');
		},
		open: function() {
			$( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
		},
		close: function() {
			$( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
		}
	});

	
		/**
		 * Показ окна с выбором города
		 */
	var showRegionPopup = function showRegionPopup() {
			regionWindow.lightbox_me({
				autofocus: true,
				onLoad: function(){
					if (inputRegion.val().length){
						inputRegion.putCursorAtEnd();
						formRegionSubmitBtn.removeClass('mDisabled');
					}
				},
				onClose: function() {
					var id = changeRegionBtn.data('region-id');

					if ( !global.docCookies.hasItem('geoshop') ) {
						global.docCookies.setItem('geoshop', id, 31536e3, '/');
						// document.location.reload()
					}
				}
			});
		},

		/**
		 * Обработка кнопок для смены региона
		 */
		changeRegionHandler = function changeRegionHandler() {
			var self = $(this),
				autoResolve = self.data('autoresolve-url');
			// end of vars

			var authFromServer = function authFromServer( res ) {
				if ( !res.data.length ) {
					$('.popupRegion .mAutoresolve').html('');
					return false;
				}

				var url = res.data[0].url,
					name = res.data[0].name,
					id = res.data[0].id;
				// end of vars

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

			if ( autoResolve !== 'undefined' ) {
				$.ajax({
					type: 'GET',
					url: autoResolve,
					success: authFromServer
				});
			}
			
			showRegionPopup();

			return false;
		},

		/**
		 * Следующий слайд с городами
		 */
		nextCitySlide = function nextCitySlide() {
			var regionSlideW = slideWithCity.width() * 1,
				sliderW = citySlides.width() * 1,
				sliderLeft = parseInt(citySlides.css('left'), 10);
			// end of vars

			leftArrow.show();
			citySlides.animate({'left':sliderLeft - regionSlideW});

			if ( sliderLeft - (regionSlideW * 2) <= -sliderW ) {
				rightArrow.hide();
			}

			return false;
		},

		/**
		 * Предыдущий слайд с городами
		 */
		prevCitySlide = function prevCitySlide() {
			var regionSlideW = slideWithCity.width() * 1,
				sliderW = citySlides.width() * 1,
				sliderLeft = parseInt(citySlides.css('left'), 10);
			// end of vars

			rightArrow.show();
			citySlides.animate({'left':sliderLeft + regionSlideW});

			if ( sliderLeft + (regionSlideW * 2) >= 0 ) {
				leftArrow.hide();
			}

			return false;
		},

		/**
		 * Раскрытие полного списка городов
		 */
		expandCityList = function expandCityList() {
			$(this).toggleClass('mExpand');
			slidesWrap.slideToggle(300);

			return false;
		},

		/**
		 * Очистка поля для ввода города
		 */
		clearInputHandler = function clearInputHandler() {
			inputRegion.val('');
			clearBtn.hide();
			
			return false;
		},

		/**
		 * Обработчик изменения в поле ввода города
		 */
		inputRegionChangeHandler = function inputRegionChangeHandler() {
			if ( $(this).val() ) {
				clearBtn.show();
			}
			else{
				clearBtn.hide();
			}
		},

		/**
		 * Обработчик сохранения введенного региона
		 */
		submitCityHandler = function submitCityHandler() {
			var url = $(this).data('url');

			if ( url ) {
				global.location = url;
			}
			else {
				regionWindow.trigger('close');
			}

			return false;
		};
	// end of functions


	/**
	 * ==== Handlers ====
	 */
	formRegionSubmitBtn.on('click', submitCityHandler);
	moreCityBtn.on('click', expandCityList);
	clearBtn.on('click', clearInputHandler);
	rightArrow.on('click', nextCitySlide);
	leftArrow.on('click', prevCitySlide);
	inputRegion.on('keyup', inputRegionChangeHandler);
	body.on('click', '.jsChangeRegion', changeRegionHandler);


	/**
	 * ==== GEOIP fix ====
	 */
	if ( !global.docCookies.hasItem('geoshop') ) {
		showRegionPopup();
	}
}(this));