;(function() {
	$('.js-siteVersionSwitcher').click(function(e){
		e.preventDefault();
		var domain = window.location.host;
		var domainParts = domain.split(".");
        if (domainParts.length > 2) {
            domain = domainParts[domainParts.length - 2] + "." + domainParts[domainParts.length - 1];
        }

		var config = $(e.currentTarget).data('config');
		document.cookie = config.cookieName + "=1; expires=" + (new Date(Date.now() + config.cookieLifetime * 1000)).toUTCString() + "; domain=" + domain + "; path=/";
		location = e.currentTarget.href;
	});
}());
