/* Обработчик смены региона */
$(function(){

    var $body = $('body'),
        $popup = $('.jsRegionPopup'),
        confirmClosedClass = 'closed';

    // Wrapper показа окна
    function openRegionPopup(){
        if ($popup.length == 0) {
            $.get('/region/init')
                .done(function (res) {
                    if (res.result) {
                        $popup = $(res.result);
                        $body.append($popup);
                        initAutocomplete($popup.find('#jscity'));
                        showPopup()
                    }
                });
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
    function showPopup() {
		var
			autoResolveUrl = $popup.data('autoresolve-url'),
			$autoresolve = $('.jsAutoresolve', $popup);

		if (autoResolveUrl != null && !$autoresolve.length) {
			$.ajax({
				type: 'GET',
				url: autoResolveUrl,
				success: function( res ) {
					if (!res.data.length) {
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
						$('.jsCityInline', $popup).prepend('<div class="cityItem mAutoresolve jsAutoresolve"><a href="'+url+'">'+name+'</a></div>');
					}

				}
			});
		}

		$popup.lightbox_me({
            autofocus: true,
            onLoad: function(){
                $popup.find('#jscity').putCursorAtEnd();
                if (!isGeoshopCookieSet()) {
                    $body.trigger('trackGoogleEvent', [{category: 'citySelector', action: 'viewed', nonInteraction: true}]);
                }

            },
            onClose: function() {
				if (!isGeoshopCookieSet()) {
                    setGeoshopCookie($popup.data('current-region-id'));
				}
            }
        })
    }

    // Init-функция, вызывается один раз, навешивает автокомплит
    function initAutocomplete($elem) {

        var submitBtn = $popup.find('#jschangecity');

        /**
         * Настройка автодополнения поля для ввода региона
         */
        $elem.autocomplete( {
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
						location = res[0].url;
					}
				});
			}

			$popup.trigger('close');
		}
    });

    $body.on('click', '.jsChangeRegionLink', function(e){
        changeRegionAction($(this).text(), $(this).attr('href'));
        e.preventDefault();
    });

    !function() {
        $('.js-region-confirm-yes').click(function() {
            var $container = $('.js-region-confirm-container');
            setGeoshopCookie($container.data('region-id'));
            $container.addClass(confirmClosedClass);
        });

        $('.js-region-confirm-no').click(function() {
            $('.js-region-confirm-container').addClass(confirmClosedClass);
            openRegionPopup();
        });
    }();
});