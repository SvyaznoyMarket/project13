ANALYTICS.admitadJS = function(data) {
	if (!data) {
		data = {};
	}

    if (data.type == 'main') {
		window._retag = window._retag || [];
		window._retag.push({code: "9ce888744e", level: 0});
		(function () {
			var id = "admitad-retag";
			if (document.getElementById(id)) {return;}
			var s = document.createElement("script");
			s.async = true; s.id = id;
			var r = (new Date).getDate();
			s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
			var a = document.getElementsByTagName("script")[0]
			a.parentNode.insertBefore(s, a);
		})()
    } else if (data.type == 'category') {
		window.ad_category = data.category.id;   // required

		window._retag = window._retag || [];
		window._retag.push({code: "9ce888744c", level: 1});
		(function () {
			var id = "admitad-retag";
			if (document.getElementById(id)) {return;}
			var s=document.createElement("script");
			s.async = true; s.id = id;
			var r = (new Date).getDate();
			s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
			var a = document.getElementsByTagName("script")[0]
			a.parentNode.insertBefore(s, a);
		})()
	} else if (data.type == 'product') {
		// required object
		window.ad_product = {
			"id": data.product.id,   // required
			"vendor": data.product.brand.name,
			"price": data.product.price,
			"url": data.product.url,
			"picture": data.product.imageUrl,
			"name": data.product.name,
			"category": data.product.category.id
		};

		window._retag = window._retag || [];
		window._retag.push({code: "9ce888744d", level: 2});
		(function () {
			var id = "admitad-retag";
			if (document.getElementById(id)) {return;}
			var s = document.createElement("script");
			s.async = true; s.id = id;
			var r = (new Date).getDate();
			s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
			var a = document.getElementsByTagName("script")[0]
			a.parentNode.insertBefore(s, a);
		})()
	} else if (data.type == 'cart') {
		window.ad_products = $.map(data.cart.products, function(product) {
			return {
				"id": product.id,   // required
				"number": product.quantity
			}
		});

		window._retag = window._retag || [];
		window._retag.push({code: "9ce888744a", level: 3});
		(function () {
			var id = "admitad-retag";
			if (document.getElementById(id)) {return;}
			var s = document.createElement("script");
			s.async = true; s.id = id;
			var r = (new Date).getDate();
			s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
			var a = document.getElementsByTagName("script")[0]
			a.parentNode.insertBefore(s, a);
		})()
	}
};