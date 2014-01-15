/**
 * Maybe3D
 *
 * @requires jQuery, ENTER.utils.logError, ENTER.config
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils,
		ARPlugin = utils.ARPlugin,
		swfobjectLoaded = false;
	// end of vars

	var
		loadWithSWF = function( functionName ) {
			if ( 'function' !== typeof(functionName) ) return false;
			if ( !swfobjectLoaded ) {
				$LAB.script('swfobject.min.js').wait(functionName);
				swfobjectLoaded = true;
			}
			else{
				functionName();
			}
			return true;
		},
		loadMaybe3D = function() {
		var
			data = $('#maybe3dModelPopup').data('value');

		var
			afterLoad = function() {
			var
				maybe3dPopupShow = function( e ) {
				e.stopPropagation();
				try {
					if ( !$('#maybe3dModel').length ) {
						$('#maybe3dModelPopup_inner').append('<div id="maybe3dModel"></div>');
					}

					swfobject.embedSWF(data.init.swf, data.init.container, data.init.width, data.init.height, data.init.version, data.init.install, data.flashvars, data.params, data.attributes);
					$('#maybe3dModelPopup').lightbox_me({
						centered: true,
						closeSelector: '.close',
						onClose: function() {
							swfobject.removeSWF(data.attributes.id);
						}
					});
				}
				catch ( err ) {
					var dataToLog = {
							event: 'swfobject_error',
							type:'ошибка загрузки swf maybe3d',
							err: err
						};
					// end of vars

					utils.logError(dataToLog);
				}
				return false;
			};

			$('.mGrad360.maybe3d').bind('click', maybe3dPopupShow);
		};

		loadWithSWF(afterLoad);
	},
	loadFitting = function loadFitting() {
		var f_afterLoad = function f_afterLoad()
		{
			var
				ARPluginLoad = function ARPluginLoad(){
					if ( 'undefined' === typeof(ARPlugin) ) {
						console.warn('ARPlugin in not defined');
						return false;
					}
					ARPlugin.init({
						type:"advanced",
						//type:"simple",

						js:"/static/js/",
						css:"/static/css/",
						img:"/static/img/",
						swf:"/static/swf/",
						resources:"/static/resources/",
						meshes_path:"/static/resources/model/",
						textures_path:"/static/resources/model/",
						marker_path:"http://pandragames.ru/enter_marker.pdf"
					});

					fittingPopupShow = function( e ) {
						e.preventDefault();
						ARPlugin.show('watch_1.obj','watch_1.png');
					};

					$('.vFitting').bind('click', fittingPopupShow);
				};

			$LAB.script('ARPlugin.min.js').wait(ARPluginLoad);

		};

		loadWithSWF(f_afterLoad);
	};

	$(document).ready(function() {
		if ( pageConfig['product.maybe3d'] ) {
			loadMaybe3D();
		}

		if ( pageConfig['product.vFitting'] ) {
			loadFitting();
		}
	});
}(this));