/**
 * Maybe3D
 *
 * @requires jQuery, ENTER.utils.logError, ENTER.config
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils,
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
		$('.mGrad360.maybe3d').bind('click', function( e ) {
			$LAB.script('/maybe3DPlayer/player.min.js').wait(function() {
				var maybe3dModelPopup = $('#maybe3dModelPopup');

				maybe3dModelPopup.lightbox_me({
					centered: true,
					closeSelector: '.close',
					onLoad: function() {
						Maybe3D.Starter.setModelPathHTML5('http://fs01.enter.ru/3d/html5/');
						Maybe3D.Starter.embed(maybe3dModelPopup.data('value').modelId, 'maybe3dModel');
					},
					onClose: function() {
						$('#maybe3dModel', maybe3dModelPopup).empty();
					}
				});
			});
			return false;
		});
    },
	loadFitting = function loadFitting() {
		console.log('### LoadFitting BEGIN');
		var
			f_afterLoad = function f_afterLoad()
			{
				var
					ARPluginLoad = function ARPluginLoad() {
						if ( 'undefined' === typeof(/*utils.*/ARPlugin) ) {
							console.warn('ARPlugin is not defined');
							$('li.vFitting' ).hide();
							return false;
						}
						console.log('ARPlugin is defined');
						/*utils.*/ARPlugin.init({
							//type:"advanced",
							type:"simple",
							js:"/js/prod/",
							css:"/styles/ARPlugin/",
							img:"/styles/ARPlugin/img/",
							swf:"/styles/ARPlugin/swf/",
							resources:		pageConfig['product.resources'],
							meshes_path:	pageConfig['product.meshes'],
							textures_path:	pageConfig['product.textures'],
							marker_path:	pageConfig['product.marker']
						});

						fittingPopupShow = function( e ) {
							e.preventDefault();
							if ( typeof _gaq !== 'undefined' ) {
								_gaq.push(['_trackEvent', '3D-primerochnaya', pageConfig['product.name'], 'click']);
							}
							/*utils.*/ARPlugin.show(
								pageConfig['product.article'] + '.obj',
								pageConfig['product.article'] + '.png'
							);
						};

						$('.vFitting').bind('click', fittingPopupShow);
					};

				$LAB.script('ARPluginOrigin.js').wait(ARPluginLoad);

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