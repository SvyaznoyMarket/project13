+function(){

    modules.define('enter.config', [], function(provide){
        var config = {
            user: {}
        };
        ENTER = {};
        provide(config);
    });

}();