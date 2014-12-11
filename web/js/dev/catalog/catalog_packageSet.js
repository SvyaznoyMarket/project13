/**
 * Package set
 *
 * 
 */
;(function( ENTER ) {

	var packageSetBtn = $('.jsChangePackageSet'),
		packageSetWindow = $('.jsPackageSetPopup');
	// end of vars
	
	/**
	 * Показ окна с изменение комплекта 
	 */
	var showPackageSetPopup = function showPackageSetPopup() {
			packageSetWindow.lightbox_me({
				autofocus: true
			});
		};

	packageSetBtn.on('click', showPackageSetPopup);

}(window.ENTER));