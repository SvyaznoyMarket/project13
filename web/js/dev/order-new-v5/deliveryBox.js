/**
 * Создает блок доставки.
 * Если для товара недоступна выбранная точка доставки, создается новый блок
 * Стоимость блока расчитывается из суммы всех товаров.
 * Стоимость доставки считается минимальная из стоимостей доставок всех товаров в блоке
 *
 * @author	Zaytsev Alexandr
 * @this	{DeliveryBox}
 * 
 * @param	{Array}			products			Массив продуктов которые необходимо добавить в блок
 * @param	{String}		state				Текущий метод доставки для блока
 * @param	{Number}		choosenPointForBox	Выбранная точка доставки
 * 
 * @param	{Object}		createdBox			Объект со созданными блоками доставки
 * @param	{Object}		OrderModel			Модель оформления заказа
 * 
 * @constructor
 */
function DeliveryBox( products, state, choosenPointForBox, createdBox, OrderModel ) {
	console.info('Cоздание блока доставки '+state+' для '+choosenPointForBox);

	var self = this;

	self.OrderModel = OrderModel;
	self.createdBox = createdBox;
	self.deliveryPoint = choosenPointForBox;

	// Продукты в блоке
	self.products = [];
	// Общая стоимость блока
	self.fullPrice = 0;
	// Метод доставки
	self.state = state;
	// Название метода доставки
	self.deliveryName = self.OrderModel.orderDictionary.getNameOfState(state);
	// Стоимость доставки. Берем максимально возможное значение, чтобы сравнивая с ним находить минимальное
	self.deliveryPrice = Number.POSITIVE_INFINITY;
	// Выбранная дата доставки
	self.choosenDate = null;

	self.choosenInterval = ko.observable();

	self.addProductGroup(products);

	self.OrderModel.deliveryBoxes.push(self);
}

/**
 * Получить имя первого свойства объекта
 *
 * @this	{DeliveryBox}
 * 
 * @param	{Object}	obj		Объект у которого необходимо получить первое свойство
 * @return	{Object}			Возвращает свойство объекта
 */
DeliveryBox.prototype._getFirstPropertyName = function( obj ) {
	for ( var i in obj ) {
		return i;
	}
};

/**
 * Добавление продукта в блок доставки
 *
 * @this	{DeliveryBox}
 * 
 * @param	{Object}		product		Продукт который нужно добавить
 */
DeliveryBox.prototype._addProduct = function( product ) {
	var self = this,
		productDeliveryPrice = null,
		token = null,
		firstAvaliblePoint = null,
		tempProductArray = [];
	// end of vars


	/**
	 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
	 */
	if ( !product.deliveries[self.state].hasOwnProperty(self.deliveryPoint) ) {
		console.warn('Для товара '+product.id+' нет пункта доставки '+self.deliveryPoint+'. Необходимо создать новый блок');

		firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
		token = self.state+'_'+firstAvaliblePoint;

		tempProductArray.push(product);

		if ( self.createdBox[token] !== undefined ) {
			// Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок
			self.createdBox[token].addProductGroup( product );
		}
		else {
			// Блока для этого типа доставки в этот пункт еще существует
			self.createdBox[token] = new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint, self.createdBox, self.OrderModel );
		}

		return;
	}

	// Определение стоимости доставки. Если стоимость доставки данного товара ниже стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
	productDeliveryPrice = parseInt(product.deliveries[self.state][self.deliveryPoint].price, 10);
	self.deliveryPrice = ( self.deliveryPrice > productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

	// Добавляем стоимость продукта к общей стоимости блока доставки
	self.fullPrice += product.sum;

	self.products.push({
		name: product.name,
		price: product.sum,
		quantity: product.quantity,
		deleteUrl: product.deleteUrl,
		productUrl: product.url,
		productImg: product.image,
		deliveries: product.deliveries[self.state]
	});
};

/**
 * Добавление нескольких товаров в блок доставки
 * После добавления продуктов запускает получение общей даты доставки
 * 
 * @this	{DeliveryBox}
 * 
 * @param	{Array}			products	Продукты которые нужно добавить
 */
DeliveryBox.prototype.addProductGroup = function( products ) {
	// добавляем товары в блок
	for ( var i = products.length - 1; i >= 0; i-- ) {
		this._addProduct(products[i]);
	}

	this.calculateDate();
};

/**
 * Проверка, доступна ли дата доставки для всех товаров в боксе
 *
 * @this	{DeliveryBox}
 *
 * @param	{Number}	checkTS	Таймштамп даты которую необходимо проверить
 * 
 * @return	{Boolean}
 */
DeliveryBox.prototype._hasDateInAllProducts = function( checkTS ) {
	var self = this,
		nowProductDates = null,
		nowTS = null,

		res = true;

	/**
	 * Перебор всех продуктов в блоке
	 */
	for (var i = self.products.length - 1; i >= 0; i--) {
		nowProductDates = self.products[i].deliveries[self.deliveryPoint].dates;

		/**
		 * Перебор всех дат доставок в блоке
		 */
		for ( var j = 0, len = nowProductDates.length; j < len; j++ ) {
			nowTS = nowProductDates[j].value;

			if ( nowTS === checkTS ) {
				res = true;
				break;
			}
			else {
				res = false;
			}
		}

		if ( !res ) {
			break;
		}
	}

	return res;
};

/**
 * Получение общей ближайшей даты доставки.
 * Перебирается дата первого товара, и если больше либо равна сегодняшней дате а так же присутствует во всех товарах - то эта дата берется за ближайшую дату доставки.
 *
 * @this	{DeliveryBox}
 */
DeliveryBox.prototype.calculateDate = function() {
	console.info('Вычисление общей даты для продуктов в блоке');

	var self = this,
		todayTS = self.OrderModel.orderDictionary.getToday(),
		nowProductDates = null,
		nowTS = null;

	console.log('Сегодняшняя дата с сервера '+todayTS);

	if ( !self.products.length ) {
		console.warn('в блоке нет товаров');
		return;
	}

	/**
	 * Перебираем даты в первом товаре
	 */
	nowProductDates = self.products[0].deliveries[self.deliveryPoint].dates;

	for ( var i = 0, len = nowProductDates.length; i < len; i++ ) {
		nowTS = nowProductDates[i].value;

		if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS ) {
			console.log(nowTS+' это общая минимальная дата для товаров в блоке');
			self.choosenDate = nowProductDates[i];
			self.choosenInterval(nowProductDates[i].intervals[0]);
			break;
		}
	}
};