(function($) {
	var
		pluginCalls = [],
		excludedFromClosingDropboxContainer;

	function isDropboxOpened(options, $container, $content) {
		return (options.htmlClasses.container.opened && $container.hasClass(options.htmlClasses.container.opened)) || (options.useCssOpening && $content.css('display') != 'none');
	}

	function closeAllDropboxes() {
		$.each(pluginCalls, function(key, pluginCall) {
			if (pluginCall.options.cssSelectors.container && (pluginCall.options.htmlClasses.container.opened || pluginCall.options.useCssOpening)) {
				pluginCall.$contexts.find(pluginCall.options.cssSelectors.container).each(function() {
					if (this !== excludedFromClosingDropboxContainer) {
						var
							$container = $(this),
							$content = $container.find(pluginCall.options.cssSelectors.content),
							$opener = $container.find(pluginCall.options.cssSelectors.opener),
							isOpened = isDropboxOpened(pluginCall.options, $container, $content);

						if (pluginCall.options.htmlClasses.container.opened) {
							$container.removeClass(pluginCall.options.htmlClasses.container.opened);
						}

						if (pluginCall.options.useCssOpening) {
							$content.hide();
						}

						$opener.removeAttr('tabindex');

						if (pluginCall.options.onClose && isOpened) {
							pluginCall.options.onClose({
								$content: $content
							});
						}
					}
				});
			}
		});

		excludedFromClosingDropboxContainer = null;
	}

	/**
	 * Пример:
	 *
	 * <style type="text/css">
	 *     .dropbox-container { margin-bottom: 20px; border: 1px solid #000; background: #ddd; }
	 *     .dropbox-container.dropbox-opened { border: 2px solid #444; }
	 *     .dropbox-opener { padding-left: 5px; background: #eede90; cursor: pointer; }
	 *     .dropbox-content { display: none; }
	 *     .dropbox-item { padding-left: 5px; border-top: 1px solid #000; cursor: pointer; }
	 * </style>
	 *
	 * <script type="text/javascript">
	 *     $(function() {
	 *         $('body').dropbox({
	 *             cssSelectors: {
	 *                 container: '.dropbox-container',
	 *                 opener: '.dropbox-opener',
	 *                 content: '.dropbox-content',
	 *                 item: '.dropbox-item'
	 *             },
	 *             htmlClasses: {
	 *                 container: {
	 *                     opened: 'dropbox-opened'
	 *                 },
	 *                 item: {
	 *                     hover: 'dropbox-item-hover'
	 *                 }
	 *             },
	 *             onClick: function(event) {
	 *                 alert(event.$item.attr('data-value'));
	 *             }
	 *         });
	 *     });
	 * </script>
	 *
	 * <div class="dropbox-container">
	 *     <div class="dropbox-opener">Dropbox 1</div>
	 *     <ul class="dropbox-content">
	 *         <li class="dropbox-item" data-value="1">Пункт 1</li>
	 *         <li class="dropbox-item" data-value="2">Пункт 2</li>
	 *     </ul>
	 * </div>
	 *
	 * <div class="dropbox-container">
	 *     <div class="dropbox-opener">Dropbox 2</div>
	 *     <ul class="dropbox-content">
	 *         <li class="dropbox-item" data-value="3">Пункт 3</li>
	 *         <li class="dropbox-item" data-value="4">Пункт 4</li>
	 *     </ul>
	 * </div>
	 *
	 * @param {Object}   options
	 * @param {Object}   options.cssSelectors           Селекторы, выбирающие все элементы, которые будут
	 *                                                  обрабатываться данным плагином
	 * @param {String}   options.cssSelectors.container Элементы, внутри которых должны содержаться все другие элементы
	 * @param {String}   options.cssSelectors.opener    Элементы, при клике на которые будут открыты/закрыты элементы options.cssSelectors.content
	 * @param {String}   options.cssSelectors.content   Элементы, которые будут открыты/закрыты
	 * @param {String}   options.cssSelectors.item      Элементы, при клике на которые будет вызываться options.onClick
	 * @param {String}   options.htmlClasses.container.opened Класс, присваивающийся элементам options.cssSelectors.container при открытии
	 * @param {String}   options.htmlClasses.item.hover       Класс, присваивающийся элементам options.cssSelectors.item при переключении на элемент с помощью клавиатуры
	 * @param {Boolean}  options.useCssOpening          Должен ли плагин самостоятельно изменять css свойства элементов options.cssSelectors.content для открытия/закрытия
	 * @param {Boolean}  options.preventDefaultForItemClick
	 * @param {Function} options.onOpen                 Вызывается при открытии выпадающего списка. В качестве аргумента передаётся объект {$content: <jQuery элемент из options.cssSelectors.content>}
	 * @param {Function} options.onClick                Вызывается при выборе элемента из списка. В качестве аргумента передаётся объект {$item: <выбранный jQuery элемент из элементов options.cssSelectors.item>}
	 * @param {Function} options.onClose                Вызывается при закрытии выпадающего списка. В качестве аргумента передаётся объект {$content: <jQuery элемент из options.cssSelectors.content>}
	 */
	$.fn.dropbox = function(options) {
		options = $.extend(true, {
			cssSelectors: {
				container: '',
				opener: '',
				content: '',
				item: ''
			},
			htmlClasses: {
				container: {
					opened: ''
				},
				item: {
					hover: ''
				}
			},
			useCssOpening: true,
			preventDefaultForItemClick: true,
			onClick: null
		}, options);

		pluginCalls.push({
			options: options,
			$contexts: this
		});

		this.each(function() {
			var $context = $(this);

			// Открытие dropbox'а
			if (options.cssSelectors.opener) {
				$context.on('click', options.cssSelectors.opener, function(e) {
					e.preventDefault();

					var $container = $(this).closest(options.cssSelectors.container),
						$content = $container.find(options.cssSelectors.content),
						$opener = $container.find(options.cssSelectors.opener),
						isOpened = isDropboxOpened(options, $container, $content);

					closeAllDropboxes();

					if (!isOpened) {
						if (options.htmlClasses.container.opened) {
							$container.addClass(options.htmlClasses.container.opened);
						}

						if (options.useCssOpening) {
							$content.show();
						}

						$opener.attr('tabindex', 999).focus();

						if (options.onOpen) {
							options.onOpen({
								$content: $content
							});
						}
					}
				});
			}

			// Выбор элемента из выпадающего списка с помощью клавиш вниз, вверх, ввод
			if (options.cssSelectors.opener && options.htmlClasses.item.hover) {
				$context.on('keydown', options.cssSelectors.opener, function(e) {
					var $items = $(options.cssSelectors.item, $(this).closest(options.cssSelectors.container)),
						hoverItemIndex = $items.index($items.filter('.' + options.htmlClasses.item.hover));

					$items.removeClass(options.htmlClasses.item.hover);
					switch (e.keyCode) {
						case 13: // Enter key
							e.preventDefault();
							if (hoverItemIndex >= 0 && hoverItemIndex <= $items.length - 1) {
								$items.eq(hoverItemIndex).click();
							}
							break;
						case 38: // up key
							e.preventDefault();
							$items.eq(hoverItemIndex <= 0 ? $items.length - 1 : hoverItemIndex - 1).addClass(options.htmlClasses.item.hover);
							break;
						case 40: // down key
							e.preventDefault();
							$items.eq(hoverItemIndex >= $items.length - 1 ? 0 : hoverItemIndex + 1).addClass(options.htmlClasses.item.hover);
							break
					}
				});
			}

			// Клик по элементу dropbox'а
			if (options.cssSelectors.item) {
				$context.on('click', options.cssSelectors.item, function(e) {
					if (options.preventDefaultForItemClick) {
						e.preventDefault();
					}

					if (options.onClick) {
						options.onClick({
							$item: $(this)
						});
					}

					closeAllDropboxes();
				});
			}

			// Предотвращение закрытия dropbox'а при клике на dropbox
			if (options.cssSelectors.container) {
				$context.on('click', options.cssSelectors.container, function(e) {
					excludedFromClosingDropboxContainer = e.currentTarget;
				});
			}

			if (pluginCalls.length == 1) {
				// Закрытие dropbox'а при клике на любое место страницы или нажатии на Esc
				$(document).on('click keyup', function(e) {
					if (
						e.originalEvent // Только если событие было вызвано пользователем, а не програмно
						&& (e.type == 'click' || (e.type == 'keyup' && e.keyCode == 27))
					) {
						closeAllDropboxes();
					}
				});
			}
		});
	};
})(jQuery);