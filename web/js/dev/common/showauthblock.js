/**
 * SITE-2693
 * Показывать окно авторизации, если по аяксу был получен ответ с 403-м статусом
 *
 * @author		Shaposhnik Vitaly
 */
;(function() {
	var authBlock, loginLink;

	$.ajaxSetup({
		error : function(jqXHR) {
			if ( 403 == jqXHR.status ) {

                loginLink = $('.bAuthLink');

                if (loginLink.length) {
                    loginLink.trigger('click');
                    return;
                }

				authBlock = $('#auth-block');

				if ( !authBlock.length ) {
					return;
				}

				authBlock.lightbox_me({
					centered: true,
					autofocus: true,
					onLoad: function() {
						authBlock.find('input:first').focus();
					}
				});
			}
		}
	});
}());