define(
    [
        'jquery', 'underscore',
        'module/config'
    ],
    function (
        $, _,
        config
    ) {
        var $body = $('body');

        config.user.infoUrl && $.post(config.user.infoUrl).done(function(response) {
            if (_.isObject(response.result)) {
                if (_.isObject(response.result.widgets)) {
                    $body.data('widget', response.result.widgets);
                    $body.trigger('render');
                }

                if (_.isObject(response.result.user)) {
                    $body.data('user', response.result.user);
                }
            }
        });

    }
);