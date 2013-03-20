window.ANALYTICS = {
	
    heiasMain : function() {
        (function(d){
            var HEIAS_PARAMS = [];
            HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
            HEIAS_PARAMS.push(['pb', '1']);
            if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
            window.HEIAS.push(HEIAS_PARAMS);
            var scr = d.createElement('script');
            scr.async = true;
            scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
            var elem = d.getElementsByTagName('script')[0];
            elem.parentNode.insertBefore(scr, elem);
        }(document)); 
    },

    heiasProduct : function() {
        var product = arguments[0];
        (function(d){
            var HEIAS_PARAMS = [];
            HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
            HEIAS_PARAMS.push(['pb', '1']);
            HEIAS_PARAMS.push(['product_id', product]);
            if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
            window.HEIAS.push(HEIAS_PARAMS);
            var scr = d.createElement('script');
            scr.async = true;
            scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
            var elem = d.getElementsByTagName('script')[0];
            elem.parentNode.insertBefore(scr, elem);
        }(document));
    },

    heiasOrder : function() {
        var orderArticle = arguments[0];

        (function(d){
            var HEIAS_PARAMS = [];
            HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
            HEIAS_PARAMS.push(['pb', '1']);
            HEIAS_PARAMS.push(['order_article', orderArticle]);
            if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
            window.HEIAS.push(HEIAS_PARAMS);
            var scr = d.createElement('script');
            scr.async = true;
            scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
            var elem = d.getElementsByTagName('script')[0];
            elem.parentNode.insertBefore(scr, elem);
        }(document));            
    },

    heiasComplete : function() {
        var a = arguments[0];

        HEIAS_T=Math.random(); HEIAS_T=HEIAS_T*10000000000000000000;
        var HEIAS_SRC='https://ads.heias.com/x/heias.cpa/count.px.v2/?PX=HT|' + HEIAS_T + '|cus|12675|pb|1|order_article|' + a.order_article + '|product_quantity|' + a.product_quantity + '|order_id|' + a.order_id + '|order_total|' + a.order_total + '';
        document.write('<img width="1" height="1" src="' + HEIAS_SRC + '" />');
        
        (function(d) {
            var HEIAS_PARAMS = [];
            HEIAS_PARAMS.push(['type', 'cpx'], ['ssl', 'force'], ['n', '12564'], ['cus', '14935']);
            HEIAS_PARAMS.push(['pb', '1']);
            HEIAS_PARAMS.push(['order_article',  a.order_article ]);
            HEIAS_PARAMS.push(['order_id', a.order_id ]);
            HEIAS_PARAMS.push(['order_total', a.order_total ]);
            HEIAS_PARAMS.push(['product_quantity', a.product_quantity ]);
            if (typeof window.HEIAS == 'undefined') window.HEIAS = []; window.HEIAS.push(HEIAS_PARAMS);
            var scr = d.createElement('script');
            scr.async = true;
            scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js'; var elem = d.getElementsByTagName('script')[0];
            elem.parentNode.insertBefore(scr, elem);
        }(document));
    },

    adblenderCommon : function() {
        //document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>')
        // 'document.write' for <script/> is overloaded in loadjs.js
        // in fact:
        // var ad = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random()
        // $LAB.script( ad )
        var baseUrl = ('https:' == document.location.protocol ? 'https://' : 'http://');
        baseUrl = baseUrl + 'bn.adblender.ru/view.gif?client=enter';
        function loadImage(act) {
            (new Image()).src = baseUrl + '&act=' + act + "&r=" + Math.random();
        }
        loadImage('view');

        window.setTimeout(function ()
            { loadImage('read'); }

            , 60000);
    },
    
    adblenderOrder : function() {
        var a = arguments[0]
        //document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape( orderSum ) + '&r=' + Math.random() + '" ></sc' + 'ript>')
        // 'document.write' for <script/> is overloaded in loadjs.js
        var script = document.createElement('script'); script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + unescape('bn.adblender.ru%2Fpixel.js%3Fclient%3Denter%26cost%3D') + escape(a.order_id) + unescape('%26order%3D') + escape(a.order_total) + unescape('%26r%3D') + Math.random(); document.getElementsByTagName('head')[0].appendChild(script);
    },
    
    mixmarket : function() {
        document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>')
    },

    adriverCommon : function() {
        var RndNum4NoCash = Math.round(Math.random() * 1000000000);
        var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
        document.write('<img src="' + ('https:' == document.location.protocol ? 'https:' : 'http:') + '//ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
    },

    adriverProduct : function() {
        var a = arguments[0];

        var RndNum4NoCash = Math.round(Math.random() * 1000000000);
        var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
        document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&bt=21&pz=0'+
            '&custom=10='+ a.productId +';11='+ a.categoryId +
            '&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
    },

    adriverOrder : function() {
        var a = arguments[0];

        var RndNum4NoCash = Math.round(Math.random() * 1000000000);
        var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
        document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&sz=order&bt=55&pz=0'+
            '&custom=150='+ a.order_id +
            '&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
    },
    
    // yandexMetrika : function() {
    //     (function (d, w, c) {
    //         (w[c] = w[c] || []).push(function() {
    //             try {
    //             w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true});
    //             } catch(e) {}
    //         });

    //         var n = d.getElementsByTagName("script")[0],
    //         s = d.createElement("script"),
    //         f = function () { n.parentNode.insertBefore(s, n); };
    //         s.type = "text/javascript";
    //         s.async = true;
    //         s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    //         if (w.opera == "[object Opera]") {
    //             d.addEventListener("DOMContentLoaded", f);
    //         } else { f(); }
    //     })(document, window, "yandex_metrika_callbacks");
    // },

    marketgidProd : function() {
        var MGDate = new Date();
        document.write('<iframe src ="http://'
        +'marketgid.com/resiver.html#label1'
        +MGDate.getYear()+MGDate.getMonth()
        +MGDate.getDate()+MGDate.getHours()
        +'" width="0%" height="0" sty'
        +'le = "position:absolute;left:'
        +'-1000px" ></iframe>');
    },

    marketgidOrder : function() {
        var MGDate = new Date();
        document.write('<iframe src ="http://'
        +'marketgid.com/resiver.html#label2'
        +MGDate.getYear()+MGDate.getMonth()
        +MGDate.getDate()+MGDate.getHours()
        +'" width="0%" height="0" sty'
        +'le = "position:absolute;left:'
        +'-1000px" ></iframe>');
    },

    marketgidOrderSuccess : function() {
        var MGDate = new Date();
        document.write('<iframe src ="http://'
        +'marketgid.com/resiver.html#label3'
        +MGDate.getYear()+MGDate.getMonth()
        +MGDate.getDate()+MGDate.getHours()
        +'" width="0%" height="0" sty'
        +'le = "position:absolute;left:'
        +'-1000px" ></iframe>');
    },

    luxupTracker : function() {
        document.write('<scr'+'ipt type="text/javascript" src="http://luxup.ru/tr_js/20634/59951/'+'?t='+(new Date()).getTime()+(document.referrer?"&r="+encodeURIComponent(document.referrer):'')+(typeof __lx__target !== 'undefined'?'&trg='+encodeURIComponent(__lx__target):'')+'"></scr'+'ipt>');        
    },

    runMethod : function( fnname ) {
        if( !this. enable )
            return
        document.writeln = function(){
            $('body').append( $(arguments[0] + '') )
        }

        if( fnname+'' in this ) {
            this[fnname+'']()
        }

    },

    parseAllAnalDivs : function( nodes ) {
        if( !this. enable )
            return

        var self = this
        $.each(  nodes , function() {
//console.info( this.id, this.id+'' in self  )
            
            // document.write is overwritten in loadjs.js to document.writeln
            var anNode = $(this)
            if( anNode.is('.parsed') )
                return
            document.writeln = function(){
                anNode.html( arguments[0] )
            }

            if( this.id+'' in self )
                self[this.id]( $(this).data('vars') )
            anNode.addClass('parsed')
        })
        document.writeln = function(){
            $('body').append( $(arguments[0] + '') )
        }
    },

    myThingsTracker: function() {
        //трекинг от MyThings. Вызывается при загрузке внешнего скрипта
        window._mt_ready = function (){
            if (typeof(MyThings) != "undefined") {
                var sendData = $('#myThingsTracker').data('value')
                if (!$.isArray(sendData)) {
                    sendData = [sendData];
                }

                $.each(sendData, function(i, e) {
                    if (e.EventType !== "undefined") {
                        e.EventType = eval(e.EventType)
                    }
                    MyThings.Track(e)
                })
            }
        }

        mtHost = (("https:" == document.location.protocol) ? "https" : "http") + "://rainbow-ru.mythings.com";
        mtAdvertiserToken = "1989-100-ru";
        document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok="+mtAdvertiserToken+"' type='text/javascript'%3E%3C/script%3E"));
    },
    testFreak : function() {
        document.write('<scr'+'ipt type="text/javascript" src="http://js.testfreaks.com/badge/enter.ru/head.js"></scr'+'ipt>')
    },


    enable : true
}

ANALYTICS.parseAllAnalDivs( $('.jsanalytics') )

var ADFOX = {
	adfoxbground : function() {
        if( $(window).width() < 1000 ) // ATTENTION
            return

		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>'+
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=enlz&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);		
	},
	
	adfox400counter : function() {
	 if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
        var dl = escape(document.location);
        var pr1 = Math.floor(Math.random() * 1000000);  
        document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
        '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>')
        AdFox_getCodeScript(1,pr1, 'http://ads.adfox.ru/171829/prepareCode?p1=biewf&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

	adfox400 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);	
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=engb&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},
	
	adfox215 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>')
		document.write( '<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emud&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);		
	},
	
	adfox683 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>' +
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},
	
	adfox683sub : function() {
        if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
        if (typeof(document.referrer) != 'undefined') {
          if (typeof(afReferrer) == 'undefined') {
            afReferrer = escape(document.referrer);
          }
        } else {
          afReferrer = '';
        }
        var addate = new Date();
        var dl = escape(document.location);
        var pr1 = Math.floor(Math.random() * 1000000);

        document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
        document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

        AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=bdto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},
	
	adfox980 : function() {
		if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
		if (typeof(document.referrer) != 'undefined') {
		  if (typeof(afReferrer) == 'undefined') {
			afReferrer = escape(document.referrer);
		  }
		} else {
		  afReferrer = '';
		}
		var addate = new Date();
		var dl = escape(document.location);
		var pr1 = Math.floor(Math.random() * 1000000);
		
		document.write( '<div id="AdFox_banner_'+pr1+'"><\/div>'+
		'<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>' )
		AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emvi&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
	},

    adfoxWowCredit : function() {
        if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
        if (typeof(document.referrer) != 'undefined') {
          if (typeof(afReferrer) == 'undefined') {
            afReferrer = escape(document.referrer);
          }
        } else {
          afReferrer = '';
        }
        var addate = new Date();
        var dl = escape(document.location);
        var pr1 = Math.floor(Math.random() * 1000000);

        document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
        document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

        AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?p1=bipsp&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
    },

    adfoxGift : function() {
        if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
        if (typeof(document.referrer) != 'undefined') {
          if (typeof(afReferrer) == 'undefined') {
            afReferrer = escape(document.referrer);
          }
        } else {
          afReferrer = '';
        }
        var addate = new Date();
        var dl = escape(document.location);
        var pr1 = Math.floor(Math.random() * 1000000);

        document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
        document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

        AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?p1=bizeq&amp;p2=engb&amp;pct=a&amp;pfc=a&amp;pfb=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
    },

    adfox920: function() {
        if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
        if (typeof(document.referrer) != 'undefined') {
          if (typeof(afReferrer) == 'undefined') {
            afReferrer = escape(document.referrer);
          }
        } else {
          afReferrer = '';
        }
        var addate = new Date();
        var dl = escape(document.location);
        var pr1 = Math.floor(Math.random() * 1000000);

        document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
        document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

        AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=epis&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
    },

    parseAllAdfoxDivs : function( nodes ) {
         if( !this. enable )
            return

        if( window.addEventListener ) {
            var nativeEL = window.addEventListener
            window.addEventListener = function(){
//console.info('addEventListener WINDOW', arguments[0])
              nativeEL.call(this, arguments[0], arguments[1])
              if( arguments[0] === 'load' )
                arguments[1]()
            }
        } else if( window.attachEvent ) { //IE < 9
            var nativeEL = window.attachEvent
            window.attachEvent = function(){
//console.info('addEventListener WINDOW', arguments[0])
//console.info('addEventListener WINDOW', arguments[0])
              //nativeEL.call(window, arguments[0], arguments[1])
              if( arguments[0] === 'onload' )
                arguments[1]()
            }
        }        
            
        var anNode = null
        document.writeln = function() {
            if( anNode )
                anNode.innerHTML += arguments[0]
        }

        $.each( nodes , function() {
//console.info( this.id, this.id+'' in ADFOX  )
            anNode = this
            if( this.id+'' in ADFOX ) {
                ADFOX[this.id]()
            }
        })
        anNode = null
        document.writeln = function(){
            $('body').append( $(arguments[0] + '') )
        }
    },

    enable : true
}

ADFOX.parseAllAdfoxDivs( $('.adfoxWrapper') )