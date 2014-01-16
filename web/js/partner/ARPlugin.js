/**
 * ARPlugin
 *
 * @requires jQuery, lightbox_me
 */
;
(function (ENTER) {

	var
	    active_ar_pandra = false,
		yml_xml_ar_pandra = null,
		vis_items_ar_pandra = 5,
		swf_ar_panra = null,
		js_ar_pandra = [
			/*"jquery-2.0.0.min.js",
			 "swfobject.js"*/
		],
		js_advanced_ar_pandra = ["jquery.als-1.1.min.js"],
		css_ar_pandra = ["ARPlugin.css"],

		type_ar_pandra = null,
		js_path_ar_pandra = null,
		css_path_ar_pandra = null,
		img_path_ar_pandra = null,
		swf_path_ar_pandra = null,
		resources_path_ar_pandra = null,
		meshes_path_ar_pandra = null,
		textures_path_ar_pandra = null,

		type_simple_ar_pandra = "simple",
		type_advanced_ar_pandra = "advanced",
		def_model_ar_pandra = null,
		def_texture_ar_pandra = null,

		marker_pdf_path_ar_pandra = null;

	ENTER.utils.ARPlugin = new plugin_wrapper_ar_pandra();

	function init_plugin_ar_pandra(options) {
		if ( options != undefined ) {
			type_ar_pandra = options.type;
			js_path_ar_pandra = options.js;
			css_path_ar_pandra = options.css;
			img_path_ar_pandra = options.img;
			swf_path_ar_pandra = options.swf;
			resources_path_ar_pandra = options.resources;
			meshes_path_ar_pandra = options.meshes_path;
			textures_path_ar_pandra = options.textures_path;
			marker_pdf_path_ar_pandra = options.marker_path;
		}
		if ( options == undefined || options.type == undefined ) {
			type_ar_pandra = type_simple_ar_pandra;
		}
		if ( options == undefined || options.js == undefined ) {
			show_warning_ar_pandra( "Please, set js folder" );
		}
		if ( options == undefined || options.css == undefined ) {
			show_warning_ar_pandra( "Please, set css folder" );
		}
		if ( options == undefined || options.swf == undefined ) {
			show_warning_ar_pandra( "Please, set swf folder" );
		}
		if ( options == undefined || options.resources == undefined ) {
			show_warning_ar_pandra( "Please, set resources folder" );
		}
		if ( options == undefined || options.textures_path == undefined ) {
			show_warning_ar_pandra( "Please, set textures folder" );
		}
		if ( options == undefined || options.meshes_path == undefined ) {
			show_warning_ar_pandra( "Please, set meshes folder" );
		}
		if ( options == undefined || options.marker_path == undefined ) {
			marker_pdf_path_ar_pandra = "http://pandra.ru/pandra_marker.pdf";
		}
		add_plugin_js_ar_pandra();
		check_load_ar_pandra();
	}

	function plugin_wrapper_ar_pandra() {
		this.init = init_plugin_ar_pandra;
		this.show = show_plugin_ar_pandra;
	}

	function add_plugin_js_ar_pandra() {
		for ( var i = 0; i < js_ar_pandra.length; i++ ) {
			place_js_ar_pandra( js_ar_pandra[i] );
		}
		if ( type_ar_pandra == type_advanced_ar_pandra ) {
			for ( var i = 0; i < js_advanced_ar_pandra.length; i++ ) {
				place_js_ar_pandra( js_advanced_ar_pandra[i] );
			}
		}
	}

	function add_plugin_css_ar_pandra() {
		for ( var i = 0; i < css_ar_pandra.length; i++ ) {
			place_css_ar_pandra( css_ar_pandra[i] );
		}
	}

	function place_js_ar_pandra(file) {
		var
			head = document.getElementsByTagName( 'head' )[0],
			script = document.createElement( 'script' );

		script.type = 'text/javascript';
		script.src = js_path_ar_pandra + file;
		head.appendChild( script );
	}

	function place_css_ar_pandra(file) {
		$( "head" ).append( "<link rel='stylesheet' type='text/css' href='" + css_path_ar_pandra + file + "'>" );
	}

	function check_load_ar_pandra() {
		if ( document.readyState == "Complete" || document.readyState == "complete" ) {
			add_plugin_css_ar_pandra();
			on_complete_loading_ar_pandra();
		} else {
			setTimeout( check_load_ar_pandra, 11 );
		}
	}

	function on_complete_loading_ar_pandra() {
		if ( type_ar_pandra == type_advanced_ar_pandra ) {
			load_xml_ar_pandra();
		}
		/*$( window ).resize( function () {
			resize_ar_pandra();
		} );*/
	}

	function load_xml_ar_pandra() {
		$.ajax( {
			type: "GET",
			url: "/static/xml/YML.xml",
			dataType: "xml",
			success: parseXml_ar_pandra
		} );
	}

	function show_plugin_ar_pandra(mesh, texture) {
		if ( type_ar_pandra == type_advanced_ar_pandra ) {
			show_advaced_plugin_ar_pandra()
		} else {
			show_simple_plugin_ar_pandra( mesh, texture );
		}
	}

	/**
	 * Запускает/открывает попап lightbox_me
	 */
	function runLightBoxMe() {
		$( "#ar_pandra" ).lightbox_me( {
			centered: true,
			closeSelector: '.close',
			onClose: function () {
				swfobject.removeSWF( 'pandra_ar_swf' );
			}
		} );
	}

	function show_simple_plugin_ar_pandra(mesh, texture) {
		if ( mesh == undefined ) {
			show_warning_ar_pandra( "Please, set current mesh" );
		}
		if ( texture == undefined ) {
			show_warning_ar_pandra( "Please, set current texture" );
		}
		active_ar_pandra = true;

		//$( "body" ).append( "<div id='background_ar_pandra' class='background_ar_pandra'></div>" );
		$( "body" ).append( "<div id='ar_pandra' class='ar_pandra'>" +
			"<div class='close_button_ar_pandra'><a href='javascript:close_ar_padnra()'><img src='" + img_path_ar_pandra + "close.png'></a></div>" +
			"<div class='ar_pandra_content' id='ar_pandra_content'></div>" +
			"<div class='footer_ar_pandra footer_ar_pandra_simple'>" +
			"<div><a href='" + marker_pdf_path_ar_pandra + "' target='_blank'>Скачать маркер (.pdf)</a></div>" +
			"|" +
			"<div class='powered_by_ar_pandra'><a href='http://www.pandra.ru' alt='pandra' target='_blank'>Разработка - <img src='" + img_path_ar_pandra + "logo.png'></a></div>" +
			"</div>" +
			"</div>" );


		/*$( "#background_ar_pandra" ).fadeTo( "fast", 0.7, function () {
		 $( "#ar_pandra" ).show( {options: {easing: "easeOutBack"}, effect: "scale", direction: "horizontal", duration: 200} );
		 } );*/

		def_model_ar_pandra = meshes_path_ar_pandra + mesh;
		def_texture_ar_pandra = textures_path_ar_pandra + texture;
		add_swf_ar_pandra();

		runLightBoxMe();

		/*resize_ar_pandra();*/
	}

	function show_advaced_plugin_ar_pandra() {
		active_ar_pandra = true;
		var
			temp_vis_items_ar_pandra = 0,
			temp_vis_items_toset_ar_pandra = 0;

		//$( "body" ).append( "<div id='background_ar_pandra' class='background_ar_pandra'></div>" );
		$( "body" ).append( "<div id='ar_pandra' class='ar_pandra'>" +
			"<div class='close_button_ar_pandra'><a href='javascript:close_ar_padnra()'><img src='" + img_path_ar_pandra + "close.png'></a></div>" +
			"<div class='ar_pandra_content' id='ar_pandra_content'></div>" +
			"<div class='als-container' id='demo1'>" +
			"<span class='als-prev'><img src='http://als.musings.it/images/thin_left_arrow.png' alt='prev' title='previous' /></span>" +
			"<div class='als-viewport'>" +
			"<ul class='als-wrapper'>" +
			"</ul>" +
			"</div>" +
			"<span class='als-next'><img src='http://als.musings.it/images/thin_right_arrow_333.png' alt='next' title='next' /></span>" +
			"</div>" +
			"<div class='footer_ar_pandra'>" +
			"<div><a href='" + marker_pdf_path_ar_pandra + "' target='_blank'>Скачать маркер (.pdf)</a></div>" +
			"|" +
			"<div class='powered_by_ar_pandra'><a href='http://www.pandra.ru'  target='_blank'>Разработка - <img src='" + img_path_ar_pandra + "logo.png'></a></div>" +
			"</div>" +
			"</div>" );
		/*$( "#background_ar_pandra" ).fadeTo( "fast", 0.7, function () {
		 $( "#ar_pandra" ).show( {options: {easing: "easeOutBack"}, effect: "scale", direction: "horizontal", duration: 200} );
		 } );*/

		$( yml_xml_ar_pandra ).find( 'offer' ).each( function () {
			var
				offer = $( this ),
				targetClass = "als-item";
			temp_vis_items_ar_pandra++;
			if ( temp_vis_items_ar_pandra == 1 ) {
				targetClass += " als-item-selected";
				set_def_model_ar_pandra( offer.attr( "id" ) );
			}
			$( ".als-wrapper" ).append( "<li class='" + targetClass + "' myId='" + offer.attr( "id" ) + "'><img src='" + offer.find( 'picture' ).text() + "' alt='orange' title='orange' />" + offer.find( 'model' ).text() + "</li>" );
			$( ".als-wrapper" ).find( "li" ).last().click( function () {
				$( '.als-item' ).each( function () {
					$( this ).removeClass( "als-item-selected" );
				} )
				$( this ).addClass( "als-item-selected" );
				load_model_ar_pandra( $( this ).attr( "myId" ) );
			} );
		} );
		if ( temp_vis_items_ar_pandra >= vis_items_ar_pandra ) {
			temp_vis_items_toset_ar_pandra = vis_items_ar_pandra;
		} else {
			temp_vis_items_toset_ar_pandra = temp_vis_items_ar_pandra;
		}
		$( "#demo1" ).als( {
			visible_items: temp_vis_items_toset_ar_pandra,
			orientation: "horizontal",
			circular: "no",
			autoscroll: "no"
		} );
		add_swf_ar_pandra();

		runLightBoxMe();

		//resize_ar_pandra();
	}

	function load_model_ar_pandra(id) {
		swf_ar_panra.loadObject( "/static/resources/model/watch_" + id + ".obj", "/static/resources/model/watch_" + id + ".png" );
	}

	function set_def_model_ar_pandra(id) {
		def_model_ar_pandra = "/static/resources/model/watch_" + id + ".obj";
		def_texture_ar_pandra = "/static/resources/model/watch_" + id + ".png";
	}

	function show_warning_ar_pandra(message) {
		console.log( "AR_PANDRA: Error! " + message );
	}

	function add_swf_ar_pandra() {
		var
			flashvars = {
				resourcesLink: resources_path_ar_pandra,
				defaultModel: def_model_ar_pandra,
				defaultTexture: def_texture_ar_pandra
			},
			params = {
				allowscriptaccess: "always",
				wmode: "direct"
			},
			attributes = {
				id: "pandra_ar_swf",
				name: "pandra_ar_swf"
			};

		swfobject.embedSWF(
			swf_path_ar_pandra + "arPrototype.swf",
			"ar_pandra_content",
			"640", "480",
			"11", "expressInstall.swf",
			flashvars,
			params,
			attributes,
			function (e) {
				swf_ar_panra = e.ref;
				//setTimeout( resize_ar_pandra, 200 )
			}
		);

	}

	function resize_ar_pandra() {
		if ( active_ar_pandra == true ) {
			var
				wdiv = $( "#ar_pandra" ),
				top = $( window ).height() / 2 - wdiv.height() / 2,
				left = $( window ).width() / 2 - wdiv.width() / 2;

			if ( !wdiv.length ) return false;

			$( "#ar_pandra" ).css( "top", top );
			$( "#ar_pandra" ).css( "left", left );
		}
	}

	function close_ar_padnra() {
		$( "#ar_pandra" ).hide( {options: {easing: "easeInBack"}, effect: "scale", direction: "horizontal", duration: 200, complete: window_hided_ar_padnra} );
	}

	function window_hided_ar_padnra() {
		$( "#background_ar_pandra" ).fadeTo( "fast", 0, function () {
			$( "#background_ar_pandra" ).remove();
			$( "#ar_pandra" ).remove();
		} );
	}

	function parseXml_ar_pandra(xml) {
		yml_xml_ar_pandra = xml;
	}
}( window.ENTER ));
