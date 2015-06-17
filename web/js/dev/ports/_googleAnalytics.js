/**
 * Google Universal Analytics Tracking
 *
 * @requires jQuery
 *
 * @author	Misiukevich Juljan
 */
ANALYTICS.gaJS = function(data) {
    var
        template	= $body.data('template') || '',
        templSep	= template.indexOf(' '),
        templLen	= template.length,
        route 		= template.substring(0, (templSep > 0) ? templSep : templLen),
        rType 		= (templSep > 0) ? template.substring(templSep + 1, templLen) : '',
        useTchiboAnalytics = Boolean($('#gaJS').data('use-tchibo-analytics')),
    // end of vars

        gaBannerClick = function gaBannerClick( BannerId ) {
            $body.trigger('trackGoogleEvent', ['Internal_Promo', BannerId]);
        },

        gaSubscribeClick = function gaSubscribeClick( type, email ) {
            $body.trigger('trackGoogleEvent', ['Subscriptions', type]);
        },

        /**
         * Tracking event for ga.js and analytics.js
         * @param {string} category (required) The name you supply for the group of objects you want to track.
         * @param {string} action (required) A string that is uniquely paired with each category, and commonly used to define the type of user interaction for the web object.
         * @param {string} [label] (optional) An optional string to provide additional dimensions to the event data.
         * @param {int|object} [value] (optional) An integer that you can use to provide numerical data about the user event (or {'hitcallback':func} object).
         * @param {bool} [nonInteraction] (optional) A boolean that when set to true, indicates that the event hit will not be used in bounce-rate calculation.
         */
        trackEvent = function trackEventF(category, action, label, value, nonInteraction) {
            var // variables
                w = window,
                _gaq = w._gaq || [],
                ga = w[w['GoogleAnalyticsObject']];

            /* Checking values */
            if (category == '' || action == '') return;
            if (typeof value == 'string') value = parseInt(value, 10);

            /* Sending */
            if (typeof _gaq === 'object') _gaq.push(['_trackEvent', category, action, label, value, nonInteraction]);
            if (typeof ga === 'function') ga('send', 'event', category, action, label, value);

            console.log('[Google trackEvent] category: %s, action: %s, label: %s, value: %s, nonInteraction: %s', category, action, label, value, nonInteraction)
        },

        ga_main = function() {

            console.info( 'GoogleAnalyticsJS main page' );

            /** Событие клика на кнопку мобильного приложения */
            $('a.bMobAppLink').click(function(){
                var
                    href = $(this).attr('href'),
                    type = false;

                if ( 'string' !== typeof(href) ) {
                    return;
                }

                if ( href.indexOf('apple.com') > 0 ) {
                    type='apple';
                } else if ( href.indexOf('google.com') > 0 ) {
                    type='google';
                } else if ( href.indexOf('windowsphone.com') > 0 ) {
                    type='windowsphone';
                }

                if ( type ) {
                    $body.trigger('trackGoogleEvent', ['Mobile App Click', type]);
                }
            });
        },

        ga_category = function ga_category() {
            console.info( 'gaJS product catalog' );
            /** Событие выбора фильтра */
            $('.js-category-filter-brand:not(:checked)').click(function ga_filterBrand(){
                var
                    input = $(this),
                    name = input.data('name');

                if ( input.is(':checked') && 'undefined' !== name ) {
                    $body.trigger('trackGoogleEvent', ['brand_selected', name]);
                }
            });
        },

        ga_catalog = function ga_catalog() {
            console.info( 'gaJS catalog' );
            $('.mBannerItem').click(function() {
                var
                    wrapper = $(this).find('.adfoxWrapper'),
                    banner = wrapper.find('div:first'),
                    id = banner.attr('id') || 'adfox';
                gaBannerClick( id );
            });
            // отслеживание кликов на блоках smartchoice
            $('.specialPriceItem').on('click', '.specialPriceItemCont_imgLink, .specialPriceItemCont_name', function (e) {
                var $parent = $(this).closest('.specialPriceItem'),
                    article = $parent.data('article'),
                    title = $parent.find('.specialPriceItemTitle').text(),
                    url = $(this).attr('href');
                e.preventDefault();
                trackEvent('smartchoice', title, article, { 'hitCallback': function () { document.location = url; }});
            });
        },

        ga_product = function() {
            console.info( 'gaJS product page' );
            var
                product = $('#jsProductCard').data('value'),
                ref = document.referrer,
                gaInteractive = function gaInteractive(type) {
                    console.log('GA: event Interactive: ' + type);
                    ga( 'send', 'event', 'Interactive', type );
                },

                gaBannerClickPrepare = function gaBannerClickPrepare() {
                    var
                        img = $( this ).find( 'img' ),
                        BannerId = img.attr( 'alt' ) || img.attr( 'src' );
                    gaBannerClick(BannerId);
                };

            /** Событие клика на баннер */
            $( '.trustfactor-right' ).on( 'click', gaBannerClickPrepare );
            $( '.trustfactor-main' ).on( 'click', gaBannerClickPrepare );
            $( '.trustfactor-content' ).on( 'click', gaBannerClickPrepare );

            /** Событие открытия списка магазинов */
            $('span.bDeliveryNowClick').one('click', function ga_deliveryNow() {
                var
                    wraper = $(this).closest('li.mDeliveryNow');

                if ( 'undefined' !== wraper && wraper.hasClass('mOpen') ) {
                    console.log('GA: Available in Stores clicked!');
                    ga('send', 'event', 'Available in Stores', 'Clicked');
                }
            });


            /** Событие нажатия кнопки «Купить» или «Резерв» */
            $body.on('click', 'a.btnBuy__eLink', function ga_btnBuy() {
                if ( 'undefined' !== product ) {

                    /* На наборах выполняется другой трекинговый код */
                    if ($(this).hasClass('js-kitButton')) {
                        console.log('GA: send event addedCollection collection %s', product.article);
                        ga('send', 'event', 'addedCollection', 'collection', product.article);
                        return ;
                    }

                    console.log('GA: btn Buy');
                    if ($(this).hasClass('mShopsOnly')) {
                        ga('send', 'event', 'reserve', product.name, product.article, product.price);
                    }
                }

            });


            /** Событие нажатия кнопки миниатюры слайдера фото галереи товара */
            $('li.bPhotoSliderGallery__eItem').each(function(j){
                var
                    slideItem = $(this),
                    type = 'Image' + (j+1),
                    link = slideItem.find('a');

                if (link) {
                    console.log(link);
                    link.one('click', function(){
                        gaInteractive(type);
                    });
                }
            });

            /** Событие нажатия кнопки 360 градусов */
            $('li.bPhotoActionOtherAction__eGrad360 a').one('click', function(){
                gaInteractive('grad360')
            });

            /** Событие нажатия кнопки «Видео» */
            $('li.mVideo a.bPhotoLink').one('click', function() {
                gaInteractive('video');
            });

            /** Событие клика по рекомендуемому товару из подборки. Не забывать, что .bSlider ajax-ом наполняется */
            $('.bProductSectionLeftCol').delegate('.js-slider a', 'click', function() {
                var
                    link = this,
                    url = link.href,
                    sender = url ? ENTER.utils.getURLParam('sender', url) : null,
                    params = sender ? sender.split('|') : null,
                    engine = params ? params[0] : null
                    ;

                if ( engine ) {
                    //event.preventDefault(); // for debug
                    console.log('GA: Recommendet link clicked, engine =', engine);
                    ga('set', 'dimension1', engine);
                }
            });

            if ( data && data.afterSearch && product.article && data.upperCat ) {
                console.log('GA: Items after Search', data.upperCat, product.article);
                ga('send', 'event', 'Items after Search', data.upperCat, product.article);
            }

            if (/product\/tchibo/.test(document.location.href)) {
                if (/catalog\/tchibo/.test(ref)) {
                    trackEvent('tchibo_item_visit', 'From_Tchibo', ref, null, true);
                    window.docCookies.setItem('tchibo_track', 1, 0, '/');
                }
                if (/catalog/.test(ref) && !/tchibo/.test(ref)) trackEvent('tchibo_item_visit', 'From_Enter', ref, null, true);
                if (!/catalog/.test(ref)) trackEvent('tchibo_item_visit', 'Other', ref, null, true);
                if (ref=='') trackEvent('tchibo_item_visit', 'From Ads', ref, null, true);
            }
        },

        ga_orderComplete = function ga_orderComplete() {
            var
                ecommerce = data ? data.ecommerce : false,
                addTransaction,
                items,
                send,
                count, i,
                order, j,
                tchiboItems = [], tchiboItemsPrice;
            // end of vars

            if ( !ecommerce ) {
                return;
            }

            console.log( 'gaJS orderComplete (require ecommerce)' );
            ga('require', 'ecommerce', 'ecommerce.js');

            for ( j in ecommerce ) {
                order = ecommerce[j];

                addTransaction = order.addTransaction;
                items = order.items;
                send = order.send;

                if ( addTransaction ) {
                    console.log('ecommerce:addTransaction', addTransaction);
                    ga('ecommerce:addTransaction', addTransaction);
                }

                if ( items ) {
                    count = items.length;
                    for ( i = 0; i < count; i++ ) {
                        console.log('ecommerce:addItem', items[i]);
                        ga('ecommerce:addItem', items[i]);
                        if (/Tchibo/.test(items[i].category) || /Tchibo/.test(items[i].name)) tchiboItems.push(items[i]);
                        if (items[i].rr_viewed) $body.trigger('trackGoogleEvent',['RR_покупка','Купил просмотренные', items[i].rr_block]);
                        if (items[i].rr_added) $body.trigger('trackGoogleEvent',['RR_покупка','Купил добавленные', items[i].rr_block]);
                    }
                }

                if ( send ) {
                    console.log('ecommerce:send', send);
                    ga('ecommerce:send', send);
                }
            }

            if (docCookies.hasItem('tchibo_track') && tchiboItems) {
                tchiboItemsPrice = tchiboItems.reduce(function(pv,cv) { return pv + cv.price; }, 0);
                if (tchiboItemsPrice) trackEvent('tchibo_item_purchase', 'purchase', '', tchiboItemsPrice, true);
            }
        },

        ga_cart = function ga_cart() {
            console.info( 'gaJS cart page' );
            var
                cartData = data ? data.cart : false;

            if ( cartData && cartData.sum ) {
                console.log('event Cart items', cartData.SKUs, cartData.uid, cartData.sum);
                ga('send', 'event', 'Cart items', cartData.SKUs, cartData.uid, cartData.sum);
            }
        },

        ga_action = function ga_action() {
            console.log( 'gaJS action' );
            switch (route) {
                case 'main':
                    ga_main(); // для главной страницы
                    break;
                case 'product_card':
                    ga_product(); // для карточки товара
                    break;
                case 'cart':
                    ga_cart(); // для корзины
                    break;
                case 'product_catalog':
                    if ( 'search' === rType ) {
                        ga_search(); // для страницы поиска
                    }
                    else {
                        ga_category(); // для стр. категории
                    }
                    ga_catalog(); // для всех страниц каталога
                    break;
                case 'order_complete':
                    ga_orderComplete(); // для стр «Спасибо за заказ»
                    break;
            }
        }
        ;// end of functions

    console.group('ports.js::gaJS');

    try{
        if ( 'function' !== typeof(ga) ) {
            console.warn('GA: init error');
            console.groupEnd();
            return false; // метод ga не определён, ошибка, нечего анализировать, выходим
        }
        ga('create', 'UA-25485956-5', 'enter.ru');
        ga('require', 'displayfeatures');

        if ( true === useTchiboAnalytics ) {
            ga('create', 'UA-12345-6', 'auto', {'name': 'tchiboTracker'});

            if( data && 'object' === typeof(data.vars) && data.vars ) {
                console.log('TchiboGA: tchiboTracker.send pageview');
                console.log(data.vars);
                ga('tchiboTracker.send', 'pageview');
            }
        }

        ga_action();

        if( data && 'object' === typeof(data.vars) && data.vars ) {
            console.log('GA: send pageview');
            console.log(data.vars);
            ga('send', 'pageview', data.vars); // трекаем весь массив с полями {dimensionN: <*М*>}
        }

        /** Событие ошибок аджакса */
        docJQ.bind('ajaxError', function ga_ajaxError(event, request, settings, error){
            //alert(request.responseText);
            console.log('GA: ajaxError');
            console.warn('ajaxError: ', event, request, settings, error);
            ga('send', 'event', 'Error', 'ajax', error); // error - <тип ошибки>
        });

        /** Событие добавления в корзину */
        $body.on('addtocart', function ga_addtocart(event, data) {
            var
                productData = data.product;
            // TODO-zra productData = data.products
            if (productData) {
                console.log('GA: addtocart clicked', productData);
                ga('send', 'event', '<button>', productData.name, productData.article, productData.price);
            }
        });

        /** Событие выбора города */
        $('.jsChangeRegionAnalytics' ).click(function(){
            var
                regionName = $(this).text();
            console.log('GA: dimension8 (ChangeRegion)', regionName);
            ga('send', 'dimension8', regionName);
        });

        /** Событие клика на подписку | TODO: проверить другие подписки */
        $('.bSubscribeLightboxPopup__eBtn').on('click', function(){
            gaSubscribeClick( 1 );
        });

        window.gaRun = {
            register: function register() {
                /** Метод для регистрации на сайте */
                console.log('GA: Registration');
                ga( 'send', 'event', 'Registration', 'Registered' );
            },
            login: function login() {
                /** Метод для авторизации на сайт */
                console.log('GA: login');
                ga('send', 'dimension7', 'Registered');
                ga('send', 'event', 'Logged in', 'True');
            }
        };
    }
    catch(e) {
        console.warn('GA exception');
        console.log(e);
    }
    console.groupEnd();
};