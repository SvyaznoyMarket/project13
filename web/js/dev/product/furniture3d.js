/**
 * 3D для мебели
 */
;(function() {
	var loadFurniture3D = function() {
		var furnitureAfterLoad = function() {

			var object = $('#3dModelImg'),
				data = object.data('value'),
				host = object.data('host');
			// end of vars

			var furniture3dPopupShow = function furniture3dPopupShow() {
				$('#3dModelImg').lightbox_me({
					centered: true,
					closeSelector: ".close"
				});

				return false;
			};

			try {
				if ( !$('#3dImgContainer').length ) {
					var AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host);
					AnimFramePlayer.DoLoadModel(data);
					$('.mGrad360.3dimg').bind('click', furniture3dPopupShow);
				}
			}
			catch ( err ) {
				var pageID = $('body').data('id'),
					dataToLog = {
						event: '3dimg',
						type:'ошибка загрузки 3dimg для мебели',
						pageID: pageID,
						err: err
					};
				// end of vars

				logError(dataToLog);
			}
		};

		$LAB.script( 'DAnimFramePlayer.min.js' ).wait(furnitureAfterLoad);
	};

	$(document).ready(function() {
		var pageConfig = $('#page-config').data('value');

		if ( pageConfig['product.img3d'] ) {
			loadFurniture3D();
		}
	});
})();