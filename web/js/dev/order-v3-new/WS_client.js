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
        }

        WS_Client.prototype.onDisconnect = function( event ) {
            console.warn('WS_Client disconnected', event);
            this.errorCb && this.errorCb(event);
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

        WS_Client.prototype.onAcceptNewRequest = function() {
            console.log('WS_Client can send request again');
            this.send();
        };

        WS_Client.prototype.onMessage = function( event ) {
             var
                m;

            try {
                m = event.data;
                m = JSON.parse(m);
            } catch( err ) {
                console.warn('WS_Client неверный формат сообщения: ' + err);
                this.errorCb(m);
            }

            if ( m.channel && m.message && m.channel === RESULT_CHANNEL ) {
                return this.onResult(m.message);
            } else if ( m.channel && m.message && m.channel === ACCEPT_NEW_REQUEST_CHANNEL ) {
                return this.onAcceptNewRequest(m.message);
            } else {
                console.warn('WS_Client: Несуществующий канал или отсутствует тело сообщения: ' + m);
                this.errorCb(m);
            }

            this.alwaysCb();
            this.clear();
        };

        WS_Client.prototype.clear = function() {
            delete this.alwaysCb;
            delete this.doneCb;
            delete this.errorCb;
            delete this.message;
        };

        WS_Client.prototype.onResult = function( result ) {
            console.log('WS_Client recieve message ', result);

            if ( result.error && result.error.code === 409 ) {
                console.info('WS_Client waiting server accept: Превышен размер очереди. Сообщение не обработано');
            } else if ( result.error ) {
                this.errorCb(result.error);
            } else {
                this.doneCb(result.result);
            }

            this.alwaysCb();
            this.clear();
        };

        WS_Client.prototype.send = function( options ) {
            if ( !this.message ) {
                this.message      = this.getMessage();
                this.alwaysCb     = options.always;
                this.errorCb      = options.fail;
                this.doneCb       = options.done;
                this.message.data = options.data;

                options.beforeSend();
            }

            console.log('WS_Client emit message', this.message);

            this.client.send(JSON.stringify({
                channel: CART_SPLIT_CHANNEL,
                message: this.message
            }));
        };

        WS_Client.prototype.getMessage = function() {
            return {
                cookies: {
                    enter: docCookies.getItem('enter')
                },
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
                data: {}
            };
        };

        return WS_Client;

    }());
}(this);