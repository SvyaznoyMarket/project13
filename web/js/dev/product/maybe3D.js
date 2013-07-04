/**
 * Maybe3D
 */
;(function(){
	var loadMaybe3D = function(){
		var data = $('#maybe3dModelPopup').data('value');
		var afterLoad = function(){
			var maybe3dPopupShow = function(e){
				e.stopPropagation();
				try {
					if (!$('#maybe3dModel').length){
						$('#maybe3dModelPopup_inner').append('<div id="maybe3dModel"></div>');
					}
					swfobject.embedSWF(data.init.swf, data.init.container, data.init.width, data.init.height, data.init.version, data.init.install, data.flashvars, data.params, data.attributes);
					$('#maybe3dModelPopup').lightbox_me({
						centered: true,
						closeSelector: ".close",
						onClose: function() {
							swfobject.removeSWF(data.attributes.id);
						}
					});
				}
				catch (err){
					var pageID = $(body).data(id);
					var dataToLog = {
						event: 'swfobject_error',
						type:'ошибка загрузки swf maybe3d',
						pageID: pageID,
						err: err
					};
					logError(dataToLog);
				}
				return false;
			};
			$('.bPhotoActionOtherAction__eGrad360.maybe3d').bind('click', maybe3dPopupShow);
		};
		$LAB.script('swfobject.min.js').wait(afterLoad);
	};

	$(document).ready(function() {
		if (pageConfig['product.maybe3d']){
			loadMaybe3D();
		}
	});
}());