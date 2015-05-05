;(function($){

	var $body = $(document.body),
		$nav = $('nav'),
        $hamburgerIcon = $('.jsHamburgerIcon'),
		MenuStorage, storage, fillRecommendBlocks, hideMenuTimeoutId;

	/**
	 * Конструктор хранилища данных. Возвращает либо lscache, либо объект с необходимыми свойствами (функциями)
	 * @return {*}
	 * @link https://github.com/pamelafox/lscache
	 * @constructor
	 */
	MenuStorage = function MenuStorageF() {
		var cKey = 'cachedData';
		if (lscache && typeof lscache.supported == 'function' && lscache.supported()) {
			return lscache
		} else {
			return {
				'set': function(key, value, time, $el){
					$el.data(cKey, value);
				},
				'get': function(key, $el) {
					return $el.data(cKey) ? $el.data(cKey) : false;
				},
				'remove': function(key, $el) {
					$el.data(cKey, false);
				}
			}
		}
	};

	/**
	 * Заполнение блоков меню "товарами дня"
	 * @param $el
	 * @param blocks
	 */
	fillRecommendBlocks = function fillRecommendBlocksF($el, blocks) {

		var $containers = $el.find('.jsMenuRecommendation');

		$.each(blocks, function(i, block) {
			try {
				if (!block.categoryId) return;
				var $container = $containers.filter('[data-parent-category-id="' + block.categoryId + '"]');
				$container.html(block.content);
			} catch (e) {
				console.error(e);
			}
		});
	};

	// объект универсального хранилища для данных "товар дня"
	storage = new MenuStorage();

	// Simple lazy loading
	$nav.on('mouseenter', '.navsite2_i', function(){
		$(this).find('.menuImgLazy').each(function(){
			$(this).attr('src', $(this).data('src'))
		});
	});

	// Товар дня
	$nav.on('mouseenter', '.navsite_i', function(){

		var	$el = $(this),
			url = $el.data('recommendUrl'),
			lKey = 'xhrLoading', // ключ для предотвращения дополнительного запроса на загрузку данных
			cacheTime = 10, // время кэширования в localstorage (в минутах)
			key, xhr;

		if (typeof url == 'string' && !$el.data(lKey) === true) {

			// отрезаем от url параметры для ключа в localstorage
			key = url.indexOf('?') === -1 ? url : url.substring(0, url.indexOf('?'));

			if (!storage.get(key, $el)) {

				xhr = $.get(url);
				$el.data(lKey, true);

				xhr.done(function(response) {
					var data = response.productBlocks;
					if (!data) return;
					storage.set(key, data, cacheTime, $el);
					fillRecommendBlocks($el, data);
				}).fail(function() {
					storage.remove(key, $el);
				}).always(function(){
					$el.data(lKey, false)
				});
			} else {
				fillRecommendBlocks($el, storage.get(key, $el));
			}

		}
	});

	// аналитика
	$body.on('click', '.jsRecommendedItemInMenu', function(event) {

		event.stopPropagation();

		try {

			var $el = $(this),
				link = $el.attr('href'),
				isNewWindow = $el.attr('target') == '_blank',
				sender = $el.data('sender');

			$body.trigger('TLT_processDOMEvent', [event]);

			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Перешел на карточку товара',
				label: sender ? sender.position : null,
				hitCallback: isNewWindow ? null : function(){

					if (link) {
						setTimeout(function() { window.location.href = link; }, 90);
					}
				}
			});

			$el.trigger('TL_recommendation_clicked');

		} catch (e) { console.error(e); }
	});

    $body.on('click', '.jsHamburgerIcon', function(){
        $nav.toggleClass('show');
    });

    // if ($hamburgerIcon.length > 0) {
    //     $hamburgerIcon.hover(function(){
    //         clearTimeout(hideMenuTimeoutId);
    //         hideMenuTimeoutId = null;
    //         $nav.show();
    //     });
    //     $body.on('hover', 'div', function(e){
    //         var $target;
    //         if ($nav.is(':visible') && !hideMenuTimeoutId) {
    //             $target = $(e.target);
    //             if ($target.closest('nav').length == 0
    //                 && $target.prop('nodeName') != 'NAV'
    //                 && !$target.hasClass('jsHamburgerIcon') ) {
    //                 hideMenuTimeoutId = setTimeout( function(){ $nav.hide() }, 2000 );
    //             }
    //         }
    //     })
    // }

})(jQuery);