;$(function() {
	$('.js-product-3d-html5-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('/maybe3dPlayer/player.min.js').wait(function() {
			$('.js-product-3d-html5-popup').lightbox_me({
				centered: true,
				closeSelector: '.jsPopupCloser',
				onLoad: function() {
					var $popup = $('.js-product-3d-html5-popup');
					Maybe3D.Starter.setModelPathHTML5($popup.data('url'));
					Maybe3D.Starter.embed($popup.data('id'), 'js-product-3d-html5-popup-model');
				},
				onClose: function() {
					$('#js-product-3d-html5-popup-model').empty();
				}
			});
		});
	});

	$('.js-product-3d-swf-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('swfobject.min.js').wait(function() {
			try {
				if (!$('#js-product-3d-swf-popup-model').length) {
					$('.js-product-3d-swf-popup-container').append('<div id="js-product-3d-swf-popup-model"></div>');
				}

				var
					swfId = 'js-product-3d-swf-popup-object',
					$popup = $('.js-product-3d-swf-popup');

				swfobject.embedSWF(
					$popup.data('url'),
					'js-product-3d-swf-popup-model',
					'700px',
					'500px',
					'10.0.0',
					'js/vendor/expressInstall.swf',
					{
						language: 'auto'
					},
					{
						menu: 'false',
						scale: 'noScale',
						allowFullscreen: 'true',
						allowScriptAccess: 'always',
						wmode: 'direct'
					},
					{
						id: swfId
					}
				);

				$popup.lightbox_me({
					centered: true,
					closeSelector: '.jsPopupCloser',
					onClose: function() {
						swfobject.removeSWF(swfId);
					}
				});
			}
			catch (err) {}
		});
	});

	// 3D для мебели
	$('.js-product-3d-img-opener').bind('click', function(e) {
		e.preventDefault();

		$LAB.script('DAnimFramePlayer.min.js').wait(function() {
			var
				$element = $('.js-product-3d-img-popup'),
				data = $element.data('value');

			try {
				if (!$('#js-product-3d-img-container').length) {
					(new DAnimFramePlayer($element[0])).DoLoadModel(data);
				}

				$element.lightbox_me({
					centered: true,
					closeSelector: '.jsPopupCloser'
				});
			}
			catch (err) {}
		});
	});
});
/* ========================================================================
 * Bootstrap: scrollspy.js v3.3.4
 * http://getbootstrap.com/javascript/#scrollspy
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // SCROLLSPY CLASS DEFINITION
    // ==========================

    function ScrollSpy(element, options) {
        this.$body          = $(document.body)
        this.$scrollElement = $(element).is(document.body) ? $(window) : $(element)
        this.options        = $.extend({}, ScrollSpy.DEFAULTS, options)
        this.selector       = (this.options.target || '') + ' .nav li > a'
        this.offsets        = []
        this.targets        = []
        this.activeTarget   = null
        this.scrollHeight   = 0

        this.$scrollElement.on('scroll.bs.scrollspy', $.proxy(this.process, this))
        this.refresh()
        this.process()
    }

    ScrollSpy.VERSION  = '3.3.4'

    ScrollSpy.DEFAULTS = {
        offset: 10
    }

    ScrollSpy.prototype.getScrollHeight = function () {
        return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
    }

    ScrollSpy.prototype.refresh = function () {
        var that          = this
        var offsetMethod  = 'offset'
        var offsetBase    = 0

        this.offsets      = []
        this.targets      = []
        this.scrollHeight = this.getScrollHeight()

        if (!$.isWindow(this.$scrollElement[0])) {
            offsetMethod = 'position'
            offsetBase   = this.$scrollElement.scrollTop()
        }

        this.$body
            .find(this.selector)
            .map(function () {
                var $el   = $(this)
                var href  = $el.data('target') || $el.attr('href')
                var $href = /^#./.test(href) && $(href)

                return ($href
                    && $href.length
                    && $href.is(':visible')
                    && [[$href[offsetMethod]().top + offsetBase, href]]) || null
            })
            .sort(function (a, b) { return a[0] - b[0] })
            .each(function () {
                that.offsets.push(this[0])
                that.targets.push(this[1])
            })
    }

    ScrollSpy.prototype.process = function () {
        var scrollTop    = this.$scrollElement.scrollTop() + this.options.offset
        var scrollHeight = this.getScrollHeight()
        var maxScroll    = this.options.offset + scrollHeight - this.$scrollElement.height()
        var offsets      = this.offsets
        var targets      = this.targets
        var activeTarget = this.activeTarget
        var i

        if (this.scrollHeight != scrollHeight) {
            this.refresh()
        }

        if (scrollTop >= maxScroll) {
            return activeTarget != (i = targets[targets.length - 1]) && this.activate(i)
        }

        if (activeTarget && scrollTop < offsets[0]) {
            this.activeTarget = null
            return this.clear()
        }

        for (i = offsets.length; i--;) {
            activeTarget != targets[i]
            && scrollTop >= offsets[i]
            && (offsets[i + 1] === undefined || scrollTop < offsets[i + 1])
            && this.activate(targets[i])
        }
    }

    ScrollSpy.prototype.activate = function (target) {
        this.activeTarget = target

        this.clear()

        var selector = this.selector +
            '[data-target="' + target + '"],' +
            this.selector + '[href="' + target + '"]'

        var active = $(selector)
            .parents('li')
            .addClass('active')

        if (active.parent('.dropdown-menu').length) {
            active = active
                .closest('li.dropdown')
                .addClass('active')
        }

        active.trigger('activate.bs.scrollspy')
    }

    ScrollSpy.prototype.clear = function () {
        $(this.selector)
            .parentsUntil(this.options.target, '.active')
            .removeClass('active')
    }


    // SCROLLSPY PLUGIN DEFINITION
    // ===========================

    function Plugin(option) {
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('bs.scrollspy')
            var options = typeof option == 'object' && option

            if (!data) $this.data('bs.scrollspy', (data = new ScrollSpy(this, options)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.scrollspy

    $.fn.scrollspy             = Plugin
    $.fn.scrollspy.Constructor = ScrollSpy


    // SCROLLSPY NO CONFLICT
    // =====================

    $.fn.scrollspy.noConflict = function () {
        $.fn.scrollspy = old
        return this
    }


    // SCROLLSPY DATA-API
    // ==================

    $(window).on('load.bs.scrollspy.data-api', function () {
        $('[data-spy="scroll"]').each(function () {
            var $spy = $(this)
            Plugin.call($spy, $spy.data())
        })
    })

}(jQuery);
/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
$(function() {
	var $productCardData = $('#jsProductCard');
	if (!$productCardData.length || $('body').data('template') != 'product_card') {
		return;
	}

	var
		product = $productCardData.data('value') || {},
		query = $.deparam((location.search || '').slice(1)),
		reviewsYandexClick = function ( e ) {
			console.log('reviewsYandexClick');
			var
				link = this //, url = link.href
			;

			if ( 'undefined' !==  product.article ) {
				_gaq.push(['_trackEvent', 'YM_link', product.article]);
				e.preventDefault();
				if ( 'undefined' !== link ) {
					setTimeout(function () {
						//document.location.href = url; // не подходит, нужно в новом окне открывать
						link.click(); // эмулируем клик по ссылке
					}, 500);
				}
			}
		};

	ENTER.utils.analytics.productPageSenders.add(product.ui, query.sender);
	ENTER.utils.analytics.productPageSenders2.add(product.ui, query.sender2);

	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}

	try {
		if ('out of stock' === product.stockState) {
			$('body').trigger('trackGoogleEvent', {
				category: 'unavailable_product',
				action: $.map(product.category, function(category) { return category.name; }).join('_'),
				label: product.barcode + '_' + product.article
			});
		}
	} catch (error) { console.error(error); }
});
/**
 * Кредит для карточки товара
 *
 * @author		Kotov Ivan, Zaytsev Alexandr
 * @requires	jQuery, printPrice, docCookies, JsHttpRequest.js
 */
;(function() {

	var creditBoxNode = $('.creditbox');

	if( creditBoxNode.length > 0 ) {

		var	$body = $(document.body),
			priceNode = creditBoxNode.find('.creditbox__sum strong');

		window.creditBox = {

			init: function() {

				var	creditd = $('input[name=dc_buy_on_credit]').data('model'),
					label = creditBoxNode.find('label');

				$('.jsProductCreditRadio').on('change', function() {
					var status = $(this).val(), // 'on'|'off'
						$link = $('.js-WidgetBuy .jsBuyButton');

					if ($link.length > 0) {
                        $link.attr('href', ENTER.utils.setURLParam('credit', status, $link.attr('href')));
                        $link.attr('href', ENTER.utils.setURLParam('sender2', 'credit', $link.attr('href')));
                    }
				});

				if (typeof window.DCLoans == 'function') window.DCLoans(
					'4427',
					'getPayment',
                    {
                        products: [
                            { price : creditd.price, count : 1, type : creditd.product_type }
                        ]
                    },
					function(response) {
                        var result = {
                            payment: null
                        };

                        console.info('DCLoans.getPayment.response', response);

                        result.payment = response.allProducts;

                        console.info('DCLoans.getPayment.result', result);

						if (result.payment) {
							priceNode.html(printPrice(Math.ceil(result.payment)));
							creditBoxNode.show();
						}
					}
				);
			}

		};

		if (ENTER.config.userInfo) {
			if ($.isArray(ENTER.config.userInfo.cart.products)) {
				var product_id = $('#jsProductCard').data('value')['id'];
				$.each(ENTER.config.userInfo.cart.products, function(i, val) {
					if (val['isCredit'] && val['id'] == product_id) {
						$('#creditinput').attr('checked', true).trigger('change')
					}
				})
			}
		}

        $body.on('click', '.jsProductCreditRadio', function(){
            $body.trigger('trackGoogleEvent', ['Credit', 'Выбор опции', 'Карточка товара']);
        });
		
		creditBox.init();
	}
	else {

		var	productDesc = $('.bProductDesc');

		if ( productDesc.length && !productDesc.hasClass('mNoCredit') ) {
			productDesc.addClass('mNoCredit'); // добавим класс, дабы скрыть кредитный чекбокс
		}
	}
}());
/**
 * Открытие окна доставки
 *
 * @author		Zhukov Roman
 * @requires	jQuery, lightbox_me
 *
 */
(function() {

    $('.js-show-shops').on('click', function() {
        var popup = $('.shopsPopup'),
            buyButtons = $('.shopsPopup .jsBuyButton');

        popup.lightbox_me({
            centered: true,
            autofocus: true
        });

        buyButtons.on('click', function(){
            popup.trigger('close')
        });
    });

}());

/**
 * Слайдер изображений товара
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
;(function(){

	/**
	 * Инициализация слайдера
	 *
	 * @param	{Object}	slider		Элемент слайдера
	 * @param	{Object}	fotoBox		Элемент контейнера с фотографиями
	 * @param	{Object}	leftArr		Стрелка влево
	 * @param	{Object}	rightArr	Стрелка вправо
	 * @param	{Object}	photos		Карточки фотографий
	 * @param	{Number}	itemW		Ширина одной карточки с фотографией
	 * @param	{Number}	nowLeft		Текущий отступ слева
	 */
	var initFotoSlider = function(){
		var slider = $('.js-photoslider');
		var fotoBox = slider.find('.js-photoslider-gal');
		var leftArr = slider.find('.js-photoslider-btn-prev');
		var rightArr = slider.find('.js-photoslider-btn-next');
		var photos = fotoBox.find('.js-photoslider-gal-i');

		if (!photos.length){
			return false;
		}

		var itemW = photos.width() + parseInt(photos.css('marginLeft'),10) + parseInt(photos.css('marginRight'),10);
		var nowLeft = 0;

		fotoBox.css({'width': photos.length*itemW, 'left':nowLeft});
		/**
		 * Проверка стрелок
		 */
		var checkArrow = function(){
			if (nowLeft > 0){
				leftArr.show();
			}
			else {
				leftArr.hide();
			}

			if (nowLeft < fotoBox.width()-slider.width()){
				rightArr.show();
			}
			else {
				rightArr.hide();
			}
		};

		/**
		 * Предыдущее фото
		 */
		var prevFoto = function(){
			nowLeft = nowLeft - itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		/**
		 * Следущее фото
		 */
		var nextFoto = function(){
			nowLeft = nowLeft + itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		checkArrow();

		leftArr.bind('click', prevFoto);
		rightArr.bind('click', nextFoto);
	};

	$(document).ready(function() {
		if ( $('.js-photoslider').length){
			initFotoSlider();
		}
	});
})();
/**
 * Подсказки к характеристикам
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
(function(){
	var hintShower = function(){
		var hintPopup = $('.bHint_ePopup');
		var hintLnk = $('.bHint_eLink');
		var hintCloseLnk = $('.bHint_ePopup .close');

		var hintShow = function(){
			hintPopup.hide();
			$(this).parent().find('.bHint_ePopup').fadeIn(150);
			return false;
		};

		var hintClose = function(){
			hintPopup.fadeOut(150);
			return false;
		};


		hintLnk.bind('click', hintShow);

		hintCloseLnk.bind('click', hintClose);
	};


	$(document).ready(function() {
		if ($('.bHint').length){
			hintShower();
		}
	});
}());
/**
 * Подписка на снижение цены
 */
;$(function() {
	var $opener = $('.js-lowPriceNotifier-opener');
	if ($opener.length) {
		var
			data = $('.js-lowPriceNotifier').data('values'),
			$popup,
			$email,
			$error,
			$subscribe;

		/**
		 * Показать окно подписки на снижение цены
		 */
		function showPopup(e) {
			e.preventDefault();

			if (!$popup) {
				$opener.after($(Mustache.render($('#tpl-lowPriceNotifier-popup').html(), {
					price: data.price,
					userOfficeUrl: data.userOfficeUrl,
					actionChannelName: data.actionChannelName,
					isSubscribedToActionChannel: ENTER.config.userInfo.user.isSubscribedToActionChannel,
					showUserEmailNotify: ENTER.config.userInfo.user.isLogined && !ENTER.config.userInfo.user.email,
					userEmail: ENTER.config.userInfo.user.email
				})));

				$popup = $('.js-lowPriceNotifier-popup');
				$email = $('.js-lowPriceNotifier-popup-email');
				$error = $('.js-lowPriceNotifier-popup-error');
				$subscribe = $('.js-lowPriceNotifier-popup-subscribe');

				$('.js-lowPriceNotifier-popup-submit').on('click', submit);
				$popup.find('.js-lowPriceNotifier-popup-close').on('click', function(e) {
					e.preventDefault();
					hidePopup();
				});
			}

			$popup.fadeIn(300);
		}

		/**
		 * Скрыть окно подписки на снижение цены
		 */
		function hidePopup() {
			$popup.fadeOut(300);
		}

		/**
		 * Отправка данных на сервер
		 */
		function submit(e) {
			e.preventDefault();

			$.get(data.submitUrl + (data.submitUrl.indexOf('?') == -1 ? '?' : '&') + 'email=' + encodeURIComponent($email.val()) + '&subscribe=' + (checkSubscribe() ? 1 : 0), function(res) {
				if ( !res.success ) {
					$email.addClass('red');

					if ( res.error.message ) {
						$error.show().html(res.error.message);
					}

					return false;
				}

				if ($subscribe[0] && $subscribe[0].checked && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'subscription', 'subscribe_price_alert', $email.val()]);
				}

				hidePopup();
				$popup.remove();
				$opener.remove();
			});
		}

		/**
		 * Проверка чекбокса "Акции и суперпредложения"
		 */
		function checkSubscribe() {
			if ($subscribe.length && $subscribe.is(':checked')) {
				return true;
			}

			return false;
		}

		$opener.on('click', showPopup);
	}
});
;+function($){

    var $body = $(document.body),
        $imgPopup = $body.find('.jsProductImgPopup'),
        $popupPhoto = $body.find('.jsProductPopupBigPhoto'),
        $popupPhotoHolder = $('.jsProductPopupBigPhotoHolder'),
        $popupPhotoThumbs = $('.jsPopupPhotoThumb'),
        $productPhotoThumb = $('.jsProductPhotoThumb'),
        $productPhotoThumbs = $('.jsProductThumbList'),
        $zoomBtn   = $('.jsProductPopupZoom'),
        $popupThumbs = $('.jsPopupThumbList'),
        thumbActiveClass = 'product-card-photo-thumbs__i--act',
        thumbsCount = $popupPhotoThumbs.length,
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            preventScroll: true,
            closeClick: true
        },
        /* Проверка возможности увеличения изображения товара */
        checkZoom = function(){
            var newImage, initWidth, initHeight, result;

            // Получаем реальные размеры изображения
            newImage = new Image();
            newImage.src = $popupPhoto.attr("src");

            initWidth = newImage.width;
            initHeight = newImage.height;

            result = initWidth < $popupPhotoHolder.width() && initHeight < $popupPhotoHolder.height();

            if (result) {
                $zoomBtn.addClass('disabled');
            }

            return !result;

        },
        /* Функция для зума фотографии */
        setZoom = function(direction) {
            var dataZoom = $popupPhoto.data('zoom'),
                newImage, initHeight, initWidth;

            if (typeof dataZoom == 'undefined') {
                if (direction < 0) return;
                else $popupPhoto.data('zoom', direction);
            } else if (dataZoom == 0 && direction < 0) {
                return;
            } else {
                $popupPhoto.data('zoom', dataZoom + direction)
            }

            // Получаем реальные размеры изображения
            newImage = new Image();
            newImage.src = $popupPhoto.attr("src");

            initWidth = newImage.width;
            initHeight = newImage.height;

            // нажали плюс и размеры картинки больше контейнера
            if ( direction > 0 && ( initWidth > $popupPhotoHolder.width() || initHeight > $popupPhotoHolder.height() ) ) {
                $popupPhoto
                    .removeClass('fixed')
                    .css({'max-height' : initHeight, 'max-width' : initWidth});

                var
                    parentOffset       = $popupPhotoHolder.offset(),
                    parentOffsetHeight = $popupPhotoHolder.height(),
                    parentOffsetWidth  = $popupPhotoHolder.width(),
                    imgWidth           = $popupPhoto.width(),
                    imgHeight          = $popupPhoto.height(),
                    right              = parentOffset.left,
                    bottom             = parentOffset.top,
                    left, top;

                if ( imgWidth > parentOffsetWidth ) {
                    left = parentOffsetWidth - imgWidth + right;
                } else {
                    left = 0;
                }

                if ( imgHeight > parentOffsetHeight ) {
                    top = parentOffsetHeight - imgHeight + bottom;
                } else {
                    top = 0;
                }

                $popupPhoto.draggable({
                    containment: [left, top, right, bottom],
                    scroll: false
                });
            }

            // нажали минус
            if ( direction < 0) {
                setDefaultSetting();
            }
        },

        // начальные установки для блока большого изображения
        setDefaultSetting = function() {
            $popupPhoto.addClass('fixed');
            $popupPhoto.css({'max-height' : '100%', 'max-width' : '100%', 'top' : 0, 'left' : 0}); // fix при установке в 0
            $popupPhoto.data('zoom', 0);
            $zoomBtn.removeClass('disabled');
            $('.jsProductPopupZoomOut').addClass('disabled');
            if ( $popupPhoto.hasClass('ui-draggable') ) {
                $popupPhoto.draggable('destroy')
            }
        },

        setPhoto = function(index) {
            // отмечаем активным классом thumb
            $popupPhotoThumbs.removeClass(thumbActiveClass).eq(index).addClass(thumbActiveClass);
            // меняем картинку
            $popupPhoto.attr('src', $popupPhotoThumbs.eq(index).data('big-img')).css({'max-height' : '100%', 'max-width' : '100%', 'top' : 0, 'left' : 0});
            setDefaultSetting();
        };

    /* Клик по фото в карточке товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        var $activeThumb = $('.' + thumbActiveClass);
        // устанавливаем большую картинку
        $imgPopup.find('.jsProductPopupBigPhoto').attr('src', $activeThumb.data('big-img'));
        // активируем thumb в попапе
        $popupPhotoThumbs.removeClass(thumbActiveClass)
            .eq($activeThumb.index()).addClass(thumbActiveClass);
        // и открываем popup
        $imgPopup.enterLightboxMe({
            centered: false,
            closeSelector: '.jsPopupCloser',
            modalCSS: {top: '0', left: '0'},
            closeClick: true,
			preventScroll: true,
            onLoad: function() {
                checkZoom();
                if ($popupPhotoThumbs.length > 11) {
                    $popupThumbs.slick(
                        {
                            prevArrow: '.product-card-photo-thumbs__btn--l.jsPopupThumbBtn',
                            nextArrow: '.product-card-photo-thumbs__btn--r.jsPopupThumbBtn',
                            infinite: false,
                            slidesToShow: 11,
                            slidesToScroll: 11
                        }
                    );
                }
            },
            onClose: function() {
                setDefaultSetting();
            }
        });

        $(window).on('resize', function() {
            setDefaultSetting();
        });
    });

    /* Меняем большое изображение в popup при клике на миниатюру */
    $body.find('.jsProductPhotoThumb').on('click', function(){
        var $this = $(this);
        $this.siblings().removeClass('product-card-photo-thumbs__i--act');
        $this.addClass('product-card-photo-thumbs__i--act');
        $body.find('.jsProductMiddlePhoto').attr('src', $this.data('middle-img'));
    });

    // /* Зум в попапе */
    $body.on('click', '.jsProductPopupZoom', function(){
        var
            $this     = $(this),
            direction = parseInt($(this).data('dir'), 10);

        if (checkZoom()) {
            $zoomBtn.removeClass('disabled');
            $this.addClass('disabled');
            setZoom(direction);
        }
    });

    /* Слайд в попапе */
    $body.on('click', '.jsProductPopupSlide', function(){

        var direction = $(this).data('dir'),
            curIndex = $popupPhotoThumbs.index($imgPopup.find('.'+thumbActiveClass)),
            max = $popupPhotoThumbs.length - 1 ,
            photoIndex = (curIndex + direction == thumbsCount) ? 0 : curIndex + direction;

        (photoIndex == -1) && (photoIndex = max);

        setPhoto(photoIndex);

        if ($popupPhotoThumbs.length > 11) { $popupThumbs.slick('slickGoTo', photoIndex); }
    });

    $popupPhotoThumbs.on('click', function(){
        setPhoto($popupPhotoThumbs.index($(this)));
    });


    // Youtube и 3D
    $body.on('click', '.jsProductMediaButton li', function(e){
        var $popup = $(e.target).next(),
            $iframe = $popup.find('iframe'),
            src = $iframe.data('src'),
            $3dContainer = $popup.find('.jsProduct3DContainer'),
            $3DJSONContainer = $popup.find('.jsProduct3DJSON');

        // Загружаем видео только при открытии попапа
        if (src) $iframe.attr('src', src);

        if ($3dContainer.length == 0 && $3DJSONContainer.length == 0) {
            // Видео
            $popup.lightbox_me($.extend(popupDefaults, {
                destroyOnClose: true,
                onClose: function(){
                    $iframe.removeAttr('src');
                    $(e.target).parent().append($popup.clone().hide()); // Возвращаем всё на место
                }
            }))
        } else {
            // 3D
            if ($3dContainer.data('type') == 'swf') {
                $LAB.script('swfobject.min.js').wait(function() {
                    var id = 'js-product-3d-swf-popup-object';

                    swfobject.embedSWF(
                        $3dContainer.data('url'),
                        'js-product-3d-swf-popup-model', '700px', '500px', '10.0.0', 'js/vendor/expressInstall.swf',
                        { language: 'auto' },
                        {
                            menu: 'false',
                            scale: 'noScale',
                            allowFullscreen: 'true',
                            allowScriptAccess: 'always',
                            wmode: 'direct'
                        },
                        { id: id }
                    );

                    $popup.lightbox_me($.extend(popupDefaults, {
                        onClose: function() {
                            $(e.target).parent().append($popup.clone().hide());
                        }
                    }))

                });
            } else if ($3DJSONContainer.length > 0) {
                $LAB.script('DAnimFramePlayer.min.js').wait(function() {
                    var data = $3DJSONContainer.data('value');

                    try {
                        if (!$('#js-product-3d-img-container').length) {
                            (new DAnimFramePlayer($3DJSONContainer[0])).DoLoadModel(data);
                        }

                        $popup.lightbox_me($.extend(popupDefaults, {
                            onClose: function() {
                                $(e.target).parent().append($popup.clone().hide());
                            }
                        }));
                    }
                    catch (err) {
                        console.error(err)
                    }
                });
            }
        }
    });
    //slick.js
    if ($productPhotoThumb.length > 5){
        $productPhotoThumbs.slick(
            {
                prevArrow: '.product-card-photo-thumbs__btn--l.jsProductThumbBtn',
                nextArrow: '.product-card-photo-thumbs__btn--r.jsProductThumbBtn',
                infinite: false,
                slidesToShow: 5,
                slidesToScroll: 5
            }
        );
    }


}(jQuery);
/*
* Новая карточка товара
* */
;(function($){

    var $window = $(window),
        $body = $(document.body),
        $creditButton = $body.find('.jsProductCreditButton'),
        $reviewFormStars = $('.jsReviewFormRating'),
        $userbar = $('.js-topbar-fixed'),
        $tabs = $('.jsProductTabs'),
        $epFishka = $('.js-pp-ep-fishka'),
        tabsOffset,// это не очень хорошее поведение, т.к. при добавлении сверху элементов (AJAX, например) offset не изменяется
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            closeClick: true
        };

    /* Если это не новая карточка, то do nothing */
    if (!$body.hasClass('product-card-new')) return;

    tabsOffset = $tabs.offset().top;


    // Кредит
    if ($creditButton.length > 0 && typeof window['DCLoans'] == 'function') {
        window['DCLoans'](
            '4427',
            'getPayment',
            {
                products: [
                    { price : $creditButton.data('credit')['price'], count : 1, type : $creditButton.data('credit')['product_type'] }
                ]
            },
            function(response) {
                var result = {
                    payment: null
                };

                console.info('DCLoans.getPayment.response', response);

                result.payment = response.allProducts;

                console.info('DCLoans.getPayment.result', result);

                if (result.payment) {
                    $creditButton.find('.jsProductCreditPrice').text(printPrice(Math.ceil(result.payment)));
                    $creditButton.show();
                }
            }
        );

        $creditButton.on('click', function(e) {
            var $target = $($(this).data('target')); // кнопка купить

            if ($target.length) {
                console.info('$target.first', $target.first());
                $target.first().trigger('click', ['on']);

                e.preventDefault();
            }
        });
    }

	(function() {
		function addReview() {
			var $reviewForm = $('.jsReviewForm2');
			var user = ENTER.config.userInfo.user;
			if (user.name) $('[name=review\\[author_name\\]]').val(user.name.slice(0,19));
			if (user.email) $('[name=review\\[author_email\\]]').val(user.email);
			$reviewForm.lightbox_me($.extend(popupDefaults, {
				onLoad: function() {},
				onClose: function() {
					$reviewForm.find('.form-ctrl__textarea--err, .form-ctrl__input--err').removeClass('form-ctrl__textarea--err form-ctrl__input--err')
				}
			}));
		}

		// Добавление отзыва
		$body.on('click', '.jsReviewAdd', function(){
			addReview();
		});

		if ('#add-review' == location.hash) {
			addReview();
		}
	})();

    // Отзывы
    $body.on('click', '.jsShowMoreReviews', function(){
        var productUi = $(this).data('ui'),
            totalNum = $(this).data('total-num'),
            $hiddenReviews = $('.jsReviewItem:hidden'),
            currentCount;
        if ($hiddenReviews.length > 0) {
            $hiddenReviews.show();
            if ($('.jsReviewItem').length == totalNum) $('.jsShowMoreReviews').hide();
        } else {
            currentCount = $('.jsReviewItem').length;
            $.ajax(
                '/product-reviews/' + productUi, {
                    data: {
                        page: currentCount / 10,
                        numOnPage: 10
                    }
                }
            ).done(function(data){
                    if (data.content) {
                        $('.jsReviewsList').append(data.content);
                        if ($('.jsReviewItem').length == totalNum) $('.jsShowMoreReviews').hide();
                    }
                });
        }
    });

    // Плавающая навигация ala scrollspy
    if ($tabs.length) {
        $window.on('scroll', function(){
            var fixedClass = 'pp-fixed';
            if ($window.scrollTop() - 100 > tabsOffset) {
                $tabs.addClass(fixedClass);
                $epFishka.addClass('fadeIn');
                $userbar.addClass(fixedClass);
            } else {
                $tabs.removeClass(fixedClass);
                $epFishka.removeClass('fadeIn');
                $userbar.removeClass(fixedClass);
            }
        });
    }

    $body.scrollspy({ target: '#jsScrollSpy', offset: 120 });

    $body.on('click', '.jsProductTabs a', function(e) {
        var hash = $(this).attr('href');
        e.preventDefault();
        window.location.hash = hash;
        window.scrollTo(0, $(hash).offset().top - 105);
    });

    $body.on('click', '.jsOneClickButton', function(){
        $('.jsProductPointsMap').trigger('close');
    });

    $body.on('click', '.jsShowDeliveryMap', function(){

        var productId = $(this).data('product-id'),
            productUi = $(this).data('product-ui'),
            $div = $('.jsProductPointsMap');

        // Если нет пунктов самовывоза
        if ($('.jsDeliveryPickupAvailable').length == 0) return;

        // Если точки были загружены, то просто показываем этот div
        if ($div.find('.jsDeliveryMapPoints').length > 0) {
            $div.lightbox_me({
                centered: true,
                preventScroll: true
            });
            return ;
        }

        if ($div.data('xhr')) return;

        $.ajax('/ajax/product/map/' + productId + '/' + productUi, {
            dataType: 'json',
            beforeSend: function(){
                $div.data('xhr', true);
                $div.lightbox_me({
                    centered: true,
                    preventScroll: true
                })
            }
        }).always(function(){
            $div.data('xhr', false)
        }).done(function(data){

            if (!data.success) {
                console.error('product/map response: %s', data.error);
            } else {
                var $mapContainer = $(data.html),
                    mapData = $.parseJSON($mapContainer.find('.jsMapData').html()),
                    mapDivId = $mapContainer.find('.js-order-map').first().attr('id'),
                    yMap, pointsModel;

                $div.html(data.html);

                $body.on('click', '.jsCloseFl', function(){
                    $div.trigger('close');
                });

                if (mapData) {

                    yMap = new ymaps.Map(mapDivId, {
                        center: [mapData.latitude, mapData.longitude],
                        zoom: mapData.zoom,
                        controls: ['zoomControl', 'fullscreenControl', 'geolocationControl', 'typeSelector']
                    },{
                        autoFitToViewport: 'always',
                        suppressMapOpenBlock: true
                    });

                    yMap.controls.remove('searchControl');

                    yMap.geoObjects.removeAll();
                    yMap.container.fitToViewport();

                    pointsModel = new ENTER.DeliveryPoints(mapData.points, yMap);

                    // Эвент на изменение размера карты (для фильтрации точек)
                    yMap.events.add('boundschange', function (event) {
                        var bounds;
                        if (event.get('newBounds')) {
                            bounds = event.get('target').getBounds();
                            pointsModel.latitudeMin(bounds[0][0]);
                            pointsModel.latitudeMax(bounds[1][0]);
                            pointsModel.longitudeMin(bounds[0][1]);
                            pointsModel.longitudeMax(bounds[1][1]);
                        }
                    });

                    // добавляем видимые точки на карту
                    $.each(mapData.points, function(i, point){
                        try {
                            yMap.geoObjects.add(new ENTER.Placemark(point, true, 'jsOneClickButton'));
                        } catch (e) {
                            console.error('Ошибка добавления точки на карту', e);
                        }
                    });

                    if (yMap.geoObjects.getLength() === 1) {
                        yMap.setCenter(yMap.geoObjects.get(0).geometry.getCoordinates(), 15);
                        yMap.geoObjects.get(0).options.set('visible', true);
                    } else {
                        yMap.setBounds(yMap.geoObjects.getBounds());
                        // точки становятся видимыми только при увеличения зума
                        yMap.events.once('boundschange', function(event){
                            if (event.get('oldZoom') < event.get('newZoom')) {
                                yMap.geoObjects.each(function(point) { point.options.set('visible', true)})
                            }
                        })
                    }

                    ko.applyBindings(pointsModel, $div[0]);


                    $body.on('click', '.jsOrderV3Dropbox',function(){
                        $(this).siblings().removeClass('opn').find('.jsOrderV3DropboxInner').hide(); // скрываем все, кроме потомка
                        $(this).find('.jsOrderV3DropboxInner').toggle(); // потомка переключаем
                        $(this).hasClass('opn') ? $(this).removeClass('opn') : $(this).addClass('opn');
                    });

                }
            }

        })

    });

    $('.js-slider-2').goodsSlider({
        leftArrowSelector: '.goods-slider__btn--prev',
        rightArrowSelector: '.goods-slider__btn--next',
        sliderWrapperSelector: '.goods-slider__inn',
        sliderSelector: '.goods-slider-list',
        itemSelector: '.goods-slider-list__i',
        categoryItemSelector: '.js-product-accessoires-category',
        //pageTitleSelector: '.slideItem_cntr',
        onLoad: function(goodsSlider) {
            ko.applyBindings(ENTER.UserModel, goodsSlider);

            // Для табов в новой карточке товара
            if ($(goodsSlider).data('position') == 'ProductSimilar') $('.jsSimilarTab').show();
        }
    });

    $reviewFormStars.on('click', function(){
        var activeClass = 'popup-rating__i--fill',
            rate = $(this).index();
        $reviewFormStars.removeClass(activeClass);
        $(this).addClass(activeClass).prevAll().addClass(activeClass);
        $('#reviewFormRating').val(++rate)
    });

    // отправка отзыва
    $('#reviewForm').on('submit', function(e){
        var $form = $(this),
            textareaErrClass = 'form-ctrl__textarea--err',
            inputErrClass = 'form-ctrl__input--err',
            textareaLblErrClass = 'form-ctrl__textarea-lbl--err';
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(data){
                if (data.error) {
                    console.log('errors in review form', data);
                    $.each(data.form.error, function(i,val){
                        var $field = $form.find('[name="review['+ val.field +']"]');
                        $field.removeClass(textareaErrClass).removeClass(inputErrClass); // снимаем ошибки
                        if (val.message) {
                            if ($field.is('textarea')) {
                                $field.addClass(textareaErrClass);
                                $field.siblings('.' + textareaLblErrClass).show();
                            }
                            if ($field.is('input')) $field.addClass(inputErrClass);
                        } else {
                            $field.siblings('.' + textareaLblErrClass).hide();
                        }
                    });
                } else if (data.success) {
                    var $successDiv = $('.jsReviewSuccessAdd');
                    $('.jsReviewFormInner').hide();
                    $('.jsReviewForm2').animate({'height': $successDiv.height()});
                    $successDiv.fadeIn();
                }
            },
            complete: function(data) {
                //console.log('complete', data);
            }
        });
    });

    $body.on('click', '.jsReviewVote', function(e){
        var $button = $(e.target),
            buttonVote = $button.data('vote'),
            $voteDiv = $(this),
            userVote = $voteDiv.data('user-vote'),
            reviewUi = $voteDiv.closest('.jsReviewItem').data('review-ui'),
            $voteButtons = $voteDiv.find('.jsReviewVoteBtn'),
            activeClass = 'active',
            voteClass = 'voting';

        if ($voteDiv.data('xhr')) return;

        $.ajax(
            '/product-reviews/vote',
            {   type: 'post',
                data: {
                    'review-ui': reviewUi,
                    'vote': userVote == buttonVote ? 0 : buttonVote
                    },
                beforeSend: function() {
                    $button.addClass(voteClass);
                    $voteDiv.data('xhr', true);
                },
                success: function(data){
                    if (data.success) {
                        $voteDiv.data('user-vote', data.vote);
                        $voteDiv.find('.jsReviewVoteBtn').removeClass(activeClass);
                        if (data.vote != 0) $voteButtons.eq(data.vote == 1 ? 0 : 1).addClass(activeClass);
                        if (typeof data.positive != 'undefined') $voteButtons.eq(0).text(data.positive);
                        if (typeof data.negative != 'undefined') $voteButtons.eq(1).text(data.negative);

                    } else {
                        console.error('Ошибка голосования: %s', data.error)
                    }
                }
            }).always(function(){
                $button.removeClass(voteClass);
                $voteDiv.data('xhr', false);
            })
    });

    // Оферта партнера
    $('.jsProductPartnerOffer').on('click', function(e){
        e.preventDefault();
        var link = $(this).attr('href'),
            $offer = $('.jsProductPartnerOfferDiv');

        if (!link) return;
        link = link.replace(/^http:\/\/.*?\//, '/');

        if ($offer.length == 0) {
            $.get(ENTER.utils.setURLParam('ajax', 1, link)).done(function (data) {
                $('<div class="jsProductPartnerOfferDiv partner-offer-popup"></div>')
                    .append($('<i class="closer jsPopupCloser">×</i>'), $('<div class="inn" />').append($('<h1 />').text(data.title || ''), $('<article />').html(data.content || '')))
                    .lightbox_me(popupDefaults)
            });
        } else {
            $offer.lightbox_me(popupDefaults)
        }

    });

    // Таблица размеров в лайтбоксе
    $body.on('click', '.jsImageInLightBox', function(e){
        e.preventDefault();
        var imageLink = $(this).data('href');
        $('<div class="popup popup--normal"><div class="closer jsPopupCloser">×</div> </div>').append($('<img />', { src: imageLink})).lightbox_me({
            destroyOnClose: true,
            closeSelector: ".jsPopupCloser",
            centered: true
        });
    });

    $body.on('click', '.jsProductImgPopup .jsBuyButton', function(){ $(this).closest('.jsProductImgPopup').trigger('close'); });


	$('.js-description-expand.collapsed').on('click', function(){

        $(this).removeClass('collapsed js-description-expand');

    });

    $body.on('click', '.jsProductCardNewLabelInfo', function(){
        $('.jsProductCardNewLabelPopup').toggleClass('info-popup--open');
    });

    //
    $body.on('click', '.jsSubscribeAfterReview', function() {

        if (!$('.js-registerForm-subscribe').is(':checked')) return;
        $.ajax({
            type: "POST",
            url: '/subscribe/create',
            data: { channel: 1, email: $('#reviewFormEmail').val() },
            success: function(data) {
                if (data.success) {
                    $('.jsReviewSuccessJustSubscribed').lightbox_me(popupDefaults);
                } else {
                    $('.jsReviewSuccessSubscribed').lightbox_me(popupDefaults);
                    if (data.code != 910) $('.jsReviewSuccessSubscribed .popup-form-success__txt').text(data.error)
                }
            }
        });
    })

})(jQuery);


/**
 * Обратный счетчик акции
 */
!function() {
    var
        countDownWrapper = $('.js-countdown'),
        countDownOut     = $('.js-countdown-out'),
        expDate          = countDownWrapper.attr('data-expires'),

        getDeclension = function( days ) {
            var
                str      = days + '',
                lastChar = str.slice(-1),
                lastNum  = lastChar * 1;

            if ( lastNum === 0 ) {
                return 'дней';
            } else if ( days > 4 && days < 21 ) {
                return 'дней';
            } else if ( lastNum > 4 && days > 20 ) {
                return 'дней';
            } else if ( lastNum > 1 && lastNum < 5 ) {
                return 'дня';
            }

            return 'день';
        },

        tick = function( opts ) {
            var
                mask = ( opts.days > 0 ) ? 'D ' + getDeclension(opts.days) + ' HH:MM:SS' : 'HH:MM:SS';

            mask = mask.replace(/(D+)/, function( str, d) { return (d.length > 1 && opts.days < 10 ) ? '0' + opts.days : opts.days });
            mask = mask.replace(/(H+)/, function( str, h) { return (h.length > 1 && opts.hours < 10 ) ? '0' + opts.hours : opts.hours });
            mask = mask.replace(/(M+)/, function( str, m) { return (m.length > 1 && opts.minutes < 10 ) ? '0' + opts.minutes : opts.minutes });
            mask = mask.replace(/(S+)/, function( str, s) { return (s.length > 1 && opts.seconds < 10 ) ? '0' + opts.seconds : opts.seconds });

            countDownOut.html(mask);
        },

        countDown;

    try {
        countDown = new CountDown({
            // timestamp: 1445597200000,
            timestamp: expDate * 1000,
            tick: tick
        });
    } catch ( err ) {
        console.warn('Не удалось запустить обратный счетчик акции');
        console.warn(err);
    }

}();
$(document).ready(function() {

    var $productDescriptionToggle = $('#productDescriptionToggle');


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	(function () {
		var
			thumbImageActiveClass = 'prod-photoslider__gal__link--active',
			$image = $('.js-photo-zoomedImg');

		if (!$image.length) {
			console.warn('Нет изображения для elevateZoom');
			return;
		}

		var zoomConfig = {
			$imageContainer: $('.js-product-bigImg'),
			zoomWindowOffety: 0,
			zoomWindowOffetx: 19,
			zoomWindowWidth: $image.data('is-slot') ? 344 : 519,
			borderSize: 1,
			borderColour: '#C7C7C7'
		};

		if ($image.data('zoom-image')) {
			$image.elevateZoom(zoomConfig);
		}

		$('.jsPhotoGalleryLink').on('click', function(e) {
			e.preventDefault();

			var $link = $(e.currentTarget);
			if ($link.hasClass(thumbImageActiveClass)) {
				return;
			}

			$('.jsPhotoGalleryLink').removeClass(thumbImageActiveClass);
			$link.addClass(thumbImageActiveClass);

			if ($image.data('elevateZoom')) {
				$image.data('elevateZoom').destroy();
			}

			if ($link.data('zoom-image')) {
				$image.data('zoom-image', $link.data('zoom-image'));
				$image.one('load', function() {
					$image.elevateZoom(zoomConfig);
				});
			}

			$image.attr('src', $link.data('image'));
		});
	})();


	/**
	 * Каутер товара
	 *
	 * @requires	jQuery, jQuery.goodsCounter
	 * @param		{Number} count Возвращает текущее значение каунтера
	 */
	$('.bCountSection').goodsCounter({
		onChange:function( count ){
			var spinnerFor = this.attr('data-spinner-for'),
				bindButton = $('.'+spinnerFor),
				newHref = bindButton.attr('href') || '';

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));

			// добавление в корзину после обновления спиннера
			// if (bindButton.hasClass('mBought')){
			// 	bindButton.eq('0').trigger('buy');
			// }
		}
	});


	/**
	 * Подключение слайдера товаров
	 */
	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});

	/**
	 * Подключение кастомных дропдаунов
	 */
	$('.bDescSelectItem').customDropDown({
		changeHandler: function( option ) {
			document.location.href = option.data('url');
		}
	});

	// карточка товара - характеристики товара краткие/полные
	if ( $productDescriptionToggle.length ) {
        $productDescriptionToggle.toggle(
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').show();
				$(this).html('Скрыть все характеристики');
			},
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').hide();
				$(this).html('Показать все характеристики');
			}
		);
	}

    try {
        var
            productId =ENTER.config.pageConfig.product ? ENTER.config.pageConfig.product.id : null,
            cookieValue = docCookies.getItem('product_viewed') || '',
            viewed = []
        ;

        if (productId) {
            viewed = cookieValue ? ENTER.utils.arrayUnique(cookieValue.split(',')) : [];
            viewed.push(productId);
            docCookies.setItem('product_viewed', viewed.slice(-20).join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }

});
;(function() {
	var
		$window = $(window),
		$body = $(document.body),
		$reviewWrap = $('.js-reviews-wrapper'),
		$reviewList = $('.js-reviews-list'),
		$moreReviewsButton = $('.js-reviews-getMore'),
		reviewCurrentPage = 0,
		reviewPageCount = $reviewWrap.attr('data-page-count'),
		productUi = $reviewWrap.attr('data-product-ui'),
		avgScore = $reviewWrap.data('avg-score'),
		firstPageAvgScore = $reviewWrap.data('first-page-avg-score'),
		categoryName = $reviewWrap.data('category-name');

	if (!$reviewWrap.length) {
		return;
	}

	$moreReviewsButton.click(function() {
		$.ajax({
			type: 'GET',
			url: ENTER.utils.generateUrl('product.reviews.get', {productUi: productUi}),
			data: {
				page: reviewCurrentPage + 1
			},
			success: function(data) {
				$reviewList.html($reviewList.html() + data.content);

				reviewCurrentPage++;
				reviewPageCount = data.pageCount;

				if (reviewCurrentPage + 1 >= reviewPageCount) {
					$moreReviewsButton.hide();
				} else {
					$moreReviewsButton.show();
				}
			}
		});
	});

	// SITE-5466
	(function() {
		var timer;
		function checkReviewsShowing() {
			var windowHeight = $window.height();
			if ($window.scrollTop() + windowHeight > $reviewWrap.offset().top) {
				if (!timer) {
					timer = setTimeout(function() {
						$window.unbind('scroll', checkReviewsShowing);

						$body.trigger('trackGoogleEvent', {
							category: 'Items_review',
							action: 'All_' + avgScore + '_Top_' + firstPageAvgScore,
							label: categoryName
						});

						ENTER.utils.analytics.reviews.add(productUi, avgScore, firstPageAvgScore, categoryName);
					}, 2000);
				}
			} else {
				if (timer) {
					clearTimeout(timer);
					timer = null;
				}
			}
		}

		$window.scroll(checkReviewsShowing);
		checkReviewsShowing();
	})();
}());



/**
 * Обработчик для формы "Отзыв о товаре"
 *
 * @author		Shaposhnik Vitaly
 */
(function() {
	var body = $('body'),
		reviewPopup = $('.jsReviewPopup'),
		form = reviewPopup.find('.jsReviewForm'),
		submitReviewButton = $('.jsFormSubmit'),
		submitReviewButtonText = submitReviewButton.val(),

		reviewStar = form.find('.stars-list__item'),
		reviewStarCount = form.find('.jsReviewStarsCount'),
		starStateClass = {
			fill: 'star-fill',
			empty: 'star-empty'
		},

		advantageField = $('.jsAdvantage'),
		disadvantageField = $('.jsDisadvantage'),
		extractField = $('.jsExtract'),
		authorNameField = $('.jsAuthorName'),
		authorEmailField = $('.jsAuthorEmail'),

		/**
		 * Конфигурация валидатора для формы "Отзыв о товаре"
		 *
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: advantageField,
					require: true,
					customErr: 'Не указаны достоинства'
				},
				{
					fieldNode: disadvantageField,
					require: true,
					customErr: 'Не указаны недостатки'
				},
				{
					fieldNode: extractField,
					require: true,
					customErr: 'Не указан комментарий'
				},
				{
					fieldNode: authorNameField,
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: authorEmailField,
					require: true,
					customErr: 'Не указан e-mail',
					validBy: 'isEmail'
				}
			]
		},
		validator = new FormValidator(validationConfig);
	//end of vars

	var 
		/**
		 * Открытие окна с отзывами
		 */
		openPopup = function openPopup() {
			reviewPopup.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {}
			});

			return false;
		},

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			var field = $('[name="review[' + formError.field + ']"]');
			// end of vars

			var clearError = function clearError() {
				validator._unmarkFieldError($(this));
			};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		},

		/**
		 * Показ глобальных сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 */
		showError = function showError( msg ) {
			var error = $('ul.error_list', form);
			// end of vars

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
				$( form ).prepend( $('<ul class="error_list" />').append('<li>' + msg + '</li>') );
			}

			return false;
		},

		/**
		 * Обработка ошибок из ответа сервера
		 *
		 * @param {Object} res Ответ сервера
		 */
		serverErrorHandler = function serverErrorHandler( res ) {
			var formError = null;
			// end of vars

			console.warn('Обработка ошибок формы');

			for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];
				console.warn(formError);

				if ( formError.field !== 'global' && formError.message !== null ) {
					formErrorHandler(formError);
				}
				else if ( formError.field === 'global' && formError.message !== null ) {
					showError(formError.message);
				}
			}

			return false;
		},

		/**
		 * Обработчик ответа от сервера
		 *
		 * @param	{Object}	response	Ответ сервера
		 */
		responseFromServer = function responseFromServer( response ) {
			console.log('Ответ от сервера');

			if ( response.error ) {
				console.warn('Form has error');
				serverErrorHandler(response);

				return false;
			}

			if ( response.success ) {
				if (response.notice.message) {
					form.before(response.notice.message);
				}
				form.hide();
			}

			return false;
		},

		/**
		 * Сабмит формы "Отзыв о товаре"
		 */
		formSubmit = function formSubmit() {
			if (form.data('disabled')) {
				return false;
			}

			form.data('disabled', true);
			submitReviewButton.attr('disabled', 'disabled');
			submitReviewButton.addClass('mDisabled');
			submitReviewButton.val('Сохраняю…');

			// очищаем блок с глобальными ошибками
			if ( $('ul.error_list', form).length ) {
				$('ul.error_list', form).html('');
			}

			function enableSubmitReviewButton() {
				form.data('disabled', false);
				submitReviewButton.removeAttr('disabled');
				submitReviewButton.removeClass('mDisabled');
				submitReviewButton.val(submitReviewButtonText);
			}

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					enableSubmitReviewButton();
				},
				onValid: function() {
					$.ajax({
						type: 'post',
						url: form.attr('action'),
						data: form.serializeArray(),
						dataType: 'json',
						success: responseFromServer,
						complete: function() {
							enableSubmitReviewButton();
						}
					});
					console.log('Сабмит формы "Отзыв о товаре"');

					return false;
				}
			});

			return false;
		},

		/**
		 * Закрашивание необходимого количества звезд
		 * 
		 * @param	{Number}	count	Количество звезд которое необходимо закрасить
		 */
		fillStars = function fillStars( count ) {
			reviewStar.removeClass(starStateClass['fill']).removeClass(starStateClass['empty']);

			reviewStar.each(function( index ) {
				if ( index + 1 <= count ) {
					$(this).addClass(starStateClass['fill']);
				}
				else {
					$(this).addClass(starStateClass['empty']);
				}
			});
		},

		/**
		 * Наведение на звезду курсора
		 */
		hoverStar = function hoverStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars

			fillStars(starIndex);			
		},

		/**
		 * Событие сведения курсора со звезды
		 */
		unhoverStar = function unhoverStar() {
			var nowStarCount = reviewStarCount.val();
			// end of vars
			
			fillStars(nowStarCount);
		},

		/**
		 * Нажатие на звезду
		 */
		markStar = function markStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars
			
			reviewStarCount.val(starIndex);
			fillStars(starIndex);
		},

		/**
		 * Заполнение данных пользователя в форме (поля "Ваше имя" и "Ваш e-mail") и скрытие полей.
		 *
		 * @param  {Object} userInfo
		 */
		fillUserData = function fillUserData( userInfo ) {
			if ( userInfo ) {
				// если присутствует имя пользователя
				if ( userInfo.name ) {
					authorNameField.val(userInfo.name);
					authorNameField.parent('.jsPlace2Col').hide();
				}
				// если присутствует email пользователя
				if ( userInfo.email ) {
					authorEmailField.val(userInfo.email);
					authorEmailField.parent('.jsPlace2Col').hide();
				}
				// если присутствует и имя и email пользователя, то скрываем весь fieldset
				if ( userInfo.name && userInfo.email ) {
					authorNameField.parents('.jsFormFieldset').hide();
				}
			}
		};
	//end of functions


	body.on('click', '.jsReviewSend', openPopup);
	body.on('submit', '.jsReviewForm', formSubmit);
	fillUserData(ENTER.config.userInfo.user);

	reviewStar.hover(hoverStar, unhoverStar);
	reviewStar.on('unhover', unhoverStar);
	reviewStar.on('click', markStar);

    if ('#add-review' == location.hash) {
        openPopup();
    }
}());
;$(function() {
	$('.js-product-similarProducts-link').on('click', function() {
		$('body').trigger('trackGoogleEvent', {
			category: 'RR_взаимодействие',
			action: 'Перешел на карточку товара',
			label: 'SEO'
		});
	});
});
/**
 * Видео в карточке товара
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.lightbox_me
 */
;$(function() {
	var $video = $('.js-product-video');
	if (!$video.length || !$('.js-product-video-container').length) {
		return;
	}

	var
		videoStartTime = 0,
		videoEndTime = 0,
		productUrl = document.location.href,
		$iframeContainer = $('.js-product-video-iframeContainer'),
		iframeHtml = $iframeContainer.html();

	$iframeContainer.empty();

	$video.bind('click', function() {
		var $iframeContainer = $('.js-product-video-iframeContainer');
		$iframeContainer.append(iframeHtml);

		var $iframe = $('iframe', $iframeContainer);
		$iframe.attr('src', $iframe.data('src'));

		$('.js-product-video-container').lightbox_me({
			centered: true,
			closeSelector: '.jsPopupCloser',
			onLoad: function() {
				videoStartTime = new Date().getTime();
			},
			onClose: function() {
				$('.js-product-video-iframeContainer').empty();
				videoEndTime = new Date().getTime();
				var videoSpent = videoEndTime - videoStartTime;
			}
		});

		return false;
	});
});