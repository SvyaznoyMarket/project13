+function(){

    modules.define('enter.userbar', ['jQuery', 'jquery.visible'], function(provide){

        var $userbar = $('.js-userbar-fixed'),
            $target = $('.js-show-fixed-userbar');

        $(window).on('scroll', function(){
            if ($target.length && !$target.visible()) {
                $userbar.fadeIn();
            } else {
                $userbar.fadeOut();
            }
        });

        provide({});

    });

}();