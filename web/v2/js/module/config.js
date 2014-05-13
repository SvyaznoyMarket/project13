define(
    ['jquery', 'underscore'],
    function ($, _){
        return _.extend({
            user: {
                infoUrl: null
            }
        }, $('body').data('config'));
    }
);