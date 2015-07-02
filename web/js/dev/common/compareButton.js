$(function() {
	var
		compareNoticeShowClass = 'topbarfix_cmpr_popup-show',
		comparePopups = {fixed: null, static: null},
		compareNoticeTimeout;

	$('body').on('click', '.jsCompareLink, .jsCompareListLink', function(e){
		var
			url = e.currentTarget.href,
			$button = $(e.currentTarget),
			productId = $button.data('id'),
			inCompare = $button.hasClass('btnCmpr_lk-act') || $button.hasClass('product-card-tools__lk--active'),
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
						var userBarType = $(window).scrollTop() > 10 ? 'fixed' : 'static';

						(function() {
							if (!comparePopups[userBarType]) {
								var $userbar = userBarType == 'fixed' ? ENTER.userBar.userBarFixed : ENTER.userBar.userBarStatic;
								comparePopups[userBarType] = $('.js-compare-addPopup', $userbar);

								$('.js-compare-addPopup-closer', comparePopups[userBarType]).click(function() {
									comparePopups[userBarType].removeClass(compareNoticeShowClass);
								});

								$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
									comparePopups[userBarType].removeClass(compareNoticeShowClass);
								});

								$('html').click(function() {
									comparePopups[userBarType].removeClass(compareNoticeShowClass);
								});

								comparePopups[userBarType].click(function(e) {
									e.stopPropagation();
								});

								$(document).keyup(function(e) {
									if (e.keyCode == 27) {
										comparePopups[userBarType].removeClass(compareNoticeShowClass);
									}
								});
							}
						})();

						if (compareNoticeTimeout) {
							clearTimeout(compareNoticeTimeout);
						}

						compareNoticeTimeout = setTimeout(function() {
							comparePopups[userBarType].removeClass(compareNoticeShowClass);
						}, 2000);

						$('.js-compare-addPopup-image', comparePopups[userBarType]).attr('src', data.product.imageUrl);
						$('.js-compare-addPopup-prefix', comparePopups[userBarType]).text(data.product.prefix);
						$('.js-compare-addPopup-webName', comparePopups[userBarType]).text(data.product.webName);

						if (userBarType == 'fixed') {
							ENTER.userBar.show();
						}

						comparePopups[userBarType].addClass(compareNoticeShowClass);

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