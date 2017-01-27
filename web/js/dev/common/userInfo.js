$(function() {
    try {
        var cookieName = ENTER.config.userInfo.user.infoCookieName,
            cookieValue,

            renderCount = function() {
                cookieValue = JSON.parse(window.docCookies.getItem(cookieName)) || {};
                $('.id-user-menu').each(function(i, el) {
                    $(el).find('[data-count]').each(function(i, el) {
                        var $el = $(el),
                            name = $el.data('count'),
                            value = (name && cookieValue.hasOwnProperty(name)) ? parseInt(cookieValue[name]) : 0;

                        if (value) {
                            $el.html(value).show();
                        } else {
                            $el.html('').hide();
                        }
                    })
                });
            };

        if (!cookieName) {
            throw {message: 'Не задана кука', context: {cookiename: 'cookieName'}};
        }

        if (
            (ENTER.config.userInfo.user.isLogined)
            && (false === ENTER.config.userInfo.user.countLoaded)
        ) {
            $.get('/user/unauthorized-info').done(function(response) {
                if (!response || !response.hasOwnProperty('orderCount')) {
                    return;
                }

                cookieValue = {
                    orderCount: response.orderCount,
                    favoriteCount: response.favoriteCount,
                    subscribeCount: response.subscribeCount,
                    addressCount: response.addressCount,
                    messageCount: response.messageCount
                };

                window.docCookies.setItem(cookieName, JSON.stringify(cookieValue), 60 * 10, '/');

                renderCount();
            });
        }

        renderCount();
    } catch (error) { console.error(error); }
});