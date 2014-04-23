/**
 * Kit JS
 *
 * @requires jQuery, knockout, printPrice
 * @author	Zhukov Roman
 * @param	{Object}	ENTER	Enter namespace
 */
;$(document).ready(function( ENTER ) {

    if ($('.packageSet').length === 0) return; // выходим из функции

	var packageSetBtn = $('.jsChangePackageSet'),
		packageSetWindow = $('.jsPackageSetPopup'),
        packageProducts = $('.mPackageSetEdit').data('value'),
        knockoutUrl = ENTER.config.debug ? '/js/vendor/knockout.js' :'/js/prod/knockout.min.js'; // TODO-zra говнокод

	/**
	 * Показ окна с изменение комплекта 
	 */
	var showPackageSetPopup = function showPackageSetPopup(event) {
        event.preventDefault();
        $('.bCountSection').goodsCounter('destroy');
			packageSetWindow.lightbox_me({
				autofocus: true
			});
		};

	packageSetBtn.on('click', showPackageSetPopup);

    /**
     * Закрытие окна
     */
    $('body').on('addtocart', function(){
        packageSetWindow.trigger('close.lme');
    });

    if (!window.ko) $.getScript(knockoutUrl, init); else init();

    function init() {
        var ko = window.ko;

        function ProductModel(product){
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
            self.deliveryDate = ko.observable(product.deliveryDate);

            self.plusClick = function() {
                if (self.maxCount() > self.count()) {
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

                if ( e.which === 38 ) { // up arrow
                    item.plusClick();
                    return false;
                }
                else if ( e.which === 40 ) { // down arrow
                    item.minusClick();
                    return false;
                }
                else if ( (( (e.which >= 48) && (e.which <= 57) ) ||  //num keys
                    ( (e.which >= 96) && (e.which <= 105) ) || //numpad keys
                    (e.which === 8) ||
                    (e.which === 46))
                    ) {
                    if (item.count().toString().length < 2 && (e.which == 8 || e.which == 46)) return false; // предотвращаем пустую строку ввода
                    return true;
                }
                return false;
            };

            self.countKeyUp = function(item, e) {
                // TODO-zra сделать проверку доставки
                return false;
            }
        }

        function ProductList(){
            var self = this;

            self.products = ko.observableArray([]);

            self.isBaseKit = ko.computed(function(){
                var isEqual = true;
                ko.utils.arrayForEach(self.products(), function(item){
                    if (packageProducts[item.id].count != item.count()) isEqual = false;
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
                self.populateWithObj(packageProducts);
            };

            self.populateWithObj(packageProducts);

        }

        ko.applyBindings(new ProductList());
    }

}(window.ENTER));