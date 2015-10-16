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
