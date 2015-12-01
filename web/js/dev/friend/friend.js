$(function() {
	ENTER.utils.analytics.ga.getClientId(function(gaClientId) {
		$('.js-friend-form-gaClientId').val(gaClientId);
	});
});