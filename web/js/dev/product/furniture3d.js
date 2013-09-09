/**
 * 3D для мебели
 *
 * @requires jQuery, ENTER.utils.logError, ENTER.config
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils;
	// end of vars
	
	var loadFurniture3D = function loadFurniture3D() {
		var furnitureAfterLoad = function furnitureAfterLoad() {

			var object = $('#3dModelImg'),
				data = object.data('value'),
				host = object.data('host'),

				AnimFramePlayer = null;
			// end of vars

			var furniture3dPopupShow = function furniture3dPopupShow() {
				$('#3dModelImg').lightbox_me({
					centered: true,
					closeSelector: '.close'
				});

				return false;
			};

			try {
				if ( !$('#3dImgContainer').length ) {
					AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host);

					AnimFramePlayer.DoLoadModel(data);
					$('.mGrad360.3dimg').bind('click', furniture3dPopupShow);
				}
			}
			catch ( err ) {
				var dataToLog = {
						event: '3dimg',
						type: 'ошибка загрузки 3dimg для мебели',
						err: err
					};
				// end of vars

				utils.logError(dataToLog);
			}
		};

		$LAB.script( 'DAnimFramePlayer.min.js' ).wait(furnitureAfterLoad);
	};

	$(document).ready(function() {
		if ( pageConfig['product.img3d'] ) {
			loadFurniture3D();
		}
	});
}(this));