/**
 * Soloway
 */
ANALYTICS.solowayJS = function(data) {
	if (!data) {
		data = {};
	}

    if (data.type == 'product') {
		ENTER.utils.analytics.soloway.send({
			action: 'productView',
			product: {
				ui: data.product.ui,
				category: {
					ui: data.product.category.ui
				}
			}
		});
    } else {
		ENTER.utils.analytics.soloway.send({action: 'pageView'});
	}
};