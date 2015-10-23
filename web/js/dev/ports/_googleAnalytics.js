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
        postImpressions = [], // impression, которые не влезли в send pageview
        ecommList = '',
    // end of vars

        /* Adfox listing */
        gaBannerClick = function gaBannerClick( BannerId ) {
            $body.trigger('trackGoogleEvent', ['Internal_Promo', BannerId]);
        },

        ga_main = function() {
            console.info( 'GoogleAnalyticsJS main page' );

            /* e-commerce analytics */
            $('.jsMainSlidesRetailRocket').each(function(i, elem) {
                $(elem).find('.jsBuyButton').each(function(ii, product) {
                    if (ii > 3) return false;
                    ENTER.utils.analytics.addImpression(product, {
                        list: $(elem).data('block'),
                        position: ii
                    })
                })
            })
        },

        ga_category = function ga_category() {
            console.info( 'gaJS product catalog' );

            ecommList = 'Catalog';

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

            /* ecomm analytics */
            $('.js-orderButton').each(function(i,el) {
                // из-за ограничений в 8 кб на передачу в самом GA
                if (i < 10) {
                    ENTER.utils.analytics.addImpression(el, {
                        list: ecommList,
                        position: i
                    });
                } else {
                    postImpressions.push({i: i, el: el});
                }
            });

        },

        ga_search = function () {
            console.info( 'gaJS search');
            ecommList = 'Search results';
        },

        ga_product = function() {
            console.info( 'gaJS product page' );

            var $productDataDiv = $('#jsProductCard'),
                product = $productDataDiv.data('value'),
                couponData = $('.js-enterprize-coupon').data('value');

                gaBannerClickPrepare = function gaBannerClickPrepare() {
                    var
                        img = $( this ).find( 'img' ),
                        BannerId = img.attr( 'alt' ) || img.attr( 'src' );
                    gaBannerClick(BannerId);
                };

            /* GA Ecommerce */
            ENTER.utils.analytics.addProduct($productDataDiv[0]);
            // если есть купон и акция, то нужно задублировать просмотр товара.
            // т.к. чуть выше залогировали акцию, то надо теперь оправить и купон
            if (couponData && $productDataDiv.data('ecommerce') && $productDataDiv.data('ecommerce').coupon) {
                ENTER.utils.analytics.addProduct($productDataDiv[0], {coupon: couponData.token})
            }
            ENTER.utils.analytics.setAction('detail');

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

        ga_order = function() {
            console.log('ga_order', data);

            $.each(data.enhancedEcomm.products, function(i,v) {
                ENTER.utils.analytics.addProduct(v);
            });
            ENTER.utils.analytics.setAction('checkout', data.enhancedEcomm.options);

            $('.jsOrderRow').each(function(i,v){
                ENTER.utils.analytics.setAction('checkout_option', {
                    'step': 2,
                    'option' : $(v).data('is-delivery') ? 'доставка' : 'самовывоз'
                });
            });
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
                case 'order-v3-new':
                    ga_order();
            }
        }
        ;// end of functions

    console.group('ports.js::gaJS');

    try {

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

        // отсылаем impressions, которые не влезли в pageview
        while (postImpressions.length > 0) {
            $.each(postImpressions.splice(0,10), function(i,v){
                ENTER.utils.analytics.addImpression(v.el, {
                    list: ecommList,
                    position: v.i
                });
            });
            $body.trigger('trackGoogleEvent', {
                category: 'catalog_impression',
                action: 'send impressions',
                nonInteraction: true
            })
        }

        /** Событие добавления в корзину */
        $body.on('addtocart', function(event, data) {
            if (data.kitProduct) {
                $body.trigger('trackGoogleEvent', ['<button>', data.kitProduct.name, data.kitProduct.article, data.kitProduct.price])
            } else if (data.setProducts && data.setProducts.length) {
                $body.trigger('trackGoogleEvent', ['<button>', data.setProducts[0].name, data.setProducts[0].article, data.setProducts[0].price])
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