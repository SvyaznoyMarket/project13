define(
    ['jquery', 'underscore'],
    function ($, _) {
        return _.extend({
            cookie: {
                domain: null,
                lifetime: null
            },
            user: {
                infoUrl: null
            },
            credit: {
                cookieName: null
            }
        }, $('body').data('config'));
    }
);