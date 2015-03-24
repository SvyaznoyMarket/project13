/**
 * Окно смены региона
 */
;(function() {

	var body = $('body'),
		regionWindow = $('.popupRegion'),
		inputRegion = $('#jscity'),
		formRegionSubmitBtn = $('#jschangecity'),
		clearBtn = regionWindow.find('.inputClear'),

		changeRegionAnalyticsBtn = $('.jsChangeRegionAnalytics'),

		slidesWrap = regionWindow.find('.regionSlidesWrap'),
		moreCityBtn = regionWindow.find('.moreCity'),
		leftArrow = regionWindow.find('.leftArr'),
		rightArrow = regionWindow.find('.rightArr'),
		citySlides = regionWindow.find('.regionSlides'),
		slideWithCity = regionWindow.find('.regionSlides_slide');

	/**
	 * Настройка автодополнения поля для ввода региона
	 */
	inputRegion.autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			queryAutocompleteVariants(request.term, function(res) {
				response( $.map( res, function( item ) {
					return {
						label: item.name,
						value: item.name,
						url: item.url
					};
				}));
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			formRegionSubmitBtn.data('url', ui.item.url );
			submitBtnEnable();
		},
		open: function() {
			$( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
		},
		close: function() {
			$( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
		}
	});

	function queryAutocompleteVariants(term, onSuccess) {
		$.ajax({
			url: inputRegion.data('url-autocomplete'),
			dataType: 'json',
			data: {
				q: term
			},
			success: function( data ) {
				if (onSuccess) {
					onSuccess(data.data.slice(0, 15));
				}
			}
		});
	}
	
	function isGeoshopCookieSet() {
		return Boolean(parseInt(docCookies.getItem('geoshop')));
	}
	
	/**
	 * Показ окна с выбором города
	 */
	function openRegionPopup() {
		var autoResolveUrl = formRegionSubmitBtn.data('autoresolve-url');

		if (typeof autoResolveUrl !== 'undefined' ) {
			$.ajax({
				type: 'GET',
				url: autoResolveUrl,
				success: function( res ) {
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

				}
			});
		}

		regionWindow.lightbox_me({
			autofocus: true,
			onLoad: function(){
				if (inputRegion.val().length){
					inputRegion.putCursorAtEnd();
					submitBtnEnable();
				}
			},
			onClose: function() {
				if (!isGeoshopCookieSet()) {
					docCookies.setItem('geoshop', formRegionSubmitBtn.data('current-region-id'), 31536e3, '/');
				}
			}
		});

		// analytics only for main page
		if ( location.pathname === '/' ) {
			body.trigger('trackGoogleEvent',[{category: 'citySelector', action: 'viewed', nonInteraction: true}]);
		}

		return false;
	}

	/**
	 * Следующий слайд с городами
	 */
	function nextCitySlide() {
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
	}

	/**
	 * Предыдущий слайд с городами
	 */
	function prevCitySlide() {
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
	}

	/**
	 * Раскрытие полного списка городов
	 */
	function expandCityList() {
		$(this).toggleClass('mExpand');
		slidesWrap.slideToggle(300);

		return false;
	}

	/**
	 * Очистка поля для ввода города
	 */
	function clearInputHandler() {
		inputRegion.val('');
		submitBtnDisable();
		clearBtn.hide();

		return false;
	}

	/**
	 * Обработчик изменения в поле ввода города
	 */
	function inputRegionChangeHandler() {
		if ( $(this).val() ) {
			submitBtnEnable();
			clearBtn.show();
		}
		else {
			submitBtnDisable();
			clearBtn.hide();
		}
	}

	function changeRegionAnalytics( regionName ) {
		if ( typeof _gaq !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'citySelector', 'selected', regionName]);
		}

		if (typeof ga == 'function') {
			ga('send', 'event', 'citySelector', 'selected', regionName, {
				'dimension14': regionName
			});
		}
	}

	function changeRegionAnalyticsHandler() {
		var regionName = $(this).text();

		changeRegionAnalytics(regionName);
	}

	/**
	 * Обработчик сохранения введенного региона
	 */
	function submitCityHandler() {
		var
			url = $(this).data('url'),
			regionName = inputRegion.val();
		// end of vars

		changeRegionAnalytics(regionName);

		if ( url ) {
			location = url;
		}
		else {
			if (ENTER.utils.trim(inputRegion[0].defaultValue) != ENTER.utils.trim(regionName)) {
				queryAutocompleteVariants(regionName, function(res) {
					if (res[0] && res[0].url) {
						location = res[0].url;
					}
				});
			}

			regionWindow.trigger('close');
		}

		return false;
	}

	/**
	 * Блокировка кнопки "Сохранить"
	 */
	function submitBtnDisable() {
		formRegionSubmitBtn.addClass('mDisabled');
		formRegionSubmitBtn.attr('disabled','disabled');
	}

	/**
	 * Разблокировка кнопки "Сохранить"
	 */
	function submitBtnEnable() {
		formRegionSubmitBtn.removeClass('mDisabled');
		formRegionSubmitBtn.removeAttr('disabled');
	}

	/**
	 * ==== Handlers ====
	 */
	formRegionSubmitBtn.on('click', submitCityHandler);
	moreCityBtn.on('click', expandCityList);
	clearBtn.on('click', clearInputHandler);
	rightArrow.on('click', nextCitySlide);
	leftArrow.on('click', prevCitySlide);
	inputRegion.on('keyup', inputRegionChangeHandler);
	body.on('click', '.jsChangeRegion', openRegionPopup);

	changeRegionAnalyticsBtn.on('click', changeRegionAnalyticsHandler);


	/**
	 * ==== GEOIP fix ====
	 */
	if (!isGeoshopCookieSet()) {
		openRegionPopup();
	}
}());