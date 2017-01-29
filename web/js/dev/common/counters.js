(function() {
	ENTER.counters = {
		callGetIntentCounter: function(data) {
			if (typeof __GetI === "undefined") {
				__GetI = [];
			}

			(function () {
				var p = {
					type: data.type,
					site_id: "267"
				};

				if (data.productId !== undefined) {
					p.product_id = data.productId;
				}

				if (data.productPrice !== undefined) {
					p.product_price = data.productPrice;
				}

				if (data.categoryId !== undefined) {
					p.category_id = data.categoryId;
				}

				if (data.orderId !== undefined) {
					p.transaction_id = data.orderId;
				}

				if (data.orderProducts !== undefined) {
					p.order = data.orderProducts;
				}

				if (data.orderRevenue !== undefined) {
					p.revenue = data.orderRevenue;
				}

				console.log('Вызов счётчика GetIntent', p);

				__GetI.push(p);
				var domain = (typeof __GetI_domain) == "undefined" ? "px.adhigh.net" : __GetI_domain;
				var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + domain + '/p.js';
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src = src;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			})();
		},

		callRetailRocketCounter: function(routeName, data) {
			var actions = {
				'product': function (data, userData) {
					console.info('RetailRocketJS product');

					var rcAsyncInit = function() {
						try {
							rcApi.view(data, userData.userId ? userData : undefined);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				},

				'product.category': function (data, userData) {
					console.info('RetailRocketJS product.category');

					var rcAsyncInit = function() {
						try {
							rcApi.categoryView(data, userData.userId ? userData : undefined);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				},

				'orderV3.complete': function (data, userData) {
					console.info('RetailRocketJS orderV3.complete');

					if (userData.userId) {
						data.userId = userData.userId;
						data.hasUserEmail = userData.hasUserEmail;
					}

					var rcAsyncInit = function() {
						try {
							rcApi.order(data);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				}
			};

			function callCounter(userInfo) {
				try {
					console.info('RetailRocketJS action');

					if (userInfo && userInfo.id) {
						rrPartnerUserId = userInfo.id; // rrPartnerUserId — по ТЗ должна быть глобальной
					}

					if (actions.hasOwnProperty(routeName)) {
						var userData = {
							userId: userInfo ? userInfo.id || false : null,
							hasUserEmail: userInfo && userInfo.email ? true : false
						};

						actions[routeName](data, userData);
					}
				} catch (err) {}
			}

			if (ENTER.config.userInfo === false) {
				callCounter();
			} else if (!ENTER.config.userInfo) {
				setTimeout(function() {
					if (ENTER.config.userInfo) {
						callCounter(ENTER.config.userInfo.user);
					} else {
						setTimeout(arguments.callee, 100);
					}
				}, 100);
			} else {
				console.warn(ENTER.config.userInfo);
				callCounter(ENTER.config.userInfo.user);
			}
		}
	};
})();