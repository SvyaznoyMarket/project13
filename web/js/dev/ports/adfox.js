/**
 * Код для adFox
 * Просьба не копипастить их код, а делать как тут или лучше
 */
;(function(){

    var ADFOX,
        date = new Date(),
        loc = encodeURI(document.location),
        afReferrer = window['afReferrer'],
        getRandom = function getRandomF() {
            return Math.floor(Math.random() * 1000000);
        };

    window.ADFOX_pr = getRandom();

    if (typeof(document.referrer) != 'undefined') {
        if (typeof(afReferrer) == 'undefined') afReferrer = encodeURI(document.referrer);
    } else {
        afReferrer = '';
    }

    // для локального окружения
    loc = loc.replace('www.enter.loc', 'www.enter.ru');
    afReferrer = afReferrer.replace('www.enter.loc', 'www.enter.ru');

    ADFOX = {

        /* Background на всех страницах */
        'adfoxbground' : function(elem) {
            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            if( $(window).width() < 1000 ) return;

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>'+
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=enlz&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Карточка продукта */
        'adfox400' : function(elem) {

            var pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            //AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=engb&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + addate.getDate() + '&pw=' + addate.getDay() + '&pv=' + addate.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Первый товар в листингах */
        'adfox215' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=emud&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* product-category-root */
        'adfox683' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=emue&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* product-category-branch, product-category-leaf */
        'adfox683sub' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';
            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=bdto&p2=emue&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        },

        /* Возможно еще используется в корзине, но похоже, что deprecated */
        'adfox920' : function(elem) {

            var pr = (typeof(ADFOX_pr) == 'undefined') ? getRandom() : ADFOX_pr,
                pr1 = getRandom();

            elem.innerHTML = '<div id="AdFox_banner_'+pr1+'"><\/div>' +
            '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>';

            AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&ps=vto&p2=epis&pct=a&plp=a&pli=a&pop=a&pr=' + pr +'&pt=b&pd=' + date.getDate() + '&pw=' + date.getDay() + '&pv=' + date.getHours() + '&prr=' + afReferrer + '&dl='+loc+'&pr1='+pr1);
        }

    };

    /* Parse document */
    $('.adfoxWrapper').each(function() {
        var id = this['id'] + '';
        if ( id in ADFOX ) {
            try {
                ADFOX[id](this);
            } catch (e) {
                console.warn('ADFOX error',e);
            }
        }
    });

})();