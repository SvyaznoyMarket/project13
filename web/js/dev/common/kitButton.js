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

				var $popup = $(getPopupTemplate());

				$popup.lightbox_me({
					autofocus: true,
					destroyOnClose: true
				});

				ko.applyBindings(new PopupModel(result.product, $button.data('sender'), $button.data('sender2') || ''), $popup[0]);

				// Закрытие окна
				$body.one('addtocart', function(){
					$popup.trigger('close.lme');
				});

				// Google Analytics
				if (typeof _gaq !== 'undefined') {
					_gaq.push(['_trackEvent', 'addedCollection', 'collection', result.product.article]);
				}
			},
			complete: function() {
				isOpening = false;
			}
		});
	});

	function getPopupTemplate() {
		return '<div class="popup packageSetPopup jsKitPopup">' +
			'<a href="" class="close"></a>' +

			'<div class="bPageHead">' +
				'<div class="bPageHead__eSubtitle" data-bind="html: $root.productPrefix"></div>' +
				'<div class="bPageHead__eTitle clearfix">' +
					'<h1 itemprop="name" data-bind="html: $root.productWebname"></h1>' +
				'</div>' +
			'</div>' +

			'<div class="packageSetMainImg"><img data-bind="attr: {src: $root.productImageUrl}" /></div>' +

			'<!-- Состав комплекта -->' +
			'<div class="packageSet mPackageSetEdit">' +

				'<div class="packageSetHead cleared">' +
					'<span class="packageSetHead_title">Уточните комплектацию</span>' +
				'</div>' +

				'<div class="packageSet_inner" data-bind="foreach: products">' +

					'<div class="packageSetBodyItem" data-bind="css: { mDisabled: count() < 1 }">' +
						'<a class="packageSetBodyItem_img" href="" data-bind="attr: { href : url }"><img src="" data-bind="attr: { src: image }" /></a><!--/ изображение товара -->' +

						'<div class="packageSetBodyItem_desc">' +
							'<div class="name"><a class="" href="" data-bind="text: name, attr: { href : url }"></a></div><!--/ название товара -->' +

							'<div class="price"><span data-bind="html: prettyItemPrice"></span>&nbsp;<span class="rubl">p</span></div> <!-- Цена за единицу товара -->' +

							'<!-- размеры товара -->' +
							'<div class="column dimantion">' +
								'<span class="dimantion_name">Высота</span>' +
								'<span class="dimantion_val" data-bind="text: height"></span>' +
							'</div>' +

							'<div class="column dimantion">' +
								'<span class="dimantion_name">&nbsp;</span>' +
								'<span class="dimantion_val separation">x</span>' +
							'</div>' +

							'<div class="column dimantion">' +
								'<span class="dimantion_name">Ширина</span>' +
								'<span class="dimantion_val" data-bind="text: width"></span>' +
							'</div>' +

							'<div class="column dimantion">' +
								'<span class="dimantion_name">&nbsp;</span>' +
								'<span class="dimantion_val separation">x</span>' +
							'</div>' +

							'<div class="column dimantion">' +
								'<span class="dimantion_name">Глубина</span>' +
								'<span class="dimantion_val" data-bind="text: depth"></span>' +
							'</div>' +

							'<div class="column dimantion">' +
								'<span class="dimantion_name">&nbsp;</span>' +
								'<span class="dimantion_val">см</span>' +
							'</div>' +
							'<!--/ размеры товара -->' +

							'<div class="column delivery" data-bind="css: { \'delivery-nodate\': deliveryDate() == \'\' } ">' +
								'<span class="dimantion_val" data-bind="if: deliveryDate() != \'\' ">Доставка <strong data-bind="text: deliveryDate()"></strong></span>' +
								'<span class="dimantion_val" data-bind="if: deliveryDate() == \'\' ">Уточните дату доставки в Контакт-сEnter</span>' +
							'</div><!--/ доставка -->' +
						'</div>' +

						'<div class="bCountSection clearfix">' +
							'<button class="bCountSection__eM" data-bind="click: minusClick, css: { mDisabled : count() == 0 }">-</button>' +
							'<input type="text" value="" class="bCountSection__eNum" data-bind="value: count, valueUpdate: \'input\', event: { keydown: countKeydown, keyup: countKeyUp }">' +
							'<button class="bCountSection__eP" data-bind="click: plusClick, css: { mDisabled : count() == maxCount() }">+</button>' +
							'<span>шт.</span>' +
						'</div>' +

						'<div class="packageSetBodyItem_price">' +
							'<span data-bind="html: prettyPrice"></span>&nbsp;<span class="rubl">p</span>' +
						'</div><!--/ цена -->' +
					'</div>' +

				'</div>' +

				'<div class="packageSetFooter">' +
					'<div class="packageSetDefault">' +
						'<input type="checkbox" id="defaultSet" class="bInputHidden bCustomInput jsCustomRadio" data-bind="click: resetToBaseKit">' +
						'<label for="defaultSet" class="packageSetLabel" data-bind="css: { mChecked : isBaseKit }, click: resetToBaseKit">Базовый комплект</label>' +
					'</div>' +

					'<div class="packageSetPrice">Итого за <span data-bind="text: totalCount"></span> предметов: <strong data-bind="html: totalPrice"></strong> <span class="rubl">p</span></div>' +

					'<div class="packageSetBuy btnBuy">' +
						'<a class="btnBuy__eLink jsBuyButton" href="" data-bind="css: { mDisabled: totalCount() == 0 }, attr: { href: buyLink, \'data-upsale\': dataUpsale($root.productId) }">Купить</a>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<!--/ Состав комплекта -->' +
		'</div>';
	}

	function PopupModel(product, sender, sender2) {
		var self = this;

		self.productId = product.id;
		self.productPrefix = product.prefix;
		self.productWebname = product.webname;
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
			var link = '/cart/set-products?',
				id = 0;

			ko.utils.arrayForEach(self.products(), function(item){
				if (item.count() > 0 ) {
					link += 'product['+id+'][id]=' + item.id + '&product['+id+'][quantity]=' + item.count() + '&';
					id += 1;
				}
			});

			link += $.param({sender: sender});

			if (sender2) {
				link += '&' + $.param({sender2: sender2});
			}

			return link;
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
		self.url = product.url;
		self.name = product.name;
		self.line = product.lineName;
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