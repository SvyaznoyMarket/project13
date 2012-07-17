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
    },

    adblender : function() {
        document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>')
        // 'document.write' for <script/> is overloaded in loadjs.js
        // in fact: 
        // var ad = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random()
        // $LAB.script( ad )
    },
    
    adblenderCost : function() {
        var orderSum = arguments[0]
        document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape( orderSum ) + '&r=' + Math.random() + '" ></sc' + 'ript>')
        // 'document.write' for <script/> is overloaded in loadjs.js            
    },
    
    mixmarket : function() {
        document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>')
    },

    adriverCommon : function() {
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

    parseAllAnalDivs : function( nodes ) {
        
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
    }
}

ANALYTICS.parseAllAnalDivs( $('.jsanalytics') )


var ADFOX = {
	adfoxbground : function() {
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

        //AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=bdto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
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

    parseAllAdfoxDivs : function( nodes ) {
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
    }
}

ADFOX.parseAllAdfoxDivs( $('.adfoxWrapper') )