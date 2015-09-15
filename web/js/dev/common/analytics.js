;(function($) {

    var body = $(document.body),
        ga = this.ga,       // Universal

        isUniversalAvailable = ENTER.utils.analytics.isEnabled,

        /**
         * Логирование просмотра страницы в Google Analytics (Classical + Universal)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/pages'
         * @link 'https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiBasicConfiguration#_gat.GA_Tracker_._trackPageview'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject Параметры в следующем порядке: 'page', 'title'
         */
        trackGooglePageview = function trackGooglePageView (jQueryEvent, eventObject) {
            var data = {};
            if (arguments.length >= 2 && typeof eventObject == 'string') {
                data.page = arguments[1];
                if (typeof data.page == 'string' && data.page.substr(0,1) != '/') data.page = '/' + data.page;
                if (arguments[2]) data.title = arguments[2]
            }
            if (isUniversalAvailable()) {
                ga('send', 'pageview', data);
                ga('secondary.send', 'pageview', data);
            }
        },

        /**
         * Логирование события в Google Analytics (Classical + Universal)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#events'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject либо объект, либо параметры в следующем порядке: 'category', 'action', 'label', 'value', 'nonInteraction', 'hitCallback'
         */
        trackGoogleEvent = function trackGoogleEventF (jQueryEvent, eventObject) {

            var e = {},
                universalEvent = { hitType: 'event' },
                props = ['category', 'action', 'label', 'value', 'nonInteraction', 'hitCallback'];

            // Формируем event
            if (arguments.length == 2 && typeof eventObject == 'object') {
                $.each(props, function(i,elem){
                    if (eventObject.hasOwnProperty(elem)) e[elem] = eventObject[elem];
                })
            } else if (arguments.length > 2 && typeof eventObject == 'string') {
                for (var i = 1, l = arguments.length; i < l; i++) {
                    e[props[i - 1]] = arguments[i];
                }
            }

            // Форматируем event
            $.each(props, function(i,elem){
                if (e.hasOwnProperty(elem)) {
                    switch (elem) {
                        case 'category':
                            e[elem] = (e[elem] + '').slice(0, 150);
                            break;
                        case 'action':
                        case 'label':
                            e[elem] = (e[elem] + '').slice(0, 500);
                            break;
                        case 'value':
                            e[elem] = parseInt(e[elem] + '', 10);
                            break;
                        case 'nonInteraction':
                            e[elem] = Boolean(e[elem]);
                            break;
                    }
                }
            });

            // Universal Tracking Code
            if (isUniversalAvailable()) {
                universalEvent.eventCategory = e.category;
                universalEvent.eventAction = e.action;
                if (e.label) universalEvent.eventLabel = e.label;
                if (e.value) universalEvent.eventValue = e.value;
                if (typeof e.hitCallback == 'function') universalEvent.hitCallback = e.hitCallback;
                else if (typeof e.hitCallback == 'string') universalEvent.hitCallback = function(){ window.location.href = e.hitCallback };
                if (e.nonInteraction) ga('set', 'nonInteraction', true);
                ga('send', universalEvent);
                ga('secondary.send', universalEvent);
                console.info('[Google Analytics] Send event:', e);
            } else {
                console.warn('No Universal Google Analytics function found', typeof universalEvent.hitCallback, e.hitCallback);
                if (typeof e.hitCallback == 'function') e.hitCallback(); // если не удалось отправить, но callback необходим
                else if (typeof e.hitCallback == 'string') window.location.href = e.hitCallback;
            }

        },
        /**
         * Объект транзакции
         * @param data Object {id,affiliation,total,shipping,tax,city}
         * @returns Object
         * @constructor
         */
        GoogleTransaction = function GoogleTransactionF(data) {
            this.id = data.id ? data.id : false;
            this.affiliation = data.affiliation;
            this.total = data.total;
            this.shipping = data.shipping ? data.shipping : '0';
            this.tax = data.tax ? data.tax : '0';
            this.city = data.city ? data.city : '';

            // checking values
            if (!this.id) throw 'Некорректный ID транзакции';
            if (!this.total) throw 'Некорректная сумма заказа';

            this.toObject = function toObjectF() {
                return {
                    'id': this.id,
                    'affiliation': this.affiliation,
                    'revenue': this.total,
                    'shipping': this.shipping,
                    'tax': this.tax
                }
            };

            this.toArray = function toArrayF() {
                return [this.id, this.affiliation, this.total, this.tax, this.shipping, this.city]
            };

            return this;
        },
        /**
         * Объект продукта
         * @param data Object {id,name,category,sku,price,quantity}
         * @param transaction_id String
         * @returns Object
         * @constructor
         */
        GoogleProduct = function GoogleProductF(data, transaction_id) {

            this.id = transaction_id ? String(transaction_id) : '';
            this.name = data.name ? String(data.name) : '';
            this.category = data.category ? String(data.category) : '';
            this.sku = data.sku ? String(data.sku) : '';
            this.price = data.price ? String(data.price) : '';
            this.quantity = data.quantity ? String(data.quantity) : '';

            if (!this.id) throw 'Некорректный ID товара';
            if (!this.name) throw 'Некорректное название товара';
            if (!this.price) throw 'Некорректная цена товара';
            if (!this.quantity) throw 'Некорректное количество товара';

            this.toObject = function toObjectF(){
                return {
                    'id': this.id,
                    'name': this.name,
                    'sku': this.sku,
                    'category': this.category,
                    'price': this.price,
                    'quantity': this.quantity
                }
            };

            this.toArray = function toArrayF(){
                return [this.id, this.sku, this.name, this.category, this.price, this.quantity];
            };

            return this;
        },
        /**
         * Логирование транзакции в Google Analytics (Classical + Universal)
		 * Если в action передаётся несколько меток, то для удобства фильтрации по ним в аналитеке нужно заключать каждую метку в скобки, например: RR_покупка (marketplace)(gift)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce'
         * @link 'https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject '{ transaction: {}, products: [] }'
         */
        trackGoogleTransaction = function trackGoogleTransactionF (jQueryEvent, eventObject) {

            var googleTrans, googleProducts;

            try {

                googleTrans = new GoogleTransaction(eventObject.transaction);
                googleProducts = $.map(eventObject.products, function(elem){ return new GoogleProduct(elem, googleTrans.id)});

                // Universal Tracking Code
                if (isUniversalAvailable()) {
                    ga('ecommerce:addTransaction', googleTrans.toObject());
                    ga('secondary.ecommerce:addTransaction', googleTrans.toObject());
                    $.each(googleProducts, function(i, product){
                        ga('ecommerce:addItem',product.toObject());
                        ga('secondary.ecommerce:addItem',product.toObject());
                    });
                    ga('ecommerce:send');
                    ga('secondary.ecommerce:send');
                } else {
                    console.warn('No Universal Google Analytics function found');
                }

            } catch (exception) {
                console.error('[Google Analytics Ecommerce] %s', exception)
            }

		};

    // common listener for triggering from another files or functions
    body.on('trackGooglePageview', trackGooglePageview);
    body.on('trackGoogleEvent', trackGoogleEvent);
    body.on('trackGoogleTransaction', trackGoogleTransaction);

})(jQuery);