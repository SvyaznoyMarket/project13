var ANALYTICS = {},
    $body = $(document.body),
    docJQ = $(document);
ANALYTICS.ActionPayJS = function (data) {

    var basketEvents = function ( pageType, product ) {

            var aprData = {pageType: pageType};

            if ( typeof(window.APRT_SEND) === 'undefined' || typeof(product) === 'undefined' ) {
                return false;
            }

            aprData.currentProduct = {
                id: product.id,
                name: product.name,
                price: product.price
            };
            window.APRT_SEND(aprData);
        },
        addToBasket = function (event, data) {
            basketEvents(8, data.product);
        },
        remFromBasket = function (event, product) {
            basketEvents(9, product);
        };

    $body.on('addtocart', addToBasket);
    $body.on('removeFromCart', remFromBasket);

    if ( typeof(data) === 'undefined' ) data = {pageType : 0};

    window.APRT_DATA = data;

    $LAB.script('//aprtx.com/code/enter/');

};
ANALYTICS.AlexaJS = function () {

    console.log('AlexaJS init');

    _atrk_opts = {
        atrk_acct: "mPO9i1acVE000x",
        domain: "enter.ru",
        dynamic: true
    };

    $LAB.script('https://d31qbv1cthcecs.cloudfront.net/atrk.js');

};
/**
 * CityAds counter
 */
ANALYTICS.xcntmyAsync = function () {
    var
        elem = $('#xcntmyAsync'),
        data = elem ? elem.data('value') : false,
        page = data ? data.page : false,
    // end of vars

        init = function() {
            $LAB.script('//x.cnt.my/async/track/?r=' + Math.random())
        },

        cart = function() {
            window.xcnt_basket_products = data.productIds; 			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
            window.xcnt_basket_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
        },

        complete = function() {
            window.xcnt_order_products = data.productIds;			// где XX,YY,ZZ – это ID товаров в корзине через запятую.
            window.xcnt_order_quantity = data.productQuantities;	// где X,Y,Z – это количество соответствующих товаров (опционально).
            window.xcnt_order_id = data.orderId;					// где XXXYYY – это ID заказа (желательно, можно  шифровать значение в MD5)
            window.xcnt_order_total = data.orderTotal;				// сумма заказа (опционально)
        },

        product = function() {
            window.xcnt_product_id = data.productId;				// где ХХ – это ID товара в каталоге рекламодателя.
        }
        ;// end of functions


    if ( 'cart' === page ) {
        cart();
    } else if ( 'order.complete' === page ) {
        complete();
    } else if ( 'product' === page ) {
        product();
    }
    init();
};

ANALYTICS.cpaexchangeJS = function () {
    (function () {
        var
            cpaexchange = $('#cpaexchangeJS'),
            data = cpaexchange.data('value'),
            s, b, c;
        // end of vars

        if ( !data || !$.isNumeric(data.id) ) {
            return;
        }

        s = document.createElement('script');
        b = document.getElementsByTagName('body')[0];
        c = document.createComment('HUBRUS RTB Segments Pixel V2.3');

        s.type = 'text/javascript';
        s.async = true;
        s.src = 'http://track.hubrus.com/pixel?id=' + data.id + '&type=js';
        b.appendChild(c);
        b.appendChild(s);
    })();
};
ANALYTICS.criteoJS = function() {
    console.log('criteoJS');
    window.criteo_q = window.criteo_q || [];
    var criteo_arr =  $('#criteoJS').data('value');
    if ( typeof(criteo_q) != "undefined" && !jQuery.isEmptyObject(criteo_arr) ) {
        try{
            window.criteo_q.push(criteo_arr);
        } catch(e) {
        }
    }
};
/**
 * Аналитика на странице подтверждения email/телефона
 */
ANALYTICS.enterprizeConfirmJs = function () {
    var enterprize = $('#enterprizeConfirmJs'),
        data = enterprize.data('value');

    $body.trigger('trackGoogleEvent', ['Enterprize Token Request', 'Номер фишки', data.enter_id]);
};

/**
 * Аналитика на странице подтверждения /enterprize/complete
 */
ANALYTICS.enterprizeCompleteJs = function () {

    var enterprize = $('#enterprizeCompleteJs'),
        data = enterprize.data('value');

    $body.trigger('trackGoogleEvent', ['Enterprize Token Granted', 'Номер фишки', data.enter_id]);

    if (typeof ga != 'undefined') ga('set', '&uid', data.enter_id);
};

/**
 * Аналитика при регистрации в EnterPrize
 */
ANALYTICS.enterprizeRegAnalyticsJS = function() {
    $body.trigger('trackGoogleEvent', ['Enterprize Registration', 'true']);
};
ANALYTICS.flocktoryScriptJS = function() {
    $LAB.script('//api.flocktory.com/v2/loader.js?site_id=427');
};

ANALYTICS.flocktoryCompleteOrderJS = function() {
    var data = $('#flocktoryCompleteOrderJS').data('value');
    window.flocktory = window.flocktory || [];
    console.info('Flocktory data', data);
    window.flocktory.push(['postcheckout', data]);
};
ANALYTICS.GetIntentJS = function(){
    var data = $('#GetIntentJS').data('value');

    ENTER.counters.callGetIntentCounter({
        type: "VIEW",
        productId: data.productId,
        productPrice: data.productPrice,
        categoryId: data.categoryId
    });

    if (data.orders) {
        $.each(data.orders, function(index, order) {
            ENTER.counters.callGetIntentCounter({
                type: "CONVERSION",
                orderId: order.id,
                orderProducts: order.products,
                orderRevenue: order.revenue
            });
        });
    }
};
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

        /* Adfox listing */
        gaBannerClick = function gaBannerClick( BannerId ) {
            $body.trigger('trackGoogleEvent', ['Internal_Promo', BannerId]);
        },

        ga_main = function() {
            console.info( 'GoogleAnalyticsJS main page' );
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
                $body.trigger('trackGoogleEvent', {
                    category: 'smartchoice',
                    action: title,
                    label: article,
                    hitCallback: url
                });
            });
        },

        ga_product = function() {
            console.info( 'gaJS product page' );
            var
                product = $('#jsProductCard').data('value'),

                gaBannerClickPrepare = function gaBannerClickPrepare() {
                    var
                        img = $( this ).find( 'img' ),
                        BannerId = img.attr( 'alt' ) || img.attr( 'src' );
                    gaBannerClick(BannerId);
                };

            /** Событие клика на баннер */
            $( '.trustfactor-right, .trustfactor-main, .trustfactor-content' ).on( 'click', gaBannerClickPrepare );

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
                    console.log('GA: Recommended link clicked, engine =', engine);
                    //ga('set', 'dimension1', engine);
                }
            });

            if ( data && data.afterSearch && product.article && data.upperCat ) {
                $body.trigger('trackGoogleEvent', ['Items after Search', data.upperCat, product.article]);
            }

        },

        ga_cart = function ga_cart() {
            console.info( 'gaJS cart page' );
            var
                cartData = data ? data.cart : false;

            if ( cartData && cartData.sum ) {
                console.log('event Cart items', cartData.SKUs, cartData.uid, cartData.sum);
                $body.trigger('trackGoogleEvent', ['Cart items', cartData.SKUs, cartData.uid, cartData.sum]);
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
            ga('secondary.send', 'pageview', data.vars); // трекаем весь массив с полями {dimensionN: <*М*>}
        }

        /** Событие добавления в корзину */
        $body.on('addtocart', function ga_addtocart(event, data) {
            var
                productData = data.product;
            // TODO-zra productData = data.products
            if (productData) {
                $body.trigger('trackGoogleEvent', ['<button>', productData.name, productData.article, productData.price])
            }
        });

        /** Событие выбора города */
        $('.jsChangeRegionAnalytics' ).click(function(){
            var
                regionName = $(this).text();
            console.log('GA: dimension8 (ChangeRegion)', regionName);
            ga('set', 'dimension8', regionName);
            ga('secondary.set', 'dimension8', regionName);
        });

    }
    catch(e) {
        console.warn('GA exception');
        console.log(e);
    }
    console.groupEnd();
};
ANALYTICS.googleTagManagerJS = function () {

    var containerId = $('#googleTagManagerJS').data('value');

    console.groupCollapsed('ports.js::googleTagManagerJS');
    console.log('googleTagManagerJS init');

    (function(w,d,s,l,i){
        w[l]=w[l]||[];
        w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayerGTM', containerId);

    console.groupEnd();
};
ANALYTICS.hubrusJS = function() {

    var productData = $('.hubrusProductData').data('value'),
        hubrusDataDiv = $('.hubrusData'),
        hubrusProperty = hubrusDataDiv.data('property'),
        hubrusValue = hubrusDataDiv.data('value'),
        lsCacheKey = 'hubrus_viewed_items',
        viewedItems, hubrusVars = {};

    // Если есть данные по продукту на странице (пользователь открыл страницу продукта)
    if (productData) {
        viewedItems = lscache.get(lsCacheKey) ? lscache.get(lsCacheKey) : [];
        // проверка на уникальность
        if ($.grep(viewedItems, function(p){ return productData.id == p.id }).length == 0) viewedItems.unshift(productData);
        hubrusVars.viewed_items = viewedItems.splice(0,10);
        lscache.set(lsCacheKey, hubrusVars.viewed_items);
    }

    if (hubrusProperty && hubrusValue) {
        hubrusVars[hubrusProperty] = hubrusValue
    }

    /** Событие добавления в корзину */
    $body.on('addtocart removeFromCart', function hubrusAddToCart(event) {
        var	smpix = window.smartPixel1,
            type = event.type;

        if (!smpix || typeof smpix['trackState'] !== 'function') return;

        smpix.trackState(type == 'addtocart' ? 'add_to_cart' : 'remove_from_cart',
            { cart_items: $.map(ENTER.UserModel.cart(), function(e){
                return {
                    id: e.id,
                    price: e.price,
                    category: e.rootCategory ? e.rootCategory.id : 0
                }
            })
            });
    });

    $body.on('click', '.jsOneClickButton-new', function(){
        var smpix = window.smartPixel1,
            product = $('#jsProductCard').data('value'),
            categoryId = 0;

        if (!smpix || typeof smpix['trackState'] !== 'function' || !product) return;
        if ($.isArray(product.category) && product.category.length > 0) categoryId = product.category[product.category.length - 1].id;

        smpix.trackState('oneclick',
            { oneclick_item: [{
                id: product.id,
                price: product.price,
                category: categoryId
            }]
            });

    });

    window.smCustomVars = hubrusVars;

    $LAB.script('http://pixel.hubrus.com/containers/enter/dist/smartPixel.min.js');
};
ANALYTICS.insiderJS = function(){

    var InsiderProduct, fillProducts, products = [];

    InsiderProduct = function (data) {
        this.category = [];
        if (data.category && $.isArray(data.category)) this.category = data.category;
        if (data.name) this.name = data.name;
        if (data.img) this.img = data.img;
        if (data.link) this.url = data.link;
        if (data.price) this.price = '' + data.price;
        return this;
    };

    fillProducts = function(data) {
        $.each(data, function(i,val){
            products.push(new InsiderProduct(val))
        });
        window.spApiPaidProducts = products;
    };

    if (ENTER.UserModel && ENTER.UserModel.cart()) fillProducts(ENTER.UserModel.cart());

    $body.on('addtocart', function(e,data){
        if (data.product) {
            window.spApiPaidProducts = window.spApiPaidProducts || [];
            data.product.category = null; // TODO временно, пока не отдаются категории в едином виде
            window.spApiPaidProducts.push(new InsiderProduct(data.product));
        }
    })
};
ANALYTICS.LiveTexJS = function () {

    console.group('ports.js::LiveTexJS log');

    function loadLiveTex() {
        console.info('LiveTexJS init');

        var lt = document.createElement('script');
        lt.type = 'text/javascript';
        lt.async = true;
        lt.src = 'http://cs15.livetex.ru/js/client.js';
        var sc = document.getElementsByTagName('script')[0];
        if ( sc ) sc.parentNode.insertBefore(lt, sc);
        else  document.documentElement.firstChild.appendChild(lt);

        console.log('LiveTexJS end');
    }

    var
        LTData = $('#LiveTexJS').data('value');
    // end of vars

    var
        liveTexAction = function() {
            if ( !LTData ) {
                return;
            }

            console.info('liveTex action');
            console.log(LTData);

            window.liveTexID = LTData.livetexID;
            window.liveTex_object = true;

            window.LiveTex = {
                onLiveTexReady: function () {
                    var widgetHidden = $('.lt-invite').is(':hidden');
                    window.LiveTex.setName(LTData.username);
                    LiveTex.on('chat_open', function(){
                        $body.trigger('trackGoogleEvent', ['webchat', 'chat_started']);
                    });
                    $body.trigger('trackGoogleEvent', ['webchat', 'chat_visibility', widgetHidden ? 'hidden' : 'visible']);
                },

                invitationShowing: true,

                addToCart: function (productData) {
                    var userid = ( LTData.userid ) ? LTData.userid : 0;
                    if ( !productData.name || !productData.article ) {
                        return false;
                    }
                    window.LiveTex.setManyPrechatFields({
                        'Department': 'Marketing',
                        'Product': productData.article,
                        'Ref': window.location.href,
                        'userid': userid
                    });

                    if ( (!window.LiveTex.invitationShowing) && (typeof(window.LiveTex.showInvitation) === 'function') ) {
                        LiveTex.showInvitation('Здравствуйте! Вы добавили корзину ' + productData.name + '. Может, у вас возникли вопросы и я могу чем-то помочь?');
                        LiveTex.invitationShowing = true;
                    }
                } // end of addToCart function
            }; // end of LiveTex Object
        },

        /**
         * @param {Object}	userInfo	Данные пользователя
         */
        liveTexUserInfo = function( userInfo ) {
            try {
                LTData.username = 'undefined' != typeof(userInfo.name) ? userInfo.name : null;
                LTData.userid = 'undefined' != typeof(userInfo.id) ? userInfo.id : null;

                liveTexAction();

            } catch ( err ) {
                ENTER.utils.logError({
                    event: 'liveTex_error',
                    type:'ошибка в action',
                    err: err
                });
            }
        };
    // end of functions

    if (ENTER.config.userInfo === false) {
        liveTexAction();
        loadLiveTex();
    } else if (ENTER.config.userInfo) {
        // SITE-4382
        liveTexUserInfo(ENTER.config.userInfo);
        loadLiveTex();
    }

    console.groupEnd();
};

ANALYTICS.marinSoftwarePageAddJS = function( callback ) {

    console.info('marinSoftwarePageAddJS');

    var mClientId ='7saq97byg0';

    $LAB.script('//tracker.marinsm.com/tracker/async/' + mClientId + '.js' ).wait(callback);
};

ANALYTICS.marinLandingPageTagJS = function() {
    var marinLandingPageTagJSHandler = function marinLandingPageTagJSHandler() {
        console.info('marinLandingPageTagJS run');

        var _mTrack = window._mTrack || [];

        _mTrack.push(['trackPage']);

        console.log('marinLandingPageTagJS complete');
    };
    // end of functions

    this.marinSoftwarePageAddJS(marinLandingPageTagJSHandler);
};

ANALYTICS.marinConversionTagJS = function() {
    var marinConversionTagJSHandler = function marinConversionTagJSHandler() {
        console.info('marinConversionTagJS run');

        var ordersInfo = $('#marinConversionTagJS').data('value'),
            _mTrack = window._mTrack || [];
        // end of vars

        if ( 'undefined' === typeof(ordersInfo) ) {
            return;
        }

        _mTrack.push(['addTrans', ordersInfo]);
        _mTrack.push(['processOrders']);

        console.log('marinConversionTagJS complete');
    };
    // end of functions

    this.marinSoftwarePageAddJS(marinConversionTagJSHandler);
};
ANALYTICS.MyThingsJS = function() {
    /* Необходимые переменные в глоб. области: mtAdvertiserToken, mtHost, _mt_ready */
    var token = window.mtAdvertiserToken = '1989-100-ru',
        data = $('#MyThingsJS').data('value');

    window.mtHost = (("https:" == document.location.protocol) ? "https" : "http") + "://rainbow-ru.mythings.com";

    window._mt_ready = function() {
        var obj = $.extend({}, data, {
            EventType: MyThings.Event[data.EventType]
        });
        console.log('MyThings Track Object', obj);
        if (typeof(MyThings) != "undefined") {
            MyThings.Track(obj);
        }
    };

    $LAB.script('//rainbow-ru.mythings.com/c.aspx?atok=' + token)
};
ANALYTICS.RetailRocketJS = function() {
    console.groupCollapsed('ports.js::RetailRocketJS');

    rrPartnerId = "519c7f3c0d422d0fe0ee9775"; // rrPartnerId — по ТЗ должна быть глобальной
    rrApi = {};
    rrApiOnReady = [];
    rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view = rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};

    console.info('RetailRocketJS init');

    (function (d) {
        var
            ref = d.getElementsByTagName( 'script' )[0],
            apiJs,
            apiJsId = 'rrApi-jssdk';

        if ( d.getElementById( apiJsId ) ) return;
        apiJs = d.createElement( 'script' );
        apiJs.id = apiJsId;
        apiJs.async = true;
        apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
        ref.parentNode.insertBefore( apiJs, ref );
    }( document ));

    // SITE-3672. Передаем email пользователя для RetailRocket
    (function() {

        var rrData = $('#RetailRocketJS').data('value'),
            userInfo = ENTER.config.userInfo.user,
            email, cookieName;

        if ( 'object' != typeof(rrData) || !rrData.hasOwnProperty('emailCookieName') ) {
            return;
        }

        cookieName = rrData.emailCookieName;

        email = window.docCookies.getItem(cookieName) || userInfo.email;
        if ( !email ) {
            return;
        }

        rrApiOnReady.push(function () {
            if (userInfo.mobile && typeof atob == 'function') {
                rrApi.setEmail(email, {
                    phone: atob(userInfo.mobile),
                    name: userInfo.name
                });
                console.info('RetailRocketJS userEmailSend with phone');
            } else {
                rrApi.setEmail(email);
                console.info('RetailRocketJS userEmailSend');
            }
        });

        window.docCookies.removeItem(cookieName, '/');
    })();

    // Вызываем счётчик для заданных в HTML коде параметров
    (function() {
        try {
            var rr_data = $('#RetailRocketJS').data('value');

            if (rr_data && rr_data.routeName && rr_data.sendData) {
                $.each(rr_data.sendData, function(index, data) {
                    ENTER.counters.callRetailRocketCounter(rr_data.routeName, data);
                });
            }
        } catch (err) {}
    })();

    console.groupEnd();
};
ANALYTICS.sociaPlusJs = function() {
    var _spapi = _spapi || [];
    _spapi.push(['_partner', 'enter']);

    $LAB.script('//enter.api.sociaplus.com/partner.js');
};
ANALYTICS.sociomanticJS = function () {
    $LAB.script('//eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru' + (ENTER.config.pageConfig.isMobile ? '-m' : ''));
};

// финальная страница оформления заказа
ANALYTICS.sociomanticOrderCompleteJS = function() {
    var basket = {products: [], transaction: '', amount: 0.0, currency: 'RUB'},
        ordersData = $('#jsOrder').data('value');

    if (!ordersData) return;

    // пройдем по заказам
    $.each(ordersData.orders, function(i,order){
        // пройдем по продуктам
        $.each(order.products, function(ii,pr) {
            basket.products.push({identifier: pr.article + '_' + docCookies.getItem('geoshop'), amount: pr.price, currency: 'RUB', quantity: pr.quantity})
        });
        // если несколько заказов, то пишем их через дефис
        basket.transaction += i == 0 ? order.numberErp : ' - ' + order.numberErp;
        // если несколько заказов, то суммируем сумму
        basket.amount += parseInt(order.sum, 10);
    });
    window.sonar_basket = basket;
};

ANALYTICS.smanticPageJS = function() {
    (function(){
        console.log('smanticPageJS');
        var
            elem = $('#smanticPageJS'),
            prod = elem.data('prod'),
            prod_cats = elem.data('prod-cats'),
            cart_prods = elem.data('cart-prods');

        window.sonar_product = window.sonar_product || {};

        if ( prod ) {
            window.sonar_product = prod;
        }

        if ( prod_cats ) {
            window.sonar_product.category = prod_cats;
        }

        if ( cart_prods ) {
            window.sonar_basket = { products: cart_prods };
        }
    })();
};
ANALYTICS.yandexOrderComplete = function() {
    try {
        var orderData = $('#jsOrder').data('value');
        if (typeof window.yandexCounter == 'undefined' || orderData == undefined) return;
        $.each(orderData.orders, function (index, order) {
            window.yandexCounter.reachGoal('ORDERCOMPLETE', {
                order_id: order.number,
                order_price: order.sum,
                currency: "RUR",
                goods: $.map(order.products, function (product) {
                    return {
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        quantity: product.quantity
                    }
                })
            })
        });
        console.info('yandexOrderComplete reachGoal successfully sended');
    } catch (e) {
        console.error('yandexOrderComplete error', e);
    }
};

/**
 * Код для adFox
 * Просьба не копипастить их код, а делать как тут или лучше
 */
;(function(){

    var ADFOX,
        date = new Date(),
        loc = encodeURI(document.location),
        afReferrer = window['afReferrer'],
        getRandom = function getRandomF() {
            return Math.floor(Math.random() * 1000000);
        };

    window.ADFOX_pr = getRandom();

    if (typeof(document.referrer) != 'undefined') {
        if (typeof(afReferrer) == 'undefined') afReferrer = encodeURI(document.referrer);
    } else {
        afReferrer = '';
    }

    // для локального окружения
    loc = loc.replace('www.enter.loc', 'www.enter.ru');
    afReferrer = afReferrer.replace('www.enter.loc', 'www.enter.ru');

    ADFOX = {

        /* Background на всех страницах */
        'adfoxbground' : function(elem) {
            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            if( $(window).width() < 1000 ) return;

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>'+
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=enlz&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Карточка продукта */
        'adfox400' : function(elem) {

            var pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            //AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=engb&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + addate.getDate() + '&pw=' + addate.getDay() + '&pv=' + addate.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Первый товар в листингах */
        'adfox215' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=emud&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* product-category-root */
        'adfox683' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=emue&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* product-category-branch, product-category-leaf */
        'adfox683sub' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=bdto&p2=emue&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Возможно еще используется в корзине, но похоже, что deprecated */
        'adfox920' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';

            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=epis&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        }

    };

    /* Parse document */
    $('.adfoxWrapper').each(function() {
        var id = this['id'] + '';
        if ( id in ADFOX ) {
            try {
                ADFOX[id](this);
            } catch (e) {
                console.warn('ADFOX error',e);
            }
        }
    });

})();
console.log('ports.js inited');

$(function(){

    console.groupCollapsed('parseAllAnalDivs');

    $('.jsanalytics').each(function() {

        // document.write is overwritten in loadjs.js to document.writeln
        var $this = $(this);

        console.log($this);

        if ( $this.hasClass('.parsed') ) {
            console.warn('Parsed. Return');
            return;
        }

        document.writeln = function() {
            $this.html( arguments[0] );
        };

        if ( this.id in ANALYTICS ) {
            try {
                // call function
                ANALYTICS[this.id]($(this).data('vars'));
            } catch (e) {
                console.error(e);
            }
        }

        $this.addClass('parsed')
    });

    document.writeln = function() {
        $('body').append( $(arguments[0] + '') );
    };
    console.groupEnd();
});


