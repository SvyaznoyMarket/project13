define(
    ['jquery', 'underscore'],
    function ($, _) {
        console.info('config', $('body').data('config'));

        return _.extend({
            user: {
                infoUrl: null
            }
        }, $('body').data('config'));
    }
);