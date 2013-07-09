/**
 * 3D для мебели
 */
;(function(){
	var loadFurniture3D = function(){
		var furnitureAfterLoad = function(){

			var object = $('#3dModelImg');
			var data = object.data('value');
			var host = object.data('host');

			var furniture3dPopupShow = function(){
				$('#3dModelImg').lightbox_me({
					centered: true,
					closeSelector: ".close"
				});
				return false;
			};

			try {
				if (!$('#3dImgContainer').length) {
					var AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host);
					AnimFramePlayer.DoLoadModel(data);
					$('.bPhotoActionOtherAction__eGrad360.3dimg').bind('click', furniture3dPopupShow);
				}
			}
			catch (err){
				var pageID = $('body').data('id');
				var dataToLog = {
					event: '3dimg',
					type:'ошибка загрузки 3dimg для мебели',
					pageID: pageID,
					err: err
				};
				logError(dataToLog);
			}
		};
		$LAB.script( 'DAnimFramePlayer.min.js' ).wait(furnitureAfterLoad);
	};

	$(document).ready(function() {
		if (pageConfig['product.img3d']){
			loadFurniture3D();
		}
	});
}());