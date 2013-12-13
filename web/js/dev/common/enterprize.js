/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function() {
	var
		enterprizeAuthLink = $('.jsEnterprizeAuthLink');
	// end of vars

	var
		removeEnterprizeAuthClass = function ( e, userInfo ) {
			if ( !userInfo || !userInfo.name ) {
				return;
			}

			if ( !enterprizeAuthLink.length ) {
				return;
			}

			$.each(enterprizeAuthLink, function () { $(this).removeClass('jsEnterprizeAuthLink') });
		};
	// end of functions

	$('body').on('userLogged', removeEnterprizeAuthClass);
}());