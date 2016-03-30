!function( window ) {
    var
        configuration = $('#page-config').data('value')['nodeMQConfig'];

    window.WS_Client = (function() {

        var
            CART_SPLIT_CHANNEL         = 'cart_split_message',
            ACCEPT_NEW_REQUEST_CHANNEL = 'accept_new_request',
            RESULT_CHANNEL             = 'cart_split_result';

        function WS_Client( onConnect, onError ) {
            // enforces new
            if ( !(this instanceof WS_Client) ) {
                return new WS_Client();
            }

            // constructor body
            this.client    = new WebSocket('ws://' + configuration['host'] + ':' + configuration['port'] + '/', 'echo-protocol');
            this.connected = false;

            console.info('WS_Client created');

            this.client.onopen    = this.onConnect.bind(this);
            this.client.onmessage = this.onMessage.bind(this);
            this.client.onclose   = this.onDisconnect.bind(this);
            this.client.onerror   = this.onError.bind(this);

            this.onConnectCb = onConnect;
            this.onErrorCb   = onError;

            this.requests = [];
        }

        WS_Client.prototype.onDisconnect = function( event ) {
            console.warn('WS_Client disconnected', event);

            var request = this.requests.pop();
            request.options.onFail && request.options.onFail(event);
            request.options.onAlways && request.options.onAlways();
        };

        WS_Client.prototype.onConnect = function( connection ) {
            console.log('WS_Client connected');
            this.connected = true;
            this.onConnectCb();
        };

        WS_Client.prototype.onError = function( error ) {
            console.error('WS_Client connection error: ' + error.toString());
            this.onErrorCb(error);
        };

        WS_Client.prototype.onMessage = function( event ) {
             var
                 m,
                 request = this.requests.pop();

            try {
                m = event.data;
                m = JSON.parse(m);
            } catch( err ) {
                console.warn('WS_Client неверный формат сообщения: ' + err);
                request.options.onFail && request.options.onFail(m);
            }

            if ( m.channel && m.message && m.channel === RESULT_CHANNEL ) {
                return this.onResult(m.message, request);
            } else if ( m.channel && m.message && m.channel === ACCEPT_NEW_REQUEST_CHANNEL ) {
                return this.onAcceptNewRequest(m.message, request);
            } else {
                console.warn('WS_Client: Несуществующий канал или отсутствует тело сообщения: ' + m);
                request.options.onFail && request.options.onFail(m);
                request.options.onAlways && request.options.onAlways();
            }
        };

        WS_Client.prototype.onResult = function( result, request ) {
            console.log('WS_Client recieve message ', result);

            if ( result.error && result.error.code === 409 ) {
                console.info('WS_Client waiting server accept: Превышен размер очереди. Сообщение не обработано');
                return;
            } else if ( result.error ) {
                request.options.onFail && request.options.onFail(result.error);
            } else {
                request.options.onDone && request.options.onDone(result.result);
            }

            request.options.onAlways && request.options.onAlways();
        };

        WS_Client.prototype.onAcceptNewRequest = function(result, request) {
            console.log('WS_Client can send request again');
            this.send(request.options);
        };

        /**
         * @param options.data
         * @param {Function} options.onDone
         * @param {Function} options.onFail
         * @param {Function} options.onBeforeSend
         * @param {Function} options.onAlways
         */
        WS_Client.prototype.send = function( options ) {
            var request = {
                options: $.extend(true, {}, options)
            };

            this.requests.push(request);

            request.options.onBeforeSend && request.options.onBeforeSend();

            var message = this.getMessage(request.options.data);

            console.log('WS_Client emit message', message);

            this.client.send(JSON.stringify({
                channel: CART_SPLIT_CHANNEL,
                message: message
            }));
        };

        WS_Client.prototype.getMessage = function(data) {
            var cookies = {};
            $.each(docCookies.keys(), function(key, value) {
                cookies[value] = docCookies.getItem(value);
            });

            return {
                cookies: cookies,
                method: 'POST',
                // host: 'www.enter.ru',
                host: window.location.host,
                pathname: '/order/delivery',
                headers: {
                    'Accept': '*/*',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                query: {},
                data: data
            };
        };

        return WS_Client;

    }());
}(this);