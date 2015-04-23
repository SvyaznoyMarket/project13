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
				data = $element.data('value'),
				host = $element.data('host');

			try {
				if (!$('#js-product-3d-img-container').length) {
					(new DAnimFramePlayer($element[0], host)).DoLoadModel(data);
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

				if (typeof window.dc_getCreditForTheProduct == 'function') dc_getCreditForTheProduct(
					4427,
					window.docCookies.getItem('enter_auth'),
					'getPayment',
					{ price : creditd.price, count : 1, type : creditd.product_type },
					function( result ) {
						if( ! 'payment' in result ){
							return;
						}
						if( result.payment > 0 ) {
							priceNode.html( printPrice( Math.ceil(result.payment) ) );
							creditBoxNode.show();
						}
					}
				);
			}

		};

		$body.on('userLogged', function(e, data) {
			if ($.isArray(data.cartProducts)) {
				var product_id = $('#jsProductCard').data('value')['id'];
				$.each(data.cartProducts, function(i, val) {
					if (val['isCredit'] && val['id'] == product_id) {
						$('#creditinput').attr('checked', true).trigger('change')
					}
				})
			}
		});

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

		var hintAnalytics = function(data){
			if (typeof(_gaq) !== 'undefined') {
				_gaq.push(['_trackEvent', 'Hints', data.hintTitle, data.url]);
			}
		};

		var hintShow = function(){
			hintPopup.hide();
			$(this).parent().find('.bHint_ePopup').fadeIn(150);

			var analyticsData = {
				hintTitle: $(this).html(),
				url: window.location.href
			};
			hintAnalytics(analyticsData);

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
 * Планировщик шкафов купе
 *
 * @requires jQuery, ENTER.utils.logError
 */
;(function( global ) {
	/**
	 * Имя объекта для конструктора шкафов купе
	 *
	 * ВНИМАНИЕ
	 * Имя переменной менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Переменная должна находится в глобальной области видимости
	 */
	global.Planner3dKupeConstructor = null;


	/**
	 * Callback Инициализации конструктора шкафов
	 *
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_Init = function ( ApiIds ) {
		// console.info(ApiIds)
	};


	/**
	 * Callback изменений в конструкторе шкафов
	 * 
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_UpdatePrice = function ( IdsWithInfo ) {
		var url = $('#planner3D').data('cart-sum-url'),
			product = {};
		// end of vars

		product.product = {};

		var authFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsPrice').html(printPrice(res.sum));
		};

		for ( var i = 0, len = IdsWithInfo.length; i < len; i++ ) {
			var prodID = IdsWithInfo[i].id;

			if ( IdsWithInfo[i].error !== '' ) {
				$('.jsBuyButton').addClass('mDisabled');
				$('#coupeError').html('Вставки продаются только парами!').show();

				return false;
			}

			$('.jsBuyButton').removeClass('mDisabled');
			$('#coupeError').hide();

			if ( product.product[prodID+''] !== undefined ) {
				product.product[prodID+''].quantity++;
			}
			else {
				product.product[prodID+''] = {
					id : prodID,
					quantity : 1
				};
			}
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: product,
			success: authFromServer
		});
	};


	/**
	 * Добавление шкафа купе в корзину
	 */
	var kupe2basket = function() {
		if ( $(this).hasClass('mDisabled') ) {
			return false;
		}

		var structure = global.Planner3dKupeConstructor.GetBasketContent(),
			url = $(this).attr('href'),
			product = {};
		// end of vars

		var resFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsBuyButton').html('В корзине').addClass('mBought').attr('href','/cart');

			/* костыль */
			res.product.name = $('.bMainContainer__eHeader-title').html();
			res.product.price = $('.jsPrice').eq('1').html();
			res.product.article = $('.bMainContainer__eHeader-article').html();
			/* */
			
			$('body').trigger('addtocart', [res]);
		};

		product.product = structure;

		$.ajax({
			type: 'POST',
			url: url,
			data: product,
			success: resFromServer
		});

		return false;
	};

	var initPlanner = function() {
		try {
			var coupeInfo = $('#planner3D').data('product');
			
			global.Planner3dKupeConstructor = new DKupe3dConstructor(document.getElementById('planner3D'),'/css/item/coupe_img/','/css/item/coupe_tex/', '/css/item/test_coupe_icons/');
			global.Planner3dKupeConstructor.Initialize('/js/KupeConstructorData.json', coupeInfo.id);
		}
		catch ( err ) {
			var dataToLog = {
					event: 'Kupe3dConstructor error',
					type:'ошибка загрузки Kupe3dConstructor',
					err: err
				};
			// end of vars
			
			global.ENTER.utils.logError(dataToLog);
		}

		$('.jsBuyButton').off();
		$('.jsBuyButton').bind('click', kupe2basket);
	};


	$(document).ready(function() {
		if ( $('#planner3D').length ) {
			$LAB.script( 'KupeConstructorScript.min.js' ).script( 'three.min.js' ).wait(initPlanner);
		}
	});
}(this));
/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin
 */
;(function() {
	var lowPriceNotifer = function lowPriceNotifer() {
		var
			notiferWrapper = $('.priceSale'),
			notiferButton = $('.jsLowPriceNotifer'),
			submitBtn = $('.bLowPriceNotiferPopup__eSubmitEmail'),
			input = $('.bLowPriceNotiferPopup__eInputEmail'),
			notiferPopup = $('.bLowPriceNotiferPopup'),
			error = $('.bLowPriceNotiferPopup__eError'),
			subscribe = $('.jsSubscribe');
		// end of vars

		var
			/**
			 * Скрыть окно подписки на снижение цены
			 */
			lowPriceNitiferHide = function lowPriceNitiferHide() {
				notiferPopup.fadeOut(300);

				return false;
			},

			/**
			 * Авторизованность пользователя
			 * Вызывается событием «userLogged» у body
			 *
			 * @param event
			 * @param userInfo — данные пользователя (если существуют)
			 */
			userLogged = function userLogin( event, userInfo ) {
				if ( userInfo ) {
					if( userInfo.name ) {
						// Если существует имя, значит юзер точно зарегистрирован и его данные получены
						notiferWrapper.show();
					}
					if( userInfo.email ) {
						input.val(userInfo.email);
					}
				}
			},

			/**
			 * Показать окно подписки на снижение цены
			 */
			lowPriceNitiferShow = function lowPriceNitiferShow() {
				notiferPopup.fadeIn(300);
				notiferPopup.find('.close').bind('click', lowPriceNitiferHide);

				return false;
			},

			/**
			 * Обработка ответа от сервера
			 * 
			 * @param	{Object}	res	Ответ от сервера
			 */
			resFromServer = function resFromServer( res ) {
				if ( !res.success ) {
					input.addClass('red');

					if ( res.error.message ) {
						error.show().html(res.error.message);
					}

					return false;
				}

				if (subscribe[0] && subscribe[0].checked && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'subscription', 'subscribe_price_alert', input.val()]);
				}

				lowPriceNitiferHide();
				notiferPopup.remove();
				notiferButton.remove();
			},

			/**
			 * Проверка чекбокса "Акции и суперпредложения"
			 */
			checkSubscribe = function checkSubscribe() {
				if ( subscribe.length && subscribe.is(':checked') ) {
					return true;
				}

				return false;
			},

			/**
			 * Отправка данных на сервер
			 */
			lowPriceNotiferSubmit = function lowPriceNotiferSubmit() {
				var
					submitUrl = submitBtn.data('url');
				// end of vars
				
				submitUrl += encodeURI('?email=' + input.val() + '&subscribe=' + (checkSubscribe() ? 1 : 0));
				$.get( submitUrl, resFromServer);

				return false;
			};
		// end of functions

		
		submitBtn.bind('click', lowPriceNotiferSubmit);
		notiferButton.bind('click', lowPriceNitiferShow);
		$('body').bind('userLogged', userLogged);
	};


	$(document).ready(function() {
		if ( $('.jsLowPriceNotifer').length ){
			lowPriceNotifer();
		}
	});
}());
;+function($){

    var $body = $(document.body),
        $imgPopup = $body.find('.jsProductImgPopup'),
        $popupPhoto = $body.find('.jsProductPopupBigPhoto'),
        $popupPhotoHolder = $('.jsProductPopupBigPhotoHolder'),
        $popupPhotoThumbs = $('.jsPopupPhotoThumb'),
        $productPhotoThumbs = $('.jsProductThumbList'),
        $productPhotoThumbsBtn = $('.jsProductThumbBtn'),
        productPhotoThumbsWidth = $productPhotoThumbs.width() - 2,
        productPhotoThumbsFullWidth = $productPhotoThumbs.get(0) ? $productPhotoThumbs.get(0).scrollWidth : 0,
        thumbActiveClass = 'product-card-photo-thumbs__i--act',
        thumbBtnDisabledClass = 'product-card-photo-thumbs__btn--disabled',
        thumbsCount = $popupPhotoThumbs.length,
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            preventScroll: true,
            closeClick: false
        },
        /* Функция для зума фотографии */
        setZoom = function(direction) {
            var cssInc = direction < 0 ? '+=' : '-=',
                hInc = direction > 0 ? '+=' : '-=',
                dataZoom = $popupPhoto.data('zoom'),
                multiply = 500;

            if (typeof dataZoom == 'undefined') {
                if (direction < 0) return;
                else $popupPhoto.data('zoom', direction);
            } else if (dataZoom == 0 && direction < 0) {
                return;
            } else {
                $popupPhoto.data('zoom', dataZoom + direction)
            }

            $popupPhoto.css('height', hInc + multiply).css('top', cssInc + multiply/2).css('left', cssInc + multiply/2);
            if (dataZoom == 1 && direction < 0) $popupPhoto.css('top', '0px').css('left', '0px'); // fix при установке в 0
        },
        setPhoto = function(index) {
            // отмечаем активным классом thumb
            $popupPhotoThumbs.removeClass(thumbActiveClass).eq(index).addClass(thumbActiveClass);
            // меняем картинку
            $popupPhoto.css('top', '0px').css('left', '0px').css('height', $popupPhotoHolder.height()).data('zoom', 0);
            $popupPhoto.attr('src', $popupPhotoThumbs.eq(index).data('big-img'));
        };

    // Перемещение увеличенной фотографии по движению мыши
    $popupPhotoHolder.on('mousemove mouseleave wheel', function(e){
        var parentOffset = $(this).parent().offset(),
            relX = e.pageX - parentOffset.left,
            relY = e.pageY - parentOffset.top,
            hW = $(this).width(),
            hH = $(this).height(),
            iW = $popupPhoto.width(),
            iH = $popupPhoto.height();

        if (e.type == 'wheel') {
            setZoom(e.originalEvent['wheelDeltaY'] > 0 ? 1 : -1);
            e.stopPropagation(); // иначе будет скролл страницы
        }

        if (typeof $popupPhoto.data('zoom') == 'undefined' || $popupPhoto.data('zoom') == 0) return;

        if (e.type == 'mousemove') $popupPhoto.css('left', relX/hW * (hW - iW)).css('top', relY/hH * (hH - iH));
        if (e.type == 'mouseleave') $popupPhoto.css('left', (hW - iW)/2).css('top', (hH - iH)/2);

    });

    /* Клик по фото в карточке товара */
    $body.on('click', '.jsOpenProductImgPopup', function(){
        var $activeThumb = $('.' + thumbActiveClass);
        // устанавливаем большую картинку
        $imgPopup.find('.jsProductPopupBigPhoto').attr('src', $activeThumb.data('big-img'));
        // активируем thumb в попапе
        $popupPhotoThumbs.removeClass(thumbActiveClass)
            .eq($activeThumb.index()).addClass(thumbActiveClass);
        // и открываем popup
        $imgPopup.lightbox_me(popupDefaults);
    });

    /* Меняем большое изображение в popup при клике на миниатюру */
    $body.find('.jsProductPhotoThumb').on('click', function(){
        var $this = $(this);
        $this.siblings().removeClass('product-card-photo-thumbs__i--act');
        $this.addClass('product-card-photo-thumbs__i--act');
        $body.find('.jsProductMiddlePhoto').attr('src', $this.data('middle-img'));
    });

    /* Зум в попапе */
    $body.on('click', '.jsProductPopupZoom', function(){
        var direction = parseInt($(this).data('dir'), 10);
        setZoom(direction);
    });

    /* Слайд в попапе */
    $body.on('click', '.jsProductPopupSlide', function(){
        var direction = $(this).data('dir'),
            curIndex = $popupPhotoThumbs.index($imgPopup.find('.'+thumbActiveClass));
        if (curIndex + direction == thumbsCount) setPhoto(0);
        else setPhoto(curIndex + direction);
    });

    $popupPhotoThumbs.on('click', function(){
        setPhoto($popupPhotoThumbs.index($(this)));
    });

    $productPhotoThumbsBtn.on('click', function(){
        if (!$productPhotoThumbs.is(':animated'))
        $productPhotoThumbs.animate({
            'margin-left': $(this).data('dir') + productPhotoThumbsWidth
        }, function(){
            var margin = parseInt($productPhotoThumbs.css('margin-left'));
            $productPhotoThumbsBtn.removeClass(thumbBtnDisabledClass);
            if (productPhotoThumbsFullWidth + margin < productPhotoThumbsWidth) $productPhotoThumbsBtn.eq(1).addClass(thumbBtnDisabledClass);
            if (margin > 0) $productPhotoThumbsBtn.eq(0).addClass(thumbBtnDisabledClass);
        });
    });

    // Youtube и 3D
    $body.on('click', '.jsProductMediaButton', function(e){
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
                    var data = $3DJSONContainer.data('value'),
                        host = $3DJSONContainer.data('host');

                    try {
                        if (!$('#js-product-3d-img-container').length) {
                            (new DAnimFramePlayer($3DJSONContainer[0], host)).DoLoadModel(data);
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
        tabsOffset,// это не очень хорошее поведение, т.к. при добавлении сверху элементов (AJAX, например) offset не изменяется
        popupDefaults = {
            centered: true,
            closeSelector: '.jsPopupCloser',
            closeClick: false
        };

    /* Если это не новая карточка, то do nothing */
    if (!$body.hasClass('product-card-new')) return;

    tabsOffset = $tabs.offset().top;


    // Кредит
    if ($creditButton.length > 0 && typeof window['dc_getCreditForTheProduct'] == 'function') {
        window['dc_getCreditForTheProduct'](
            4427,
            window.docCookies.getItem('enter_auth'),
            'getPayment',
            { price : $creditButton.data('credit')['price'], count : 1, type : $creditButton.data('credit')['product_type'] },
            function( result ) {
                if( typeof result['payment'] != 'undefined' && result['payment'] > 0 ) {
                    $creditButton.find('.jsProductCreditPrice').text( printPrice( Math.ceil(result['payment']) ) );
                    $creditButton.show();
                }
            }
        )
    }

    // Добавление отзыва
    $body.on('click', '.jsReviewAdd', function(){
        $('.jsReviewForm2').lightbox_me($.extend(popupDefaults, {
            onLoad: function() {},
            onClose: function() {}
        }));
    });

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
            if ($window.scrollTop() - 110 > tabsOffset) {
                $tabs.addClass(fixedClass);
                $userbar.addClass(fixedClass);
            } else {
                $tabs.removeClass(fixedClass);
                $userbar.removeClass(fixedClass);
            }
        });
    }

    $body.scrollspy({ target: '#jsScrollSpy', offset: 120 });

    $body.on('click', '.jsProductTabs a', function(e) {
        var hash = $(this).attr('href');
        e.preventDefault();
        window.location.hash = hash;
        window.scrollTo(0, $(hash).offset().top - 100);
    });

    $body.on('click', '.jsOneClickButtonOnDeliveryMap', function(){
        $('.jsProductPointsMap').trigger('close');
    });

    $body.on('click', '.jsShowDeliveryMap', function(){

        var productId = $(this).data('product-id'),
            $div = $('.jsProductPointsMap');

        // Если нет пунктов самовывоза
        if ($('.jsDeliveryPickupAvailable').length == 0) return;

        // Если точки были загружены, то просто показываем этот div
        if ($div.find('.jsDeliveryMapPoints').length > 0) {
            $div.lightbox_me({
                centered: true,
                preventScroll: true,
                closeClick: false
            });
            return ;
        }

        if ($div.data('xhr')) return;

        $.ajax('/ajax/product/map/' + productId, {
            dataType: 'json',
            beforeSend: function(){
                $div.data('xhr', true);
                $div.lightbox_me({
                    centered: true,
                    preventScroll: true,
                    closeClick: false
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
                        zoom: mapData.zoom
                    },{
                        autoFitToViewport: 'always'
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
                            yMap.geoObjects.add(new ENTER.Placemark(point, true, 'jsOneClickButtonOnDeliveryMap jsOneClickButton-new'));
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
        //categoryItemSelector: '.bGoodsSlider__eCatItem',
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

    $('#reviewForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(data){
                console.log(data);
            },
            complete: function(data) {
                console.log(data);
            }
        });
    })

})(jQuery);

$(document).ready(function() {

    var $productDescriptionToggle = $('#productDescriptionToggle');


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	+function () {
		var image = $('.js-photo-zoomedImg');

		if ( !image.length ) {
			console.warn('Нет изображения для elevateZoom');

			return;
		}

		var
			zoomDisable = ( image.data('zoom-disable') !== undefined ) ? image.data('zoom-disable') : true,
			zoomConfig = {
				gallery: 'productImgGallery',
				galleryActiveClass: 'prod-photoslider__gal__link--active',
				zoomWindowOffety: 0,
				zoomWindowOffetx: 19,
				zoomWindowWidth: image.data('is-slot') ? 344 : 519,
				borderSize: 1,
				borderColour: '#C7C7C7',
				disableZoom: zoomDisable
			};
		// end of vars

		var
			/**
			 * Обработчик клика на изображение в галерее.
			 * Нужен для инициализации/удаления зумера
			 */
			photoGalleryLinkClick = function() {
				if ( $(this).data("zoom-disable") == undefined ) {
					return;
				}

				if ( $(this).data("zoom-disable") == zoomDisable ) {
					return;
				}

				zoomDisable = $(this).data("zoom-disable");

				// инициализация зумера
				if( !zoomDisable ) {
					zoomConfig.disableZoom = zoomDisable;
					image.elevateZoom(zoomConfig);
				}
				else { // удаления зумера
					$.removeData(image, 'elevateZoom');//remove zoom instance from image
					$('.zoomContainer').remove();//remove zoom container from DOM
				}

				return false;
			};
		// end of functions

		image.elevateZoom(zoomConfig);
		$('.jsPhotoGalleryLink').on('click', photoGalleryLinkClick);
	}();


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
                bindOneClickButton = $('.' + spinnerFor + '-oneClick'),
				newHref = bindButton.attr('href') || '';
			// end of vars

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));
            bindOneClickButton.data('quantity', count);

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
/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
(function() {
	var
		productInfo = $('#jsProductCard').data('value') || {},
	// end of vars

		reviewsYandexClick = function ( e ) {
			console.log('reviewsYandexClick');
			var
				link = this //, url = link.href
			;

			if ( 'undefined' !==  productInfo.article ) {
				_gaq.push(['_trackEvent', 'YM_link', productInfo.article]);
				e.preventDefault();
				if ( 'undefined' !== link ) {
					setTimeout(function () {
						//document.location.href = url; // не подходит, нужно в новом окне открывать
						link.click(); // эмулируем клик по ссылке
					}, 500);
				}
			}
		};
	// end of functions and vars
	
	if ( !$('#jsProductCard').length ) {
		return false;
	}

	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}
})();

;(function() {
	// текущая страница для каждой вкладки
	var reviewCurrentPage = {
			user: -1,
			pro: -1
		},
		// количество страниц для каждой вкладки
		reviewPageCount = {
			user: 0,
			pro: 0
		},
		reviewsProductUi = null,
		reviewsType = null,
		reviewsContainerClass = null,

		//nodes
		moreReviewsButton = $('.jsGetReviews'),
		reviewTab = $('.bReviewsTabs__eTab'),
		reviewWrap = $('.bReviewsWrapper'),
		reviewContent = $('.bReviewsContent');
	// end of vars

	/**
	 * Получение отзывов
	 * @param	{String}	productId
	 * @param	{String}	type
	 * @param	{String}	containerClass
	 */
	var getReviews = function( productId, type, containerClass ) {
		var page = reviewCurrentPage[type] + 1,
			layout = false,
			url = '/product-reviews/'+productId,
			dataToSend;
		// end of vars

		var reviewsResponse = function reviewsResponse( data ) {
            var $container = $('.'+containerClass);
			$container.html($container.html() + data.content);
			reviewCurrentPage[type]++;
			reviewPageCount[type] = data['pageCount'];

			if ( reviewCurrentPage[type] + 1 >= reviewPageCount[type] ) {
				moreReviewsButton.hide();
			}
			else {
				moreReviewsButton.show();
			}
		};
		// end of functions

		if ( $('body').hasClass('jewel') ) {
			layout = 'jewel';
		}

		dataToSend = {
			page: page,
			type: type,
			layout: layout
		};

		$.ajax({
			type: 'GET',
			data: dataToSend,
			url: url,
			success: reviewsResponse
		});
	};

	// карточка товара - отзывы - переключение по табам
	if ( reviewTab.length ) {
		// начальная инициализация
		var initialType = reviewWrap.attr('data-reviews-type');

		reviewCurrentPage[initialType]++;
		reviewPageCount[initialType] = reviewWrap.attr('data-page-count');

		if ( reviewPageCount[initialType] > 1 ) {
			moreReviewsButton.show();
		}

		reviewsProductUi = reviewWrap.attr('data-product-ui');
		reviewsType = reviewWrap.attr('data-reviews-type');
		reviewsContainerClass = reviewWrap.attr('data-container');

		reviewTab.click(function() {

            var $reviewContainer = $('.'+reviewsContainerClass);

			reviewsContainerClass = $(this).attr('data-container');

			if ( reviewsContainerClass === undefined ) {
				return;
			}

			reviewsType = $(this).attr('data-reviews-type');
			reviewTab.removeClass('active');
			$(this).addClass('active');
			reviewContent.hide();
			$reviewContainer.show();

			moreReviewsButton.hide();

			if (reviewsType === 'user') {
				moreReviewsButton.html('Показать ещё отзывы');
			}

			if ( !$reviewContainer.html() ) {
				getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
			}
			else {
				// проверяем что делать с кнопкой "показать еще" - скрыть/показать
				if ( reviewCurrentPage[reviewsType] + 1 >= reviewPageCount[reviewsType] ) {
					moreReviewsButton.hide();
				}
				else {
					moreReviewsButton.show();
				}
			}
		});

		moreReviewsButton.click(function() {
			getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
		});
	}

//	var leaveReview = function() {
//		if ( !$('#jsProductCard').length ) {
//			return false;
//		}
//
//		var productInfo = $('#jsProductCard').data('value'),
//			pid = $(this).data('pid'),
//			name = productInfo.name,
//			src = 'http://reviews.testfreaks.com/reviews/new?client_id=enter.ru&' + $.param({key: pid, name: name});
//		// end of vars
//
//		$('.reviewPopup').lightbox_me({
//			onLoad: function() {
//				$('#rframe').attr('src', src);
//			}
//		});
//
//		return false;
//	};
//
//	$('.jsLeaveReview').on('click', leaveReview);

}(jQuery));



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

		reviewStar = form.find('.starsList__item'),
		reviewStarCount = form.find('.jsReviewStarsCount'),
		starStateClass = {
			fill: 'mFill',
			empty: 'mEmpty'
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
		 * @param  {Event} e
		 * @param  {Object} userInfo
		 */
		fillUserData = function fillUserData( e, userInfo ) {
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
	body.on('userLogged', fillUserData);

	reviewStar.hover(hoverStar, unhoverStar);
	reviewStar.on('unhover', unhoverStar);
	reviewStar.on('click', markStar);

    if ('#add-review' == location.hash) {
        openPopup();
    }
}());

;$(function() {
	var $body = $('body');

	/** Событие клика на товар в слайдере */
	$('.js-product-similarProducts-link').on('click', function(event) {
		try {
			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Перешел на карточку товара',
				label: 'SEO',
				hitCallback: $(this).attr('href')
			});

			event.stopPropagation();

		} catch (e) { console.error(e); }
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
			onLoad: function() {
				videoStartTime = new Date().getTime();

				if (typeof(_gaq) !== 'undefined') {
					_gaq.push(['_trackEvent', 'Video', 'Play', productUrl]);
				}
			},
			onClose: function() {
				$('.js-product-video-iframeContainer').empty();
				videoEndTime = new Date().getTime();
				var videoSpent = videoEndTime - videoStartTime;

				if (typeof _gaq !== 'undefined') {
					_gaq.push(['_trackEvent', 'Video', 'Stop', productUrl, videoSpent]);
				}
			}
		});

		return false;
	});
});