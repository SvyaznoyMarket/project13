;(function () {
	/**********************************************************************
	 Visitor split, into groups, enabling separate targeting and remarketing.
	 Groups can be compared, checking for cost-per-conversion, and remarketing effectiveness.
	 **********************************************************************/

	/**********************************************************************
	 Group settings with number prefix, e.g. GROUP_1, GROUP_2, GROUP_3
	 **********************************************************************/
	var
		GROUP_1_VENDORS = [1],
		GROUP_1_PERCENT = 33.33,   //33.33 assigns one-third visitors, 50 assigns half, 100 assigns all visitors to group, 101 is invalid

		GROUP_2_VENDORS = [2],
		GROUP_2_PERCENT = 33.33,

		GROUP_3_VENDORS = [3],
		GROUP_3_PERCENT = 33.33;

	//var GROUP_3_VENDORS = [4,5,6,7,8];
	//var GROUP_3_PERCENT = 50;

	var // CONSTANTS
		MAX_GROUPS = 3,
		MAX_VENDORS = 3;

	/**********************************************************************
	 Vendor settings with number prefix, e.g. VENDOR_1, VENDOR_2, VENDOR_3
	 **********************************************************************/
	//  VENDOR 1 - Criteo tag
	//var VENDOR_1_TAG_URL = "//r.bstatic.com/static/js/criteo_ld_min.1993.js";   //Tag URL, or function, provided by vendor
	var VENDOR_1_TAG_URL = "//static.criteo.net/js/ld/ld.js";
	var VENDOR_1_TAG_TYPE = "js";    //Only 1 of 'js, jsFunction, img, or iframe' Relevant DOM Element dynamically inserted into page

	/*
	 //  VENDOR 2 - Monetate [production] tag v6
	 var VENDOR_2_TAG_URL = "//b.monetate.net/js/1/a-f44145b4/p/www.backcountry.com/" + Math.floor((monetateT + 1118388) / 3600000) + "/g"; //  Dynamic URLs allowed
	 var VENDOR_2_TAG_TYPE = "js";
	 */

	//  VENDOR 2 - Sociomantic
	var VENDOR_2_TAG_URL = "//eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru";
	var VENDOR_2_TAG_TYPE = "js";

	//  VENDOR 3 - Google conversion tag
	//  Remove vendor if not required
	var VENDOR_3_TAG_URL = "http://www.googleadservices.com/pagead/conversion.js";
	var VENDOR_3_TAG_TYPE = "js";

	/*
	 //  VENDOR 4 - CRITEO BASKET TAG
	 var VENDOR_4_TAG_URL = "https://dis.us.criteo.com/dis/dis.aspx?p1="+escape("v=2&wi=7711869&s=0&i1=SIC0093&p1=19.95&q1=1")+"&t1=transaction&p=2168&c=2&cb="+Math.floor(Math.random()*99999999999);
	 var VENDOR_4_TAG_TYPE = "js";

	 //  VENDOR 5 - CRITEO JavaScript Function
	 var VENDOR_5_TAG_URL = "CRITEO.Load(document.location.protocol+'//dis.us.criteo.com/dis/dis.aspx?')";
	 var VENDOR_5_TAG_TYPE = "jsFunction";

	 //  VENDOR 6 - YESMAIL CART TRACKING
	 var VENDOR_6_TAG_URL = "http://link.p0.com/1x1c.dyn?p=96JT2A93";
	 var VENDOR_6_TAG_TYPE = "img";

	 //  VENDOR 7 - MERCENT tag
	 var VENDOR_7_TAG_URL = "http://cdn.mercent.com/js/tracker.js";
	 var VENDOR_7_TAG_TYPE = "js";

	 //  VENDOR 8 - DoubleClick Floodlight
	 var VENDOR_8_TAG_URL = "https://fls.doubleclick.net/activityi;src=3254838;type=visit825;cat=visit186;u=985883812;qty=1;cost=199.90;u3=NA;u2=unknown;u5=NA;u4=NA;u8=NA;u6=NA;u9=0.00;;u1=Tï¿½nis de Corrida Performance;ord=399451158";
	 var VENDOR_8_TAG_TYPE = "iframe";

	 //  VENDOR 9 - InviteMedia
	 var VENDOR_9_TAG_URL = "http://segment-pixel.invitemedia.com/pixel?pixelID=110417&partnerID=14&clientID=6125&key=segment&returnType=js";
	 var VENDOR_9_TAG_TYPE = "js";*/

	//Permanently assign visitor to group. When visitor returns, they are part of same group
	var ASSIGN_VISITOR_TO_GROUP = true;   //false would assign visitor to different group, at each page-view

	/************************ NO MODIFICATIONS BELOW LINE ************************/


	/**********************************************************************
	 CONSTANTS
	 **********************************************************************/
	//var MAX_GROUPS  = 10;  //  Limit to 10 max groups to avoid page-slow-loading
	//var MAX_VENDORS = 10;  //  Limit to 10 max vendors
	var VISITOR_ASSIGNED_TO_GROUP_FOR_DAYS = 60; //  visitor assigned to same group


	/**********************************************************************
	 Returns selected group number to tag. Possibly undefined
	 **********************************************************************/
	function getVisitorGroup() {
		var selectedGroup;
		if ( ASSIGN_VISITOR_TO_GROUP ) {  //Read from cookie
			selectedGroup = read_cookie( "visitorSplitGroup" );

			// перезаписываем куку
			if (selectedGroup) {
				setVisitorGroup(selectedGroup);
			}
		}
		if ( !selectedGroup ) {
			var marker = Math.random() * 100;
			var percentCounter = 0;
			for ( var i = 1; i <= MAX_GROUPS; i++ ) {
				var percent;
				try {
					percent = eval( "GROUP_" + i + "_PERCENT" );
				} catch ( exception ) {
					break;
				} //  Cant report error, but let's break faulty execution
				percentCounter = percentCounter + percent;
				if ( percentCounter > marker ) {          //  percentCounter crossed marker
					selectedGroup = i;
					if ( ASSIGN_VISITOR_TO_GROUP ) setVisitorGroup( selectedGroup );
					break;
				}
			}
		}
		return selectedGroup;
	}


	/**********************************************************************
	 Sets visitor to belong to specified group. Cookie stores group number
	 **********************************************************************/
	function setVisitorGroup(group) {
		// cookie domain - dynamically get top-level domain. Domain can be hardcoded too.
		var domain = window.location.host;
		var arr = domain.split( "." );
		if ( arr.length > 1 ) {
			// Use site.com instead of www.site.com, sub.site.com.  BUG: doesnt work for site.co.uk, non standard url format.
			var l = arr.length;
			if ( l > 2 ) domain = arr[l - 2] + "." + arr[l - 1];
		}
		// cookie path - set to root directory so all pages in subdirs share common site cookie
		var path = "/";
		var expireDate = (   new Date( Date.now() + VISITOR_ASSIGNED_TO_GROUP_FOR_DAYS * 24 * 60 * 60 * 1000 )  ).toUTCString();
		document.cookie = "visitorSplitGroup=" + group + "; expires=" + expireDate +
			"; domain=" + domain + "; path=" + path;
	}


	/**********************************************************************
	 read_cookie code reused
	 **********************************************************************/
	function read_cookie(key) {
		var result;
		return (result = new RegExp( '(?:^|; )' + encodeURIComponent( key ) + '=([^;]*)' ).exec( document.cookie )) ? (result[1]) : null;
	}


	/**********************************************************************
	 Inserts tag in page. Tag inserted just-before this script
	 While img and iframe tags are appended to DOM, js tags are async loaded to event 'onload'
	 **********************************************************************/
	function insertTag(type, url) {
		if ( url && type ) {
			if ( url.indexOf( "conversion.js" ) > -1 || url.indexOf( "googleadservices.com" ) > -1 ) {
				url = buildGoogleSmartPixelImgUrl();
				type = "img";
			}
			var scripts = document.getElementsByTagName( 'script' );
			var lastScript = scripts[scripts.length - 1];       //lastScript likely points to this current script
			var element;
			if ( type == 'img' || type == 'iframe' ) {
				element = document.createElement( type );
				element.src = url;
				element.width = 1;
				element.height = 1;
				lastScript.parentNode.appendChild( element );    //More reliable then document.body.appendChild()
			} else if ( type == 'js' || type == 'jsFunction' ) {
				// Add onload event for async loading
				function async_load() {
					console.log('async_load');
					element = document.createElement( 'script' );
					element.type = 'text/javascript';
					element.async = true;
					if ( type == 'js' )         element.src = url;
					else if ( type == 'jsFunction' ) element.innerHTML = url;
					lastScript.parentNode.appendChild( element );    //More reliable then document.body.appendChild()
				}

				async_load();
				//console.log('set Event');
				//window.attachEvent ? window.attachEvent( 'onload', async_load ) : window.addEventListener( 'load', async_load, false );
			}
		}
	}


	/**********************************************************************
	 Builds smart pixel url, including adding key=value pairs in query parameters. Inspects global variables for conversion id and parameters.
	 Returns empty string on error
	 **********************************************************************/
	function buildGoogleSmartPixelImgUrl() {
		var url = "";
		if ( google_conversion_id && google_conversion_label ) {
			url = "//www.googleadservices.com/pagead/conversion/" + google_conversion_id + "/?label=" + google_conversion_label + "&guid=ON&script=0";
		}
		if ( url.length > 0 && google_custom_params ) {
			var data = "";
			for ( var key in google_custom_params ) {
				if ( !google_custom_params.hasOwnProperty( key ) ) continue;
				var value = "";
				if ( (value = google_custom_params[key]) !== undefined ) {
					if ( Array.isArray( value ) ) value = value.toString().replace( /,/g, "%2C" );  // %2C is ,
					data = data + (data.length > 0 ? "%3B" : "") + key + "%3D" + encodeURIComponent( value ); // (%3B is ;) (%3D is =)
				}
			}
		}
		if ( data.length > 0 ) url = url + "&data=" + data;
		return url;
	}

	/**********************************************************************
	 Test function. All vendors are activated, and visit tagged
	 **********************************************************************/
	function testInsertAllTags() {
		var
			i, url, type;

		try {
			for ( i = 1; i <= MAX_VENDORS; i++ ) {
				url = eval( "VENDOR_" + i + "_TAG_URL" );
				type = eval( "VENDOR_" + i + "_TAG_TYPE" );
				insertTag( type, url );
			}
			console.log( 'VisitorSplit testing Success!' );
		} catch ( ex ) {
			console.log( 'VisitorSplit Error, params: ', i, url, type );
			alert( ex );
		}
	}


	/**********************************************************************
	 main function
	 **********************************************************************/
	function main() {
		console.groupCollapsed('ports.js::VisitorSplit');
		var
			selectedGroup = getVisitorGroup(),
			vendors, i, url, type;

		if ( selectedGroup ) {
			console.log('selectedGroup', selectedGroup);
			try {
				vendors = eval( "GROUP_" + selectedGroup + "_VENDORS" );
				if ( vendors ) {
					console.log('vendors ', vendors, vendors.length);
					for ( i = 0; i < vendors.length; i++ ) {
						url = eval( "VENDOR_" + vendors[i] + "_TAG_URL" );
						type = eval( "VENDOR_" + vendors[i] + "_TAG_TYPE" );
						insertTag( type, url );
						console.log( 'insertTag', url, type );
					}
					console.log( 'VisitorSplit loading Success!' );
				}
				else {
					console.log( 'VisitorSplit Error: vendors is false');
				}
			} catch ( ex ) {
				console.log( 'VisitorSplit Error', ex );
				console.log( 'Params: ', i, url, type );
			}
		}
		console.groupEnd();
	}

	/**
	 * Head to head test partners
	 */
	// Очерёдность загрузки партнёров:
	// Google (before)
	// sociomantic (before)
	// Visitor Split !!! main()
	// Criteo  (after)

	if ( $('#smanticPageJS').length ) {
		window.ANALYTICS.smanticPageJS();
	}
	main(); // run Visitor Split
	if ( $('#criteoJS' ).length ) {
		window.ANALYTICS.criteoJS();
	}

	//testInsertAllTags(); // for partners pixels debug

}());
