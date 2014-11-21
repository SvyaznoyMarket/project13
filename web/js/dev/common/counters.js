(function() {
	ENTER.counters = {
		initGetIntentCounter: function(data) {
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
		}
	};
})();