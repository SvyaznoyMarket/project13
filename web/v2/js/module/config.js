define(
    ['jquery', 'underscore'],
    function ($, _){
        return _.extend({
            user: {
                infoCookie: null,
                infoUrl: null
            }
        }, $('body').data('config'));
    }
);