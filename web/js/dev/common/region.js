/* Обработчик смены региона */
;(function($){

    var $body = $('body'),
        $popup = $('.jsRegionPopup'),
        openRegionPopup, showPopup, initAutocomplete, changeRegionAction;

    // Wrapper показа окна
    openRegionPopup = function openRegionPopupF(){
        if ($popup.length == 0) {
            $.get('/region/init')
                .done(function (res) {
                    if (res.result) {
                        $popup = $(res.result);
                        $body.append($popup);
                        initAutocomplete($popup.find('#jscity'));
                        showPopup($popup)
                    }
                });
        } else {
            showPopup($popup);
        }

        // analytics only for main page
        if ( document.location.pathname === '/' ) {
            $body.trigger('trackGoogleEvent', [{category: 'citySelector', action: 'viewed', nonInteraction: true}]);
        }

    };

    // Основная функция, которая сначала отправляет аналитику, а потом меняет регион
    changeRegionAction = function changeRegionActionF(regionName, url) {
        $body.trigger('trackGoogleEvent',{
            category: 'citySelector',
            action: 'selected',
            label: regionName,
            hitCallback: url
        });
    };

	function isGeoshopCookieSet() {
		return Boolean(parseInt(docCookies.getItem('geoshop')));
	}

    // Lightbox
    showPopup = function showPopupF($elem) {
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

        $elem.lightbox_me({
            autofocus: true,
            onLoad: function(){
                $popup.find('#jscity').putCursorAtEnd();
            },
            onClose: function() {
				if (!isGeoshopCookieSet()) {
					docCookies.setItem('geoshop', $popup.data('current-region-id'), 31536e3, '/');
				}
            }
        })
    };

    // Init-функция, вызывается один раз, навешивает автокомплит
    initAutocomplete = function initAutoCompleteF($elem) {

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

        function queryAutocompleteVariants(term, onSuccess) {
            $.ajax({
                url: $elem.data('url-autocomplete'),
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

    };

    // клик по названию региона в юзербаре
    $body.on('click', '.jsChangeRegion', function(e) {
		e.preventDefault();
		openRegionPopup();
	});

    // полный список городов
    $body.on('click', '.jsRegionListMoreCity', function(e){
        e.preventDefault();
        $(this).toggleClass('mExpand');
        $popup.find('.jsRegionSlidesWrap').slideToggle(300);
    });

    // очистка поля ввода
    $body.on('click', '.jsRegionInputClear', function(e){
        e.preventDefault();
        $popup.find('#jscity').val('');
        $popup.find('#jschangecity').addClass('mDisabled').attr('disabled','disabled');
    });

    // Пролистывание списка городов
    $body.on('click', '.jsRegionArrow', function(){
        var direction = $(this).data('dir'),
            $holder = $popup.find('.jsRegionSlidesHolder'),
            $leftArrow = $popup.find('.jsRegionArrowLeft'),
            $rightArrow = $popup.find('.jsRegionArrowRight'),
            holderWidth = $holder.width(),
            width = $popup.find('.jsRegionOneSlide').width(),
            leftAfterComplete;

        $holder.animate({
            'left' : direction + '=' + width
        }, function(){
            leftAfterComplete = parseInt($holder.css('left'), 10);
            if (leftAfterComplete < 0) $leftArrow.show();
            if (leftAfterComplete == 0) $leftArrow.hide();
            if (width - leftAfterComplete == holderWidth) $rightArrow.hide();
            if (width - leftAfterComplete < holderWidth) $rightArrow.show()
        })

    });

    // Клик по кнопке "Сохранить"
    $body.on('click', '#jschangecity', function submitCityHandler(e) {

        var url = $(this).data('url'),
            inputRegion = $popup.find('#jscity'),
            regionName = inputRegion.val();

        changeRegionAction(regionName, url);

        e.preventDefault();
    });

    $body.on('click', '.jsChangeRegionLink', function(e){
        changeRegionAction($(this).text(), $(this).attr('href'));
        e.preventDefault();
    });

	if (!isGeoshopCookieSet()) {
		openRegionPopup();
	}
}(jQuery));