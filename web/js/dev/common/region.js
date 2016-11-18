/* Обработчик смены региона */
$(function(){

    var $body = $('body'),
        $popup = $('.jsRegionPopup'),
		isInit = false;

    function openRegionPopup(){
        if (!isInit) {
			showPopup(function() {
				initAutocomplete($popup.find('#jscity'));
			});
			isInit = true;
        } else {
			showPopup();
        }
    }

    // Основная функция, которая сначала отправляет аналитику, а потом меняет регион
    function changeRegionAction(regionName, url) {
        $body.trigger('trackGoogleEvent',{
            category: 'citySelector',
            action: 'selected',
            label: regionName,
            hitCallback: url
        });
    }

	function isGeoshopCookieSet() {
		return Boolean(parseInt(docCookies.getItem('geoshop')));
	}

    function setGeoshopCookie(regionId) {
        docCookies.setItem('geoshop', regionId, 31536e3, '/');
    }

	function queryAutocompleteVariants(term, onSuccess) {
		$.ajax({
			url: $popup.data('autocomplete-url'),
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

    // Lightbox
    function showPopup(onLoad) {
		var
			autoResolveUrl = $popup.data('autoresolve-url'),
			$autoresolve = $('.jsAutoresolve', $popup);

		if (autoResolveUrl != null && !$autoresolve.length) {
			$.ajax({
				type: 'GET',
				url: autoResolveUrl,
				success: function( res ) {
					if (!res.data || !res.data.length) {
						$autoresolve.html('');
						return false;
					}

					var url = res.data[0].url,
						name = res.data[0].name,
						id = res.data[0].id;

					if (id === 14974 || id === 108136) {
						return false;
					}

					if ($autoresolve.length) {
						$autoresolve.html('<a href="' + url + '">' + name + '</a>');
					}  else {
						$('.jsCityInline', $popup).prepend('<a href="'+url+'" class="cityItem mAutoresolve jsAutoresolve">'+name+'</a>');
					}

				}
			});
		}

		$popup.lightbox_me({
            autofocus: true,
            onLoad: function(){
                $popup.find('#jscity').focus().putCursorAtEnd();
                if (!isGeoshopCookieSet()) {
                    $body.trigger('trackGoogleEvent', [{category: 'citySelector', action: 'viewed', nonInteraction: true}]);
                }

                if (onLoad) {
                    onLoad();
                }

            },
            onClose: function() {
				if (!isGeoshopCookieSet()) {
                    setGeoshopCookie($popup.data('current-region-id'));
				}
            }
        });
    }

    // Init-функция, вызывается один раз, навешивает автокомплит
    function initAutocomplete($elem) {

        var submitBtn = $popup.find('#jschangecity');

        /**
         * Настройка автодополнения поля для ввода региона
         */
        $elem.myAutocomplete( {
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
                submitBtn.data('url', ui.item.url ).removeClass('mDisabled').removeAttr('disabled');
            },
            open: function() {
                $(this).removeClass('ui-corner-all').addClass('ui-corner-top');
            },
            close: function() {
                $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            }
        });
    }

    // клик по названию региона в юзербаре
    $body.on('click', '.jsChangeRegion', function(e) {
		e.preventDefault();
		openRegionPopup();
	});

    // очистка поля ввода
    $body.on('click', '.jsRegionInputClear', function(e){
        e.preventDefault();
        $popup.find('#jscity').val('');
        $popup.find('#jschangecity').addClass('mDisabled').attr('disabled','disabled');
    });

    $body.on('input', '#jscity', function(e){
        e.preventDefault();
        if($popup.find('#jscity').val() == ''){
            $popup.find('#jschangecity').addClass('mDisabled').attr('disabled','disabled');
        }
    });

    // Клик по кнопке "Сохранить"
    $body.on('click', '#jschangecity', function submitCityHandler(e) {
		e.preventDefault();

        var url = $(this).data('url'),
            inputRegion = $popup.find('#jscity'),
            regionName = inputRegion.val();

		if (url) {
			changeRegionAction(regionName, url);
		} else {
			// SITE-5123
			if (ENTER.utils.trim(inputRegion[0].defaultValue) != ENTER.utils.trim(regionName)) {
				queryAutocompleteVariants(regionName, function(res) {
					if (res[0] && res[0].url) {
						location.href = res[0].url;
					}
				});
			}

			$popup.trigger('close');
		}
    });

    $body.on('click', '.jsChangeRegionLink', function(e){
        changeRegionAction($(this).text(), ENTER.utils.router.generateUrl('region.change', {'regionId': $(this).attr('data-region-id')}));
        e.preventDefault();
    });

	$body.on('click', '.js-regionSelection-showMoreCities', function(e){
		e.preventDefault();
		$(e.currentTarget).remove();
		$('.js-regionSelection-moreCities').show();
	});

    // Блок "Ваш город Москва?"
    !function() {
        $('.js-region-confirm-yes').click(function(e) {
            e.preventDefault();
            var $container = $('.js-region-confirm-container');
            setGeoshopCookie($container.data('region-id'));
            $container.fadeOut(200);
        });

        $('.js-region-confirm-no').click(function(e) {
            e.preventDefault();
            $('.js-region-confirm-container').fadeOut(200);
            openRegionPopup();
        });
    }();
});