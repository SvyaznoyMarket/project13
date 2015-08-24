;$(function() {
	var
		$body = $('body'),
		isOpening = false;

	$body.on('click', '.js-kitButton', function(e) {
		e.preventDefault();

		if (isOpening) {
			return;
		}

		var $button = $(e.currentTarget);

		isOpening = true;
		$.ajax({
			url: ENTER.utils.generateUrl('product.kit', {productUi: $button.data('product-ui')}),
			type: 'POST',
			dataType: 'json',
			closeClick: false,
			success: function(result) {
				$('.bCountSection').goodsCounter('destroy');

				var $popup = $(Mustache.render($('#tpl-cart-kitForm').html()));

                $('.jsProductImgPopup').trigger('close'); // закрытие окна с изображением

				$popup.lightbox_me({
					autofocus: true,
					closeSelector: ".jsPopupCloser",
					destroyOnClose: true
				});

				ko.applyBindings(new PopupModel(
					result.product,
					ENTER.utils.analytics.productPageSenders.get($button),
					ENTER.utils.analytics.productPageSenders2.get($button)
				), $popup[0]);

				// Закрытие окна
				$body.one('addtocart', function(){
					$popup.trigger('close.lme');
				});

			},
			complete: function() {
				isOpening = false;
			}
		});
	});

    $body.on('addtocart', function(){ $('.jsKitPopup').trigger('close')} ); // закрываем окно popup

	function PopupModel(product, sender, sender2) {
		var self = this;

		self.productId = product.id;
		self.productUi = product.ui;
		self.productPrefix = product.prefix;
		self.productWebname = product.webname;
		self.productName = self.productPrefix + ' ' + self.productWebname;
		self.productImageUrl = product.imageUrl;
		self.products = ko.observableArray([]);

		self.isBaseKit = ko.computed(function(){
			var isEqual = true;
			ko.utils.arrayForEach(self.products(), function(item){
				if (product.kitProducts[item.id].count != item.count()) isEqual = false;
			});
			return isEqual;
		});

		self.totalPrice = ko.computed(function(){
			var total = 0;
			ko.utils.arrayForEach(self.products(), function(item) {
				total += parseInt(item.count()) * parseInt(item.price)
			});
			return window.printPrice(total);
		});

		self.totalCount = ko.computed(function(){
			var total = 0;
			ko.utils.arrayForEach(self.products(), function(item) {
				total += parseInt(item.count())
			});
			return total;
		});

		self.buyLink = ko.computed(function(){
			var params = {
				kitProduct: {ui: self.productUi},
				products: []
			};

			ko.utils.arrayForEach(self.products(), function(item){
				if (item.count() > 0) {
					params.products.push({
						ui: item.ui,
						quantity: '+' + item.count(),
						up: '1'
					});
				}
			});

			if (sender) {
				params.sender = sender;
			}

			if (sender2) {
				params.sender2 = sender2;
			}

			return ENTER.utils.generateUrl('cart.product.setList', params);
		});

		self.dataUpsale = function(mainId){
			var url = '/ajax/upsale/' + mainId;
			return ko.toJSON({url : url, fromUpsale: false});
		};

		self.addProduct = function(product){
			self.products.push(new ProductModel(product))
		};

		self.populateWithObj = function(obj) {
			// Заполняем Модель продуктами
			self.products($.map(obj, function (item) {
				return new ProductModel(item)
			}));

			// Сортируем по line
			self.products.sort(function(a, b){
				return a.line == b.line ? 0 : ( a.line < b.line ? -1 : 1)
			});
		};

		self.resetToBaseKit = function() {
			self.populateWithObj(product.kitProducts);
		};

		self.populateWithObj(product.kitProducts);
	}

	function ProductModel(product) {
		var self = this;

		self.id = product.id;
		self.ui = product.ui;
		self.url = product.url;
		self.name = product.name;
		self.price = product.price;
		self.image = product.image;
		self.height = product.height;
		self.width = product.width;
		self.depth = product.depth;
		self.count = ko.observable(product.count);
		self.maxCount = ko.observable(Infinity);
		self.prettyPrice = ko.computed(function(){
			return window.printPrice(parseInt(self.price) * parseInt(self.count()));
		});
		self.prettyItemPrice = ko.computed(function(){
			return window.printPrice(parseInt(self.price));
		});
		self.deliveryDate = ko.observable(product.deliveryDate);

		self.plusClick = function() {
			if (self.maxCount() > self.count() && self.count() < 99) {
				self.count(parseInt(self.count()) + 1);
				$.post('/ajax/product/delivery', {product: [
					{id: self.id, quantity: self.count()}
				] }, function (data) {
					if (data.success) {
						self.deliveryDate(data.product[0].delivery[0].date.value);
						console.log('Delivery: id=', self.id, ' quantity=', self.count(), ' date: ', data.product[0].delivery)
					} else {
						self.count(self.count() - 1);
						self.maxCount(self.count());
					}
				})
			}
		};

		self.minusClick = function() {
			if (self.count() > 0) self.count(self.count()-1);
		};

		self.countKeydown = function(item, e) {
			e.stopPropagation();
			var isTextSelected = e.target.selectionStart - e.target.selectionEnd != 0;

			if ( e.which === 38 ) { // up arrow
				item.plusClick();
				return false;
			}
			else if ( e.which === 40 ) { // down arrow
				item.minusClick();
				return false;
			}
			else if ( e.which === 39 || e.which === 37 ) return true;
			else if ( (( (e.which >= 48) && (e.which <= 57) ) ||  //num keys
				( (e.which >= 96) && (e.which <= 105) ) || //numpad keys
				(e.which === 8) ||
				(e.which === 46))
			) {
				if (!isTextSelected) { // если текст не выделен
					if (item.count().toString().length < 2 && (e.which == 8 || e.which == 46)) return false; // предотвращаем пустую строку ввода
					if (item.count().toString().length > 1 && !(e.which == 8 || e.which == 46)) return false;
				}
				return true;
			}
			return false;
		};

		self.countKeyUp = function(item, e) {
			// TODO-zra сделать проверку доставки
			if (self.count() === "") self.count(1); // если поле ввода вдруг окажется пустым
			return false;
		}
	}
});