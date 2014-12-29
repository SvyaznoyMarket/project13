;(function(w) {
    w.ENTER.OrderV3 = {
        info: {},
        constructors: {},
        map: {},
        mapOptions: {},
        $map: {},
        kladrCityId: 0
    };

    $('body').bind('userLogged', function(e, data) {
        try {
            var
                $form = $('.jsOrderV3OneClickForm'),
                user = data.user

            ;

            if (user) {
                if (user.email) {
                    $form.find('[name="user_info[email]"]').val(user.email);
                }
                if (user.name) {
                    $form.find('[name="user_info[first_name]"]').val(user.name);
                }
                if (user.mobile) {
                    $form.find('[name="user_info[mobile]"]').val(w.ENTER.utils.Base64.decode(user.mobile));
                }
            }
        } catch (e) {
            console.error(e);
        }
    });
}(window));