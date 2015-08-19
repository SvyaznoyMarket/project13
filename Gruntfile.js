module.exports = function(grunt) {

    var jsRootPath = 'web/js/',
        jsDevPath = jsRootPath+'dev/',
        jsProdPath = jsRootPath+'prod/',

        globalConfig = {
            jqueryPlugins:  [
                jsDevPath+'jquery-plugins/jquery.lightbox_me.js',
                jsDevPath+'jquery-plugins/jquery.scrollto.js',
                jsDevPath+'jquery-plugins/jquery.placeholder.js',
                jsDevPath+'jquery-plugins/jquery.infinityCarousel.js',
                jsDevPath+'jquery-plugins/jquery.visible.js',
                jsDevPath+'jquery-plugins/jquery.maskedinput.js',
                jsDevPath+'jquery-plugins/jquery.put_cursor_at_end.js',
                jsDevPath+'jquery-plugins/goodsCounter.js',
                jsDevPath+'jquery-plugins/jquery.deparam.js',
                jsDevPath+'jquery-plugins/jquery.elevatezoom.js',
                jsDevPath+'jquery-plugins/jquery.enterLightboxMe.js',
                jsDevPath+'jquery-plugins/customDropDown.js',
                jsDevPath+'jquery-plugins/goodsSlider.js',
                jsDevPath+'jquery-plugins/jquery-ui-1.10.3.custom.js',
                jsDevPath+'jquery-plugins/jquery.kladr.js'
            ],
            libraryFiles: [
                jsDevPath+'library/getKeysLength.js',
                jsDevPath+'library/JSON.js',
                jsDevPath+'library/isTrueEmail.js',
                jsDevPath+'library/printPrice.js',
                jsDevPath+'library/doc_cookies.js',
                jsDevPath+'library/simple_templating.js',
                jsDevPath+'library/black_box.js',
                jsDevPath+'library/formValidator.js',
                jsDevPath+'library/addParameterToUrl.js',
                jsDevPath+'library/*.js'
            ]
        };

    require('time-grunt')(grunt);

    require('load-grunt-config')(grunt,{
        init: true,
        data: {
            gc: globalConfig,
            pathRoot: jsRootPath,
            pathDev: jsDevPath,
            pathProd: jsProdPath
        }
    });
};