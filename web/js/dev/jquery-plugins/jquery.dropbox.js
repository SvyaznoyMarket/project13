(function($) {
	var
		pluginCalls = [],
		excludedFromClosingDropboxContainer;

	function closeAllDropboxes() {
		$.each(pluginCalls, function(key, pluginCall) {
			if (pluginCall.options.htmlClasses.opened || pluginCall.options.useCssOpening) {
				pluginCall.$contexts.find(pluginCall.options.cssSelectors.container).each(function() {
					if (this !== excludedFromClosingDropboxContainer) {
						if (pluginCall.options.htmlClasses.opened) {
							$(this).removeClass(pluginCall.options.htmlClasses.opened);
						}

						if (pluginCall.options.useCssOpening) {
							$(this).find(pluginCall.options.cssSelectors.content).hide();
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
	 *                 opened: 'dropbox-opened'
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
	 * @param {String}   options.htmlClasses.opened     Класс, присваивающийся элементам options.cssSelectors.container при открытии
	 * @param {Boolean}  options.useCssOpening          Должен ли плагин самостоятельно изменять css свойства элементов options.cssSelectors.content для открытия/закрытия
	 * @param {Function} options.onClick                Вызывается при выборе элемента из списка. В качестве аргумента передаётся объект {$item: <выбранный jQuery элемент из элементов options.cssSelectors.item>}
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
				opened: ''
			},
			useCssOpening: true,
			onClick: null
		}, options);

		pluginCalls.push({
			options: options,
			$contexts: this
		});

		this.each(function() {
			var $context = $(this);

			// Открытие dropbox'а
			$context.on('click', options.cssSelectors.opener, function(e) {
				e.preventDefault();

				var $container = $(this).closest(options.cssSelectors.container),
					$content = $container.find(options.cssSelectors.content),
					isOpened = (options.htmlClasses.opened && $container.hasClass(options.htmlClasses.opened)) || (options.useCssOpening && $content.css('display') != 'none');

				closeAllDropboxes();

				if (!isOpened) {
					if (options.htmlClasses.opened) {
						$container.addClass(options.htmlClasses.opened);
					}

					if (options.useCssOpening) {
						$content.show();
					}
				}
			});

			// Клик по элементу dropbox'а
			$context.on('click', options.cssSelectors.item, function(e) {
				e.preventDefault();

				if (options.onClick) {
					options.onClick({
						$item: $(this)
					});
				}

				closeAllDropboxes();
			});

			// Предотвращение закрытия dropbox'а при клике на dropbox
			$context.on('click', options.cssSelectors.container, function(e) {
				excludedFromClosingDropboxContainer = e.currentTarget;
			});

			if (pluginCalls.length == 1) {
				// Закрытие dropbox'а при клике на любое место страницы или нажатии на Esc
				$(document).on('click keyup', function(e) {
					if (e.type == 'click' || (e.type == 'keyup' && e.keyCode == 27)) {
						closeAllDropboxes();
					}
				});
			}
		});
	};
})(jQuery);