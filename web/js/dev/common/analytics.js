;(function($) {

    var body = $(document.body),
        ga = this.ga,
        _gaq = this._gaq,

        /**
         * Логирование события в Google Analytics (Classical + Universal)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#events'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject либо объект, либо параметры в следующем порядке: 'category', 'action', 'label', 'value', 'nonInteraction', 'hitCallback'
         */
        trackGoogleEvent = function trackGoogleEventF (jQueryEvent, eventObject) {

            var e = {},
                universalEvent = { hitType: 'event' },
                classicEvent = ['_trackEvent'],
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
                            e[elem] = e[elem].slice(0, 150);
                            break;
                        case 'action':
                        case 'label':
                            e[elem] = e[elem].slice(0, 500);
                            break;
                        case 'value':
                            e[elem] = parseInt(e[elem].slice, 10);
                            break;
                        case 'nonInteraction':
                            e[elem] = Boolean(e[elem]);
                            break;
                    }
                }
            });

            // Classic Tracking Code
            if (typeof _gaq === 'object') {
                classicEvent.push(e.category, e.action);
                if (e.label) classicEvent.push(e.label);
                if (e.value) classicEvent.push(e.value);
                if (e.nonInteraction) classicEvent.push(e.nonInteraction);
                _gaq.push(classicEvent);
            } else {
                console.warn('No Google Analytics object found')
            }

            // Universal Tracking Code
            if (typeof ga === 'function' && ga.getAll().length != 0) {
                universalEvent.eventCategory = e.category;
                universalEvent.eventAction = e.action;
                if (e.label) universalEvent.eventLabel = e.label;
                if (e.value) universalEvent.eventValue = e.value;
                if (e.hitCallback) universalEvent.hitCallback = e.hitCallback;
                if (e.nonInteraction) ga('set', 'nonInteraction', true);
                ga('send', universalEvent);
            } else {
                console.warn('No Universal Google Analytics function found');
                if (typeof e.hitCallback == 'function') e.hitCallback(); // если не удалось отправить, но callback необходим
            }

            // log to console
            console.info('[Google Analytics] Send event:', e);
        },
        /**
         * Объект транзакции
         * @param data Object {id,affiliation,total,shipping,tax,city}
         * @returns {}
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
         * @returns {}
         * @constructor
         */
        GoogleProduct = function GoogleProductF(data) {

            this.id = data.id ? String(data.id) : '';
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
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce'
         * @link 'https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject '{ transaction: {}, products: [] }'
         */
        trackGoogleTransaction = function trackGoogleTransactionF (jQueryEvent, eventObject) {

            var googleTrans, googleProducts;

            try {

                googleTrans = new GoogleTransaction(eventObject.transaction);
                googleProducts = $.map(eventObject.products, function(elem){ return new GoogleProduct(elem)});

                // Classic Tracking Code
                if (typeof _gaq === 'object') {
                    _gaq.push(['_addTrans'].concat(googleTrans.toArray()));
                    $.each(googleProducts, function(i, product){
                        _gaq.push(['_addItem'].concat(product.toArray()))
                    });
                    _gaq.push(['_trackTrans']);
                } else {
                    console.warn('No Google Analytics object found')
                }

                // Universal Tracking Code
                if (typeof ga === 'function' && ga.getAll().length != 0) {
                    ga('require', 'ecommerce', 'ecommerce.js');
                    ga('ecommerce:addTransaction', googleTrans.toObject());
                    $.each(googleProducts, function(i, product){
                        ga('ecommerce:addItem',product.toObject())
                    });
                    ga('ecommerce:send');
                } else {
                    console.warn('No Universal Google Analytics function found');
                }

            } catch (exception) {
                console.error('[Google Analytics Ecommerce] %s', exception)
            }

        };

    if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

    // common listener for triggering from another files or functions
    body.on('trackGoogleEvent', trackGoogleEvent);
    body.on('trackGoogleTransaction', trackGoogleTransaction);

    // TODO вынести инициализацию трекера из ports.js
    if (typeof ga === 'function' && ga.getAll().length == 0) {
        ga( 'create', 'UA-25485956-5', 'enter.ru' );
    }

})(jQuery);