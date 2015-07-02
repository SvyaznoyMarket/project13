$(function( ENTER ) {
	var
		compareNoticeShowClass = 'topbarfix_cmpr_popup-show',
		$comparePopup,
		compareNoticeTimeout;

	$('body').on('click', '.jsCompareLink, .jsCompareListLink', function(e){
		var
			url = e.currentTarget.href,
			$button = $(e.currentTarget),
			productId = $button.data('id'),
			inCompare = $button.hasClass('btnCmprb-act'),
			isSlot = $button.data('is-slot'),
			isOnlyFromPartner = $button.data('is-only-from-partner');

		var location = '';
		if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
			location = 'listing';
		} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
			location = 'product';
		}

		if ($(this).hasClass('jsCompareListLink')) {
			url = inCompare ? ENTER.utils.generateUrl('compare.delete', {productId: productId}) : ENTER.utils.generateUrl('compare.add', {productId: productId, location: location});
		}

		e.preventDefault();

		$.ajax({
			url: url,
			success: function(data) {
				if (data.compare) {
					ENTER.UserModel.compare.removeAll();
					$.each(data.compare, function(i,val){ ENTER.UserModel.compare.push(val) });

					if (!inCompare) {
						if (!$comparePopup) {
							var $userbar = ENTER.userBar.userBarStatic;
							$comparePopup = $('.js-compare-addPopup', $userbar);

							$('.js-compare-addPopup-closer', $comparePopup).click(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$('html').click(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$($comparePopup).click(function(e) {
								e.stopPropagation();
							});

							$(document).keyup(function(e) {
								if (e.keyCode == 27) {
									$comparePopup.removeClass(compareNoticeShowClass);
								}
							});
						}

						if (compareNoticeTimeout) {
							clearTimeout(compareNoticeTimeout);
						}

						compareNoticeTimeout = setTimeout(function() {
							$comparePopup.removeClass(compareNoticeShowClass);
						}, 2000);

						$('.js-compare-addPopup-image', $comparePopup).attr('src', data.product.imageUrl);
						$('.js-compare-addPopup-prefix', $comparePopup).text(data.product.prefix);
						$('.js-compare-addPopup-webName', $comparePopup).text(data.product.webName);

						ENTER.userBar.show(true, function(){
							$comparePopup.addClass(compareNoticeShowClass);
						});

						(function() {
							var action;
							if (isSlot) {
								action = 'marketplace-slot';
							} else if (isOnlyFromPartner) {
								action = 'marketplace';
							} else {
								action = 'enter';
							}

							if (location) {
								$('body').trigger('trackGoogleEvent', ['Compare_добавление', action, location]);
							}
						})();
					}
				}
			}
		})
	});
});